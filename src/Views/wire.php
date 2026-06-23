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
        <?php $wireName = strtolower($wire['name']); ?>
<?php if ($wireName === 'red'): ?>
<!-- Hello, secret agent... I knew they’d send you—they’re so predictable 🙄... Well, as you can see, I tend to go a bit overboard, but that’s what they say: "When in doubt, use a power of two." -->
<?php elseif ($wireName === 'orange'): ?>
<!-- Hi, Secret Agent, orange is my favorite color 🤠. -->
<!-- Is orange your favorite color too??? Wow, what an explosive revelation 🤯. -->
<!-- Well, since you have good taste, here’s a great tip: things always get better with a bit of salt, especially if that salt is a “BOMB” 💥💥💥💥💥💥. -->
<?php endif; ?>
<?= htmlspecialchars($wireContent, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>