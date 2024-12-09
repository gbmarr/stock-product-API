<?php
require_once 'APIModel.php';

class CategoryAPIModel extends APIModel {

    function __construct(){
        parent::__construct();
    }

    /* Metodo que devuelve un array de objetos con los datos de
    las categorias que se encuentran en la tabla categories de la base de datos */
    function getAllCategories($field = null, $value = null, $attribute = null, $order = null, $pages = null, $lim = null){
        if(is_numeric($value)){
            $value = (float)($value);
        }else{
            $value = "'$value'";
        }

        /* array de atributos validos sobre los cuales filtrar y ordenar los registros de la consulta */
        $validAttributes = array('idcat', 'catname', 'catimage');

        /* consulta SQL base para la obtencion de todos los datos de categorias */
        $query = "SELECT `idcat`, `catname`, `catimage` FROM `categories`";

        /* si el valor de field se encuenta dentro del array, agrega a la query
        la condicion de que sean todas las categorias que tengan el valor de value en ese campo */
        if($field && in_array($field, $validAttributes)){
            $query .= " WHERE $field = $value";
        }

        /* si el valor de attribute se encuentra dentro del array, agrega a la query
        la condicion de que todos los productos retornados se ordenen por ese campo */
        if($attribute && in_array($attribute, $validAttributes)){
            $query .= " ORDER BY $attribute $order";
        }
        
        if($lim != 0 && $pages != 0){
            $query .= " LIMIT $lim OFFSET $pages";
        }

        $categories = $this->executeQuery($query);
        return $categories->fetchAll(PDO::FETCH_OBJ);
    }

    /* Metodo que devuelve un objeto con los datos de una categoria
    especifica de la base de datos mediante su ID de producto */
    function getCategory($id){
        $query = "SELECT `idcat`, `catname`, `catimage` FROM `categories` WHERE `idcat` = ?";
        
        $category = $this->executeQuery($query, [$id]);
        return $category->fetch(PDO::FETCH_OBJ);
    }

    /* Metodo que obtiene por parametro: nombre e imagen.
    Luego inserta los datos en la tabla categories como una nueva categoria */
    function addCategory($categoryName, $categoryImage){
        $query = "INSERT INTO `categories` (`catname`, `catimage`) VALUES (?, ?)";
        return $this->executeQuery($query, [$categoryName, $categoryImage]);
    }

    /* Metodo que obtiene por parametro: id de categoria, nombre e imagen.
    Luego actualiza los datos en la tabla categories en el registro que contenga el ID de categoria identico al obtenido por parametro. */
    function updateCategory($id, $categoryName, $categoryImage){
        $query = "UPDATE `categories` SET `catname` = ?, `catimage` = ? WHERE `idcat` = ?";
        $this->executeQuery($query, [$categoryName, $categoryImage, $id]);
    }

    /* Metodo que recibe ID de categoria por parametro y elimina el registro de la tabla categories
    que contenga el ID de categoria identico al obtenido por parametro. */
    function deleteCategory($id){
        $query = "DELETE FROM `categories` WHERE `idcat` = ?";
        $this->executeQuery($query, [$id]);
    }
}