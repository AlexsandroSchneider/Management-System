<?php
    session_start();
    unset ($_SESSION['email']);
    header("Location: index.php?erro=2");
    exit();
?>