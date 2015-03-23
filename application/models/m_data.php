<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class M_data extends CI_Model {

    //var $sesion = null;

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->helper('file');
    }

    function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    function object_to_array($obj) {
        $arrObj = is_object($obj) ? get_object_vars($obj) : $obj;
        foreach ($arrObj as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }

    function get_all_post() {
        $post = array();
        foreach ($_POST as $key => $value) {
            $data = ($this->input->post($key));
            if (!empty($data) or $data == 0) {
                $post[$key] = (($data));
            }
        }
        $post = (object) $post;
        return $post;
    }

    function get_all_get() {
        $post = array();
        foreach ($_GET as $key => $value) {
            $data = ($this->input->get($key));
            if (!empty($data) or $data == 0) {
                $post[$key] = (($data));
            }
        }
        $post = (object) $post;
        return $post;
    }

    function get_all_server() {
        $post = array();
        foreach ($_SERVER as $key => $value) {
            //$data = ($this->input->get($key));
            if (!empty($value)) {
                $post[$key] = (($value));
            }
        }
        $post = (object) $post;
        return $post;
    }

    function replaceIntro($text) {
        return str_replace("\r", "&#13;", $text);
    }

    function replaceSymbols($text) {
        $text = str_replace('#', '&#35', $text);
        $text = str_replace('&', '&#38;', $text);
        $text = str_replace(';', '&#59', $text);

        //$text = str_replace(' ', '&#32;', $text);
        $text = str_replace('!', '&#33;', $text);
        $text = str_replace('"', '&#34;', $text);
        $text = str_replace('$', '&#36;', $text);
        $text = str_replace('%', '&#37;', $text);
        $text = str_replace("'", '&#39', $text);
        $text = str_replace('(', '&#40;', $text);
        $text = str_replace(')', '&#41;', $text);
        $text = str_replace('*', '&#42;', $text);
        $text = str_replace('+', '&#43', $text);
        $text = str_replace(',', '&#44;', $text);
        $text = str_replace('-', '&#45;', $text);
        $text = str_replace('.', '&#46;', $text);
        $text = str_replace('/', '&#47', $text);
        $text = str_replace(':', '&#58;', $text);
        $text = str_replace('<', '&#60;', $text);
        $text = str_replace('=', '&#61;', $text);
        $text = str_replace('>', '&#62;', $text);
        $text = str_replace('?', '&#63', $text);
        $text = str_replace('[', '&#91', $text);
        $text = str_replace('\\', '&#92;', $text);
        $text = str_replace(']', '&#93;', $text);
        $text = str_replace('^', '&#94;', $text);
        $text = str_replace('_', '&#95', $text);
        $text = str_replace('`', '&#96', $text);
        $text = str_replace('{', '&#123;', $text);
        $text = str_replace('|', '&#124;', $text);
        $text = str_replace('}', '&#125', $text);
        $text = str_replace('~', '&#126', $text);

        return $text;
    }

    function createDateRangeArray($strDateFrom, $strDateTo) {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.
        // could test validity of dates here but I'm already doing
        // that in the main script
        $begin1 = new DateTime($strDateFrom);
        $end1 = new DateTime($strDateTo);
        $aryRange = array();

        $interval1 = DateInterval::createFromDateString('1 day');
        $fechas = new DatePeriod($begin1, $interval1, $end1);

        //var_dump(sizeof($fechas));

        if (sizeof($fechas) == 1) {
            $aryRange[] = $strDateFrom;
        } else {
            foreach ($fechas as $key => $fecha) {
                $aryRange[] = $fecha->format("Y-m-d");
            }
        }

        return $aryRange;
    }

    function get_all_from_table_form($table_name) {
        $query = $this->db->get($table_name);
        $result = (object) array();
        foreach ($query->result() as $row) {
            //array_push($result, $row);
            $index = $row->id;
            $result->$index = $row->nombre;
        }
        //$result = (object) $result;
        return $result;
    }

    function send_mail($email_from, $email_to, $nombre_from, $str_asunto, $str_html_content) {

        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => "smtp.mailgun.org",
            'smtp_port' => 587,
            'smtp_user' => 'postmaster@ummus.co',
            'smtp_pass' => '2xa4o71b5g52',
            'mailtype' => 'html',
                //'charset' => 'iso-8859-1'
        );

        $this->load->library('email', $config);
        //$this->email->initialize($config);

        $this->email->from($email_from, $nombre_from);
        $this->email->to($email_to);
        //$this->email->cc('another@another-example.com');
        //$this->email->bcc('them@their-example.com');

        $this->email->subject($str_asunto);



        $this->email->message($this->mesaje_mail($str_html_content));

        $this->email->send();
        $strdate = date("Y-m-d H_i_s");

        if (!write_file("email_log/$email_to $strdate.txt", $this->email->print_debugger())) {
            //echo 'Unable to write the file';
        } else {
            //echo 'File written!';
        }

        //echo $this->email->print_debugger();
    }

    function mesaje_mail($str_content) {
        $file = read_file('./data/plantillas/email_registro.html');
        $array_file = explode("<!-- [data] -->", $file);
        $str_mensaje = $array_file[0] . $str_content;
        unset($array_file[0]);
        $str_mensaje .= implode("", $array_file);
        return $str_mensaje;
    }

    function send_mail2($email_from, $email_to, $nombre_from, $str_asunto, $str_html_content) {
        $this->load->library('My_PHPMailer');
        $mail = new PHPMailer();
        $mail->IsSMTP(); // establecemos que utilizaremos SMTP
        $mail->SMTPAuth = true; // habilitamos la autenticación SMTP
        $mail->SMTPSecure = "ssl";  // establecemos el prefijo del protocolo seguro de comunicación con el servidor
        $mail->Host = "smtpout.secureserver.net";      // establecemos GMail como nuestro servidor SMTP
        $mail->Port = 465;                   // establecemos el puerto SMTP en el servidor de GMail
        $mail->Username = "info@ummus.com.co";  // la cuenta de correo GMail
        $mail->Password = "CharalA@0407";            // password de la cuenta GMail
        $mail->SetFrom($email_from, $nombre_from);  //Quien envía el correo
        $mail->AddReplyTo($email_from, $nombre_from);  //A quien debe ir dirigida la respuesta
        $mail->Subject = $str_asunto;  //Asunto del mensaje
        $mail->Body = $str_html_content;
        //$mail->AltBody = "Cuerpo en texto plano";
        $destino = $email_to;
        $mail->AddAddress($destino, "UMMUS");

        //$mail->AddAttachment("images/phpmailer.gif");      // añadimos archivos adjuntos si es necesario
        //$mail->AddAttachment("images/phpmailer_mini.gif"); // tantos como queramos

        if (!$mail->Send()) {
            return "Error en el envío: " . $mail->ErrorInfo;
        } else {
            return "¡Mensaje enviado correctamente!";
        }
    }

    /**
     * Calcula la suma del cuadrado de un arreglo
     *
     * Ciclo que recorre el arreglo, obtiene el valor lo eleva al cuadrado y se lo
     * suma y retorna el total
     *
     * @param string $nombre_tabla El nombre de la tabla al que se van a verificar los atributos
     * @param stdClass $data datos a verificar para ser ingresados
     * @param boolean $is_detalle_json comprueba el valor para excluir todo lo que no corresponda a la tabla (si es false) o meter todo en un json (si es true) y guardarlo en el campo 'detalles'
     * @return stdClass
     */
    function crear_input($nombre_tabla, $data, $is_detalle_json = false) {
        $fields = $this->db->list_fields($nombre_tabla);
        $input = array();
        foreach ($fields as $value) {
            if (isset($data->$value)) {
                if (!empty($data->$value)) {
                    $input[$value] = $data->$value;
                    unset($data->$value);
                } else {
                    unset($data->$value);
                }
            } elseif ($value == 'usuario_id') {
                $input['usuario_id'] = $this->m_sesion->obtener_id_sesion();
            }
        }

        if (!empty($data)) {
            if ($is_detalle_json) {
                $input['detalles'] = json_encode($data);
            } else {
                $input['detalles'] = ($data);
            }
        }

        $input = (object) $input;
        return $input;
    }

    function read_file($string_rute, $isAbsolutePath = false) {
        $resp = null;
        if ($isAbsolutePath) {
            $resp = file_get_contents($string_rute);
        } else {
            $resp = file_get_contents(base_url() . $string_rute);
        }
        return $resp;
    }

    function crea_form($string_form) {
        $id_unico = md5(uniqid(rand(), true));
        $data['array'] = json_decode($string_form, true);
        $boolPasa = false;
        $resp = array();
        // <editor-fold defaultstate="collapsed" desc="errores en json">
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $boolPasa = true;
                //echo ' - Sin errores';
                break;
            case JSON_ERROR_DEPTH:
                echo ' - Excedido tamaño máximo de la pila';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                echo ' - Desbordamiento de buffer o los modos no coinciden';
                break;
            case JSON_ERROR_CTRL_CHAR:
                echo ' - Encontrado carácter de control no esperado';
                break;
            case JSON_ERROR_SYNTAX:
                echo $string_form;
                echo ' - Error de sintaxis, JSON mal formado';
                break;
            case JSON_ERROR_UTF8:
                echo ' - Caracteres UTF-8 malformados, posiblemente están mal codificados';
                break;
            default:
                echo ' - Error desconocido';
                break;
        }// </editor-fold>

        if ($boolPasa) {

            $resp['id_unico'] = "$id_unico";
            $resp['extra'] = "";
            $resp['extra2'] = "";

            foreach ($data['array'] as $key => $value) {

                $error_message = 'Este campo es necesario';
                $require = 'required';
                if (isset($value['required'])) {
                    if ($value['required']) {
                        $require = 'required';
                        $error_message = 'Este campo es necesario';
                        if (isset($value['error_message'])) {
                            $error_message = $value['error_message'] . '';
                        }
                    } else {
                        $require = '';
                    }
                }

                if (isset($value['equalTo'])) {
                    $resp['extra'] .= $key . ': {equalTo: "#' . $id_unico . ' [name=' . "'" . $value['equalTo'] . "'" . ']"}, ';
                }

                if (isset($value['table_out']) and isset($value['table_in'])) {
                    $value['value'] = $this->get_all_from_table_form($value['table_out']);
                    //var_dump($value['value']);
                    array_push($resp, '<input readonly type="hidden" name="table_in[' . $key . ']" value="' . $value['table_in'] . '">');
                    //$resp['extra2'] .= '$(' . "'" . '[name=*"' . $key . '"]' . "'" . ').parents("tr:eq(0)").css("display","none")';
                }

                switch ($value['type']) {
                    default:
                        //var_dump($value);
                        break;
                    case "button":
                        $direc = "";
                        if ($value['site'] == "relative") {
                            $direc = site_url($value['value']);
                        } elseif ($value['site'] == "absolute") {
                            $direc = $value['value'];
                        }
                        $resp['extra2'] .= '$("#' . $id_unico . ' .' . $key . '").on("click",'
                                . 'function(event){  '
                                . 'event.preventDefault();'
                                . '$("#' . $id_unico . '").parents("form:eq(0)").attr("action","' . $direc . '");'
                                . '$("#' . $id_unico . '").parents("form:eq(0)").submit();'
                                . '  });' . "\n";
                        array_push($resp, '<button href="' . $direc . '" class="btn ' . @$value['class'] . ' ' . $key . '" >' . @$value['label'] . '</button>');
                        break;
                    case "action":
                        $direc = "";
                        if ($value['site'] == "relative") {
                            $direc = site_url($value['value']);
                        } elseif ($value['site'] == "absolute") {
                            $direc = $value['value'];
                        }
                        $resp['extra2'] .= '$("#' . $id_unico . '").parents("form:eq(0)").attr("action","' . $direc . '");' . "\n";
                        break;

                    case "verify":
                        $direc = "";
                        if ($value['site'] == "relative") {
                            $direc = site_url($value['value']);
                        } elseif ($value['site'] == "absolute") {
                            $direc = $value['value'];
                        }
                        $resp['extra2'] .= '$("#' . $id_unico . '").parents("form:eq(0)").prop("verify","' . $direc . '");' . "\n";
                        break;

                    case "load":
                        $direc = site_url($value['value']);
                        if ($value['site'] == "relative") {
                            $direc = "";
                        } elseif ($value['site'] == "absolute") {
                            $direc = $value['value'];
                        }
                        $resp['extra2'] .=
                                "$.ajax({"
                                . "type: 'post',"
                                . "url: '" . site_url('usuario/datos_usuario') . "',"
                                . "beforeSend: function(data) {"
                                . "$('#cargando')"
                                . ".html(('<div class=\"page-header\"><h1>" . @$value['label'] . "<small>" . @$value['placeholder'] . "</small></h1></div><img src=\"" . base_url() . "img-static/ajax-loader.gif\">'));"
                                . "$('" . $value['site'] . "').css('visibility', 'hidden');"
                                . "},"
                                . "success: function(data, textStatus, jqXHR) {"
                                . "data = JSON.recursive_parse(data);"
                                //console.log(data);
                                . "interpretate_json(data, '#" . $id_unico . "');"
                                . "$('" . $value['site'] . "').css('visibility', 'visible');"
                                . "$('#cargando').html('');"
                                . "},"
                                . "error: function(jqXHR, textStatus, errorThrown) {"
                                . "console.error('Un error ocurrió, porfavor, intentalo denuevo\\n' + errorThrown);"
                                . "}"
                                . "});" . "\n";
                        break;
                    case "div":
                        array_push($resp, '<div id="' . $key . '">' . '</div>');
                        break;
                    case 'readonly':
                        array_push($resp, array('<label class="control-label" for="' . $key . '">' . @$value['label'] . '</label>',
                            '<input name="' . $key . '" class="form-control" readonly type="text">'));
                        break;
                    case 'hidden':
                        array_push($resp, array('<label class="control-label" for="' . $key . '">' . '</label>',
                            '<input name="' . $key . '" class="form-control" value="' . $value['value'] . '" readonly type="hidden">'));
                        $resp['extra2'] .= '$(' . "'" . '[name="' . $key . '"]' . "'" . ').parents("tr:eq(0)").css("display","none")' . "\n";
                        break;
                    case 'page_header':
                        array_push($resp, '<div class="page-header"><h1>' . ($value['value']) . ' <small>' . @$value['placeholder'] . '</small></h1></div>');
                        break;
                    case 'info':
                        array_push($resp, '<strong>' . @$value['label'] . '</strong><p>' . $value['value'] . '</p>');
                        break;
                    case 'title':
                        array_push($resp, '<h' . $value['value'] . '>' . @$value['label'] . ' <small>' . @$value['placeholder'] . '</small></h' . $value['value'] . '>');
                        break;
                    case 'date':
                        $resp['extra'] .= $key . ': {date: true}, ';
                        $resp['extra2'] .= '$(' . "'" . '[name="' . $key . '"]' . "'" . ').datepicker({ dateFormat: "yy-mm-dd", defaultDate: +0,todayBtn: true,language: "es",autoclose: true});';
                        array_push($resp, array('<label class="control-label" for="' . $key . '">' . @$value['label'] . '</label>',
                            '<input autocomplete="off" class="form-control prevent-type" type="text" ' . $require . ' title="' . $error_message . '" data-validation="alphanumeric length" data-validation-length="min4" name="' . $key . '" value="' . $value['value'] . '" placeholder="' . @$value['placeholder'] . '">'));
                        break;
                    case 'email':
                    case 'mail':
                    case 'correo':
                        $resp['extra'] .= $key . ': {email: true}, ';
                        array_push($resp, array('<label class="control-label" for="' . $key . '">' . @$value['label'] . '</label>',
                            '<input class="form-control" type="text" ' . $require . ' title="' . $error_message . '" data-validation="alphanumeric length" data-validation-length="min4" name="' . $key . '" value="' . $value['value'] . '" placeholder="' . @$value['placeholder'] . '">'));
                        break;
                    case 'text':
                        array_push($resp, array('<label class="control-label" for="' . $key . '">' . @$value['label'] . '</label>',
                            '<input class="form-control" type="text" ' . $require . ' title="' . $error_message . '" data-validation="alphanumeric length" data-validation-length="min4" name="' . $key . '" value="' . $value['value'] . '" placeholder="' . @$value['placeholder'] . '">'));
                        break;
                    case 'number':
                    case 'numeric':
                    case 'integer':
                        array_push($resp, array('<label class="control-label" for="' . $key . '">' . @$value['label'] . '</label>',
                            '<input class="form-control" type="text" ' . $require . ' title="' . $error_message . '" data-validation="number" name="' . $key . '" value="' . $value['value'] . '" placeholder="' . @$value['placeholder'] . '">'));

                        break;
                    case 'password':
                        array_push($resp, array('<label class="control-label" for="' . $key . '">' . @$value['label'] . '</label>',
                            '<input class="form-control" type="password" ' . $require . ' title="' . $error_message . '" data-validation="alphanumeric length" data-validation-length="min4" name="' . $key . '" value="' . $value['value'] . '" placeholder="' . @$value['placeholder'] . '">'));
                        break;
                    case 'textarea':
                        array_push($resp, array('<label class="control-label" for="' . $key . '">' . @$value['label'] . '</label>',
                            '<div></div><textarea class="form-control" type="text" ' . $require . ' title="' . $error_message . '" data-validation="alphanumeric length" data-validation-length="min10" name="' . $key . '" placeholder="' . @$value['placeholder'] . '">' . $value['value'] . '</textarea>'));
                        break;

                    case 'radio':
                        $label = ('<label class="control-label" for="' . $key . '">' . @$value['label'] . '</label>');
                        if ($require != '') {
                            $resp['extra'] .= $key . ': {required: true,minlength: 1}, ';
                            $require = '';
                        }
                        $temp_value = array();
                        foreach ($value['value'] as $key2 => $value2) {
                            if (!is_array($value2)) {
                                array_push($temp_value, '<label title="' . $error_message . '" class = "radio-inline" for="' . $key . $key2 . '"><input ' . $require . ' id="' . $key . $key2 . '" type = "radio" value = "' . $key2 . '" name = "' . $key . '"> ' . $value2 . '</label>');
                            } else {
                                foreach ($value2 as $key3 => $value3) {
                                    array_push($temp_value, '<label class = "">' . $key3 . ' <input type = "text" class = "input-small" name = "' . $key . '[' . $key2 . ']" value = "' . $value3 . '" placeholder = "' . @$value['placeholder'] . '"></label>');
                                }
                            }
                        }
                        array_push($resp, array($label, $temp_value));
                        break;
                    case 'checkbox':
                        $label = ('<label class="control-label" for="' . $key . '">' . @$value['label'] . '</label>');
                        if ($require != '') {
                            $resp['extra'] .= "'" . $key . "[]'" . ': {required: true,minlength: 1}, ';
                            $require = '';
                        }
                        $temp_value = array();
                        //var_dump($value['value']);
                        foreach ($value['value'] as $key2 => $value2) {
                            if (!is_array($value2)) {
                                array_push($temp_value, '<label class = "checkbox-inline" for="' . $key . $key2 . '"><input ' . $require . ' title="' . $error_message . '" id="' . $key . $key2 . '" type = "checkbox" value = "' . $key2 . '" name = "' . $key . '[]"> ' . $value2 . '</label>');
                            } else {
                                foreach ($value2 as $key3 => $value3) {
                                    //array_push($temp_value, '<label class = "">' . $key3 . ' <input type = "text" class = "input-small" name = "' . $key . '[]" value = "' . $value3 . '" placeholder = "' . @$value['placeholder'] . '"></label>');
                                }
                            }
                        }
                        array_push($resp, array($label, $temp_value));
                        break;
                    case 'select':
                        $label = ('<label ' . $require . ' class="control-label" for="' . $key . '">' . @$value['label'] . '</label>');
                        $string_value = '<select class="form-control" name = "' . $key . '" title="' . $error_message . '" ' . $require . ' >';

                        $string_value .= '<option value = "">' . @$value['placeholder'] . '</option>';
                        foreach ($value['value'] as $key2 => $value2) {

                            $string_value .= '<option value = "' . $key2 . '">' . $value2 . '</option>';
                        }
                        $string_value .= '</select>';
                        array_push($resp, array($label, $string_value));
                        break;
                }
            }
        } else {
            $resp = array();
        }

        return $resp;
    }

    //$data = Valores reales obtenidos de una consulta para luego ser traducidos
    //$array_forms_json = Formularios de los que se van a sacar los datos para traducir los indices
    //$array_display = posiciones en el array $data que se traduciran. Si es nulo se traduciran todos
    function mostrar_form($array_forms_json, $data, $array_display = null, $array_mod = null) {


        $get_data = null;
        $resp = array();

        //$array_display = null;
        if (!empty($array_display)) {
            $get_data = (object) array();
            foreach ($array_display as $value) {
                foreach ($array_forms_json as $value2) {
                    if (isset($value2->$value)) {
                        $get_data->$value = $value2->$value;
                        break;
                        //array_push($get_data, $value2->$value);
                    }
                }
            }
        }

        if (empty($get_data)) {
            $get_data = array();
            foreach ($array_forms_json as $key => $value) {
                $value = (array) $value;
                foreach ($value as $key1 => $value1) {
                    $get_data[$key1] = $value1;
                }
            }
            $get_data = (object) $get_data;
        }
        //var_dump($data);
        //var_dump($get_data);

        foreach ($data as $key => $valor) {
            if (isset($get_data->$key)) {

                $value = (array) $get_data->$key;

                if (isset($value['table_out']) and isset($value['table_in'])) {
                    $value['value'] = (object) $this->get_all_from_table_form($value['table_out']);
                }



                switch ($value['type']) {
                    case 'text':
                    case 'number':
                    case 'numeric':
                    case 'integer':
                    case 'textarea':
                        if (is_object($valor)) {
                            ($resp[@$value['label']] = $valor->scalar);
                        } else {
                            ($resp[@$value['label']] = $valor);
                        }
                        if (isset($array_mod[$key])) {
                            foreach ($array_mod[$key] as $keya => $valuea) {
                                $resp[@$value['label']] = (call_user_func_array(array($this, $keya), array($resp[@$value['label']], $valuea[0], $valuea[1])));
                            }
                        }
                        break;
                    case 'radio':
                    case 'select':
                        if (is_object($valor)) {
                            $index = $valor->scalar;
                        } else {
                            $index = $valor;
                        }
                        if (isset($value['value']->$index)) {
                            ($resp[@$value['label']] = $value['value']->$index);
                        } else {
                            //var_dump( $index);
                        }

                        break;
                    case 'checkbox':
                        $temp_array = array();
                        //var_dump($valor);
                        //var_dump($value['value']);
                        foreach ($valor as $key => $value3) {
                            if (isset($value['value']->$value3))
                                array_push($temp_array, $value['value']->$value3);
                            else {
                                //var_dump($key);
                                //var_dump($value['value']);
                                //var_dump($valor->$key);
                            }
                        }
                        $resp[@$value['label']] = $temp_array;
                        break;
                }
            }
        }

        /*
         * $array_mod = array(
          "precioTemAlta" => array(
          'multiplicar' => array('0.15', $valor_iva)
          ),
          "precioTemBaja" => array(
          'multiplicar' => array('0.15', $valor_iva)
          )
          );
         */

        return $resp;
    }

