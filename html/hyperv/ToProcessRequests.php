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
		//$cod = $cod[2];
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
		else if($name[0]=="c"){
			echo "Período de Manutenção<br>";	
		}
		$unit = fgets($file);
		echo "Nome da maquina: " . $unit . "<br>";

		$unit = fgets($file);

		if($cod==1 || $cod==2){
			//$unit = intval($unit[1]);
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
			//$unit = intval($unit[1]);
			for($count=0; $count<$unit; $count++){
				$disk_name = fgets($disk);
			}
			$disk_name = explode(" ", $disk_name);
			echo "Disco: $disk_name[2]<br>";
			fclose($disk);

			if($cod==1){
				$unit = fgets($file);
				//$unit = intval($unit[1]);
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
		else if($cod[2]=="b"){
			//$unit = fgets($file);
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
		else if($cod[0]=="c"){
			echo "Começo da manutenção: " . $unit . "<br>";
			$unit = fgets($file);
			echo "Final da manutenção: " . $unit . "<br>";
			$unit = fgets($file);
			echo "ID de manutenção: " . $unit;
		}
		fclose($file);
	}
	?>
</body>
</html>