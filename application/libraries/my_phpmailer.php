<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class My_PHPMailer {

    public function My_PHPMailer() {
        require_once('PHPMailer/class.phpmailer.php');
    }

}
