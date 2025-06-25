<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class MY_Controller extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Colombo');
        $this->company_name = 'Make-Kit | '; 
        /*if($_SERVER["HTTPS"] != "on"){
            header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
            exit();
        }*/
    }
    
    function get_encrypted_password($password) {
        $options = [
            'cost' => 10
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }
}

