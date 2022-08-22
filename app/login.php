<?php include_once('header.php'); include_once('db_connect.php'); include_once('utils.php');?>

<div class="container-sm">
<?php

    if (session_status() === PHP_SESSION_NONE) session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = test_input($_POST["email"]);
        $senha = test_input($_POST["senha"]);

        if (pg_num_rows($res = pg_query_params($db_connect, 'SELECT password FROM users WHERE email = $1', array($email))) == 1) {
            if (password_verify($senha, pg_fetch_result($res,0,0))) {
                $_SESSION["email"] = $email;
                if (isset($_SESSION['return_to'])) {
                    header('Location: '.$_SESSION["return_to"].'');
                    exit();
                } else {
                    header("Location: index.php");
                    exit();
                }
            }
        }
        unset ($_SESSION['email']);
        error_msg('Erro ao efetuar LOGIN!');
    }
?>

    <h1>LOGIN</h1>
    <form action="login.php" method="post" class="form login col-sm-6 col-sm-offset-3">
        <fieldset>
        <div class="form-outline mb-4">
            <label class="form-label" for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-outline mb-4">
            <label class="form-label" for="senha">Password</label>
            <input type="password" name="senha" id="senha" class="form-control" required>
        </div>
        <div class="form-outline mb-4">
            <input type="checkbox" onclick="showPW()"> Mostrar a senha
        </div>
        <button type="submit" class="btn btn-primary btn-block mb-4">LOGAR</button>
        </fieldset>
    </form>
    </div>

    <script>
        function showPW() {
            var x = document.getElementById("senha");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>

<?php include_once('footer.php');?>