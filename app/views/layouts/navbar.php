<?php
$avatarName = $authUser['nombres'] ?? 'PI';
$avatarInitials = strtoupper(substr($avatarName, 0, 2));
?>
<div class="content-column">
    <header class="topbar">
        <button class="mobile-menu-button" type="button" data-sidebar-toggle aria-controls="sidebar" aria-label="Abrir menú">
            ☰
        </button>
        <div class="topbar-message">Tu camino hacia Saber 11, paso a paso.</div>
        <div class="topbar-actions">
            <button class="icon-button theme-toggle" id="theme-toggle" type="button" aria-label="Activar modo oscuro" title="Cambiar a modo oscuro" aria-pressed="false">☼</button>
            <a class="help-link"
                href="<?= htmlspecialchars($baseUrl . 'ayuda', ENT_QUOTES, 'UTF-8') ?>">
                <span aria-hidden="true">?</span> Ayuda
            </a>
            <a class="avatar" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>perfil" aria-label="Abrir perfil">
                <?= htmlspecialchars($avatarInitials, ENT_QUOTES, 'UTF-8') ?>
            </a>
        </div>
    </header>
    <main class="page-content">