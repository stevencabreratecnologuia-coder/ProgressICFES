<?php if (($esVistaAdmin ?? false) === true): ?>
    <div class="admin-dashboard-return">
        <a class="back-link"
            href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>">
            ← Volver al dashboard de administración
        </a>
    </div>
<?php endif; ?>

<section class="welcome-section">
    <div class="welcome-copy">
        <span class="coach-avatar" aria-hidden="true">PI</span>

        <div>
            <p class="eyebrow">PREPÁRATE HOY</p>
            <h1>Tu preparación para Saber 11 empieza aquí.</h1>
            <p class="welcome-text">Practica a tu ritmo, identifica lo que debes reforzar y avanza con intención.</p>
        </div>
    </div>

    <div class="daily-suggestion">
        <p class="eyebrow">RECOMENDACIÓN DE HOY</p>

        <?php if ($materiaSugerida !== null): ?>
            <h2><?= htmlspecialchars($materiaSugerida['nombre'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p>Es la materia donde más te conviene practicar ahora mismo.</p>

            <div class="suggestion-actions">
                <div class="quantity-selector" data-quantity-input="cantidad_dashboard">
                    <span>¿CUÁNTAS?</span>
                    <button class="quantity-option is-selected" type="button" data-question-count="5">5</button>
                    <button class="quantity-option" type="button" data-question-count="10">10</button>
                    <button class="quantity-option" type="button" data-question-count="15">15</button>
                </div>

                <a class="primary-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica">
                    Empezar práctica <span aria-hidden="true">→</span>
                </a>
            </div>

            <a class="text-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica">Elegir otra materia</a>
        <?php else: ?>
            <h2>Todavía no hay materias creadas.</h2>
            <p>Pídele a un administrador que registre al menos una materia para empezar a practicar.</p>
        <?php endif; ?>
    </div>
</section>

<div class="information-card">
    <span class="information-icon" aria-hidden="true">✓</span>

    <div>
        <h2>Práctica enfocada en Saber 11</h2>
        <p>Las preguntas se organizan por competencias y temas de Matemáticas, Lectura Crítica, Ciencias Naturales, Sociales y Ciudadanas e Inglés.</p>
    </div>
</div>

<div class="activity-callout">
    <div>
        <p class="activity-title">¿Tienes una actividad asignada?</p>
        <p>Revisa los simulacros que tu profesor haya programado para ti.</p>
    </div>

    <a class="secondary-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>simulacros">
        Ver simulacros <span aria-hidden="true">→</span>
    </a>
</div>

<?php if ($materias !== []): ?>
    <section class="subjects-section">
        <p class="section-kicker">EXPLORA POR MATERIA</p>
        <h2>Elige qué quieres practicar</h2>

        <?php
        $iconosMateria = [
            'matematicas' => '➗',
            'lectura' => '📖',
            'ciencias' => '🧪',
            'sociales' => '🌎',
            'ingles' => '🗣️',
        ];
        ?>

        <div class="subjects-grid">
            <?php foreach ($materias as $materia): ?>
                <a class="subject-card <?= htmlspecialchars($materia['clase'], ENT_QUOTES, 'UTF-8') ?>"
                    href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica">
                    <div class="subject-icon" aria-hidden="true"><?= $iconosMateria[$materia['icono']] ?? '📚' ?></div>
                    <strong><?= htmlspecialchars($materia['nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <small><?= htmlspecialchars($materia['descripcion'] ?: 'Practica esta materia', ENT_QUOTES, 'UTF-8') ?></small>
                    <div class="subject-cta">Practicar <span aria-hidden="true">→</span></div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>