<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {

    $isSecureConnection =
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (int) ($_SERVER['SERVER_PORT'] ?? 80) === 443;

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $isSecureConnection,
        'path' => '/',
    ]);

    session_start();
}


/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN PRINCIPAL
|--------------------------------------------------------------------------
*/

define('BASE_PATH', dirname(__DIR__));


$scriptDirectory = str_replace(
    '\\',
    '/',
    dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')
);

$normalizedBaseUrl = rtrim(
    $scriptDirectory,
    '/'
);

define(
    'BASE_URL',
    $normalizedBaseUrl === ''
        ? '/'
        : $normalizedBaseUrl . '/'
);


/*
|--------------------------------------------------------------------------
| BASE DE DATOS
|--------------------------------------------------------------------------
*/

require BASE_PATH . '/app/config/database.php';


/*
|--------------------------------------------------------------------------
| AUTOLOAD
|--------------------------------------------------------------------------
*/

spl_autoload_register(
    static function (string $class): void {

        $folders = [
            BASE_PATH . '/app/controllers/',
            BASE_PATH . '/app/models/',
            BASE_PATH . '/app/services/',
            BASE_PATH . '/app/services/ia/',
        ];

        foreach ($folders as $folder) {

            $classFile = $folder . $class . '.php';

            if (is_file($classFile)) {

                require $classFile;

                return;
            }
        }
    }
);


/*
|--------------------------------------------------------------------------
| OBTENER URL
|--------------------------------------------------------------------------
*/

$requestUri = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
) ?? '/';


$basePath = rtrim(
    BASE_URL,
    '/'
);


/*
|--------------------------------------------------------------------------
| ELIMINAR BASE DEL PROYECTO
|--------------------------------------------------------------------------
*/

if (
    $basePath !== ''
    && $basePath !== '/'
    && str_starts_with($requestUri, $basePath)
) {

    $requestUri = substr(
        $requestUri,
        strlen($basePath)
    );
}


$route = trim(
    $requestUri,
    '/'
);


$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';


/*
|--------------------------------------------------------------------------
| RUTAS
|--------------------------------------------------------------------------
*/

