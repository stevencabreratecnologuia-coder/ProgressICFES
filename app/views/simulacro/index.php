<section class="practica-intro">
    <p class="eyebrow">SIMULACROS</p>
    <h1>Pon a prueba lo que sabes.</h1>
    <p class="practica-subtitle">Presenta simulacros cronometrados creados por tus docentes.</p>
</section>

<?php if (isset($errors['general'])): ?>
    <div class="alert alert-error" role="alert"><?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php
$historial = $historial ?? [];
$resumen = $resumen ?? ['total' => 0, 'mejor' => 0, 'promedio' => 0];
?>

<?php if (true): ?>
    <div class="simulacros-layout">
        <div class="simulacro-grid">
            <?php if ($simulacros === []): ?>
                <article class="simulacro-card simulacro-vacio">
                    <p class="eyebrow">TODO AL DÍA</p>
                    <h2 class="simulacro-card-titulo">No tienes simulacros pendientes.</h2>
                    <p class="aside-empty">
                        <?= (int) $resumen['total'] > 0
                            ? 'Ya presentaste todos los simulacros disponibles. Cuando tus docentes publiquen uno nuevo, aparecerá aquí.'
                            : 'Todavía no hay simulacros disponibles. Vuelve pronto.' ?>
                    </p>
                </article>
            <?php endif; ?>
            <?php foreach ($simulacros as $simulacro): ?>
                <article class="simulacro-card">
                    <p class="eyebrow"><?= htmlspecialchars($simulacro['materia_nombre'] ?? 'General', ENT_QUOTES, 'UTF-8') ?></p>
                    <h2 class="simulacro-card-titulo"><?= htmlspecialchars($simulacro['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <div class="simulacro-card-meta">
                        <span><?= (int) $simulacro['num_preguntas'] ?> preguntas</span>
                        <span><?= (int) $simulacro['tiempo_limite_min'] ?> min</span>
                    </div>
                    <form action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>simulacro/iniciar" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="simulacro_id" value="<?= (int) $simulacro['id'] ?>">
                        <button class="primary-button simulacro-submit" type="submit">Presentar simulacro <span aria-hidden="true">→</span></button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>

        <aside class="simulacros-aside" aria-label="Tu progreso y recomendaciones">
            <section class="aside-card">
                <p class="eyebrow">TU PROGRESO</p>

                <?php if ((int) $resumen['total'] === 0): ?>
                    <p class="aside-empty">Aún no has presentado ningún simulacro. Tu primer resultado aparecerá aquí.</p>
                <?php else: ?>
                    <div class="aside-stats">
                        <div><strong><?= (int) $resumen['total'] ?></strong><span>Presentados</span></div>
                        <div><strong><?= (int) $resumen['mejor'] ?></strong><span>Mejor / 500</span></div>
                        <div><strong><?= (int) $resumen['promedio'] ?></strong><span>Promedio / 500</span></div>
                    </div>

                    <ul class="aside-history">
                        <?php foreach ($historial as $item): ?>
                            <li>
                                <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>simulacro/resultado?id=<?= (int) $item['id'] ?>">
                                    <span class="aside-history-title"><?= htmlspecialchars((string) $item['titulo'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="aside-history-meta">
                                        <?= htmlspecialchars(date('d/m/Y', strtotime((string) $item['fecha_realizacion'])), ENT_QUOTES, 'UTF-8') ?>
                                        · <?= (int) $item['puntaje_estimado_icfes'] ?>/500
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="aside-card aside-tips">
                <p class="eyebrow">ANTES DE EMPEZAR</p>
                <ul>
                    <li>Busca un lugar tranquilo y sin distracciones.</li>
                    <li>El tiempo corre desde que empiezas y no se puede pausar.</li>
                    <li>Si se acaba el tiempo, se envía automáticamente.</li>
                    <li>Al terminar podrás ver tu resultado y pedir ayuda a ProgressIA.</li>
                </ul>
            </section>
        </aside>
    </div>
<?php endif; ?>