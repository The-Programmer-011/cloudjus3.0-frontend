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
<title>Validate VM</title>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<body>
	<?php include_once("../Assets/navbar.php"); ?>
	<?php

	function CreateTemplateJSON($filename, $user, $vm, $log, $group){
		$path = "Requests/" . $filename;
		$file = fopen($path, "w");
		if(!$file){
			echo "ERRO";
		}
		else{
			if(isset($_SESSION['custom'])){
				$permission_filename = "Requests/" . $filename;
				$file = fopen($permission_filename, "w");
				fwrite($file, "{\n");
				fwrite($file, '"operation":1' . ",\n");
				fwrite($file, '"permission":0' . ",\n");
				fwrite($file, '"vm":"' . $vm . '"' . ",\n");
				fwrite($file, '"user":"' . $user . '"' . ",\n");
				fwrite($file, '"log":"' . $log . '"' . ",\n");
				fwrite($file, '"group":"' . $group . '"' . "\n");
				fwrite($file, "}");

				fclose($file);

				$path = "ToProcess/" . $filename;
			}
		}
		
		$file = fopen($path, "w");
		if(!$file){
			echo "ERRO";
		}
		$file = fopen($path, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":1' . ",\n");
		fwrite($file, '"permission":1' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"host":' . $_GET['servidor_host'] . ",\n");
		fwrite($file, '"disk":' . $_GET['hardDisk'] . ",\n");
		fwrite($file, '"template":' . $_GET['template'] . ",\n");
		fwrite($file, '"cpu":' . $_GET['core_number'] . ",\n");
		fwrite($file, '"ram":' . $_GET['ram'] . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}

	function DelVMJSON($filename, $user, $vm, $log, $group){
		$filename = "Requests/" . $filename;
		$file = fopen($filename, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":3' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}

	function AddDiskJSON($filename, $user, $vm, $log, $group){
		$filename = "Requests/" . $filename;
		$file = fopen($filename, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":4' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"disk":' . $_GET['disk'] . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}

	function ChangeCoreJSON($filename, $user, $vm, $log, $group){
		$filename = "Requests/" . $filename;
		$file = fopen($filename, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":5' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"cpu":' . $_GET['core_number'] . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}

	function ChangeMemJSON($filename, $user, $vm, $log, $group){
		$filename = "Requests/" . $filename;
		$file = fopen($filename, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":6' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"ram":' . $_GET['ram'] . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}

	function ShutdownOSJSON($filename, $user, $vm, $log, $group){
		$filename = "Requests/" . $filename;
		$file = fopen($filename, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":7' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}

	function RestartOSJSON($filename, $user, $vm, $log, $group){
		$filename = "Requests/" . $filename;
		$file = fopen($filename, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":8' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}

	function PwrOFFJSON($filename, $user, $vm, $log, $group){
		$filename = "Requests/" . $filename;
		$file = fopen($filename, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":9' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}

	function PwrONJSON($filename, $user, $vm, $log, $group){
		$filename = "Requests/" . $filename;
		$file = fopen($filename, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":10' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}

	function BackupJSON($filename, $user, $vm, $log, $group){
		$filename = "Requests/" . $filename;
		$file = fopen($filename, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":11' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"backup":' . $_GET['policy_code'] . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}

function MaintenanceJSON($filename, $user, $vm, $log, $group){
	$vm_name = strtoupper($_GET['nome_maquina']);
	
	$maintenance_name = $_GET['motivo'];
	$maintenance_name = explode(";", $maintenance_name);
	$maintenance_name = $maintenance_name[0];
	
	$start_time = $_GET['data_inicio'];
	$start_time = explode(";", $start_time);
	$start_time = $start_time[0];
	$start_time = explode("T", $start_time);
	$start_date = $start_time[0];
	$start_time = $start_time[1];
	$start_date = explode("-", $start_date);
	$start_year = $start_date[0];
	$start_month = $start_date[1];
	$start_day = $start_date[2];
	$start_time = $start_day . "-" . $start_month . "-" . $start_year . " " . $start_time;
	
	$end_time = $_GET['data_final'];
	$end_time = explode(";", $end_time);
	$end_time = $end_time[0];
	$end_time = explode("T", $end_time);
	$end_date = $end_time[0];
	$end_time = $end_time[1];
	$end_date = explode("-", $end_date);
	$end_year = $end_date[0];
	$end_month = $end_date[1];
	$end_day = $end_date[2];
	$end_time = $end_day . "-" . $end_month . "-" . $end_year . " " . $end_time;
	
	$description = $_GET['descricao'];
	$description = explode(";", $description);
	$description = $description[0];
	
	$start_input = $start_time;
	$end_input = $end_time;
	echo "$vm_name<br>";
	echo "$maintenance_name<br>";
	echo "$start_time<br>";
	echo "$end_time<br>";
	echo "$description<br>";
	$login = system('curl --insecure -s -i -X POST -H "Content-Type: application/json-rpc" -d "{
	\"jsonrpc\": \"2.0\",
	\"method\": \"user.login\",
	\"params\": {
		\"password\": \"St0ckc@r\",
		\"user\": \"srv_ansible\"
	},
	\"id\": 0
	}" https://monitoramento.stf.jus.br/api_jsonrpc.php ');
	$login = explode('"', $login);
	
	$auth = $login[7];
	echo "<br><br>";
	echo $auth;
	echo "<br><br>";
	$get_host = system('curl --insecure -s -i -X POST -H "Content-Type: application/json-rpc" -d "{
	    "\""jsonrpc"\"": "\""2.0"\"",
	    "\""method"\"": "\""host.get"\"",
	    "\""params"\"": {
	        "\""output"\"": "\""extend"\"",
	        "\""filter"\"": {
	            "\""host"\"":"\""' . $vm_name . '"\""	
	        }
	    },
	    "\""auth"\"": "\""' . $auth . '"\"",
	    "\""id"\"": 1
	}" https://monitoramento.stf.jus.br/api_jsonrpc.php');
	$get_host = explode('"', $get_host);
	$hostid = $get_host[9];
	
	echo "<br><br>";
	echo $hostid;
	echo "<br><br>";
	$start_time = strtotime($start_time);
	$end_time =  strtotime($end_time);
	echo $start_time;
	echo "<br>";
	echo $end_time;
	echo "<br>";
	$duration = $end_time - $start_time;
	echo $duration;
	echo "<br><br>";
	$command = system('curl --insecure -s -i -X POST -H "Content-Type: application/json-rpc" -d "{
	    \"jsonrpc\": \"2.0\",
	    \"method\": \"maintenance.create\",
	    \"params\": {
	        \"name\": \"' . $maintenance_name . '\",
	        \"active_since\": ' . $start_time . ',
	        \"active_till\": ' . $end_time . ',
	        \"hostids\": [
	            \"' . $hostid . '\"
	        ],
	        \"timeperiods\": [
	            {
	            	\"timeperiod_type\": 0,
	            	\"start_date\": ' . $start_time . ',
	                \"period\": ' . $duration . '
	            }
	        ]
	    },
	    \"auth\": \"' . $auth . '\",
	    \"id\": 1
	}" https://monitoramento.stf.jus.br/api_jsonrpc.php');
	echo "<br>";
	echo $command;
	$command = explode('"', $command);
	$maintenance_id = $command[9];
	echo "<br><br>";
	echo $maintenance_id;

	if($maintenance_id == "message"){
		$_SESSION['maintenance_error'] = 1;
	}
	else{
		$filename = "Processed/" . $filename;
		$file = fopen($filename, "w");
		fwrite($file, "{\n");
		fwrite($file, '"operation":12' . ",\n");
		fwrite($file, '"vm":"' . $vm . '"' . ",\n");
		fwrite($file, '"start":"' . $start_input . '"' . ",\n");
		fwrite($file, '"end":"' . $end_input . '"' . ",\n");
		fwrite($file, '"id":' . $maintenance_id . ",\n");
		fwrite($file, '"user":"' . $user . '"' . ",\n");
		fwrite($file, '"log":"' . $log . '"' . ",\n");
		fwrite($file, '"group":"' . $group . '"' . "\n");
		fwrite($file, "}");
		fclose($file);
	}
}


	$url = $_SERVER['HTTP_REFERER'];
	$url2 = explode("/", $url);
	$url3 = explode("?", $url2[4]);
	$origin = $url3[0];
	echo $origin . "<br>";
	$_GET['nome_maquina'] = strtoupper($_GET['nome_maquina']);
	$time = getdate ($timestamp = time());
	$log = $time['hours'] . "-" . $time['minutes'] . "-" . $time['seconds'] . " " . $time['mday'] . "-" . $time['mon'] . "-" . $time['year'];
	if($origin=="SmallList.php" || $origin=="g_SmallList.php"){
		$origin = $_GET['origin'];
	}
	if($origin=="CreateVM2.php" || $origin=="TemplateVM.php" || $origin=="g_CreateVM.php" || $origin=="g_TemplateVM.php"){
		echo "<h1>Pedido de criacao de VM recebido</h1>";
		echo "<h2> Por favor aguarde... <h2>";
		echo "<br>Arquivo do pedido:<br><br>";
		if(!$_GET['notemplate']){
			$filename = "1 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
			CreateTemplateJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
		}
		else{
			$filename = "2 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
			CreateNoOS($filename);
		}
	}
	else if($origin=="CreateVMCluster2.php" || $origin=="TemplateVMCluster.php" || $origin=="g_CreateVMCluster.php" || $origin=="g_TemplateVMCluster.php"){
		$VMnumber = $_GET['quantidade'];
		$vm = $_GET['nome_maquina'];
		$number = 1;
		for($count=1; $count<=$VMnumber; $count++){
			do{
				$_GET['nome_maquina'] = $vm;
				$_GET['nome_maquina'] = $_GET['nome_maquina'] . $number;

				$error = 1;
				$search = $_GET['nome_maquina'];
				$file = fopen("vms_names.txt", "r");
				if($file){
					$error = 0;
					while(!feof($file)){
						$line = fgets($file);
						$word_array = explode(" ", $line);
						if($search == $word_array[0]){
							$error = 1;
						}
					}
				}
				fclose($file);
				$number++;
			}while($error);
			$filename = "1 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
			CreateTemplateJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
		}
	}
	else if($origin=="DelVM.php" || $origin=="DelVMConfirmation.php" || $origin=="g_DelVM.php" || $origin=="g_DelVMConfirmation.php"){
		echo "<h1 id='green_header'>Pedido recebido</h1>";
		echo "<h2> Por favor aguarde... <h2>";
		echo "<br>Arquivo do pedido:<br><br>";
		$filename = "3 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
		DelVMJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
	}
	else if($origin=="AddDisk.php" || $origin=="g_AddDisk.php"){
		echo "<h1 id='green_header'>Pedido de modificacao recebido</h1>";
		echo "<h2> Por favor aguarde... <h2>";
		echo "<br>Arquivo do pedido:<br><br>";
		$filename = "4 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
		AddDiskJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
	}
	else if($origin == "AltCore.php" || $origin == "g_AltCore.php"){
		echo "<h1 id='green_header'>Pedido de modificacao recebido</h1>";
		echo "<h2> Por favor aguarde... <h2>";
		echo "<br>Arquivo do pedido:<br><br>";
		$filename = "5 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
		ChangeCoreJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
	}
	else if($origin == "AltMem.php" || $origin == "g_AltMem.php"){
		echo "<h1 id='green_header'>Pedido de modificacao recebido</h1>";
		echo "<h2> Por favor aguarde... <h2>";
		echo "<br>Arquivo do pedido:<br><br>";
		$filename = "6 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
		ChangeMemJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
	}
	else if($origin == "ShutdownOS.php" || $origin == "g_ShutdownOS.php"){
		echo "<h1 id='green_header'>Pedido de shutdown recebido</h1>";
		echo "<h2> Por favor aguarde... <h2>";
		echo "<br>Arquivo do pedido:<br><br>";
		$filename = "7 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
		ShutdownOSJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
	}
	else if($origin == "RestartOS.php" || $origin == "g_RestartOS.php"){
		echo "<h1 id='green_header'>Pedido de restart recebido</h1>";
		echo "<h2> Por favor aguarde... <h2>";
		echo "<br>Arquivo do pedido:<br><br>";
		$filename = "8 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
		RestartOSJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
	}
	else if($origin == "PwrOFF.php" || $origin == "g_PwrOFF.php"){
		echo "<h1 id='green_header'>Pedido de desligamento recebido</h1>";
		echo "<h2> Por favor aguarde... <h2>";
		echo "<br>Arquivo do pedido:<br><br>";
		$filename = "9 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
		PwrOFFJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);	
	}
	else if($origin == "PwrON.php" || $origin == "g_PwrON.php"){
		echo "<h1 id='green_header'>Pedido de ligamento recebido</h1>";
		echo "<h2> Por favor aguarde... <h2>";
		echo "<br>Arquivo do pedido:<br><br>";
		$filename = "a " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
		PwrONJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']); 
	}
	else if($origin == "assign_backup.php" || $origin == "g_assign_backup.php"){
		echo "<h1 id='green_header'>Pedido de ligamento recebido</h1>";
		echo "<h2> Por favor aguarde... <h2>";
		echo "<br>Arquivo do pedido:<br><br>";
		$filename = "b " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
		BackupJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']); 
	}
	else if($origin == "maintenance.php" || $origin == "g_maintenance.php"){
		if($_SESSION['operation'] == "7"){
			$filename = "7 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
			ShutdownOSJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
		}
		else if($_SESSION['operation'] == "9"){
			$filename = "9 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
			PwrOFFJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);	
		}
		if($_GET['data_inicio']){
			$maintenance_filename = "c " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
			MaintenanceJSON($maintenance_filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
			$_SESSION['pedido_manuntencao'] = $maintenance_filename;
		}
	}
	else if($origin == "g_mass_delete.php"){
		if(isset($_GET['vms'])){
			$len = count($_GET['vms']);
			if($len){
				for($count=0;$count<$len;$count++){
					$filename = "3 " . $_SESSION['username'] . " " . $_GET['vms'][$count] . " " . $log . " " . $_SESSION['grupo'] . ".json";
					DelVMJSON($filename, $_SESSION['username'], $_GET['vms'][$count], $log, $_SESSION['grupo']);
				}
			}
		}
	}
	$_SESSION['pedido'] = "Processed/" . $filename;
	if(isset($_SESSION['operation'])){
		unset($_SESSION['operation']);
	}

	if(isset($_SESSION['alert_off'])){
		unset($_SESSION['alert_off']);
	}

	if(isset($_SESSION['gentelella'])){
		header("Location: ../../g_menu.php?request=1");	
	}
	else{
		header("Location: success.php");
	}
	?>
	<a href="<?php echo $filename;?>">link</a>;
</body>
</html>