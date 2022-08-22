<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        if (isset($_GET["id"])) {
            $document_id = $_GET["id"];
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $document_id = test_input($_POST["document_id"]);
            $payment_method = test_input($_POST["payment_method"]);

            pg_query_params($db_connect, 'UPDATE outbound_documents SET payment_method = $1, active = $2 WHERE id = $3', array($payment_method, 'f', $document_id));
            sucess_msg("Pagamento concluído!");
            $document_id = "";
        }
    ?>

    <br><br><h1>PAGAMENTO DE COMANDAS</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="document_id" class="form-label">Comanda referenciada</label>
            <select class="form-select" name="document_id" required>
                <?php
                    echo "<option value='' selected disabled hidden>Selecione o documento</option>";
                    $res = pg_query($db_connect, "SELECT od.id, c.full_name, SUM(op.sold_price * op.quantity)
                        FROM outbound_documents od JOIN clients c on od.client_id = c.id
                        JOIN outbound_products op ON od.id = op.document_id WHERE od.active = 't' GROUP BY 1,2 ORDER BY od.id;");
                    while ($row = pg_fetch_row($res)){
                        if ($row[0] == $document_id) {
                            echo "<option value='$row[0]' selected>$row[0] -> R$ $row[2] -> $row[1]</option>";
                        } else echo "<option value='$row[0]'>$row[0] -> R$ $row[2] -> $row[1]</option>";
                    }
                ?>
            </select>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="payment_method" class="form-label">Tipo de pagamento</label>
            <select class="form-select" name="payment_method" required>
                <?php $arr = array("À vista" => "À vista", "A prazo" => "A prazo", "Débito" => "Débito", "Crédito" => "Crédito", "PIX" => "PIX", "Outros" => "Outros");
                    echo "<option value='' selected disabled hidden>Selecione a forma de pagamento</option>";
                    foreach ($arr as $key => $value) {
                        echo "<option value='$key'>$value</option>";
                    }?>
            </select>
        </div>
        <br><button type="submit" class="btn btn-secondary">PAGAR</button>
    </form>

<?php include_once('footer.php');?>