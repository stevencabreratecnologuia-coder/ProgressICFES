<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>"><span aria-hidden="true">←</span> Volver al inicio</a>

<section class="practica-intro">

    <p class="eyebrow">USUARIOS</p>

    <h1>
        Estudiantes, profesores y solicitudes registradas.
    </h1>

</section>


<?php if (isset($mensajes['exito'])): ?>

    <div class="alert alert-success" role="alert">

        <?= htmlspecialchars(
            $mensajes['exito'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>

    </div>

<?php endif; ?>


<?php if (isset($mensajes['error'])): ?>

    <div class="alert alert-error" role="alert">

        <?= htmlspecialchars(
            $mensajes['error'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>

    </div>

<?php endif; ?>


<?php if ($solicitudesPendientes !== []): ?>

    <section class="progreso-section">

        <h2 class="progreso-section-title">

            Solicitudes de profesores pendientes
            (<?= count($solicitudesPendientes) ?>)

        </h2>

        <p class="practica-subtitle">

            Revisa las solicitudes antes de permitir que un profesor
            acceda al sistema.

        </p>


        <div class="admin-table-wrap">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>Profesor</th>
                        <th>Correo</th>
                        <th>Documento</th>
                        <th>Materia solicitada</th>
                        <th>Solicitud</th>
                        <th>Decisión</th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($solicitudesPendientes as $solicitud): ?>

                        <tr>

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $solicitud['nombre']
                                            . ' '
                                            . $solicitud['apellido'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </strong>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $solicitud['correo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $solicitud['documento_identidad'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $solicitud['materia_nombre']
                                        ?? 'Sin materia',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    date(
                                        'd/m/Y',
                                        strtotime(
                                            (string)
                                            $solicitud['fecha_registro']
                                        )
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td class="admin-table-actions">

                                <form
                                    class="admin-inline-form"
                                    action="<?= htmlspecialchars(
                                                $baseUrl,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>usuarios/profesor/aprobar"
                                    method="post">

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars(
                                                    $csrfToken,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>">

                                    <input
                                        type="hidden"
                                        name="usuario_id"
                                        value="<?= (int) $solicitud['id'] ?>">

                                    <button
                                        class="primary-button admin-small-button"
                                        type="submit">
                                        Aprobar
                                    </button>

                                </form>


                                <form
                                    class="admin-inline-form"
                                    action="<?= htmlspecialchars(
                                                $baseUrl,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>usuarios/profesor/rechazar"
                                    method="post"
                                    onsubmit="return confirm('¿Seguro que deseas denegar esta solicitud?');">

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars(
                                                    $csrfToken,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>">

                                    <input
                                        type="hidden"
                                        name="usuario_id"
                                        value="<?= (int) $solicitud['id'] ?>">

                                    <button
                                        class="admin-delete-button"
                                        type="submit">
                                        Denegar
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

<?php endif; ?>


<section class="progreso-section">

    <h2 class="progreso-section-title">
        Todos los usuarios
    </h2>


    <div class="admin-table-wrap">

        <table class="admin-table">

            <thead>

                <tr>

                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Materia</th>
                    <th>Registrado</th>
                    <th>Acciones</th>

                </tr>

            </thead>


            <tbody>

                <?php foreach ($usuarios as $usuario): ?>

                    <tr>

                        <td>

                            <a
                                class="admin-table-link"
                                href="<?= htmlspecialchars(
                                            $baseUrl,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>usuarios/detalle?id=<?= (int) $usuario['id'] ?>">

                                <?= htmlspecialchars(
                                    $usuario['nombre']
                                        . ' '
                                        . $usuario['apellido'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </a>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $usuario['correo'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <form
                                class="admin-inline-form"
                                action="<?= htmlspecialchars(
                                            $baseUrl,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>usuarios/rol"
                                method="post">

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars(
                                                $csrfToken,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>">

                                <input
                                    type="hidden"
                                    name="usuario_id"
                                    value="<?= (int) $usuario['id'] ?>">

                                <select
                                    name="rol"
                                    class="admin-role-select"
                                    onchange="this.form.submit()"
                                    <?= (int) $usuario['id'] === (int) $usuarioActualId
                                        ? 'disabled'
                                        : '' ?>>

                                    <option
                                        value="estudiante"
                                        <?= $usuario['rol'] === 'estudiante'
                                            ? 'selected'
                                            : '' ?>>
                                        Estudiante
                                    </option>

                                    <option
                                        value="profesor"
                                        <?= $usuario['rol'] === 'profesor'
                                            ? 'selected'
                                            : '' ?>>
                                        Profesor
                                    </option>

                                    <option
                                        value="admin"
                                        <?= $usuario['rol'] === 'admin'
                                            ? 'selected'
                                            : '' ?>>
                                        Admin
                                    </option>

                                </select>

                            </form>

                        </td>


                        <td>

                            <?php if ($usuario['estado'] === 'activo'): ?>

                                <span>Activo</span>

                            <?php elseif ($usuario['estado'] === 'pendiente'): ?>

                                <span>Pendiente</span>

                            <?php elseif ($usuario['estado'] === 'rechazado'): ?>

                                <span>Rechazado</span>

                            <?php else: ?>

                                <span>

                                    <?= htmlspecialchars(
                                        (string) $usuario['estado'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php if (
                                $usuario['rol'] === 'profesor'
                            ): ?>

                                <?php if (
                                    $usuario['estado'] === 'pendiente'
                                ): ?>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $usuario['materia_nombre']
                                                ?? 'Sin materia',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </strong>

                                <?php else: ?>

                                    <form
                                        class="admin-inline-form"
                                        action="<?= htmlspecialchars(
                                                    $baseUrl,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>usuarios/materia"
                                        method="post">

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars(
                                                        $csrfToken,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                        <input
                                            type="hidden"
                                            name="usuario_id"
                                            value="<?= (int) $usuario['id'] ?>">

                                        <select
                                            name="materia_id"
                                            class="admin-role-select"
                                            onchange="this.form.submit()">

                                            <option value="">
                                                Sin materia
                                            </option>

                                            <?php foreach ($materias as $materia): ?>

                                                <option
                                                    value="<?= (int) $materia['id'] ?>"
                                                    <?= (int) (
                                                        $usuario['materia_id']
                                                        ?? 0
                                                    ) === (int) $materia['id']
                                                        ? 'selected'
                                                        : '' ?>>

                                                    <?= htmlspecialchars(
                                                        $materia['nombre'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                    </form>

                                <?php endif; ?>

                            <?php else: ?>

                                <span class="admin-table-muted">
                                    —
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                date(
                                    'd/m/Y',
                                    strtotime(
                                        (string)
                                        $usuario['fecha_registro']
                                    )
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td class="admin-table-actions">

                            <a
                                class="secondary-button admin-small-button"
                                href="<?= htmlspecialchars(
                                            $baseUrl,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>usuarios/detalle?id=<?= (int) $usuario['id'] ?>">
                                Ver
                            </a>


                            <?php if (
                                (int) $usuario['id']
                                !== (int) $usuarioActualId
                            ): ?>

                                <form
                                    class="admin-inline-form"
                                    action="<?= htmlspecialchars(
                                                $baseUrl,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>usuarios/eliminar"
                                    method="post"
                                    onsubmit="return confirm('¿Eliminar a este usuario? Esta acción no se puede deshacer.');">

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars(
                                                    $csrfToken,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>">

                                    <input
                                        type="hidden"
                                        name="usuario_id"
                                        value="<?= (int) $usuario['id'] ?>">

                                    <button
                                        class="admin-delete-button"
                                        type="submit">
                                        Eliminar
                                    </button>

                                </form>

                            <?php else: ?>

                                <span class="admin-table-muted">
                                    Tu cuenta
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>


                <?php if ($usuarios === []): ?>

                    <tr>

                        <td colspan="7">
                            No hay usuarios registrados.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>