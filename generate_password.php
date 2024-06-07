<?php
$hashed_password = password_hash('1234567890', PASSWORD_BCRYPT);
echo $hashed_password;
?>
