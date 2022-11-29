<?php 

namespace Controllers;
use MVC\Router;

class CitaController {
    public static function index( Router $router) {

        session_start();

        isAuth(); // comprueba si el usuario esta autenticado

        $router->render('cita/index', [
            'nombre' => $_SESSION['nombre'],
            'id' => $_SESSION['id']
            
        ]);
    }
}