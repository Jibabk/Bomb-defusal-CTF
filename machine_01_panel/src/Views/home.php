<main class="home-page">
    <section class="rules-panel" aria-label="Game rules">

        <h1 class="rules-title">Message From HQ</h1>

        <div class="story-text">
            <p>
                Secret Agent, I hope this message finds you well! Your mission today is to disarm a digital bomb that has been planted on one of our servers.
            </p>

            <p>
                We don't know how, who, or why this bomb was planted, but if it isn't disarmed, we believe it has the potential to delete our entire server!
            </p>

            <p>
                We're counting on your skills once again...
            </p>
        </div>

        <h1 class="rules-title">Game Rules</h1>
        <p>
            Here are the rules the Hacker gave us; be careful not to violate them, attempts to violate them may detonate the bomb before time runs out!
        </p>
        <br>
        <ol class="rules-list">
            <li>The bomb contains 8 wires, each representing a cryptanalysis challenge.</li>
            <li>Upon completing each challenge, you will find a 6-digit hexadecimal secret.</li>
            <li>The secret must be entered into the bomb's panel; after typing, click the central red button. This will cut the wire if the secret is correct.</li>
            <li>Be careful, each incorrect attempt will consume one minute of the bomb's time!</li>
            <li>Any form of cheating will detonate the bomb; we will show no mercy.</li>
        </ol>

        <form class="start-form" method="post" action="index.php?route=start">
            <button class="start-button" type="submit">Start Challenge</button>
        </form>
    </section>
</main>
