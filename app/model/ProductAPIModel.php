<?php
require_once 'app/model/APIModel.php';

class ProductAPIModel extends APIModel {
    private $ID_CAT_DEFAULT;
    private $IMG_DEFAULT;

    function __construct(){
        parent::__construct();
        $this->ID_CAT_DEFAULT = 1;
        $this->IMG_DEFAULT = 'images/imagenPorDefault.jpg';
    }

    /* Metodo que devuelve un array de objetos con los datos de
    los productos que se encuentran en la tabla products de la base de datos */
    function getAllProducts($field = null, $value = null, $attribute = null, $order = null, $pages = null, $lim = null){
        if(is_numeric($value)){
            $value = (float)($value);
        }else{
            $value = "'$value'";
        }
        
        /* array de atributos validos sobre los cuales filtrar y ordenar los registros de la consulta */
        $validAttributes = array('idproduct', 'prodname', 'description', 'idcategory', 'price', 'stock', 'imgproduct');

        /* consulta SQL base para la obtencion de todos los datos de productos */
        $query = "SELECT `idproduct`, `prodname`, `description`, P.`idcategory`, C.`idcat`, C.`catname` catdescription, `price`, `stock`, `imgproduct` FROM `products` P, `categories` C WHERE `idcategory` = `idcat`";

        /* si el valor de field se encuenta dentro del array, agrega a la query
        la condicion de que sean todos los productos que tengan el valor de value en ese campo */
        if($field && in_array($field, $validAttributes)){
            $query .= " AND $field = $value";
        }

        /* si el valor de attribute se encuentra dentro del array, agrega a la query
        la condicion de que todos los productos retornados se ordenen por ese campo */
        if($attribute && in_array($attribute, $validAttributes)){
            $query .= " ORDER BY $attribute $order";
        }
        
        if($lim != 0 && $pages != 0){
            $query .= " LIMIT $lim OFFSET $pages";
        }
        
        $products = $this->executeQuery($query);
        return $products->fetchAll(PDO::FETCH_OBJ);
    }

    /* Metodo que devuelve un objeto con los datos de un producto
    especifico de la base de datos mediante su ID de producto */
    function getProduct($id){
        $query = "SELECT `idproduct`, P.`prodname`, `description`, P.`idcategory`, C.`idcat`, C.`catname` catdescription, `price`, `stock`, `imgproduct` FROM `products` P, `categories` C WHERE `idcategory` = `idcat` AND `idproduct` = ?";

        $product = $this->executeQuery($query, [$id]);
        return $product->fetch(PDO::FETCH_OBJ);
    }

    /* Metodo que recibe el ID de una categoria por parametro y retorna
    todos los productos que coincidan con ese ID en su campo 'idcategory' */
    function getAllProductsByCategory($id){
        $query = "SELECT `idproduct`, P.`prodname`, `description`, P.`idcategory`, C.`idcat`, C.`catname` catdescription, `price`, `stock`, `imgproduct` FROM `products` P, `categories` C WHERE `idcategory` = `idcat` AND `idcat` = ?";
        
        $products = $this->executeQuery($query, [$id]);
        return $products->fetchAll(PDO::FETCH_OBJ);
    }

    /* Metodo que obtiene por parametro: nombre, descripcion, id de categoria, precio, stock e imagen.
    Luego inserta los datos en la tabla products como un nuevo producto */
    function addProduct($name, $desc, $idcat, $price, $stock, $imgproduct){
        $imgproduct = $this->imgValidate($imgproduct);
        
        $query = "INSERT INTO `products` (`prodname`, `description`, `idcategory`, `price`, `stock`, `imgproduct`) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->executeQuery($query, [$name, $desc, $idcat, $price, $stock, $imgproduct]);
    }

    /* Metodo que obtiene por parametro: id de producto, nombre, descripcion, id de categoria, precio, stock e imagen.
    Luego actualiza los datos en la tabla products en el registro que contenga el ID de producto identico al obtenido por parametro. */
    function updateProduct($id, $name, $desc, $idcat, $price, $stock, $imgproduct){
        $imgproduct = $this->imgValidate($imgproduct);
        
        $query = "UPDATE `products` SET `prodname` = ?, `description` = ?, `idcategory` = ?, `price` = ?, `stock` = ?, `imgproduct` = ? WHERE `idproduct` = ?";
        $this->executeQuery($query, [$name, $desc, $idcat, $price, $stock, $imgproduct, $id]);
    }

    /* Metodo que recibe ID de producto por parametro y actualiza su ID de categoria
    con el ID DEFAULT definido en la clase ProductAPIModel. */
    // function updateProductCategory($id){
    //     $query = "UPDATE `products` SET `idcategory` = ? WHERE `idproduct` = ?";
    //     $this->executeQuery($query, [$this->ID_CAT_DEFAULT, $id]);
    // }

    /* Metodo que recibe ID de producto por parametro y elimina el registro de la tabla products
    que contenga el ID de producto identico al obtenido por parametro. */
    function deleteProduct($id){
        $query = "DELETE FROM `products` WHERE `idproduct` = ?";
        $this->executeQuery($query, [$id]);
    }

    /* Metodo que recibe por parametro la ruta de la imagen y en caso de
    que la imagen sea nula, le asigna el valor de la imagen default de la clase.
    En ambos casos retorna la imagen, ya sea la original o la seteada por default. */
    function imgValidate(String $image){
        if($image == null){
            $image = $this->IMG_DEFAULT;
        }
        return $image;
    }
}