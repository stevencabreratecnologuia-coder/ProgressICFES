<section class="practica-intro">

    <p class="eyebrow">TU PROGRESO</p>

    <h1>Así vas en tu preparación.</h1>

    <p class="practica-subtitle">
        Resultados acumulados por materia, según tus prácticas y simulacros.
    </p>

</section>

<section class="resumen-grid">

    <div class="resumen-card">

        <span class="resumen-card-value">
            <?= (int) $resumen['preguntas_practica'] ?>
        </span>

        <span class="resumen-card-label">
            Preguntas practicadas
        </span>

    </div>

    <div class="resumen-card">

        <span class="resumen-card-value">
            <?= (int) $resumen['porcentaje_practica'] ?>%
        </span>

        <span class="resumen-card-label">
            Aciertos en práctica
        </span>

    </div>

    <div class="resumen-card">

        <span class="resumen-card-value">
            <?= (int) $resumen['intentos_simulacro'] ?>
        </span>

        <span class="resumen-card-label">
            Simulacros presentados
        </span>

    </div>

    <div class="resumen-card is-highlight">

        <span class="resumen-card-value">
            <?= (int) $resumen['mejor_puntaje_icfes'] ?>
            <small>/500</small>
        </span>

        <span class="resumen-card-label">
            Mejor puntaje ICFES
        </span>

    </div>

</section>


