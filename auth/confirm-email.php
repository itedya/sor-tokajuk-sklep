<?php

require_once "../frontend-tooling/autoload.php";
require_once "../backend-tooling/autoload.php";
loadFrontendTooling();
loadBackendTooling();

$params = [];
parse_str($_SERVER['QUERY_STRING'], $params);


if (isset($params["hash"])) {
    $conn = getDatabaseConnection();

    $hash = $params["hash"];
    $stmt = $conn->prepare("SELECT users.id FROM email_verification_attempts INNER JOIN users ON users.id = email_verification_attempts.user_id WHERE email_verification_attempts.hash = ?;");
    $stmt->bind_param("s", $hash);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_row();

    $stmt->close();

    if ($row === null) {
        header("Location: ../index.php");
        die();
    }

    $stmt = $conn->prepare("DELETE FROM email_verification_attempts WHERE hash = ?;");
    $stmt->bind_param("s", $hash);
    $stmt->execute();

    $stmt->close();

    $stmt = $conn->prepare("UPDATE users SET is_verified = true WHERE id = ?;");
    $stmt->bind_param("i", $row[0]);
    $stmt->execute();

    $stmt->close();
} else {
    header("Location: ../index.php");
    die();
}

$body = <<<HTML
<div class="flex flex-col justify-center items-center p-4 gap-3">
    <h1 class="text-3xl text-zinc-300">Twój email został zweryfikowany</h1>
    <a class="text-xl text-blue-200" href="../index.php">Powrót do strony głównej</a>
</div>
HTML;

echo (new Layout($body))->render();