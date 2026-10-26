<section class="practica-result-header">
    <p class="eyebrow">RESULTADO</p>
    <h1>Obtuviste <?= (int) $resultado['correctas'] ?> de <?= (int) $resultado['total'] ?> correctas.</h1>
    <p class="practica-subtitle"><?= (int) $resultado['porcentaje'] ?>% de aciertos en esta práctica.</p>
    <div class="practica-result-actions">
        <a class="primary-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica">Practicar de nuevo <span aria-hidden="true">→</span></a>
        <a class="secondary-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>">Volver al inicio</a>
    </div>
</section>

<?php if (!empty($resultado['prompt_ia'])): ?>
    <section class="prompt-ia-card">
        <p class="eyebrow">PARA UNA IA</p>
        <h2>Pégale esto a ProgressIA (o a otra IA) para que te ayude</h2>
        <p class="prompt-ia-subtitle">Ya armamos un resumen de esta práctica. Cópialo y pégalo donde quieras pedir ayuda.</p>
        <textarea class="prompt-ia-textarea" data-prompt-ia readonly rows="8"><?= htmlspecialchars($resultado['prompt_ia'], ENT_QUOTES, 'UTF-8') ?></textarea>
        <div class="prompt-ia-actions">
            <button type="button" class="secondary-button" data-copy-prompt>Copiar</button>
            <a class="secondary-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>chat">Abrir ProgressIA</a>
            <span class="prompt-ia-copied" data-copy-feedback hidden>¡Copiado!</span>
        </div>
    </section>
<?php endif; ?>