//var_dump(call_user_func_array($func, array(2, 4)));

    private function agregar_impuesto($valor, $array_multiplicar) {
        $valor = str_replace(".", "", $valor);
        $valor = floatval($valor);

        //var_dump($array_multiplicar);

        foreach ($array_multiplicar as $key => $value) {
            //var_dump(floatval($value));
            $valor = $valor + $valor * floatval($value);
        }

        return round($valor, 0);
    }

    private function agregar_comision($valor, $comision, $iva) {
        $valor = str_replace(".", "", $valor);
        $valor = floatval($valor);

        $valor += ($valor * $comision) + ($valor * $comision * $iva);
        return round($valor, -3);
    }

    function call($value_data, $array_arguments) {
        if (is_object($array_arguments) or is_array($array_arguments)) {
            foreach ($array_arguments as $keyb => $valueb) {
                if ($this->is_method($this, $keyb)) {
                    $value_data = $this->call($value_data, $valueb);
                } else {
                    $value_data = (call_user_func_array(array($this, $keya), array($value_data, $valueb)));
                }
            }
        }
        return $value_data;
    }

    function is_method($class_name, $method_name) {
        if (method_exists($class_name, $method_name) && is_callable(array($class_name, $method_name))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Redirect with POST data.
     *
     * @param string $url URL.
     * @param array $post_data POST data. Example: array('foo' => 'var', 'id' => 123)
     * @param array $headers Optional. Extra headers to send.
     */
    public function redirect_post($url, array $params, array $headers = null) {
        $data = "<html><body><form action='$url' method='post' id='frm' name='frm'>";

        foreach ($params as $a => $b) {
            if (!is_array($b) && !is_array($a) && !is_object($b) && !is_object($a)) {
                $data .= "<input type='hidden' name='" . htmlentities($a) . "' value='" . htmlentities($b) . "'>";
            }
        }

        $data .= "</form>";
        $data .='<script language="JavaScript">';
        $data .= 'document.frm.submit();';
        $data.='</script></body></html>';

        return $data;
    }

}
