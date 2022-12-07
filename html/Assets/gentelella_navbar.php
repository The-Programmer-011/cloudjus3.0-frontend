<?php
function associate_operation_icon ($operation){
	if($operation == "1" || $operation == "2" || $operation == "3" || $operation == "b" || $operation == "c"){
		return "fa fa-cogs";
	}
	else if($operation == "4" || $operation == "5" || $operation == "6"){
		return "fa fa-database";
	}
	else if($operation == "7" || $operation == "8" || $operation == "9" || $operation == "a"){
		return "fa fa-power-off";
	}
	else if($operation == "$1" || $operation == "$2" || $operation == "$3"){
		return "fa fa-camera";
	}
}

function operation_name ($filename){
  if($_SESSION['hv']){
	 if($filename[0] == "1"){return "Criação de VM";}
	 //else if($filename[0] == "2"){return }	
	 else if($filename[0] == "3"){return "Remoção de VM";}
	 else if($filename[0] == "4"){return "Adicionar Disco";}
	 else if($filename[0] == "5"){return "Alterar Processadores";}
	 else if($filename[0] == "6"){return "Alterar Memória";}
	 else if($filename[0] == "7"){return "Shutdown VM";}
	 else if($filename[0] == "8"){return "Restart VM";}
	 else if($filename[0] == "9"){return "Power OFF VM";}
	 else if($filename[0] == "a"){return "Power ON VM";}
	 else if($filename[0] == "b"){return "Backup Alterado";}
	 else if($filename[0] == "c"){return "Período de Manutenção";}
	 else if($filename[0] == "$"){
	 	if($filename[1] == "1"){return "Criar Snapshot";}
	 	else if($filename[1] == "2"){return "Reverter Snapshot";}
	 	else if($filename[1] == "3"){return "Remover Snapshot";}
	 }
	 else{return 0;}
  }
  else if($_SESSION['hv']=="2"){
    if($filename[0] == "1"){return "Criação de VM";}
    //else if($filename[0] == "2"){return }  
    else if($filename[0] == "3"){return "Remoção de VM";}
    else if($filename[0] == "4"){return "Adicionar Disco";}
    else if($filename[0] == "5"){return "Alterar Processadores";}
    else if($filename[0] == "6"){return "Alterar Memória";}
    else if($filename[0] == "7"){return "Shutdown VM";}
    else if($filename[0] == "8"){return "Restart VM";}
    else if($filename[0] == "a"){return "Power OFF VM";}
    else if($filename[0] == "9"){return "Power ON VM";}
    else if($filename[0] == "b"){return "Backup Alterado";}
    else if($filename[0] == "c"){return "Período de Manutenção";}
    else if($filename[0] == "$"){
     if($filename[1] == "1"){return "Criar Snapshot";}
     else if($filename[1] == "2"){return "Reverter Snapshot";}
     else if($filename[1] == "3"){return "Remover Snapshot";}
    }
    else{return 0;}
  }
}

$request_link = "g_show_request.php";

?>

