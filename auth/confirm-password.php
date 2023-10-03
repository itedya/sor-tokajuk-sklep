<?php

require_once "../frontend-tooling/autoload.php";
loadFrontendTooling("..");

$params = null;
parse_str($_SERVER['QUERY_STRING'], $params);


if(isset($params["hash"])) {
    $conn = require "../database.php";
    
    $hash = $params["hash"];
    $stmt = $conn->prepare("SELECT users.id FROM email_verification_attempts INNER JOIN users ON users.id = email_verification_attempts.user_id WHERE email_verification_attempts.hash = ?;");
    $stmt->bind_param("s", $hash);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_row();
    
    $stmt->close();
   
    if($row === null) {
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

    var_dump($row);
    
}



?>