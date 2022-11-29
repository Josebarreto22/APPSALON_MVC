<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController {
    public static function index() {
        //consultamos la base de datos
        $servicios = Servicio::all();
        // json transporte
        echo json_encode($servicios);
    }

    public static function guardar() {
        
        // Almacena la cita y Deulve el ID
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado['id'];

        // Almacena los servicios con el ID de la cita
        $idServicios = explode(",", $_POST['servicios']);

        foreach($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        echo json_encode(['resultado' => $resultado]);
    }

    public static function  eliminar() {
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leemos el ID
            $id = $_POST['id'];
            // Lo encontramos
            $cita = Cita::find($id);
            // Y lo eliminamos
            $cita->eliminar();
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }
}