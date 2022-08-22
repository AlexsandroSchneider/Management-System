<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        $inclusion_date = date('Y-m-d');

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $document_id = test_input($_POST["document_id"]);
            $remaining_amount = test_input($_POST["remaining_amount"]);
            $inclusion_date = test_input($_POST["inclusion_date"]);
            $additional_info = test_input($_POST["additional_info"]);

            pg_query_params($db_connect, 'INSERT INTO accounts_receivable VALUES(DEFAULT, $1, $2, $3, $4, $5)', array($inclusion_date, $remaining_amount, $additional_info, $document_id, 't'));
            pg_query_params($db_connect, 'UPDATE outbound_documents SET payment_method = $1, active = $2 WHERE id = $3', array('A prazo','f', $document_id));
            sucess_msg("Conta incluída!");
        }
    ?>

    <br><br><h1>INCLUIR CONTAS A RECEBER</h1>
    <form action="" method="post">
        <fieldset>
        <legend>Pagamento de contas a prazo</legend>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="document_id" class="form-label">Comanda referenciada</label>
            <select class="form-select" name="document_id" required>
                <?php
                    echo "<option value='' selected disabled hidden>Selecione o documento</option>";
                    $res = pg_query($db_connect, "SELECT od.id, c.full_name, SUM(op.sold_price * op.quantity)
                        FROM outbound_documents od JOIN clients c on od.client_id = c.id
                        JOIN outbound_products op ON od.id = op.document_id WHERE od.active = 't' GROUP BY 1,2 ORDER BY od.id;");
                    while ($row = pg_fetch_row($res)){
                        echo "<option value='$row[0]'>$row[0] -> R$ $row[2] -> $row[1]</option>";
                    }
                ?>
            </select>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="remaining_amount" class="form-label">Valor restante</label>
            <input type="number" step="0.01" min="0" class="form-control" name="remaining_amount" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="inclusion_date" class="form-label">Data da inclusão</label>
            <input type="date" class="form-control" name="inclusion_date" value="<?php echo $inclusion_date;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="additional_info">Informações adicionais</label>
            <textarea class="form-control" name="additional_info" rows="3"></textarea>
        </div>
        <br><button type="submit" class="btn btn-secondary">INCLUIR</button>
        </fieldset>
    </form>

<?php include_once('footer.php');?>