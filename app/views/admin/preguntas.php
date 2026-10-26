<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>"><span aria-hidden="true">←</span> Volver al inicio</a>

<section class="practica-intro">
    <p class="eyebrow">BANCO DE PREGUNTAS</p>
    <h1>Preguntas registradas.</h1>
</section>

<?php if (isset($mensajes['exito'])): ?>
    <div class="alert alert-success" role="alert"><?= htmlspecialchars($mensajes['exito'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (isset($mensajes['error'])): ?>
    <div class="alert alert-error" role="alert"><?= htmlspecialchars($mensajes['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="admin-toolbar">
    <form class="admin-filter-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/preguntas" method="get">
        <select name="materia_id" onchange="this.form.submit()">
            <option value="">Todas las materias</option>
            <?php foreach ($materias as $materia): ?>
                <option value="<?= (int) $materia['id'] ?>" <?= $materiaSeleccionada === (int) $materia['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($materia['nombre'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    <a class="primary-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/preguntas/formulario">Nueva pregunta <span aria-hidden="true">→</span></a>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Enunciado</th>
                <th>Materia</th>
                <th>Tema</th>
                <th>Dificultad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($preguntas as $pregunta): ?>
                <tr>
                    <td class="admin-table-enunciado">
                        <a class="admin-table-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/preguntas/formulario?id=<?= (int) $pregunta['id'] ?>">
                            <?= htmlspecialchars($pregunta['enunciado'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars((string) $pregunta['materia_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $pregunta['tema_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(ucfirst((string) $pregunta['dificultad']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="admin-table-actions">
                        <a class="secondary-button admin-small-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/preguntas/formulario?id=<?= (int) $pregunta['id'] ?>">Editar</a>
                        <form class="admin-inline-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/preguntas/eliminar" method="post" onsubmit="return confirm('¿Eliminar esta pregunta?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="pregunta_id" value="<?= (int) $pregunta['id'] ?>">
                            <button class="admin-delete-button" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($preguntas === []): ?>
                <tr>
                    <td colspan="5">No hay preguntas para mostrar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>