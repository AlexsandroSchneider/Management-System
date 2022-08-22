<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        $descr = $barr = $categ = $preco = $lucro = $estqAt = $estqMin = $marca = $categ = "";
        $erro1 = $erro2 = 1;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $preco = test_input($_POST["sellingPrice"]);
            $lucro = test_input($_POST["profit"]);
            $estqAt = test_input($_POST["currInv"]);
            $estqMin = test_input($_POST["minInv"]);
            $marca = test_input($_POST["brand"]);
            $categ = test_input($_POST["category"]);

            if (strlen($_POST["description"])<5) {
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

            if (!$erro1 AND !$erro2) {
                $exist = pg_query_params($db_connect, 'SELECT * FROM products WHERE bar_code = $1', array($barr));
                if (pg_num_rows($exist) > 0) {
                    error_msg("Código de barras já utilizado em outro cadastro.");
                } else {
                    $arr = array($barr, $descr, $preco, $lucro, $estqAt, $estqMin, $marca, $categ, 't');
                    pg_query_params($db_connect, 'INSERT INTO products VALUES (DEFAULT, $1, $2, $3, $4, $5, $6, $7, $8, $9)', $arr);
                    sucess_msg("Cadastro concluído!");
                }
            }
        }
    ?>

    <br><br><h1>CADASTRO DE PRODUTOS</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
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
            <label for="category" class="form-label">Categoria</label>
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
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="currInv" class="form-label">Estoque Atual</label>
            <input type="number" min='0' class="form-control" name="currInv" value="<?php echo $estqAt;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="minInv" class="form-label">Estoque Mínimo</label>
            <input type="number" min='0' class="form-control" name="minInv" value="<?php echo $estqMin;?>" required>
        </div>
        <br><button type="submit" class="btn btn-secondary">CADASTRAR</button>
    </form>

<?php include_once('footer.php');?>