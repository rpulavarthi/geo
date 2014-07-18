<?php
 
class UserController extends DooController {
        function setClusterZoomLevel () {
                $zoom=$_POST["cluster_zoom_level"];
                session_start();
                $_SESSION['user']['cluster_zoom_level']=$zoom;
                //header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Origin: *");
                header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
		//echo  $_SESSION['user']['cluster_zoom_level'];
		var_dump($_SESSION);
        }
}
 
 
?>
 

