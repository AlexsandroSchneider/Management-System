<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <br><br><h1>PRODUTOS DO DOCUMENTO DE ENTRADA</h1>
    <div class="table-responsive">
    <?php
        $id = $number = $type = $value = $issue_date = $inbound_date = $supplier_id = "";
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $res = pg_query_params($db_connect, 'SELECT id.number as "Número", s.corporate_name as "Fornecedor", id.value as "Valor",
                id.issue_date as "Data de Emissão" FROM inbound_documents id JOIN suppliers s on id.supplier_id = s.id WHERE id.id = $1', array($id));

            if (pg_num_rows($res) > 0) {

                $i = pg_num_fields($res);
                echo "<table class='table table-bordered table-hover table-dark w-75 text-white'><tr>";
                for ($j = 0; $j < $i; $j++) {
                    $fieldname = pg_field_name($res, $j);
                    echo "<th>$fieldname</th>";
                }
                $row = pg_fetch_row($res);
                $dataEmit = date("d/m/Y", strtotime($row[3]));
                echo "</tr><tr><td>$row[0]</td><td>$row[1]</td><td>R$ $row[2]</td><td>$dataEmit</td></tr></table></div>";
                
                $res = pg_query_params($db_connect, 'SELECT ip.id as "Item", p.description as "Descrição", ip.quantity as "QTD", ip.cost_price as "Custo UN",
                    (ip.quantity * ip.cost_price) as "Total" FROM inbound_products ip JOIN products p on ip.product_id = p.id
                    WHERE ip.document_id = $1 ORDER BY ip.id', array($id));

                if (pg_num_rows($res) > 0){
                    $i = pg_num_fields($res);
                    echo "<div class='table-responsive'><table class='table table-bordered table-hover table-dark w-75 text-white'><tr>";
                    for ($j = 0; $j < $i; $j++) {
                        $fieldname = pg_field_name($res, $j);
                        echo "<th>$fieldname</th>";
                    } echo "</tr>";
                    $count = 1;
                    while($row = pg_fetch_row($res)) {
                        echo "<tr><td>$count</td><td>$row[1]</td><td>$row[2]</td><td>R$ $row[3]</td><td>R$ $row[4]</td></tr>";
                        $count++;
                    } echo "</table>";
                }
            } else header("Location: documento_entrada_listar.php");
        } else header("Location: documento_entrada_listar.php");
    ?>
    </div>
    
<?php include_once('footer.php');?>