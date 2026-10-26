<?php

declare(strict_types=1);

abstract class Controller
{
    /** @param array<string, mixed> $data */
    protected function render(string $view, array $data = []): void
    {
        $viewPath = BASE_PATH . '/app/views/' . trim($view, '/') . '.php';

        if (!is_file($viewPath)) {
            throw new RuntimeException("La vista '{$view}' no existe.");
        }

        $pageTitle = $data['pageTitle'] ?? 'ProgressICFES';
        $activePage = $data['activePage'] ?? '';
        $baseUrl = BASE_URL;
        $authUser = $data['authUser'] ?? $this->currentUser();
        $csrfToken = $this->csrfToken();
        $bodyClass = 'app-body';

        extract($data, EXTR_SKIP);

        require BASE_PATH . '/app/views/layouts/header.php';
        require BASE_PATH . '/app/views/layouts/sidebar.php';
        require BASE_PATH . '/app/views/layouts/navbar.php';
        require $viewPath;
        require BASE_PATH . '/app/views/layouts/footer.php';
    }

    /** @param array<string, mixed> $data */
    protected function renderAuth(string $view, array $data = []): void
    {
        $viewPath = BASE_PATH . '/app/views/' . trim($view, '/') . '.php';

        if (!is_file($viewPath)) {
            throw new RuntimeException("La vista '{$view}' no existe.");
        }

        $pageTitle = $data['pageTitle'] ?? 'ProgressICFES';
        $baseUrl = BASE_URL;
        $csrfToken = $this->csrfToken();
        $bodyClass = 'auth-body';

        extract($data, EXTR_SKIP);

        require BASE_PATH . '/app/views/layouts/header.php';
        require $viewPath;
        require BASE_PATH . '/app/views/layouts/auth_footer.php';
    }

    /** @return array{id: int, nombres: string, rol: string}|null */
    protected function currentUser(): ?array
    {
        if (!isset($_SESSION['usuario_id'], $_SESSION['usuario_nombres'], $_SESSION['usuario_rol'])) {
            return null;
        }

        return [
            'id' => (int) $_SESSION['usuario_id'],
            'nombres' => (string) $_SESSION['usuario_nombres'],
            'rol' => (string) $_SESSION['usuario_rol'],
        ];
    }

    protected function requireAuthentication(): void
    {
        if ($this->currentUser() === null) {
            $this->redirect('auth/login');
        }
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . ltrim($path, '/'));
        exit;
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['csrf_token'];
    }

    protected function validCsrfToken(mixed $token): bool
    {
        return is_string($token)
            && isset($_SESSION['csrf_token'])
            && hash_equals((string) $_SESSION['csrf_token'], $token);
    }
}
