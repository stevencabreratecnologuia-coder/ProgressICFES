<?php if (($esVistaAdmin ?? false) === true): ?>
    <a class="back-link"
        href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>">
        ← Volver al dashboard de administración
    </a>
<?php endif; ?>

<section class="practica-intro">
    <p class="eyebrow">PANEL DEL PROFESOR</p>
    <?php if ($materia !== null): ?>
        <h1>Rendimiento en <?= htmlspecialchars($materia['nombre'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="practica-subtitle">Así les está yendo a los estudiantes en tu materia, entre práctica libre y simulacros.</p>
    <?php else: ?>
        <h1>Todavía no tienes una materia asignada.</h1>
        <p class="practica-subtitle">Pídele a un administrador que te asigne una materia desde la sección de Usuarios para poder ver el rendimiento de tus estudiantes aquí.</p>
    <?php endif; ?>
</section>

<?php if ($materia !== null): ?>
    <section class="progreso-section">
        <h2 class="progreso-section-title">Práctica libre</h2>

        <?php if ($rendimientoPractica['total'] === 0): ?>
            <p class="practica-subtitle">Todavía no hay práctica registrada en esta materia.</p>
        <?php else: ?>
            <section class="resumen-grid">
                <div class="resumen-card">
                    <span class="resumen-card-value"><?= (int) $rendimientoPractica['estudiantes'] ?></span>
                    <span class="resumen-card-label">Estudiantes que han practicado</span>
                </div>
                <div class="resumen-card">
                    <span class="resumen-card-value"><?= (int) $rendimientoPractica['total'] ?></span>
                    <span class="resumen-card-label">Preguntas respondidas</span>
                </div>
                <div class="resumen-card is-highlight">
                    <span class="resumen-card-value"><?= (int) $rendimientoPractica['porcentaje'] ?>%</span>
                    <span class="resumen-card-label">Aciertos promedio</span>
                </div>
            </section>
        <?php endif; ?>
    </section>

    <section class="progreso-section">
        <h2 class="progreso-section-title">Simulacros</h2>

        <?php if ($rendimientoSimulacro['total'] === 0): ?>
            <p class="practica-subtitle">Todavía no hay simulacros presentados en esta materia.</p>
        <?php else: ?>
            <section class="resumen-grid">
                <div class="resumen-card">
                    <span class="resumen-card-value"><?= (int) $rendimientoSimulacro['estudiantes'] ?></span>
                    <span class="resumen-card-label">Estudiantes que han presentado</span>
                </div>
                <div class="resumen-card">
                    <span class="resumen-card-value"><?= (int) $rendimientoSimulacro['total'] ?></span>
                    <span class="resumen-card-label">Preguntas respondidas</span>
                </div>
                <div class="resumen-card is-highlight">
                    <span class="resumen-card-value"><?= (int) $rendimientoSimulacro['porcentaje'] ?>%</span>
                    <span class="resumen-card-label">Aciertos promedio</span>
                </div>
            </section>
        <?php endif; ?>
    </section>


    <section class="progreso-section profesor-grados-section">
        <h2 class="progreso-section-title">Rendimiento por grado</h2>

        <p class="practica-subtitle">
            Consulta cómo va cada grado y revisa el rendimiento individual
            de los estudiantes de tu materia.
        </p>

        <?php if (empty($estudiantesPorGrado ?? [])): ?>
            <div class="profesor-grados-empty">
                <p>
                    Todavía no hay estudiantes registrados en los grados
                    de esta materia.
                </p>
            </div>
        <?php else: ?>
            <div class="profesor-grados-list">
                <?php foreach ($estudiantesPorGrado as $grupo): ?>
                    <?php
                    $cantidadEstudiantes = count($grupo['estudiantes']);
                    $promedioGrupo = (int) $grupo['promedio'];
                    ?>

                    <article class="profesor-grado-card">
                        <div class="profesor-grado-header">
                            <div>
                                <span class="profesor-grado-eyebrow">
                                    GRADO
                                </span>

                                <h3>
                                    <?= (int) $grupo['grado'] ?>°
                                </h3>

                                <p>
                                    <?= $cantidadEstudiantes ?>
                                    estudiante<?= $cantidadEstudiantes !== 1 ? 's' : '' ?>
                                </p>
                            </div>

                            <div class="profesor-grado-promedio">
                                <span>Promedio del grupo</span>
                                <strong>
                                    <?= $promedioGrupo ?>%
                                </strong>
                            </div>
                        </div>

                        <div class="profesor-grado-barra" aria-hidden="true">
                            <div
                                class="profesor-grado-barra-fill"
                                style="width: <?= min(100, max(0, $promedioGrupo)) ?>%;">
                            </div>
                        </div>

                        <div class="profesor-estudiantes-list">
                            <?php foreach ($grupo['estudiantes'] as $estudiante): ?>
                                <div class="profesor-estudiante-row">
                                    <div class="profesor-estudiante-info">
                                        <strong>
                                            <?= htmlspecialchars(
                                                $estudiante['nombre'] . ' ' . $estudiante['apellido'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>

                                        <span>
                                            <?= (int) $estudiante['total_practica'] ?>
                                            pregunta<?= (int) $estudiante['total_practica'] !== 1 ? 's' : '' ?>
                                            respondida<?= (int) $estudiante['total_practica'] !== 1 ? 's' : '' ?>
                                        </span>
                                    </div>

                                    <div class="profesor-estudiante-rendimiento">
                                        <div class="profesor-estudiante-barra" aria-hidden="true">
                                            <div
                                                class="profesor-estudiante-barra-fill"
                                                style="width: <?= min(100, max(0, (int) $estudiante['porcentaje_practica'])) ?>%;">
                                            </div>
                                        </div>

                                        <strong>
                                            <?= (int) $estudiante['porcentaje_practica'] ?>%
                                        </strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>