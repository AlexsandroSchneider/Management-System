<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <div class="table-responsive">
    <?php
        if (isset($_COOKIE['docId'])) {
            sucess_msg("Pagamento da conta n°".$_COOKIE['docId']." concluído.");
        }
        
        $res = pg_query($db_connect, 'SELECT ar.document_id as "Comanda",
            (SELECT c.full_name FROM outbound_documents od JOIN clients c ON od.client_id = c.id WHERE od.id = ar.document_id) as "Cliente",
            ar.remaining_amount as "Valor restante", ar.inclusion_date as "Data da Inclusão", ar.additional_info as "Informações",
            ar.id as "Pagamento" FROM accounts_receivable ar WHERE ar.active = true');
        
        echo "<h1>LISTA DE CONTAS A RECEBER</h1>";
        $i = pg_num_fields($res);
        if ($i > 0){
            echo "<table class='table table-bordered table-hover table-dark w-75 text-white'><tr>";
            for ($j = 0; $j < $i; $j++) {
                $fieldname = pg_field_name($res, $j);
                echo "<th>$fieldname</th>";
            } echo "<tr>";
            while ($row = pg_fetch_row($res)){
                $dataForm = date("d/m/Y", strtotime($row[3]));
                echo "<tr title='Clique duplo para efetuar o pagamento' ondblclick=","javascript:location.href='contas_receber_pagamento.php?id=$row[5]'",">";
                echo "<td><a href='documento_saida_produto.php?id=$row[0]' title='Acessar documento referente'>ID $row[0]</td>";
                echo "<td>$row[1]</td><td>R$ $row[2]</td><td>$dataForm</td><td>$row[4]</td>";
                echo "<td><a href='contas_receber_pagamento.php?id=$row[5]' title='Pagamento a prazo'>Pagar</a></td></tr>";
            }
            echo "</table>";
        }
    ?>
    </div>
    
<?php include_once('footer.php');?>