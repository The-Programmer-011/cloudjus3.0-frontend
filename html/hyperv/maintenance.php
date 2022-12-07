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

<!--Header html-->
<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<style>

input[type=button].yes{
	background-color: #00e600;
}

input[type=button].yes:hover{
	background-color: #00cc00;
}

input[type=button].yes:active{
	background-color: #009900;
}

input[type=button].no{
	background-color: #e62e00;
	color: white;
}

input[type=button].no:hover{
	background-color: #cc2900;
}

input[type=button].no:active{
	background-color: #991f00;
}

</style>
<title>Manutenção</title>
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
	}
	//echo $error;

	if(!$error){
		$vm = $_GET['nome_maquina'];
		if(!$_GET['maintenance']){
			if($_GET['data-inicio'] && $_GET['data-final']){
				$inicio = $_GET['data-inicio'];
				$final = $_GET['data-final'];
				if($_GET['descricao']){
					$descricao = $_GET['descricao'];
				}

				//echo $inicio . "<br>";
				//echo $final . "<br>";

				if($inicio <= $final){
					$motivo = $_GET['motivo'];
					header("Location: requests/request_validation.php?nome_maquina=$vm&data_inicio=$inicio&data_final=$final&descricao=$descricao&motivo=$motivo");
				}
				else{
					$error = 1;
				}
			}
			else{
				?>
				<?php include_once("../Assets/navbar.php"); ?>
				<h1>Gostaria de deixar a maquina para manutenção após o desligamento?</h1>
				<a href="maintenance.php?nome_maquina=<?php echo $vm; ?>&maintenance=1"><input type='button' class='yes' value='yes'></a> <a href="requests/request_validation.php?nome_maquina=<?php echo $vm; ?>"><input type='button' class='no' value='no'></a>

				<?php
			}	
		}
		if($_GET['maintenance'] || $error){?>
			<?php include_once("../Assets/navbar.php"); ?>
			<h1>Selecione o tipo de manutenção para a maquina</h1>
			<form method="get">
				<input type="text" name="nome_maquina" value="<?php echo $vm; ?>" readonly>
				<br>
				<h2>Motivo</h2>
				<input type="text" name="motivo" required>
				<br>
				<h2>Início</h2>
				<?php if($error){echo "<strong>*Favor digitar datas válidas*</strong><br>";}?>
				<input type="datetime-local" name="data-inicio">
				<br>
				<h2>Fim</h2>
				<?php if($error){echo "<strong>*Favor digitar datas válidas*</strong><br>";}?>
				<input type="datetime-local" name="data-final">
				<br>
				<input type="submit" value="Enviar pedido">
			</form>
			<?php	
		}
	}
	?>

</body>
</html>