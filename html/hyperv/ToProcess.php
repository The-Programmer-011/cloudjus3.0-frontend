<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
if($_SESSION['administrador']!="11"){
	header("Location: GroupRequests.php");
}

?>

<?php

function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array();    
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : false;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<style>
	a{
		color:white;
	}
	a:hover{
		color:#3399ff;
	}
	table{
		width:100%;
	    border: 1px solid white;
	    text-align: center;
	}
	th, td{
		width:18%;
		height: 40px;
	    border: 1px solid white;
	    text-align: center;
	} 

</style>

<?php
if(isset($_GET['operation']) && isset($_GET['requests'])){
	$len = count($_GET['requests']);
	if($_GET['operation']=="del"){
		for($count=0;$count<$len;$count++){
			$filename = $_GET['requests'][$count];
			echo $filename . "<br>";
			rename("requests/ToProcess/" . $filename, "requests/x" . $filename);
		}
	}
	else if($_GET['operation']=="aprov"){
		for($count=0;$count<$len;$count++){
			$filename = $_GET['requests'][$count];
			echo $filename . "<br>";
			rename("requests/ToProcess/" . $filename, "requests/" . $filename);
		}
	}
	sleep(5);
	header("Location: ToProcess.php");
}


else{?>
<title>Pedidos para aprovacao</title>
<body>
	<?php include_once("../Assets/navbar.php"); ?>		
	<?php
	$dir = "requests/ToProcess";
	$files_in_dir = scan_dir($dir);
	$size = count($files_in_dir);
	?>
	<form method="get">
	<h1>Pedidos aguardando aprovacao</h1>
	<div class='table' style="max-height: 500px; overflow-y:scroll">
	<table>
		<tr>
			<th>Operacao</th>
			<th>Usuario</th>
			<th>Nome VM</th>
			<th>Data</th>
			<th>Grupo</th>
			<th style="width: 10%"></th>
		</tr>
	</table>
	</div>
	<div class='table' style="max-height: 300px; overflow-y:scroll; border:1px ">
	<table>
	<?php
	for($count=0;$count<$size;$count++){
		$isRequest = 1;
		$name = explode(" ", $files_in_dir[$count]);
		$group_name = explode(".", $name[5]);
		$group_name = $group_name[0];
		$filename = $files_in_dir[$count];
		$filename_path = "/ToProcess/" . $files_in_dir[$count];
		echo "<tr>";
		if($name[0]=="1"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Criacao VM</td>";
		}
		else if($name[0]=="2"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Criacao VM sem template</td>";	
		}
		else if($name[0]=="3"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Remocao VM</td>";	
		}
		else if($name[0]=="4"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Adicionar disco</td>";	
		}
		else if($name[0]=="5"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Alterar numero de processadores</td>";	
		}
		else if($name[0]=="6"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Alterar quantidade de memoria</td>";	
		}
		else if($name[0]=="7"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Shutdown VM</td>";	
		}
		else if($name[0]=="8"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Restart VM</td>";	
		}
		else if($name[0]=="a"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Power ON VM</td>";	
		}
		else if($name[0]=="9"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Power OFF VM</td>";	
		}
		else if($name[0]=="$1"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Criar Snapshot</td>";	
		}
		else if($name[0]=="$2"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Reverter Snapshot</td>";	
		}
		else if($name[0]=="$3"){
			echo "<td><a href='ToProcessRequests.php?file=$filename_path'>Remover Snapshot</td>";	
		}
		else if($name[0]=="b"){
			echo "<td><a href='ToProcessRequests.php?file=$filename'>Modifificar Politica de Backup</td>";	
		}
		else if($name[0]=="c"){
			echo "<td><a href='ToProcessRequests.php?file=$filename'>Período de manutenção</td>";	
		}
		else{
			$isRequest = 0;
		}
		if($isRequest){
			echo "<td>" . $name[1] . "</td>";
			echo "<td>" . $name[2] . "</td>";
			$hora = explode("-", $name[3]);
			$data = explode("-", $name[4]);
			$ano = explode(".", $data[2]);
			echo "<td>" . $hora[0] . ":" . $hora[1] . ":" . $hora[2] . " " . $data[0] . "/" . $data[1] . "/" . $ano[0] . "</td>";
			echo "<td>" . $group_name . "</td>";
			echo '<td style="width: 10%"><input type="checkbox" name="requests[]" value="' . $filename . '"></td>';
			echo "</a>";
		}
		echo "</tr>";
	}

	?>
	</table>
	</div>
	<br>
	<input type="radio" name="operation" value="del"> Descartar<br>
  	<input type="radio" name="operation" value="aprov" checked> Confirmar<br>
  	<br>
  	<input type="submit" title="Processar">
  </form>
  <?php } ?>
</body>
</html>