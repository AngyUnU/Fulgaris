<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'label' => 'Administrador',
                'description' => 'Control total del sistema: gestión global de proyectos, usuarios, asignaciones y reportes.',
            ],
            [
                'name' => 'lider',
                'label' => 'Líder de Proyecto',
                'description' => 'Administración de proyectos, asignación de actividades, establecimiento de fechas de compromiso y registro de acuerdos.',
            ],
            [
                'name' => 'desarrollador',
                'label' => 'Desarrollador / Integrante',
                'description' => 'Consulta de actividades asignadas, actualización de porcentaje de avance, registro de comentarios y notas de trabajo.',
            ],
            [
                'name' => 'qa',
                'label' => 'Analista de Negocio / QA',
                'description' => 'Validación de requerimientos, aseguramiento de calidad, revisión de avances y cumplimiento de acuerdos.',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'label' => $roleData['label'],
                    'description' => $roleData['description'],
                ]
            );
        }

    }
}
