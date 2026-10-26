document.addEventListener("DOMContentLoaded", () => {
    const ayudas = document.querySelectorAll("[data-progressia-error-help]");

    if (!ayudas.length) {
        return;
    }

    const baseUrl = window.baseUrl || "/";
    const csrfToken = window.progressiaCsrfToken || "";

    const agregarMensaje = (contenedor, tipo, texto) => {
        const item = document.createElement("div");
        item.className = `progressia-error-message ${tipo}`;

        const etiqueta = document.createElement("strong");
        etiqueta.textContent = tipo === "user" ? "Tú" : "ProgressIA";

        const contenido = document.createElement("div");
        contenido.className = "progressia-error-message-text";
        contenido.textContent = texto;

        item.appendChild(etiqueta);
        item.appendChild(contenido);
        contenedor.appendChild(item);
        contenedor.scrollTop = contenedor.scrollHeight;
    };

    const solicitarOrientacion = async (ayuda, mensaje) => {
        const chat = ayuda.querySelector("[data-progressia-chat]");
        const mensajes = ayuda.querySelector("[data-progressia-messages]");
        const input = ayuda.querySelector("[data-progressia-input]");
        const enviar = ayuda.querySelector(".progressia-error-form button[type='submit']");

        const preguntaId = ayuda.dataset.preguntaId || "";
        const origen = ayuda.dataset.origen || "";
        const resultadoId = ayuda.dataset.resultadoId || "";

        if (!preguntaId || !origen) {
            agregarMensaje(mensajes, "error", "No se pudo identificar esta pregunta.");
            return;
        }

        const datos = new FormData();
        datos.append("csrf_token", csrfToken);
        datos.append("pregunta_id", preguntaId);
        datos.append("origen", origen);
        datos.append("mensaje", mensaje);

        if (resultadoId) {
            datos.append("resultado_id", resultadoId);
        }

        if (input) {
            input.disabled = true;
        }

        if (enviar) {
            enviar.disabled = true;
        }

        const cargando = document.createElement("div");
        cargando.className = "progressia-error-message assistant is-loading";
        cargando.textContent = "ProgressIA está analizando tu error...";
        mensajes.appendChild(cargando);
        mensajes.scrollTop = mensajes.scrollHeight;

        try {
            const respuesta = await fetch(
                `${baseUrl}chat/orientar-pregunta`,
                {
                    method: "POST",
                    body: datos,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                }
            );

            const texto = await respuesta.text();
            let json;

            try {
                json = JSON.parse(texto);
            } catch (error) {
                throw new Error("El servidor no devolvió una respuesta válida.");
            }

            cargando.remove();

            if (!respuesta.ok || !json.success) {
                throw new Error(json.message || "ProgressIA no pudo generar la orientación.");
            }

            agregarMensaje(mensajes, "assistant", json.respuesta);
        } catch (error) {
            cargando.remove();
            agregarMensaje(
                mensajes,
                "error",
                error.message || "Ocurrió un error al conectar con ProgressIA."
            );
        } finally {
            if (input) {
                input.disabled = false;
                input.focus();
            }

            if (enviar) {
                enviar.disabled = false;
            }
        }
    };

    ayudas.forEach((ayuda) => {
        const iniciar = ayuda.querySelector("[data-progressia-start]");
        const chat = ayuda.querySelector("[data-progressia-chat]");
        const mensajes = ayuda.querySelector("[data-progressia-messages]");
        const formulario = ayuda.querySelector("[data-progressia-form]");
        const input = ayuda.querySelector("[data-progressia-input]");

        if (!iniciar || !chat || !mensajes || !formulario || !input) {
            return;
        }

        iniciar.addEventListener("click", async () => {
            iniciar.disabled = true;
            chat.hidden = false;
            iniciar.textContent = "Orientación iniciada";

            await solicitarOrientacion(
                ayuda,
                "Ayúdame a entender esta pregunta que respondí mal. No me des directamente la respuesta; guíame paso a paso para que yo pueda encontrarla."
            );
        });

        formulario.addEventListener("submit", async (event) => {
            event.preventDefault();

            const mensaje = input.value.trim();

            if (!mensaje) {
                return;
            }

            agregarMensaje(mensajes, "user", mensaje);
            input.value = "";

            await solicitarOrientacion(ayuda, mensaje);
        });
    });
});
