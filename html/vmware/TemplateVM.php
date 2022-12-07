<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][0]=="0" || $_SESSION['administrador'][0]>"2"){
	header("Location: /index.php?op=err");
	$_SESSION['denied'] = 1;
}
if(isset($_SESSION['custom'])){
	unset($_SESSION['custom']);
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

		$file = fopen("requests/Lists/hosts.txt", "r");
		$cont = 1;
		$bigger_size = 0;
		while(!feof($file)){
			$host = fgets($file);
			if($host=="");
			else{
				//echo $host . "<br>";
				$host = explode(" ", $host);
				$host_num = $cont;
				$host_size = floatval($host[4]);
				if($host_size > $bigger_size){
					$bigger_size = $host_size;
					$bigger_host = $host_num;
				}
				$cont++;
			}
		}
		$host = $bigger_host;
		fclose($file);

		if(isset($_GET['datastore'])){
			$file = fopen("requests/Lists/datastores.txt", "r");
			$cont = 1;
			$bigger_size = 0;
			while(!feof($file)){
				$datastore = fgets($file);
				if($datastore=="");
				else{
					//echo $datastore . "<br>";
					$datastore = explode(" ", $datastore);
					$datastore_num = $cont;
					$datastore_name = $datastore[1];
					$datastore_type = explode("_", $datastore_name);
					$datastore_type = $datastore_type[0];
					$datastore_size = str_replace(".", "", $datastore[4]);
					$datastore_size = floatval($datastore_size);
					if($datastore_type==$_GET['datastore']){
						if($datastore_size > $bigger_size){
							$bigger_size = $datastore_size;
							$bigger_datastore = $datastore_num;
						}
					}
					$cont++;
				}
			}
			$data = $bigger_datastore;
			fclose($file);
		}

		if(isset($_GET['size'])){
			$size_filename = "../vm_template/" . $_GET['size'];
			$file = fopen($size_filename, "r");
			if($file){
				$line = fgets($file);
				$line = explode(":", $line);
				$cpu = str_replace(";", "", $line[1]);

				$line = fgets($file);
				$line = explode(":", $line);
				$ram = str_replace(";", "", $line[1]);
				$ram = intval($ram) * 1024;
			}
		}
		else{
			$error = 1;
		}

		$file = fopen("requests/Lists/pastas.txt", "r");
		$cont = 1;
		echo $_SESSION['pasta'] . "<br>";
		while(!feof($file)){
			$pasta = fgets($file);
			//echo $pasta . "<br>";
			if($pasta=="");
			else{
				if(strpos($pasta, $_SESSION['pasta'])){
					$pasta_num = $cont;
				}
				$cont++;
			}
		}
		$pasta = $pasta_num;
		fclose($file);

		if(!$error){
			$vm = $_GET['nome_maquina'];
			$template = $_GET['template'];
			header("Location: requests/request_validation.php?nome_maquina=$vm&permission=1&servidor_host=$host&datastore=$data&template=$template&pasta=$pasta&ram=$ram&core_number=$cpu");
			echo "requests/request_validation.php?nome_maquina=$vm&servidor_host=$host&datastore=$data&template=$template&pasta=$pasta&core_number=$cpu&ram=$ram";
		}
	}

	?>

	<?php include_once("../Assets/navbar.php"); ?>
	<h1>Criar máquina virtual</h1>
	<form>
		<p>Nome da nova VM:</p><br>
		<?php
			if($error){
				echo "* VM já existe <br>";
			}
		?>
		<input type="text" name="nome_maquina" value="<?php echo $_GET['nome_maquina']; ?>" required>
		<br><br>

		<p>Templates:</p>

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
		<br><br><br>

		<table style="width: 25%">
			<tr>
				<th>Instância</th>
				<th>#Cores (vCPU)</th>
				<th>#RAM (GB)</th>
				<th style="width: 20%">Selecione:</th>
			</tr>
			
			<?php
			$vm_templates = scandir("../vm_template");
			$len = count($vm_templates);
			for($count=0; $count<$len; $count++){
				//echo $vm_templates[$count] . "<br>";
				if($vm_templates[$count]=="." || $vm_templates[$count]=="..");
				else{
				$filename = "../vm_template/" . $vm_templates[$count];
				$file = fopen($filename, "r");
				if($file){
					echo "<tr>";

					$size_filename = $vm_templates[$count];
					$cod = explode(".", $vm_templates[$count]);
					$cod = explode("_", $cod[0]);
					$cod = $cod[1];
					echo "<td>$cod</td>";

					while(!feof($file)){
						$line = fgets($file);
						$line = explode(":", $line);
						$cell = explode(";", $line[1]);
						echo "<td>" . $cell[0] . "</td>";
					}

					echo '<td><input type="radio" name="size" value=' . $size_filename . '></td>';

					echo "</tr>";
					fclose($file);
				}
				}
			}
			?>

		</table>
		<br>

		<p>Zona de Disponibilidade:</p>

		<select name="datastore" value="<?php echo $_GET['datastore'];?>">
		<?php

		$file = fopen("requests/Lists/datastore_types.txt", "r");
		$line = fgets($file);
		fclose($file);
		$types = explode(";", $line);
		$len = count($types);
		for($count=0; $count<$len; $count++){
			$datastore_type = $types[$count];
			if($datastore_type!=""){
				$num = $count+1;
				if($datastore_type == $_GET['datastore']){
					echo "<option value='$datastore_type' selected> STF Datacenter $num</option>";
				}
				else{
					echo "<option value='$datastore_type'>STF Datacenter $num</option>";
				}
			}
		}
		?>
		<!--<option value="2">dois</option>-->
		</select>
		<a href="CreateVM.php?custom=1" title="Solicitar a criação de uma VM customizada, que será submetida aos Administradores"><input type=button class="refresh" value="Custom" style="padding: 14px 12px;"></a>

		<br><br>

	<input type='submit' value='Validar'>
	</form>
</body>
</html>
