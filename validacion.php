<?php    
    function validaRequerido($valor){
        if(trim($valor) == '')
            return false;
        else
            return true;
    }
    function validaEntero($valor, $opciones=null){
        if(filter_var($valor, FILTER_VALIDATE_INT, $opciones) === FALSE)
            return false;
        else
            return true;
    }
    function validaEmail($valor){
        // eliminamos caracteres no validos
        $valorlimpio = filter_var($valor, FILTER_SANITIZE_EMAIL); 

        if ($valorlimpio!=$valor)
            return false;
        else if(filter_var($valor, FILTER_VALIDATE_EMAIL) === FALSE)
            return false;
        else
            return true;
    }
    function validaLongitud($valor, $min=0, $max=0) {
        if ($min>0 && strlen($valor) < $min) 
            return false;
        else if ($max>0 && strlen($valor) > $max) 
            return false;
        else
            return true;
    }
    function limpia($valor) {
        return trim(htmlspecialchars($valor));
    }
    function validaLetras($valor) {
        if (reg_match("/^[a-zA-Z0-p9]+$/", $valor))
            return true;
        return false;
    }
    function validaFecha($valor) {
        $array = explode("/",$valor);
        $dia = $array[1];
        $mes = $array[0];
        $ano = $array[2];
        if (!is_numeric($dia) || !is_numeric($mes) || !is_numeric($ano)) 
            return false;
        else 
            return checkdate($mes, $dia, $ano);
    }
?>