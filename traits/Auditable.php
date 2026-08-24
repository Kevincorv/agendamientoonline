<?php

namespace App\Traits;

use App\Models\AuditoriaModel;

/**
 * Trait para agregar auditoría automática a cualquier modelo.
 *
 * Uso en un modelo:
 *   use Auditable;
 *   protected string $auditTabla = 'citas';
 *
 *   // Antes de guardar:
 *   $antes = $this->find($id);
 *   $this->update($id, $datos);
 *   $this->auditarEditar($id, $antes, $datos);
 */
trait Auditable
{
    protected string $auditTabla = '';

    public function auditarCrear(int $registroId, array $datos = []): void
    {
        // Eliminar campos sensibles antes de loguear
        $datosLimpios = $this->limpiarDatosSensibles($datos);
        AuditoriaModel::registrar([
            'accion'        => 'crear',
            'tabla'         => $this->auditTabla,
            'registro_id'   => $registroId,
            'descripcion'   => "Creó registro #{$registroId} en {$this->auditTabla}",
            'datos_despues' => $datosLimpios,
        ]);
    }

    public function auditarEditar(int $registroId, array $antes = [], array $despues = []): void
    {
        $antesLimpio   = $this->limpiarDatosSensibles($antes);
        $despuesLimpio = $this->limpiarDatosSensibles($despues);
        AuditoriaModel::registrar([
            'accion'        => 'editar',
            'tabla'         => $this->auditTabla,
            'registro_id'   => $registroId,
            'descripcion'   => "Editó registro #{$registroId} en {$this->auditTabla}",
            'datos_antes'   => $antesLimpio,
            'datos_despues' => $despuesLimpio,
        ]);
    }

    public function auditarEliminar(int $registroId, array $datos = []): void
    {
        $datosLimpios = $this->limpiarDatosSensibles($datos);
        AuditoriaModel::registrar([
            'accion'       => 'eliminar',
            'tabla'        => $this->auditTabla,
            'registro_id'  => $registroId,
            'descripcion'  => "Eliminó registro #{$registroId} de {$this->auditTabla}",
            'datos_antes'  => $datosLimpios,
        ]);
    }

    private function limpiarDatosSensibles(array $datos): array
    {
        $campos = ['password', 'password_hash', 'token', 'two_factor_secret', 'csrf_token'];
        foreach ($campos as $campo) {
            if (isset($datos[$campo])) {
                $datos[$campo] = '***';
            }
        }
        return $datos;
    }
}