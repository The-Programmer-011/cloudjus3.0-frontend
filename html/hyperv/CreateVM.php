<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}

if(isset($_GET['custom'])){
	$_SESSION['custom'] = 1;
}

if($_SESSION['administrador'][0]=="0" || $_SESSION['administrador'][0]>"2"){
	header("Location: /index.php?op=err");
	$_SESSION['denied'] = 1;
}

if($_SESSION['administrador'][0]=="2"){
	if(!isset($_SESSION['custom'])){
		header("Location: g_TemplateVM.php");
	}
}



?>

<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>Create VM</title>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<link rel="icon">
<body>
	<?php
	if($_GET['nome_maquina']!=""){
		$_GET['nome_maquina'] = strtolower ($_GET['nome_maquina']);
		$file = fopen("requests/vm_names.txt", "r");
		$error = 0;
		while(!feof($file)){
			$line = fgets($file);
			$line = explode(" ", $line);
			if($line[0]==$_GET['nome_maquina']){
				$error++;
				break;
			}
		}
		fclose($file);
		if(!$error){
			$vm = $_GET['nome_maquina'];
			$host = $_GET['servidor_host'];
			$data = $_GET['datastore'];
			$template = $_GET['template'];
			$pasta = $_GET['pasta'];
			$core = $_GET['core_number'];
			$ram = $_GET['ram'];
			if(isset($_SESSION['custom'])){
				$permission = 0;
			}else{
				$permission = 1;
			}
			header("Location: requests/request_validation.php?nome_maquina=$vm&servidor_host=$host&datastore=$data&template=$template&pasta=$pasta&permission=$permission&core_number=$core&ram=$ram");
		}
	}

	?>
	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Criar máquina virtual</h1>
	<?php
	if(isset($_SESSION['custom'])){
		echo "<p><i>Obs: Seu pedido será submetido ao fluxo de aprovação antes de ser executado.</i></p>";	
	}
	?>
	<form oninput="core.value=parseInt(core_number.value); ram_amount.value=parseInt(ram.value);">
		<p>Nome da VM:</p><br>
		<?php
			if($error){
				echo "* VM já existe <br>";
			}
		?>
		<input type="text" name="nome_maquina" value="<?php echo $_GET['nome_maquina']; ?>" required>
		<br><br>

		<p>Selecione o Host:</p>

		<select name="servidor_host" value="<?php echo $_GET['servidor_host'];?>">
			<?php
			$file = fopen("requests/Lists/hosts.txt", "r");
			$cont = 1;
			while(!feof($file)){
				$host = fgets($file);
				if($host=="");
				else{
					if($cont==$_GET['servidor_host']){
						echo "<option value='$cont' selected>$host</option>";
					}
					else{
						echo "<option value='$cont'>$host</option>";
					}
					$cont++;
				}
			}
			fclose($file);
			?>
		</select>
		<br><br>

		<p>Selecione o Repositório:</p>

		<select name="datastore" value="<?php echo $_GET['datastore'];?>">
			<?php

			$file = fopen("requests/Lists/datastores.txt", "r");
			$cont = 1;
			while(!feof($file)){
				$data = fgets($file);
				if($data=="");
				else{
					if($cont==$_GET['datastore']){
						echo "<option value='$cont' selected>$data</option>";
					}
					else{
						echo "<option value='$cont' selected>$data</option>";
					}
					$cont++;
				}
			}
			fclose($file);
			?>
		</select>
		<br><br>

		<p>Template Base:</p>

		<select name="template">
			<?php
			$file = fopen("requests/Lists/templates.txt", "r");
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

		<p>Unidade Responsável:</p>

		<select name="pasta">
			<?php
			$file = fopen("requests/Lists/pastas.txt", "r");
			$cont = 1;
			while(!feof($file)){
				$pasta = fgets($file);
				if($pasta=="");
				else{
					if($cont==$_GET['pasta']){
						echo "<option value='$cont' selected>$pasta</option>"; 
					}
					else{
						echo "<option value='$cont'>$pasta</option>";
					}
					$cont++;
				}
			}			

			?>
		</select>
		<br><br>

		<p>Quantidade de vCPUs: <output name="core" for="core_number"></output></p>
		1
		<?php if(isset($_SESSION['custom'])){ ?>
			<input type="range" name="core_number" class="slider" id="myRange" min="1" max="4" value="<?php if(!isset($_GET['core_number'])){echo '1';}else{ echo $_GET['core_number']; }?>">
			4
		<?php }else{ ?>
		<input type="range" name="core_number" class="slider" id="myRange" min="1" max="8" value="<?php if(!isset($_GET['core_number'])){echo '1';}else{ echo $_GET['core_number']; }?>">
			24
		<?php } ?>
		<br><br>

		<p>Quantidade de Memória:<output name="ram_amount" for="ram"></output>MB</p>
		<input type="range" name="ram" class="slider" id="myRange" min="512" max="16384" step="512" value="<?php echo $_GET['core_number'];?>">
	
	<br><br><br>
	<input type='submit' value='Validar'>
	</form>
</body>
</html>
