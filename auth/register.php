<?php
require_once "../frontend-tooling/autoload.php";
loadFrontendTooling("..");


$errors = array();

function postMethod() 
{

    if (!isset($_POST['email'])) ValidationErrorFacade::add('email', 'Email jest wymagany!');
    if (!isset($_POST['password'])) ValidationErrorFacade::add('password', 'Hasło jest wymagane!');


    register($_POST['email'], $_POST['password']);
}

function register($email, $password, $repeat_password) 
{
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    if (empty($email) OR empty($password) OR empty($passwordRepeat)) {
        array_push($errors,"All fields are required");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not correct");
    }
    if (strlen($password)<8) {
        array_push($errors, "Password must be at least 8 characters long");
    }
    if ($password!==$repeat_password) {
        array_push($errors, "Password does not match");
    }

        $conn = require "../database.php";

        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $rowCount = mysqli_num_rows($result);
            if ($rowCount>0) {
                array_push($errors, "User with this email already exists");
            }
            var_dump($errors);
            if (count($errors)>0) {
                foreach ($errors as $error) {
                    echo $error;
                }

            }else{
                
                $sql = "INSERT INTO users(email, password) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt,"ss", $email, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "Registered successfully";
                }else{
                    die("Error");
                }
            }
        
        session_start();

        $_SESSION['user_id'] = $id;
        header('Location: ../index.php');

}



$body = <<<HTML
<div class="flex justify-center items-center p-4">
<form method="POST" action="/auth/register.php" class="w-full max-w-xl p-4 flex flex-col gap-8 rounded-xl">
    <h1 class="text-4xl font-bold text-center text-neutral-300">Rejestracja</h1>

    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
            <label for="email" class="text-lg text-neutral-300 font-semibold mx-2">Email</label>
            <input type="email" name="email" id="email"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"/>
        </div>

        <div class="flex flex-col gap-1">
            <label for="password" class="text-lg text-neutral-300 font-semibold mx-2">Hasło</label>
            <input type="password" name="password" id="password"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"/>
        </div>

        <div class="flex flex-col gap-1">
            <label for="password" class="text-lg text-neutral-300 font-semibold mx-2">Powtórz hasło</label>
            <input type="password" name="repeat_password" id="repeat_password"
                   class="p-4 bg-neutral-800 rounded-xl border-4 border-transparent outline-none focus:outline-none text-lg text-neutral-300 focus:border-neutral-700 duration-300"/>
        </div>
    </div>

    <div class="flex justify-end">
        <button class="px-8 py-2 bg-blue-600 text-neutral-200 font-semibold rounded-lg">Zarejestruj się</button>
    </div>
</form>
</div>
HTML;

echo (new Layout($body))->render();
?>
