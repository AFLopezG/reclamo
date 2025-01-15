<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DelitoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('delitos')->insert([
            ['tipo'=>'LEVE','detalle'=>'Exponer anuncios, letreros o similares con un contenido lascivo o discriminador.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'LEVE','detalle'=>'No mantener el vehículo en condiciones de higiene y limpieza.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'LEVE','detalle'=>'Prestar el servicio público de transporte de pasajeros, sin conservar la higiene respectiva por parte de los conductores.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'LEVE','detalle'=>'No exhibir las tarifas aprobadas por la autoridad municipal competente. ','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'LEVE','detalle'=>'No participar en los programas de capacitación en materia de transporte, tránsito y cultura ciudadana que implemente la A.T.M.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'No respetar los derechos de los usuarios.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'No respetar la capacidad del vehículo motorizado en cuanto a la cantidad de carga y pasajeros.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'Recoger o dejar a los pasajeros fuera de los puntos de parada autorizados.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'Realizar actividades sin contar con el registro, autorización y renovación de parada.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'No contar con la tarjeta de operaciones y registro del operador. ','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'Circular transportando pasajeros en la parte exterior de la carrocería. ','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'Acondicionar el funcionamiento del vehículo con combustible no autorizado que represente riesgo a la seguridad e integridad física de los pasajeros.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'Realizar agresiones verbales o de hecho a los usuarios.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'No portar la tarjeta de operación, dependiendo de la modalidad de transporte.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'Negarse a la presentación de tarjeta de operación y hoja de control de servicio cuando lo requiera la o el guardia municipal de transporte. ','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'Incumplir las instrucciones de la Autoridad de Transporte Municipal A.T.M. Incumplir la ruta asignada.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'Realizar paradas intermedias antes de las veinte (20) horas,','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'Avasallar y/o alterar rutas.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'GRAVE','detalle'=>'Dejar pasajeros a medio recorrido.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'MUYGRAVE','detalle'=>'Incumplir la ruta asignada aprobada mediante normativa municipal y/o modificar los recorridos establecidos, exceptuando casos de fuerza mayor debidamente comprobados (trameaje).','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'MUYGRAVE','detalle'=>'Incumplir con las frecuencias de ruta y horarios establecidos por la instancia municipal competente.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'MUYGRAVE','detalle'=>'Negarse a llevar pasajeros sin justificativo alguno.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'MUYGRAVE','detalle'=>'Efectuar cobros que excedan las tarifas establecidas conforme a normativa municipal.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'MUYGRAVE','detalle'=>'Poner en riesgo la seguridad de los usuarios al conducir.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'MUYGRAVE','detalle'=>'Utilizar como paradas espacios no autorizados.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'MUYGRAVE','detalle'=>'No brindar atención preferencial a personas con discapacidad, adultos mayores, mujeres embarazadas, niñas y niños.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'MUYGRAVE','detalle'=>'Hacer uso del teléfono celular y/o cualquier dispositivo digital que distraiga su atención al conducir.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'MUYGRAVE','detalle'=>'Circular por áreas restringidas en días y horarios establecidos por G.A.M.O.','servicio'=>'TRANSPORTE PUBLICO'],
            ['tipo'=>'MUYGRAVE','detalle'=>'Negarse a prestar servicio a pasajeros, niños y estudiantes en general, sin causal justificado','servicio'=>'TRANSPORTE PUBLICO'],
            
        ]);

    }
}
