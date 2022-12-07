<?php

//Inicio de sessao
session_start();

//Caso o usuario nao esteja logado, redireciona para a pagina de login
if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
//Caso o usuario nao tenha permissao nivel 3 redireciona para a main
if($_SESSION['administrador'][1]=="0" || $_SESSION['administrador'][1]>"5"){
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
	?>

	<!-- Adiciona a navbar -->
	<?php include_once("../Assets/navbar.php"); ?>
	
	<h1>Backup</h1>
	<form method="get">

	<?php

	if($error){
	?>
	<p>Nome da máquina virtual:</p>

	<!--Input do nome da maquina -->
	<input list="vms" name="nome_maquina" value="<?php echo $_GET['nome_maquina'];?>" required>
	
	<!-- Mostra a lista de VMs em que o usuario pode executar o comando -->
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
	<?php
		//Caso tenha sido encontrada algum erro, dependendo do erro sera impressa a mensagem correspondente na pagina
		if($name_not_found){
			echo "* máquina não encontrada!<br><br>";
		}
		else if($unauthorized_machine){
			echo "* a máquina não pertence ao grupo $group!<br><br>";
		}

		echo '<input type="submit" value="Validar"><br><br>';
		}
		else{
	?>
	<h2>Máquina Virtual: <?php echo $_GET['nome_maquina'];?></h2>
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

	$vm = $_GET['nome_maquina'];

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
				echo "</div>";

				echo "<br><br>";

				echo '<a href="assign_backup.php?nome_maquina=' . $vm . '&policy=' . $backup_policy . '"><input type="button" value="Modificar política de backup"</input></a>';

				break;
			}
		}
	}

	else{
		echo "<h3 style='color:red'>Nenhuma política de backup encontrada para essa maquina.</h3>";
		echo "<br>";
		echo '<a href="assign_backup.php?nome_maquina=' . $vm . '"><input type="button" value="Adicionar maquina à politica de backup"</input></a>';
	}

	fclose($file);
	?>

	<br>
	<a href="/main.php"><input type="button" value="Página Inicial"></a>

<?php } ?>
<br>
<a href="backup_list.php"><input type="button" value="Lista de Máquinas"></a>

</form>
</body>
</html>