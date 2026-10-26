<?php
$esEdicion = $pregunta !== null;

$nombresMaterias = [];
foreach ($materias as $materia) {
    $nombresMaterias[(int) $materia['id']] = $materia['nombre'];
}
?>
<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin"><span aria-hidden="true">←</span> Volver a gestión de práctica</a>

<section class="practica-intro">
    <p class="eyebrow"><?= $esEdicion ? 'EDITAR PREGUNTA' : 'NUEVA PREGUNTA' ?></p>
    <h1><?= $esEdicion ? 'Edita esta pregunta.' : 'Agrega una pregunta nueva.' ?></h1>
</section>

<form class="practica-form admin-pregunta-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin/pregunta/guardar" method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($esEdicion): ?>
        <input type="hidden" name="id" value="<?= (int) $pregunta['id'] ?>">
    <?php endif; ?>

    <div class="practica-field">
        <label for="tema_id">Tema</label>
        <?php if ($temas === []): ?>
            <p class="admin-table-muted">
                <?= $esAdmin ? 'Todavía no hay temas creados.' : 'Todavía no hay temas creados para tu materia. Pídele a un administrador que agregue alguno.' ?>
            </p>
        <?php endif; ?>
        <select id="tema_id" name="tema_id" required>
            <option value="">Selecciona un tema</option>
            <?php foreach ($temas as $tema): ?>
                <?php $nombreMateria = $nombresMaterias[(int) $tema['materia_id']] ?? ''; ?>
                <option value="<?= (int) $tema['id'] ?>" <?= $esEdicion && (int) $pregunta['tema_id'] === (int) $tema['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars(($esAdmin && $nombreMateria !== '' ? $nombreMateria . ' — ' : '') . $tema['nombre'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="practica-field">
        <label for="enunciado">Enunciado</label>
        <textarea id="enunciado" name="enunciado" rows="3" required><?= $esEdicion ? htmlspecialchars((string) $pregunta['enunciado'], ENT_QUOTES, 'UTF-8') : '' ?></textarea>
    </div>

    <div class="admin-opciones-grid">
        <?php foreach (['a', 'b', 'c', 'd'] as $letra): ?>
            <div class="practica-field">
                <label for="opcion_<?= $letra ?>">Opción <?= strtoupper($letra) ?></label>
                <input
                    type="text"
                    id="opcion_<?= $letra ?>"
                    name="opcion_<?= $letra ?>"
                    value="<?= $esEdicion ? htmlspecialchars((string) $pregunta['opcion_' . $letra], ENT_QUOTES, 'UTF-8') : '' ?>"
                    required>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="practica-field">
        <label for="respuesta_correcta">Respuesta correcta</label>
        <select id="respuesta_correcta" name="respuesta_correcta" required>
            <?php foreach (['a', 'b', 'c', 'd'] as $letra): ?>
                <option value="<?= $letra ?>" <?= $esEdicion && strtolower((string) $pregunta['respuesta_correcta']) === $letra ? 'selected' : '' ?>>
                    Opción <?= strtoupper($letra) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="practica-field">
        <label for="dificultad">Dificultad</label>
        <select id="dificultad" name="dificultad" required>
            <?php foreach ($dificultades as $nivel): ?>
                <option value="<?= $nivel ?>" <?= $esEdicion && $pregunta['dificultad'] === $nivel ? 'selected' : '' ?>>
                    <?= ucfirst($nivel) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="practica-field">
        <label for="explicacion">Explicación</label>
        <textarea id="explicacion" name="explicacion" rows="2"><?= $esEdicion ? htmlspecialchars((string) $pregunta['explicacion'], ENT_QUOTES, 'UTF-8') : '' ?></textarea>
    </div>

    <button class="primary-button practica-submit" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Crear pregunta' ?></button>
</form>