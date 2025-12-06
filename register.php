<?php

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Save into a file (database ছাড়া)
$file = fopen("users.txt", "a");
fwrite($file, "$name | $email | $password\n");
fclose($file);

header("Location: success.html");
exit;
?>
