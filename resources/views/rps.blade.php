<html class="rps">
<head>
    <title>Rock Paper Scissors</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<script>
    score = 0;
</script>
<h1>Rock Paper Scissors</h1>
<div class="rps-container">
    <button id="rock" class="rps-button">🗿</button>
    <button id="paper" class="rps-button">🧻</button>
    <button id="scissors" class="rps-button">✂️</button>
</div>
<div id="result" class="rps-result"></div>
<script>
    function sendScore(points) {
        fetch('{{ route("rps.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ score: points })
        })
        .then(() => window.location = '{{ route("welcome") }}')
        .catch(error => console.error('Error:', error));
    }

    const choices = ['rock', 'paper', 'scissors'];
    const buttons = document.querySelectorAll('.rps-button');
    const resultDiv = document.getElementById('result');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const userChoice = button.id;
            const computerChoice = choices[Math.floor(Math.random() * choices.length)];
            let result = '';

            if (userChoice === computerChoice) {
                result = "It's a tie!";
            } else if (
                (userChoice === 'rock' && computerChoice === 'scissors') ||
                (userChoice === 'paper' && computerChoice === 'rock') ||
                (userChoice === 'scissors' && computerChoice === 'paper')
            ) {
                result = "You win!";
                score += 1;
            } else {
                result = "Computer wins!";
                // score = 0;
            }

            resultDiv.textContent = `You chose ${userChoice}, computer chose ${computerChoice}. ${result}`;
        });
    });
</script>
<div class="toGames">
    <button onclick="sendScore(score)"> Terug naar welkom</button>
</div>

</body>