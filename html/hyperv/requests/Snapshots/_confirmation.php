<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}


?>

<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>Confirmacao</title>
<?php if($_SESSION['theme']=="stf"){
	echo '<link rel="stylesheet" href="/Assets/stf_style.css">';
}
else{
	echo '<link rel="stylesheet" href="/Assets/vmware_style.css">';
}
?>
<style>

input[type=button].yes{
	background-color: #00e600;
}

input[type=button].yes:hover{
	background-color: #00cc00;
}

input[type=button].yes:active{
	background-color: #009900;
}

input[type=button].no{
	background-color: #e62e00;
	color: white;
}

input[type=button].no:hover{
	background-color: #cc2900;
}

input[type=button].no:active{
	background-color: #991f00;
}

</style>
<body>
	<?php
		$vm = $_GET['nome_maquina'];
		$op = $_GET['op'];
		$snap = $_GET['snap'];
		if($_GET['op']==2){
			echo "<h1>Tem certeza que voce deseja reverter a maquina virtual $vm para o snapshot $snap?</h1>";
		}
		else if($_GET['op']==3){
			echo "<h1>Tem certeza que voce deseja excluir o snapshot $snap? da maquina virtual $vm?</h1>";
		}
		echo "<a href='_snapshot_manager.php?nome_maquina=$vm&snap=$snap&op=$op'><input type='button' value='Sim' class='yes'></a> <a href='/main.php'><input type='button' value='Nao' class='no'></a>";
	?>
</body>
</html>