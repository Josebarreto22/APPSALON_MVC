<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class loginController {
    public static function login( Router $router) {
        $alertas = [];     

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            // Mostrar mensaje de errores
            $alertas = $auth->validarLogin();

            // Verificando si el usuario existe
            if(empty($alertas)) {
                // Comprobar si existe el usuario
                $usuario = Usuario::where('email', $auth->email);

                if($usuario) {
                    // Verificar el password
                   if( $usuario->comprobarPasswordAndVerificado($auth->password) ) {
                        // Autenticar el Usuario 
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        //  Redireccionamiento
                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide( Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1") {
                    
                    // Generar Un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar el email:
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();


                    //Alerta de exito
                    Usuario::setAlerta('exito', 'Se ha enviado correctamente a tu email');
                } else {
                    //Alerta de error
                    Usuario::setAlerta('error', 'El Usuario no existe o no esta registrado');
                }
            }
 
        }
          $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);   
    }

    public static function recuperar( Router $router) {
        $alertas = [];  
        $error = false;

        $token = s($_GET['token']);
        
        // Buscar usuario por tu token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Valido');
            // No mostrar el formulario
            $error = true;
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Leer el nuevo password y guardarlo
            
            // Instanciar
            $password = new Usuario($_POST);
            // Validar Password
            $alertas = $password->validarPassword();
            
            if(empty($alertas)) {
                // elimina el password q tiene actualmente el usuario
                $usuario->password = null;

                $usuario->password = $password->password;
                //hashea el nuevo password
                $usuario->hashPassword();
                //Eliminamos el token
                $usuario->token = null;

                // Guardar el nuevo password
                $resultado = $usuario->guardar();
                if($resultado) {
                    // Crear mensaje de exito
                    Usuario::setAlerta('exito', 'Password Actualizado Correctamente');
                                    
                    // Redireccionar al login tras 3 segundos
                    header('Refresh: 3; url=/');
                }
            
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);   
    }

    public static function crear( Router $router) {
       
        $usuario = New Usuario;

        //Alertas Vacias
        $alertas = [];


        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
          
            // Revisar que alerta este vacio
            if(empty($alertas)) {
                // verificar si ya el usuario esta registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el Password
                    $usuario->hashPassword();
                    
                    // Generar un Token Unico
                    $usuario->crearToken();

                    // Enviar El Email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear EL usuario
                    $resultado = $usuario->guardar();
                    if($resultado) {
                        header('Location: /mensaje');
                    }
                        
                    

                    
                }
            }
        }

        $router->render('auth/crear-cuenta', [  
            'usuario' => $usuario,
            'alertas' => $alertas

        ]);
    }
    public static function mensaje( Router $router) {

        $router->render('auth/mensaje');
    }

    public static function confirmar( Router $router) {
        $alertas = [];

        //sanitizar y leer token desde la url
        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario) || $usuario->token === '') {
            //Mostrar el mensaje de error
           Usuario::setAlerta('error', 'Token no Válido');
        } else {
            // modificar usuario confirmado
            //cambiar valor de columna confirmados 
            $usuario->confirmado = "1";
             //eliminar token
            $usuario->token = '';
            //Guardar y Actualizar 
            $usuario->guardar();
             //mostrar mensaje de exito
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }
        // Obtener Alertas
        $alertas = Usuario::getAlertas();

        // Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}