<?php

namespace App\Http\Controllers;

use App\Models\Licencia;
use App\Models\Multa;
use App\Models\Sancion;
use App\Models\Taxi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MultaController extends Controller
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

    public function buscarPorPlaca(Request $request)
    {
        $validated = $request->validate([
            'placa' => ['required', 'string', 'max:255'],
        ]);

        $placa = strtoupper(trim((string) $validated['placa']));

        $taxi = Taxi::with(['propietario'])->where('placa', $placa)->first();
        if (!$taxi) {
            return response()->json([
                'message' => 'La placa no está registrada.',
                'placa' => $placa,
                'exists' => false,
            ], 404);
        }

        $licencia = Licencia::with(['chofer', 'sindicato'])
            ->where('taxi_id', $taxi->id)
            ->orderByDesc('id')
            ->first();

        if (!$licencia) {
            return response()->json([
                'message' => 'Vehículo registrado, pero no tiene licencia asociada.',
                'exists' => true,
                'taxi' => $taxi,
                'licencia' => null,
                'vigente' => false,
                'hoy' => now()->startOfDay()->toDateString(),
            ]);
        }

        return response()->json([
            'message' => $this->esVigente($licencia) ? 'Licencia vigente.' : 'Licencia vencida.',
            'exists' => true,
            'taxi' => $taxi,
            'licencia' => $licencia,
            'vigente' => $this->esVigente($licencia),
            'hoy' => now()->startOfDay()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => ['required', 'string', 'max:255'],
            'sancion_id' => ['required', 'integer', 'exists:sanciones,id'],
        ]);

        $placa = strtoupper(trim((string) $validated['placa']));
        $taxi = Taxi::with(['propietario'])->where('placa', $placa)->first();
        if (!$taxi) {
            return response()->json(['message' => 'La placa no está registrada.'], 404);
        }

        $licencia = Licencia::with(['chofer'])->where('taxi_id', $taxi->id)->orderByDesc('id')->first();
        if (!$licencia) {
            return response()->json(['message' => 'El vehículo no tiene licencia asociada.'], 422);
        }

        $sancion = Sancion::findOrFail($validated['sancion_id']);
        $userId = $request->user()?->id;

        $multa = Multa::create([
            'fecha_hora' => now(),
            'sancion_id' => $sancion->id,
            'taxi_id' => $taxi->id,
            'licencia_id' => $licencia->id,
            'propietario_id' => $taxi->propietario_id,
            'conductor_id' => $licencia->chofer_id,
            'user_id' => $userId,
            'placa' => $taxi->placa,
            'num_licencia' => $licencia->num_licencia,
            'cedula_propietario' => $taxi->propietario?->cedula,
            'cedula_conductor' => $licencia->chofer?->cedula,
        ]);

        return $multa->load(['sancion', 'taxi.propietario', 'licencia.chofer', 'user']);
    }

    public function reporte(Request $request)
    {
        $validated = $request->validate([
            'ini' => ['required', 'date'],
            'fin' => ['required', 'date'],
            'placa' => ['nullable', 'string', 'max:255'],
            'cedula_conductor' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Multa::with(['sancion', 'taxi.propietario', 'licencia.chofer', 'user'])
            ->whereDate('fecha_hora', '>=', $validated['ini'])
            ->whereDate('fecha_hora', '<=', $validated['fin'])
            ->orderByDesc('fecha_hora');

        if (!empty($validated['placa'])) {
            $placa = strtoupper(trim((string) $validated['placa']));
            $query->where('placa', 'like', '%' . $placa . '%');
        }
        if (!empty($validated['cedula_conductor'])) {
            $ced = trim((string) $validated['cedula_conductor']);
            $query->where('cedula_conductor', 'like', '%' . $ced . '%');
        }

        return $query->get();
    }

    public function reportePdf(Request $request)
    {
        $validated = $request->validate([
            'ini' => ['required', 'date'],
            'fin' => ['required', 'date'],
            'placa' => ['nullable', 'string', 'max:255'],
            'cedula_conductor' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Multa::with(['sancion', 'taxi.propietario', 'licencia.chofer', 'user'])
            ->whereDate('fecha_hora', '>=', $validated['ini'])
            ->whereDate('fecha_hora', '<=', $validated['fin'])
            ->orderByDesc('fecha_hora');

        if (!empty($validated['placa'])) {
            $placa = strtoupper(trim((string) $validated['placa']));
            $query->where('placa', 'like', '%' . $placa . '%');
        }
        if (!empty($validated['cedula_conductor'])) {
            $ced = trim((string) $validated['cedula_conductor']);
            $query->where('cedula_conductor', 'like', '%' . $ced . '%');
        }

        $multas = $query->get();

        $pdf = Pdf::loadView('multas_reporte_pdf', [
            'multas' => $multas,
            'ini' => $validated['ini'],
            'fin' => $validated['fin'],
            'placa' => $validated['placa'] ?? null,
            'cedula_conductor' => $validated['cedula_conductor'] ?? null,
            'hoy' => now()->startOfDay()->toDateString(),
        ])->setPaper('letter', 'landscape');

        return $pdf->stream('multas_' . $validated['ini'] . '_al_' . $validated['fin'] . '.pdf');
    }
}

