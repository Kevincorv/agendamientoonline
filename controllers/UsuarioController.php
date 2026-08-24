<?php

class UsuarioController
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function listado(): void
    {
        AuthMiddleware::requireRole('administrador');
        $usuarios  = $this->usuarioModel->obtenerTodos();
        $roles     = $this->usuarioModel->obtenerRoles();
        $csrfToken = generateCsrfToken();
        require_once __DIR__ . '/../views/admin/usuarios.php';
    }

    public function crear(): void
    {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/usuarios');
        }
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/admin/usuarios');
        }

        $data = [
            'nombre'   => sanitize($_POST['nombre']   ?? ''),
            'apellido' => sanitize($_POST['apellido'] ?? ''),
            'email'    => sanitize($_POST['email']    ?? ''),
            'password' => $_POST['password'] ?? '',
            'rol_id'   => (int) ($_POST['rol_id']     ?? 0),
        ];

        if ($this->usuarioModel->crear($data)) {
            flashMessage('success', 'Usuario creado correctamente.');
        } else {
            flashMessage('error', 'Error al crear el usuario.');
        }
        redirect('/admin/usuarios');
    }

    public function editar(): void
    {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/usuarios');
        }
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/admin/usuarios');
        }

        $id   = (int) ($_POST['id'] ?? 0);
        $data = [
            'nombre'   => sanitize($_POST['nombre']   ?? ''),
            'email'    => sanitize($_POST['email']    ?? ''),
            'rol_id'   => (int) ($_POST['rol_id']     ?? 0),
        ];

        // Solo actualizar contraseña si se envió
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        if ($this->usuarioModel->actualizar($id, $data)) {
            flashMessage('success', 'Usuario actualizado correctamente.');
        } else {
            flashMessage('error', 'Error al actualizar el usuario.');
        }
        redirect('/admin/usuarios');
    }

    public function eliminar(): void
    {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/usuarios');
        }
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/admin/usuarios');
        }

        $id = (int) ($_POST['id'] ?? 0);

        // Protección: no eliminar al propio usuario logueado
        if ($id === (int) ($_SESSION['usuario_id'] ?? 0)) {
            flashMessage('error', 'No podés eliminar tu propio usuario.');
            redirect('/admin/usuarios');
        }

        if ($this->usuarioModel->eliminar($id)) {
            flashMessage('success', 'Usuario eliminado correctamente.');
        } else {
            flashMessage('error', 'Error al eliminar el usuario.');
        }
        redirect('/admin/usuarios');
    }

    public function desbloquear(): void
    {
        AuthMiddleware::requireRole('administrador');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/usuarios');
        }
        if (!validateCsrf($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'CSRF inválido.');
            redirect('/admin/usuarios');
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($this->usuarioModel->desbloquear($id)) {
            flashMessage('success', 'Usuario desbloqueado correctamente.');
        } else {
            flashMessage('error', 'Error al desbloquear el usuario.');
        }
        redirect('/admin/usuarios');
    }
}