<?php
//Pagina basica para deslogar o usuario
session_start();

if(isset($_SESSION['username'])){
    session_destroy();
    header("Location:index.php");
}
?>