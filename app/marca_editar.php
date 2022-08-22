<?php include_once('header.php'); include_once('navbar.php'); include_once('db_connect.php'); include_once('utils.php'); is_logged_in()?>

    <?php
        $id = $name = $description = "";
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $res = pg_query_params($db_connect, 'SELECT * FROM product_brands WHERE id = $1', array($id));
            if (pg_num_rows($res) == 1) {
                $arr = pg_fetch_array($res, 0);
                $name = $arr['name'];
                $description = $arr['description'];
            } else header("Location: marca_listar.php");
        } else header("Location: marca_listar.php");

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $name = test_input($_POST["name"]);
            $description = test_input($_POST["description"]);
            $exist = pg_query_params($db_connect, 'SELECT * FROM product_brands WHERE name = $1 AND NOT id = $2', array($name, $id));

            if (pg_num_rows($exist) > 0) {
                error_msg("Nome já utilizado em outro cadastro.");
            } else {
                pg_query_params($db_connect, 'UPDATE product_brands SET name = $1, description = $2 WHERE id = $3', array($name, $description, $id));
                sucess_msg("Atualização concluída!");
            }
        }
    ?>
    
    <br><br><h1>EDITAR PRODUTOS</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']), "?id=", $id;?>">
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="name" class="form-label">Nome da marca</label>
            <input type="text" class="form-control" name="name" value="<?php echo $name;?>" required>
        </div>
        <div class="col-md-5 col-sm-10 col-xs-10">
            <label for="description" class="form-label">Descrição</label>
            <input type="text" class="form-control" name="description" value="<?php echo $description;?>" required>
        </div>
        <br><button type="submit" class="btn btn-secondary">ATUALIZAR</button>
    </form>

<?php include_once('footer.php');?>