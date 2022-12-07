<?php
session_start();
if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][1]=="0" || $_SESSION['administrador'][1]>"3"){
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
	$error = 1;
	$error--;
	if($_GET["nome_maquina"]==""){
		$error++;
		$null_name = 1;
	}
	else{
		$error++;
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
					//echo strlen($word_array[1]);
					if(strlen($word_array[1]) == 9){ //9 eh para "Running", 5 eh para "off"
						$machine_on = 1;
						$error = 1;
						break;
					}
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
			$ram = $_GET['ram'];
			header("Location: requests/request_validation.php?nome_maquina=$vm&ram=$ram");
		}
	}
	?>

	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Alterar quantidade de memória de uma VM</h1>
	<form action="<?php if(!$error){echo '/hyperv/requests/request_validation.php';}?>" method="get" oninput="ram_amount.value=parseInt(ram.value);">
	<p>Nome da máquina virtual:</p>
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
	<p>Quantidade de memória: <output name="ram_amount" for="ram"></output> MB</p>
	<input type="range" name="ram" class="slider" id="myRange" min="512" max="16384" step="512" value="<?php echo $_GET['core_number'];?>">
	<br><br>
	<?php
		if(!$error){
			echo "<p style='color:#66ff33'>Informacoes válidas<br></p>";
			echo '<input type="submit" value="Alterar memória">';
		}
		else{
			echo '<input type="submit" value="Validar">';	
		}
	?>
	</form>
</body>
</html>
