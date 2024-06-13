<?php
	define("DIR_PUJOL", $_SERVER['DOCUMENT_ROOT'].'/BACKEND');

	if($_SERVER['REQUEST_METHOD'] == "OPTIONS"){
        exit();
    }
// require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');