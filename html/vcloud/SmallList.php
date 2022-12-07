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
	    border: 1px solid white;
	    width: 33%;
	    height: 40px; 
	}

</style>
<body>
	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Informacoes das maquinas virtuais</h1>
	<br>
	<div class='table' style="max-height: 500px; overflow-y:scroll">
	<table>
		<tr>
			<th>Maquina</th>
			<th>Estado</th>
			<th></th>
		</tr>
	</table>
	</div>
	<div class='table' style="max-height: 450px; overflow-y:scroll">
	<table>
	<?php
		$file = fopen("requests/vm_names.txt", "r");
		if($_SESSION['administrador'][0]=="1"){
			while(!feof($file)){
				echo "<tr>";
				$line = fgets($file);
				$line = explode(" ", $line);
				echo "<td style='text-align:center'>" . $line[0] . "</td>";
				echo "<td style='text-align:center'>" . $line[1] . "</td>";
				$vm = $line[0];
				if(strlen($line[1]) == 11){ //11 = PoweredOn
					echo "<td style='text-align:center'><a href='requests/request_validation.php?nome_maquina=$vm&origin=ShutdownOS.php' target='_blank'><input type='button' class='refresh' style='background-color:#e67300; color:white' value='Desligar'></a> <a href='requests/request_validation.php?nome_maquina=$vm&origin=RestartOS.php' target='_blank'><input type='button' class='refresh' style='background-color:#ffd11a; color:black' value='Reiniciar'></a></td>";
				}
				else{
					echo "<td style='text-align:center'><a href='requests/request_validation.php?nome_maquina=$vm&origin=PwrON.php' target='_blank'><input type='button' class='refresh' style='background-color:#40ff00; color:black' value='Ligar'></a> <a href='DelVMConfirmation.php?nome_maquina=$vm' target='_blank'><input type='button' class='refresh' style='background-color:Red; color:white' value='Remover'></a></td>";	
				}
				echo "</tr>";
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
				$line = explode(" ", $line);
				for($count=0;$count<$len;$count++){
					//echo $vms[$count] . " = " . $line[0] . "<br>";
					if($vms[$count]==$line[0] && $vms[$count]!=""){
						echo "<tr>";
						echo "<td style='text-align:center'>" . $line[0] . "</td>";
						echo "<td style='text-align:center'>" . $line[1] . "</td>";
						$vm = $line[0];
						echo "<td style='text-align:center'>";
						if($_SESSION['administrador'][0]!="0"){
							if(strlen($line[1]) == 11){ //11 = PoweredOn
								echo "<a href='requests/request_validation.php?nome_maquina=$vm&origin=ShutdownOS.php' target='_blank'><input type='button' class='refresh' style='background-color:#e67300; color:white' value='Desligar'></a> <a href='requests/request_validation.php?nome_maquina=$vm&origin=RestartOS.php' target='_blank'><input type='button' class='refresh' style='background-color:#ffd11a; color:black' value='Reiniciar'></a>";
							}
							else{
								echo "<a href='requests/request_validation.php?nome_maquina=$vm&origin=PwrON.php' target='_blank'><input type='button' class='refresh' style='background-color:#40ff00; color:black' value='Ligar'></a>";
								if($_SESSION['administrador'][0]<="2"){
									echo " <a href='DelVMConfirmation.php?nome_maquina=$vm' target='_blank'><input type='button' class='refresh' style='background-color:Red; color:white' value='Remover'></a>";
								}
							}
						}
						echo "</td>";
						echo "</tr>";
					}
				}
			}
		}
		fclose($file);
	?>
	</table>
	</div>
</body>
</html>
