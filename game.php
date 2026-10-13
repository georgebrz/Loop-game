<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include(__DIR__ . "/db.php");

$scene_id = 1;

if (isset($_GET['scene_id']) && is_numeric($_GET['scene_id'])) {
    $scene_id = (int) $_GET['scene_id'];
}

/* Stores memory */
if ($scene_id == 4) {
    $_SESSION['knows_code'] = true;
}

/* Get current scene */
$scene_sql = "SELECT scene_id, title, content, is_ending, image_path FROM scenes WHERE scene_id = ?";
$scene_stmt = $conn->prepare($scene_sql);

$scene_stmt->bind_param("i", $scene_id);
$scene_stmt->execute();
$scene_stmt->bind_result($db_scene_id, $db_title, $db_content, $db_is_ending, $db_image_path);

if (!$scene_stmt->fetch()) {
    die("Scene not found.");
}

$scene = [
    'scene_id' => $db_scene_id,
    'title' => $db_title,
    'content' => $db_content,
    'is_ending' => $db_is_ending,
    'image_path' => $db_image_path
];

$scene_stmt->close();

/* Get choices */
$choices = [];

if (!$scene['is_ending']) {
    $choice_sql = "SELECT choice_id, choice_text, next_scene_id FROM choices WHERE scene_id = ?";
    $choice_stmt = $conn->prepare($choice_sql);

    $choice_stmt->bind_param("i", $scene_id);
    $choice_stmt->execute();
    $choice_stmt->bind_result($choice_id, $choice_text, $next_scene_id);

    while ($choice_stmt->fetch()) {

        if (
            $scene['scene_id'] == 7 &&
            $choice_text == 'Enter the number you remember hearing' &&
            !isset($_SESSION['knows_code'])
        ) {
            continue;
        }

        $choices[] = [
            'choice_id' => $choice_id,
            'choice_text' => $choice_text,
            'next_scene_id' => $next_scene_id
        ];
    }

    $choice_stmt->close();
}

/* Async response */
if (isset($_GET['ajax'])) {
    header("Content-Type: application/json");

    $ending_message = "";

    if ($scene['is_ending']) {
        $ending_message = ($scene['scene_id'] == 12)
            ? "You broke the loop."
            : "The loop continues.";
    }

    echo json_encode([
        "scene" => $scene,
        "choices" => $choices,
        "ending_message" => $ending_message
    ]);

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($scene['title']); ?> - Loop: Midnight Reset</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body class="<?php echo $scene['is_ending'] ? 'ending-screen' : ''; ?>">

<div class="container game-container">
    <h1>Loop: Midnight Reset</h1>
<div style="text-align: left; margin-bottom: 10px;">
    <a href="index.php" style="
        padding: 6px 12px;
        font-weight: bold;
        border: 1px solid #c4b5fd;
        border-radius: 6px;
        text-decoration: none;
        color: #c4b5fd;
        font-size: 14px;
    ">
        Home
    </a>
</div>

    <div id="game-area">

        <?php if (!$scene['is_ending']) : ?>

            <div class="scene-box">
                <?php if (!empty($scene['image_path'])) : ?>
                    <div class="scene-image">
                        <img src="<?php echo htmlspecialchars($scene['image_path']); ?>" alt="Scene image">
                    </div>
                <?php endif; ?>

                <h2><?php echo htmlspecialchars($scene['title']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($scene['content'])); ?></p>
            </div>

        <?php else : ?>

            <div class="ending-box">
                <?php if (!empty($scene['image_path'])) : ?>
                    <div class="scene-image">
                        <img src="<?php echo htmlspecialchars($scene['image_path']); ?>" alt="Ending image">
                    </div>
                <?php endif; ?>

                <h2><?php echo htmlspecialchars($scene['title']); ?></h2>

                <p class="ending-text">
                    <?php echo nl2br(htmlspecialchars($scene['content'])); ?>
                </p>

                <p class="ending-message">
                    <?php echo ($scene['scene_id'] == 12) ? "You broke the loop." : "The loop continues."; ?>
                </p>

                <a class="main-button" href="restart.php">Restart Game</a>
            </div>

        <?php endif; ?>

    </div>

    <div class="choices-box" id="choices-area">

        <?php if (!$scene['is_ending']) : ?>

            <?php foreach ($choices as $choice) : ?>
                <a class="choice-button"
                   href="game.php?scene_id=<?php echo $choice['next_scene_id']; ?>"
                   data-scene="<?php echo $choice['next_scene_id']; ?>">
                    <?php echo htmlspecialchars($choice['choice_text']); ?>
                </a>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</div>

<script>
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("choice-button")) {
        e.preventDefault();
        loadScene(e.target.dataset.scene);
    }
});

async function loadScene(sceneId) {
    try {
        const response = await fetch("game.php?scene_id=" + sceneId + "&ajax=1");
        const data = await response.json();
        updateGame(data);
    } catch (error) {
        console.error("Error loading scene:", error);
    }
}

function updateGame(data) {
    document.title = data.scene.title + " - Loop: Midnight Reset";

    if (data.scene.is_ending == 1) {
        showEnding(data);
    } else {
        showScene(data);
    }
}

function showScene(data) {
    document.body.className = "";

    document.getElementById("game-area").innerHTML = `
        <div class="scene-box">
            ${getImage(data.scene.image_path)}
            <h2>${escapeHtml(data.scene.title)}</h2>
            <p>${escapeHtml(data.scene.content).replace(/\n/g, "<br>")}</p>
        </div>
    `;

    const choicesArea = document.getElementById("choices-area");
    choicesArea.innerHTML = "";

    data.choices.forEach(choice => {
        const link = document.createElement("a");
        link.className = "choice-button";
        link.href = "game.php?scene_id=" + choice.next_scene_id;
        link.dataset.scene = choice.next_scene_id;
        link.textContent = choice.choice_text;

        choicesArea.appendChild(link);
    });
}

function showEnding(data) {
    document.body.className = "ending-screen";

    document.getElementById("game-area").innerHTML = `
        <div class="ending-box">
            ${getImage(data.scene.image_path)}
            <h2>${escapeHtml(data.scene.title)}</h2>
            <p class="ending-text">${escapeHtml(data.scene.content).replace(/\n/g, "<br>")}</p>
            <p class="ending-message">${escapeHtml(data.ending_message)}</p>
            <a class="main-button" href="restart.php">Restart Game</a>
        </div>
    `;

    document.getElementById("choices-area").innerHTML = "";
}

function getImage(path) {
    return path ? `<div class="scene-image"><img src="${escapeHtml(path)}"></div>` : "";
}

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}
</script>

</body>
</html>