<?php $planRefuerzo = $resultado['plan_refuerzo'] ?? null; ?>
<?php if (is_array($planRefuerzo) && !isset($planRefuerzo['error'])): ?>
    <section class="progressia-refuerzo">
        <div class="progressia-refuerzo-header">
            <div>
                <span class="progressia-error-help-badge">ProgressIA</span>
                <p class="eyebrow">PLAN AUTOMÁTICO DE REFUERZO</p>
                <h2>Detecté tus puntos de mejora</h2>
                <p><?= htmlspecialchars((string) ($planRefuerzo['resumen'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="progressia-refuerzo-priority">
                Prioridad: <?= htmlspecialchars(ucfirst((string) ($planRefuerzo['nivel_prioridad'] ?? 'media')), ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>

        <?php if (!empty($planRefuerzo['temas_prioritarios'])): ?>
            <div class="progressia-refuerzo-topics">
                <h3>Temas que debes reforzar</h3>
                <div class="progressia-refuerzo-topic-list">
                    <?php foreach ($planRefuerzo['temas_prioritarios'] as $tema): ?>
                        <article class="progressia-refuerzo-topic">
                            <strong><?= htmlspecialchars((string) ($tema['tema'] ?? 'Tema'), ENT_QUOTES, 'UTF-8') ?></strong>
                            <span><?= (int) ($tema['cantidad_errores'] ?? 0) ?> error(es) · Prioridad <?= htmlspecialchars((string) ($tema['prioridad'] ?? 'media'), ENT_QUOTES, 'UTF-8') ?></span>
                            <p><?= htmlspecialchars((string) ($tema['accion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="progressia-refuerzo-plan">
            <h3>Tu plan de refuerzo</h3>
            <div class="progressia-refuerzo-days">
                <?php foreach ($planRefuerzo['plan'] as $dia): ?>
                    <article class="progressia-refuerzo-day">
                        <span>Día <?= (int) ($dia['dia'] ?? 1) ?></span>
                        <h4><?= htmlspecialchars((string) ($dia['objetivo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h4>
                        <p><?= htmlspecialchars((string) ($dia['actividad'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                        <small><?= (int) ($dia['cantidad'] ?? 0) ?> ejercicio(s) · <?= htmlspecialchars((string) ($dia['recomendacion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($planRefuerzo['mensaje_motivador'])): ?>
            <p class="progressia-refuerzo-motivation"><?= htmlspecialchars((string) $planRefuerzo['mensaje_motivador'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </section>
<?php elseif (is_array($planRefuerzo) && isset($planRefuerzo['error'])): ?>
    <section class="progressia-refuerzo progressia-refuerzo-error">
        <span class="progressia-error-help-badge">ProgressIA</span>
        <h2>Plan de refuerzo</h2>
        <p><?= htmlspecialchars((string) $planRefuerzo['error'], ENT_QUOTES, 'UTF-8') ?></p>
    </section>
<?php elseif ((int) $resultado['correctas'] === (int) $resultado['total'] && (int) $resultado['total'] > 0): ?>
    <section class="progressia-refuerzo">
        <span class="progressia-error-help-badge">ProgressIA</span>
        <h2>Excelente resultado</h2>
        <p>No se detectaron errores que requieran un plan de refuerzo en esta práctica. Sigue practicando para mantener tu nivel.</p>
    </section>
<?php endif; ?>

<section class="practica-review">
    <?php foreach ($resultado['detalle'] as $indice => $item): ?>
        <?php $pregunta = $item['pregunta']; ?>
        <article class="review-card <?= $item['es_correcta'] ? 'is-correct' : 'is-incorrect' ?>">
            <p class="quiz-card-number">Pregunta <?= $indice + 1 ?> · <?= $item['es_correcta'] ? 'Correcta' : 'Incorrecta' ?></p>
            <h3 class="quiz-card-enunciado"><?= htmlspecialchars($pregunta['enunciado'], ENT_QUOTES, 'UTF-8') ?></h3>

            <div class="quiz-options">
                <?php foreach (['a', 'b', 'c', 'd'] as $letra): ?>
                    <?php $textoOpcion = $pregunta['opcion_' . $letra] ?? null; ?>
                    <?php if ($textoOpcion === null || $textoOpcion === ''): ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <?php
                    $respuestaCorrecta = strtolower(trim((string) $pregunta['respuesta_correcta']));
                    $esRespuestaCorrecta = $letra === $respuestaCorrecta;
                    $esSeleccionEstudiante = $letra === $item['opcion_seleccionada'];
                    $clases = 'quiz-option review-option';
                    if ($esRespuestaCorrecta) {
                        $clases .= ' is-correct-answer';
                    }
                    if ($esSeleccionEstudiante && !$esRespuestaCorrecta) {
                        $clases .= ' is-wrong-answer';
                    }
                    ?>
                    <div class="<?= $clases ?>">
                        <span class="quiz-option-letter"><?= strtoupper($letra) ?></span>
                        <span class="quiz-option-text"><?= htmlspecialchars($textoOpcion, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($pregunta['explicacion'])): ?>
                <p class="review-explicacion"><strong>Explicación:</strong> <?= htmlspecialchars($pregunta['explicacion'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <?php if (!$item['es_correcta']): ?>
                <div
                    class="progressia-error-help"
                    data-progressia-error-help
                    data-origen="practica"
                    data-pregunta-id="<?= (int) $pregunta['id'] ?>">
                    <div class="progressia-error-help-header">
                        <div>
                            <span class="progressia-error-help-badge">ProgressIA</span>
                            <strong>¿Quieres entender por qué fallaste?</strong>
                            <p>Te daré pistas y te guiaré paso a paso sin darte directamente la respuesta.</p>
                        </div>
                        <button type="button" class="progressia-help-button" data-progressia-start>
                            Pedir orientación
                        </button>
                    </div>
                    <div class="progressia-error-chat" data-progressia-chat hidden>
                        <div class="progressia-error-messages" data-progressia-messages></div>
                        <form class="progressia-error-form" data-progressia-form>
                            <input type="text" data-progressia-input placeholder="Escribe qué parte no entendiste..." autocomplete="off">
                            <button type="submit">Enviar</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>

<link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL . 'css/chat.css', ENT_QUOTES, 'UTF-8') ?>?v=<?= filemtime(BASE_PATH . '/public/css/chat.css') ?>">
<script>
    window.baseUrl = "<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>";
    window.progressiaCsrfToken = "<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>";
</script>
<script src="<?= htmlspecialchars(BASE_URL . 'js/error-tutor.js', ENT_QUOTES, 'UTF-8') ?>?v=<?= filemtime(BASE_PATH . '/public/js/error-tutor.js') ?>"></script>