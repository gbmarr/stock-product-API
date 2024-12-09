<?php

class Route {
    private $url;
    private $verb;
    private $controller;
    private $method;
    private $params;

    function __construct($url, $verb, $controller, $method){
        // $this->url = $url;
        // $this->verb = $verb;
        $this->url = trim($url, "/");
        $this->verb = strtoupper($verb);
        $this->controller = $controller;
        $this->method = $method;
        $this->params = [];
    }

    /* Metodo que verifica la la coincidencia entre la URL de la instancia
    y la URL pasada por parametro. Si coinciden, llama al metodo correspondiente
    del controlador. */
    public function match($url, $verb){
        if($this->verb != strtoupper($verb)){
            return false;
        }

        $urlSegments = explode("/", trim($url, "/"));
        $routeSegments = explode("/",trim($this->url, "/"));
        if(count($urlSegments) != count($routeSegments)) {
            return false;
        }

        foreach ($routeSegments as $index => $segment){
            if(!empty($segment) && $segment[0] == ":"){
                if(isset($urlSegments[$index])){
                    $this->params[substr($segment,1)] = $urlSegments[$index];
                }
            }elseif($segment != $urlSegments[$index]){
                return false;
            }
        }
        return true;
    }

    /* Metodo que genera una nueva instancia de controlador y llama al metodo correspondiente del mismo */
    public function run(){
        $controller = $this->controller;
        $method = $this->method;
        $params = $this->params;
        (new $controller())->$method($params);
    }
}

/* --------------------------------------------------------------- */
class Router {
    private $routes = [];
    private $default;

    function __construct(){
        // $this->default = new Route("/", "GET", "ProductAPIController", "viewAllProducts");
        $this->setDefaultRoute("ProductAPIController", "viewAllProducts");
    }

    /* Metodo que recibe URL por parametro y recorre el arreglo de rutas
    en busca de una coincidencia. En caso de existir, se ejecuta el metodo run */
    public function route($url, $verb){
        foreach($this->routes as $route){
            if($route->match($url, $verb)){
                $route->run();
                return;
            }
        }
        if($this->default != null){
            $this->default->run();
        }
    }

    /* Metodo que agrega una nueva ruta a la lista de rutas
    con su URL, verbo, controlador y metodo del controlador */
    public function addRoute($url, $verb, $controller, $method){
        $this->routes[] = new Route($url, $verb, $controller, $method);
    }

    /* Metodo que setea la ruta por default a la que se va a acceder */
    public function setDefaultRoute($controller, $method){
        $this->default = new Route('', '', $controller, $method);
    }
}