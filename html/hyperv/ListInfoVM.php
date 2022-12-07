<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}


?>

<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>Show VM Info</title>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<body>
	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Listar informacoes sobre uma VM</h1>
	<form action="table.php" method="get">
		<p>Nome da maquina virtual (favor utilizar caps lock):</p>
		<input list="vms" name="vm" value="<?php echo $_GET['nome_maquina'];?>" required>
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
		<br><br>
		<input type="submit" value="Procurar maquina virtual">
	</form>
</body>
</html>