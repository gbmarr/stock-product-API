<?php

class ViewAPIJSON {

    /* Metodo que genera la salida de los datos de la API en formato JSON */
    public function response($data, $status){
        header('Content-Type: application/json');
        $statusText = $this->_requestStatus($status);
        
        header("HTTP/1.1 $status $statusText");
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /* Metodo que devuelve el texto del mensaje correspondiente al status de la solicitud */
    private function _requestStatus($code){
        $status = array(
            200 => 'OK',
            201 => 'Created',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            404 => 'Not found',
            500 => 'Internal Server Error',
        );

        return (isset($status[$code])) ? $status[$code] : 'Unknown Status Code';
    }
}