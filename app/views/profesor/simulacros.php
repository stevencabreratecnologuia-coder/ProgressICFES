<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>"><span aria-hidden="true">←</span> Volver al inicio</a>

<section class="practica-intro">
    <p class="eyebrow">SIMULACROS DEL PROFESOR</p>
    <h1>Mis simulacros</h1>
    <p class="practica-subtitle">Gestiona los simulacros de tu materia o genera uno con ProgressIA.</p>
</section>

<?php if (isset($mensajes['exito'])): ?>
    <div class="alert alert-success" role="alert"><?= htmlspecialchars($mensajes['exito'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (isset($mensajes['error'])): ?>
    <div class="alert alert-error" role="alert"><?= htmlspecialchars($mensajes['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="admin-toolbar">
    <a class="primary-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>profesor/simulacro">Nuevo simulacro manual <span aria-hidden="true">→</span></a>
    <a class="secondary-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>chat">Generar con ProgressIA</a>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Materia</th>
                <th>Preguntas</th>
                <th>Tiempo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($simulacros as $simulacro): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $simulacro['titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($simulacro['materia_nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $simulacro['num_preguntas'] ?></td>
                    <td><?= (int) $simulacro['tiempo_limite_min'] ?> min</td>
                    <td class="admin-table-actions">
                        <a class="secondary-button admin-small-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>profesor/simulacro?id=<?= (int) $simulacro['id'] ?>">Editar</a>
                        <form class="admin-inline-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>profesor/simulacro/eliminar" method="post" onsubmit="return confirm('¿Eliminar este simulacro?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="simulacro_id" value="<?= (int) $simulacro['id'] ?>">
                            <button class="admin-delete-button" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($simulacros === []): ?>
                <tr>
                    <td colspan="5">No hay simulacros creados todavía.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>