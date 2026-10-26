<section class="practica-intro">
    <p class="eyebrow">SIMULACRO EN CURSO</p>
    <h1><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="practica-subtitle"><?= count($preguntas) ?> preguntas · tiempo límite: <?= (int) $tiempoLimiteMin ?> min</p>
</section>

<div class="simulacro-timer" data-tiempo-limite-min="<?= (int) $tiempoLimiteMin ?>">
    <span class="simulacro-timer-label">Tiempo restante</span>
    <span class="simulacro-timer-valor" data-timer-display>--:--</span>
</div>

<form class="practica-quiz-form" id="simulacro-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>simulacro/responder" method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

    <?php foreach ($preguntas as $indice => $pregunta): ?>
        <article class="quiz-card">
            <p class="quiz-card-number">Pregunta <?= $indice + 1 ?> de <?= count($preguntas) ?></p>
            <h2 class="quiz-card-enunciado"><?= htmlspecialchars($pregunta['enunciado'], ENT_QUOTES, 'UTF-8') ?></h2>

            <div class="quiz-options">
                <?php foreach (['a', 'b', 'c', 'd'] as $letra): ?>
                    <?php $textoOpcion = $pregunta['opcion_' . $letra] ?? null; ?>
                    <?php if ($textoOpcion === null || $textoOpcion === ''): ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <label class="quiz-option">
                        <input type="radio" name="respuestas[<?= (int) $pregunta['id'] ?>]" value="<?= $letra ?>">
                        <span class="quiz-option-letter"><?= strtoupper($letra) ?></span>
                        <span class="quiz-option-text"><?= htmlspecialchars($textoOpcion, ENT_QUOTES, 'UTF-8') ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <button class="primary-button practica-submit" type="submit">Terminar simulacro <span aria-hidden="true">→</span></button>
</form>