<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: /index.php?op=err");
}

function CreateTemplateJSON($filename, $user, $vm, $log, $group){
	$path = "Requests/" . $filename;
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
	$file = fopen($path, "w");
	if(!$file){
		echo "ERRO";
	}
	fwrite($file, "{\n");
	fwrite($file, '"operation":1' . ",\n");
	fwrite($file, '"permission":1' . ",\n");
	fwrite($file, '"vm":"' . $vm . '"' . ",\n");
	fwrite($file, '"host":' . $_GET['servidor_host'] . ",\n");
	fwrite($file, '"disk":' . $_GET['datastore'] . ",\n");
	fwrite($file, '"template":' . $_GET['template'] . ",\n");
	fwrite($file, '"pasta":' . $_GET['pasta'] . ",\n");
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
	if(!$file){
		echo "ERRO NO PEDIDO!<br><br>";
	}
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
	if(!$file){
		echo "ERRO NO PEDIDO!<br><br>";
	}
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
	if(!$file){
		echo "ERRO NO PEDIDO!<br><br>";
	}
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
	if(!$file){
		echo "ERRO NO PEDIDO!<br><br>";
	}
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
	if(!$file){
		echo "ERRO NO PEDIDO!<br><br>";
	}
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
	if(!$file){
		echo "ERRO NO PEDIDO!<br><br>";
	}
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
	if(!$file){
		echo "ERRO NO PEDIDO!<br><br>";
	}
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
	if(!$file){
		echo "ERRO NO PEDIDO!<br><br>";
	}
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
	if(!$file){
		echo "ERRO NO PEDIDO!<br><br>";
	}
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
//echo $origin;
$time = getdate ($timestamp = time());
$log = $time['hours'] . "-" . $time['minutes'] . "-" . $time['seconds'] . " " . $time['mday'] . "-" . $time['mon'] . "-" . $time['year'];

if($origin=="SmallList.php" || $origin=="g_SmallList.php"){
	$origin = $_GET['origin'];
}
if($origin=="CreateVM.php" || $origin == "TemplateVM.php"  || $origin=="g_CreateVM.php" || $origin == "g_TemplateVM.php"){
	$filename = "1 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
	CreateTemplateJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
}
else if($origin=="CreateVMCluster.php" || $origin=="TemplateVMCluster.php" || $origin=="g_CreateVMCluster.php" || $origin=="g_TemplateVMCluster.php"){
	$VMnumber = $_GET['quantidade'];
	$vm = $_GET['nome_maquina'];
	$number = 1;
	for($count=1; $count<=$VMnumber; $count++){
		do{
			$_GET['nome_maquina'] = $vm;
			$_GET['nome_maquina'] = $_GET['nome_maquina'] . $number;
			$file = fopen("vm_names.txt", "r");
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
			$number++;
		}while($error);
		$filename = "1 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
		CreateTemplateJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
	}
}
else if($origin=="DelVM.php" || $origin=="DelVMConfirmation.php" || $origin=="g_DelVM.php" || $origin=="g_DelVMConfirmation.php"){
	$filename = "3 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
	DelVMJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
}
else if($origin=="AddDisk.php" || $origin=="g_AddDisk.php"){
	$filename = "4 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
	AddDiskJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
}
else if($origin == "AltCore.php" || $origin == "g_AltCore.php"){
	$filename = "5 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
	ChangeCoreJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
}
else if($origin == "AltMem.php" || $origin == "g_AltMem.php"){
	$filename = "6 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
	ChangeMemJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
}
else if($origin == "ShutdownOS.php" || $origin == "g_ShutdownOS.php"){
	$filename = "7 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
	ShutdownOSJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
}
else if($origin == "RestartOS.php" || $origin == "g_RestartOS.php"){
	$filename = "8 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
	RestartOSJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
}
else if($origin == "PwrOFF.php" || $origin == "g_PwrOFF.php"){
	$filename = "9 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
	PwrOFFJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']);
}
else if($origin == "PwrON.php" || $origin == "g_PwrON.php"){
	$filename = "a " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
	PwrONJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']); 
}
else if($origin == "assign_backup.php" || $origin == "g_assign_backup.php"){
	$filename = "b " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
	BackupJSON($filename, $_SESSION['username'], $_GET['nome_maquina'], $log, $_SESSION['grupo']); 
}
else if($origin == "ListInfoVMs.php"){
	$filename = "0.txt";
	$file = fopen($filename, "w");
	sleep (1);
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

if(isset($_SESSION['operation'])){
	unset($_SESSION['operation']);
}

if(isset($_SESSION['alert_off'])){
	unset($_SESSION['alert_off']);
}

$_SESSION['pedido'] = "Processed/" . $filename;
if(isset($_SESSION['gentelella'])){
	header("Location: ../../g_menu.php?request=1");	
}
else{
	header("Location: success.php");
}
echo '<a href="' . $filename . '">link</a>';
?>