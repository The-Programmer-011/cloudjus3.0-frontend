<?php

//Inicio de sessao
session_start();

//Caso o usuario nao esteja logado, redireciona para a pagina de login
if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
//Caso o usuario nao tenha permissao nivel 3 redireciona para a main
if($_SESSION['administrador'][1]=="0" || $_SESSION['administrador'][1]>"3"){
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
<title>Add Disk</title>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
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
					$name_not_found = 0; //Erro que informa que a mauina nao foi encontrada
					$error = 0;
					//echo strlen($word_array[1]);
					//O script testa se a maquina esta ligada ou desligada pelo tamanho da palavra
					if(strlen($word_array[1]) == 9){ //9 eh para "Running", 5 eh para "off"
						$machine_on = 1; //Erro que informa que a maquina estal ligada
						$error = 1;
						break;
					}
				}
			}
		}
		fclose($file);
		//Caso o usuario nao seja super_adm do hyperV o script testa se o ousuario tem permissao para executar esse comando na maquina escolhida
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

		//Caso nao tenha sido encontrado nenhum erro no form, o script redireciona para a proxima pagina
		if(!$error){
			$vm = $_GET['nome_maquina'];
			$disk = $_GET['disk'];
			header("Location: requests/request_validation.php?nome_maquina=$vm&disk=$disk");
		}
	}
	?>

	<!-- Adiciona a navbar -->
	<?php include_once("../Assets/navbar.php"); ?>
	
	<h1>Adicionar disco a uma VM</h1>
	<form action="<?php if(!$error){echo '/hyperv/requests/request_validation.php';}?>" method="get">
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
	<br>
	<?php
		//Caso tenha sido encontrada algum erro, dependendo do erro sera impressa a mensagem correspondente na pagina
		if($name_not_found){
			echo "* máquina não encontrada!";
		}
		else if($unauthorized_machine){
			echo "* a máquina não pertence ao grupo $group!";
		}
		else if($machine_on){
			echo "* a máquina deve estar desligada executar esta ação!";
		}
	?>
	<br>

	<!-- Input do novo tamanho de disco -->
	<p>Tamanho do disco (GB):</p>
	<input type="number" name="disk" value="<?php echo $_GET['disk'];?>" min="1" max="65536" <?php if(!$error){echo "readonly";}?> required>
	<br><br>
	<?php
		if(!$error){
			echo "<p style='color:#66ff33'>Informacoes validas<br></p>";
			echo '<input type="submit" value="Alterar disco">';
		}
		else{
			echo '<input type="submit" value="Validar">';	
		}
	?>
	</form>
</body>
</html>
