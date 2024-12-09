<?php
require_once 'router/Router.php';
require_once 'app/controller/AuthAPIController.php';
require_once 'app/controller/CategoryAPIController.php';
require_once 'app/controller/ProductAPIController.php';

/*
* Router configuration */
$router = new Router();

/* Endpionts(EP) de la API para el recurso Products */

/* EP que obtiene todos los productos existentes en la tabla Products de la base de datos */
$router->addRoute('products', 'GET', 'ProductAPIController', 'viewAllProducts');
/* EP que obtiene un producto, en caso de existir, de la tabla Products de la base de datos mediante su ID de producto */
$router->addRoute('products/:ID', 'GET', 'ProductAPIController', 'viewProduct');
/* EP que agrega a la tabla Products un nuevo producto */
$router->addRoute('products', 'POST', 'ProductAPIController', 'newProduct');
/* EP que guarda modificaciones y cambios en datos previos de un producto seleccionado mediante su ID de producto */
$router->addRoute('products/:ID', 'PUT', 'ProductAPIController', 'updateProduct');
/* EP que elimina un producto de la base de datos, en caso de existir, a través de su ID de producto */
$router->addRoute('products/:ID', 'DELETE', 'ProductAPIController', 'deleteProduct');

/* Endpionts(EP) de la API para el recurso Products */

/* EP que obtiene todas las categorias existentes en la tabla Categories de la base de datos */
$router->addRoute('categories', 'GET', 'CategoryAPIController', 'viewAllCategories');
/* EP que obtiene una categoria, en caso de existir, de la tabla Categories de la base de datos mediante su ID de categoría */
$router->addRoute('categories/:ID', 'GET', 'CategoryAPIController', 'viewCategory');
/* EP que agrega a la tabla Categories una nueva categoria */
$router->addRoute('categories', 'POST', 'CategoryAPIController', 'newCategory');
/* EP que guarda modificaciones y cambios en datos previos de una categoria seleccionada mediante su ID de categoria */
$router->addRoute('categories/:ID', 'PUT', 'CategoryAPIController', 'updateCategory');
/* EP que elimina un categoria de la base de datos, en caso de existir, a través de su ID de categoria */
$router->addRoute('categories/:ID', 'DELETE', 'CategoryAPIController', 'deleteCategory');
/* EP que loguea y valida que los datos y credenciales del usuario sean los correctos */
$router->addRoute('auth/login', 'POST', 'AuthAPIController', 'login');

$router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);