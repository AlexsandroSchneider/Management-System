<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <h1>LISTA DE COMANDAS</h1>
    <div class="table-responsive">
    <?php
        $res = pg_query_params($db_connect, 'SELECT od.id AS "Documento", c.full_name AS "Cliente", od.sale_datetime AS "Data da Venda",
            (SELECT COALESCE(SUM(sold_price*quantity), 0.00) FROM outbound_products WHERE document_id = od.id) AS "Valor total",
            od.payment_method as "Pagamento", od.additional_info as "Informações"
            FROM outbound_documents od JOIN clients c ON od.client_id = c.id WHERE od.active = $1 ORDER BY od.id', array('t'));
            
        $i = pg_num_fields($res);
        if ($i > 0){
            echo "<table class='table table-bordered table-hover table-dark w-75 text-white'><tr>";
            for ($j = 0; $j < $i; $j++) {
                $fieldname = pg_field_name($res, $j);
                echo "<th>$fieldname</th>";
            } echo "<tr>";
            while ($row = pg_fetch_row($res)){
                $dataForm = date("d/m/Y H:i:s", strtotime($row[2]));
                echo "<tr title='Clique duplo para listar/adicionar produtos' ondblclick=","javascript:location.href='documento_saida_produto.php?id=$row[0]'",">";
                echo "<td><a href='documento_saida_produto.php?id=$row[0]' title='Listar/adicionar produtos'>DOC: $row[0]</td>";
                echo "<td>$row[1]</td><td>$dataForm</td><td>R$ $row[3]</td>";
                if ($row[3] != 0) echo "<td><a href='documento_saida_pagamento.php?id=$row[0]' title='Pagar comanda'>Pagar</td><td>$row[5]</td></tr>";
                else echo "<td>Inicie a venda</td><td>$row[5]</td></tr>";
            }
            echo "</table>";
        }
    ?>
    </div>
    
<?php include_once('footer.php');?>