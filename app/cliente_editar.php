<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        $id = $cpf = $nome = $apelido = $dtNasc = $telefone = $endereco = $ativo = "";
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $res = pg_query_params($db_connect, 'SELECT * FROM clients WHERE id = $1', array($id));
            if (pg_num_rows($res) == 1) {
                $arr = pg_fetch_array($res, 0);
                $cpf = $arr['cpf'];
                $nome = $arr['full_name'];
                $apelido = $arr['nickname'];
                $dtNasc = $arr['birth_date'];
                $telefone = $arr['phone_number'];
                $endereco = $arr['address'];
                $ativo = $arr['active'];
            } else header("Location: cliente_listar.php");
        } else header("Location: cliente_listar.php");

        $erro1 = $erro2 = 1;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

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

            $telefone = test_input($_POST["phoneNumber"]);
            $apelido = test_input($_POST["nickname"]);
            $dtNasc = test_input($_POST["birthDate"]);
            $endereco = test_input($_POST["address"]);
            isset($_POST['active']) ? $ativo = 't' : $ativo = 'f';
            
            if (!$erro1 AND !$erro2) {
                $exist = pg_query_params($db_connect, 'SELECT * FROM clients WHERE cpf = $1 AND NOT id = $2', array($cpf, $id));
                if (pg_num_rows($exist) > 0) {
                    error_msg("CPF já utilizado em outro cadastro.");
                } else {
                    $arr = array($cpf, $nome, $apelido, $dtNasc, $telefone, $ativo, $endereco, $id);
                    pg_query_params($db_connect, 'UPDATE clients SET cpf = $1, full_name = $2, nickname = $3,
                        birth_date = $4, phone_number = $5, active = $6, address = $7 WHERE id = $8', $arr);
                    sucess_msg("Atualização concluída!");
                }
            }
        }
    ?>

    <br><br><h1>EDITAR CLIENTES</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']), "?id=", $id;?>">
        CADASTRO ATIVO?<br>
        <div class="form-check">
            <?php if($ativo == 't') {?>
                <input class="form-check-input" type="checkbox" name="active" checked>
            <?php } else {?>
                <input class="form-check-input" type="checkbox" name="active">
            <?php }?>
            <label class="form-check-label" for="active">ATIVO</label>
        </div><br>
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
        <br><button type="submit" class="btn btn-secondary">ATUALIZAR</button>
    </form>

<?php include_once('footer.php');?>