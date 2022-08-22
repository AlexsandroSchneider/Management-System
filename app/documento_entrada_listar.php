<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <br><br><h1>LISTA DE DOCUMENTOS DE ENTRADA</h1>
    <div class="table-responsive">
    <?php
        $res = pg_query($db_connect, 'SELECT id.id, id.number as "Número", s.corporate_name as "Fornecedor", id.value as "Valor Total",
            id.issue_date as "Data de emissão", (select count(*) from inbound_products where document_id = id.id) as "Itens"
            FROM inbound_documents id JOIN suppliers s on id.supplier_id = s.id ORDER BY id.id');

        $i = pg_num_fields($res);
        if ($i > 0) {
            echo "<table class='table table-bordered table-hover table-dark w-75 text-white'><tr>";
            for ($j = 1; $j < $i; $j++) {
                $fieldname = pg_field_name($res, $j);
                echo "<th>$fieldname</th>";
            } echo "<tr>";
            while ($row = pg_fetch_row($res)){
                $dataForm = date("d/m/Y", strtotime($row[4]));
                echo "<tr title='Clique duplo para acessar o documento' ondblclick=","javascript:location.href='documento_entrada_produto_listar.php?id=$row[0]'",">";
                echo "<td><a href='documento_entrada_produto_listar.php?id=$row[0]' title='Acessar o documento'>$row[1]</td>";
                echo "<td>$row[2]</td><td>R$ $row[3]</td><td>$dataForm</td><td>$row[5]</td></tr>";
            }
            echo "</table>";
        }
    ?>
    </div>
    
<?php include_once('footer.php');?>