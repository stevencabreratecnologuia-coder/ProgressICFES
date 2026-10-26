<?php

$temasPorMateria = [];

foreach ($temas as $tema) {
    $temasPorMateria[(int) $tema['materia_id']][] = $tema;
}

$resumen = $resumen ?? ['sesiones' => 0, 'preguntas' => 0, 'correctas' => 0, 'porcentaje' => 0];

?>

<section class="practica-intro">
    <p class="eyebrow">PRÁCTICA LIBRE</p>

    <h1>Elige qué quieres practicar hoy.</h1>

    <p class="practica-subtitle">
        Selecciona una materia, un tema si quieres afinar más,
        y cuántas preguntas vas a resolver.
    </p>

</section>

<?php if (isset($errors['general'])): ?>

    <div class="alert alert-error" role="alert">
        <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
    </div>

<?php endif; ?>

<div class="practica-layout">
    <form
        class="practica-form"
        action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/iniciar"
        method="post">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div class="practica-field">
            <label for="materia_id">Materia</label>

            <select id="materia_id" name="materia_id" required>
                <option value="" disabled selected>
                    Selecciona una materia
                </option>

                <?php foreach ($materias as $materia): ?>
                    <option value="<?= (int) $materia['id'] ?>">
                        <?= htmlspecialchars(
                            $materia['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- INFORMACIÓN DEL NIVEL DE INGLÉS -->
        <div
            id="nivel-ingles-info"
            class="nivel-ingles-info"
            style="display: none;">
            <strong>Tu nivel actual de Inglés:</strong>

            <span id="nivel-ingles-actual">
                Se calculará automáticamente
            </span>

            <p>
                Las preguntas se seleccionarán automáticamente según
                tu rendimiento y nivel actual.
            </p>
        </div>

        <div class="practica-field">
            <label for="tema_id">Tema (opcional)</label>

            <select
                id="tema_id"
                name="tema_id"
                disabled>
                <option value="">
                    Primero elige una materia
                </option>
            </select>
        </div>

        <div class="practica-field">
            <span class="practica-field-label">
                ¿Cuántas preguntas?
            </span>

            <div
                class="quantity-selector"
                data-quantity-input="cantidad">
                <button
                    class="quantity-option is-selected"
                    type="button"
                    data-question-count="5">
                    5
                </button>

                <button
                    class="quantity-option"
                    type="button"
                    data-question-count="10">
                    10
                </button>

                <button
                    class="quantity-option"
                    type="button"
                    data-question-count="15">
                    15
                </button>

                <button
                    class="quantity-option"
                    type="button"
                    data-question-count="20">
                    20
                </button>
            </div>

            <input
                type="hidden"
                id="cantidad"
                name="cantidad"
                value="5">
        </div>

        <button
            class="primary-button practica-submit"
            type="submit">
            Empezar práctica
            <span aria-hidden="true">→</span>
        </button>

    </form>

    <aside class="simulacros-aside" aria-label="Tu progreso y recomendaciones">
        <section class="aside-card">
            <p class="eyebrow">TU PROGRESO</p>

            <?php if ((int) $resumen['sesiones'] === 0): ?>
                <p class="aside-empty">Aún no has hecho ninguna práctica. Tu primer resultado aparecerá aquí.</p>
            <?php else: ?>
                <div class="aside-stats">
                    <div><strong><?= (int) $resumen['sesiones'] ?></strong><span>Prácticas</span></div>
                    <div><strong><?= (int) $resumen['preguntas'] ?></strong><span>Preguntas</span></div>
                    <div><strong><?= (int) $resumen['porcentaje'] ?>%</strong><span>Aciertos</span></div>
                </div>
            <?php endif; ?>
        </section>

        <section class="aside-card aside-tips">
            <p class="eyebrow">ANTES DE EMPEZAR</p>
            <ul>
                <li>Elige una materia y, si quieres afinar, un tema específico.</li>
                <li>Empieza con 5 preguntas y sube la cantidad a medida que avances.</li>
                <li>No hay cronómetro: tómate el tiempo para razonar cada respuesta.</li>
                <li>Al terminar verás la retroalimentación de cada pregunta y podrás pedir ayuda a ProgressIA.</li>
            </ul>
        </section>
    </aside>
</div>


<script>
    window.temasPorMateria = <?= json_encode(
                                    $temasPorMateria,
                                    JSON_UNESCAPED_UNICODE
                                ) ?>;

    const materiaSelect = document.getElementById('materia_id');
    const nivelInglesInfo = document.getElementById('nivel-ingles-info');

    materiaSelect.addEventListener('change', function() {
        const materiaSeleccionada =
            this.options[this.selectedIndex].text.trim().toLowerCase();

        if (
            materiaSeleccionada === 'inglés' ||
            materiaSeleccionada === 'ingles'
        ) {
            nivelInglesInfo.style.display = 'block';
        } else {
            nivelInglesInfo.style.display = 'none';
        }
    });
</script>