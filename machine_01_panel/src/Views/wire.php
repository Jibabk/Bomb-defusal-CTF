<?php if ($wire['id'] === 'blue'): ?>
    <div class="challenge-container">
        <p>HAHAHAHAHAHA </p>
        <p>Você nunca irá descobrir a tempo como desarmar essa bomba!!! </p>
        <p>Mas como estou com um bom humor talvez você encontre algo em algum desses, boa sorte tentando todos</p>
        <p>HA HA HA</p>
        
        <a href="/wordlist_bomb.txt" download class="download-link">
            Baixar wordlist_ctf.txt
        </a>
    </div>



<?php else: ?>
    <div class="default-wire-container">
        <?= htmlspecialchars($wire['code'], ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>