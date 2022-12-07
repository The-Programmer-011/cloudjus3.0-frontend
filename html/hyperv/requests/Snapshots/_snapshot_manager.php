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
<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>Gerenciador de Snapshots</title>
<?php if($_SESSION['theme']=="stf"){
	echo '<link rel="stylesheet" href="/Assets/stf_style.css">';
}
else{
	echo '<link rel="stylesheet" href="/Assets/vmware_style.css">';
}
?>
<body>
	<?php

	function FormatString($string, $start){
		$size = strlen($string);
		$count=$start;
		$newString = "";	
		while($count<$size){
			$newString = $newString . $string[$count];
			$count+=2;
		}
		return $newString;
	}

	function EraseFirstChar($string){
		$size = strlen($string);
		for($count=1;$count<$size;$count++){
			$newString = $newString . $string[$count];
		}
		return $newString;
	}

	$url = $_SERVER['HTTP_REFERER'];
	$url2 = explode("/", $url);
	$url3 = explode("?", $url2[4]);
	$origin = $url3[0];
	$_GET['nome_maquina'] = strtoupper($_GET['nome_maquina']);
	$time = getdate ($timestamp = time());
	$log = $time['hours'] . "-" . $time['minutes'] . "-" . $time['seconds'] . " " . $time['mday'] . "-" . $time['mon'] . "-" . $time['year'];
	if($origin == "CreateSnap.php"){

		$status = fopen("../_status.txt", "r");
		if($status){
			$status_num = fgets($status);
			$status_num = $status_num[2];
			fclose($status);
		}

		$filename = "../$1 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".txt";
		$file = fopen($filename, "w");
		if(!$file){
			echo "ERROR<br>";
		}
		fwrite($file, "$1\n");
		fwrite($file, $_GET['nome_maquina'] . "\n");
		$_GET['snap'] = str_replace(':', '-', $_GET['snap']);
		$snap_name = $_GET['snap'] . "(" . $log . ")";
		fwrite($file, $snap_name);
		fclose();
		if($status_num == 1){
			echo "<h1 style='color:#00cc44'>Snapshot criado!</h1>";
			echo "<p>Nome: $snap_name</p>";
		}
		else if($status_num == 2){
			echo "<h1 style='color:#ff9900'>Servidores ocupados, seu snapshot sera feito em breve.</h1>";
			echo "<p>Nome: $snap_name</p>";
		}
		else if($status_num == 0){
			echo "<h1 style='color:#ff3300'>Servidores OFFLINE</h1>";
			echo "<p>Seu pedido sera armazenado e sera executado assim que for possivel.</p>";
			echo "<p>Nome: $snap_name</p>";
		}
	}
	else if($origin == "ListSnap.php"){
		include_once("../navbar.php");
		?>
	<style>

	#default{
		width: 20%;
    	background-color: #3399ff;
    	color: white;
    	padding: 14px 20px;
    	margin: 8px 0;
    	border: none;
    	border-radius: 4px;
    	cursor: pointer;
	}

	#default:hover{
		background-color: #1a8cff;
	}

	input[type=button]{
    	width: 70px;
    	background-color: #3399ff;
    	color: white;
    	padding: 7px 10px;
    	margin: 8px 0;
	}
	
	input[type=button]:hover{
		background-color: #00b33c;;
	}

	input[type=button].delete{
		background-color: #ff3300;
	}

	input[type=button].delete:hover{
		background-color: #cc2900;
	}

	input[type=button].reverter{
		background-color: #ff9900;
	}

	input[type=button].reverter:hover{
		background-color: #e68a00;
	}
	input[type=button].black{
    	width: 70px;
    	background-color: transparent;
    	color: black;
    	padding: 7px 10px;
    	margin: 8px 0;
    	cursor: default;
	}

	</style>
		<?php
		$vm = $_GET['nome_maquina'];
		echo "<h1>Snapshots de $vm</h1>";
		$vmfile = $_GET['nome_maquina'] . ".txt";
		$file = fopen($vmfile, "r");
		fgets($file);
		if(feof($file)){
			echo "<h1>Nao ha snapshots a mostrar.</h1>";
		}
		else{
			echo "<br>";
			//echo "<pre><input type='button' class='black'><input type='button' class='black'>   " . fgets($file) . "</pre>";
			//echo "<pre><input type='button' class='black'><input type='button' class='black'>   " . fgets($file) . "</pre>";
			$count=0;
			//echo "<pre>";
			fgets($file);
			//fgets($file);
			while(!feof($file)){
				$count++;
				$snap_name = "";
				while($snap_name == "" && !feof($file)){
					$line = fgets($file);
					$line = explode(":", $line);
					$no_space = str_replace(" ", "", $line[0]);
					if($no_space=="Name"){
						echo "<div class='snap'>";
						$snap_name = $line[1];
						echo "Snapshot:" . $snap_name . "<br>";
					}
				}
				if($snap_name != ""){
					echo "<a href='_confirmation.php?nome_maquina=$vm&snap=$snap_name&op=2' target='_blank'><input	type='button' value='reverter' class='reverter'></a> <a href='_confirmation.php?nome_maquina=$vm&snap=$snap_name&op=3' 	target='blank'><input type='button' value='deletar' class='delete'></a>  ";
				}
				echo "</div>";
				echo "<br><br>";
			}
			//echo "</pre>";
		}
		fclose($file);
	}
	else{
		$_GET['snap'] = EraseFirstChar($_GET['snap']);
		$status = fopen("../_status.txt", "r");
		if($status){
			$status_num = fgets($status);
			$status_num = $status_num[2];
			fclose($status);
		}
		if($_GET['op']==2){
			if($status_num == 1){
				echo "<h1>Snapshot revertida com sucesso!</h1>";
			}
			else if($status_num == 2){
				echo "<h1 style='color:#ff9900'>Servidores ocupados, seu pedido sera executado em breve.</h1>";
			}
			else if($status_num == 0){
				echo "<h1 style='color:#ff3300'>Servidores OFFLINE</h1>";
				echo "<p>Seu pedido sera armazenado e sera executado assim que for possivel.</p>";
			}
			$filename = "../$2 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".txt";
			$file = fopen($filename, "w");
			fwrite($file, "$2\n");
			fwrite($file, $_GET['nome_maquina'] . "\n");
			fwrite($file, $_GET['snap']);
			fclose($file);
		}
		else if($_GET['op'] == 3){
			if($status_num == 1){
				echo "<h1>Snapshot deletada com sucesso!</h1>";
			}
			else if($status_num == 2){
				echo "<h1 style='color:#ff9900'>Servidores ocupados, seu pedido sera executado em breve.</h1>";
			}
			else if($status_num == 0){
				echo "<h1 style='color:#ff3300'>Servidores OFFLINE</h1>";
				echo "<p>Seu pedido sera armazenado e sera executado assim que for possivel.</p>";
			}
			$filename = "../$3 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".txt";
			$file = fopen($filename, "w");
			fwrite($file, "$3\n");
			fwrite($file, $_GET['nome_maquina'] . "\n");
			fwrite($file, $_GET['snap']);
			fclose($file);
		}
	}
	?>
	<br><br><br>
	<a href='../../ListSnap.php'><input type='button' value='Voltar a consulta' id="default"></a><br><br>
	<a href='/main.php'><input type='button' value='Voltar a pagina principal' id="default"></a>
</body>
</html>