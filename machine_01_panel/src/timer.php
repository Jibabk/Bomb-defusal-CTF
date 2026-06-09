<?php
// O PHP renderiza a estrutura inicial e o JavaScript assume a contagem em tempo real
?>
<div id="timer-display" class="text-6xl font-bold tracking-widest text-red-600 mb-4" style="text-shadow: 0 0 10px red;">
    15:00
</div>
<p id="timer-status" class="text-lg text-yellow-500 animate-pulse">AWAITING OVERRIDE KEY...</p>

<script>
    // Configura o tempo inicial da bomba em segundos (15 minutos = 900 segundos)
    let timeLeft = 15 * 60;
    
    // Captura os elementos HTML que vamos manipular
    const timerElement = document.getElementById('timer-display');
    const statusElement = document.getElementById('timer-status');

    // Cria um intervalo que roda a cada 1000 milissegundos (1 segundo)
    const countdown = setInterval(() => {
        if (timeLeft <= 0) {
            // O tempo acabou! Para o cronômetro e muda o visual para "detonado"
            clearInterval(countdown);
            timerElement.innerText = "00:00";
            timerElement.classList.replace('text-red-600', 'text-gray-700');
            timerElement.classList.remove('animate-ping');
            
            statusElement.innerText = "SYSTEM DETONATED. CONNECTION LOST.";
            statusElement.classList.replace('text-yellow-500', 'text-red-700');
            statusElement.classList.remove('animate-pulse');
            return;
        }

        // Subtrai um segundo
        timeLeft--;

        // Calcula os minutos e segundos restantes
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;

        // Formata os números para sempre terem dois dígitos (ex: "09:05" em vez de "9:5")
        const formattedMinutes = String(minutes).padStart(2, '0');
        const formattedSeconds = String(seconds).padStart(2, '0');

        // Atualiza o cronômetro na tela
        timerElement.innerText = `${formattedMinutes}:${formattedSeconds}`;
        
        // Efeito extra: Quando faltarem 30 segundos, o cronômetro começa a piscar freneticamente
        if (timeLeft === 30) {
            timerElement.classList.add('animate-ping');
            statusElement.innerText = "CRITICAL WARNING: DETONATION IMMINENT!";
            statusElement.classList.replace('text-yellow-500', 'text-red-500');
        }
    }, 1000);
</script>