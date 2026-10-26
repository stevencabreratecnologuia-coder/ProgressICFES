<?php

declare(strict_types=1);

$conversaciones = $conversaciones ?? [];

?>

<div class="page-content">

    <div class="progressia-page">

        <!-- =====================================================
             ENCABEZADO
        ====================================================== -->

        <div class="progressia-header">

            <div class="progressia-logo">
                🤖
            </div>

            <div>

                <p class="eyebrow">
                    TU TUTOR INTELIGENTE
                </p>

                <h1>
                    ProgressIA
                </h1>

                <p>
                    <?= !empty($esProfesor)
                        ? 'Tu asistente inteligente para planificar, analizar y crear contenido educativo.'
                        : 'Tu tutor inteligente para prepararte para las Pruebas Saber 11.' ?>
                </p>

            </div>

        </div>


        <!-- =====================================================
             CONTENEDOR PRINCIPAL
        ====================================================== -->

        <div class="progressia-chat">


            <!-- =================================================
                 HISTORIAL DE CONVERSACIONES
            ================================================== -->

            <aside class="chat-sidebar">

                <button
                    class="new-chat-btn"
                    id="nuevoChat"
                    type="button">

                    ＋ Nueva conversación

                </button>


                <div class="chat-history">

                    <div class="history-title">
                        Conversaciones
                    </div>


                    <?php if (empty($conversaciones)): ?>

                        <div
                            class="history-empty"
                            id="historyEmpty">

                            Aún no tienes conversaciones.

                        </div>

                    <?php else: ?>

                        <div
                            class="history-list"
                            id="historyList">

                            <?php foreach ($conversaciones as $conversacion): ?>

                                <div
                                    class="history-row"
                                    data-conversacion-id="<?= (int) $conversacion['id'] ?>">

                                    <button
                                        type="button"
                                        class="history-item"
                                        data-conversacion-id="<?= (int) $conversacion['id'] ?>">

                                        <span
                                            class="history-icon"
                                            aria-hidden="true">

                                            💬

                                        </span>

                                        <span class="history-text">

                                            <?= htmlspecialchars(
                                                (string) $conversacion['titulo'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </span>

                                    </button>

                                    <button
                                        type="button"
                                        class="history-delete"
                                        data-conversacion-id="<?= (int) $conversacion['id'] ?>"
                                        title="Eliminar conversación"
                                        aria-label="Eliminar conversación">

                                        🗑

                                    </button>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

            </aside>


            <!-- =================================================
                 CHAT PRINCIPAL
            ================================================== -->

            <section class="chat-main">


                <div
                    class="chat-messages"
                    id="chatMessages">


                    <div class="ia-message">

                        <div class="avatar">
                            🤖
                        </div>


                        <div class="message">

                            <strong>
                                ProgressIA
                            </strong>


                            <p>

                                ¡Hola!

                                <?= !empty($esProfesor)
                                    ? 'Soy ProgressIA, tu asistente docente dentro de ProgressICFES. Puedo analizar el rendimiento de tus estudiantes, ayudarte a interpretar sus dificultades y apoyarte en la creación de prácticas y simulacros.'
                                    : 'Soy ProgressIA, tu tutor inteligente para el ICFES. Puedo ayudarte a comprender los temas, analizar tus errores y enseñarte a resolver los ejercicios paso a paso.' ?>

                            </p>

                        </div>

                    </div>


                </div>


                <!-- =================================================
                     INDICADOR
                ================================================== -->

                <div
                    class="typing"
                    id="typingIndicator"
                    style="display: none;">

                    🤖 ProgressIA está escribiendo...

                </div>


                <!-- =================================================
                     ENTRADA DEL MENSAJE
                ================================================== -->

                <form
                    id="chatForm"
                    class="chat-input">

                    <textarea
                        id="mensaje"
                        name="mensaje"
                        placeholder="Escribe tu pregunta..."
                        rows="1"
                        autocomplete="off"
                        required></textarea>


                    <button
                        type="submit"
                        id="btnEnviar"
                        aria-label="Enviar mensaje">

                        ➤

                    </button>

                </form>


            </section>

        </div>

    </div>



<?php if (!empty($esProfesor)): ?>
    <div class="progressia-docente-integrado">
        <?php
        $error = (string) ($errorProfesorIA ?? '');
        $exito = (string) ($exitoProfesorIA ?? '');
        include __DIR__ . '/../profesor/ia.php';
        ?>
    </div>
<?php endif; ?>
</div>


<!-- =========================================================
     CSS DE PROGRESSIA
========================================================= -->

<link
    rel="stylesheet"
    href="<?= htmlspecialchars(
                BASE_URL . 'css/chat.css',
                ENT_QUOTES,
                'UTF-8'
            ) ?>?v=<?= filemtime(BASE_PATH . '/public/css/chat.css') ?>">


<script>
    window.baseUrl = "<?= BASE_URL ?>";
</script>

<!-- =========================================================
     JAVASCRIPT DE PROGRESSIA
========================================================= -->

<script
    src="<?= htmlspecialchars(
                BASE_URL . 'js/chat.js',
                ENT_QUOTES,
                'UTF-8'
            ) ?>?v=<?= filemtime(BASE_PATH . '/public/js/chat.js') ?>">
</script>