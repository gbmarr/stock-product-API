<?php
require_once './config/config.php';

class APIModel {
    protected $database;

    function __construct(){
        try {
            $this->deploy();

            $data = "mysql:host=".SQL_HOST.";dbname=".SQL_DBNAME.";charset=utf8";
            $this->database = new PDO($data, SQL_USER, SQL_PASS);

            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException$e) {
            echo $e->getMessage();
        }
    }

    /* Metodo de deploy de la DB.
       El metodo obtiene los datos del archivo SQL, lo ejecuta e instancia la base de datos de la clase
       con las constantes obtenidas del archivo config.
       Verifica si las tablas de la DB se encuentran vacías, de ser así, realiza la inserción de los registros
       correspondientes a cada tabla. */
    private function deploy(){
        try{
            $pdo = new PDO("mysql:host=".SQL_HOST."", SQL_USER, SQL_PASS);

            $sql = file_get_contents('data/database.sql');

            $pdo->exec($sql);

            $this->database = new PDO("mysql:host=".SQL_HOST.";dbname=".SQL_DBNAME."", SQL_USER, SQL_PASS);
            if($this->isTableEmpty('categories') && $this->isTableEmpty('products') && $this->isTableEmpty('users')){
                $this->insertData();
            }
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    /* Metodo de verificacion de tabla vacía. */
    private function isTableEmpty($table){
        $sql = "SELECT COUNT(*) FROM $table";
        $isCharge = $this->database->prepare($sql);
        $isCharge->execute();
        return $isCharge->fetchColumn() == 0;
    }

    /* Metodo de inserción de datos.
    Se encarga de insertar los datos en las tablas de la base de datos. */
    private function insertData(){
        try {
            $this->database->exec(
                "INSERT INTO `categories` (`catname`, `catimage`) VALUES
                ('Sin categoría asignada', 'images/imagenPorDefault.jpg'),
                ('Electrónica', 'images/imagenPorDefault.jpg'),
                ('Ropa', 'images/imagenPorDefault.jpg'),
                ('Hogar', 'images/imagenPorDefault.jpg');");

            $this->database->exec(
                "INSERT INTO `products` (`prodname`, `description`, `idcategory`, `stock`, `price`, `imgproduct`) VALUES
                ('Televisor', 'Televisor LED de 42 pulgadas', 2, TRUE, 200, 'images/img-tv.jpeg'),
                ('Camiseta', 'Camiseta de algodón 100%', 3, TRUE, 30.00, 'images/camiseta.jpeg'),
                ('Sofá', 'Sofá de 3 plazas color gris', 4, TRUE, 500.10, 'images/sofa.jpeg'),
                ('Auriculares', 'Auriculares con cancelación de ruido', 2, TRUE, 1500, 'images/auriculares.jpeg');
            ");

            $stmt = $this->database->prepare(
                "INSERT INTO `users` (`name`, `surname`, `email`, `pass`, `token`, `admin`) VALUES
                ('Admin', 'User', 'admin@admin.com', :adminpass, :admintoken, TRUE),
                ('UserComun', 'Apellido', 'user@comun.com', :userpass, :usertoken, FALSE);"
            );
            $stmt->execute([
                ':adminpass' => '$2y$10$xHn/k/qTE8tGr1iSSe3X0OOUbkSnYGTLp7BAuIKFUAb9teWs/Ybxi',
                ':userpass' => '$2y$10$hP1MFvzm7SpV6CbZX7GQDekSTWD0GyVtR0eN2icxbLHNM.QfQ0U4a',
                ':admintoken' => '646fc0d751c58050fb1d81ee8f455420',
                ':usertoken' => '80da7e7ce496db405ee6b67d87bf648d'
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /* Metodo que recibe una query y parametros de consulta(en caso de existir)
    y la ejecuta para devolver un resultado que puede variar: un booleano, un registro de una tabla, etc. */
    function executeQuery($query, $params = []){
        try {
            $action = $this->database->prepare($query);
            $action->execute($params);
            return $action;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}