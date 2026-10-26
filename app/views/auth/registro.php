<main class="auth-page">

    <section class="auth-card auth-card-wide" aria-labelledby="register-title">
        <div class="auth-identity">
            <a
                class="auth-brand"
                href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>auth/login"
                aria-label="ProgressICFES">
                <img
                    class="auth-logo auth-logo-light"
                    src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/progressicfes-logo.png"
                    alt="ProgressICFES">
                <img
                    class="auth-logo auth-logo-dark"
                    src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/progressicfes-logo-dark.png"
                    alt="ProgressICFES">
            </a>

            <a
                class="auth-institution"
                href="https://www.iefranciscodepaulasantander.edu.co/"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Visitar el sitio web de la Institucion Educativa Francisco de Paula Santander">
                <span class="auth-institution-name">
                    <span>Institucion Educativa</span>
                    <strong>Francisco de Paula Santander</strong>
                </span>
                <span class="auth-institution-crest" aria-hidden="true">
                    <img
                        src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/institucion-crest.png"
                        alt="">
                </span>
                <svg class="auth-institution-link-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10 13.5a4 4 0 0 0 5.66 0l2.17-2.17a4 4 0 0 0-5.66-5.66l-1.24 1.24"/>
                    <path d="M14 10.5a4 4 0 0 0-5.66 0l-2.17 2.17a4 4 0 0 0 5.66 5.66l1.24-1.24"/>
                </svg>
            </a>
        </div>

        <div class="auth-intro">

            <p class="eyebrow">CREA TU CUENTA</p>

            <h1 id="register-title">Únete a ProgressICFES.</h1>

            <p>
                Crea una cuenta de estudiante o solicita una cuenta
                de profesor.
            </p>

        </div>

        <?php if (isset($errors['general'])): ?>

            <div class="alert alert-error" role="alert">

                <?= htmlspecialchars(
                    $errors['general'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <form
            class="auth-form"
            action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>auth/registro"
            method="post"
            novalidate>

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">


            <label for="tipo_cuenta">
                Tipo de cuenta
            </label>

            <select
                id="tipo_cuenta"
                name="tipo_cuenta"
                required>

                <option
                    value="estudiante"
                    <?= ($data['tipo_cuenta'] ?? 'estudiante') === 'estudiante'
                        ? 'selected'
                        : '' ?>>
                    Estudiante
                </option>

                <option
                    value="profesor"
                    <?= ($data['tipo_cuenta'] ?? '') === 'profesor'
                        ? 'selected'
                        : '' ?>>
                    Profesor
                </option>

            </select>


            <?php if (isset($errors['tipo_cuenta'])): ?>

                <small class="field-error">

                    <?= htmlspecialchars(
                        $errors['tipo_cuenta'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </small>

            <?php endif; ?>


            <!-- CAMPOS DEL ESTUDIANTE -->

            <div
                id="estudiante-campos"
                style="<?= ($data['tipo_cuenta'] ?? 'estudiante') === 'estudiante'
                            ? ''
                            : 'display: none;' ?>">

                <label for="grado">
                    Grado
                </label>

                <select
                    id="grado"
                    name="grado">

                    <option value="">
                        Selecciona tu grado
                    </option>

                    <option
                        value="6"
                        <?= (string) ($data['grado'] ?? '') === '6'
                            ? 'selected'
                            : '' ?>>
                        6°
                    </option>

                    <option
                        value="7"
                        <?= (string) ($data['grado'] ?? '') === '7'
                            ? 'selected'
                            : '' ?>>
                        7°
                    </option>

                    <option
                        value="8"
                        <?= (string) ($data['grado'] ?? '') === '8'
                            ? 'selected'
                            : '' ?>>
                        8°
                    </option>

                    <option
                        value="9"
                        <?= (string) ($data['grado'] ?? '') === '9'
                            ? 'selected'
                            : '' ?>>
                        9°
                    </option>

                    <option
                        value="10"
                        <?= (string) ($data['grado'] ?? '') === '10'
                            ? 'selected'
                            : '' ?>>
                        10°
                    </option>

                    <option
                        value="11"
                        <?= (string) ($data['grado'] ?? '') === '11'
                            ? 'selected'
                            : '' ?>>
                        11°
                    </option>

                </select>


                <?php if (isset($errors['grado'])): ?>

                    <small class="field-error">

                        <?= htmlspecialchars(
                            $errors['grado'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </small>

                <?php endif; ?>

            </div>


            <!-- CAMPOS DEL PROFESOR -->

            <div
                id="profesor-campos"
                style="<?= ($data['tipo_cuenta'] ?? 'estudiante') === 'profesor'
                            ? ''
                            : 'display: none;' ?>">

                <div class="profesor-registro-info">

                    Al solicitar una cuenta de profesor, un administrador
                    deberá aprobar tu solicitud antes de que puedas iniciar
                    sesión.

                </div>


                <label for="materia_id">
                    Materia que deseas enseñar
                </label>

                <select
                    id="materia_id"
                    name="materia_id">

                    <option value="">
                        Selecciona una materia
                    </option>

                    <?php foreach ($materias as $materia): ?>

                        <option
                            value="<?= (int) $materia['id'] ?>"
                            <?= (int) ($data['materia_id'] ?? 0) === (int) $materia['id']
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


                <?php if (isset($errors['materia_id'])): ?>

                    <small class="field-error">

                        <?= htmlspecialchars(
                            $errors['materia_id'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </small>

                <?php endif; ?>


                <label for="documento_identidad">
                    Número de documento de identidad
                </label>

                <input
                    id="documento_identidad"
                    name="documento_identidad"
                    type="text"
                    value="<?= htmlspecialchars(
                                $data['documento_identidad'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                    autocomplete="off">


                <?php if (isset($errors['documento_identidad'])): ?>

                    <small class="field-error">

                        <?= htmlspecialchars(
                            $errors['documento_identidad'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </small>

                <?php endif; ?>

            </div>


            <!-- NOMBRES Y APELLIDOS -->

            <div class="form-grid">

                <div class="form-field">

                    <label for="nombres">
                        Nombres
                    </label>

                    <input
                        id="nombres"
                        name="nombres"
                        type="text"
                        value="<?= htmlspecialchars(
                                    $data['nombres'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        autocomplete="given-name"
                        required>


                    <?php if (isset($errors['nombres'])): ?>

                        <small class="field-error">

                            <?= htmlspecialchars(
                                $errors['nombres'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </small>

                    <?php endif; ?>

                </div>


                <div class="form-field">

                    <label for="apellidos">
                        Apellidos
                    </label>

                    <input
                        id="apellidos"
                        name="apellidos"
                        type="text"
                        value="<?= htmlspecialchars(
                                    $data['apellidos'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        autocomplete="family-name"
                        required>


                    <?php if (isset($errors['apellidos'])): ?>

                        <small class="field-error">

                            <?= htmlspecialchars(
                                $errors['apellidos'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </small>

                    <?php endif; ?>

                </div>

            </div>


            <!-- CORREO -->

            <label for="correo">
                Correo electrónico
            </label>

            <input
                id="correo"
                name="correo"
                type="email"
                value="<?= htmlspecialchars(
                            $data['correo'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                autocomplete="email"
                required>


            <?php if (isset($errors['correo'])): ?>

                <small class="field-error">

                    <?= htmlspecialchars(
                        $errors['correo'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </small>

            <?php endif; ?>


            <!-- CONTRASEÑA -->

            <label for="password">
                Contraseña
            </label>

            <div class="password-field">

                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    minlength="8"
                    required>

                <button
                    type="button"
                    class="password-toggle"
                    data-toggle-password="password"
                    aria-pressed="false"
                    aria-label="Mostrar contraseña">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" />

                        <circle
                            cx="12"
                            cy="12"
                            r="2.6" />

                    </svg>

                </button>

            </div>


            <?php if (isset($errors['password'])): ?>

                <small class="field-error">

                    <?= htmlspecialchars(
                        $errors['password'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </small>

            <?php endif; ?>


            <!-- CONFIRMAR CONTRASEÑA -->

            <label for="confirmar_password">
                Confirmar contraseña
            </label>

            <div class="password-field">

                <input
                    id="confirmar_password"
                    name="confirmar_password"
                    type="password"
                    autocomplete="new-password"
                    minlength="8"
                    required>

                <button
                    type="button"
                    class="password-toggle"
                    data-toggle-password="confirmar_password"
                    aria-pressed="false"
                    aria-label="Mostrar contraseña">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" />

                        <circle
                            cx="12"
                            cy="12"
                            r="2.6" />

                    </svg>

                </button>

            </div>


            <?php if (isset($errors['confirmar_password'])): ?>

                <small class="field-error">

                    <?= htmlspecialchars(
                        $errors['confirmar_password'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </small>

            <?php endif; ?>


            <!-- BOTÓN -->

            <button
                id="boton-registro"
                class="primary-button auth-submit"
                type="submit">

                Crear cuenta de estudiante

                <span aria-hidden="true">
                    →
                </span>

            </button>

        </form>


        <p class="auth-switch">

            ¿Ya tienes una cuenta?

            <a
                href="<?= htmlspecialchars(
                            $baseUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>auth/login">

                Inicia sesión

            </a>

        </p>

    </section>

</main>


<script>
    const tipoCuenta = document.getElementById(
        'tipo_cuenta'
    );


    const estudianteCampos = document.getElementById(
        'estudiante-campos'
    );


    const profesorCampos = document.getElementById(
        'profesor-campos'
    );


    const materiaId = document.getElementById(
        'materia_id'
    );


    const documentoIdentidad = document.getElementById(
        'documento_identidad'
    );


    const grado = document.getElementById(
        'grado'
    );


    const botonRegistro = document.getElementById(
        'boton-registro'
    );


    function actualizarFormularioRegistro() {

        const esProfesor =
            tipoCuenta.value === 'profesor';


        const esEstudiante =
            tipoCuenta.value === 'estudiante';


        estudianteCampos.style.display =
            esEstudiante ? 'block' : 'none';


        profesorCampos.style.display =
            esProfesor ? 'block' : 'none';


        materiaId.required =
            esProfesor;


        documentoIdentidad.required =
            esProfesor;


        grado.required =
            esEstudiante;


        botonRegistro.innerHTML = esProfesor ?
            'Enviar solicitud de profesor <span aria-hidden="true">→</span>' :
            'Crear cuenta de estudiante <span aria-hidden="true">→</span>';

    }


    tipoCuenta.addEventListener(
        'change',
        actualizarFormularioRegistro
    );


    actualizarFormularioRegistro();
</script>