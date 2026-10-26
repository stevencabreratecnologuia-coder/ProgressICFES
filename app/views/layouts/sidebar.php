<?php

$rol = $_SESSION['usuario_rol'] ?? '';
$esAdmin = $rol === 'admin';
$esProfesor = $rol === 'profesor';
$esGestion = $esAdmin || $esProfesor;

$navigation = [
    ['key' => 'inicio', 'label' => 'Inicio', 'icon' => '⌂', 'url' => ''],
    ['key' => 'practica', 'label' => 'Práctica', 'icon' => '▣', 'url' => $esGestion ? 'practica/admin' : 'practica'],
    ['key' => 'simulacros', 'label' => 'Simulacros', 'icon' => '◎', 'url' => $esGestion ? 'simulacros/admin' : 'simulacros'],
];

if (!$esGestion) {
    $navigation[] = ['key' => 'progreso', 'label' => 'Progreso', 'icon' => '▥', 'url' => 'progreso'];
}

$navigation[] = ['key' => 'chat', 'label' => 'ProgressIA', 'icon' => '🤖', 'url' => 'chat'];


$navigation[] = ['key' => 'perfil', 'label' => 'Perfil', 'icon' => '♙', 'url' => 'perfil'];

if ($esAdmin) {
    $navigation[] = ['key' => 'usuarios', 'label' => 'Usuarios', 'icon' => '👥', 'url' => 'usuarios'];
}

?>

<aside class="sidebar" id="sidebar">
    <a class="brand" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>" aria-label="Ir al inicio de ProgressICFES">
        <img class="brand-logo brand-logo-light" src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/progressicfes-logo.png" alt="ProgressICFES">
        <img class="brand-logo brand-logo-dark" src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/progressicfes-logo-dark.png" alt="ProgressICFES">
        <img class="brand-mark brand-mark-light" src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/progressicfes-mark.png" alt="ProgressICFES">
        <img class="brand-mark brand-mark-dark" src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/progressicfes-mark-dark.png" alt="ProgressICFES">
    </a>

    <a class="institution-brand" href="https://www.iefranciscodepaulasantander.edu.co/" target="_blank" rel="noopener noreferrer" aria-label="Visitar el sitio web de la Institucion Educativa Francisco de Paula Santander">
        <span class="institution-name">
            <span>Institucion Educativa</span>
            <strong>Francisco de Paula Santander</strong>
        </span>
        <span class="institution-crest" aria-hidden="true"><img src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/institucion-crest.png" alt=""></span>
        <svg class="institution-link-icon" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M10 13.5a4 4 0 0 0 5.66 0l2.17-2.17a4 4 0 0 0-5.66-5.66l-1.24 1.24"/>
            <path d="M14 10.5a4 4 0 0 0-5.66 0l-2.17 2.17a4 4 0 0 0 5.66 5.66l1.24-1.24"/>
        </svg>
    </a>

    <nav class="sidebar-nav" aria-label="Navegación principal">
        <?php foreach ($navigation as $item): ?>
            <a class="nav-link <?= $activePage === $item['key'] ? 'is-active' : '' ?>"
                href="<?= htmlspecialchars($baseUrl . $item['url'], ENT_QUOTES, 'UTF-8') ?>">
                <span class="nav-icon" aria-hidden="true"><?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-bottom">
        <a class="nav-link <?= $activePage === 'ayuda' ? 'is-active' : '' ?>"
            href="<?= htmlspecialchars($baseUrl . 'ayuda', ENT_QUOTES, 'UTF-8') ?>">
            <span class="nav-icon" aria-hidden="true">?</span>
            <span>Ayuda</span>
        </a>

        <button class="collapse-button" type="button" data-sidebar-toggle aria-controls="sidebar" aria-expanded="true">
            <span aria-hidden="true">‹</span>
            Contraer
        </button>

        <form action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>auth/logout" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <button class="logout-button" type="submit">
                <span aria-hidden="true">↪</span>
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>