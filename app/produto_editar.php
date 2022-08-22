<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        $id = $descr = $barr = $categ = $preco = $lucro = $estqAt = $estqMin = $marca = $categ = $ativo = "";
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $res = pg_query_params($db_connect, 'SELECT * FROM products WHERE id = $1', array($id));
            if (pg_num_rows($res) == 1) {
                $arr = pg_fetch_array($res, 0);
                $descr = $arr['description'];
                $barr = $arr['bar_code'];
                $preco = $arr['selling_price'];
                $lucro = $arr['profit_margin'];
                $estqAt = $arr['current_inventory'];
                $estqMin = $arr['minimum_inventory'];
                $marca = $arr['brand_id'];
                $categ = $arr['category_id'];
                $ativo = $arr['active'];
            } else header("Location: produto_listar.php");
        } else header("Location: produto_listar.php");

        $erro1 = $erro2 = 1;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $preco = test_input($_POST["sellingPrice"]);
            $lucro = test_input($_POST["profit"]);
            $categ = test_input($_POST["category"]);
            $marca = test_input($_POST["brand"]);

            if (strlen($_POST["description"]) < 5) {
                error_msg("Descrição precisa ter mais que 5 digitos.");
            } else {
                $descr = test_input($_POST["description"]);
                $erro1 = 0;
            }

            if (strlen($_POST["barCode"])<5 OR strlen($_POST["barCode"])>13) {
                error_msg("Código de barras precisa ter entre 5 e 13 digitos.");
            } else {
                $barr = test_input($_POST["barCode"]);
                $erro2 = 0;
            }

            isset($_POST['active']) ? $ativo = 't' : $ativo = 'f';

            if (!$erro1 AND !$erro2) {
                $exist = pg_query_params($db_connect, 'SELECT * FROM products WHERE bar_code = $1 AND NOT id = $2', array($barr, $id));
                if (pg_num_rows($exist) > 0) {
                    error_msg("Código de barras já utilizado em outro cadastro.");
                } else {
                    pg_query_params($db_connect, 'UPDATE products SET bar_code = $1, description = $2, selling_price = $3,
                        profit_margin = $4, current_inventory = $5, minimum_inventory = $6, brand_id = $7, category_id = $8, active = $9 WHERE id = $10',
                        array($barr, $descr, $preco, $lucro, $estqAt, $estqMin, $marca, $categ, $ativo, $id));
                    sucess_msg("Atualização concluída!");
                }
            }
        }
    ?>
    
    <br><br><h1>EDITAR PRODUTOS</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']), "?id=", $id;?>">
        CADASTRO ATIVO?<br>
        <div class="form-check">
            <?php if($ativo == 't') {?>
                <input class="form-check-input" type="checkbox" name="active" checked>
            <?php } else {?>
                <input class="form-check-input" type="checkbox" name="active">
            <?php }?>
            <label class="form-check-label" for="active">ATIVO</label>
        </div><br>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="description" class="form-label">Descrição</label>
            <input type="text" class="form-control" name="description" value="<?php echo $descr;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="barCode" class="form-label">Código de barras</label>
            <input type="number" min='0' class="form-control" name="barCode" value="<?php echo $barr;?>">
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="brand" class="form-label">Marca</label>
            <select class="form-select" name="brand" required>
                <?php
                    echo "<option value='' selected disabled hidden>Selecione a marca</option>";
                    $res = pg_query($db_connect, "SELECT id, name FROM product_brands ORDER BY name");
                    while ($row = pg_fetch_row($res)){
                        if ($row[0] == $marca) {
                            echo "<option value='$row[0]' selected>$row[1]</option>";
                        } else echo "<option value='$row[0]'>$row[1]</option>";
                    }
                ?>
            </select>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="brand" class="form-label">Categoria</label>
            <select class="form-select" name="category" required>
                <?php
                    echo "<option value='' selected disabled hidden>Selecione a categoria</option>";
                    $res = pg_query($db_connect, "SELECT id, name FROM product_categories order by name");
                    while ($row = pg_fetch_row($res)){
                        if ($row[0] == $categ) {
                            echo "<option value='$row[0]' selected>$row[1]</option>";
                        } else echo "<option value='$row[0]'>$row[1]</option>";
                    }
                ?>
            </select>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="sellingPrice" class="form-label">Preço de Venda</label>
            <input type="number" step="0.01" min='0' max='10000' class="form-control" name="sellingPrice" value="<?php echo $preco;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="profit" class="form-label">Margem de lucro</label>
            <input type="number" min='0' class="form-control" name="profit" value="<?php echo $lucro;?>" required>
        </div>
        <br><button type="submit" class="btn btn-secondary">ATUALIZAR</button>
    </form>

<?php include_once('footer.php');?>