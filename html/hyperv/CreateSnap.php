<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][1]=="0" || $_SESSION['administrador'][1]>"4"){
	header("Location: /index.php?op=err");
	$_SESSION['denied'] = 1;
}

?>
<?php
	$error++;
	if($_GET['nome_maquina']!=""){
		$name_not_found = 1;
		$_GET['nome_maquina'] = strtoupper($_GET['nome_maquina']);
		$search = $_GET["nome_maquina"];
		$file = fopen("requests/vms_names.txt", "r");
		if($file){
			while(!feof($file)){
				$line = fgets($file);
				$word_array = explode(" ", $line);
				if($search == $word_array[0]){
					$name_not_found = 0;
					$error = 0;
				}
			}
		}
		fclose($file);

	if($_SESSION['administrador'][1]!="1"){
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
			$snap = $_GET['snap'];
			header("Location: requests/Snapshots/_snapshot_manager.php?nome_maquina=$vm&snap=$snap");
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>Criar Snapshot</title>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<body>
	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Criar Snapshots de uma máquina virtual</h1>
	<br><br>
	<form action="<?php if(!$error){echo '/hyperv/requests/Snapshots/_snapshot_manager.php';}?>" method="get">
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
				echo "<br>* máquina nao encontrada!";
			}
			else if($unauthorized_machine){
				echo "* a máquina nao pertence ao grupo $group!";
			}
		?>
		<br><br>
		<p>Nome do snapshot:</p>
		<input type="text" name="snap" value="<?php echo $_GET['snap'];?>" >
		<br><br>		
		<?php
		if(!$error){
			echo "<p style='color:#66ff33'>Maquina encontrada<br></p>";
			echo '<input type="submit" value="Continuar">';
		}
		else{
			echo '<input type="submit" value="Validar" target:"_blank">';
		}
		?>
		<br>
	</form>
</html>
