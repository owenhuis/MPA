<html class="rps">
<head>
    <title>Rock Paper Scissors</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<h1>Rock Paper Scissors</h1>
<div class="rps-container">
    <button id="rock" class="rps-button">🗿</button>
    <button id="paper" class="rps-button">🧻</button>
    <button id="scissors" class="rps-button">✂️</button>
</div>
<div id="result" class="rps-result"></div>
<script>
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
            } else {
                result = "Computer wins!";
            }

            resultDiv.textContent = `You chose ${userChoice}, computer chose ${computerChoice}. ${result}`;
        });
    });
</script>
<div class="toGames">
    <button onclick="window.location='{{ route('welcome') }}'"> Terug naar welkom</button>
</div>
</body>