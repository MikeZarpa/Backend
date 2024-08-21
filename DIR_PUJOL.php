<?php
	define("DIR_PUJOL", $_SERVER['DOCUMENT_ROOT'].'/BACKEND');
    require_once (DIR_PUJOL."/clases/autenticacion/gestor_de_permisos.php");

	if($_SERVER['REQUEST_METHOD'] == "OPTIONS"){
        exit();
    }
// require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');