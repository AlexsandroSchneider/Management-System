<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        $razao = $fantas = $cnpj = $email = $telefone = $endereco = "";
        $erro1 = $erro2 = $erro3 = $erro4 = 1;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (strlen($_POST["corpName"])<5) {
                error_msg("Razão social precisa ter mais que 5 caracteres.");
            } else {
                $razao = test_input($_POST["corpName"]);
                $erro1 = 0;
            }

            if (strlen($_POST["tradeName"])<5) {
                error_msg("Nome fantasia precisa ter mais que 5 caracteres.");
            } else {
                $fantas = test_input($_POST["tradeName"]);
                $erro2 = 0;
            }

            if (strlen($_POST["cnpj"]) != 14) {
                error_msg("CNPJ precisa ter 14 digitos.");
            } else {
                $cnpj = test_input($_POST["cnpj"]);
                $erro3 = 0;
            }

            if (!empty($_POST["email"])) {
                $email = test_input($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    error_msg("Email inválido.");
                } else $erro4 = 0;
            } else $erro4 = 0;

            $telefone = test_input($_POST["phoneNumber"]);
            $endereco = test_input($_POST["address"]);

            if (!$erro1 AND !$erro2 AND !$erro3 AND !$erro4) {
                $exist = pg_query_params($db_connect, 'SELECT * FROM suppliers WHERE cnpj = $1', array($cnpj));
                if (pg_num_rows($exist) > 0) {
                    error_msg("CNPJ já utilizado em outro cadastro.");
                } else {
                    $arr = array($cnpj, $razao, $fantas, $email, $telefone, $endereco, 't');
                    pg_query_params($db_connect, 'INSERT INTO suppliers VALUES (DEFAULT, $1, $2, $3, $4, $5, $6, $7)', $arr);
                    sucess_msg("Cadastro concluído!");
                }
            }
        }
    ?>

    <br><br><h1>CADASTRO DE FORNECEDORES</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="corpName" class="form-label">Razão Social</label>
            <input type="text" class="form-control" name="corpName" value="<?php echo $razao;?>">
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="tradeName" class="form-label">Nome Fantasia</label>
            <input type="text" class="form-control" name="tradeName" value="<?php echo $fantas;?>">
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="cnpj" class="form-label">CNPJ</label>
            <input type="number" min='0' class="form-control" name="cnpj" value="<?php echo $cnpj;?>">
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo $email;?>">
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="phoneNumber" class="form-label">Telefone</label>
            <input type="number" min='0' class="form-control" name="phoneNumber" value="<?php echo $telefone;?>">
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="address" class="form-label">Endereço</label>
            <input type="text" class="form-control" name="address" value="<?php echo $endereco;?>">
        </div>
        <br><button type="submit" class="btn btn-secondary">CADASTRAR</button>
    </form>

<?php include_once('footer.php');?>