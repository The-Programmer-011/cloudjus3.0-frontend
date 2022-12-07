<html>
<body>
<?php
//inicio de sessao
session_start();

//Caso o usuario nao estaja logado, redireciona para a pagina de login
if(!isset($_SESSION['username'])){ 
	header("Location: /index.php?op=err");
}
//Caso o usuario nao seja super_adm, redireciona para a main
if($_SESSION['administrador'][$_SESSION['hv']-1]!="1"){
	header("Location: /index.php?op=err");
	$_SESSION['denied'] = 1;
}

if($_SESSION['hv'] == '1'){
	$hypervisor = "vmware";
}
else if($_SESSION['hv'] == '2'){
	$hypervisor = "hyperv";
}
if($_SESSION['hv'] == '3'){
	$hypervisor = "vcloud";
}

//cria um arquivo chamado 0.txt no BRITANIA para forcar a atualizacao completa das listas
$file = fopen("$hypervisor/requests/_list.txt", "w");
if($file){
	fwrite($file, "2");
	fclose($file);
}
else{
	echo "n deu";
}

//Espera um segundo e redireciona para a main
sleep(1);
if(isset($_SESSION['gentelella'])){
	header("Location: g_menu.php");
}
else{
	header("Location: main.php");
}
?>
</body>
</html>

<!--The_Programmer-->