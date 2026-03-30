<?php

namespace App\Http\Controllers;

use App\Models\Licencia;
use App\Models\LogLicencia;
use App\Models\Taxi;
use App\Models\Contribuyente;
use App\Models\Sindicato;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LicenciaController extends Controller
{
    private function normalizarEstado(?string $estado): string
    {
        $e = strtoupper(trim((string) ($estado ?? '')));
        if ($e === '' || $e === 'VIGENTE') return 'ACTIVO';
        if ($e === 'ANULADA') return 'ANULADO';
        return $e;
    }

    private function esVigente(Licencia $licencia): bool
    {
        $hoy = now()->startOfDay()->toDateString();
        $estado = $this->normalizarEstado($licencia->estado);
        if ($estado === 'ANULADO') return false;
        if ($licencia->vigencia_hasta && $licencia->vigencia_hasta < $hoy) return false;
        return $estado === 'ACTIVO';
    }

    private function refreshVencidas(): void
    {
        $hoy = now()->startOfDay()->toDateString();

        Licencia::query()
            ->whereNotNull('vigencia_hasta')
            ->where('vigencia_hasta', '<', $hoy)
            ->whereNotIn('estado', ['ANULADO', 'ANULADA', 'VENCIDO'])
            ->update(['estado' => 'VENCIDO']);

        LogLicencia::query()
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', $hoy)
            ->where('estado', 'ACTIVO')
            ->update(['estado' => 'VENCIDO']);
    }

    private function normalizarComp(?string $comp): ?string
    {
        if ($comp === null) {
            return null;
        }

        $comp = strtoupper(trim($comp));

        return $comp === '' ? null : $comp;
    }

    private function generarCodigoVerificacion(): string
    {
        do {
            $codigo = Str::random(48);
        } while (Licencia::where('codigo_verificacion', $codigo)->exists());

        return $codigo;
    }

    private function generarNumeroLicencia(): string
    {
        $year = now()->format('Y');
        $prefix = 'LIC-' . $year . '-';

        $last = Licencia::query()
            ->where('num_licencia', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        $next = 1;
        if ($last && is_string($last->num_licencia) && str_starts_with($last->num_licencia, $prefix)) {
            $suffix = substr($last->num_licencia, strlen($prefix));
            if ($suffix !== false && ctype_digit($suffix)) {
                $next = ((int) $suffix) + 1;
            }
        }

        // Garantiza unicidad incluso si hay registros con formatos distintos.
        do {
            $num = $prefix . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
            $next++;
        } while (Licencia::where('num_licencia', $num)->exists());

        return $num;
    }

    private function vigenciaDesdeHoy(): array
    {
        $hoy = now()->startOfDay();

        return [
            'fecha_licencia' => $hoy->toDateString(),
            'vigencia_hasta' => $hoy->copy()->addYear()->toDateString(),
        ];
    }

    private function urlFrontVerificacion(string $codigo): string
    {
        $base = (string) (env('URL_FRONT') ?: (env('URL_BASE') ?: config('app.url')));
        $base = rtrim($base, '/');

        return $base . '/#/licencias/verificar/' . $codigo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->refreshVencidas();
        return Licencia::with([
            'taxi.propietario',
            'chofer',
            'sindicato',
        ])->orderByDesc('id')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'num_licencia' => ['nullable', 'string', 'max:255', Rule::unique('licencias', 'num_licencia')],
            'tipo' => ['required', 'string', 'in:SINDICATO,INDEPENDIENTE', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
            'sindicato_id' => ['nullable', 'integer', 'exists:sindicatos,id', 'required_if:tipo,SINDICATO'],

            'taxi.placa' => ['required', 'string', 'max:255'],
            'taxi.marca' => ['nullable', 'string', 'max:255'],
            'taxi.modelo' => ['nullable', 'string', 'max:255'],
            'taxi.linea' => ['nullable', 'string', 'max:255'],
            'taxi.color' => ['nullable', 'string', 'max:255'],
            'taxi.anio' => ['nullable', 'string', 'max:255'],
            'taxi.ruat' => ['nullable', 'string', 'max:255'],

            'propietario.cedula' => ['required', 'string', 'max:255'],
            'propietario.comp' => ['nullable', 'string', 'max:255'],
            'propietario.nombre' => ['required', 'string', 'max:255'],
            'propietario.apellido' => ['nullable', 'string', 'max:255'],
            'propietario.fecha_nacimiento' => ['nullable', 'date'],
            'propietario.telefono' => ['nullable', 'string', 'max:255'],
            'propietario.direccion' => ['nullable', 'string', 'max:255'],
            'propietario.categoria' => ['nullable', 'string', 'max:255'],

            'chofer.cedula' => ['required', 'string', 'max:255'],
            'chofer.comp' => ['nullable', 'string', 'max:255'],
            'chofer.nombre' => ['required', 'string', 'max:255'],
            'chofer.apellido' => ['nullable', 'string', 'max:255'],
            'chofer.fecha_nacimiento' => ['nullable', 'date'],
            'chofer.telefono' => ['nullable', 'string', 'max:255'],
            'chofer.direccion' => ['nullable', 'string', 'max:255'],
            'chofer.categoria' => ['nullable', 'string', 'max:255'],
        ]);

        $userId = $request->user()?->id;

        $licencia = DB::transaction(function () use ($validated, $userId) {
            $vigencia = $this->vigenciaDesdeHoy();

            $placa = strtoupper($validated['taxi']['placa']);
            $taxi = Taxi::updateOrCreate(
                ['placa' => $placa],
                [
                    'marca' => isset($validated['taxi']['marca']) ? strtoupper($validated['taxi']['marca']) : null,
                    'modelo' => isset($validated['taxi']['modelo']) ? strtoupper($validated['taxi']['modelo']) : null,
                    'linea' => isset($validated['taxi']['linea']) ? strtoupper($validated['taxi']['linea']) : null,
                    'color' => isset($validated['taxi']['color']) ? strtoupper($validated['taxi']['color']) : null,
                    'anio' => $validated['taxi']['anio'] ?? null,
                    'ruat' => isset($validated['taxi']['ruat']) ? strtoupper($validated['taxi']['ruat']) : null,
                ]
            );

            $propCedula = trim((string) $validated['propietario']['cedula']);
            $propComp = $this->normalizarComp($validated['propietario']['comp'] ?? null);
            $choferCedula = trim((string) $validated['chofer']['cedula']);
            $choferComp = $this->normalizarComp($validated['chofer']['comp'] ?? null);
            $mismoContribuyente = $propCedula === $choferCedula && (string) ($propComp ?? '') === (string) ($choferComp ?? '');

            $propietario = Contribuyente::updateOrCreate(
                ['cedula' => $propCedula],
                [
                    'comp' => $propComp,
                    'nombre' => strtoupper($validated['propietario']['nombre']),
                    'apellido' => isset($validated['propietario']['apellido']) ? strtoupper($validated['propietario']['apellido']) : null,
                    'fecha_nacimiento' => $validated['propietario']['fecha_nacimiento'] ?? null,
                    'telefono' => $validated['propietario']['telefono'] ?? null,
                    'direccion' => $validated['propietario']['direccion'] ?? null,
                    'categoria' => isset($validated['propietario']['categoria']) ? strtoupper($validated['propietario']['categoria']) : null,
                ]
            );

            $taxi->propietario_id = $propietario->id;
            $taxi->save();

            // Si propietario y chofer son la misma persona, se registra con los datos del primero
            // y se reutiliza el ID sin sobrescribir con la segunda secciÃ³n.
            if ($mismoContribuyente) {
                $chofer = $propietario;
            } else {
                $chofer = Contribuyente::updateOrCreate(
                    ['cedula' => $choferCedula],
                    [
                        'comp' => $choferComp,
                        'nombre' => strtoupper($validated['chofer']['nombre']),
                        'apellido' => isset($validated['chofer']['apellido']) ? strtoupper($validated['chofer']['apellido']) : null,
                        'fecha_nacimiento' => $validated['chofer']['fecha_nacimiento'] ?? null,
                        'telefono' => $validated['chofer']['telefono'] ?? null,
                        'direccion' => $validated['chofer']['direccion'] ?? null,
                        'categoria' => isset($validated['chofer']['categoria']) ? strtoupper($validated['chofer']['categoria']) : null,
                    ]
                );
            }

            $codigo = $this->generarCodigoVerificacion();
            $tipo = strtoupper($validated['tipo']);
            $hoy = now()->startOfDay()->toDateString();

            $existeVigente = Licencia::query()
                ->where(function ($q) use ($taxi, $chofer) {
                    $q->where('taxi_id', $taxi->id)->orWhere('chofer_id', $chofer->id);
                })
                ->whereNotIn('estado', ['ANULADO', 'ANULADA'])
                ->where(function ($q) use ($hoy) {
                    $q->whereNull('vigencia_hasta')->orWhere('vigencia_hasta', '>=', $hoy);
                })
                ->exists();

            if ($existeVigente) {
                throw ValidationException::withMessages([
                    'licencia' => 'Ya existe una licencia vigente para este taxi/chofer.',
                ]);
            }

            $licencia = Licencia::create([
                'num_licencia' => isset($validated['num_licencia']) && trim((string) $validated['num_licencia']) !== ''
                    ? strtoupper($validated['num_licencia'])
                    : $this->generarNumeroLicencia(),
                'fecha_licencia' => $vigencia['fecha_licencia'],
                'vigencia_hasta' => $vigencia['vigencia_hasta'],
                'tipo' => $tipo,
                'estado' => isset($validated['estado']) ? $this->normalizarEstado($validated['estado']) : 'ACTIVO',
                'codigo_verificacion' => $codigo,
                'taxi_id' => $taxi->id,
                'sindicato_id' => $tipo === 'SINDICATO' ? ($validated['sindicato_id'] ?? null) : null,
                'chofer_id' => $chofer->id,
                'user_id' => $userId,
            ]);

            LogLicencia::create([
                'licencia_id' => $licencia->id,
                'user_id' => $userId,
                'tipo' => 'EMISION',
                'fecha_inicio' => $vigencia['fecha_licencia'],
                'fecha_fin' => $vigencia['vigencia_hasta'],
                'estado' => 'ACTIVO',
            ]);

            $chofer->update([
                'num_licencia' => $licencia->num_licencia,
                'fecha_licencia' => $vigencia['fecha_licencia'],
            ]);

            return $licencia;
        });

        return $licencia->load(['taxi.propietario', 'chofer', 'sindicato']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Licencia $licencia)
    {
        $this->refreshVencidas();
        return $licencia->load(['taxi.propietario', 'chofer', 'sindicato']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Licencia $licencia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Licencia $licencia)
    {
        $validated = $request->validate([
            'num_licencia' => ['nullable', 'string', 'max:255', Rule::unique('licencias', 'num_licencia')->ignore($licencia->id)],
            'tipo' => ['required', 'string', 'in:SINDICATO,INDEPENDIENTE', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
            'sindicato_id' => ['nullable', 'integer', 'exists:sindicatos,id', 'required_if:tipo,SINDICATO'],

            'taxi.placa' => ['required', 'string', 'max:255'],
            'taxi.marca' => ['nullable', 'string', 'max:255'],
            'taxi.modelo' => ['nullable', 'string', 'max:255'],
            'taxi.linea' => ['nullable', 'string', 'max:255'],
            'taxi.color' => ['nullable', 'string', 'max:255'],
            'taxi.anio' => ['nullable', 'string', 'max:255'],
            'taxi.ruat' => ['nullable', 'string', 'max:255'],

            'propietario.cedula' => ['required', 'string', 'max:255'],
            'propietario.comp' => ['nullable', 'string', 'max:255'],
            'propietario.nombre' => ['required', 'string', 'max:255'],
            'propietario.apellido' => ['nullable', 'string', 'max:255'],
            'propietario.fecha_nacimiento' => ['nullable', 'date'],
            'propietario.telefono' => ['nullable', 'string', 'max:255'],
            'propietario.direccion' => ['nullable', 'string', 'max:255'],
            'propietario.categoria' => ['nullable', 'string', 'max:255'],

            'chofer.cedula' => ['required', 'string', 'max:255'],
            'chofer.comp' => ['nullable', 'string', 'max:255'],
            'chofer.nombre' => ['required', 'string', 'max:255'],
            'chofer.apellido' => ['nullable', 'string', 'max:255'],
            'chofer.fecha_nacimiento' => ['nullable', 'date'],
            'chofer.telefono' => ['nullable', 'string', 'max:255'],
            'chofer.direccion' => ['nullable', 'string', 'max:255'],
            'chofer.categoria' => ['nullable', 'string', 'max:255'],
        ]);

        $licencia = DB::transaction(function () use ($validated, $licencia) {
            $placa = strtoupper($validated['taxi']['placa']);
            $taxi = Taxi::updateOrCreate(
                ['placa' => $placa],
                [
                    'marca' => isset($validated['taxi']['marca']) ? strtoupper($validated['taxi']['marca']) : null,
                    'modelo' => isset($validated['taxi']['modelo']) ? strtoupper($validated['taxi']['modelo']) : null,
                    'linea' => isset($validated['taxi']['linea']) ? strtoupper($validated['taxi']['linea']) : null,
                    'color' => isset($validated['taxi']['color']) ? strtoupper($validated['taxi']['color']) : null,
                    'anio' => $validated['taxi']['anio'] ?? null,
                    'ruat' => isset($validated['taxi']['ruat']) ? strtoupper($validated['taxi']['ruat']) : null,
                ]
            );

            $propCedula = trim((string) $validated['propietario']['cedula']);
            $propComp = $this->normalizarComp($validated['propietario']['comp'] ?? null);
            $choferCedula = trim((string) $validated['chofer']['cedula']);
            $choferComp = $this->normalizarComp($validated['chofer']['comp'] ?? null);
            $mismoContribuyente = $propCedula === $choferCedula && (string) ($propComp ?? '') === (string) ($choferComp ?? '');

            $propietario = Contribuyente::updateOrCreate(
                ['cedula' => $propCedula],
                [
                    'comp' => $propComp,
                    'nombre' => strtoupper($validated['propietario']['nombre']),
                    'apellido' => isset($validated['propietario']['apellido']) ? strtoupper($validated['propietario']['apellido']) : null,
                    'fecha_nacimiento' => $validated['propietario']['fecha_nacimiento'] ?? null,
                    'telefono' => $validated['propietario']['telefono'] ?? null,
                    'direccion' => $validated['propietario']['direccion'] ?? null,
                    'categoria' => isset($validated['propietario']['categoria']) ? strtoupper($validated['propietario']['categoria']) : null,
                ]
            );

            $taxi->propietario_id = $propietario->id;
            $taxi->save();

            if ($mismoContribuyente) {
                $chofer = $propietario;
            } else {
                $chofer = Contribuyente::updateOrCreate(
                    ['cedula' => $choferCedula],
                    [
                        'comp' => $choferComp,
                        'nombre' => strtoupper($validated['chofer']['nombre']),
                        'apellido' => isset($validated['chofer']['apellido']) ? strtoupper($validated['chofer']['apellido']) : null,
                        'fecha_nacimiento' => $validated['chofer']['fecha_nacimiento'] ?? null,
                        'telefono' => $validated['chofer']['telefono'] ?? null,
                        'direccion' => $validated['chofer']['direccion'] ?? null,
                        'categoria' => isset($validated['chofer']['categoria']) ? strtoupper($validated['chofer']['categoria']) : null,
                    ]
                );
            }

            $tipo = strtoupper($validated['tipo']);

            $licencia->update([
                // Se mantiene el número existente; si llega vacío, no se modifica.
                'num_licencia' => isset($validated['num_licencia']) && trim((string) $validated['num_licencia']) !== ''
                    ? strtoupper($validated['num_licencia'])
                    : $licencia->num_licencia,
                'tipo' => $tipo,
                'estado' => isset($validated['estado']) ? $this->normalizarEstado($validated['estado']) : $licencia->estado,
                'taxi_id' => $taxi->id,
                'sindicato_id' => $tipo === 'SINDICATO' ? ($validated['sindicato_id'] ?? null) : null,
                'chofer_id' => $chofer->id,
            ]);

            if (empty($licencia->codigo_verificacion)) {
                $licencia->codigo_verificacion = $this->generarCodigoVerificacion();
                $licencia->save();
            }

            $chofer->update([
                'num_licencia' => $licencia->num_licencia,
                'fecha_licencia' => $licencia->fecha_licencia,
            ]);

            return $licencia;
        });

        return $licencia->load(['taxi.propietario', 'chofer', 'sindicato']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Licencia $licencia)
    {
        $licencia->delete();

        return response()->json(['success' => true]);
    }

    public function anular(Licencia $licencia)
    {
        $licencia->update(['estado' => 'ANULADO']);

        $lastLog = LogLicencia::query()
            ->where('licencia_id', $licencia->id)
            ->orderByDesc('id')
            ->first();
        if ($lastLog) {
            $lastLog->update(['estado' => 'ANULADO']);
        }

        return $licencia->fresh()->load(['taxi.propietario', 'chofer', 'sindicato']);
    }

    public function renovar(Licencia $licencia)
    {
        $this->refreshVencidas();
        $estado = $this->normalizarEstado($licencia->estado);
        if ($estado === 'ANULADO') {
            return response()->json(['message' => 'No se puede renovar una licencia anulada.'], 422);
        }

        $hoy = now()->startOfDay()->toDateString();
        $estaVencida = $licencia->vigencia_hasta && $licencia->vigencia_hasta < $hoy;
        if (!$estaVencida) {
            return response()->json(['message' => 'Solo se puede renovar si la licencia está vencida.'], 422);
        }

        $vigencia = $this->vigenciaDesdeHoy();
        $userId = auth()->id();

        DB::transaction(function () use ($licencia, $vigencia, $userId) {
            $lastLog = LogLicencia::query()
                ->where('licencia_id', $licencia->id)
                ->orderByDesc('id')
                ->first();
            if ($lastLog && $lastLog->estado === 'ACTIVO') {
                $lastLog->update(['estado' => 'VENCIDO']);
            }

            $licencia->update([
                'fecha_licencia' => $vigencia['fecha_licencia'],
                'vigencia_hasta' => $vigencia['vigencia_hasta'],
                'estado' => 'ACTIVO',
            ]);

            LogLicencia::create([
                'licencia_id' => $licencia->id,
                'user_id' => $userId,
                'tipo' => 'RENOVACION',
                'fecha_inicio' => $vigencia['fecha_licencia'],
                'fecha_fin' => $vigencia['vigencia_hasta'],
                'estado' => 'ACTIVO',
            ]);
        });

        return $licencia->fresh()->load(['taxi.propietario', 'chofer', 'sindicato']);
    }

    public function credencialPdf(Licencia $licencia)
    {
        $licencia->load(['taxi.propietario', 'chofer', 'sindicato']);

        $this->refreshVencidas();
        if (!$this->esVigente($licencia)) {
            return response()->json(['message' => 'Solo se puede imprimir credencial si la licencia está vigente.'], 422);
        }

        if (empty($licencia->codigo_verificacion)) {
            $licencia->codigo_verificacion = $this->generarCodigoVerificacion();
            $licencia->save();
        }

        $verifyUrl = $this->urlFrontVerificacion($licencia->codigo_verificacion);
        $qrCode = QrCode::format('png')->size(220)->margin(1)->generate($verifyUrl);

        $pdf = Pdf::loadView('licencia_credencial', [
            'licencia' => $licencia,
            'qrcode' => base64_encode($qrCode),
            'verifyUrl' => $verifyUrl,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('credencial_' . $licencia->num_licencia . '.pdf');
    }

    public function resumenPdf(Licencia $licencia)
    {
        $licencia->load(['taxi.propietario', 'chofer', 'sindicato']);

        if (empty($licencia->codigo_verificacion)) {
            $licencia->codigo_verificacion = $this->generarCodigoVerificacion();
            $licencia->save();
        }

        $verifyUrl = $this->urlFrontVerificacion($licencia->codigo_verificacion);
        $qrCode = QrCode::format('png')->size(180)->margin(1)->generate($verifyUrl);

        $pdf = Pdf::loadView('licencia_resumen_pdf', [
            'licencia' => $licencia,
            'qrcode' => base64_encode($qrCode),
            'verifyUrl' => $verifyUrl,
        ])->setPaper('letter', 'portrait');

        return $pdf->stream('licencia_' . $licencia->num_licencia . '.pdf');
    }

    public function verifyApi(string $codigo)
    {
        $licencia = Licencia::with(['taxi.propietario', 'chofer', 'sindicato'])
            ->where('codigo_verificacion', $codigo)
            ->first();

        if (!$licencia) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $this->refreshVencidas();
        $hoy = now()->startOfDay();
        $vigente = $this->esVigente($licencia);

        return response()->json([
            'licencia' => $licencia,
            'vigente' => $vigente,
            'hoy' => $hoy->toDateString(),
        ]);
    }

    public function listaPdf(Request $request)
    {
        $validated = $request->validate([
            'filtro' => ['nullable', 'string', 'in:todos,vigentes,vencidas'],
        ]);

        $filtro = strtolower((string) ($validated['filtro'] ?? 'todos'));
        $hoy = now()->startOfDay()->toDateString();

        $query = Licencia::with(['taxi.propietario', 'chofer', 'sindicato'])->orderByDesc('id');

        if ($filtro === 'vigentes') {
            $query
                ->where(function ($q) {
                    $q->whereNull('estado')->orWhereNotIn('estado', ['ANULADA', 'ANULADO']);
                })
                ->where(function ($q) use ($hoy) {
                    $q->whereNull('vigencia_hasta')->orWhere('vigencia_hasta', '>=', $hoy);
                });
        } elseif ($filtro === 'vencidas') {
            $query
                ->where(function ($q) {
                    $q->whereNull('estado')->orWhereNotIn('estado', ['ANULADA', 'ANULADO']);
                })
                ->whereNotNull('vigencia_hasta')
                ->where('vigencia_hasta', '<', $hoy);
        }

        $licencias = $query->get();

        $titulo = match ($filtro) {
            'vigentes' => 'LICENCIAS VIGENTES',
            'vencidas' => 'LICENCIAS VENCIDAS',
            default => 'TODAS LAS LICENCIAS',
        };

        $pdf = Pdf::loadView('licencias_lista_pdf', [
            'licencias' => $licencias,
            'titulo' => $titulo,
            'filtro' => $filtro,
            'hoy' => $hoy,
        ])->setPaper('letter', 'landscape');

        return $pdf->stream('licencias_' . $filtro . '_' . $hoy . '.pdf');
    }

    public function verifyPage(string $codigo)
    {
        $licencia = Licencia::with(['taxi.propietario', 'chofer', 'sindicato'])
            ->where('codigo_verificacion', $codigo)
            ->first();

        if (!$licencia) {
            abort(404);
        }

        $this->refreshVencidas();
        $hoy = now()->startOfDay();
        $vigente = $this->esVigente($licencia);

        return view('licencia_verify', [
            'licencia' => $licencia,
            'vigente' => $vigente,
            'hoy' => $hoy->toDateString(),
        ]);
    }
}
