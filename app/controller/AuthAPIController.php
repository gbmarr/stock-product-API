<?php
require_once 'app/model/AuthAPIModel.php';
require_once 'app/controller/ControllerAPI.php';

class AuthAPIController extends ControllerAPI {
    protected $authModel;

    function __construct(){
        parent::__construct();
        $this->authModel = new AuthAPIModel();
    }

    /* Metodo que obtiene los datos decodificados del user y los almacena
    en una variable.
    Luego valida el usuario y obtiene su token, si el token existe muestra mensaje
    de respuesta satisfactorio con codigo 200, sino muestra mensaje de error de
    credenciales y codigo 401. */
    public function login(){
        try{

            $user = $this->getData();
            $token = $this->authModel->validateUser($user->email, $user->pass);
            
            if($token){
                $this->view->response("Usuario logueado satisfactoriamente con token: $token" , 200);
            }else{
                $this->view->response('Credenciales no válidas, intentelo nuevamente.', 401);
            }
        }catch(Exception $e){
            $this->view->response($e->getMessage(), 500);
        }
    }
}