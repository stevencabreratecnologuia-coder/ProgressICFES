<?php $esEdicion = $simulacro !== null; ?>
<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>profesor/simulacros"><span aria-hidden="true">←</span> Volver a mis simulacros</a>

<section class="practica-intro">
    <p class="eyebrow"><?= $esEdicion ? 'EDITAR SIMULACRO' : 'NUEVO SIMULACRO' ?></p>
    <h1><?= $esEdicion ? 'Edita este simulacro.' : 'Crea un simulacro nuevo.' ?></h1>
    <p class="practica-subtitle">Materia: <?= htmlspecialchars((string) ($materia['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
</section>

<form class="practica-form admin-pregunta-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>profesor/simulacro/guardar" method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($esEdicion): ?><input type="hidden" name="id" value="<?= (int) $simulacro['id'] ?>"><?php endif; ?>

    <div class="practica-field">
        <label for="titulo">Título</label>
        <input id="titulo" name="titulo" type="text" value="<?= $esEdicion ? htmlspecialchars((string) $simulacro['titulo'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
    </div>

    <div class="practica-field">
        <label for="tiempo_limite_min">Tiempo límite (minutos)</label>
        <input id="tiempo_limite_min" name="tiempo_limite_min" type="number" min="1" value="<?= $esEdicion ? (int) $simulacro['tiempo_limite_min'] : 15 ?>" required>
    </div>

    <div class="practica-field">
        <span class="practica-field-label">Preguntas de la materia</span>
        <div class="admin-preguntas-checklist">
            <?php foreach ($preguntas as $pregunta): ?>
                <label class="admin-checklist-item">
                    <input type="checkbox" name="preguntas[]" value="<?= (int) $pregunta['id'] ?>" <?= in_array((int) $pregunta['id'], $preguntaIds, true) ? 'checked' : '' ?>>
                    <span><?= htmlspecialchars((string) $pregunta['enunciado'], ENT_QUOTES, 'UTF-8') ?></span>
                </label>
            <?php endforeach; ?>
            <?php if ($preguntas === []): ?><p class="admin-table-muted">No hay preguntas disponibles.</p><?php endif; ?>
        </div>
    </div>

    <button class="primary-button practica-submit" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Crear simulacro' ?></button>
</form>