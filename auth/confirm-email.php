<?php

require_once '../tooling/autoload.php';

gate_redirect_if_logged_in();

if (get_query_param('hash') !== null) {
    $conn = get_db_connection();

    $hash = get_query_param('hash');
    $stmt = $conn->prepare("SELECT users.id FROM email_verification_attempts INNER JOIN users ON users.id = email_verification_attempts.user_id WHERE email_verification_attempts.hash = ?;");
    $stmt->bind_param("s", $hash);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_row();

    $stmt->close();

    if ($row === null) {
        redirect_and_kill("../index.php");
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
    redirect_and_kill("../index.php");
}

echo render_in_layout(function () { ?>
    <div class="flex flex-col justify-center items-center p-4 gap-3">
        <h1 class="text-3xl text-zinc-300">Twój email został zweryfikowany</h1>
        <a class="text-xl text-blue-200" href="../index.php">Powrót do strony głównej</a>
    </div>
<?php });