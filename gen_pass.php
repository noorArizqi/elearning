<?php
$password = 'password'; // ganti dengan password yang ingin Anda gunakan
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hash: " . $hash;
?>