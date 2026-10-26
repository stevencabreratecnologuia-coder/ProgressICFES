<section class="practica-intro">
    <p class="eyebrow">PANEL DE ADMINISTRACIÓN</p>
    <h1>Hola, Admin 👋</h1>
    <p class="practica-subtitle">Este es el resumen general de ProgressICFES hoy.</p>
</section>

<section class="resumen-grid">
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_estudiantes'] ?></span>
        <span class="resumen-card-label">Estudiantes</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_docentes'] ?></span>
        <span class="resumen-card-label">Profesores / admins</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_preguntas'] ?></span>
        <span class="resumen-card-label">Preguntas en el banco</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_materias'] ?></span>
        <span class="resumen-card-label">Materias</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_intentos_practica'] ?></span>
        <span class="resumen-card-label">Prácticas realizadas</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_intentos_simulacro'] ?></span>
        <span class="resumen-card-label">Simulacros presentados</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_simulacros'] ?></span>
        <span class="resumen-card-label">Simulacros creados</span>
    </div>
    <div class="resumen-card is-highlight">
        <span class="resumen-card-value"><?= (int) $estadisticas['promedio_general'] ?>%</span>
        <span class="resumen-card-label">Aciertos promedio (todos)</span>
    </div>
</section>

<h2 class="progreso-section-title">Accesos rápidos</h2>
<h2 class="progreso-section-title">Ver dashboards</h2>

<section class="admin-links">
    <a class="admin-link-card"
        href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>dashboard/profesor">

        <span class="admin-link-title">Ver como profesor</span>

        <span class="admin-link-desc">
            Visualiza el dashboard correspondiente a los profesores.
        </span>
    </a>

    <a class="admin-link-card"
        href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>dashboard/estudiante">

        <span class="admin-link-title">Ver como estudiante</span>

        <span class="admin-link-desc">
            Visualiza cómo se muestra la plataforma desde el dashboard de un estudiante.
        </span>
    </a>
</section>

<section class="admin-links">
    <a class="admin-link-card" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin">
        <span class="admin-link-title">Práctica</span>
        <span class="admin-link-desc">Gestiona materias, temas, tipos de pregunta y el banco de preguntas.</span>
    </a>
    <a class="admin-link-card" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>simulacros/admin">
        <span class="admin-link-title">Simulacros</span>
        <span class="admin-link-desc">Crea, edita y elimina simulacros para tus estudiantes.</span>
    </a>
    <a class="admin-link-card" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>usuarios">
        <span class="admin-link-title">Usuarios</span>
        <span class="admin-link-desc">Ve, cambia el rol, o elimina estudiantes y profesores.</span>
    </a>
</section>