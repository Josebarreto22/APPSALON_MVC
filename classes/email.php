<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
       $this->email = $email;
       $this->nombre = $nombre;
       $this->token = $token; 
    }
    public function enviarConfirmacion() {

        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '7028e2f63e449d';
        $mail->Password = '9c9152cff94ba1';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'Appsalon.com');
        $mail->Subject = 'Confirma tu cuenta';

        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        
        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->email . "</strong> Has creado tu cuenta en App
        Salón, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3001/confirmar-cuenta?token="
        . $this->token . "'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no solicitaste reestablecer tu passsword o contraseña, puedes ignorar el mensaje.</p>";
        $contenido .= '</html';
        $mail->Body = $contenido;

        // Enviar mail
        $mail->send();
    }

    public function enviarInstrucciones() {
        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '7028e2f63e449d';
        $mail->Password = '9c9152cff94ba1';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'Appsalon.com');
        $mail->Subject = 'Reestablece tu Password';

        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        
        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado reestablecer
        tu password, sigue el siguiente enlace para continuar.</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3001/recuperar?token="
        . $this->token . "'>Reestablecer Cuenta</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje.</p>";
        $contenido .= '</html';
        $mail->Body = $contenido;

        // Enviar mail
        $mail->send();
    }
}