<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        $client_id = $additional_info = "";
        $sale_datetime = date("Y-m-d H:i:s");

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $client_id = test_input($_POST["client_id"]);
            $sale_datetime = test_input($_POST["sale_datetime"]);
            $additional_info = test_input($_POST["additional_info"]);

            $exist = pg_query_params($db_connect, 'SELECT id from outbound_documents WHERE sale_datetime = $1 AND client_id = $2', array($sale_datetime, $client_id));
            if (pg_num_rows($exist) == 0) {
                pg_query_params($db_connect, 'INSERT INTO outbound_documents VALUES(DEFAULT, $1, $2, $3, $4, $5)', array($sale_datetime, 'Aberto', $additional_info, $client_id, 't'));
                $res = pg_query_params($db_connect, 'SELECT id from outbound_documents WHERE sale_datetime = $1 AND client_id = $2', array($sale_datetime, $client_id));
                header('Location: documento_saida_produto.php?id='.pg_fetch_result($res, 0, 0).'');
                exit();
            } else error_msg("ERRO! Já existe documento para os mesmos cliente & dia.");
        }
    ?>

    <br><br><h1>INICIAR COMANDA</h1>
    <form action="" method="post">
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="client_id" class="form-label">Cliente</label>
            <select class="form-select" name="client_id" required>
                <?php
                    echo "<option value='' selected disabled hidden>Selecione o cliente</option>";
                    $res = pg_query($db_connect, "SELECT id, full_name FROM clients order by full_name");
                    while ($row = pg_fetch_row($res)){
                        if ($row[1] == 'Diversos') {
                            echo "<option value='$row[0]' selected>$row[1]</option>";
                        } else echo "<option value='$row[0]'>$row[1]</option>";
                    }
                ?>
            </select>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="sale_datetime" class="form-label">Data da Venda</label>
            <input type="datetime-local" class="form-control" name="sale_datetime" value="<?php echo $sale_datetime;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="additional_info">Informações adicionais</label>
            <textarea class="form-control" name="additional_info" rows="3" value="<?php echo $additional_info;?>"></textarea>
        </div>
        <br><button type="submit" class="btn btn-secondary">AVANÇAR</button>
    </form>

<?php include_once('footer.php');?>