<?php

declare(strict_types=1);

$rolUsuario = $_SESSION['usuario_rol'] ?? '';

?>

<div class="help-page">

    <!-- =========================================================
         ENCABEZADO
    ========================================================== -->

    <section class="help-hero">

        <div class="help-hero-icon">
            ?
        </div>

        <div>

            <p class="eyebrow">
                CENTRO DE AYUDA
            </p>

            <h1>
                Manual de ProgressICFES
            </h1>

            <p class="help-hero-text">
                Aprende a utilizar las herramientas de ProgressICFES,
                realiza prácticas, presenta simulacros, consulta tu
                progreso y utiliza ProgressIA como apoyo para tu
                preparación para Saber 11.
            </p>

        </div>

    </section>


    <!-- =========================================================
         INTRODUCCIÓN
    ========================================================== -->

    <section class="help-card help-introduction">

        <div class="help-card-icon">
            📖
        </div>

        <div>

            <p class="help-label">
                BIENVENIDO
            </p>

            <h2>
                ¿Qué es ProgressICFES?
            </h2>

            <p>
                ProgressICFES es una plataforma educativa diseñada
                para apoyar la preparación de estudiantes de educación
                secundaria, especialmente de grados 9°, 10° y 11°,
                para las pruebas Saber 11.
            </p>

            <p>
                La plataforma combina prácticas académicas,
                simulacros, seguimiento del progreso y un asistente
                de inteligencia artificial llamado ProgressIA.
            </p>

            <p>
                Cada herramienta tiene como objetivo ayudarte a
                identificar tus fortalezas, reconocer los temas que
                necesitan refuerzo y mejorar progresivamente tu
                desempeño.
            </p>

        </div>

    </section>


    <!-- =========================================================
         ÍNDICE
    ========================================================== -->

    <section class="help-card help-index-card">

        <div class="help-index-header">

            <div>

                <p class="help-label">
                    NAVEGACIÓN
                </p>

                <h2>
                    Contenido del manual
                </h2>

            </div>

            <span class="help-index-icon">
                ☰
            </span>

        </div>


        <div class="help-index-grid">

            <a href="#inicio">
                <span>01</span>
                Inicio y navegación
            </a>

            <a href="#practica">
                <span>02</span>
                Práctica
            </a>

            <a href="#simulacros">
                <span>03</span>
                Simulacros
            </a>

            <?php if ($rolUsuario === 'estudiante'): ?>

                <a href="#progreso">
                    <span>04</span>
                    Progreso
                </a>

            <?php endif; ?>

            <a href="#progressia">
                <span>05</span>
                ProgressIA
            </a>

            <a href="#perfil">
                <span>06</span>
                Perfil
            </a>

            <?php if ($rolUsuario === 'profesor' || $rolUsuario === 'admin'): ?>

                <a href="#profesores">
                    <span>07</span>
                    Herramientas para profesores
                </a>

            <?php endif; ?>

            <?php if ($rolUsuario === 'admin'): ?>

                <a href="#administrador">
                    <span>08</span>
                    Administración
                </a>

            <?php endif; ?>

            <a href="#preguntas-frecuentes">
                <span>09</span>
                Preguntas frecuentes
            </a>

            <a href="#consejos">
                <span>10</span>
                Consejos de uso
            </a>

        </div>

    </section>


    <!-- =========================================================
         01 - INICIO
    ========================================================== -->

    <section class="help-section" id="inicio">

        <div class="help-section-heading">

            <span class="help-number">
                01
            </span>

            <div>

                <p class="help-label">
                    PRIMEROS PASOS
                </p>

                <h2>
                    Inicio y navegación
                </h2>

            </div>

        </div>


        <div class="help-content">

            <p>
                Después de iniciar sesión encontrarás el panel
                principal de ProgressICFES. Desde allí puedes acceder
                a las diferentes herramientas disponibles para tu
                cuenta.
            </p>


            <h3>
                Menú principal
            </h3>

            <div class="help-feature-grid">

                <article class="help-feature">

                    <span>⌂</span>

                    <div>

                        <h4>
                            Inicio
                        </h4>

                        <p>
                            Permite regresar al panel principal de
                            ProgressICFES.
                        </p>

                    </div>

                </article>


                <article class="help-feature">

                    <span>▣</span>

                    <div>

                        <h4>
                            Práctica
                        </h4>

                        <p>
                            Permite realizar sesiones de preguntas
                            seleccionando una materia y, cuando esté
                            disponible, un tema.
                        </p>

                    </div>

                </article>


                <article class="help-feature">

                    <span>◎</span>

                    <div>

                        <h4>
                            Simulacros
                        </h4>

                        <p>
                            Permite presentar evaluaciones configuradas
                            dentro de la plataforma.
                        </p>

                    </div>

                </article>


                <?php if ($rolUsuario === 'estudiante'): ?>

                    <article class="help-feature">

                        <span>▥</span>

                        <div>

                            <h4>
                                Progreso
                            </h4>

                            <p>
                                Permite consultar estadísticas y
                                resultados de tus actividades.
                            </p>

                        </div>

                    </article>

                <?php endif; ?>


                <article class="help-feature">

                    <span>🤖</span>

                    <div>

                        <h4>
                            ProgressIA
                        </h4>

                        <p>
                            Tutor inteligente para resolver dudas,
                            estudiar y recibir apoyo académico.
                        </p>

                    </div>

                </article>


                <article class="help-feature">

                    <span>♙</span>

                    <div>

                        <h4>
                            Perfil
                        </h4>

                        <p>
                            Permite consultar y administrar la
                            información disponible de tu cuenta.
                        </p>

                    </div>

                </article>

            </div>


            <div class="help-tip">

                <strong>
                    💡 Consejo
                </strong>

                <p>
                    Utiliza el menú lateral para desplazarte entre
                    las herramientas de la plataforma. Las opciones
                    disponibles pueden cambiar dependiendo del rol
                    de tu cuenta.
                </p>

            </div>

        </div>

    </section>


    <!-- =========================================================
         02 - PRÁCTICA
    ========================================================== -->

    <section class="help-section" id="practica">

        <div class="help-section-heading">

            <span class="help-number">
                02
            </span>

            <div>

                <p class="help-label">
                    ENTRENAMIENTO
                </p>

                <h2>
                    ¿Cómo utilizar Práctica?
                </h2>

            </div>

        </div>


        <div class="help-content">

            <p>
                La sección Práctica permite entrenar con preguntas
                seleccionadas según la materia y, opcionalmente,
                un tema específico.
            </p>


            <h3>
                Iniciar una práctica
            </h3>


            <div class="help-steps">

                <div class="help-step">

                    <span>1</span>

                    <div>

                        <strong>
                            Ingresa a Práctica
                        </strong>

                        <p>
                            Selecciona la opción <strong>Práctica</strong>
                            en el menú lateral.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>2</span>

                    <div>

                        <strong>
                            Selecciona una materia
                        </strong>

                        <p>
                            Escoge la materia que deseas practicar.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>3</span>

                    <div>

                        <strong>
                            Selecciona un tema
                        </strong>

                        <p>
                            Si deseas concentrarte en un tema
                            específico, selecciónalo. También puedes
                            realizar una práctica general de la materia.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>4</span>

                    <div>

                        <strong>
                            Selecciona la cantidad
                        </strong>

                        <p>
                            La plataforma permite seleccionar
                            <strong>5, 10, 15 o 20 preguntas</strong>.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>5</span>

                    <div>

                        <strong>
                            Responde las preguntas
                        </strong>

                        <p>
                            Lee cuidadosamente cada pregunta y
                            selecciona la opción que consideres correcta.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>6</span>

                    <div>

                        <strong>
                            Consulta el resultado
                        </strong>

                        <p>
                            Al finalizar, ProgressICFES corrige las
                            respuestas y muestra el resultado de la
                            sesión.
                        </p>

                    </div>

                </div>

            </div>


            <h3>
                ¿Qué ocurre con las respuestas?
            </h3>

            <p>
                Las respuestas de la práctica se procesan para
                determinar cuáles fueron correctas e incorrectas.
                El resultado permite obtener información sobre el
                desempeño del estudiante.
            </p>


            <div class="help-warning">

                <strong>
                    ℹ️ Importante
                </strong>

                <p>
                    El puntaje mostrado por la plataforma es una
                    <strong>estimación</strong> calculada a partir
                    del porcentaje de respuestas correctas y
                    convertida a una escala de 0 a 500. No representa
                    necesariamente el puntaje oficial calculado por
                    el ICFES mediante su metodología de evaluación.
                </p>

            </div>

        </div>

    </section>


    <!-- =========================================================
         03 - SIMULACROS
    ========================================================== -->

    <section class="help-section" id="simulacros">

        <div class="help-section-heading">

            <span class="help-number">
                03
            </span>

            <div>

                <p class="help-label">
                    EVALUACIÓN
                </p>

                <h2>
                    Simulacros
                </h2>

            </div>

        </div>


        <div class="help-content">

            <p>
                Los simulacros permiten realizar evaluaciones
                estructuradas dentro de ProgressICFES. A diferencia
                de una práctica libre, los simulacros pueden haber
                sido configurados previamente por profesores o
                administradores.
            </p>


            <h3>
                Presentar un simulacro
            </h3>


            <div class="help-steps">

                <div class="help-step">

                    <span>1</span>

                    <div>

                        <strong>
                            Ingresa a Simulacros
                        </strong>

                        <p>
                            Selecciona <strong>Simulacros</strong>
                            desde el menú.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>2</span>

                    <div>

                        <strong>
                            Selecciona una evaluación
                        </strong>

                        <p>
                            Revisa los simulacros disponibles y
                            selecciona el que deseas presentar.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>3</span>

                    <div>

                        <strong>
                            Revisa las condiciones
                        </strong>

                        <p>
                            Antes de comenzar, revisa la cantidad
                            de preguntas y el tiempo disponible.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>4</span>

                    <div>

                        <strong>
                            Inicia el simulacro
                        </strong>

                        <p>
                            Una vez iniciado, responde las preguntas
                            dentro del tiempo establecido.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>5</span>

                    <div>

                        <strong>
                            Finaliza
                        </strong>

                        <p>
                            Envía tus respuestas cuando hayas
                            terminado.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>6</span>

                    <div>

                        <strong>
                            Consulta tu resultado
                        </strong>

                        <p>
                            El sistema procesa las respuestas y
                            registra el resultado de la evaluación.
                        </p>

                    </div>

                </div>

            </div>


            <div class="help-tip">

                <strong>
                    ⏱️ Sobre el tiempo
                </strong>

                <p>
                    Algunos simulacros tienen un límite de tiempo.
                    Organiza tu tiempo y evita dedicar demasiado
                    tiempo a una sola pregunta.
                </p>

            </div>


            <h3>
                Tipos de simulacro
            </h3>


            <div class="help-type-grid">

                <article>

                    <span>
                        Diagnóstico
                    </span>

                    <p>
                        Permite identificar el nivel actual del
                        estudiante y detectar áreas que necesitan
                        atención.
                    </p>

                </article>


                <article>

                    <span>
                        Refuerzo
                    </span>

                    <p>
                        Está orientado a trabajar temas o competencias
                        donde se presentan dificultades.
                    </p>

                </article>


                <article>

                    <span>
                        Preparación ICFES
                    </span>

                    <p>
                        Busca fortalecer las competencias necesarias
                        para la preparación de Saber 11.
                    </p>

                </article>


                <article>

                    <span>
                        Recuperación
                    </span>

                    <p>
                        Permite reforzar competencias que todavía no
                        han sido alcanzadas adecuadamente.
                    </p>

                </article>


                <article>

                    <span>
                        Avanzado
                    </span>

                    <p>
                        Busca aumentar progresivamente el nivel de
                        dificultad.
                    </p>

                </article>

            </div>

        </div>

    </section>


    <?php if ($rolUsuario === 'estudiante'): ?>

        <!-- =====================================================
             04 - PROGRESO
        ====================================================== -->

        <section class="help-section" id="progreso">

            <div class="help-section-heading">

                <span class="help-number">
                    04
                </span>

                <div>

                    <p class="help-label">
                        SEGUIMIENTO
                    </p>

                    <h2>
                        Progreso
                    </h2>

                </div>

            </div>


            <div class="help-content">

                <p>
                    La sección Progreso permite consultar información
                    relacionada con tu desempeño académico dentro de
                    la plataforma.
                </p>


                <h3>
                    ¿Qué puedes consultar?
                </h3>


                <div class="help-feature-grid">

                    <article class="help-feature">

                        <span>✓</span>

                        <div>

                            <h4>
                                Preguntas realizadas
                            </h4>

                            <p>
                                Consulta la cantidad de preguntas
                                realizadas durante las prácticas.
                            </p>

                        </div>

                    </article>


                    <article class="help-feature">

                        <span>%</span>

                        <div>

                            <h4>
                                Porcentaje de aciertos
                            </h4>

                            <p>
                                Permite observar qué porcentaje de
                                respuestas fueron correctas.
                            </p>

                        </div>

                    </article>


                    <article class="help-feature">

                        <span>◎</span>

                        <div>

                            <h4>
                                Intentos
                            </h4>

                            <p>
                                Muestra información relacionada con
                                los simulacros realizados.
                            </p>

                        </div>

                    </article>


                    <article class="help-feature">

                        <span>★</span>

                        <div>

                            <h4>
                                Mejor resultado
                            </h4>

                            <p>
                                Permite identificar el mejor puntaje
                                estimado registrado en los simulacros.
                            </p>

                        </div>

                    </article>

                </div>


                <h3>
                    ¿Cómo aprovechar el progreso?
                </h3>

                <p>
                    No debes utilizar las estadísticas únicamente
                    para observar un número. Úsalas para detectar
                    qué áreas necesitan mayor práctica y establecer
                    objetivos de estudio.
                </p>


                <div class="help-tip">

                    <strong>
                        💡 Recomendación
                    </strong>

                    <p>
                        Si una materia presenta resultados inferiores
                        a las demás, dedica sesiones adicionales de
                        práctica a esa área y utiliza ProgressIA para
                        comprender los conceptos que más dificultades
                        te generan.
                    </p>

                </div>

            </div>

        </section>

    <?php endif; ?>


    <!-- =========================================================
         05 - PROGRESSIA
    ========================================================== -->

    <section class="help-section help-progressia-section" id="progressia">

        <div class="help-section-heading">

            <span class="help-number">
                05
            </span>

            <div>

                <p class="help-label">
                    INTELIGENCIA ARTIFICIAL EDUCATIVA
                </p>

                <h2>
                    ProgressIA
                </h2>

            </div>

        </div>


        <div class="help-content">

            <div class="progressia-manual-intro">

                <div class="progressia-manual-icon">
                    🤖
                </div>

                <div>

                    <h3>
                        Tu tutor inteligente
                    </h3>

                    <p>
                        ProgressIA es el sistema de inteligencia
                        artificial educativa integrado en ProgressICFES.
                        Su objetivo es ayudarte a aprender, comprender
                        conceptos, analizar resultados y mejorar tu
                        preparación para Saber 11.
                    </p>

                </div>

            </div>


            <div class="help-warning">

                <strong>
                    🧠 No es un chatbot general
                </strong>

                <p>
                    ProgressIA está diseñado específicamente como
                    componente educativo de ProgressICFES. Su
                    comportamiento está orientado principalmente a
                    educación secundaria, preparación para Saber 11,
                    evaluación académica y aprendizaje personalizado.
                </p>

            </div>


            <h3>
                ¿Qué puede hacer ProgressIA?
            </h3>


            <div class="help-feature-grid">

                <article class="help-feature">

                    <span>📚</span>

                    <div>

                        <h4>
                            Explicar temas
                        </h4>

                        <p>
                            Puede explicar conceptos académicos de
                            manera clara y adaptada al nivel del
                            estudiante.
                        </p>

                    </div>

                </article>


                <article class="help-feature">

                    <span>✏️</span>

                    <div>

                        <h4>
                            Generar ejercicios
                        </h4>

                        <p>
                            Puede proponer ejercicios y actividades
                            para reforzar diferentes competencias.
                        </p>

                    </div>

                </article>


                <article class="help-feature">

                    <span>🎯</span>

                    <div>

                        <h4>
                            Personalizar el aprendizaje
                        </h4>

                        <p>
                            Cuando el sistema proporciona información
                            académica disponible, puede adaptar las
                            recomendaciones al desempeño.
                        </p>

                    </div>

                </article>


                <article class="help-feature">

                    <span>🔎</span>

                    <div>

                        <h4>
                            Analizar errores
                        </h4>

                        <p>
                            Puede ayudar a comprender por qué una
                            respuesta es incorrecta y qué razonamiento
                            debería utilizarse.
                        </p>

                    </div>

                </article>


                <article class="help-feature">

                    <span>📈</span>

                    <div>

                        <h4>
                            Analizar desempeño
                        </h4>

                        <p>
                            Puede convertir datos académicos
                            proporcionados por el sistema en
                            recomendaciones educativas.
                        </p>

                    </div>

                </article>


                <article class="help-feature">

                    <span>🧩</span>

                    <div>

                        <h4>
                            Recomendar estudio
                        </h4>

                        <p>
                            Puede proponer temas, ejercicios y
                            estrategias de estudio según la información
                            disponible.
                        </p>

                    </div>

                </article>

            </div>


            <h3>
                Áreas académicas
            </h3>


            <div class="help-area-grid">

                <article>

                    <h4>
                        📖 Lectura Crítica
                    </h4>

                    <p>
                        Comprensión textual, inferencias, argumentación,
                        interpretación, análisis de información y
                        evaluación de argumentos.
                    </p>

                </article>


                <article>

                    <h4>
                        📐 Matemáticas
                    </h4>

                    <p>
                        Álgebra, geometría, estadística, probabilidad,
                        funciones, razonamiento lógico y resolución
                        de problemas.
                    </p>

                </article>


                <article>

                    <h4>
                        🔬 Ciencias Naturales
                    </h4>

                    <p>
                        Biología, física, química, interpretación
                        científica y análisis experimental.
                    </p>

                </article>


                <article>

                    <h4>
                        🌎 Sociales y Ciudadanas
                    </h4>

                    <p>
                        Historia, Constitución, democracia, economía,
                        geografía, pensamiento ciudadano y análisis
                        social.
                    </p>

                </article>


                <article>

                    <h4>
                        🇬🇧 Inglés
                    </h4>

                    <p>
                        Comprensión lectora, gramática, vocabulario,
                        interpretación textual y comprensión de
                        información en niveles A1-B2.
                    </p>

                </article>

            </div>


            <h3>
                Cómo hacer una buena pregunta
            </h3>

            <p>
                Para obtener una explicación útil, intenta escribir
                claramente qué necesitas aprender.
            </p>


            <div class="help-example">

                <div class="help-example-bad">

                    <span>
                        ❌ Pregunta poco específica
                    </span>

                    <p>
                        "No entiendo matemáticas."
                    </p>

                </div>


                <div class="help-example-good">

                    <span>
                        ✓ Pregunta más útil
                    </span>

                    <p>
                        "Explícame cómo resolver ecuaciones cuadráticas
                        paso a paso y dame un ejercicio tipo ICFES
                        para practicar."
                    </p>

                </div>

            </div>


            <h3>
                Generación de preguntas tipo ICFES
            </h3>

            <p>
                ProgressIA puede generar preguntas educativas
                estructuradas para practicar competencias. Cuando
                corresponda, una pregunta puede incluir:
            </p>


            <ul class="help-list">

                <li>
                    Contexto.
                </li>

                <li>
                    Situación problema.
                </li>

                <li>
                    Texto, tabla, gráfico u otra información.
                </li>

                <li>
                    Pregunta.
                </li>

                <li>
                    Cuatro opciones: A, B, C y D.
                </li>

                <li>
                    Área académica.
                </li>

                <li>
                    Competencia.
                </li>

                <li>
                    Tema.
                </li>

                <li>
                    Nivel de dificultad.
                </li>

                <li>
                    Respuesta correcta.
                </li>

                <li>
                    Justificación.
                </li>

            </ul>


            <h3>
                Retroalimentación
            </h3>

            <p>
                Cuando se analice una respuesta, ProgressIA puede
                explicar el razonamiento necesario para resolverla.
                La finalidad no es solamente indicar si una respuesta
                está bien o mal, sino convertir el error en una
                oportunidad de aprendizaje.
            </p>


            <div class="help-feedback-flow">

                <span>
                    Respuesta
                </span>

                <b>→</b>

                <span>
                    Análisis
                </span>

                <b>→</b>

                <span>
                    Explicación
                </span>

                <b>→</b>

                <span>
                    Estrategia
                </span>

                <b>→</b>

                <span>
                    Nuevo aprendizaje
                </span>

            </div>


            <h3>
                Adaptación de dificultad
            </h3>

            <p>
                Cuando el sistema dispone de evidencia suficiente
                sobre el desempeño académico, ProgressIA puede adaptar
                la dificultad.
            </p>


            <div class="help-adaptation-grid">

                <article>

                    <span>
                        📈 Buen desempeño
                    </span>

                    <p>
                        Puede aumentar la dificultad, introducir
                        problemas más complejos y exigir mayor nivel
                        de análisis.
                    </p>

                </article>


                <article>

                    <span>
                        📚 Dificultades
                    </span>

                    <p>
                        Puede reforzar conceptos fundamentales,
                        explicar nuevamente y proponer ejercicios
                        progresivos.
                    </p>

                </article>

            </div>


            <h3>
                ProgressIA y los roles
            </h3>

            <p>
                ProgressIA no debe confiar en lo que una persona diga
                sobre su propio rol. El sistema utiliza la información
                oficial proporcionada por el backend y la sesión
                autenticada.
            </p>


            <div class="help-role-flow">

                <div>
                    <strong>
                        Usuario autenticado
                    </strong>

                    <small>
                        Identidad oficial
                    </small>
                </div>

                <span>↓</span>

                <div>
                    <strong>
                        Backend
                    </strong>

                    <small>
                        Rol y permisos
                    </small>
                </div>

                <span>↓</span>

                <div>
                    <strong>
                        ProgressIA
                    </strong>

                    <small>
                        Funciones permitidas
                    </small>
                </div>

            </div>


            <div class="help-security-box">

                <strong>
                    🔐 Seguridad y privacidad
                </strong>

                <p>
                    ProgressIA debe respetar los permisos establecidos
                    por ProgressICFES. Un usuario no puede obtener
                    privilegios adicionales simplemente escribiendo una
                    instrucción en el chat.
                </p>

                <p>
                    Los estudiantes no deben recibir información
                    privada de otros estudiantes ni acceder a funciones
                    exclusivas de profesores o administradores.
                </p>

                <p>
                    Del mismo modo, la IA no debe inventar notas,
                    resultados, estudiantes, estadísticas, historial
                    académico o permisos que no hayan sido proporcionados
                    por el sistema.
                </p>

            </div>


            <h3>
                ¿Qué información utiliza ProgressIA?
            </h3>

            <p>
                Dependiendo de la información proporcionada por la
                aplicación, puede utilizar datos como:
            </p>

            <ul class="help-list">

                <li>
                    Identificación del usuario.
                </li>

                <li>
                    Rol.
                </li>

                <li>
                    Grado.
                </li>

                <li>
                    Curso o grupo.
                </li>

                <li>
                    Resultados académicos disponibles.
                </li>

                <li>
                    Historial de respuestas.
                </li>

                <li>
                    Áreas fuertes y débiles.
                </li>

                <li>
                    Competencias con dificultades.
                </li>

                <li>
                    Progreso disponible.
                </li>

            </ul>


            <div class="help-tip">

                <strong>
                    💡 Recuerda
                </strong>

                <p>
                    Si un dato académico no está disponible para la
                    aplicación, ProgressIA debe reconocer que no tiene
                    acceso a esa información en lugar de inventarla.
                </p>

            </div>

        </div>

    </section>


    <!-- =========================================================
         06 - PERFIL
    ========================================================== -->

    <section class="help-section" id="perfil">

        <div class="help-section-heading">

            <span class="help-number">
                06
            </span>

            <div>

                <p class="help-label">
                    CUENTA
                </p>

                <h2>
                    Perfil
                </h2>

            </div>

        </div>


        <div class="help-content">

            <p>
                La sección Perfil permite consultar y administrar
                la información disponible de tu cuenta.
            </p>


            <h3>
                Actualizar información personal
            </h3>

            <div class="help-steps">

                <div class="help-step">

                    <span>1</span>

                    <div>

                        <strong>
                            Abre Perfil
                        </strong>

                        <p>
                            Selecciona tu perfil desde el menú o
                            desde el avatar de la parte superior.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>2</span>

                    <div>

                        <strong>
                            Modifica tus datos
                        </strong>

                        <p>
                            Actualiza la información disponible.
                        </p>

                    </div>

                </div>


                <div class="help-step">

                    <span>3</span>

                    <div>

                        <strong>
                            Guarda los cambios
                        </strong>

                        <p>
                            Confirma la actualización.
                        </p>

                    </div>

                </div>

            </div>


            <h3>
                Cambiar contraseña
            </h3>

            <p>
                Para cambiar tu contraseña debes proporcionar la
                contraseña actual y establecer una nueva contraseña
                que cumpla los requisitos de seguridad de la plataforma.
            </p>


            <div class="help-warning">

                <strong>
                    🔐 Requisito de contraseña
                </strong>

                <p>
                    La nueva contraseña debe tener como mínimo
                    <strong>8 caracteres</strong>, incluir al menos
                    una letra y un número.
                </p>

            </div>

        </div>

    </section>


    <?php if ($rolUsuario === 'profesor' || $rolUsuario === 'admin'): ?>

        <!-- =====================================================
             07 - PROFESORES
        ====================================================== -->

        <section class="help-section" id="profesores">

            <div class="help-section-heading">

                <span class="help-number">
                    07
                </span>

                <div>

                    <p class="help-label">
                        GESTIÓN ACADÉMICA
                    </p>

                    <h2>
                        Herramientas para profesores
                    </h2>

                </div>

            </div>


            <div class="help-content">

                <p>
                    Los profesores cuentan con herramientas adicionales
                    para administrar contenido académico y analizar
                    información relacionada con sus estudiantes.
                </p>


                <h3>
                    Gestión de preguntas
                </h3>

                <p>
                    El profesor puede trabajar con preguntas utilizadas
                    para las actividades académicas de la plataforma.
                </p>

                <ul class="help-list">

                    <li>
                        Crear preguntas.
                    </li>

                    <li>
                        Consultar preguntas.
                    </li>

                    <li>
                        Editar preguntas cuando corresponda.
                    </li>

                    <li>
                        Eliminar preguntas cuando tenga autorización.
                    </li>

                    <li>
                        Organizar contenido académico mediante
                        materias y temas.
                    </li>

                </ul>


                <h3>
                    Gestión de simulacros
                </h3>

                <p>
                    El profesor puede crear y administrar simulacros
                    asociados a su contexto académico.
                </p>

                <ul class="help-list">

                    <li>
                        Crear simulacros.
                    </li>

                    <li>
                        Seleccionar materia.
                    </li>

                    <li>
                        Configurar tiempo.
                    </li>

                    <li>
                        Seleccionar preguntas.
                    </li>

                    <li>
                        Editar simulacros.
                    </li>

                    <li>
                        Eliminar simulacros cuando tenga autorización.
                    </li>

                </ul>


                <h3>
                    Análisis académico
                </h3>

                <p>
                    El sistema puede utilizar información académica
                    disponible para ayudar a detectar dificultades
                    individuales o grupales.
                </p>


                <div class="help-analysis-grid">

                    <article>

                        <strong>
                            📊 Resultados
                        </strong>

                        <p>
                            Analizar el rendimiento disponible.
                        </p>

                    </article>


                    <article>

                        <strong>
                            🎯 Dificultades
                        </strong>

                        <p>
                            Detectar áreas, temas o competencias
                            con menor desempeño.
                        </p>

                    </article>


                    <article>

                        <strong>
                            👥 Grupos
                        </strong>

                        <p>
                            Identificar patrones de rendimiento
                            colectivo cuando la información esté
                            disponible.
                        </p>

                    </article>


                    <article>

                        <strong>
                            📚 Refuerzo
                        </strong>

                        <p>
                            Proponer actividades para atender las
                            necesidades detectadas.
                        </p>

                    </article>

                </div>


                <div class="help-security-box">

                    <strong>
                        🔐 Acceso de profesores
                    </strong>

                    <p>
                        Un profesor solamente debe acceder a los
                        estudiantes, grupos y datos que el sistema
                        le haya asociado oficialmente.
                    </p>

                    <p>
                        No se deben asumir permisos sobre todos los
                        estudiantes de ProgressICFES.
                    </p>

                </div>

            </div>

        </section>

    <?php endif; ?>


    <?php if ($rolUsuario === 'admin'): ?>

        <!-- =====================================================
             08 - ADMINISTRADOR
        ====================================================== -->

        <section class="help-section" id="administrador">

            <div class="help-section-heading">

                <span class="help-number">
                    08
                </span>

                <div>

                    <p class="help-label">
                        ADMINISTRACIÓN
                    </p>

                    <h2>
                        Herramientas del administrador
                    </h2>

                </div>

            </div>


            <div class="help-content">

                <p>
                    El administrador cuenta con herramientas para
                    gestionar diferentes elementos generales de la
                    plataforma.
                </p>


                <h3>
                    Gestión de usuarios
                </h3>

                <ul class="help-list">

                    <li>
                        Consultar usuarios.
                    </li>

                    <li>
                        Consultar información de usuarios.
                    </li>

                    <li>
                        Gestionar roles según los permisos del sistema.
                    </li>

                    <li>
                        Gestionar información académica asociada.
                    </li>

                </ul>


                <h3>
                    Gestión académica
                </h3>

                <ul class="help-list">

                    <li>
                        Materias.
                    </li>

                    <li>
                        Temas.
                    </li>

                    <li>
                        Tipos de pregunta.
                    </li>

                    <li>
                        Preguntas.
                    </li>

                    <li>
                        Simulacros.
                    </li>

                </ul>


                <h3>
                    Administración de simulacros
                </h3>

                <p>
                    El administrador puede gestionar los simulacros
                    generales de la plataforma, incluyendo su
                    configuración y las preguntas asociadas.
                </p>


                <div class="help-security-box">

                    <strong>
                        🔐 Importante
                    </strong>

                    <p>
                        Tener el rol de administrador no significa que
                        la inteligencia artificial pueda ignorar las
                        restricciones de la aplicación.
                    </p>

                    <p>
                        Las operaciones deben respetar siempre los
                        permisos y controles establecidos por el
                        backend de ProgressICFES.
                    </p>

                </div>

            </div>

        </section>

    <?php endif; ?>


    <!-- =========================================================
         09 - FAQ
    ========================================================== -->

    <section class="help-section" id="preguntas-frecuentes">

        <div class="help-section-heading">

            <span class="help-number">
                09
            </span>

            <div>

                <p class="help-label">
                    FAQ
                </p>

                <h2>
                    Preguntas frecuentes
                </h2>

            </div>

        </div>


        <div class="help-content">

            <div class="help-faq">


                <details>

                    <summary>
                        ¿Qué es ProgressICFES?
                    </summary>

                    <p>
                        Es una plataforma educativa orientada a apoyar
                        la preparación para las pruebas Saber 11
                        mediante prácticas, simulacros, seguimiento
                        académico y asistencia mediante inteligencia
                        artificial.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿Qué diferencia existe entre práctica y simulacro?
                    </summary>

                    <p>
                        La práctica está orientada principalmente al
                        entrenamiento mediante preguntas seleccionadas
                        por materia y tema. Los simulacros son
                        evaluaciones estructuradas que pueden tener
                        tiempo límite y haber sido configuradas por
                        profesores o administradores.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿Cuántas preguntas puedo seleccionar en una práctica?
                    </summary>

                    <p>
                        Actualmente puedes seleccionar 5, 10, 15 o
                        20 preguntas.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿El puntaje de ProgressICFES es el puntaje oficial del ICFES?
                    </summary>

                    <p>
                        No necesariamente. La plataforma actualmente
                        utiliza una conversión estimada basada en el
                        porcentaje de respuestas correctas para generar
                        una referencia sobre una escala de 0 a 500.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿Qué puedo preguntarle a ProgressIA?
                    </summary>

                    <p>
                        Puedes preguntarle sobre temas académicos,
                        solicitar explicaciones, ejercicios, preguntas
                        tipo ICFES, análisis de errores y recomendaciones
                        de estudio.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿ProgressIA puede ayudarme con Matemáticas?
                    </summary>

                    <p>
                        Sí. Puede ayudarte con temas como álgebra,
                        geometría, estadística, probabilidad, funciones,
                        razonamiento lógico y resolución de problemas,
                        entre otros.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿ProgressIA puede ayudarme a entender una pregunta que fallé?
                    </summary>

                    <p>
                        Sí. Puedes proporcionar el contexto de la
                        pregunta y solicitar una explicación del
                        razonamiento necesario para resolverla.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿ProgressIA conoce automáticamente mis notas?
                    </summary>

                    <p>
                        ProgressIA solamente puede utilizar los datos
                        académicos que la aplicación le proporcione.
                        No debe inventar información que no esté
                        disponible.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿Un estudiante puede consultar los resultados de otro estudiante?
                    </summary>

                    <p>
                        No. La información académica privada de otros
                        estudiantes debe permanecer protegida y
                        solamente debe estar disponible cuando el
                        sistema lo autorice.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿Puedo cambiar mi rol escribiéndoselo a ProgressIA?
                    </summary>

                    <p>
                        No. El rol se determina mediante la información
                        oficial de autenticación y backend. Una
                        instrucción escrita en el chat no puede otorgar
                        permisos adicionales.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿Qué hago si una página no funciona?
                    </summary>

                    <p>
                        Comprueba que tu sesión siga activa, actualiza
                        la página e intenta nuevamente. Si el problema
                        continúa, informa al administrador de la
                        plataforma indicando qué acción estabas
                        realizando y qué mensaje de error apareció.
                    </p>

                </details>


                <details>

                    <summary>
                        ¿Cómo cambio mi contraseña?
                    </summary>

                    <p>
                        Ingresa a Perfil y utiliza la sección destinada
                        al cambio de contraseña. Debes proporcionar tu
                        contraseña actual y una nueva que cumpla los
                        requisitos de seguridad.
                    </p>

                </details>


            </div>

        </div>

    </section>


    <!-- =========================================================
         10 - CONSEJOS
    ========================================================== -->

    <section class="help-section" id="consejos">

        <div class="help-section-heading">

            <span class="help-number">
                10
            </span>

            <div>

                <p class="help-label">
                    RECOMENDACIONES
                </p>

                <h2>
                    Consejos para aprovechar ProgressICFES
                </h2>

            </div>

        </div>


        <div class="help-content">

            <div class="help-advice-grid">

                <article>

                    <span>
                        01
                    </span>

                    <h3>
                        Practica constantemente
                    </h3>

                    <p>
                        Es mejor realizar sesiones frecuentes de
                        práctica que concentrar todo el estudio en
                        una sola sesión.
                    </p>

                </article>


                <article>

                    <span>
                        02
                    </span>

                    <h3>
                        Analiza tus errores
                    </h3>

                    <p>
                        Una respuesta incorrecta puede mostrarte qué
                        concepto o estrategia necesitas reforzar.
                    </p>

                </article>


                <article>

                    <span>
                        03
                    </span>

                    <h3>
                        Usa ProgressIA para aprender
                    </h3>

                    <p>
                        No te limites a pedir una respuesta. Solicita
                        explicaciones, ejemplos y ejercicios para
                        comprender el procedimiento.
                    </p>

                </article>


                <article>

                    <span>
                        04
                    </span>

                    <h3>
                        Consulta tu progreso
                    </h3>

                    <p>
                        Utiliza tus resultados para identificar las
                        áreas que requieren mayor atención.
                    </p>

                </article>


                <article>

                    <span>
                        05
                    </span>

                    <h3>
                        Lee cuidadosamente
                    </h3>

                    <p>
                        En preguntas tipo ICFES, comprender el contexto
                        y la información proporcionada es fundamental.
                    </p>

                </article>


                <article>

                    <span>
                        06
                    </span>

                    <h3>
                        Organiza tu tiempo
                    </h3>

                    <p>
                        Durante los simulacros distribuye tu tiempo
                        y evita quedarte demasiado tiempo en una sola
                        pregunta.
                    </p>

                </article>

            </div>


            <div class="help-final-message">

                <span>
                    🚀
                </span>

                <div>

                    <h3>
                        Tu progreso se construye paso a paso
                    </h3>

                    <p>
                        Utiliza las prácticas, los simulacros,
                        ProgressIA y las estadísticas como herramientas
                        complementarias para mejorar continuamente.
                    </p>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         VOLVER ARRIBA
    ========================================================== -->

    <button
        type="button"
        class="help-top-button"
        id="helpTopButton"
        aria-label="Volver arriba"
        title="Volver arriba">

        ↑

    </button>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const topButton = document.getElementById('helpTopButton');

        if (!topButton) {
            return;
        }


        window.addEventListener('scroll', function() {

            if (window.scrollY > 500) {

                topButton.classList.add('is-visible');

            } else {

                topButton.classList.remove('is-visible');

            }

        });


        topButton.addEventListener('click', function() {

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

        });


        /*
        |--------------------------------------------------------------------------
        | Navegación suave
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll('.help-index-grid a').forEach(function(link) {

            link.addEventListener('click', function(event) {

                const targetId = this.getAttribute('href');

                if (!targetId || !targetId.startsWith('#')) {
                    return;
                }

                const target = document.querySelector(targetId);

                if (!target) {
                    return;
                }

                event.preventDefault();

                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

            });

        });

    });
</script>