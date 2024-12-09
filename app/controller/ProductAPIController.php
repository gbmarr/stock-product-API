<?php
require_once 'app/model/ProductAPIModel.php';
require_once 'app/controller/ControllerAPI.php';

class ProductAPIController extends ControllerAPI {
    protected $productModel;

    function __construct(){
        parent::__construct();
        $this->productModel = new ProductAPIModel();
    }

    /* Metodo que obtiene todos los productos */
    public function viewAllProducts(){
        try{
            $params = $this->getParams(); /* se obtiene y almacena el array de parametros de la URL */

            $products = $this->productModel->getAllProducts(
                $params['filter-field'], $params['filter-value'], $params['attribute'],
                $params['order'], $params['pages'], $params['limit']);
            
            if($products){
                /* en caso de existencia de productos, se toma cada producto y se lo guarda
                en un arreglo de JSON para luego ser retornado */
                $jsonProducts = [];
                foreach ($products as $product) {
                    array_push($jsonProducts, $this->jsonFormat($product));
                }

                /* se muestran los productos que se encuentren en el arreglo */
                $this->view->response($jsonProducts, 200);
            }else{
                /* en caso de no existir productos, se muestra mensaje + codigo 404 */
                $this->view->response("No se encontraron productos.", 404);
            }
        }catch(Exception $e){
            $this->view->response("Error de servidor, contacte a su desarrollador amigo.", 500);
        }
    }

    /* Metodo que obtiene un ID por parametro y retorna el producto coincidente */
    public function viewProduct($param = null){
        $id = $this->validateIDParam($param['ID']); /* verificacion de ID valido */

        try{
            $product = $this->productModel->getProduct($id); /* se obtiene el producto de la DB */

            if($product){
                /* en caso de existencia de producto, se lo guarda en formato JSON para luego ser retornado */
                $jsonProduct = $this->jsonFormat($product);
                $this->view->response($jsonProduct, 200);
            }else{
                /* en caso de no existir producto, se muestra mensaje + codigo 404 */
                $this->view->response("No se encontró el producto solicitada", 404);
            }
        }catch(Exception $e){
            $this->view->response("Error de servidor, contacte a su desarrollador amigo.", 500);
        }
    }

    /* Metodo que realiza el POST para guardar un nuevo producto en la DB */
    public function newProduct(){
        try {
            /* se autentifica el usuario que realiza la solicitud a partir de su key(token) */
            $this->verifyAuth($_SERVER['HTTP_AUTHORIZATION']);

            $product = $this->getData(); /* se obtienen los datos para almacenarlos en el nuevo producto */
            
            if(isset($product->prodname) || isset($product->description) || isset($product->idcategory) || isset($product->price) || isset($product->stock) || isset($product->imgproduct)){
                /* se verifica que se hayan ingresado todos los datos requeridos
                para poder guardar el producto, de ser asi, se lo crea. */
                $newProduct = $this->productModel->addProduct($product->prodname, $product->description, $product->idcategory, $product->price, boolval($product->stock), $product->imgproduct);
                
                if($newProduct){
                    /* en caso de exito, se muestra el mensaje de producto creado correctamente */
                    $this->view->response("Producto creado satisfactoriamente", 201);
                }else{
                    /* en caso de error, se muestra mensaje de error + codigo 500 */
                    $this->view->response("El producto no ha podido ser creado, intentelo nuevamente.", 500);
                }

            }else{
                /* en caso de no ingresarse alguno de los datos requeridos, se muestra mensaje de
                error + codigo 400 */
                $this->view->response("Error al completar los datos del producto, asegurese de completar todos los campos.", 400);
            }
        } catch (Exception $e) {
            $this->view->response("Error de servidor, contacte a su desarrollador amigo.", 500);
        }
    }

    /* Metodo que recibe un ID por parametro y realiza el PUT para actualizar
    un producto existente en la DB que contenga el mismo ID de producto */
    public function updateProduct($param = null){
        $id = $this->validateIDParam($param['ID']); /* verificacion de ID valido */

        try{
            /* se autentifica el usuario que realiza la solicitud a partir de su key(token) */
            $this->verifyAuth($_SERVER['HTTP_AUTHORIZATION']);
            
            /* se obtiene el producto que contenga el mismo ID de producto que el obtenido por parametro */
            $oldProduct = $this->productModel->getProduct($id);

            if($oldProduct){
                /* si el producto existe se obtienen los datos para actualizar el producto */
                $product = $this->getData();
                
                if(isset($product->prodname) || isset($product->description) || isset($product->idcategory) || isset($product->price) || isset($product->stock) || isset($product->imgproduct)){
                    /* se verifica que se hayan ingresado todos los datos requeridos
                    para poder guardar los nuevos datos del producto, de ser asi, se lo actualiza
                    y se muestra mensaje de actualizacion correcta */
                    $this->productModel->updateProduct($id, $product->prodname, $product->description, $product->idcategory, $product->price, $product->stock, $product->imgproduct);
                    $this->view->response("Producto actualizado satisfactoriamente.", 200);
                }else{
                    /* en caso de no ingresarse alguno de los datos requeridos, se muestra mensaje de
                    error + codigo 400 */
                    $this->view->response("Error al completar los datos del producto, asegurese de completar todos los campos.", 400);
                }
            }else{
                /* en caso de no existir el producto, se muestra mensaje de error + codigo 404 */
                $this->view->response("No se encontró el producto solicitado para actualizar.", 404);
            }
        }catch(Exception $e){
            $this->view->response("Error de servidor, contacte a su desarrollador amigo.", 500);
        }
    }

    /* Metodo que recibe un ID por parametro y realiza el DELETE para eliminar
    un producto existente en la DB que contenga el mismo ID de producto */
    public function deleteProduct($param = null){
        $id = $this->validateIDParam($param['ID']); /* verificacion de ID valido */

        try{
            /* se obtiene el producto que contenga el mismo ID de producto que el obtenido por parametro */
            $product = $this->productModel->getProduct($id);
            
            if($product){
                /* si el producto existe se elimina el producto de la DB
                y muestra mensaje de eliminacion correcta */
                $this->productModel->deleteProduct($id);
                $this->view->response("Product eliminado satisfactoriamente.", 200);
            }else{
                $this->view->response("No se encontró el producto solicitado.", 404);
            }
        }catch(Exception $e){
            $this->view->response("Error de servidor, contacte a su desarrollador amigo.", 500);
        }
    }

    /* Metodo que recibe un objeto de tipo producto por parametro
    y se lo retorna en formato JSON */
    private function jsonFormat($product){
        $jsonformat = [
            "id" => $product->idproduct,
            "product name" => $product->prodname,
            "description" => $product->description,
            "category" => $product->catdescription,
            "price" => $product->price,
            "stock" => $product->stock,
            "image" => $product->imgproduct
        ];
        return $jsonformat;
    }
}