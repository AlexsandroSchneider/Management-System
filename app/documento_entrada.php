<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        $numero = $tipo = $fornec = $valor = "";
        $dtEmiss = $dtEntr = date('Y-m-d');

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $numero = test_input($_POST["docNumber"]);
            $tipo = test_input($_POST["docType"]);
            $fornec = test_input($_POST["suppId"]);
            $valor = test_input($_POST["value"]);
            $dtEmiss = test_input($_POST["issueDate"]);
            $dtEntr = test_input($_POST["inboundDate"]);

            $info = array('inbN' => $numero, 'inbT' => $tipo, 'inbS' => $fornec, 'inbV' => $valor, 'inbIssD' => $dtEmiss, 'inbInbD' => $dtEntr);
            $res = pg_query_params($db_connect, 'SELECT * FROM inbound_documents WHERE number = $1 AND supplier_id = $2', array($numero, $fornec));
            if (pg_num_rows($res) == 0) {
                setcookie('doc_entry', json_encode($info), time()+360);
                header("Location: documento_entrada_produto.php");
            } else error_msg("Documento n°$numero já existe para o fornecedor $fornec.");
        }
    ?>

    <br><br><h1>DOCUMENTO DE ENTRADA</h1>
    <form action="#" method="post">
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="docNumber" class="form-label">Número do documento</label>
            <input type="number" min="1" class="form-control" name="docNumber" value="<?php echo $numero;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="docType" class="form-label">Tipo de documento</label>
            <select class="form-select" name="docType" required>
                <?php
                    $arr = array("Cupom Fiscal" => "Cupom Fiscal", "Nota Fiscal Eletrônica" => "Nota Fiscal Eletrônica", "Outros" => "Outros");
                    echo "<option value='' selected disabled hidden>Selecione o tipo de documento</option>";
                    foreach ($arr as $key => $value) {
                        if ($key == $tipo) {
                            echo "<option value='$key' selected>$value</option>";
                        } else echo "<option value='$key'>$value</option>";
                    }
                ?>
            </select>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="suppId" class="form-label">Fornecedor</label>
            <select class="form-select" name="suppId" required>
                <?php
                    echo "<option value='' selected disabled hidden>Selecione o fornecedor</option>";
                    $res = pg_query($db_connect, "SELECT id, corporate_name FROM suppliers order by corporate_name");
                    while ($row = pg_fetch_row($res)){
                        if ($row[0] == $fornec) {
                            echo "<option value='$row[0]' selected>$row[1]</option>";
                        } else echo "<option value='$row[0]'>$row[1]</option>";
                    }
                ?>
            </select>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="value" class="form-label">Valor total</label>
            <input type="number" step="0.01" min="0" class="form-control" name="value" value="<?php echo $valor;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="issueDate" class="form-label">Data de Emissão</label>
            <input type="date" class="form-control" name="issueDate" value="<?php echo $dtEmiss;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="inboundDate" class="form-label">Data de Entrada</label>
            <input type="date" class="form-control" name="inboundDate" value="<?php echo $dtEntr;?>" required>
        </div>
        <br><button type="submit" class="btn btn-secondary">AVANÇAR</button>
    </form>

<?php include_once('footer.php');?>