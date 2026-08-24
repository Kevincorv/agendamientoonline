<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\UsuarioModel;
use App\Models\RbacModel;
use App\Models\AuditoriaModel;
use App\Models\SesionModel;
use App\Core\Session;

class UsuariosController
{
    private UsuarioModel $model;
    private RbacModel    $rbac;

    public function __construct()
    {
        $this->model = new UsuarioModel();
        $this->rbac  = new RbacModel();
    }

    /** GET /admin/usuarios */
    public function index(): void
    {
        AuthMiddleware::requirePermiso('usuarios.ver');
        $usuarios  = $this->model->getAll();
        $roles     = $this->rbac->getRoles();
        $csrfToken = Session::generateCsrf();
        require_once VIEW_PATH . '/admin/usuarios.php';
    }

    /** POST /admin/usuarios/crear */
    public function crear(): void
    {
        AuthMiddleware::requirePermiso('usuarios.crear');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token CSRF inválido.');
            header('Location: ' . APP_URL . '/admin/usuarios'); exit;
        }

        // Validar email único
        if ($this->model->findByEmail($_POST['email'])) {
            Session::setFlash('error', 'El email ya está registrado.');
            header('Location: ' . APP_URL . '/admin/usuarios'); exit;
        }

        $id = $this->model->crear([
            'nombre'   => trim($_POST['nombre']),
            'email'    => trim($_POST['email']),
            'password' => $_POST['password'],
            'rol_id'   => (int)$_POST['rol_id'],
        ]);

        Session::setFlash('success', 'Usuario creado correctamente.');
        header('Location: ' . APP_URL . '/admin/usuarios');
        exit;
    }

    /** POST /admin/usuarios/{id}/editar */
    public function editar(int $id): void
    {
        AuthMiddleware::requirePermiso('usuarios.crear');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token CSRF inválido.');
            header('Location: ' . APP_URL . '/admin/usuarios'); exit;
        }

        $datos = [
            'nombre' => trim($_POST['nombre']),
            'email'  => trim($_POST['email']),
            'rol_id' => (int)$_POST['rol_id'],
        ];
        if (!empty($_POST['password'])) {
            $datos['password'] = $_POST['password'];
        }

        $this->model->actualizar($id, $datos);
        Session::setFlash('success', 'Usuario actualizado correctamente.');
        header('Location: ' . APP_URL . '/admin/usuarios');
        exit;
    }

    /** POST /admin/usuarios/{id}/eliminar */
    public function eliminar(int $id): void
    {
        AuthMiddleware::requirePermiso('usuarios.eliminar');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token CSRF inválido.');
            header('Location: ' . APP_URL . '/admin/usuarios'); exit;
        }

        // No permitir eliminar su propia cuenta
        $user = Session::get('admin_user');
        if ($user && $user['id'] === $id) {
            Session::setFlash('error', 'No podés eliminar tu propia cuenta.');
            header('Location: ' . APP_URL . '/admin/usuarios'); exit;
        }

        $this->model->eliminar($id);
        // Invalida todas sus sesiones
        (new SesionModel())->invalidarTodasSesiones($id);

        Session::setFlash('success', 'Usuario eliminado.');
        header('Location: ' . APP_URL . '/admin/usuarios');
        exit;
    }

    /** POST /admin/usuarios/{id}/desbloquear */
    public function desbloquear(int $id): void
    {
        AuthMiddleware::requirePermiso('usuarios.crear');
        if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Token CSRF inválido.');
            header('Location: ' . APP_URL . '/admin/usuarios'); exit;
        }
        $this->model->desbloquear($id);
        AuditoriaModel::registrar([
            'accion'      => 'editar',
            'tabla'       => 'usuarios',
            'registro_id' => $id,
            'descripcion' => "Desbloqueó manualmente usuario #$id",
        ]);
        Session::setFlash('success', 'Usuario desbloqueado.');
        header('Location: ' . APP_URL . '/admin/usuarios');
        exit;
    }
}