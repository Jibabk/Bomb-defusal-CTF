<!-- Hi, Secret Agent 🤓. -->
<!-- Well, our incredible friendship ends here 😐. -->
<!-- I know this may be disappointing for you, but from now on I won’t be able to go easy on you, since our goals are diametrically opposed 😔. -->
<!-- Knowing your incredible skills, I created this flawless validation that will prevent you from moving forward. -->
<!-- I wish you bad luck from this point on 😝. -->
<main class="login-shell">
    <section class="login-panel" aria-label="Wire login">
        <?php if ($wireContent !== ''): ?>
            <div class="login-message is-granted">
                <code><?= htmlspecialchars($wireContent, ENT_QUOTES, 'UTF-8') ?></code>
            </div>
        <?php else: ?>
            <form
                class="login-form"
                id="login-form"
                method="post"
                action="index.php?route=wire&amp;id=<?= rawurlencode($wire['id']) ?>"
                autocomplete="off"
            >
                <label for="username">Username</label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    spellcheck="false"
                    autocapitalize="none"
                    autocomplete="off"
                    maxlength="32"
                    required
                >

                <label for="password">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="off"
                    maxlength="32"
                    required
                >

                <p class="filter-warning" id="filter-warning" hidden>
                    Request blocked by filter: forbidden characters detected.
                </p>

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <p class="login-message is-denied">Access denied.</p>
                <?php endif; ?>

                <button type="submit">Login</button>
            </form>
        <?php endif; ?>
    </section>
</main>
