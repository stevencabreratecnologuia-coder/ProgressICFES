<?php
$fechaRegistro = !empty($usuario['fecha_registro'])
    ? date('d/m/Y', strtotime((string) $usuario['fecha_registro']))
    : null;
$rolLegible = [
    'estudiante' => 'Estudiante',
    'docente' => 'Docente',
    'admin' => 'Administrador',
][$usuario['rol'] ?? ''] ?? ucfirst((string) ($usuario['rol'] ?? ''));
?>
<section class="practica-intro">
    <p class="eyebrow">MI PERFIL</p>
    <h1>Tus datos personales.</h1>
    <p class="practica-subtitle">Actualiza tu información o cambia tu contraseña cuando quieras.</p>
</section>

<?php if (isset($mensajes['exito'])): ?>
    <div class="alert alert-success" role="alert"><?= htmlspecialchars($mensajes['exito'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (isset($mensajes['error'])): ?>
    <div class="alert alert-error" role="alert"><?= htmlspecialchars($mensajes['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="perfil-grid">
    <form class="practica-form perfil-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>perfil/datos" method="post">
        <h2 class="perfil-form-title">Datos personales</h2>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div class="practica-field">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars((string) $usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="practica-field">
            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars((string) $usuario['apellido'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="practica-field">
            <label>Correo</label>
            <input type="email" value="<?= htmlspecialchars((string) $usuario['correo'], ENT_QUOTES, 'UTF-8') ?>" disabled>
        </div>

        <div class="perfil-meta">
            <span><strong>Rol:</strong> <?= htmlspecialchars($rolLegible, ENT_QUOTES, 'UTF-8') ?></span>
            <?php if ($fechaRegistro !== null): ?>
                <span><strong>Miembro desde:</strong> <?= htmlspecialchars($fechaRegistro, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
        </div>

        <button class="primary-button practica-submit" type="submit">Guardar cambios</button>
    </form>

    <form class="practica-form perfil-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>perfil/password" method="post">
        <h2 class="perfil-form-title">Cambiar contraseña</h2>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div class="practica-field">
            <label for="password_actual">Contraseña actual</label>
            <input type="password" id="password_actual" name="password_actual" required>
        </div>

        <div class="practica-field">
            <label for="password_nueva">Contraseña nueva</label>
            <input type="password" id="password_nueva" name="password_nueva" minlength="8" required>
        </div>

        <div class="practica-field">
            <label for="password_confirmacion">Confirmar contraseña nueva</label>
            <input type="password" id="password_confirmacion" name="password_confirmacion" minlength="8" required>
        </div>

        <button class="primary-button practica-submit" type="submit">Actualizar contraseña</button>
    </form>
</div>