<?php
require_once 'app/view/ViewAPIJSON.php';

class ControllerAPI {
    protected $view;
    protected $data;
    protected $model;

    function __construct(){
        $this->view = new ViewAPIJSON();
        $this->model = new AuthAPIModel();
    }

    /* Metodo que decodifica los datos JSON, los almacena en la propertie
    data de la clase y la retorna */
    protected function getData(){
        $this->data = json_decode(file_get_contents('php://input'));
        return $this->data;
    }

    /* Metodo que recibe un token por parametro.
    En caso de encontrar un registro en la DB que contenga el mismo codigo token
    devuelve el token del usuario asociado, en cualquier otro caso retorna falso. */
    protected function validateToken($token){
        if(!$token){
            return false;
        }
        
        try{
            $query = "SELECT `token` FROM `users` WHERE `token` = ?";
            $result = $this->model->executeQuery($query, [$token]);
            if($result && $result->rowCount() > 0){
                return $result->fetch(PDO::FETCH_OBJ);
            }else{
                return false;
            }
        }catch(Exception $e){
            return false;
        }
    }

    /* Metodo que devuelve un array cargado con los datos asociados a los
    parametros de la URL actual. */
    protected function getParams(){
        $params = [
            'filter-field' => $_GET['filter'] ?? null,
            'filter-value' => $_GET['value'] ?? null,
            'attribute' => $_GET['attribute'] ?? null,
            'order' => $_GET['order'] ?? null,
            'limit' => $_GET['limit'] ?? null,
            'pages' => $_GET['pages'] ?? null
        ];
        return $params;
    }

    /* Metodo que recibe un ID por parametro.
    En caso de un ID null, retorna mensaje de error con codigo 404,
    en caso contrario retorna el ID. */
    protected function validateIDParam($id){
        if($id === null){
            $this->view->response("ID de categoría no proporcionado.", 404);
            return;
        }
        return $id;
    }

    /*
    Metodo que recibe el header por parametro y verifica la autenticacion del usuario.
    En caso de cumplirse las siguientes condiciones se retorna mensaje de error + código 401:
        * En caso de un header nulo.
        * Si la cantidad de elementos del array header es distinta a 2. (Previamente transforma el header en un array)
        * En caso de que el elemento en la primera posición del array sea distinta a 'Bearer'.
        * En caso de que el token del array no sea el mismo que el token del usuario en la DB.
    */
    protected function verifyAuth($authHeader = null){
        if($authHeader === null){
            $this->view->response("Su token de autenticación no fue proporcionado.", 401);
            return;
        }
        
        $authHeader = explode(' ', $authHeader);
            
        if(count($authHeader) != 2){
            $this->view->response("Su token debe estar compuesto de la siguiente manera: 'Bearer codigo_token'.", 401);
            return;
        }elseif($authHeader[0] != 'Bearer'){
            $this->view->response("Asegurese de que su token se vea de la siguiente manera: 'Bearer codigo_token'.", 401);
            return;
        }

        $token = $authHeader[1];
        if(!$this->validateToken($token)){
            $this->view->response("Token no válido, intentelo nuevamente y de persistir el error contacte a su desarrollador amigo.", 401);
            return;
        }
    }
}