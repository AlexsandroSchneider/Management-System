<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        if (empty($_COOKIE['doc_entry'])) {
            header("Location: documento_entrada.php");
        } else {
            $info = json_decode($_COOKIE['doc_entry'], true);

            $_COOKIE['doc_entry'] = "";

            $docN = $info['inbN'];
            $docT = $info['inbT'];
            $docV = $info['inbV'];
            $docS = $info['inbS'];
            $docEmiD = $info['inbIssD'];
            $docEntD = $info['inbInbD'];

            $supplier = pg_fetch_result(pg_query_params($db_connect, 'SELECT corporate_name FROM suppliers WHERE id = $1', array($docS)),0,0);
            echo "<script>var valorTotal = $docV;</script>";

            $count = 0;

            pg_prepare($db_connect, "insert_product_indoc", 'INSERT INTO inbound_products VALUES(DEFAULT, $1, $2, $3, $4)');
            pg_prepare($db_connect, 'add_to_inventory', 'UPDATE products SET current_inventory = current_inventory + $1 WHERE id = $2');

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                pg_query_params($db_connect, 'INSERT INTO inbound_documents VALUES (DEFAULT, $1, $2, $3, $4, $5, $6, $7)', array($docN, $docT, $docV, 'BOLETO', $docEmiD, $docEntD, $docS));
                $docId = pg_fetch_result(pg_query_params($db_connect, 'SELECT id FROM inbound_documents WHERE number = $1 AND supplier_id = $2', array($docN, $docS)), 0, 0);
                while (isset($_POST["qty$count"])){
                    $qty = test_input($_POST["qty$count"]);
                    $cost = test_input($_POST["cost$count"]);
                    $prodId = test_input($_POST["prodId$count"]);
                    pg_execute($db_connect, "insert_product_indoc", array($qty, $cost, $prodId, $docId));
                    pg_execute($db_connect, 'add_to_inventory', array($qty, $prodId));
                    $count++;
                }
                header('Location: documento_entrada_produto_listar.php?id='.$docId.'');
            }
        }
    ?>

    <script type='text/javascript'>
        document.addEventListener('DOMContentLoaded', addFields);
        var contador = 0;
        var objeto = "";
        
        function HandlePopupResult(result) {
            document.getElementById(objeto[0]).value = result.split(',')[0];
            document.getElementById(objeto[1]).value = result.split(',')[1];
            objeto = "";
        }

        function addFields(){
            var products = document.getElementById("products");
            var container = document.createElement("div");
            container.id = "prod" + contador;
            products.appendChild(container);
            var iLabel = document.createElement("label");
            var quantity = document.createElement("input");
            var qLabel = document.createElement("label");
            var cost = document.createElement("input");
            var cLabel = document.createElement("label");
            var prodID = document.createElement("input");
            var prodName = document.createElement("input");
            var button = document.createElement("input");
            iLabel.innerText = "Item " + (contador+1) + ":";
            quantity.type = "number";
            quantity.id = "qty" + contador;
            quantity.name = "qty" + contador;
            qLabel.innerText = "Quantidade:";
            cost.type = "number";
            cost.step = "0.01";
            cost.id = "cost" + contador;
            cost.name = "cost" + contador;
            cLabel.innerText = "Custo:";
            cLabel.htmlFor = cost.name;
            prodID.type = "text";
            prodID.id = "prodId" + contador;
            prodID.name = "prodId" + contador;
            prodName.type = "text";
            prodName.id = "prodName" + contador;
            prodName.name = "prodName" + contador;
            button.value = "PRODUTO";
            button.type = "button";
            button.id = "select" + contador;
            container.appendChild(iLabel);
            container.appendChild(button);
            container.appendChild(prodID);
            container.appendChild(prodName);
            container.appendChild(qLabel);
            container.appendChild(quantity);
            container.appendChild(cLabel);
            container.appendChild(cost);
            container.appendChild(document.createElement("br"));
            document.getElementById(quantity.id).style.width = '75px';
            document.getElementById(cost.id).style.width = '75px';
            document.getElementById(prodID.id).hidden = true;
            document.getElementById(prodID.id).size = '3';
            document.getElementById(prodName.id).disabled = true;
            document.getElementById(prodName.id).size = '30';
            button.addEventListener('click', function(){
                objeto = [prodID.id, prodName.id];
                window.open("produto_selecionar.php", width=450,height=700);
            });
            contador++;
        }

        function removeFields(){
            var products = document.getElementById("products");
            products.removeChild(products.lastChild);
            contador--;
        }

        function saveForms() {
            var total = 0;
            var form = document.getElementById("dynForm");
            if (!document.getElementById('qty0')) {
                alert("Insira um produto.");
                return;
            }
            for (var i = 0; i < contador; i++) {
                var t1 = document.getElementById("qty"+i).value;
                var t2 = document.getElementById("cost"+i).value;
                var t3 = document.getElementById("prodId"+i).value;
                if (Number(t1) == 0 || Number(t2) == 0 || Number(t3) == 0) {
                    alert("Verifique o cadastro do Item " + (i+1));
                    return;
                }
                total = total + (t1*t2);
            }
            if (total == valorTotal) {
                form.submit();
            } else alert("Valor do documento: R$ " + valorTotal +"\nValor dos produtos: R$ " + total);
        }
    </script>
    <br><br><h1>PRODUTOS DO DOCUMENTO DE ENTRADA</h1><br>
    <fieldset>
        <legend>Informações do documento:</legend>
        <p>Fornecedor: <?php echo $supplier; ?></p><p>Número: <?php echo $docN; ?></p>
        <p>Valor: R$ <?php echo $docV; ?></p><p>Data Emissão: <?php echo date("d/m/Y", strtotime($docEmiD)); ?></p>
    </fieldset>
    <fieldset>
    <button type="button" id="ADD" class="btn btn-secondary" onclick='addFields()'>+ ITEM</button> 
    <button type="button" id="REMOVE" class="btn btn-secondary" onclick='removeFields()'>- ITEM</button>
    <button type="button" id="SAVE" class="btn btn-primary" onclick='saveForms()'>SALVAR</button><br><br>
    </fieldset>
    <form id="dynForm" action="" method="post">
        <div class='' id="products"></div>
    </form>

<?php include_once('footer.php');?>