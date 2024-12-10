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
                ('Televisor', 'SmartTV de 42 pulgadas con pantalla curva...', 2, TRUE, 200, 'images/television.jpeg'),
                ('Camiseta', 'Camiseta de la Selección Argentina, con numeración y nombre estampado.', 3, TRUE, 30.00, 'images/camiseta.jpeg'),
                ('Sofá', 'Sofá de 3 plazas, compuesto de espuma densa y tela simil cuero.', 4, TRUE, 500.10, 'images/sofa.jpeg'),
                ('Auriculares', 'Auriculares con cancelación de ruido y conexión inalámbrica.', 2, TRUE, 1500, 'images/auriculares.jpeg'),
                ('Campera', 'Campera de neopreno. Talle L y cierre con refuerzo.', 1, TRUE, 300, 'images/campera.jpeg'),
                ('Zapatillas', 'Zapatillas AdiPure. Talle 42. Color negro con detalles en blanco.', 3, TRUE, 21500, 'images/zapatillas.jpeg'),
                ('Remera', 'Remera oversize. Talle XL, color crudo.', 1, TRUE, 331500, 'images/remera.jpeg'),
                ('Notebook', 'Ordenador portatil con procesador i7 y memoria RAM de 32GB.', 2, TRUE, 9331500, 'images/notebook.jpeg');
            ");

            $stmt = $this->database->prepare(
                "INSERT INTO `users` (`name`, `surname`, `email`, `pass`, `token`, `admin`) VALUES
                ('Admin', 'User', 'webadmin@admin.com', :adminpass, :admintoken, TRUE),
                ('UserComun', 'Apellido', 'user@comun.com', :userpass, :usertoken, FALSE);"
            );
            $stmt->execute([
                ':adminpass' => '$2y$10$11WnltIgF5IzvPCUCH6N7uuxWJyG14M4wRgS9ji6llO04Ln20aLGK',
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