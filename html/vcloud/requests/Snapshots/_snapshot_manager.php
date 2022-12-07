<?php
session_start();
if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][0]=="0" || $_SESSION['administrador'][0]>"4"){
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
<?php if($_SESSION['theme'] == 'stf'){
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
	$url = $_SERVER['HTTP_REFERER'];
	$url2 = explode("/", $url);
	$url3 = explode("?", $url2[4]);
	$origin = $url3[0];
	$_GET['nome_maquina'] = strtolower($_GET['nome_maquina']);
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
		$_GET['snap'] = str_replace(' ', '', $_GET['snap']);
		$snap_name = $_GET['snap'] . "(" . $time['minutes'] . "-" . $time['seconds'] . "-" . $time['mday'] . "-" . $time['mon'] . "-" . $time['year'] . ")";
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
    	background-color: #00cc44;
    	color: white;
    	padding: 14px 20px;
    	margin: 8px 0;
    	border: none;
    	border-radius: 4px;
    	cursor: pointer;
	}
	#default:hover{
		background-color: #00b33c;
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
    	background-color: black;
    	color: black;
    	padding: 7px 10px;
    	margin: 8px 0;
	}
	
	input[type=button].black:hover{
		background-color: black;
	}
	</style>
		<?php
		$vm = $_GET['nome_maquina'];
		echo "<h1>Snapshots de $vm</h1>";
		$vm_file = $_GET['nome_maquina'] . ".txt";
		$file = fopen($vm_file, "r");
		if(!$file){
			echo "<h1>Nao ha snapshots a mostrar.</h1>";
		}
		else{
			$count=0;
			fgets($file) . "<br>";
			echo "<table class='table table-bordered'>";
			while(!feof($file)){
				fgets($file) . "<br>";
				fgets($file) . "<br>";
				$line = fgets($file);
				$line = explode(":", $line);
				$snap = str_replace(' ', '', $line[1]);
				$snap = FormatString($snap, 2);
				if($snap){
					echo "<tr>";
					echo "<td>";
					echo fgets($file) . "<br>";
					echo fgets($file) . "<br>";
					echo fgets($file) . "<br>";
					echo fgets($file) . "<br>";
					echo "Snapshot: " . $snap . "<br>";
					echo "</td>";

					echo "<td>";
					echo "<a href='_confirmation.php?nome_maquina=$vm&snap=$snap&op=2' target='_blank'><input	type='button' value='reverter' class='reverter'></a> <a href='_confirmation.php?nome_maquina=$vm&snap=$snap&op=3' 	target='blank'><input type='button' value='deletar' class='delete'></a>" .  "<br>";
					echo "</td>";
					echo "</tr>";
				}
				else{
					fgets($file);
					fgets($file);
					fgets($file);
					fgets($file);
				}
			}
			echo "</table>"
		}
		fclose($file);
	}
	else{
		$status = fopen("../_status.txt", "r");
		if($status){
			$status_num = fgets($status);
			$status_num = $status_num[2];
			fclose($status);
		}
		if(!$_SESSION['administrador']){
			header("Location: /index.php?op=err");
			$_SESSION['denied'] = 1;
		}
		$snapshot_filename = $_GET['nome_maquina'] . ".txt";
		/*
		$snapshot_file = fopen($snapshot_filename, "r");
		fgets($snapshot_file);
		fgets($snapshot_file);
		fgets($snapshot_file);
		for($count=1; $count<=$_GET['snap']; $count++){
			$line = fgets($snapshot_file);
			$line = FormatString($line, 1);
			$line = explode(" ", $line);
			$vm = $line[0];
			$cont = 1;
			while($line[$cont]==""){
				$cont++;
			}
			$snapshot_name = $line[$cont];
		}
		fclose($snapshot_file);
		*/
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
	<a href='/main.php'><input type='button' value='Voltar a pagina principal' id="default"></a>
</body>
</html>