<section class="practica-intro">
    <p class="eyebrow">PANEL DE ADMINISTRACIÓN</p>
    <h1>Resumen general de la plataforma.</h1>
    <p class="practica-subtitle">Estadísticas acumuladas de todos los estudiantes.</p>
</section>

<section class="resumen-grid">
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_estudiantes'] ?></span>
        <span class="resumen-card-label">Estudiantes</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_docentes'] ?></span>
        <span class="resumen-card-label">Docentes / admins</span>
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
        <span class="resumen-card-value"><?= (int) $estadisticas['total_simulacros'] ?></span>
        <span class="resumen-card-label">Simulacros creados</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_intentos_practica'] ?></span>
        <span class="resumen-card-label">Prácticas realizadas</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $estadisticas['total_intentos_simulacro'] ?></span>
        <span class="resumen-card-label">Simulacros presentados</span>
    </div>
    <div class="resumen-card is-highlight">
        <span class="resumen-card-value"><?= (int) $estadisticas['promedio_general'] ?>%</span>
        <span class="resumen-card-label">Aciertos promedio (todos)</span>
    </div>
</section>

<section class="admin-links">
    <a class="admin-link-card" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>usuarios">
        <span class="admin-link-title">Usuarios</span>
        <span class="admin-link-desc">Ver, cambiar rol, asignar materia o eliminar estudiantes y profesores.</span>
    </a>
    <a class="admin-link-card" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin#preguntas">
        <span class="admin-link-title">Banco de preguntas</span>
        <span class="admin-link-desc">Crear, editar y eliminar preguntas.</span>
    </a>
    <a class="admin-link-card" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin#materias">
        <span class="admin-link-title">Materias y temas</span>
        <span class="admin-link-desc">Agregar nuevas materias o temas.</span>
    </a>
    <a class="admin-link-card" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>simulacros/admin">
        <span class="admin-link-title">Simulacros</span>
        <span class="admin-link-desc">Crear y gestionar los simulacros disponibles para estudiantes.</span>
    </a>
</section>