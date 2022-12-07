<?php

//Inicio de sessao
session_start();

//Caso o usuario nao esteja logado, redireciona para a pagina de login
if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
//Caso o usuario nao tenha permissao nivel 3 redireciona para a main
if($_SESSION['administrador'][0]=="0" || $_SESSION['administrador'][0]>"5"){
	header("Location: /index.php?op=err");
	$_SESSION['denied'] = 1;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>VMs Info</title>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<style>

	table{
		width: 100%;
	}
	
	th, td {
		width:33%;
	    border: 1px solid white;
	    height: 40px;
	    text-align: center; 
	}

</style>
<body>
	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Informacoes de backup</h1>
	<?php 

	$backup_file = fopen("requests/_backup.txt", "r");
	$dir = scandir("../backup");
	$len = count($dir);

	?>
	<div class='table' style="max-height: 500px; overflow-y:scroll;">
	<table>
		<tr>
			<th>Nome máquina</th>
			<th>Política Backup</th>
			<th></th>
		</tr>
	</table>
	</div>
	<div class='table' style="max-height: 500px; overflow-y:scroll;">
	<table>
		<?php
		if(!$backup_file){
			echo "n deu";
		}
		else{
		while(!feof($backup_file)){
			$line = fgets($backup_file);
			$element = explode(",", $line);
			$vm = $element[0];
			$policy_code = $element[1];
			$policy_code = explode(";", $policy_code);
			$policy_code = $policy_code[0];

			for($count=0; $count<$len; $count++){
				if($dir[$count][0] == $policy_code){
					$policy_name = explode("_", $dir[$count]);
					$policy_name = explode(".", $policy_name[1]);
					$policy_name = $policy_name[0];
					break;
				}
			}

			if($vm){
				echo "<tr>";
				echo "<td>$vm</td>";
				echo "<td>$policy_name</td>";
				echo "<td><a class='link' href='backup.php?nome_maquina=$vm' target='_blank'>Visualizar</a></td>";
				echo "</td>";
			}
		}

		fclose($backup_file);
		}
		?>
	</table>
</div>
</body>
</html>