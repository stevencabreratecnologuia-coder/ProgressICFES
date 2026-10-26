<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/materias"><span aria-hidden="true">←</span> Volver a materias</a>

<section class="practica-intro">
    <p class="eyebrow">MATERIA</p>
    <h1><?= htmlspecialchars($materia['nombre'], ENT_QUOTES, 'UTF-8') ?></h1>
    <?php if (!empty($materia['descripcion'])): ?>
        <p class="practica-subtitle"><?= htmlspecialchars($materia['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</section>

<?php if (isset($mensajes['exito'])): ?>
    <div class="alert alert-success" role="alert"><?= htmlspecialchars($mensajes['exito'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (isset($mensajes['error'])): ?>
    <div class="alert alert-error" role="alert"><?= htmlspecialchars($mensajes['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<section class="progreso-section">
    <h2 class="progreso-section-title">Temas de esta materia</h2>

    <form class="practica-form admin-pregunta-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/materias/tema" method="post" style="margin-bottom: 20px;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="materia_id" value="<?= (int) $materia['id'] ?>">
        <div class="practica-field">
            <label for="nombre_tema">Agregar tema nuevo</label>
            <input type="text" id="nombre_tema" name="nombre" placeholder="Nombre del tema" required>
        </div>
        <button class="primary-button practica-submit" type="submit">Agregar tema</button>
    </form>

    <?php if ($temas === []): ?>
        <p class="practica-subtitle">Esta materia todavía no tiene temas.</p>
    <?php else: ?>
        <div class="admin-materias-list">
            <?php foreach ($temas as $tema): ?>
                <div class="admin-materia-block">
                    <h3><?= htmlspecialchars($tema['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="progreso-section">
    <div class="admin-toolbar">
        <h2 class="progreso-section-title" style="margin-bottom: 0;">Preguntas de esta materia</h2>
        <a class="primary-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/preguntas/formulario">Nueva pregunta <span aria-hidden="true">→</span></a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Enunciado</th>
                    <th>Tema</th>
                    <th>Dificultad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($preguntas as $pregunta): ?>
                    <tr>
                        <td class="admin-table-enunciado"><?= htmlspecialchars($pregunta['enunciado'], ENT_QUOTES, 'UTF-8') ?></td>
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
                        <td colspan="4">Esta materia todavía no tiene preguntas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>