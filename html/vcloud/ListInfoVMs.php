<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
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
		width:8.5%;
	    border: 1px solid white;
	    height: 40px;
	    text-align: center; 
	}

	.vm{
		width: 15%;
	}
</style>
<body>
	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Informacoes das maquinas virtuais</h1>
	<br>
	<div class='table' style="max-height: 500px; overflow-y:scroll;">
	<table>
		<tr>
			<th class="vm">Maquina Virtual</th>
			<th>Host</th>
			<th>N° Nucleos</th>
			<th>% CPU</th>
			<th>Mem. Assoc</th>
			<th>$ Mem</th>
			<th>Disco Aloc. (Gb)</th>
			<th>Disco Usado (Gb)</th>
			<th>HW Vers</th>
			<th>Uptime</th>
			<th>On/Off</th>
		</tr>
	</table>
	</div>
	<div class='table' style="max-height: 500px; overflow-y:scroll;">
	<table>
	<?php
		$file = fopen("requests/vms.txt", "r");
		fgets($file);
		fgets($file);
		fgets($file);
		fgets($file);
		if($_SESSION['administrador'][0]=="1"){
			while(!feof($file)){
				echo "<tr>";
				$line = fgets($file);
				$line = str_replace(" ", "", $line);
				$line = explode("|", $line);
				for($count=1;$count<=11;$count++){
					if($count==1){
						echo "<td class='vm'>" . $line[$count] . "</td>";	
					}
					else{
						echo "<td>" . $line[$count] . "</td>";
					}
				}
				echo "</tr>";
				fgets($file);
			}
		}
		else{
			$filename = "requests/Groups/" . $_SESSION['grupo'] . ".txt";
			$file_vm = fopen($filename, "r");
			$line = fgets($file_vm);
			$vms = explode(";", $line);
			$len = count($vms);
			fclose($file_vm);
			while(!feof($file)){
				$line = fgets($file);
				$line = str_replace(" ", "", $line);
				$line = explode("|", $line);
				for($count=0;$count<$len;$count++){
					//echo $vms[$count] . " = " . $line[1] . "<br>";
					if($vms[$count]==$line[1] && $vms[$count]!=""){
						echo "<tr>";
						for($cont=1;$cont<=11;$cont++){
							if($cont==1){
								echo "<td class='vm'>" . $line[$cont] . "</td>";	
							}
							else{
								echo "<td>" . $line[$cont] . "</td>";
							}
						}
						echo "</tr>";
					}
				}
				fgets($file);
			}
		}
		fclose($file);
	?>
	</table>
	</div>
</body>
</html>

