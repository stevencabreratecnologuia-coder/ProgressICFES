<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <?php
    $cssVersion = (string) (filemtime(BASE_PATH . '/public/css/estilos.css') ?: time());
    $responsiveVersion = (string) (filemtime(BASE_PATH . '/public/css/responsive.css') ?: time());
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ProgressICFES, tu plataforma de preparación para las pruebas Saber 11.">
    <script>
        (function () {
            try {
                var theme = localStorage.getItem("progressicfes-theme");
                document.documentElement.setAttribute("data-theme", theme === "dark" ? "dark" : "light");
            } catch (e) {
                document.documentElement.setAttribute("data-theme", "light");
            }
        }());
    </script>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/progressicfes-mark.png">
    <title><?= htmlspecialchars((string) $pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>css/estilos.css?v=<?= $cssVersion ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>css/responsive.css?v=<?= $responsiveVersion ?>">
</head>
<body class="<?= htmlspecialchars($bodyClass ?? 'app-body', ENT_QUOTES, 'UTF-8') ?>">
<div class="app-shell">