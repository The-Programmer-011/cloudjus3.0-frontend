<?php
//=================================================FUNCOES=================================================

function Shorten_disk_measures($disk_size){
	if(intval($disk_size/1000) > 0){
  		$disk_size /= 1000;
  		if(intval($disk_size/1000) > 0){
  		  $disk_size /= 1000;
  		  $disk_size = number_format((float)$disk_size, 2, '.', '');
  		  $disk_size = $disk_size . "PB";
  		}
  		else{
  		  $disk_size = number_format((float)$disk_size, 2, '.', '');
  		  $disk_size = $disk_size . "TB";
  		}
	}
	else{
	  $disk_size = number_format((float)$disk_size, 2, '.', '');
	  $disk_size = $disk_size . "GB";
	}
	return $disk_size;
}

function Shorten_mem_measures($mem_size){
	if(intval($mem_size/1000) > 0){
	  $mem_size /= 1000;
	  if(intval($mem_size/1000) > 0){
	    $mem_size /= 1000;
	    $mem_size = number_format((float)$mem_size, 2, '.', '');
	    $mem_size = $mem_size . "TB";
	  }
	  else{
	    $mem_size = number_format((float)$mem_size, 2, '.', '');
	    $mem_size = $mem_size . "GB";
	  }
	}
	else{
	  $mem_size = number_format((float)$mem_size, 2, '.', '');
	  $mem_size = $mem_size . "MB";
	}
	return $mem_size;
}

function Dashboard_limits($current_value, $limits_values){
	$data = explode(":", $limits_values);
	$warning = floatval($data[2] * 0.9);
	$limit = floatval($data[2]);
	if($current_value > $warning){
		if($current_value > $limit){
			return "red";
		}
		else{
			return "orange";
		}
	}
	else{
		return "green";
	}
}

function Count_VMs ($hypervisor, &$vm_count, &$vm_on, &$vm_off){
	if($hypervisor == "hyperv"){
	  $vm_file = fopen("hyperv/requests/vms_names.txt", "r");
	}
	else if($hypervisor == "vmware"){
	  $vm_file = fopen("vmware/requests/vm_names.txt", "r");
	}
	else if($hypervisor == "vcloud"){
		$vm_file = fopen("vcloud/requests/vm_names.txt", "r");
	}
	
	$vm_count = 0;
	$vm_on = 0;
	$vm_off = 0;
	if($_SESSION['administrador'][$_SESSION['hv']-1] == "1"){
	  while(!feof($vm_file)){
	    $line = fgets($vm_file);
	    if($line){
	      $vm_count++;
	      $state = explode(" ", $line);
	      $state = $state[1];
	      if($hypervisor == "hyperv"){
	        if(strlen($state) == 9){ //9 para running, 5 para off
	          $vm_on++;
	        }
	        else{
	          $vm_off++;
	        }
	      }
	      else if($hypervisor == "vmware" || $hypervisor == "vcloud"){
	       if(strlen($state) == 11){ //11 para PoweredOn, 12 para PoweredOff
	          $vm_on++;
	        }
	        else{
	          $vm_off++;
	        } 
	      }
	    }
	  }
	}
	else{
	  $group_file = fopen("$hypervisor/requests/Groups/" . $_SESSION['grupo'] . ".txt", "r");
	  $vm_list = fgets($group_file);
	  $vm_list = explode(";", $vm_list);
	  //$vm_count = count($vm_list) - 1;
	  $len = count($vm_list);
	
	  fclose($group_file);
	
	  $vm_on = 0;
	  $vm_off = 0;
	  for($count=0;$count<$len;$count++){
	    rewind($vm_file);
	    while(!feof($vm_file)){
	      $line = fgets($vm_file);
	      $state = explode(" ", $line);
	      $vm_name = $state[0];
	      $state = $state[1];
	      if($vm_name == $vm_list[$count] && $vm_name!=""){
	        $vm_count++;
	        if($hypervisor == "hyperv"){
	          if(strlen($state) == 9){ //9 para running, 5 para off
	            $vm_on++;
	          }
	          else{
	            $vm_off++;
	          }
	        }
	        else if($hypervisor == "vmware" || $hypervisor == "vcloud"){
	         if(strlen($state) == 11){ //11 para PoweredOn, 12 para PoweredOff
	            $vm_on++;
	          }
	          else{
	            $vm_off++;
	          } 
	        }
	        break;
	      }
	    }
	  }
	}
	fclose($vm_file);
}

