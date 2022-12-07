<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}


?>
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
				break;
			}
		}
		if($error){
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

		fclose($file);
		if(!$error){
			$vm = $_GET['nome_maquina'];
			header("Location: requests/Snapshots/_snapshot_manager.php?nome_maquina=$vm");
			echo "deu";
		}
	}

	?>
<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>Gerenciar Snapshot</title>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<body>
	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Gerenciar Snapshots de uma maquinha virtual</h1>
	<br>
	<form method="get">
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
		<?php
			if($name_not_found){
				echo "<br>* maquina nao encontrada!";
			}
			else if($unauthorized_machine){
				echo "<br>* a maquina nao pertence ao grupo $group!";
			}
		?>
		<br><br>
		<input type="submit" value="Validar">
		<br>
	</form>
</html>