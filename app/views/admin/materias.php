<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>"><span aria-hidden="true">←</span> Volver al inicio</a>

<section class="practica-intro">
    <p class="eyebrow">MATERIAS Y TEMAS</p>
    <h1>Organiza el contenido del banco de preguntas.</h1>
</section>

<?php if (isset($mensajes['exito'])): ?>
    <div class="alert alert-success" role="alert"><?= htmlspecialchars($mensajes['exito'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (isset($mensajes['error'])): ?>
    <div class="alert alert-error" role="alert"><?= htmlspecialchars($mensajes['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="perfil-grid">
    <form class="practica-form perfil-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/materias/crear" method="post">
        <h2 class="perfil-form-title">Nueva materia</h2>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div class="practica-field">
            <label for="nombre_materia">Nombre</label>
            <input type="text" id="nombre_materia" name="nombre" required>
        </div>
        <div class="practica-field">
            <label for="descripcion_materia">Descripción</label>
            <input type="text" id="descripcion_materia" name="descripcion">
        </div>
        <button class="primary-button practica-submit" type="submit">Crear materia</button>
    </form>

    <form class="practica-form perfil-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/materias/tema" method="post">
        <h2 class="perfil-form-title">Nuevo tema</h2>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div class="practica-field">
            <label for="materia_id_tema">Materia</label>
            <select id="materia_id_tema" name="materia_id" required>
                <option value="">Selecciona una materia</option>
                <?php foreach ($materias as $materia): ?>
                    <option value="<?= (int) $materia['id'] ?>"><?= htmlspecialchars($materia['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="practica-field">
            <label for="nombre_tema">Nombre del tema</label>
            <input type="text" id="nombre_tema" name="nombre" required>
        </div>
        <button class="primary-button practica-submit" type="submit">Crear tema</button>
    </form>
</div>

<h2 class="progreso-section-title admin-materias-titulo">Materias y sus temas</h2>

<div class="admin-materias-list">
    <?php foreach ($materias as $materia): ?>
        <a class="admin-materia-block admin-materia-block-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>admin/materias/detalle?id=<?= (int) $materia['id'] ?>">
            <h3><?= htmlspecialchars($materia['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
            <ul>
                <?php
                $temasDeEstaMateria = array_filter($temas, static fn(array $tema): bool => (int) $tema['materia_id'] === (int) $materia['id']);
                ?>
                <?php foreach ($temasDeEstaMateria as $tema): ?>
                    <li><?= htmlspecialchars($tema['nombre'], ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
                <?php if ($temasDeEstaMateria === []): ?>
                    <li class="admin-table-muted">Sin temas todavía</li>
                <?php endif; ?>
            </ul>
            <span class="admin-materia-block-cta">Ver y editar →</span>
        </a>
    <?php endforeach; ?>
</div>