<section class="progreso-section">

    <h2 class="progreso-section-title">
        Práctica libre, por materia
    </h2>

    <?php if ($porMateriaPractica === []): ?>

        <p class="practica-subtitle">
            Todavía no tienes práctica registrada.
            Ve a "Práctica" para empezar a acumular resultados.
        </p>

    <?php else: ?>

        <div class="materia-card-grid">

            <?php foreach ($porMateriaPractica as $fila): ?>

                <?php
                $esIngles =
                    mb_strtolower(
                        trim((string) $fila['materia']),
                        'UTF-8'
                    ) === 'inglés'
                    ||
                    mb_strtolower(
                        trim((string) $fila['materia']),
                        'UTF-8'
                    ) === 'ingles';

                $nivelActual = null;
                $proximoNivel = null;
                $mensajeNivel = null;

                if (
                    $esIngles
                    &&
                    isset($nivelIngles)
                    &&
                    is_array($nivelIngles)
                ) {
                    $nivelActual =
                        $nivelIngles['nivel'] ?? null;

                    $proximoNivel =
                        $nivelIngles['proximo_nivel'] ?? null;

                    $mensajeNivel =
                        $nivelIngles['mensaje'] ?? null;
                }
                ?>

                <div
                    class="materia-card <?= htmlspecialchars(
                                            $fila['clase_color'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?><?= $esIngles ? ' materia-card-ingles' : '' ?>">

                    <div
                        class="materia-radial"
                        style="--pct: <?= (int) $fila['porcentaje'] ?>;">
                        <span class="materia-radial-value">
                            <?= (int) $fila['porcentaje'] ?>%
                        </span>
                    </div>

                    <div class="materia-card-info">

                        <span class="materia-card-name">
                            <?= htmlspecialchars(
                                $fila['materia'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                        <span class="materia-card-detail">
                            <?= (int) $fila['correctas'] ?>
                            /
                            <?= (int) $fila['total'] ?>
                            correctas
                        </span>

                        <?php if ($esIngles && $nivelActual !== null): ?>

                            <div class="nivel-ingles-card">

                                <span class="nivel-ingles-label">
                                    Nivel actual
                                </span>

                                <strong class="nivel-ingles-value">
                                    <?= htmlspecialchars(
                                        (string) $nivelActual,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                                <?php if ($proximoNivel !== null): ?>

                                    <span class="nivel-ingles-next">
                                        Próximo nivel:
                                        <strong>
                                            <?= htmlspecialchars(
                                                (string) $proximoNivel,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
                                    </span>

                                <?php else: ?>

                                    <span class="nivel-ingles-next">
                                        Has alcanzado el nivel más alto
                                    </span>

                                <?php endif; ?>

                                <?php if ($mensajeNivel !== null): ?>

                                    <span class="nivel-ingles-message">
                                        <?= htmlspecialchars(
                                            (string) $mensajeNivel,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                <?php endif; ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>


<section class="progreso-section">

    <h2 class="progreso-section-title">
        Simulacros, por materia
    </h2>

    <?php if ($porMateriaSimulacro === []): ?>

        <p class="practica-subtitle">
            Todavía no has presentado simulacros.
            Ve a "Simulacros" para empezar.
        </p>

    <?php else: ?>

        <div class="materia-card-grid">

            <?php foreach ($porMateriaSimulacro as $fila): ?>

                <?php
                $esIngles =
                    mb_strtolower(
                        trim((string) $fila['materia']),
                        'UTF-8'
                    ) === 'inglés'
                    ||
                    mb_strtolower(
                        trim((string) $fila['materia']),
                        'UTF-8'
                    ) === 'ingles';

                $nivelActual = null;
                $proximoNivel = null;
                $mensajeNivel = null;

                if (
                    $esIngles
                    &&
                    isset($nivelIngles)
                    &&
                    is_array($nivelIngles)
                ) {
                    $nivelActual =
                        $nivelIngles['nivel'] ?? null;

                    $proximoNivel =
                        $nivelIngles['proximo_nivel'] ?? null;

                    $mensajeNivel =
                        $nivelIngles['mensaje'] ?? null;
                }
                ?>

                <div
                    class="materia-card <?= htmlspecialchars(
                                            $fila['clase_color'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?><?= $esIngles ? ' materia-card-ingles' : '' ?>">

                    <div
                        class="materia-radial"
                        style="--pct: <?= (int) $fila['porcentaje'] ?>;">
                        <span class="materia-radial-value">
                            <?= (int) $fila['porcentaje'] ?>%
                        </span>
                    </div>

                    <div class="materia-card-info">

                        <span class="materia-card-name">
                            <?= htmlspecialchars(
                                $fila['materia'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                        <span class="materia-card-detail">
                            <?= (int) $fila['correctas'] ?>
                            /
                            <?= (int) $fila['total'] ?>
                            correctas
                        </span>

                        <?php if ($esIngles && $nivelActual !== null): ?>

                            <div class="nivel-ingles-card">

                                <span class="nivel-ingles-label">
                                    Nivel actual
                                </span>

                                <strong class="nivel-ingles-value">
                                    <?= htmlspecialchars(
                                        (string) $nivelActual,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                                <?php if ($proximoNivel !== null): ?>

                                    <span class="nivel-ingles-next">
                                        Próximo nivel:
                                        <strong>
                                            <?= htmlspecialchars(
                                                (string) $proximoNivel,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
                                    </span>

                                <?php else: ?>

                                    <span class="nivel-ingles-next">
                                        Has alcanzado el nivel más alto
                                    </span>

                                <?php endif; ?>

                                <?php if ($mensajeNivel !== null): ?>

                                    <span class="nivel-ingles-message">
                                        <?= htmlspecialchars(
                                            (string) $mensajeNivel,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                <?php endif; ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <?php if ($historialSimulacros !== []): ?>

        <h3 class="progreso-subtitle">
            Últimos intentos
        </h3>

        <div class="progreso-historial">

            <?php foreach ($historialSimulacros as $intento): ?>

                <div class="progreso-historial-item">

                    <span class="progreso-historial-icono">
                        🎯
                    </span>

                    <div class="progreso-historial-texto">

                        <span class="progreso-historial-titulo">
                            <?= htmlspecialchars(
                                (string) $intento['titulo'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                        <span class="progreso-historial-fecha">

                            <?= htmlspecialchars(
                                date(
                                    'd/m/Y',
                                    strtotime(
                                        (string) $intento['fecha_realizacion']
                                    )
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>

                    </div>

                    <span class="progreso-historial-puntaje">
                        <?= (int) $intento['puntaje_estimado_icfes'] ?>/500
                    </span>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>