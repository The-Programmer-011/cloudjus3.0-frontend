<?php

//Inicio de sessao
session_start();

//Caso o usuario nao esteja logado, redireciona para a pagina de login
if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
//Caso o usuario nao tenha permissao nivel 3 redireciona para a main
if($_SESSION['administrador'][1]=="0" || $_SESSION['administrador'][1]>"2"){
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
<title>Backup</title>
<body>
	<?php
	//Verifica se o o form ja foi preenchido
	$error = 1;
	$error--;
	if($_GET["nome_maquina"]==""){
		$error++;
		$null_name = 1;
	}
	//Caso o form tenha sido preenchido, o php faz a consistencia de dados
	else{
		$error++;
		$name_not_found = 1;
		$_GET['nome_maquina'] = strtoupper($_GET['nome_maquina']); //Passa o nome da maquina para maiusculo
		$search = $_GET["nome_maquina"];
		$file = fopen("requests/vms_names.txt", "r"); //Abre o arquivo com as informacoes das maquinas virtuais
		if($file){
			while(!feof($file)){ //Testa se a maquina existe
				$line = fgets($file);
				$word_array = explode(" ", $line);
				if($search == $word_array[0]){
					$name_not_found = 0; //Erro que informa que a maquina nao foi encontrada
					$error = 0;
				}
			}
		}
		fclose($file);

		//Caso o usuario nao seja super_adm do hyperV o script testa se o usuario tem permissao para executar esse comando na maquina escolhida
		if($_SESSION['administrador'][1]!="1"){
			$filename = "requests/Groups/" . $_SESSION['grupo'] . ".txt"; //Abre o arquivo com as maquinas que o grupo do usuario tem permissao
			//echo $filename;
			$file = fopen($filename, "r");
			$unauthorized_machine=1;
			$line = fgets($file);
			$line = explode(";", $line);
			$len = count($line);
			$error++;
			for($count=0;$count<$len;$count++){ //Procura a maquina escolhida no arquivo
				$vm = $line[$count];
				if($_GET['nome_maquina'] == $vm){
					$unauthorized_machine=0; //Erro que informa que o usuario nao tem permissao para executar essa acao nesta maquina
					$error--;
					break;
				}
			}
			fclose($file);
		}
	}

	if($error){
		header("Location: /index.php?op=err");
		$_SESSION['denied'] = 1;
	}

	if($_GET['policy_code']){
		$policy_code = $_GET['policy_code'];
		$vm = $_GET['nome_maquina'];
		header("Location: requests/request_validation.php?nome_maquina=$vm&policy_code=$policy_code");
	}
	
	?>

	<!-- Adiciona a navbar -->
	<?php include_once("../Assets/navbar.php"); ?>

	<form method="get">

	<h2>Máquina Virtual:</h2>
	<br>
	<input type="text" name="nome_maquina" value="<?php echo $_GET['nome_maquina'];?>" readonly>

	<h3>Política Atual: 

	<?php if($_GET['policy']){ ?>
	</h3>
	<br>
	<?php

	$file = fopen("requests/_backup.txt", "r");

	while(!feof($file)){
		$line = fgets($file);
		$element = explode(",", $line);
		$vm_name = $element[0];
		if($vm_name==$_GET['nome_maquina']){
			$aux = explode(";", $element[1]);
			$backup_policy = $aux[0];
			break;
		}
	}

	if($backup_policy){
		$files_in_dir = scandir("../backup/");
		$len = count($files_in_dir);
		for($count=2;$count<$len;$count++){
			if($files_in_dir[$count][0] == $backup_policy){
				$filename = "../backup/" . $files_in_dir[$count];
				$backup_file = fopen($filename, "r");

				//SETANDO VARIAVEIS

				$line = fgets($backup_file);
				$line = explode(";", $line);
				$line = $line[0];
				$element = explode(":", $line);
				$days = $element[1];

				$line = fgets($backup_file);
				$line = explode(";", $line);
				$line = $line[0];
				$element = explode(":", $line);
				$months = $element[1];

				$line = fgets($backup_file);
				$line = explode(";", $line);
				$line = $line[0];
				$element = explode(":", $line);
				$years = $element[1];

				$line = fgets($backup_file);
				$line = explode(";", $line);
				$line = $line[0];
				$element = explode(":", $line);
				$policy = $element[1];

				fclose($backup_file);

				$aux = explode("_", $files_in_dir[$count]);
				$aux = explode(".", $aux[1]);
				$policy_name = $aux[0];

				//FIM

				echo '<table style="width: 25%" class="background">';

				echo "<tr>";
				echo "<th>Tipo</th>";
				echo "<td>$policy_name</td>";
				echo "</tr>";

				echo "<th>Frequencia</th>";

				if($days){
					echo "<td>$days dias</td>";
				}
				else if($months){
					echo "<td>$months meses</td>";
				}
				else if($years){
					echo "<td>$years anos</td>";
				}

				echo "<tr>";
				echo "<th>Polítca</th>";
				echo "<td>$policy</td>";
				echo "</tr>";

				echo "</table>";

				echo "<br><br>";

				break;
			}
		}
	}
}
else{
	echo "<span style='color:red'>NENHUMA</span></h3>";
}
?>

<h3>Nova Política de backup:</h3>
<br>

	<table style="width: 25%" class="background">
		<tr>
			<th>Nome</th>
			<th>Backup</th>
			<th>Política</th>
			<th style="width: 20%">Selecione:</th>
		</tr>

		<?php

		$files_in_dir = scandir("../backup/");
		$len = count($files_in_dir);
		for($count=2;$count<$len;$count++){

			echo "<tr>";

			$filename = "../backup/" . $files_in_dir[$count];
			$backup_file = fopen($filename, "r");
			$cod = $files_in_dir[$count][0];

			//SETANDO VARIAVEIS

			$line = fgets($backup_file);
			$line = explode(";", $line);
			$line = $line[0];
			$element = explode(":", $line);
			$days = $element[1];

			$line = fgets($backup_file);
			$line = explode(";", $line);
			$line = $line[0];
			$element = explode(":", $line);
			$months = $element[1];

			$line = fgets($backup_file);
			$line = explode(";", $line);
			$line = $line[0];
			$element = explode(":", $line);
			$years = $element[1];

			$line = fgets($backup_file);
			$line = explode(";", $line);
			$line = $line[0];
			$element = explode(":", $line);
			$policy = $element[1];

			fclose($backup_file);
			
			$aux = explode("_", $files_in_dir[$count]);
			$aux = explode(".", $aux[1]);
			$policy_name = $aux[0];

			echo "<td>$policy_name</td>";

			if($days){
				echo "<td>$days dias</td>";
			}
			else if($months){
				echo "<td>$months meses</td>";
			}
			else if($years){
				echo "<td>$years anos</td>";
			}

			echo "<td>$policy</td>";

			echo '<td><input type="radio" name="policy_code" value=' . $cod . '></td>';

			fclose($backup_file);

			echo "</tr>";
		}
?>
</table>
<br><br>
<input type="submit" value="Confirmar Mudança">
<br>
<a href="backup.php"><input type="button" value="Voltar"></a>

</form>
</body>
</html>