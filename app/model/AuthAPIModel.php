<?php
require_once 'app/model/APIModel.php';

class AuthAPIModel extends APIModel {

    /* Metodo que obtiene un email(string) por parametro y en caso de existir coincidencia
    con algun registro de la tabla users de la DB, retorna el usuario correspondiente. */
    function getUser($userEmail){
        try{
            $query = "SELECT `email`, `pass`, `admin` FROM `users` WHERE `email` = ?";
            $user = $this->executeQuery($query, [$userEmail]);
            
            return $user->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e){
            return $e->getMessage();
        }
    }

    /* Metodo que recibe email y pass por parametro, obtiene el usuario de la tabla
    users mediante el email.
    En caso de existencia del usuario y coincidencia entre
    el password obtenido por parametro y el del usuario, genera un token, lo actualiza
    en el usuario y lo retorna. */
    public function validateUser($email, $pass){
        $user = $this->getUser($email);

        if($user && password_verify($pass, $user->pass)){
            $token = bin2hex(random_bytes(16));
            $this->updateUserToken($user->email, $token);
            return $token;
        }

        return null;
    }

    /* Metodo que recibe email y token por parametro.
    El metodo actualiza el token del usuario de la tabla users que contiene el email. */
    private function updateUserToken($email, $token){
        $query = "UPDATE `users` SET `token` = ? WHERE `email` = ?";
        $this->executeQuery($query, [$token, $email]);
    }
}