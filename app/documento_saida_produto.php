<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <br><br><h1>PRODUTOS DA COMANDA</h1>
    <div class="table-responsive">
    <?php
        $id = $number = $type = $value = $issue_date = $inbound_date = $supplier_id = "";
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $insArr = array($_POST['quantity'], $_POST['price'], $_POST['prodId'], $id);
                $subArr = array($_POST['quantity'], $_POST['prodId']);
                pg_query_params($db_connect, 'INSERT INTO outbound_products VALUES (DEFAULT, $1, $2, $3, $4)', $insArr);
                pg_query_params($db_connect, 'UPDATE products SET current_inventory = current_inventory - $1 WHERE id = $2', $subArr);
            }

            $res = pg_query_params($db_connect, 'SELECT od.id AS "ID", c.full_name AS "Cliente",
                (SELECT COALESCE(SUM(sold_price*quantity), 0.00) FROM outbound_products WHERE document_id = od.id) AS "Valor total",
                od.sale_datetime AS "Data da Venda", od.additional_info as "Informações", od.active as "Ativo?"
                FROM outbound_documents od JOIN clients c ON od.client_id = c.id where od.id = $1', array($id));

            if (pg_num_rows($res) == 1) {
                
                $i = pg_num_fields($res);
                echo "<table class='table table-bordered table-hover table-dark w-75 text-white'><tr>";
                for ($j = 0; $j < $i-1; $j++) {
                    $fieldname = pg_field_name($res, $j);
                    echo "<th>$fieldname</th>";
                }
                $row = pg_fetch_row($res);
                $dataEmit = date("d/m/Y H:i:s", strtotime($row[3]));
                echo "</tr><tr><td>$row[0]</td><td>$row[1]</td><td>R$ $row[2]</td><td>$dataEmit</td><td>$row[4]</td></tr></table></div>";
                
                if ($row[5] == 'f') echo "<script>docAtivo = false;</script>";

                $res = pg_query_params($db_connect, 'SELECT op.id as "Item", p.id as "Cód. Produto", p.description as "Descrição", op.quantity as "QTD",
                    op.sold_price as "Preço de Venda", COALESCE((op.quantity * op.sold_price), 0.00) as "Preço Total"
                    FROM outbound_products op JOIN products p on op.product_id = p.id WHERE op.document_id = $1 ORDER BY op.id', array($id));

                if (pg_num_rows($res) > 0) {
                    $i = pg_num_fields($res);
                    echo "<div class='table-responsive'><table class='table table-bordered table-hover table-dark w-75 text-white'><tr>";
                    for ($j = 0; $j < $i; $j++) {
                        $fieldname = pg_field_name($res, $j);
                        echo "<th>$fieldname</th>";
                    } echo "</tr>";
                    $count = 1;
                    while($row = pg_fetch_row($res)) {
                        echo "<tr><td>$count</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>R$ $row[4]</td><td>R$ $row[5]</td></tr>";
                        $count++;
                    } echo "</table>";
                }

            } else header("Location: documento_saida_listar.php");
        } else header("Location: documento_saida_listar.php");
    ?>
    </div>

    <script type='text/javascript'>
        document.addEventListener('DOMContentLoaded', function(){if (typeof docAtivo === 'undefined') addFields()});
        var objeto = "";

        function HandlePopupResult(result) {
            document.getElementById(objeto[0]).value = result.split(',')[0];
            document.getElementById(objeto[1]).value = result.split(',')[1];
            document.getElementById(objeto[2]).value = result.split(',')[2];
            objeto = "";
        }

        function addFields(){
            var container = document.getElementById("product");
            var quantity = document.createElement("input");
            var qLabel = document.createElement("label");
            var price = document.createElement("input");
            var pLabel = document.createElement("label");
            var prodID = document.createElement("input");
            var prodName = document.createElement("input");
            var button = document.createElement("input");
            var submit = document.createElement("input");
            price.type = quantity.type = "number";
            quantity.min = "1";
            quantity.id = quantity.name = qLabel.htmlFor = "quantity";
            qLabel.innerText = "Quantidade: ";
            price.min = price.step = "0.01";
            price.id = price.name = pLabel.htmlFor = "price";
            pLabel.innerText = "Preço: ";
            prodID.type = prodName.type = "text";
            prodID.id = prodID.name = "prodId";
            prodName.id = prodName.name = "prodName";
            button.value = "PRODUTO";
            button.type = "button";
            button.id = "select";
            submit.type = "submit";
            submit.value = "SALVAR";
            price.required = prodID.required = quantity.required = true;
            container.appendChild(button);
            container.appendChild(prodID);
            container.appendChild(prodName);
            container.appendChild(qLabel);
            container.appendChild(quantity);
            container.appendChild(pLabel);
            container.appendChild(price);
            container.appendChild(submit);
            container.appendChild(document.createElement("br"));
            document.getElementById(quantity.id).style.width = '75px';
            document.getElementById(price.id).style.width = '75px';
            document.getElementById(prodID.id).hidden = true;
            document.getElementById(prodID.id).size = '3';
            document.getElementById(prodName.id).disabled = true;
            document.getElementById(prodName.id).size = '30';
            button.addEventListener('click', function(){
                objeto = [prodID.id, prodName.id, price.id];
                window.open("produto_selecionar.php", width=450,height=700);
            });
        }

    </script>
    <form id="dynForm" action="" method="post">
        <fieldset>
            <div class='' id="product"></div>
        </fieldset>
    </form>
        
<?php include_once('footer.php');?>