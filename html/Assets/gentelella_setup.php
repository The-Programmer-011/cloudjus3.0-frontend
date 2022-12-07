		<?php

		function scan_dir($dir) {
		    $ignored = array('.', '..', '.svn', '.htaccess');
		
		    $files = array();    
		    foreach (scandir($dir) as $file) {
		        if (in_array($file, $ignored)) continue;
		        $files[$file] = filemtime($dir . '/' . $file);
		    }
		
		    arsort($files);
		    $files = array_keys($files);
		
		    return ($files) ? $files : false;
		}

		function is_request ($filename){
			if($filename[0] == "1"){return "Criação de VM";}
			//else if($filename[0] == "2"){return }	
			else if($filename[0] == "3"){return "Remoção de VM";}
			else if($filename[0] == "4"){return "Adicionar Disco";}
			else if($filename[0] == "5"){return "Alterar Processadores";}
			else if($filename[0] == "6"){return "Alterar Memória";}
			else if($filename[0] == "7"){return "Shutdown VM";}
			else if($filename[0] == "8"){return "Restart VM";}
			else if($filename[0] == "9"){return "Power ON VM";}
			else if($filename[0] == "a"){return "Power OFF VM";}
			else if($filename[0] == "b"){return "Backup Alterado";}
			else if($filename[0] == "c"){return "Período de Manutenção";}
			else if($filename[0] == "$"){
				if($filename[1] == "1"){return "Criar Snapshot";}
				else if($filename[1] == "2"){return "Reverter Snapshot";}
				else if($filename[1] == "3"){return "Remover Snapshot";}
			}
			else{return 0;}
		}

		//Seta o nome do usuario
		$name = $_SESSION['name'] . " " . $_SESSION['lastname'];

		//Verifica se o usuario é convidado
		if($_SESSION['administrador'][$_SESSION['hv'] - 1] == "0"){
			$guest = 1;
		}

		//Verifica se o usuario eh super_adm e seta a foto de perfil
		if($_SESSION['administrador'][$_SESSION['hv'] - 1] != "1"){
			$profile_img = "/Assets/node_modules/gentelella/production/images/user.png";
			$update_lists = 0;
		}
		else{
			$profile_img = "/Assets/node_modules/gentelella/production/images/adm.png";
			$update_lists = 1;
		}

		//Seta o nome dos hypervisors e o diretorio atual
		$dir = getcwd();
		if($_SESSION['hv']=="1"){
			$hypervisor = "vmware";
			$hv_icon = "fa fa-desktop";
			$hv_badge_code = "badge bg-green";
			$console = "/Console/VMwareConsole.exe";
			$group_color = "#00cc44";
		}
		else if($_SESSION['hv']=="2"){
			$hypervisor = "hyperv";
			$hv_icon = "fa fa-windows";
			$hv_badge_code = "badge bg-blue";
			$console = "/Console/HyperVConsole.exe";
			$group_color = "#3399ff";
		}
		else if($_SESSION['hv']=="3"){
			$hypervisor = "vcloud";
			$hv_badge_code = "badge bg-grey";
			$hv_icon = "fa fa-cloud";
			$console = "/Console/VCloudConsole.exe";
			$group_color = "#8c8c8c";
		}

		//Abre o arquivo _status.txt dependendo do diretorio atual
		if($dir == "/var/www/html"){
			$status = fopen("$hypervisor/requests/_status.txt", "r");
		}
		else if($dir == "/var/www/html/$hypervisor"){
			$status = fopen("requests/_status.txt", "r");	
		}
		else if($dir == "/var/www/html/$hypervisor/requests"){
			$status = fopen("_status.txt", "r");	
		}
		else if($dir == "/var/www/html/$hypervisor/requests/Snapshots"){
			$status = fopen("../_status.txt", "r");	
		}

		//Seta a variavel de status com o conteudo da _status.txt
		if($status){
			$status_num = fgets($status);
			$status_num = $status_num[2];
			fclose($status);
		}

		//Abre o arquivo _status_list.txt dependendo do diretorio atual
		//1 para ONLINE
		//2 para OCUPADO
		//0 para OFFLINE
		if($dir == "/var/www/html"){
			$status_list = fopen("$hypervisor/requests/_list.txt", "r");
		}
		else if($dir == "/var/www/html/$hypervisor"){
			$status_list = fopen("requests/_list.txt", "r");	
		}
		else if($dir == "/var/www/html/$hypervisor/requests"){
			$status_list = fopen("_list.txt", "r");	
		}
		else if($dir == "/var/www/html/$hypervisor/requests/Snapshots"){
			$status_list = fopen("../_list.txt", "r");	
		}
		//Seta a variavel de status com o conteudo da _status_list.txt
		//0 para standby e 1 para atualizando as listas
		if($status_list){
			$status_list_num = fgets($status_list);
			$status_list_num = $status_list_num[2];
			fclose($status_list);
		}
		
		//Seta a variavel de grupo
		$group = $_SESSION['grupo'];

		//=====================================Opção de pedidos======================================

		$path = getcwd ();
		$path = explode("/", $path);
		$len = count($path);

		$path_prefix = "";

		for($count=4;$count<$len;$count++){
			$path_prefix .= "../";
		}

		$processed_requests = scan_dir($path_prefix . "$hypervisor/requests/Processed");
		$to_process_requests = scan_dir($path_prefix . "$hypervisor/requests");
		$to_approve_requests = scan_dir($path_prefix . "$hypervisor/requests/ToProcess");

		$pro_len = count($processed_requests);
		$topro_len = count($to_process_requests);
		$apv_len = count($to_approve_requests);

		if($_SESSION['administrador'][$_SESSION['hv'] - 1] == "1"){
			$req_num = 0;
			for($count=0;$count<$topro_len;$count++){
				if(is_request($to_process_requests[$count])){
					$to_process_navbar[$req_num] = $to_process_requests[$count];
					$req_num++;
				}
			}
			$to_pro_req_num = $req_num;
	
			$req_num = 0;
			for($count=0;$count<$apv_len;$count++){
				if(is_request($to_approve_requests[$count])){
					$to_approve_navbar[$req_num] = $to_approve_requests[$count];
					$req_num++;
				}
			}
			$to_apv_req_num = $req_num;
			$nav_req_count = $to_apv_req_num + $to_pro_req_num;

			$processed_num = 0;
			$aux = 0;
			while($processed_num < 5 && $aux<$pro_len){
				if(is_request($processed_requests[$aux])){
					$processed_navbar[$processed_num] = $processed_requests[$aux];
					$processed_num++;
				}
				$aux++;
			}

		}
		else{
			$req_num = 0;
			for($count=0;$count<$topro_len;$count++){
				if(is_request($to_process_requests[$count])){
					$elements = explode(" ", $to_process_requests[$count]);
					$aux_group = explode(".", $elements[5]);
					$aux_group = $aux_group[0];
					if($aux_group == $group){
						$to_process_navbar[$req_num] = $to_process_requests[$count];
						$req_num++;
					}
				}
			}
			$to_pro_req_num = $req_num;
	
			$req_num = 0;
			for($count=0;$count<$apv_len;$count++){
				if(is_request($to_approve_requests[$count])){
					$elements = explode(" ", $to_approve_requests[$count]);
					$aux_group = explode(".", $elements[5]);
					$aux_group = $aux_group[0];
					if($aux_group == $group){
						$to_approve_navbar[$req_num] = $to_approve_requests[$count];
						$req_num++;
					}
				}
			}
			$to_apv_req_num = $req_num;
			$nav_req_count = $to_apv_req_num + $to_pro_req_num;

			$processed_num = 0;
			$aux = 0;
			while($processed_num < 5 && $aux<$pro_len){
				if(is_request($processed_requests[$aux])){
					$elements = explode(" ", $processed_requests[$aux]);
					$aux_group = explode(".", $elements[5]);
					$aux_group = $aux_group[0];
					if($aux_group == $group){
						$processed_navbar[$processed_num] = $processed_requests[$aux];
						$processed_num++;
					}
				}
				$aux++;
			}
		}


		?>