function Get_ResoursePool ($hypervisor, &$core_num, &$mem_num, &$disc_aloc_num, &$disc_used_num){
	$core_num = 0;
	$mem_num = 0;
	$disc_aloc_num = 0;
	$disc_used_num = 0;
	$file = fopen("$hypervisor/requests/vms.txt", "r");
	fgets($file);
	fgets($file);
	fgets($file);
	fgets($file);
	if($hypervisor == "vmware" || $hypervisor == "vcloud"){
	  if($_SESSION['administrador'][$_SESSION['hv']-1]=="1"){
	    while(!feof($file)){
	      $line = fgets($file);
	      $line = str_replace(" ", "", $line);
	      $line = explode("|", $line);
	
	      $core_num += intval($line[3]);
	      $mem_num += intval($line[5]);
	      $disc_aloc_num += intval($line[7]);
	      $disc_used_num += intval($line[8]);
	
	      fgets($file);
	    }
	  }
	  else{
	    $filename = "$hypervisor/requests/Groups/" . $_SESSION['grupo'] . ".txt";
	    $file_vm = fopen($filename, "r");
	    $line = fgets($file_vm);
	    $vms = explode(";", $line);
	    $len = count($vms);
	    fclose($file_vm);
	    while(!feof($file)){
	      $line = fgets($file);
	      $line = str_replace(" ", "", $line);
	      $line = explode("|", $line);
	      for($count=0;$count<$len;$count++){
	        if($vms[$count]==$line[1] && $vms[$count]!=""){
	          
	          $core_num += intval($line[3]);
	          $mem_num += intval($line[5]);
	          $disc_aloc_num += intval($line[7]);
	          $disc_used_num += intval($line[8]);
	
	        }
	      }
	      fgets($file);
	    }
	  }
	}
	if($hypervisor == "hyperv"){
		if($_SESSION['administrador'][1]=="1"){
		  while(!feof($file)){
		    $line = fgets($file);
		    $line = str_replace(" ", "", $line);
		    $line = explode("|", $line);
		
		    $core_num += intval($line[3]);
		    $mem_num += intval($line[5]);
		    $disc_aloc_num += intval($line[11]);
		    $disc_used_num += intval($line[12]);
		
		    fgets($file);
		  }
		}
		else{
		  $filename = "hyperv/requests/Groups/" . $_SESSION['grupo'] . ".txt";
		  $file_vm = fopen($filename, "r");
		  $line = fgets($file_vm);
		  $vms = explode(";", $line);
		  $len = count($vms);
		  fclose($file_vm);
		  while(!feof($file)){
		    $line = fgets($file);
		    $line = str_replace(" ", "", $line);
		    $line = explode("|", $line);
		    for($count=0;$count<$len;$count++){
		      //echo $vms[$count] . " = " . $line[1] . "<br>";
		      if(strtoupper($vms[$count])==$line[1] && $vms[$count]!=""){
		        $core_num += intval($line[3]);
		        $mem_num += intval($line[5]);
		        $disc_aloc_num += intval($line[11]);
		        $disc_used_num += intval($line[12]);
		      }
		    }
		    fgets($file);
		  }
		}
	}
	fclose($file);
}

//=================================================HEADER=================================================

session_start();

if(!isset($_SESSION['username'])){ //caso o usuario nao esteja logado, redireciona para a pagina de login
	header("Location: index.php?op=err");
}
if(!isset($_SESSION['gentella'])){
	$_SESSION['gentelella'] = 1;
}
if(isset($_GET['hv'])){ //Caso exista um request de modificao de hyperisor, ele modifica a variavel de sessao 'hv' (hypervisor)
  $_SESSION['hv'] = $_GET['hv'];
}
if(isset($_SESSION['custom'])){
  unset($_SESSION['custom']);
}
if(isset($_SESSION['operation'])){
  unset($_SESSION['operation']);
}
if(!isset($_SESSION['gentelella'])){
	$_SESSION['gentelella'] = 1;
}
?>

<?php include_once("Assets/gentelella_setup.php"); ?>

<?php

//=================================================Vms on e off=================================================

$hyperv_vm_count = 0;
$hyperv_vm_on = 0;
$hyperv_vm_off = 0;

$vmware_vm_count = 0;
$vmware_vm_on = 0;
$vmware_vm_off = 0;

$vcloud_vm_count = 0;
$vcloud_vm_on = 0;
$vcloud_vm_off = 0;

