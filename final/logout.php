<?php
    session_start();
    unset($_SESSION['username']);
    echo "<body><script> location.href='./login.php'; </script></body>";
?>