<main class="auth-page">
    <section class="auth-card" aria-labelledby="login-title">
        <div class="auth-identity">
            <a class="auth-brand" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>auth/login" aria-label="ProgressICFES">
                <img class="auth-logo auth-logo-light" src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/progressicfes-logo.png" alt="ProgressICFES">
                <img class="auth-logo auth-logo-dark" src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>img/progressicfes-logo-dark.png" alt="ProgressICFES">
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
            <p class="eyebrow">BIENVENIDO DE NUEVO</p>
            <h1 id="login-title">Continúa tu preparación.</h1>
            <p>Ingresa para practicar, realizar simulacros y consultar tu progreso.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success" role="status"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-error" role="alert"><?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form class="auth-form" action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>auth/login" method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <label for="correo">Correo electrónico</label>
            <input id="correo" name="correo" type="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required>

            <label for="password">Contraseña</label>
            <div class="password-field">
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                <button type="button" class="password-toggle" data-toggle-password="password" aria-pressed="false" aria-label="Mostrar contraseña">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" />
                        <circle cx="12" cy="12" r="2.6" />
                    </svg>
                </button>
            </div>

            <button class="primary-button auth-submit" type="submit">Iniciar sesión <span aria-hidden="true">→</span></button>
        </form>

        <p class="auth-switch">¿Aún no tienes una cuenta? <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>auth/registro">Regístrate</a></p>
    </section>
</main>