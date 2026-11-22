const puzzle = document.getElementById("puzzle");
const gameToken = puzzle.dataset.gameToken;
const board = document.getElementById("board");
const movesElement = document.getElementById("moves");
const restartButton = document.getElementById("restart");
const message = document.getElementById("message");

let tiles = [1, 2, 3, 4, 5, 6, 7, 8, 0];
let moves = 0;

function render() {
    board.innerHTML = "";

    tiles.forEach((tile, index) => {
        const button = document.createElement("button");

        button.textContent = tile === 0 ? "" : tile;
        button.classList.add("tile");

        if (tile === 0) {
            button.classList.add("empty");
        }

        button.addEventListener("click", () => moveTile(index));

        board.appendChild(button);
    });

    movesElement.textContent = moves;
}

function moveTile(index) {
    const emptyIndex = tiles.indexOf(0);

    const row = Math.floor(index / 3);
    const col = index % 3;

    const emptyRow = Math.floor(emptyIndex / 3);
    const emptyCol = emptyIndex % 3;

    const distance = Math.abs(row - emptyRow) + Math.abs(col - emptyCol);

    if (distance !== 1) {
        return;
    }

    [tiles[index], tiles[emptyIndex]] = [tiles[emptyIndex], tiles[index]];

    moves++;

    render();
    checkWin();
}

async function checkWin() {
    const solved = [1, 2, 3, 4, 5, 6, 7, 8, 0];

    if (!tiles.every((tile, index) => tile === solved[index])) {
        return;
    }

    message.textContent = `Bravo ! Puzzle terminé en ${moves} coups.`;
    message.classList.add("success");

    try {
        const response = await fetch("/game/puzzle/score", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                moves: moves,
                gameToken: gameToken
            })
        });

        const data = await response.json();

        if (!data.success) {
            console.error(data.message);
        }
    } catch (error) {
        console.error(
            "Erreur lors de l'enregistrement du score :",
            error
        );
    }
}

function shuffle() {
    tiles = [1, 2, 3, 4, 5, 6, 7, 8, 0];
    moves = 0;
    message.textContent = "";

    for (let i = 0; i < 100; i++) {
        const emptyIndex = tiles.indexOf(0);

        const possibleMoves = [];

        const row = Math.floor(emptyIndex / 3);
        const col = emptyIndex % 3;

        if (row > 0) {
            possibleMoves.push(emptyIndex - 3);
        }

        if (row < 2) {
            possibleMoves.push(emptyIndex + 3);
        }

        if (col > 0) {
            possibleMoves.push(emptyIndex - 1);
        }

        if (col < 2) {
            possibleMoves.push(emptyIndex + 1);
        }

        const randomIndex =
            possibleMoves[Math.floor(Math.random() * possibleMoves.length)];

        [tiles[randomIndex], tiles[emptyIndex]] =
            [tiles[emptyIndex], tiles[randomIndex]];
    }

    render();
}

restartButton.addEventListener("click", shuffle);

shuffle();