document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("chatForm");
    const textarea = document.getElementById("mensaje");
    const chat = document.getElementById("chatMessages");
    const typing = document.getElementById("typingIndicator");
    const nuevoChat = document.getElementById("nuevoChat");

    if (!form || !textarea || !chat) {
        console.error("ProgressIA: No se encontraron los elementos del chat.");
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Conversación actual
    |--------------------------------------------------------------------------
    */

    let conversacionId = null;


    /*
    |--------------------------------------------------------------------------
    | Escapar HTML
    |--------------------------------------------------------------------------
    */

    function escaparHTML(texto) {

        const div = document.createElement("div");

        div.textContent = texto;

        return div.innerHTML;
    }


    /*
    |--------------------------------------------------------------------------
    | Scroll automático
    |--------------------------------------------------------------------------
    */

    function bajarChat() {

        chat.scrollTop = chat.scrollHeight;
    }


    /*
    |--------------------------------------------------------------------------
    | Mensaje del usuario
    |--------------------------------------------------------------------------
    */

    function agregarMensajeUsuario(texto) {

        chat.insertAdjacentHTML("beforeend", `
            <div class="user-message">

                <div class="message">

                    <strong>Tú</strong>

                    <p>
                        ${escaparHTML(texto).replace(/\n/g, "<br>")}
                    </p>

                </div>

                <div class="avatar">
                    👤
                </div>

            </div>
        `);

        bajarChat();
    }


    /*
    |--------------------------------------------------------------------------
    | Mensaje de ProgressIA
    |--------------------------------------------------------------------------
    */

    function agregarMensajeIA(texto) {

        chat.insertAdjacentHTML("beforeend", `
            <div class="ia-message">

                <div class="avatar">
                    🤖
                </div>

                <div class="message">

                    <strong>ProgressIA</strong>

                    <p>
                        ${escaparHTML(texto).replace(/\n/g, "<br>")}
                    </p>

                </div>

            </div>
        `);

        bajarChat();
    }


    /*
    |--------------------------------------------------------------------------
    | Mensaje de error
    |--------------------------------------------------------------------------
    */

    function mostrarError(texto) {

        chat.insertAdjacentHTML("beforeend", `
            <div class="ia-message">

                <div class="avatar">
                    ⚠️
                </div>

                <div class="message">

                    <strong>ProgressIA</strong>

                    <p>
                        ${escaparHTML(texto).replace(/\n/g, "<br>")}
                    </p>

                </div>

            </div>
        `);

        bajarChat();
    }


    /*
    |--------------------------------------------------------------------------
    | ENVIAR MENSAJE
    |--------------------------------------------------------------------------
    */

    form.addEventListener("submit", async (event) => {

        event.preventDefault();

        const mensaje = textarea.value.trim();

        if (mensaje === "") {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Mostrar mensaje inmediatamente
        |--------------------------------------------------------------------------
        */

        agregarMensajeUsuario(mensaje);


        /*
        |--------------------------------------------------------------------------
        | Limpiar textarea
        |--------------------------------------------------------------------------
        */

        textarea.value = "";
        textarea.style.height = "55px";


        /*
        |--------------------------------------------------------------------------
        | Mostrar indicador
        |--------------------------------------------------------------------------
        */

        if (typing) {
            typing.style.display = "block";
        }


        /*
        |--------------------------------------------------------------------------
        | Desactivar botón
        |--------------------------------------------------------------------------
        */

        const botonEnviar = document.getElementById("btnEnviar");

        if (botonEnviar) {
            botonEnviar.disabled = true;
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | Crear datos
            |--------------------------------------------------------------------------
            */

            const datos = new FormData();

            datos.append("mensaje", mensaje);


            /*
            |--------------------------------------------------------------------------
            | Enviar ID de conversación si existe
            |--------------------------------------------------------------------------
            */

            if (conversacionId !== null) {

                datos.append(
                    "conversacion_id",
                    String(conversacionId)
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CONEXIÓN CON CHATCONTROLLER
            |--------------------------------------------------------------------------
            */

            console.log(
                "ProgressIA: enviando mensaje..."
            );

            console.log(
                "Conversación:",
                conversacionId
            );


            const respuesta = await fetch(
                (window.baseUrl || "/") + "chat/enviar",
                {
                    method: "POST",
                    body: datos
                }
            );


            /*
            |--------------------------------------------------------------------------
            | OBTENER RESPUESTA REAL DEL SERVIDOR
            |--------------------------------------------------------------------------
            */

            const textoRespuesta = await respuesta.text();


            console.log(
                "STATUS HTTP:",
                respuesta.status
            );

            console.log(
                "RESPUESTA REAL DEL SERVIDOR:",
                textoRespuesta
            );


            /*
            |--------------------------------------------------------------------------
            | Verificar HTTP
            |--------------------------------------------------------------------------
            */

            if (!respuesta.ok) {

                throw new Error(
                    `Error HTTP ${respuesta.status}: ${textoRespuesta}`
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Convertir respuesta a JSON
            |--------------------------------------------------------------------------
            */

            let json;

            try {

                json = JSON.parse(textoRespuesta);

            } catch (error) {

                throw new Error(
                    "El servidor no devolvió JSON válido.\n\n" +
                    textoRespuesta
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Ocultar indicador
            |--------------------------------------------------------------------------
            */

            if (typing) {
                typing.style.display = "none";
            }


            /*
            |--------------------------------------------------------------------------
            | Activar botón
            |--------------------------------------------------------------------------
            */

            if (botonEnviar) {
                botonEnviar.disabled = false;
            }


            /*
            |--------------------------------------------------------------------------
            | RESPUESTA CORRECTA
            |--------------------------------------------------------------------------
            */

            if (json.success) {

                console.log(
                    "ProgressIA respondió correctamente:",
                    json
                );


                /*
                |--------------------------------------------------------------------------
                | Guardar ID de conversación
                |--------------------------------------------------------------------------
                */

                if (json.conversacion_id) {

                    conversacionId =
                        Number(json.conversacion_id);

                }


                /*
                |--------------------------------------------------------------------------
                | Mostrar respuesta de ProgressIA
                |--------------------------------------------------------------------------
                */

                agregarMensajeIA(
                    json.respuesta
                );


            } else {

                /*
                |--------------------------------------------------------------------------
                | Error enviado por PHP
                |--------------------------------------------------------------------------
                */

                const mensajeError =
                    json.message ??
                    "ProgressIA no pudo responder.";

                console.error(
                    "ERROR ENVIADO POR PHP:",
                    mensajeError
                );

                mostrarError(
                    "Error: " + mensajeError
                );
            }


        } catch (error) {

            /*
            |--------------------------------------------------------------------------
            | ERROR REAL
            |--------------------------------------------------------------------------
            */

            console.error(
                "======================================"
            );

            console.error(
                "ERROR COMPLETO DE PROGRESSIA:"
            );

            console.error(
                error
            );

            console.error(
                "======================================"
            );


            /*
            |--------------------------------------------------------------------------
            | Ocultar indicador
            |--------------------------------------------------------------------------
            */

            if (typing) {
                typing.style.display = "none";
            }


            /*
            |--------------------------------------------------------------------------
            | Activar botón
            |--------------------------------------------------------------------------
            */

            if (botonEnviar) {
                botonEnviar.disabled = false;
            }


            /*
            |--------------------------------------------------------------------------
            | Mostrar error REAL en pantalla
            |--------------------------------------------------------------------------
            */

            mostrarError(
                "ERROR: " +
                (error.message ??
                    "Error desconocido.")
            );
        }

    });


    /*
    |--------------------------------------------------------------------------
    | Ajustar altura del textarea
    |--------------------------------------------------------------------------
    */

    textarea.addEventListener("input", function () {

        this.style.height = "55px";

        this.style.height =
            Math.min(
                this.scrollHeight,
                180
            ) + "px";

    });


    /*
    |--------------------------------------------------------------------------
    | NUEVA CONVERSACIÓN
    |--------------------------------------------------------------------------
    */

    if (nuevoChat) {

        nuevoChat.addEventListener("click", () => {

            /*
            |--------------------------------------------------------------------------
            | Eliminar ID anterior
            |--------------------------------------------------------------------------
            */

            conversacionId = null;


            /*
            |--------------------------------------------------------------------------
            | Reiniciar chat visual
            |--------------------------------------------------------------------------
            */

            chat.innerHTML = `

                <div class="ia-message">

                    <div class="avatar">
                        🤖
                    </div>

                    <div class="message">

                        <strong>
                            ProgressIA
                        </strong>

                        <p>
                            ¡Nueva conversación iniciada!
                            ¿Sobre qué tema del ICFES
                            deseas aprender hoy?
                        </p>

                    </div>

                </div>

            `;


            textarea.focus();

            bajarChat();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | VER Y ELIMINAR CONVERSACIONES DEL HISTORIAL
    |--------------------------------------------------------------------------
    */

    const historyList = document.getElementById("historyList");

    function renderizarConversacion(mensajes) {

        chat.innerHTML = "";

        mensajes.forEach((mensaje) => {

            if (mensaje.rol === "user") {
                agregarMensajeUsuario(mensaje.mensaje);
            } else {
                agregarMensajeIA(mensaje.mensaje);
            }

        });

        bajarChat();
    }

    if (historyList) {

        historyList.addEventListener("click", async (event) => {

            const botonCargar = event.target.closest(".history-item");
            const botonEliminar = event.target.closest(".history-delete");

            /*
            |--------------------------------------------------------------------------
            | Ver una conversación en detalle
            |--------------------------------------------------------------------------
            */

            if (botonCargar) {

                const idConversacion = botonCargar.getAttribute("data-conversacion-id");

                if (!idConversacion) {
                    return;
                }

                try {

                    const datos = new FormData();
                    datos.append("conversacion_id", idConversacion);

                    const respuesta = await fetch(
                        (window.baseUrl || "/") + "chat/cargar",
                        {
                            method: "POST",
                            body: datos
                        }
                    );

                    const json = await respuesta.json();

                    if (!json.success) {
                        mostrarError(json.message ?? "No se pudo cargar la conversación.");
                        return;
                    }

                    conversacionId = Number(json.conversacion_id);

                    renderizarConversacion(json.mensajes);

                    historyList.querySelectorAll(".history-item").forEach((item) => {
                        item.classList.remove("is-active");
                    });

                    botonCargar.classList.add("is-active");

                } catch (error) {

                    console.error("ProgressIA: error al cargar la conversación:", error);

                    mostrarError("No se pudo cargar la conversación.");
                }

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Eliminar una conversación
            |--------------------------------------------------------------------------
            */

            if (botonEliminar) {

                const idConversacion = botonEliminar.getAttribute("data-conversacion-id");

                if (!idConversacion) {
                    return;
                }

                const confirmado = confirm(
                    "¿Eliminar esta conversación? Esta acción no se puede deshacer."
                );

                if (!confirmado) {
                    return;
                }

                try {

                    const datos = new FormData();
                    datos.append("conversacion_id", idConversacion);

                    const respuesta = await fetch(
                        (window.baseUrl || "/") + "chat/eliminar",
                        {
                            method: "POST",
                            body: datos
                        }
                    );

                    const json = await respuesta.json();

                    if (!json.success) {
                        mostrarError(json.message ?? "No se pudo eliminar la conversación.");
                        return;
                    }

                    const fila = botonEliminar.closest(".history-row");

                    if (fila) {
                        fila.remove();
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Si era la conversación activa, reiniciar el chat
                    |--------------------------------------------------------------------------
                    */

                    if (conversacionId !== null && String(conversacionId) === idConversacion) {

                        conversacionId = null;

                        if (nuevoChat) {
                            nuevoChat.click();
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Mostrar "sin conversaciones" si ya no queda ninguna
                    |--------------------------------------------------------------------------
                    */

                    if (!historyList.querySelector(".history-row")) {

                        historyList.insertAdjacentHTML("afterend", `
                            <div class="history-empty" id="historyEmpty">
                                Aún no tienes conversaciones.
                            </div>
                        `);

                        historyList.remove();
                    }

                } catch (error) {

                    console.error("ProgressIA: error al eliminar la conversación:", error);

                    mostrarError("No se pudo eliminar la conversación.");
                }
            }

        });

    }

});