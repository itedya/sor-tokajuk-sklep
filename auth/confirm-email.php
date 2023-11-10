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
    <div class="flex flex-col justify-center items-center p-4 gap-8">
        <h1 class="text-4xl font-bold text-center text-neutral-300">Sukces!</h1>
        <p class="text-xl text-neutral-200">Twój email został zweryfikowany, teraz możesz się zalogować</p>
        <a class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg disabled:bg-blue-400 duration-300"
           href="<?= config("app.url") . "/auth/login.php" ?>">Powrót do strony logowania</a>
    </div>
<?php });