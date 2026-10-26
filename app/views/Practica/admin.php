<?php
$temasPorMateria = [];
foreach ($temas as $tema) {
    $temasPorMateria[(int) $tema['materia_id']][] = $tema;
}
?>
<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>"><span aria-hidden="true">←</span> Volver al inicio</a>

<section class="practica-intro">
    <p class="eyebrow">GESTIÓN DE PRÁCTICA</p>
    <h1>Administra todo lo relacionado con Práctica.</h1>
    <p class="practica-subtitle">Desde aquí puedes gestionar materias, temas, tipos de pregunta y el banco de preguntas sin salir del apartado Práctica.</p>
</section>

<?php if (isset($mensajes['exito'])): ?>
    <div class="alert alert-success" role="alert"><?= htmlspecialchars($mensajes['exito'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (isset($mensajes['error'])): ?>
    <div class="alert alert-error" role="alert"><?= htmlspecialchars($mensajes['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="admin-workspace">
    <aside class="admin-context-rail" aria-label="Navegación rápida de Práctica">
        <div class="context-rail-head">
            <span class="context-rail-kicker">CENTRO DE CONTROL</span>
            <strong>Práctica</strong>
            <span>Gestiona el contenido sin perder de vista las secciones.</span>
        </div>

        <nav class="context-rail-nav">
            <?php if ($esAdmin): ?>
                <a href="#materias"><span>01</span> Materias y temas</a>
                <a href="#tipos"><span>02</span> Tipos de pregunta</a>
            <?php endif; ?>
            <a href="#preguntas"><span>03</span> Banco de preguntas</a>
        </nav>

        <div class="context-rail-stats">
            <?php if ($esAdmin): ?>
                <div><b><?= count($materias) ?></b><span>Materias</span></div>
                <div><b><?= count($temas) ?></b><span>Temas</span></div>
                <div><b><?= count($tiposPregunta) ?></b><span>Tipos</span></div>
            <?php endif; ?>
            <div><b><?= count($preguntas) ?></b><span><?= $esAdmin ? 'Preguntas' : 'Mis preguntas' ?></span></div>
        </div>

        <div class="context-rail-tip">
            <span aria-hidden="true">✦</span>
            <div><strong>Consejo</strong>
                <p>Mantén los temas claros y específicos para obtener prácticas más precisas.</p>
            </div>
        </div>
    </aside>

    <div class="admin-main-column">
        <section class="admin-links admin-quick-links">
            <?php if ($esAdmin): ?>
                <a class="admin-link-card" href="#materias">
                    <span class="admin-link-title">Materias y temas</span>
                    <span class="admin-link-desc">Crea materias y agrega temas para organizar el banco.</span>
                </a>
                <a class="admin-link-card" href="#tipos">
                    <span class="admin-link-title">Tipos de pregunta</span>
                    <span class="admin-link-desc">Define los formatos que usará el banco de preguntas.</span>
                </a>
            <?php endif; ?>
            <a class="admin-link-card" href="#preguntas">
                <span class="admin-link-title">Preguntas</span>
                <span class="admin-link-desc"><?= $esAdmin ? 'Crea, edita, filtra y elimina preguntas.' : 'Crea, edita y elimina tus propias preguntas.' ?></span>
            </a>
        </section>

        <?php if ($esAdmin): ?>
            <section class="progreso-section" id="materias">
                <div class="admin-toolbar">
                    <h2 class="progreso-section-title" style="margin-bottom:0">Materias y temas</h2>
                </div>
                <div class="admin-form-grid">
                    <form class="practica-form perfil-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin/materia/crear" method="post">
                        <h3 class="perfil-form-title">Nueva materia</h3>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="practica-field"><label for="nombre_materia">Nombre</label><input type="text" id="nombre_materia" name="nombre" required></div>
                        <div class="practica-field"><label for="descripcion_materia">Descripción</label><input type="text" id="descripcion_materia" name="descripcion"></div>
                        <button class="primary-button practica-submit" type="submit">Crear materia</button>
                    </form>

                    <form class="practica-form perfil-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin/tema/crear" method="post">
                        <h3 class="perfil-form-title">Nuevo tema</h3>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="practica-field"><label for="materia_id_tema">Materia</label><select id="materia_id_tema" name="materia_id" required>
                                <option value="">Selecciona una materia</option><?php foreach ($materias as $materia): ?><option value="<?= (int) $materia['id'] ?>"><?= htmlspecialchars($materia['nombre'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?>
                            </select></div>
                        <div class="practica-field"><label for="nombre_tema">Nombre del tema</label><input type="text" id="nombre_tema" name="nombre" required></div>
                        <button class="primary-button practica-submit" type="submit">Crear tema</button>
                    </form>
                </div>

                <div class="admin-materias-list">
                    <?php foreach ($materias as $materia): ?>
                        <div class="admin-materia-block">
                            <h3><?= htmlspecialchars($materia['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <?php if (!empty($materia['descripcion'])): ?><p><?= htmlspecialchars($materia['descripcion'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                            <ul>
                                <?php foreach (($temasPorMateria[(int) $materia['id']] ?? []) as $tema): ?>
                                    <li><?= htmlspecialchars($tema['nombre'], ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach; ?>
                                <?php if (empty($temasPorMateria[(int) $materia['id']] ?? [])): ?><li class="admin-table-muted">Sin temas todavía</li><?php endif; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="progreso-section" id="tipos">
                <div class="admin-toolbar">
                    <h2 class="progreso-section-title" style="margin-bottom:0">Tipos de pregunta</h2>
                </div>
                <form class="practica-form perfil-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin/tipo/crear" method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <div class="practica-field"><label for="tipo_nombre">Nombre</label><input type="text" id="tipo_nombre" name="nombre" placeholder="Ej. Selección múltiple con única respuesta" required></div>
                    <div class="practica-field"><label for="tipo_descripcion">Descripción</label><input type="text" id="tipo_descripcion" name="descripcion" placeholder="Descripción opcional"></div>
                    <button class="primary-button practica-submit" type="submit">Crear tipo</button>
                </form>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tiposPregunta as $tipo): ?>
                                <tr>
                                    <td><?= htmlspecialchars($tipo['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) ($tipo['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="admin-table-actions">
                                        <form class="admin-inline-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin/tipo/eliminar" method="post" onsubmit="return confirm('¿Eliminar este tipo de pregunta?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="tipo_pregunta_id" value="<?= (int) $tipo['id'] ?>">
                                            <button class="admin-delete-button" type="submit">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($tiposPregunta === []): ?><tr>
                                    <td colspan="3">No hay tipos de pregunta creados.</td>
                                </tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        <section class="progreso-section" id="preguntas">
            <div class="admin-toolbar">
                <h2 class="progreso-section-title" style="margin-bottom:0"><?= $esAdmin ? 'Banco de preguntas' : 'Mis preguntas' ?></h2>
                <a class="primary-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin/pregunta">Nueva pregunta <span aria-hidden="true">→</span></a>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Enunciado</th>
                            <th>Materia</th>
                            <th>Tema</th>
                            <th>Tipo</th>
                            <th>Dificultad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($preguntas as $pregunta): ?>
                            <tr>
                                <td class="admin-table-enunciado"><a class="admin-table-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin/pregunta?id=<?= (int) $pregunta['id'] ?>"><?= htmlspecialchars($pregunta['enunciado'], ENT_QUOTES, 'UTF-8') ?></a></td>
                                <td><?= htmlspecialchars($pregunta['materia_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($pregunta['tema_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($pregunta['tipo_pregunta_nombre'] ?? 'Sin tipo'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars(ucfirst($pregunta['dificultad']), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="admin-table-actions">
                                    <button type="button" class="secondary-button admin-small-button" onclick="this.closest('tr').nextElementSibling.classList.toggle('is-open')">Ver</button>
                                    <a class="secondary-button admin-small-button" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin/pregunta?id=<?= (int) $pregunta['id'] ?>">Editar</a>
                                    <form class="admin-inline-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>practica/admin/pregunta/eliminar" method="post" onsubmit="return confirm('¿Eliminar esta pregunta?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="pregunta_id" value="<?= (int) $pregunta['id'] ?>">
                                        <button class="admin-delete-button" type="submit">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="admin-pregunta-preview-row">
                                <td colspan="6">
                                    <div class="pregunta-preview">
                                        <p class="pregunta-preview-enunciado"><?= htmlspecialchars($pregunta['enunciado'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <ul class="pregunta-preview-opciones">
                                            <?php foreach (['a', 'b', 'c', 'd'] as $letra): ?>
                                                <?php $esCorrecta = strtolower((string) $pregunta['respuesta_correcta']) === $letra; ?>
                                                <li class="<?= $esCorrecta ? 'is-correcta' : '' ?>">
                                                    <span class="pregunta-preview-letra"><?= strtoupper($letra) ?></span>
                                                    <span><?= htmlspecialchars((string) $pregunta['opcion_' . $letra], ENT_QUOTES, 'UTF-8') ?></span>
                                                    <?php if ($esCorrecta): ?><span class="pregunta-preview-check">✓ Correcta</span><?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <?php if (trim((string) ($pregunta['explicacion'] ?? '')) !== ''): ?>
                                            <p class="pregunta-preview-explicacion"><strong>Explicación:</strong> <?= htmlspecialchars($pregunta['explicacion'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($preguntas === []): ?><tr>
                                <td colspan="6">No hay preguntas en el banco.</td>
                            </tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>