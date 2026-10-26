<section class="practica-intro">
    <p class="eyebrow">PRÁCTICA EN CURSO</p>
    <h1>Responde con calma, cada pregunta cuenta.</h1>
    <p class="practica-subtitle"><?= count($preguntas) ?> preguntas por resolver.</p>
</section>

<form class="practica-quiz-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/responder" method="post">
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
                        <input type="radio" name="respuestas[<?= (int) $pregunta['id'] ?>]" value="<?= $letra ?>" required>
                        <span class="quiz-option-letter"><?= strtoupper($letra) ?></span>
                        <span class="quiz-option-text"><?= htmlspecialchars($textoOpcion, ENT_QUOTES, 'UTF-8') ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <button class="primary-button practica-submit" type="submit">Terminar y ver resultado <span aria-hidden="true">→</span></button>
</form>