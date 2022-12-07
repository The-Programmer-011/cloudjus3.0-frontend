<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}


$_GET["vm"] = strtoupper($_GET["vm"]);

?>

<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title> <?php echo $_GET["vm"]; ?> </title>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<style>

	table{
		width: 100%;
	}
	
	th, td {
		width:5.6%;
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
	<h1> Searching for: <?php echo $_GET["vm"]; ?></h1>
	<div class='table' style="max-height: 500px; overflow-y:scroll;">
	<table>
		<tr>
			<th class="vm">Maquina Virtual</th>
			<th>Host</th>
			<th>N° Nucleos</th>
			<th>% CPU</th>
			<th>Mem. Assoc</th>
			<th>Mem. Demand</th>
			<th>Mem. Startup</th>
			<th>Mem. Din</th>
			<th>Mem Min</th>
			<th>Mem Max</th>
			<th>VHD Aloc (Gb)</th>
			<th>VHD Usado (Gb)</th>
			<th>Gen</th>
			<th>HA</th>
			<th>Uptime (dias)</th>
			<th>On/Off</th>
		</tr>
	</table>
	</div>
	<div class='table' style="max-height: 500px; overflow-y:scroll;">
	<table>
	<?php
		$vm = $_GET['vm'];
		$file = fopen("requests/vms.txt", "r");
		fgets($file);
		fgets($file);
		fgets($file);
		fgets($file);
		if($_SESSION['administrador'][0]=="1"){
			while(!feof($file)){
				$line = fgets($file);
				$allline = $line;
				$line = str_replace(" ", "", $line);
				$line = explode("|", $line);
				if(strpos($allline, $vm)!=FALSE){
					for($count=1;$count<=16;$count++){
						if($count==1){
							echo "<td class='vm'>" . $line[$count] . "</td>";	
						}
						else{
							echo "<td>" . $line[$count] . "</td>";
						}
					}
					echo "</tr>";
				}
				fgets($file);
			}
		}
		else{
			$group_file = "requests/Groups/" . $_SESSION['grupo'] . ".txt";
			$file_vms = fopen($group_file, "r");
			$permited_vms = explode(";", fgets($file_vms));
			fclose($file_vms);
			$len = count($permited_vms);
			while(!feof($file)){
				$line = fgets($file);
				$allline = $line;
				$line = str_replace(" ", "", $line);
				$line = explode("|", $line);
				for($cont=0;$cont<$len;$cont++){
					if(strpos($allline, $vm)!=FALSE && $line[1]==strtoupper($permited_vms[$cont])){
						for($count=1;$count<=16;$count++){
							if($count==1){
								echo "<td class='vm'>" . $line[$count] . "</td>";	
							}
							else{
								echo "<td>" . $line[$count] . "</td>";
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
