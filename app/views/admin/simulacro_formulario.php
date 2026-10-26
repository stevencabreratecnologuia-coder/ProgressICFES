<?php
$esEdicion = $simulacro !== null;

$nombreMateriaFiltro = '';
foreach ($materias as $materia) {
    if ((int) $materia['id'] === $materiaFiltro) {
        $nombreMateriaFiltro = $materia['nombre'];
        break;
    }
}
?>
<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>simulacros"><span aria-hidden="true">←</span> Volver a simulacros</a>

<section class="practica-intro">
    <p class="eyebrow"><?= $esEdicion ? 'EDITAR SIMULACRO' : 'NUEVO SIMULACRO' ?></p>
    <h1><?= $esEdicion ? 'Edita este simulacro.' : 'Crea un simulacro nuevo.' ?></h1>
    <p class="practica-subtitle">Primero elige la materia para ver sus preguntas disponibles, después completa los datos y marca las que quieres incluir.</p>
</section>

<?php if ($esAdmin): ?>
    <form class="practica-form admin-pregunta-form admin-filtro-materia-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>simulacros/formulario" method="get">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= (int) $simulacro['id'] ?>">
        <?php endif; ?>
        <div class="practica-field">
            <label for="materia_filtro">Materia (para ver sus preguntas)</label>
            <select id="materia_filtro" name="materia_id" onchange="this.form.submit()">
                <?php foreach ($materias as $materia): ?>
                    <option value="<?= (int) $materia['id'] ?>" <?= $materiaFiltro === (int) $materia['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($materia['nombre'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
<?php endif; ?>

<form class="practica-form admin-pregunta-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>simulacros/guardar" method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($esEdicion): ?>
        <input type="hidden" name="id" value="<?= (int) $simulacro['id'] ?>">
    <?php endif; ?>

    <div class="practica-field">
        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" value="<?= $esEdicion ? htmlspecialchars($simulacro['titulo'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
    </div>

    <div class="practica-field">
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            <option value="por_materia" <?= $esEdicion && $simulacro['tipo'] === 'por_materia' ? 'selected' : '' ?>>Por materia</option>
            <option value="completo" <?= $esEdicion && $simulacro['tipo'] === 'completo' ? 'selected' : '' ?>>Completo</option>
        </select>
    </div>

    <?php if ($esAdmin): ?>
        <div class="practica-field">
            <label for="materia_id">Materia asociada</label>
            <select id="materia_id" name="materia_id">
                <?php foreach ($materias as $materia): ?>
                    <?php
                    $marcada = $esEdicion
                        ? (int) ($simulacro['materia_id'] ?? 0) === (int) $materia['id']
                        : $materiaFiltro === (int) $materia['id'];
                    ?>
                    <option value="<?= (int) $materia['id'] ?>" <?= $marcada ? 'selected' : '' ?>>
                        <?= htmlspecialchars($materia['nombre'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php else: ?>
        <p class="practica-subtitle">Materia: <?= htmlspecialchars($nombreMateriaFiltro !== '' ? $nombreMateriaFiltro : 'sin asignar', ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <div class="practica-field">
        <label for="tiempo_limite_min">Tiempo límite (minutos)</label>
        <input type="number" id="tiempo_limite_min" name="tiempo_limite_min" min="1" value="<?= $esEdicion ? (int) $simulacro['tiempo_limite_min'] : 15 ?>" required>
    </div>

    <div class="practica-field">
        <span class="practica-field-label">Preguntas de <?= htmlspecialchars($nombreMateriaFiltro, ENT_QUOTES, 'UTF-8') ?></span>
        <div class="admin-preguntas-checklist">
            <?php foreach ($preguntasDisponibles as $pregunta): ?>
                <label class="admin-checklist-item">
                    <input type="checkbox" name="preguntas[]" value="<?= (int) $pregunta['id'] ?>" <?= in_array((int) $pregunta['id'], $preguntaIdsSeleccionadas, true) ? 'checked' : '' ?>>
                    <span><?= htmlspecialchars($pregunta['enunciado'], ENT_QUOTES, 'UTF-8') ?></span>
                </label>
            <?php endforeach; ?>
            <?php if ($preguntasDisponibles === []): ?>
                <p class="admin-table-muted">No hay preguntas para esta materia todavía.</p>
            <?php endif; ?>
        </div>
        <p class="admin-checklist-nota">
            Ojo: al cambiar de materia arriba, la página se recarga y solo se guardan las preguntas marcadas de la materia que estés viendo en ese momento.
        </p>
    </div>

    <button class="primary-button practica-submit" type="submit"><?= $esEdicion ? 'Guardar cambios' : 'Crear simulacro' ?></button>
</form>