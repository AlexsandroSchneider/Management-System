<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        $name = $description = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $name = test_input($_POST["name"]);
            $description = test_input($_POST["description"]);
            $exist = pg_query_params($db_connect, 'SELECT * FROM product_categories WHERE name = $1', array($name));
            if (pg_num_rows($exist) > 0) {
                error_msg("Categoria já cadastrada.");
            } else {
                pg_query_params($db_connect, 'INSERT INTO product_categories VALUES(DEFAULT, $1, $2)', array($name, $description));
                sucess_msg("Cadastro concluído!");
            }
        }
    ?>

    <br><br><h1>CADASTRO DE CATEGORIAS</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="name" class="form-label">Nome da categoria</label>
            <input type="text" class="form-control" name="name" value="<?php echo $name;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="description" class="form-label">Descrição</label>
            <input type="text" class="form-control" name="description" value="<?php echo $description;?>" required>
        </div>
        <br><button type="submit" class="btn btn-secondary">CADASTRAR</button>
    </form>

<?php include_once('footer.php');?>