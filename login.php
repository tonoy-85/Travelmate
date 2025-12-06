<?php
session_start();

$email = trim($_POST['email']);
$password = trim($_POST['password']);

$users = file("users.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($users as $u) {

    $u = trim($u);

    if (strpos($u, "|") !== false) {
        list($name, $uEmail, $uPass) = array_map('trim', explode("|", $u));
    } else {
        continue;
    }

    if ($uEmail === $email) {

        // Case 1: hashed password
        if (password_verify($password, $uPass)) {
            $_SESSION['user'] = $name;
            header("Location: index.html");
            exit();
        }

        // Case 2: plain text password (old users)
        if ($password === $uPass) {
            $_SESSION['user'] = $name;
            header("Location: index.html");
            exit();
        }

        // password does not match
        echo "Invalid password!";
        exit();
    }
}

echo "User not found!";
?>
