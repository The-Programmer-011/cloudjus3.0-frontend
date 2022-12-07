<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][1]=="0" || $_SESSION['administrador'][1]>"2"){
	header("Location: /index.php?op=err");
	$_SESSION['denied'] = 1;
}
if($_SESSION['administrador'][1]=="2"){
	if(!isset($_SESSION['custom'])){
		header("Location: TemplateVMCluster.php");
	}
}


?>

<html lang="pt-BR">
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>Create VM Cluster</title>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<body>
	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Criar máquinas virtuais em bloco</h1>
	<?php
	if(isset($_SESSION['custom'])){
		echo "<p><i>Obs: Seu pedido será submetido ao fluxo de aprovação antes de ser executado.</i></p>";	
	}
	?>
	<form action="/hyperv/requests/request_validation.php" method="get" oninput="core.value=parseInt(core_number.value); ram_amount.value=parseInt(ram.value)">
		<p>Nome da nova maquina virtual:</p><br>
		<input type="text" name="nome_maquina" value="<?php echo strtoupper($_GET['nome_maquina']); ?>" readonly>
		<br><br>
		<p>Servidor Host:</p>
		<br>
		<?php
		$file = fopen("requests/Lists/hosts.txt", "r");
		$cont = 1;
		while(!feof($file)){
			$host = fgets($file);
			if($host=="");
			else{
				if($cont==$_GET['servidor_host']){
					echo "<input style='width:1px' type='text' name='servidor_host' value='$cont' readonly> $host";
				}
				$cont++;
			}
		}
		fclose($file);
		?>
		<br><br>
		<p>Disco:</p>
		<br><br>
		
		<select name="hardDisk">
			<?php
			$host_num = $_GET['servidor_host'] - 1;
			$file_name = "requests/Lists/" . $host_num . " disks.txt";
			$file = fopen($file_name, "r");
			$cont = 1;
			while(!feof($file)){
				$disk = fgets($file);
				if($disk=="");
				else{
					if($cont==$_GET['hardDisk']){
						echo "<option value='$cont' selected>$disk</option>";
					}
					else{
						echo "<option value='$cont'>$disk</option>";
					}
					$cont++;
				}
			}
			fclose($file);
			?>
		</select>
		<br><br>
		<p>Templates:</p>
		<br><br>
		<select name="template">
			<?php
			$host_num = $_GET['servidor_host'] - 1;
			$file_name = "requests/Lists/" . $host_num . " templates.txt";
			$file = fopen($file_name, "r");
			$cont = 1;
			while(!feof($file)){
				$template = fgets($file);
				if($template=="");
				else{
					if($cont==$_GET['template']){
						echo "<option value='$cont' selected>$template</option>";
					}
					else{
						echo "<option value='$cont'>$template</option>";
					}
					$cont++;
				}
			}
			fclose($file);
			?>
		</select>
		<br><br>
		<p>Numero de processadores: <output name="core" for="core_number"></output></p>
		<?php if(isset($_SESSION['custom'])){ ?>
		1
		<input type="range" name="core_number" class="slider" id="myRange" min="1" max="4" value="1">
		4
		<?php }else{ ?>
		1
		<input type="range" name="core_number" class="slider" id="myRange" min="1" max="24" value="1">
		24
		<?php } ?>
		<br>
		<br><br>
		<p>Quantidade de memoria: <output name="ram_amount" for="ram"></output> Mb</p>
		<input type="range" name="ram" class="slider" id="myRange" min="512" max="11776" step="512" value="<?php echo $_GET['core_number'];?>">
		<br><br>
		<p>Quantidade de maquinas a serem criadas:</p>
		<input type="number" name="quantidade" min="1" max="10" value="1" required>
		<br><br><br>
		<input type="submit" value="Criar maquina virtual">
	</form>
</body>
</html>	