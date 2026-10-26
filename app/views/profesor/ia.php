<a class="back-link" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>"><span aria-hidden="true">←</span> Volver al inicio</a>

<section class="practica-intro">
    <p class="eyebrow">HERRAMIENTAS DE PROGRESSIA</p>
    <h1>Usa ProgressIA como asistente docente para crear práctica y simulacros.</h1>
    <p class="practica-subtitle">
        ProgressIA usa la materia y el rendimiento real de tus estudiantes para preparar contenido.
        Todo comienza como BORRADOR: tú revisas, modificas y decides qué publicar.
    </p>
</section>

<?php if ($error !== ''): ?>
    <div class="alert alert-error" role="alert">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($exito !== ''): ?>
    <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($materia === null || $contextoIA === null): ?>
    <section class="progreso-section">
        <h2 class="progreso-section-title">Materia no disponible</h2>
        <p class="practica-subtitle">Necesitas tener una materia asignada por un administrador antes de usar la generación docente.</p>
    </section>
<?php else: ?>
    <section class="progreso-section">
        <h2 class="progreso-section-title">Generador</h2>

        <form class="practica-form admin-pregunta-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>profesor/ia/generar" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="practica-field">
                <label for="modo">Qué quieres generar</label>
                <select id="modo" name="modo" required>
                    <option value="practica">Práctica: preguntas para el banco</option>
                    <option value="simulacro" selected>Simulacro: preguntas + simulacro completo</option>
                    <option value="autonomo">Simulacro autónomo según rendimiento</option>
                </select>
            </div>

            <div class="form-grid">
                <div class="practica-field">
                    <label for="grado">Grado</label>
                    <select id="grado" name="grado">
                        <option value="0">Todos / automático</option>
                        <?php for ($grado = 6; $grado <= 11; $grado++): ?>
                            <option value="<?= $grado ?>"><?= $grado ?>°</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="practica-field">
                    <label for="tema_id">Tema</label>
                    <select id="tema_id" name="tema_id">
                        <option value="0">Automático según rendimiento</option>
                        <?php foreach ($contextoIA['temas_autorizados'] as $tema): ?>
                            <option value="<?= (int) $tema['id'] ?>">
                                <?= htmlspecialchars($tema['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="practica-field">
                    <label for="dificultad">Dificultad</label>
                    <select id="dificultad" name="dificultad">
                        <option value="auto">Automática</option>
                        <option value="facil">Fácil</option>
                        <option value="media">Media</option>
                        <option value="dificil">Difícil</option>
                    </select>
                </div>

                <div class="practica-field">
                    <label for="cantidad">Cantidad de preguntas</label>
                    <input id="cantidad" name="cantidad" type="number" min="1" max="20" value="10" required>
                </div>
            </div>

            <div class="practica-field">
                <label for="tipo_simulacro">Tipo de simulacro</label>
                <select id="tipo_simulacro" name="tipo_simulacro">
                    <option value="diagnostico">Diagnóstico</option>
                    <option value="refuerzo" selected>Refuerzo</option>
                    <option value="icfes">Preparación ICFES</option>
                    <option value="recuperacion">Recuperación</option>
                    <option value="avanzado">Avanzado</option>
                </select>
            </div>

            <div class="practica-field">
                <label for="instruccion">Instrucción adicional</label>
                <textarea id="instruccion" name="instruccion" rows="4" placeholder="Ej. Prioriza interpretación de gráficas y situaciones cotidianas."></textarea>
            </div>

            <button class="primary-button practica-submit" type="submit">
                Generar borrador con ProgressIA
            </button>
        </form>
    </section>

    <?php if (is_array($borrador)): ?>
        <section class="progreso-section">
            <div class="admin-toolbar">
                <div>
                    <p class="eyebrow">BORRADOR</p>
                    <h2 class="progreso-section-title" style="margin-bottom:0">Revisión humana antes de publicar</h2>
                </div>
            </div>

            <form class="practica-form admin-pregunta-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>profesor/ia/publicar" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                <?php if (($borrador['_meta']['modo'] ?? '') !== 'practica'): ?>
                    <div class="practica-field">
                        <label for="titulo">Título del simulacro</label>
                        <input id="titulo" name="titulo" type="text" value="<?= htmlspecialchars((string) ($borrador['titulo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="practica-field">
                        <label for="tiempo_limite_min">Tiempo límite (minutos)</label>
                        <input id="tiempo_limite_min" name="tiempo_limite_min" type="number" min="1" value="<?= (int) ($borrador['tiempo_limite_min'] ?? 15) ?>" required>
                    </div>
                <?php endif; ?>

                <?php foreach ($borrador['preguntas'] as $indice => $pregunta): ?>
                    <article class="profesor-ia-question">
                        <h3>Pregunta <?= $indice + 1 ?></h3>

                        <div class="pregunta-preview">
                            <p class="pregunta-preview-enunciado"><?= htmlspecialchars((string) ($pregunta['enunciado'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                            <ul class="pregunta-preview-opciones">
                                <?php foreach (['a', 'b', 'c', 'd'] as $letra): ?>
                                    <?php $esCorrecta = strtolower((string) ($pregunta['respuesta_correcta'] ?? '')) === $letra; ?>
                                    <li class="<?= $esCorrecta ? 'is-correcta' : '' ?>">
                                        <span class="pregunta-preview-letra"><?= strtoupper($letra) ?></span>
                                        <span><?= htmlspecialchars((string) ($pregunta['opcion_' . $letra] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if ($esCorrecta): ?><span class="pregunta-preview-check">✓ Correcta</span><?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (trim((string) ($pregunta['explicacion'] ?? '')) !== ''): ?>
                                <p class="pregunta-preview-explicacion"><strong>Explicación:</strong> <?= htmlspecialchars((string) $pregunta['explicacion'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>

                        <details class="profesor-ia-question-editar">
                            <summary>Editar esta pregunta antes de publicar</summary>

                            <div class="form-grid">
                                <div class="practica-field">
                                    <label>Tema</label>
                                    <select name="preguntas[<?= $indice ?>][tema_id]" required>
                                        <?php foreach ($contextoIA['temas_autorizados'] as $tema): ?>
                                            <option value="<?= (int) $tema['id'] ?>" <?= (int) ($pregunta['tema_id'] ?? 0) === (int) $tema['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($tema['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="practica-field">
                                    <label>Tipo de pregunta</label>
                                    <select name="preguntas[<?= $indice ?>][tipo_pregunta_id]" required>
                                        <?php foreach ($contextoIA['tipos_pregunta_autorizados'] as $tipo): ?>
                                            <option value="<?= (int) $tipo['id'] ?>" <?= (int) ($pregunta['tipo_pregunta_id'] ?? 0) === (int) $tipo['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($tipo['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="practica-field">
                                <label>Enunciado</label>
                                <textarea name="preguntas[<?= $indice ?>][enunciado]" rows="5" required><?= htmlspecialchars((string) ($pregunta['enunciado'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>

                            <div class="form-grid">
                                <?php foreach (['a', 'b', 'c', 'd'] as $letra): ?>
                                    <div class="practica-field">
                                        <label>Opción <?= strtoupper($letra) ?></label>
                                        <textarea name="preguntas[<?= $indice ?>][opcion_<?= $letra ?>]" rows="3" required><?= htmlspecialchars((string) ($pregunta['opcion_' . $letra] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="form-grid">
                                <div class="practica-field">
                                    <label>Respuesta correcta</label>
                                    <select name="preguntas[<?= $indice ?>][respuesta_correcta]" required>
                                        <?php foreach (['a', 'b', 'c', 'd'] as $letra): ?>
                                            <option value="<?= $letra ?>" <?= ($pregunta['respuesta_correcta'] ?? '') === $letra ? 'selected' : '' ?>><?= strtoupper($letra) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="practica-field">
                                    <label>Dificultad</label>
                                    <select name="preguntas[<?= $indice ?>][dificultad]" required>
                                        <?php foreach (['facil', 'media', 'dificil'] as $nivel): ?>
                                            <option value="<?= $nivel ?>" <?= ($pregunta['dificultad'] ?? '') === $nivel ? 'selected' : '' ?>><?= ucfirst($nivel) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="practica-field">
                                <label>Explicación / justificación</label>
                                <textarea name="preguntas[<?= $indice ?>][explicacion]" rows="4" required><?= htmlspecialchars((string) ($pregunta['explicacion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>

                            <p class="admin-table-muted">
                                Área: <?= htmlspecialchars((string) ($pregunta['area'] ?? 'No indicada'), ENT_QUOTES, 'UTF-8') ?>
                                · Competencia: <?= htmlspecialchars((string) ($pregunta['competencia'] ?? 'No indicada'), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </details>
                    </article>
                <?php endforeach; ?>

                <div class="admin-toolbar">
                    <button class="primary-button practica-submit" type="submit">
                        <?= ($borrador['_meta']['modo'] ?? '') === 'practica' ? 'Publicar preguntas en el banco' : 'Publicar simulacro' ?>
                    </button>
                </div>
            </form>

            <form action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>profesor/ia/descartar" method="post" style="margin-top:1rem">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <button class="secondary-button" type="submit">Descartar borrador</button>
            </form>
        </section>
    <?php endif; ?>
<?php endif; ?>