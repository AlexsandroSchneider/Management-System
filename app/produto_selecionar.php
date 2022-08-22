<?php include_once('header.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <div class="container-sm">
    <div class="row">
    <br><br><h1>LISTA DE PRODUTOS</h1>
    <div class="table-responsive">
    <?php
        $res = pg_query($db_connect, 'SELECT p.id as "ID", p.description as "Descrição", pb.name as "Marca", p.selling_price as "Preço de Venda",
            p.current_inventory as "Estoque atual" FROM products p JOIN product_brands pb on p.brand_id = pb.id ORDER BY p.id');
            
        $i = pg_num_fields($res);
        if ($i > 0) {
            echo "<table class='table table-bordered table-hover table-dark w-75 text-white'><tr>";
            for ($j = 0; $j < $i; $j++) {
                $fieldname = pg_field_name($res, $j);
                echo "<th>$fieldname</th>";
            } echo "<tr>";
            while ($row = pg_fetch_row($res)){
                echo "<tr><td>$row[0]</td><td><a href='#' result='$row[0],$row[1],$row[3]' onclick='return CloseMySelf(this)'>";
                echo "$row[1]</a></td><td>$row[2]</td><td>R$ $row[3]</td><td>$row[4]</td></tr>";
            }
            echo "</table>";
        }
    ?>
    </div>
    </div>
    </div>

    <script>
        function CloseMySelf(sender) {
            try {
                window.opener.HandlePopupResult(sender.getAttribute("result"));
            }
            catch (err) {}
            window.close();
            return false;
        }
    </script>
    
<?php include_once('footer.php');?>