<?php
require_once 'app/model/CategoryAPIModel.php';
require_once 'app/controller/ControllerAPI.php';

class CategoryAPIController extends ControllerAPI {
    protected $categoryModel;

    function __construct(){
        parent::__construct();
        $this->categoryModel = new CategoryAPIModel();
    }

    /* Metodo que obtiene todas las categorias */
    public function viewAllCategories(){
        try{
            $params = $this->getParams(); /* se obtiene y almacena el array de parametros de la URL */
            
            $categories = $this->categoryModel->getAllCategories(
                $params['filter-field'], $params['filter-value'], $params['attribute'],
                $params['order'], $params['pages'], $params['limit']);
            
            if($categories){
                /* en caso de existencia de categorias, se toma cada categoria y se la guarda
                en un arreglo de JSON para luego ser retornado */
                $jsonCategories = [];
                foreach ($categories as $category) {
                    array_push($jsonCategories, $this->jsonFormat($category));
                }
                
                /* se muestran las categorias que se encuentren en el arreglo */
                $this->view->response($jsonCategories, 200);
            }else{
                /* en caso de no existir productos, se muestra mensaje + codigo 404 */
                $this->view->response("No se encontraron categorías.", 404);
            }
        }catch(Exception $e){
            $this->view->response("Error de servidor, contacte a su desarrollador amigo.", 500);
        }
    }

    /* Metodo que obtiene un ID por parametro y retorna la categoria coincidente */
    public function viewCategory($param = null){
        $id = $this->validateIDParam($param['ID']); /* verificacion de ID valido */

        try{
            $category = $this->categoryModel->getCategory($id); /* se obtiene la categoria de la DB */

            if($category){
                /* en caso de existencia de categoria, se la guarda en formato JSON para luego ser retornado */
                $jsonCategory = $this->jsonFormat($category);
                $this->view->response($jsonCategory, 200);
            }else{
                /* en caso de no existir categoria, se muestra mensaje + codigo 404 */
                $this->view->response("No se encontró la categoría solicitada.", 404);
            }
        }catch(Exception $e){
            $this->view->response("Error de servidor, contacte a su desarrollador amigo.", 500);
        }
    }

    /* Metodo que realiza el POST para guardar una nueva categoria en la DB */
    public function newCategory(){
        try{
            /* se autentifica el usuario que realiza la solicitud a partir de su key(token) */
            $this->verifyAuth($_SERVER['HTTP_AUTHORIZATION']);

            $category = $this->getData(); /* se obtienen los datos para almacenarlos en la nueva categoria */

            if(isset($category->catname) || isset($category->catimage)){
                /* se verifica que se hayan ingresado todos los datos requeridos
                para poder guardar la categoria, de ser asi, se la crea. */
                $newCategory = $this->categoryModel->addCategory($category->catname, $category->catimage);
                
                if($newCategory){
                    /* en caso de exito, se muestra el mensaje de categoria creada correctamente */
                    $this->view->response("Categoría creada satisfactoriamente.", 201);
                }else{
                    /* en caso de error, se muestra mensaje de error + codigo 500 */
                    $this->view->response("La categoría no ha podido ser creada, intentelo nuevamente.", 500);
                }
                    
            }else{
                /* en caso de no ingresarse alguno de los datos requeridos, se muestra mensaje de
                error + codigo 400 */
                $this->view->response("Error al completar los datos de la categoría, asegurese de completar todos los campos.", 400);
            }
        }catch(Exception $e){
            $this->view->response("Error de servidor, contacte a su desarrollador amigo.", 500);
        }
    }

    /* Metodo que recibe un ID por parametro y realiza el PUT para actualizar
    una categoria existente en la DB que contenga el mismo ID de categoria */
    public function updateCategory($param = null){
        $id = $this->validateIDParam($param['ID']); /* verificacion de ID valido */

        try{
            /* se autentifica el usuario que realiza la solicitud a partir de su key(token) */
            $this->verifyAuth($_SERVER['HTTP_AUTHORIZATION']);
            
            /* se obtiene la categoria que contenga el mismo ID de categoria que el obtenido por parametro */
            $oldCategory = $this->categoryModel->getCategory($id);
            
            if($oldCategory){
                /* si la categoria existe se obtienen los datos para actualizar la categoria */
                $category = $this->getData();
                
                if(isset($category->catname) || isset($category->catimage)){
                    /* se verifica que se hayan ingresado todos los datos requeridos
                    para poder guardar los nuevos datos de la categoria, de ser asi, se la actualiza
                    y se muestra mensaje de actualizacion correcta */
                    $this->categoryModel->updateCategory($id, $category->catname, $category->catimage);
                    $this->view->response("Categoría actualizada satisfactoriamente.", 200);
                }else{
                    /* en caso de no ingresarse alguno de los datos requeridos, se muestra mensaje de
                    error + codigo 400 */
                    $this->view->response("Error al completar los datos de la categoría, asegurese de completar todos los campos.", 400);
                }
            }else{
                /* en caso de no existir la categoria, se muestra mensaje de error + codigo 404 */
                $this->view->response("No se encontró la categoría solicitada para actualizar.", 404);
            }
        }catch(Exception $e){
            $this->view->response("Error de servidor, contacte a su desarrollador amigo.", 500);
        }
    }

    /* Metodo que recibe un ID por parametro y realiza el DELETE para eliminar
    una categoria existente en la DB que contenga el mismo ID de categoria */
    public function deleteCategory($param = null){
        $id = $this->validateIDParam($param['ID']); /* verificacion de ID valido */

        try{
            /* se obtiene la categoria que contenga el mismo ID de categoria que el obtenido por parametro */
            $category = $this->categoryModel->getCategory($id);
            
            if($category){
                /* si la categoria existe se elimina la categoria de la DB
                y muestra mensaje de eliminacion correcta */
                $this->categoryModel->deleteCategory($id);
                $this->view->response("Categoría eliminada satisfactoriamente.", 200);
            }else{
                $this->view->response("No se encontró la categoría solicitada.", 404);
            }
        }catch(Exception $e){
            $this->view->response("Error de servidor, contacte a su desarrollador amigo.", 500);
        }
    }

    /* Metodo que recibe un objeto de tipo categoria por parametro
    y se lo retorna en formato JSON */
    private function jsonFormat($category){
        $jsonFormat = [
            "id" => $category->idcat,
            "category name" => $category->catname,
            "catimage" => $category->catimage
        ];
        return $jsonFormat;
    }
}