$routes = [

    /*
    |--------------------------------------------------------------------------
    | GET
    |--------------------------------------------------------------------------
    */

    'GET' => [
        /*
        | Ayuda
        */
        'ayuda' => [
            AyudaController::class,
            'index'
        ],


        /*
        | Dashboard
        */

        '' => [
            DashboardController::class,
            'index'
        ],

        'dashboard/profesor' => [
            DashboardController::class,
            'profesor'
        ],

        'dashboard/estudiante' => [
            DashboardController::class,
            'estudiante'
        ],


        /*
        | Autenticación
        */

        'auth/login' => [
            AuthController::class,
            'login'
        ],

        'auth/registro' => [
            AuthController::class,
            'registro'
        ],


        /*
        | Práctica
        */

        'practica' => [
            PracticaController::class,
            'index'
        ],

        'practica/preguntas' => [
            PracticaController::class,
            'preguntas'
        ],

        'practica/resultado' => [
            PracticaController::class,
            'resultado'
        ],

        'practica/admin/pregunta' => [
            AdminController::class,
            'formularioPregunta'
        ],

        'practica/admin' => [
            AdminController::class,
            'gestionPractica'
        ],

        'usuarios' => [
            AdminController::class,
            'usuarios'
        ],

        'usuarios/detalle' => [
            AdminController::class,
            'detalleUsuario'
        ],


        /*
        |--------------------------------------------------------------------------
        | SIMULACROS
        |--------------------------------------------------------------------------
        */

        'simulacros' => [
            SimulacroController::class,
            'index'
        ],

        'simulacro/preguntas' => [
            SimulacroController::class,
            'preguntas'
        ],

        'simulacro/resultado' => [
            SimulacroController::class,
            'resultado'
        ],

        'simulacros/formulario' => [
            AdminController::class,
            'formularioSimulacro'
        ],

        'simulacros/admin' => [
            AdminController::class,
            'simulacrosAdmin'
        ],

        'admin' => [
            AdminController::class,
            'index'
        ],


        /*
        | Progreso
        */

        'progreso' => [
            ProgresoController::class,
            'index'
        ],


        /*
        | Perfil
        */

        'perfil' => [
            PerfilController::class,
            'index'
        ],


        /*
        |--------------------------------------------------------------------------
        | ProgressIA
        |--------------------------------------------------------------------------
        */

        'chat' => [
            ChatController::class,
            'index'
        ],

        'profesor/ia' => [
            ProfesorIAController::class,
            'index'
        ],

        'profesor/simulacros' => [
            ProfesorController::class,
            'simulacros'
        ],

        'profesor/simulacro' => [
            ProfesorController::class,
            'formularioSimulacro'
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | POST
    |--------------------------------------------------------------------------
    */

    'POST' => [

        /*
        | Autenticación
        */

        'auth/login' => [
            AuthController::class,
            'procesarLogin'
        ],

        'auth/registro' => [
            AuthController::class,
            'procesarRegistro'
        ],

        'auth/logout' => [
            AuthController::class,
            'cerrarSesion'
        ],


        /*
        | Práctica
        */

        'practica/iniciar' => [
            PracticaController::class,
            'iniciar'
        ],

        'practica/responder' => [
            PracticaController::class,
            'responder'
        ],

        'practica/admin/pregunta/guardar' => [
            AdminController::class,
            'guardarPregunta'
        ],

        'practica/admin/pregunta/eliminar' => [
            AdminController::class,
            'eliminarPregunta'
        ],

        'practica/admin/materia/crear' => [
            AdminController::class,
            'crearMateria'
        ],

        'practica/admin/tema/crear' => [
            AdminController::class,
            'crearTema'
        ],

        'practica/admin/tipo/crear' => [
            AdminController::class,
            'crearTipoPregunta'
        ],

        'practica/admin/tipo/eliminar' => [
            AdminController::class,
            'eliminarTipoPregunta'
        ],

        'usuarios/rol' => [
            AdminController::class,
            'cambiarRolUsuario'
        ],

        'usuarios/materia' => [
            AdminController::class,
            'actualizarMateriaUsuario'
        ],

        'usuarios/eliminar' => [
            AdminController::class,
            'eliminarUsuario'
        ],

        'usuarios/profesor/aprobar' => [
            AdminController::class,
            'aprobarSolicitudProfesor'
        ],

        'usuarios/profesor/rechazar' => [
            AdminController::class,
            'rechazarSolicitudProfesor'
        ],


        /*
        |--------------------------------------------------------------------------
        | SIMULACROS
        |--------------------------------------------------------------------------
        */

        'simulacro/iniciar' => [
            SimulacroController::class,
            'iniciar'
        ],

        'simulacro/responder' => [
            SimulacroController::class,
            'responder'
        ],

        'simulacros/guardar' => [
            AdminController::class,
            'guardarSimulacro'
        ],

        'simulacros/eliminar' => [
            AdminController::class,
            'eliminarSimulacro'
        ],


        /*
        |--------------------------------------------------------------------------
        | ProgressIA
        |--------------------------------------------------------------------------
        */

        'chat/enviar' => [
            ChatController::class,
            'enviar'
        ],

        'chat/orientar-pregunta' => [
            ChatController::class,
            'orientarPregunta'
        ],

        'chat/cargar' => [
            ChatController::class,
            'cargar'
        ],

        'chat/eliminar' => [
            ChatController::class,
            'eliminarConversacion'
        ],

        'profesor/ia/generar' => [
            ProfesorIAController::class,
            'generar'
        ],

        'profesor/ia/publicar' => [
            ProfesorIAController::class,
            'publicar'
        ],

        'profesor/ia/descartar' => [
            ProfesorIAController::class,
            'descartar'
        ],

        'profesor/simulacro/guardar' => [
            ProfesorController::class,
            'guardarSimulacro'
        ],

        'profesor/simulacro/eliminar' => [
            ProfesorController::class,
            'eliminarSimulacro'
        ],
    ],
];


/*
|--------------------------------------------------------------------------
| VALIDAR MÉTODO
|--------------------------------------------------------------------------
*/

if (!isset($routes[$method])) {

    http_response_code(405);

    header('Allow: GET, POST');

    exit('Método no permitido.');
}


/*
|--------------------------------------------------------------------------
| VALIDAR RUTA
|--------------------------------------------------------------------------
*/

if (!isset($routes[$method][$route])) {

    foreach ($routes as $registeredRoutes) {

        if (isset($registeredRoutes[$route])) {

            http_response_code(405);

            header('Allow: GET, POST');

            exit('Método no permitido.');
        }
    }


    /*
    | Página 404
    */

    [$controllerClass, $action] = [
        ErrorController::class,
        'notFound'
    ];
} else {

    [$controllerClass, $action] =
        $routes[$method][$route];
}


/*
|--------------------------------------------------------------------------
| EJECUTAR CONTROLADOR
|--------------------------------------------------------------------------
*/

$controller = new $controllerClass();

$controller->{$action}();
