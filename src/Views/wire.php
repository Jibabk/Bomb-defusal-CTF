<?php $wireName = strtolower($wire['name']); ?>
<?php if ($wireName === 'red'): ?>
<!-- Hello, secret agent... -->
<!-- I knew they’d send you—they’re so predictable 🙄... -->
<!-- Well, as you can see, I tend to go a bit overboard, but that’s what they say: "When in doubt, use a power of two." -->
<?php elseif ($wireName === 'orange'): ?>
<!-- Hi, Secret Agent, orange is my favorite color 🤠. -->
<!-- Is orange your favorite color too??? Wow, what an explosive revelation 🤯. -->
<!-- Well, since you have good taste, here’s a great tip: things always get better with a bit of salt, especially if that salt is a “BOMB” 💥💥💥💥💥💥. -->
<?php elseif ($wireName === 'yellow'): ?>
<!-- Hi, Secret Agent 🥸. -->
<!-- I was thinking—since we have so much in common—maybe we’d get along really well as friends 🤔… -->
<!-- Well, as my newest friend, I want to share an amazing moment with you: watch one of the best free-kick takers in action ⚽. -->
<!-- Pay close attention to how this audio is simply mind-blowing 🥶💣. -->
<?php endif; ?>

<?php if ($wireName === 'yellow'): ?>
<style>
html,
body {
    background: #000;
    margin: 0;
    padding: 0;
}

body {
    align-items: center;
    display: flex;
    justify-content: center;
    min-height: 100vh;
}

video {
    display: block;
}
</style>
<video src="Content/challenge.mkv" controls></video>
<?php else: ?>
<?= htmlspecialchars($wireContent, ENT_QUOTES, 'UTF-8') ?>
<?php endif; ?>