Count_VMs("hyperv", $hyperv_vm_count, $hyperv_vm_on, $hyperv_vm_off);
Count_VMs("vmware", $vmware_vm_count, $vmware_vm_on, $vmware_vm_off);
Count_VMs("vcloud", $vcloud_vm_count, $vcloud_vm_on, $vcloud_vm_off);

//=================================================Mem e Disco=================================================

$core_num = 0;
$mem_num = 0;
$disc_aloc_num = 0;
$disc_used_num = 0;

Get_ResoursePool("vmware", $vmware_core_num, $vmware_mem_num, $vmware_disc_aloc_num, $vmware_disc_used_num);
Get_ResoursePool("hyperv", $hyperv_core_num, $hyperv_mem_num, $hyperv_disc_aloc_num, $hyperv_disc_used_num);
Get_ResoursePool("vcloud", $vcloud_core_num, $vcloud_mem_num, $vcloud_disc_aloc_num, $vcloud_disc_used_num);
//=================================================Setar Variaveis de dashboard=================================================

if($_SESSION['hv'] == "1"){
	$vm_count = $vmware_vm_count;
	$vm_on = $vmware_vm_on;
	$vm_off = $vmware_vm_off;
	$core_num = $vmware_core_num;
	$mem_num = $vmware_mem_num;
	$disc_aloc_num = $vmware_disc_aloc_num;
	$disc_used_num = $vmware_disc_used_num;
}
else if($_SESSION['hv'] == "2"){
	$vm_count = $hyperv_vm_count;
	$vm_on = $hyperv_vm_on;
	$vm_off = $hyperv_vm_off;
	$core_num = $hyperv_core_num;
	$mem_num = $hyperv_mem_num;
	$disc_aloc_num = $hyperv_disc_aloc_num;
	$disc_used_num = $hyperv_disc_used_num;
}
else if($_SESSION['hv'] == "3"){
	$vm_count = $vcloud_vm_count;
	$vm_on = $vcloud_vm_on;
	$vm_off = $vcloud_vm_off;
	$core_num = $vcloud_core_num;
	$mem_num = $vcloud_mem_num;
	$disc_aloc_num = $vcloud_disc_aloc_num;
	$disc_used_num = $vcloud_disc_used_num;
}

//=================================================Limites=================================================

$file = fopen("$hypervisor/requests/ResourcePool/" . $group . ".txt", "r");
if(!$file){
	echo "<br>ERRO<br>";
}
$data = fgets($file);
fclose($file);

$data = explode(";", $data);
$cpu = $data[0];
$mem = $data[1];
$disk = "disk:" . $disc_aloc_num*0.8 . ":" . $disc_aloc_num;

//==========CPU==========

$cpu_dashboard_color = Dashboard_limits($core_num, $cpu);
$cpu = explode(":", $cpu);
$cpu_limit = $cpu[2];
$cpu_warning = $cpu[1];
$cpu_percentage = ($core_num/$cpu_limit)*100;
$cpu_percentage = number_format((float)$cpu_percentage, 2, '.', '');

//==========Mem==========

$mem_dashboard_color = Dashboard_limits($mem_num, $mem);
$mem = explode(":", $mem);
$mem_limit = $mem[2];
$mem_warning = $mem[1];
$mem_percentage = ($mem_num/$mem_limit)*100;
$mem_percentage = number_format((float)$mem_percentage, 2, '.', '');

//==========Disk==========

$disk_dashboard_color = Dashboard_limits($disc_used_num, $disk);
$disk_percentage = ($disc_used_num/$disc_aloc_num)*100;
$disk_percentage = number_format((float)$disk_percentage, 2, '.', '');

//============Formatar Valores==========
$mem_num = Shorten_mem_measures($mem_num);
$mem_limit = Shorten_mem_measures($mem_limit);
$disc_aloc_num = Shorten_disk_measures($disc_aloc_num);
$disc_used_num = Shorten_disk_measures($disc_used_num);

//=================================================Grupos=================================================

$dir = scandir("$hypervisor/requests/Groups");
$group_num = count($dir);
$group_name;
$group_vm_number;

for($count=0;$count<$group_num;$count++){
	$file = fopen("$hypervisor/requests/Groups/" . $dir[$count], "r");
	$group_name[$count] = str_replace(".txt", "", $dir[$count]);
	$content = fgets($file);
	$content = explode(";", $content);
	$group_vm_number[$count] = count($content) - 1;
	fclose($file);
}

