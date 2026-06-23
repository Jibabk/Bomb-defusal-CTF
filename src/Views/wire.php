<?php $wireName = strtolower($wire['name']); ?>
<?php if ($wireName === 'red'): ?>
<!-- Hello, secret agent... I knew they’d send you—they’re so predictable 🙄... Well, as you can see, I tend to go a bit overboard, but that’s what they say: "When in doubt, use a power of two." -->
<?php elseif ($wireName === 'orange'): ?>
<!-- Hi, Secret Agent, orange is my favorite color 🤠. -->
<!-- Is orange your favorite color too??? Wow, what an explosive revelation 🤯. -->
<!-- Well, since you have good taste, here’s a great tip: things always get better with a bit of salt, especially if that salt is a “BOMB” 💥💥💥💥💥💥. -->
<?php endif; ?>
<?= htmlspecialchars($wireContent, ENT_QUOTES, 'UTF-8') ?>
