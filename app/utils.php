<?php date_default_timezone_set("America/Sao_Paulo");
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function sucess_msg($string) { ?>
    <div class="p-2 mb-1 bg-success text-white border border-dark d-flex justify-content-center">
        <?php echo '<h4>'.$string.'</h4>';?>
    </div>
<?php
}

function error_msg($string) { ?>
    <div class="p-2 mb-1 bg-danger text-white border border-dark d-flex justify-content-center">
        <?php echo '<h4>'.$string.'</h4>';?>
    </div>
<?php
}

function info_msg($string) { ?>
    <div class="p-2 mb-1 bg-info text-white border border-dark d-flex justify-content-center">
        <?php echo '<h4>'.$string.'</h4>';?>
    </div>
<?php
}

function is_logged_in() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['email'])) {
        $_SESSION['return_to'] = $_SERVER["PHP_SELF"];
        header("Location: login.php");
        exit();
    }
}
?>