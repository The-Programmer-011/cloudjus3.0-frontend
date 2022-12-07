<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
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
		width:25%;
		height: 40px;
	    border: 1px solid white;
	    text-align: center;
	} 

</style>
<title>Pedidos</title>
<body>
	<?php include_once("../Assets/navbar.php"); ?>
	<?php
	if(!$_GET['file']){
		$dir = "requests/";
		$files_in_dir = scan_dir($dir);
		$size = count($files_in_dir);
		?>
		<h1>Pedidos nao Processados</h1>
		<div class='table' style="max-height: 500px; overflow-y:scroll">
		<table>
			<tr>
				<th>Operacao</th>
				<th>Nome VM</th>
				<th>Data</th>
			</tr>
		</table>
		</div>
		<div class='table' style="max-height: 500px; overflow-y:scroll">
		<table>
		<?php
		for($count=0;$count<$size;$count++){
			$name = explode(" ", $files_in_dir[$count]);
			if($name[1] == $_SESSION['username']){
				$filename = $files_in_dir[$count];
				echo "<tr>";
				if($name[0]=="1"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Criacao VM</td>";
				}
				else if($name[0]=="2"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Criacao VM sem template</td>";	
				}
				else if($name[0]=="3"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Remocao VM</td>";	
				}
				else if($name[0]=="4"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Adicionar disco</td>";	
				}
				else if($name[0]=="5"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Alterar numero de processadores</td>";	
				}
				else if($name[0]=="6"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Alterar quantidade de memoria</td>";	
				}
				else if($name[0]=="7"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Shutdown VM</td>";	
				}
				else if($name[0]=="8"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Restart VM</td>";	
				}
				else if($name[0]=="9"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Power ON VM</td>";	
				}
				else if($name[0]=="a"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Power OFF VM</td>";	
				}
				else if($name[0]=="$1"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Criar Snapshot</td>";	
				}
				else if($name[0]=="$2"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Reverter Snapshot</td>";	
				}
				else if($name[0]=="$3"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Remover Snapshot</td>";	
				}
				else if($name[0]=="b"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Modifificar Politica de Backup</td>";	
				}
				else if($name[0]=="c"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Período de manutenção</td>";	
				}
				echo "</a>";
				echo "<td>" . $name[2] . "</td>";
				$hora = explode("-", $name[3]);
				$data = explode("-", $name[4]);
				$ano = explode(".", $data[2]);
				echo "<td>" . $hora[0] . ":" . $hora[1] . ":" . $hora[2] . " " . $data[0] . "/" . $data[1] . "/" . $ano[0] . "</td>";
				echo "</tr>";
			}
		}
		?>
		</table>
		</div>

		<?php
		$dir = "requests/Processed";
		$files_in_dir = scan_dir($dir);
		$size = count($files_in_dir);
		?>
		<h1>Pedidos Processados</h1>
		<div class='table' style="max-height: 500px; overflow-y:scroll">
		<table>
			<tr>
				<th>Operacao</th>
				<th>Nome VM</th>
				<th>Data</th>
			</tr>
		</table>
		</div>
		<div class='table' style="max-height: 500px; overflow-y:scroll">
		<table>
		<?php
		for($count=0;$count<$size;$count++){
			$name = explode(" ", $files_in_dir[$count]);
			if($name[1] == $_SESSION['username']){
				$filename = $files_in_dir[$count];
				echo "<tr>";
				if($name[0]=="1"){
					echo "<td><a href='manage_requests.php?file=$filename'>Criacao VM</td>";
				}
				else if($name[0]=="2"){
					echo "<td><a href='manage_requests.php?file=$filename'>Criacao VM sem template</td>";	
				}
				else if($name[0]=="3"){
					echo "<td><a href='manage_requests.php?file=$filename'>Remocao VM</td>";	
				}
				else if($name[0]=="4"){
					echo "<td><a href='manage_requests.php?file=$filename'>Adicionar disco</td>";	
				}
				else if($name[0]=="5"){
					echo "<td><a href='manage_requests.php?file=$filename'>Alterar numero de processadores</td>";	
				}
				else if($name[0]=="6"){
					echo "<td><a href='manage_requests.php?file=$filename'>Alterar quantidade de memoria</td>";	
				}
				else if($name[0]=="7"){
					echo "<td><a href='manage_requests.php?file=$filename'>Shutdown VM</td>";	
				}
				else if($name[0]=="8"){
					echo "<td><a href='manage_requests.php?file=$filename'>Restart VM</td>";	
				}
				else if($name[0]=="9"){
					echo "<td><a href='manage_requests.php?file=$filename'>Power ON VM</td>";	
				}
				else if($name[0]=="a"){
					echo "<td><a href='manage_requests.php?file=$filename'>Power OFF VM</td>";	
				}
				else if($name[0]=="$1"){
					echo "<td><a href='manage_requests.php?file=$filename'>Criar Snapshot</td>";	
				}
				else if($name[0]=="$2"){
					echo "<td><a href='manage_requests.php?file=$filename'>Reverter Snapshot</td>";	
				}
				else if($name[0]=="$3"){
					echo "<td><a href='manage_requests.php?file=$filename'>Remover Snapshot</td>";	
				}
				else if($name[0]=="b"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Modifificar Politica de Backup</td>";	
				}
				else if($name[0]=="c"){
					echo "<td><a href='ToProcessRequests.php?file=$filename'>Período de manutenção</td>";	
				}
				echo "</a>";
				echo "<td>" . $name[2] . "</td>";
				$hora = explode("-", $name[3]);
				$data = explode("-", $name[4]);
				$ano = explode(".", $data[2]);
				echo "<td>" . $hora[0] . ":" . $hora[1] . ":" . $hora[2] . " " . $data[0] . "/" . $data[1] . "/" . $ano[0] . "</td>";
				echo "</a>";
				echo "</tr>";
			}
		}
	}	
	?>
	</table>
	</div>
	<?php

	if($_GET['file']){
		$filename = $_GET['file'];
		$name = explode(" ", $filename);
		$file = fopen("requests/$filename", "r");
		if(!$file){
			$file = fopen("requests/Processed/$filename", "r");
		}
		if(!$file){
			echo "<h1>ERROR</h1>";
		}
		echo "Detalhes do pedido:<br><br>";
		$cod = fgets($file);
		$cod_snap = $cod[3];
		$cod = $cod[2];
		if($cod==1 || $cod==2){
			echo "Pedido de criacao de maquina virtual:<br>";
		}
		else if($cod==3){
			echo "Pedido de eliminacao de maquina virtual<br>";
		}
		else if($cod == 4){
			echo "Pedido de alteracao de tamanho de disco<br>";
		}
		else if($cod == 5){
			echo "Pedido de alteracao do numero de processadores<br>";
		}
		else if($cod == 6){
			echo "Pedido de alteracao de quantidade de memoria<br>";
		}
		else if($cod == 7){
			echo "Pedido de shutdown<br>";
		}
		else if($cod == 8){
			echo "Pedido de restart<br>";
		}
		else if($cod == "a"){
			echo "Pedido de desligamento (Power OFF)<br>";
		}
		else if($cod == 9){
			echo "Pedido para ligar uma VM<br>";
		}
		else if($name[0]=="$1"){
			echo "Pedido de criacao de snapshot<br>";
		}
		else if($name[0]=="$2"){
			echo "Pedido de revercao de snapshot<br>";
		}
		else if($name[0]=="$3"){
			echo "Pedido de remocao de snapshot<br>";
		}
		else if($name[0]=="b"){
			echo "Pedido de modificação de política de backup<br>";
		}
		$unit = fgets($file);
		echo "Nome da maquina: " . $unit . "<br>";

		$unit = fgets($file);

		if($cod==1 || $cod==2){
			$unit = intval($unit[1]);
			$host = fopen("requests/Lists/hosts.txt", "r");
			for($count=0; $count<$unit; $count++){
				$host_name = fgets($host);
			}
			$host_name = explode(" ", $host_name);
			echo "Host: $host_name[2]<br>";
			fclose($host);

			$host = $unit - 1;
			$disk_file = "requests/Lists/" . $host . " disks.txt";
			$disk = fopen($disk_file, "r");
			$unit = fgets($file);
			$unit = intval($unit[1]);
			for($count=0; $count<$unit; $count++){
				$disk_name = fgets($disk);
			}
			$disk_name = explode(" ", $disk_name);
			echo "Disco: $disk_name[2]<br>";
			fclose($disk);

			if($cod==1){
				$unit = fgets($file);
				$unit = intval($unit[1]);
				$template_file = "requests/Lists/" . $host . " templates.txt";
				$template = fopen($template_file, "r");
				for($count=0; $count<$unit; $count++){
					$template_name = fgets($template);
				}
				$template_name = explode(" ", $template_name);
				echo "Template: $template_name[2]<br>";
				fclose($template);
			}

			$unit = fgets($file);
			echo "Processadores: $unit<br>";

			$unit = fgets($file);
			echo "Memoria: $unit Mb<br>";

			if($cod==2){
				$unit = fgets($file);
				echo "Tamanho de disco: $file Gb<br>";
			}		
		}
		else if($cod==4){
			$unit = intval($unit[1]);
			echo "Disco aumentado em $unit Gb<br>";
		}
		else if($cod==5){
			$unit = intval($unit[1]);
			echo "Novo numero de processadores: $unit<br>";	
		}
		else if($cod==6){
			$unit = intval($unit[1]);
			echo "Nova quantidade de memoria: $unit Mb<br>";
		}
		else if($cod[0] == "$"){
			echo "Nome do snapshot: $unit<br>";
		}
		else if($cod=="b"){
			$dir = scandir("../backup");
			$len = count($dir);
			for($aux=0;$aux<$len;$aux++){
				//echo $dir[$aux][0] . "=" . $unit[1] . " " . strlen($unit) . "<br>";
				if($dir[$aux][0]==$unit[1]){
					$policy = $dir[$aux];
					$policy = explode("_", $policy);
					$policy = explode(".", $policy[1]);
					$policy = $policy[0];
					echo "Nova polítca de backup: $policy<br>";
				}
			}
		}
		fclose($file);
	}

	?>
</body>
</html>