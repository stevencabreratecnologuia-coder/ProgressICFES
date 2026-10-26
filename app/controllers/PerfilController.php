<?php

declare(strict_types=1);

class PerfilController extends Controller
{
    // Muestra los datos actuales del usuario.
    public function index(): void
    {
        $this->requireAuthentication();

        $usuario = (new Usuario())->buscarPorId($this->currentUser()['id']);

        $mensajes = $_SESSION['perfil_mensajes'] ?? [];
        unset($_SESSION['perfil_mensajes']);

        $this->render('perfil/index', [
            'pageTitle' => 'Perfil | ProgressICFES',
            'activePage' => 'perfil',
            'usuario' => $usuario,
            'mensajes' => $mensajes,
        ]);
    }

    // Actualiza nombre y apellido.
    public function actualizarDatos(): void
    {
        $this->requireAuthentication();

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('perfil');
            return;
        }

        $nombre = $this->normalizarNombre($_POST['nombre'] ?? '');
        $apellido = $this->normalizarNombre($_POST['apellido'] ?? '');

        if (!preg_match("/^[\\p{L}][\\p{L} '\\-]{1,59}$/u", $nombre) || !preg_match("/^[\\p{L}][\\p{L} '\\-]{1,59}$/u", $apellido)) {
            $_SESSION['perfil_mensajes'] = ['error' => 'Escribe un nombre y un apellido válidos (mínimo 2 letras).'];
            $this->redirect('perfil');
            return;
        }

        $usuarioId = $this->currentUser()['id'];
        (new Usuario())->actualizarDatos($usuarioId, $nombre, $apellido);

        // Mantener sincronizado el nombre que se muestra en el sidebar.
        $_SESSION['usuario_nombres'] = $nombre;

        $_SESSION['perfil_mensajes'] = ['exito' => 'Tus datos se actualizaron correctamente.'];
        $this->redirect('perfil');
    }

    // Cambia la contraseña, validando la actual.
    public function actualizarPassword(): void
    {
        $this->requireAuthentication();

        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirect('perfil');
            return;
        }

        $actual = (string) ($_POST['password_actual'] ?? '');
        $nueva = (string) ($_POST['password_nueva'] ?? '');
        $confirmacion = (string) ($_POST['password_confirmacion'] ?? '');

        $usuarioModel = new Usuario();
        $usuarioId = $this->currentUser()['id'];
        $usuario = $usuarioModel->buscarPorId($usuarioId);

        if ($usuario === null || !password_verify($actual, (string) $usuario['password'])) {
            $_SESSION['perfil_mensajes'] = ['error' => 'La contraseña actual no es correcta.'];
            $this->redirect('perfil');
            return;
        }

        if (strlen($nueva) < 8 || !preg_match('/[A-Za-z]/', $nueva) || !preg_match('/\d/', $nueva)) {
            $_SESSION['perfil_mensajes'] = ['error' => 'La nueva contraseña debe tener mínimo 8 caracteres, una letra y un número.'];
            $this->redirect('perfil');
            return;
        }

        if ($nueva !== $confirmacion) {
            $_SESSION['perfil_mensajes'] = ['error' => 'La confirmación no coincide con la nueva contraseña.'];
            $this->redirect('perfil');
            return;
        }

        $usuarioModel->actualizarPassword($usuarioId, password_hash($nueva, PASSWORD_DEFAULT));

        $_SESSION['perfil_mensajes'] = ['exito' => 'Tu contraseña se actualizó correctamente.'];
        $this->redirect('perfil');
    }

    private function normalizarNombre(mixed $valor): string
    {
        $nombre = trim((string) $valor);

        return preg_replace('/\s+/u', ' ', $nombre) ?? '';
    }
}
