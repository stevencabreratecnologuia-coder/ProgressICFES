<?php

declare(strict_types=1);

class AuthController extends Controller
{
    public function login(): void
    {
        if ($this->currentUser() !== null) {
            $this->redirect('');
            return;
        }

        $registro = (string) ($_GET['registro'] ?? '');

        $message = '';

        if ($registro === 'exitoso') {
            $message =
                'Tu cuenta fue creada. Ahora puedes iniciar sesión.';
        }

        if ($registro === 'pendiente') {
            $message =
                'Tu solicitud como profesor fue enviada correctamente. '
                . 'Debes esperar la aprobación de un administrador.';
        }

        $this->showLogin([], '', $message);
    }

    public function procesarLogin(): void
    {
        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->showLogin([
                'general' =>
                'La sesión del formulario expiró. Inténtalo de nuevo.'
            ]);

            return;
        }

        $email = strtolower(
            trim((string) ($_POST['correo'] ?? ''))
        );

        $password = (string) ($_POST['password'] ?? '');

        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL)
            || $password === ''
        ) {
            $this->showLogin([
                'general' =>
                'Ingresa un correo y contraseña válidos.'
            ], $email);

            return;
        }

        try {
            $user = (new Usuario())->findForLogin($email);
        } catch (PDOException) {
            $this->showLogin([
                'general' =>
                'No fue posible conectar con la base de datos. '
                    . 'Revisa la instalación.'
            ], $email);

            return;
        }

        if ($user === null) {
            $this->showLogin([
                'general' =>
                'El correo o la contraseña no son correctos.'
            ], $email);

            return;
        }

        if (!password_verify(
            $password,
            (string) $user['password']
        )) {
            $this->showLogin([
                'general' =>
                'El correo o la contraseña no son correctos.'
            ], $email);

            return;
        }

        /*
         * La aprobación pendiente/rechazada
         * solamente aplica a profesores.
         */
        if (
            $user['rol'] === 'profesor'
            && $user['estado'] === 'pendiente'
        ) {
            $this->showLogin([
                'general' =>
                'Tu cuenta de profesor todavía está pendiente '
                    . 'de aprobación por un administrador.'
            ], $email);

            return;
        }

        if (
            $user['rol'] === 'profesor'
            && $user['estado'] === 'rechazado'
        ) {
            $this->showLogin([
                'general' =>
                'La solicitud para crear tu cuenta de profesor '
                    . 'fue denegada por el administrador.'
            ], $email);

            return;
        }

        /*
         * Los estudiantes se crean directamente como activos.
         * No pasan por aprobación.
         */
        if (
            $user['rol'] === 'estudiante'
            && $user['estado'] !== 'activo'
        ) {
            $this->showLogin([
                'general' =>
                'Tu cuenta no está disponible actualmente.'
            ], $email);

            return;
        }

        /*
         * Cualquier otro estado no válido
         * bloquea el inicio de sesión.
         */
        if (
            $user['rol'] !== 'profesor'
            && $user['rol'] !== 'estudiante'
            && $user['estado'] !== 'activo'
        ) {
            $this->showLogin([
                'general' =>
                'Tu cuenta no está disponible actualmente.'
            ], $email);

            return;
        }

        /*
         * Un profesor solamente puede entrar
         * cuando su cuenta está activa.
         */
        if (
            $user['rol'] === 'profesor'
            && $user['estado'] !== 'activo'
        ) {
            $this->showLogin([
                'general' =>
                'Tu cuenta no está disponible actualmente.'
            ], $email);

            return;
        }

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = (int) $user['id'];
        $_SESSION['usuario_nombres'] = $user['nombre'];
        $_SESSION['usuario_rol'] = $user['rol'];

        $this->redirect('');
    }

    public function registro(): void
    {
        if ($this->currentUser() !== null) {
            $this->redirect('');
            return;
        }

        $materias = (new Materia())->obtenerTodas();

        $this->showRegistration([], [], $materias);
    }

    public function procesarRegistro(): void
    {
        if (!$this->validCsrfToken($_POST['csrf_token'] ?? null)) {
            $materias = (new Materia())->obtenerTodas();

            $this->showRegistration([
                'general' =>
                'La sesión del formulario expiró. Inténtalo de nuevo.'
            ], [], $materias);

            return;
        }

        $tipoCuenta = (string) (
            $_POST['tipo_cuenta'] ?? 'estudiante'
        );

        $data = [
            'nombres' => $this->normalizeName(
                $_POST['nombres'] ?? ''
            ),

            'apellidos' => $this->normalizeName(
                $_POST['apellidos'] ?? ''
            ),

            'correo' => strtolower(
                trim((string) ($_POST['correo'] ?? ''))
            ),

            'password' => (string) (
                $_POST['password'] ?? ''
            ),

            'tipo_cuenta' => $tipoCuenta,

            /*
             * El grado solamente se utiliza
             * cuando la cuenta es de estudiante.
             */
            'grado' => (int) (
                $_POST['grado'] ?? 0
            ),

            /*
             * Estos campos solamente se utilizan
             * cuando la cuenta es de profesor.
             */
            'materia_id' => (int) (
                $_POST['materia_id'] ?? 0
            ),

            'documento_identidad' => trim(
                (string) ($_POST['documento_identidad'] ?? '')
            ),
        ];

        $confirmation = (string) (
            $_POST['confirmar_password'] ?? ''
        );

        $errors = $this->validateRegistration(
            $data,
            $confirmation
        );

        $materias = (new Materia())->obtenerTodas();

        if ($errors !== []) {
            $this->showRegistration(
                $errors,
                $data,
                $materias
            );

            return;
        }

        try {
            $users = new Usuario();

            if ($users->emailExists($data['correo'])) {
                $this->showRegistration([
                    'correo' =>
                    'Ya existe una cuenta o solicitud con este correo.'
                ], $data, $materias);

                return;
            }

            /*
             * PROFESOR:
             *
             * Se crea como pendiente.
             * El administrador debe aprobarlo.
             */
            if ($tipoCuenta === 'profesor') {
                $users->createTeacherRequest($data);

                $this->redirect(
                    'auth/login?registro=pendiente'
                );

                return;
            }

            /*
             * ESTUDIANTE:
             *
             * Se crea directamente como activo.
             * NO necesita aprobación.
             */
            $users->createStudent($data);
        } catch (PDOException $exception) {
            $message = $exception->getCode() === '23000'
                ? 'Ya existe una cuenta o solicitud con estos datos.'
                : 'No fue posible crear la cuenta. '
                . 'Verifica la base de datos e inténtalo nuevamente.';

            $this->showRegistration(
                ['general' => $message],
                $data,
                $materias
            );

            return;
        }

        $this->redirect(
            'auth/login?registro=exitoso'
        );
    }

    public function cerrarSesion(): void
    {
        if (!$this->validCsrfToken(
            $_POST['csrf_token'] ?? null
        )) {
            $this->redirect('auth/login');
            return;
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parameters = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parameters['path'],
                $parameters['domain'],
                $parameters['secure'],
                $parameters['httponly']
            );
        }

        session_destroy();

        $this->redirect('auth/login');
    }

    private function showLogin(
        array $errors = [],
        string $email = '',
        string $message = ''
    ): void {
        $this->renderAuth('auth/login', [
            'pageTitle' =>
            'Iniciar sesión | ProgressICFES',

            'errors' => $errors,

            'email' => $email,

            'message' => $message,
        ]);
    }

    private function showRegistration(
        array $errors = [],
        array $data = [],
        array $materias = []
    ): void {
        $this->renderAuth('auth/registro', [
            'pageTitle' =>
            'Crear cuenta | ProgressICFES',

            'errors' => $errors,

            'data' => $data,

            'materias' => $materias,
        ]);
    }

    private function validateRegistration(
        array $data,
        string $confirmation
    ): array {
        $errors = [];

        if (
            !preg_match(
                "/^[\p{L}][\p{L} '\-]{1,59}$/u",
                $data['nombres']
            )
        ) {
            $errors['nombres'] =
                'Escribe tus nombres usando al menos 2 letras.';
        }

        if (
            !preg_match(
                "/^[\p{L}][\p{L} '\-]{1,59}$/u",
                $data['apellidos']
            )
        ) {
            $errors['apellidos'] =
                'Escribe tus apellidos usando al menos 2 letras.';
        }

        if (!filter_var(
            $data['correo'],
            FILTER_VALIDATE_EMAIL
        )) {
            $errors['correo'] =
                'Escribe un correo electrónico válido.';
        }

        if (
            strlen($data['password']) < 8
            || !preg_match(
                '/[A-Za-z]/',
                $data['password']
            )
            || !preg_match(
                '/\d/',
                $data['password']
            )
        ) {
            $errors['password'] =
                'La contraseña debe tener mínimo 8 caracteres, '
                . 'una letra y un número.';
        }

        if ($data['password'] !== $confirmation) {
            $errors['confirmar_password'] =
                'Las contraseñas no coinciden.';
        }

        if (
            !in_array(
                $data['tipo_cuenta'],
                ['estudiante', 'profesor'],
                true
            )
        ) {
            $errors['tipo_cuenta'] =
                'Selecciona un tipo de cuenta válido.';
        }

        /*
         * VALIDACIÓN DE ESTUDIANTE
         *
         * El grado es obligatorio únicamente
         * para estudiantes y debe estar entre 6° y 11°.
         */
        if ($data['tipo_cuenta'] === 'estudiante') {
            if (
                !in_array(
                    (int) $data['grado'],
                    [6, 7, 8, 9, 10, 11],
                    true
                )
            ) {
                $errors['grado'] =
                    'Selecciona un grado válido entre 6° y 11°.';
            }
        }

        /*
         * VALIDACIÓN DE PROFESOR
         *
         * El profesor necesita materia y documento.
         * Su cuenta quedará pendiente de aprobación.
         */
        if ($data['tipo_cuenta'] === 'profesor') {
            if ((int) $data['materia_id'] <= 0) {
                $errors['materia_id'] =
                    'Selecciona la materia que deseas enseñar.';
            }

            if (
                !preg_match(
                    '/^[0-9A-Za-z-]{5,30}$/',
                    $data['documento_identidad']
                )
            ) {
                $errors['documento_identidad'] =
                    'Escribe un número de documento válido.';
            }
        }

        return $errors;
    }

    private function normalizeName(mixed $value): string
    {
        $name = trim((string) $value);

        return preg_replace(
            '/\s+/u',
            ' ',
            $name
        ) ?? '';
    }
}
