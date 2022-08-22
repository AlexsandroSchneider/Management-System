<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $res = pg_query_params($db_connect, 'SELECT * FROM accounts_receivable WHERE id = $1', array($id));
            if (pg_num_rows($res) == 1) {
                $arr = pg_fetch_array($res, 0);
                $remaining_amount = $arr['remaining_amount'];
                $additional_info = $arr['additional_info'];
                $inclusion_date = date("d/m/Y", strtotime($arr['inclusion_date']));
            } else header('Location: contas_receber_listar.php');
        } else header('Location: contas_receber_listar.php');

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $amount_paid = test_input($_POST["amount_paid"]);
            
            $remaining_amount = $remaining_amount - $amount_paid;
            if ($remaining_amount == 0) $active = 'f';
            else $active = 't';

            pg_query_params($db_connect, 'UPDATE accounts_receivable SET remaining_amount = $1, active = $2 WHERE id = $3', array($remaining_amount, $active, $id));

            if ($active == 'f') {
                setcookie("docId", $id, time()+1);
                header('Location: contas_receber_listar.php');
                exit();
            } else info_msg("Valor descontado. Restou R$ $remaining_amount");

        }
    ?>

    <br><br><h1>PAGAMENTO DE CONTAS A RECEBER</h1>
    <fieldset>
        <legend>Dados da conta</legend>
        <?php echo "<p><strong>ID do Documento: ".$id."<br>";?>
        <?php echo "Data da inclusão: ".$inclusion_date."<br>";?>
        <?php if (!empty($additional_info)) echo "Informações: ".$additional_info."<br>";?>
        <?php echo "VALOR RESTANTE: R$ ".$remaining_amount."</strong></p>";?>
    </fieldset>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']), "?id=", $id;?>">
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="amount_paid" class="form-label">Valor a descontar</label>
            <input type="number" step="0.01" min='0' max="<?php echo $remaining_amount;?>" class="form-control" name="amount_paid" required>
        </div>
        <br><button type="submit" class="btn btn-secondary">DESCONTAR</button>
    </form>

<?php include_once('footer.php');?>