<div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <div class="nav toggle">
                <?php
                if($status_num == 1){
                  echo '<span class="badge bg-green pull-right">ONLINE</span>';
                }
                else if($status_num == 2){
                  echo '<span class="badge bg-orange pull-right">OCUPADO</span>';
                }
                else if($status_num == 0){
                  echo '<span class="badge bg-red pull-right">OFFLINE</span>';
                }
                ?>
              </div>

              <?php if(isset($_SESSION['BETA'])){?>

              <div class="nav toggle">
                <span class="badge bg-purple"> <?php echo $_SESSION['BETA']; ?></span>
              </div>

              <?php } ?>

              <?php if($status_list_num == "3"){?>

              <div class="nav toggle">
                <span class="badge bg-blue"> Fast Update</span>
              </div>

              <?php } ?>

             <?php if($status_list_num == "4"){?>

              <div class="nav toggle">
                <span class="badge bg-orange"> Atualizando Listas</span>
              </div>

              <?php } ?>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo $profile_img; ?>" alt=""><?php echo $name; ?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="javascript:;"><strong style="color:<?php echo $group_color;?>"> <?php echo $group;?></strong></a></li>
                    <li>
                      <a href="/g_check_status.php">
                        <?php
                        if($status_num == 1){
                          echo '<span class="badge bg-green pull-right">ONLINE</span>';
                        }
                        else if($status_num == 2){
                          echo '<span class="badge bg-orange pull-right">OCUPADO</span>';
                        }
                        else if($status_num == 0){
                          echo '<span class="badge bg-red pull-right">OFFLINE</span>';
                        }
                        ?>
                        <span>Status</span>
                      </a>
                    </li>
                    <?php if($_SESSION['administrador'][$_SESSION['hv'] - 1] == "1"){?>
                      
                      <li><a href="/g_adm.php"> Área Adm </a></li>
                      <li><a href="/<?php echo $hypervisor; ?>/g_ToProcess.php"> Aprovar pedidos</a></li>
                      <li><a href="/Console/MaintenanceConsoleDev.exe"> Console de Manutenção - Dev</a></li>
                      <li><a href="/Console/MaintenanceConsoleProd.exe"> Console de Manutenção - Prod</a></li>

                    <?php } ?>
                    <li><a href="/logout.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                <li role="presentation" class="dropdown">
                  <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-newspaper-o" data-placement="top" title="Requisições"></i>
                    <?php if($nav_req_count){ echo"<span class='badge bg-red'>$nav_req_count</span>";}?>
                  </a>
                  <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">

                  <?php
                  	$count = 0;
                  	while($count<$to_pro_req_num){
                  		$elements = explode(" ", $to_process_navbar[$count]);
                  		$operation = operation_name($to_process_navbar[$count]);
                  		$operation_code = $elements[0];
                  		$user = $elements[1];
                  		$vm = $elements[2];
                  		$time = str_replace("-", ":", $elements[3]);
                  		$date = str_replace("-", "/", $elements[4]);
                  		$request_group = str_replace(".json", "", $elements[5]);
                  		$element_icon = associate_operation_icon($operation_code);
                  	?>
                    <li>
                      <a href='<?php echo "/$hypervisor/$request_link?file=" . $to_process_navbar[$count]; ?>' target="_blank">
                        <span><i class="<?php echo $element_icon; ?>"></i></span>
                        <span>
                          <strong><?php echo $user; ?></strong>
                          <span class="time"><?php echo $time . " " . $date; ?></span>
                        </span>
                        <span class="message">
                        	<?php echo "$operation - $vm Grupo: $request_group"; ?> 
                        </span>
                        <span class="badge bg-red">TRAVADO</span>
                      </a>
                    </li>
                    <?php $count++;} ?>

                    <?php
                  	$count = 0;
                  	while($count<$to_apv_req_num){
                  		$elements = explode(" ", $to_approve_navbar[$count]);
                  		$operation = operation_name($to_approve_navbar[$count]);
                  		$operation_code = $elements[0];
                  		$user = $elements[1];
                  		$vm = $elements[2];
                  		$time = str_replace("-", ":", $elements[3]);
                  		$date = str_replace("-", "/", $elements[4]);
                  		$request_group = str_replace(".json", "", $elements[5]);
                  		$element_icon = associate_operation_icon($operation_code);
                  	?>
                    <li>
                      <a href='<?php echo "/$hypervisor/$request_link?file=ToProcess/" . $to_approve_navbar[$count]; ?>' target="_blank">
                        <span><i class="<?php echo $element_icon; ?>"></i></span>
                        <span>
                          <strong><?php echo $user; ?></strong>
                          <span class="time"><?php echo $time . " " . $date; ?></span>
                        </span>
                        <span class="message">
                        	<?php echo "$operation - $vm Grupo: $request_group"; ?> 
                        </span>
                        <span class="badge">Aguardando permissão</span>
                      </a>
                    </li>
                    <?php $count++;} ?>

                    <?php
                  	$count = 0;
                  	while($count<5){
                  		$elements = explode(" ", $processed_navbar[$count]);
                  		$operation = operation_name($processed_navbar[$count]);
                  		$operation_code = $elements[0];
                  		$user = $elements[1];
                  		$vm = $elements[2];
                  		$time = str_replace("-", ":", $elements[3]);
                  		$date = str_replace("-", "/", $elements[4]);
                  		$request_group = str_replace(".json", "", $elements[5]);
                  		$element_icon = associate_operation_icon($operation_code);
                  	?>
                    <li>
                      <a href='<?php echo "/$hypervisor/$request_link?file=/Processed/" . $processed_navbar[$count]; ?>' target="_blank">
                        <span><i class="<?php echo $element_icon; ?>"></i></span>
                        <span>
                          <strong><?php echo $user; ?></strong>
                          <span class="time"><?php echo $time . " " . $date; ?></span>
                        </span>
                        <span class="message">
                        	<?php echo "$operation - $vm Grupo: $request_group"; ?>
                        </span>
                        <span class="badge bg-green">Processado!</span>
                      </a>
                    </li>
                    <?php $count++;} ?>

                    <li>
                      <div class="text-center">
                        <a href="/<?php echo $hypervisor; ?>/g_AllRequests.php">
                          <strong>Ver todas requisições</strong>
                          <i class="fa fa-angle-right"></i>
                        </a>
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
            </nav>
          </div>
        </div>