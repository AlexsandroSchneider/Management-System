<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        $nome = $apelido = $cpf = $dtNasc = $telefone = $endereco = "";
        $erro1 = $erro2 = 1;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $apelido = test_input($_POST["nickname"]);
            $dtNasc = test_input($_POST["birthDate"]);
            $telefone = test_input($_POST["phoneNumber"]);
            $endereco = test_input($_POST["address"]);

            if (strlen($_POST["fullName"])<5) {
                error_msg("Nome precisa ter mais que 5 caracteres.");
            } else {
                $nome = test_input($_POST["fullName"]);
                $erro1 = 0;
            }

            if (strlen($_POST["cpf"]) != 11) {
                error_msg("CPF precisa ter 11 digitos.");
            } else {
                $cpf = test_input($_POST["cpf"]);
                $erro2 = 0;
            }

            if (!$erro1 AND !$erro2) {
                $exist = pg_query_params($db_connect, 'SELECT * FROM clients WHERE cpf = $1', array($cpf));
                if (pg_num_rows($exist) > 0) {
                    error_msg("CPF já utilizado em outro cadastro.");
                } else {
                    $arr = array($cpf, $nome, $apelido, $dtNasc, $telefone, $endereco, 't');
                    pg_query_params($db_connect, 'INSERT INTO clients VALUES (DEFAULT, $1, $2, $3, $4, $5, $6, $7)', $arr);
                    sucess_msg("Cadastro concluído!");
                }
            }
        }
    ?>

    <br><br><h1>CADASTRO DE CLIENTES</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="fullName" class="form-label">Nome completo</label>
            <input type="text" class="form-control" name="fullName" value="<?php echo $nome;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="nickname" class="form-label">Apelido</label>
            <input type="text" class="form-control" name="nickname" value="<?php echo $apelido;?>">
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="cpf" class="form-label">CPF</label>
            <input type="number" min='0' class="form-control" name="cpf" value="<?php echo $cpf;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="birthDate" class="form-label">Data de nascimento</label>
            <input type="date" class="form-control" name="birthDate" value="<?php echo $dtNasc;?>">
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