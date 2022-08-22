<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <br><br><h1>LISTA DE FORNECEDORES</h1>
    <div class="table-responsive">
    <?php
        $res = pg_query($db_connect, 'SELECT id as "ID", corporate_name as "Nome", phone_number as "Telefone", active as "Ativo?" FROM suppliers ORDER BY id');
        
        $i = pg_num_fields($res);
        if ($i > 0){
            echo "<table class='table table-bordered table-hover table-dark w-75 text-white'><tr>";
            for ($j = 0; $j < $i; $j++) {
                $fieldname = pg_field_name($res, $j);
                echo "<th>$fieldname</th>";
            } echo "<tr>";
            while ($row = pg_fetch_row($res)){
                echo "<tr title='Clique duplo para editar o cadastro' ondblclick=","javascript:location.href='fornecedor_editar.php?id=$row[0]'",">";
                echo "<td>$row[0]</td><td><a href='fornecedor_editar.php?id=$row[0]' title='Editar cadastro'>";
                echo "$row[1]</a></td><td>$row[2]</td><td>";
                if ($row[3] == 't') echo "SIM</td></tr>";
                else echo "NÃO</td></tr>";
            }
            echo "</table>";
        }
    ?>
    </div>
    
<?php include_once('footer.php');?>