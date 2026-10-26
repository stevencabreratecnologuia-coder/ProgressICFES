<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>usuarios"><span aria-hidden="true">←</span> Volver a usuarios</a>

<section class="practica-intro">
    <p class="eyebrow">USUARIOS</p>
    <h1><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido'], ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="practica-subtitle">
        <?= htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8') ?>
        · <?= htmlspecialchars(ucfirst((string) $usuario['rol']), ENT_QUOTES, 'UTF-8') ?>
        · Registrado el <?= htmlspecialchars(date('d/m/Y', strtotime((string) $usuario['fecha_registro'])), ENT_QUOTES, 'UTF-8') ?>
    </p>
</section>

<section class="resumen-grid">
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $resumen['preguntas_practica'] ?></span>
        <span class="resumen-card-label">Preguntas practicadas</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $resumen['porcentaje_practica'] ?>%</span>
        <span class="resumen-card-label">Aciertos en práctica</span>
    </div>
    <div class="resumen-card">
        <span class="resumen-card-value"><?= (int) $resumen['intentos_simulacro'] ?></span>
        <span class="resumen-card-label">Simulacros presentados</span>
    </div>
    <div class="resumen-card is-highlight">
        <span class="resumen-card-value"><?= (int) $resumen['mejor_puntaje_icfes'] ?><small>/500</small></span>
        <span class="resumen-card-label">Mejor puntaje ICFES</span>
    </div>
</section>

<section class="progreso-section">
    <h2 class="progreso-section-title">Práctica libre, por materia</h2>

    <?php if ($porMateriaPractica === []): ?>
        <p class="practica-subtitle">Este usuario todavía no tiene práctica registrada.</p>
    <?php else: ?>
        <div class="materia-card-grid">
            <?php foreach ($porMateriaPractica as $fila): ?>
                <div class="materia-card <?= htmlspecialchars($fila['clase_color'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="materia-radial" style="--pct: <?= (int) $fila['porcentaje'] ?>;">
                        <span class="materia-radial-value"><?= (int) $fila['porcentaje'] ?>%</span>
                    </div>
                    <div class="materia-card-info">
                        <span class="materia-card-name"><?= htmlspecialchars($fila['materia'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="materia-card-detail"><?= (int) $fila['correctas'] ?>/<?= (int) $fila['total'] ?> correctas</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="progreso-section">
    <h2 class="progreso-section-title">Simulacros, por materia</h2>

    <?php if ($porMateriaSimulacro === []): ?>
        <p class="practica-subtitle">Este usuario todavía no ha presentado simulacros.</p>
    <?php else: ?>
        <div class="materia-card-grid">
            <?php foreach ($porMateriaSimulacro as $fila): ?>
                <div class="materia-card <?= htmlspecialchars($fila['clase_color'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="materia-radial" style="--pct: <?= (int) $fila['porcentaje'] ?>;">
                        <span class="materia-radial-value"><?= (int) $fila['porcentaje'] ?>%</span>
                    </div>
                    <div class="materia-card-info">
                        <span class="materia-card-name"><?= htmlspecialchars($fila['materia'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="materia-card-detail"><?= (int) $fila['correctas'] ?>/<?= (int) $fila['total'] ?> correctas</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($historialSimulacros !== []): ?>
        <h3 class="progreso-subtitle">Últimos intentos</h3>
        <div class="progreso-historial">
            <?php foreach ($historialSimulacros as $intento): ?>
                <div class="progreso-historial-item">
                    <span class="progreso-historial-icono">🎯</span>
                    <div class="progreso-historial-texto">
                        <span class="progreso-historial-titulo"><?= htmlspecialchars((string) $intento['titulo'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="progreso-historial-fecha">
                            <?= htmlspecialchars(date('d/m/Y', strtotime((string) $intento['fecha_realizacion'])), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <span class="progreso-historial-puntaje"><?= (int) $intento['puntaje_estimado_icfes'] ?>/500</span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>