<?php
session_start();

// Inicializar variables de sesión si es la primera vez
if (!isset($_SESSION['word']) || isset($_POST['startButton'])) {
    $_SESSION['word'] = getRandomWord();
    $_SESSION['numberOfFails'] = 0;
    $_SESSION['numberOfRights'] = 0;
    $_SESSION['usedLetters'] = array();
}

$word = $_SESSION['word'];
$numberOfFails = $_SESSION['numberOfFails'];
$numberOfRights = $_SESSION['numberOfRights'];
$usedLetters = $_SESSION['usedLetters'];

// Lógica del juego
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['letter'])) {
    $clickedLetter = strtoupper($_POST['letter']);

    if (!in_array($clickedLetter, $usedLetters)) {
        $usedLetters[] = $clickedLetter;

        if (!in_array($clickedLetter, str_split($word))) {
            $numberOfFails++;
        } else {
            $numberOfRights++;
        }
    }
}

// Verificar resultado del juego
if ($numberOfFails === 7) {
    $result = "Perdiste, la palabra correcta es $word";
    gameOver();
} elseif ($numberOfRights === strlen($word)) {
    $result = "¡Enhorabuena, has ganado!";
    gameOver();
} else {
    $result = '';
}

// Mostrar palabra con letras adivinadas y guiones bajos
$displayWord = array();
foreach (str_split($word) as $letter) {
    if (in_array($letter, $usedLetters)) {
        $displayWord[] = $letter;
    } else {
        $displayWord[] = '_';
    }
}

function getRandomWord() {
    $words = ['ROJO', 'AMARILLO', 'VERDE', 'NEGRO', 'AZUL', 'ROSA'];
    return $words[array_rand($words)];
}

function gameOver() {
    // Reiniciar variables de sesión
    unset($_SESSION['word']);
    unset($_SESSION['numberOfFails']);
    unset($_SESSION['numberOfRights']);
    unset($_SESSION['usedLetters']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hangman Game</title>
    <link rel="stylesheet" type="text/css" href="./src/assets/styles.css" />
</head>
<body>
    <header>
        <h2 id="result"><?php echo $result; ?></h2>
    </header>
    <main>
        <img id="image" src="src/assets/images/hangman<?php echo $numberOfFails; ?>.png" alt="hangman" />

        <form method="post">
            <div id="textsContainer">
                <div id="wordContainer">
                    <?php foreach ($displayWord as $letter) { ?>
                        <span><?php echo $letter; ?></span>
                    <?php } ?>
                </div>

                <div id="letterButtonsContainer">
                    <?php foreach (range('A', 'Z') as $letter) { ?>
                        <button type="submit" name="letter" value="<?php echo $letter; ?>" <?php echo in_array($letter, $usedLetters) ? 'disabled' : ''; ?>><?php echo $letter; ?></button>
                    <?php } ?>
                </div>
            </div>
        </form>
    </main>

    <footer>
        <form method="post">
            <button type="submit" name="startButton">Play Again</button>
        </form>
    </footer>
</body>
</html>