//=================================================Notificações=================================================

if(isset($_SESSION['pedido'])){
	//echo "ainda tem pedido<br>";
	if(isset($_GET['request']) && !isset($_SESSION['alert_off'])){
		$notification = "new PNotify({ title: 'Pedido Recebido', text: 'Seu pedido foi recebido e será executado em breve. Você pode ver detalhes do seu pedido na tab de notificações.', type: 'info', styling: 'bootstrap3'});";
		$_SESSION['hypervisor_request'] = $hypervisor;
		if(isset($_SESSION['pedido'])){
			$pedido = str_replace("Processed/", "", $_SESSION['pedido']);
			$dir = scandir("$hypervisor/requests/ToProcess");
			$len = count($dir);
			for($count=0;$count<$len;$count++){
				//echo $pedido . "=" . $dir[$count] . "?<br>"; 
				if($dir[$count] == $pedido){
					$notification = "new PNotify({ title: 'Pedido aguardando aprovação', text: 'Seu pedido será avaliado pelos administradores do sistema e você será notificado quando a avaliação do pedido for concluída.', styling: 'bootstrap3'});";
					$_SESSION['to_approve_request'] = $pedido;
				}
			}
		}
		$_SESSION['alert_off'] = 1;
	}
	if(isset($_SESSION['alert_off'])){
		if($hypervisor == $_SESSION['hypervisor_request']){
			$pedido = str_replace("Processed/", "", $_SESSION['pedido']);
			$dir = scandir("$hypervisor/requests/Processed");
			$len = count($dir);
			for($count=0;$count<$len;$count++){
				//echo $pedido . "=" . $dir[$count] . "?<br>"; 
				if($dir[$count] == $pedido){
					$notification = "new PNotify({ title: 'Pedido processado', text: 'Seu pedido foi processado e executado. Você pode ver o histórico de pedidos no tab de notificações.', type: 'success', styling: 'bootstrap3'});";
					if($_SESSION['to_approve_request'] == $pedido){
						$notification = "new PNotify({ title: 'Pedido aprovado', text: 'Seu pedido foi aprovado e executado pelos administradores. Você pode ver o histórico de pedidos no tab de notificações.', type: 'success', styling: 'bootstrap3'});";
						unset($_SESSION['to_approve_request']);
					}
					unset($_SESSION['pedido']);
					unset($_SESSION['hypervisor_request']);
					unset($_SESSION['alert_off']);
				}
			}
			if(isset($_SESSION['to_approve_request'])){
				$deleted = 1;
				$dir = scandir("$hypervisor/requests/ToProcess");
				$len = count($dir);
				for($count=0;$count<$len;$count++){
					//echo $pedido . "=" . $dir[$count] . "?<br>"; 
					if($dir[$count] == $_SESSION['to_approve_request']){
						$deleted = 0;
					}
				}
				if($deleted){
					$dir = scandir("$hypervisor/requests");
					$len = count($dir);
					for($count=0;$count<$len;$count++){
						//echo $pedido . "=" . $dir[$count] . "?<br>"; 
						if($dir[$count] == $_SESSION['to_approve_request']){
							$deleted = 0;
						}
					}
				}
				if($deleted){
					$notification = "new PNotify({ title: 'Pedido negado', text: 'Seu pedido foi analizado e negado pelos administradores.', type: 'error', styling: 'bootstrap3'});";
					unset($_SESSION['pedido']);
					unset($_SESSION['hypervisor_request']);
					unset($_SESSION['to_approve_request']);
					unset($_SESSION['alert_off']);
				}
			}
		}
	}
}
if(isset($_SESSION['pedido_manuntencao'])){
	$notification .= "new PNotify({ title: 'Pedido de manutenção enviado', text: 'Seu pedido de manutenção foi enviado para o Zabbix.', type: 'success', styling: 'bootstrap3'});";
	unset($_SESSION['pedido_manuntencao']);
}
if(isset($_SESSION['denied'])){
	$notification .= "new PNotify({ title: 'ACESSO NEGADO', text: 'Você não tem permissão para acessar esta página.', type: 'error', styling: 'bootstrap3'});";
	unset($_SESSION['denied']);
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
  	<?php include_once("Assets/gentelella_head.html"); ?>
    <title>CloudJus</title>
    <link rel="icon" href="/Assets/tab_icon.png">
    <!-- PNotify -->
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
   	<style>
   		.orange{
   			color:#ff8c1a;
   		}
   	</style>
  </head>

  <body class="nav-md" <?php if($notification){echo 'onload="' . $notification . '"';}?>>

  		<!-- Sidebar -->
  		<?php include_once("Assets/gentelella_sidebar.php"); ?>
  		<!-- Sidebar -->

        <!-- top navigation -->
        <?php include_once("Assets/gentelella_navbar.php"); ?>
        <!-- /top navigation -->

        <!-- page content -->

        <div class="right_col" role="main">
          <!-- top tiles -->
          <div class="row tile_count">
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-desktop"></i> Número de VMs</span>
              <div class="count"><?php echo $vm_count; ?></div>
              <!--<span class="count_bottom"><i class="green">4% </i> From last Week</span>-->
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-plug"></i> VMs Ligadas</span>
              <div class="count green"><?php echo $vm_on; ?></div>
              <!--<span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>3% </i> From last Week</span>-->
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-power-off"></i> VMs Desligadas</span>
              <div class="count red"><?php echo $vm_off; ?></div>
              <!--<span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span>-->
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-cog"></i> Número de vCPUs</span>
              <div class="count <?php echo $cpu_dashboard_color; ?>"><?php echo $core_num; ?></div>
              <span class="count_bottom">Limite: <?php echo "$cpu_limit"; ?><i class="<?php echo $cpu_dashboard_color; ?>"> <?php echo " ($cpu_percentage%)"; ?> </i></span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-database"></i> Memória Associada</span>
              <div class="count <?php echo $mem_dashboard_color; ?>"><?php echo $mem_num; ?></div>
              <span class="count_bottom"> Limite: <?php echo "$mem_limit"; ?> <i class="<?php echo $mem_dashboard_color; ?>"> <?php echo " ($mem_percentage%)"; ?></i></span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
              <span class="count_top"><i class="fa fa-th"></i> Disco Alocado</span>
              <div class="count <?php echo $disk_dashboard_color; ?>"><?php echo $disc_aloc_num; ?></div>
              <span class="count_bottom"> Disco Utilizado: <?php echo $disc_used_num; ?><i class="<?php echo $disk_dashboard_color; ?>"><?php echo " ($disk_percentage%)"; ?></i></span>
            </div>
          </div>
          <!-- /top tiles -->

          <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>vCPU</h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="#">Settings 1</a>
                      </li>
                      <li><a href="#">Settings 2</a>
                      </li>
                    </ul>
                  </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a>
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div id="cpu_gauge" style="height:370px;"></div>
              </div>
            </div>
          </div>

          <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Memória Associada</h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="#">Settings 1</a>
                      </li>
                      <li><a href="#">Settings 2</a>
                      </li>
                    </ul>
                  </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a>
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div id="mem_gauge" style="height:370px;"></div>
              </div>
            </div>
          </div>

          <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Disco Utilizado</h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="#">Settings 1</a>
                      </li>
                      <li><a href="#">Settings 2</a>
                      </li>
                    </ul>
                  </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a>
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div id="disk_gauge" style="height:370px;"></div>
              </div>
            </div>
          </div>

          <div class="col-md-4 col-sm-4 col-xs-12">
               <div class="x_panel">
                 <div class="x_title">
                   <h2>Estado das máquinas</h2>
                   <ul class="nav navbar-right panel_toolbox">
                     <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                     </li>
                     <li class="dropdown">
                       <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                       <ul class="dropdown-menu" role="menu">
                         <li><a href="#">Settings 1</a>
                         </li>
                         <li><a href="#">Settings 2</a>
                         </li>
                       </ul>
                     </li>
                     <li><a class="close-link"><i class="fa fa-close"></i></a>
                     </li>
                   </ul>
                   <div class="clearfix"></div>
                 </div>
                 <div class="x_content">

                  <div id="grafico_on_off" style="height:350px;"></div>

                </div>
              </div>
          </div>

          <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Sonar</h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                    <ul class="dropdown-menu" role="menu">
                      <li><a href="#">Settings 1</a>
                      </li>
                      <li><a href="#">Settings 2</a>
                      </li>
                    </ul>
                  </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a>
                  </li>
                </ul>
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div id="relacao_hv" style="height:370px;"></div>
              </div>
            </div>
          </div>

          <?php if($_SESSION['administrador'][$_SESSION['hv']-1] == "1"){?>
          	  <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>VMs por grupo</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Settings 1</a>
                          </li>
                          <li><a href="#">Settings 2</a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <div id="controle_grupos" style="height:350px;"></div>

                  </div>
                </div>
              </div>
          <?php } ?>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
            <br>
            Version <?php echo $_SESSION['version']; ?>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>	

    <script type="text/javascript">
	    var vm_count = <?php echo $vm_count; ?>;
	    var vm_on = <?php echo $vm_on; ?>;
	    var vm_off = <?php echo $vm_off; ?>;
	      
	    var cpu_percentage = <?php echo $cpu_percentage; ?>;
	    var mem_percentage = <?php echo $mem_percentage; ?>;
	    var disk_percentage = <?php echo $disk_percentage; ?>;
	     
	    var vmware_vm_count = <?php echo $vmware_vm_count; ?>;
		var vmware_vm_on = <?php echo $vmware_vm_on; ?>;
		var vmware_vm_off = <?php echo $vmware_vm_off; ?>;
		var vmware_core_num = <?php echo $vmware_core_num; ?>;
		var vmware_mem_num = <?php echo $vmware_mem_num/1000; ?>;
		var vmware_disc_aloc_num = <?php echo $vmware_disc_aloc_num/1000; ?>;
		//var vmware disc_used_num = <?php echo $vmware_disc_used_num; ?>;
	
		var hyperv_vm_count = <?php echo $hyperv_vm_count; ?>;
		var hyperv_vm_on = <?php echo $hyperv_vm_on; ?>;
		var hyperv_vm_off = <?php echo $hyperv_vm_off; ?>;
		var hyperv_core_num = <?php echo $hyperv_core_num; ?>;
		var hyperv_mem_num = <?php echo $hyperv_mem_num/1000; ?>;
		var hyperv_disc_aloc_num = <?php echo $hyperv_disc_aloc_num/1000; ?>;
		//var hyperv disc_used_num = <?php echo $hyperv_disc_used_num; ?>;

		var vcloud_vm_count = <?php echo $vcloud_vm_count; ?>;
		var vcloud_vm_on = <?php echo $vcloud_vm_on; ?>;
		var vcloud_vm_off = <?php echo $vcloud_vm_off; ?>;
		var vcloud_core_num = <?php echo $vcloud_core_num; ?>;
		var vcloud_mem_num = <?php echo $vcloud_mem_num/1000; ?>;
		var vcloud_disc_aloc_num = <?php echo $vcloud_disc_aloc_num/1000; ?>;
		//var hyperv disc_used_num = <?php echo $hyperv_disc_used_num; ?>;

		var limit_vm_count = <?php echo $vmware_vm_count + $hyperv_vm_count + $vcloud_vm_count; ?>;
		var limit_vm_on = <?php echo $vmware_vm_on + $hyperv_vm_on + $vcloud_vm_on; ?>;
		var limit_vm_off = <?php echo $vmware_vm_off + $hyperv_vm_off + $vcloud_vm_off; ?>;
		var limit_core_num = <?php echo $vmware_core_num + $hyperv_core_num + $vcloud_core_num; ?>;
		var limit_mem_num = <?php echo $vmware_mem_num/1000 + $hyperv_mem_num/1000 + $vcloud_mem_num/1000; ?>;
		var limit_disc_aloc_num = <?php echo $vmware_disc_aloc_num/1000 + $hyperv_disc_aloc_num/1000 + $vcloud_disc_aloc_num/1000; ?>;
		//var limit_disc_used_num = ;

		var groups = [
			<?php
				for($count=2;$count<$group_num;$count++){
					echo "'" . $group_name[$count] . "'";
					if($count+1<$group_num){
						echo ",";
					}
				}
			?>
		];
		var groups_info = [
			<?php 
			for($count=2;$count<$group_num;$count++){
				echo "{value: " . $group_vm_number[$count] . ", name: '" . $group_name[$count] . "'}";
				if($count+1<$group_num){
					echo ",";
				}
			}
			?>
	  	    ];

	</script>
	<?php include_once("Assets/gentelella_scripts.html"); ?>
    
    <!-- ECharts -->
    <script src="/Assets/node_modules/gentelella/vendors/echarts/dist/echarts.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/echarts/map/js/world.js"></script>
    <!-- PNotify -->
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.nonblock.js"></script>
    <!-- iCheck -->
    <script src="/Assets/node_modules/gentelella/vendors/iCheck/icheck.min.js"></script>
	
  </body>
</html>
