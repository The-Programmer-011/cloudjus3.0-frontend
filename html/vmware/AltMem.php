<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][0]=="0" || $_SESSION['administrador'][0]>"3"){
	header("Location: /index.php?op=err");
	$_SESSION['denied'] = 1;
}


?>

<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>Change Memory</title>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<body>
	<?php
	if($_GET['nome_maquina']!=""){
		$_GET['nome_maquina'] = strtolower ($_GET['nome_maquina']);
		$file = fopen("requests/vm_names.txt", "r");
		$error = 1;
		while(!feof($file)){
			$line = fgets($file);
			$line = explode(" ", $line);
			if($line[0]==$_GET['nome_maquina']){
				$error = 0;
				if(strlen($line[1]) == 11){ //PoweredOn
					$error = 1;
					$machine_on = 1;
				}
				break;
			}
		}
		if($error && $machine_on != 1){
			$name_not_found = 1;
		}
		fclose($file);

		if($_SESSION['administrador']!="11"){
			$filename = "requests/Groups/" . $_SESSION['grupo'] . ".txt";
			//echo $filename;
			$file = fopen($filename, "r");
			$unauthorized_machine=1;
			$line = fgets($file);
			$line = explode(";", $line);
			$len = count($line);
			$error++;
			for($count=0;$count<$len;$count++){
				$vm = $line[$count];
				if($_GET['nome_maquina'] == $vm){
					$unauthorized_machine=0;
					$error--;
					break;
				}
			}
			fclose($file);
		}

		if(!$error){
			$vm = $_GET['nome_maquina'];
			$ram = $_GET['ram'];
			header("Location: requests/request_validation.php?nome_maquina=$vm&ram=$ram&permission=1");
			echo "deu";
		}
	}

	?>

	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Alterar quantidade de memoria de uma maquina virtual</h1>
	<form method="get" oninput="ram_amount.value=parseInt(ram.value);">
	<p>Nome da maquina virtual:</p>
	<input list="vms" name="nome_maquina" value="<?php echo $_GET['nome_maquina'];?>" required>
	<datalist id="vms">
		<?php
		$filename = "requests/Groups/" . $_SESSION['grupo'] . ".txt";
		//echo $filename;
		$file = fopen($filename, "r");
		$line = fgets($file);
		$line = explode(";", $line);
		$len = count($line);
		for($count=0;$count<$len;$count++){
			$vm = $line[$count];
			echo "<option value='$vm'>";
		}
		fclose($file);
		?>
	</datalist>
	<br>
	<?php
		if($name_not_found){
			echo "* maquina nao encontrada!";
		}
		else if($unauthorized_machine){
			echo "* a maquina nao pertence ao grupo $group!";
		}
		else if($machine_on){
			echo "* a maquina deve estar desligada para essa acao ser executada!";
		}
	?>
	<br>
	<p>Quantidade de memoria: <output name="ram_amount" for="ram"></output> Mb</p>
	<input type="range" name="ram" class="slider" id="myRange" min="512" max="16000" step="512" value="<?php echo $_GET['core_number'];?>">
	<br><br>
	<input type="submit" value="Validar">
	</form>
</body>
</html>