<?php
session_start();

if(!isset($_SESSION['username'])){ //caso o usuario nao esteja logado, redireciona para a pagina de login
	header("Location: index.php?op=err");
}
if($_SESSION['administrador']!="111"){ //Caso o usuario nao seja super administrador, redireciona para a main
	header("Location: /index.php?op=err");
	$_SESSION['denied'] = 1;
}


//Verificacao de todos os espacos de dados
//Caso o espaco tenha sido preenchido, muda a variavel de sessao

if($_GET['username']){
	$_SESSION['username'] = $_GET['username'];
	$change = 1;
}
if($_GET['administrador']!=""){
	$_SESSION['administrador'] = $_GET['administrador'];
	$change = 1;
}
if($_GET['name']){
	$_SESSION['name'] = $_GET['name'];
	$change = 1;
}
if($_GET['lastname']){
	$_SESSION['lastname'] = $_GET['lastname'];
	$change = 1;
}
if($_GET['grupo']){
	$_SESSION['grupo'] = $_GET['grupo'];
	$change = 1;
}
if($_GET['theme']){
	$_SESSION['theme'] = $_GET['theme'];
	$change = 1;
}
if($_GET['dashboard']){
	$_SESSION['dashboard'] = $_GET['dashboard'];
	$change = 1;
}
if($_GET['pasta_vmware']){
	$_SESSION['pasta'] = $_GET['pasta_vmware'];
	$change = 1;
}
//Caso tenha tido alguma mudanca, redireciona para a main
if($change){
	header("Location: main.php");
}


?>

<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>Administrator</title>
<link rel="icon">
<body>
	<?php include_once("Assets/navbar.php"); ?>
	<h1>Area SuperAdm</h1>
	<br>
	<h3>Use essa pagina para redefinir aspectos da sua conta temporariamente. Para voltar ao estado default relogue.</h3>
	<br>
	<form>
		<p>Username</p>
		<input type="text" name="username">
		<br>
		<p>Nome</p>
		<input type="text" name="name">
		<br>
		<p>Sobrenome</p>
		<input type="text" name="lastname">
		<br>
		<p>Codigo de acesso</p>
		<input type="text" name="administrador">
		<br>
		<p>Grupo</p>
		<input type="text" name="grupo">
		<br>
		<p>Link do Dashboard</p>
		<input type="text" name="dashboard">
		<br>
		<p>Nome da pasta do VMware</p>
		<input type="text" name="pasta_vmware">
		<br>
		<p>Tema</p>
		STF<input type="radio" name="theme" value="STF"><br>
		Dark<input type="radio" name="theme" value="dark">
		<br>
		<input type="submit">
	</form>
</body>
</html>
<!--The_Programmer-->
		