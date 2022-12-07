<?php

//=============================HEADER=============================

session_start();
if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][$_SESSION['hv']-1] != "1"){
  header("Location: /index.php?op=err");
  $_SESSION['denied'] = 1;
}
include_once("Assets/gentelella_setup.php");

//=======================Mudança no sessão (super_adm)======================
if(isset($_GET['delete'])){
	//coloca o conteudo do GRUPOS.txt na variavel $groups_line
	$groups_file = fopen("GRUPOS/GRUPOS.txt", "r");
	$cont = 0;
	while(!feof($groups_file)){
	  $groups_line[$cont] = fgets($groups_file);
	  $cont++;
	}
	$cont--; // $cont = numero de linhas do GRUPOS.txt
	fclose($groups_file);

	//Altera o GRUPOS.txt
	$groups_file = fopen("GRUPOS/GRUPOS.txt", "w");
	for($cont2 = 0; $cont2 < $cont; $cont2++){
	  $content = explode(";", $groups_line[$cont2]);
	  if($content[1] != $_GET['group_name']){
	  	fputs($groups_file, $groups_line[$cont2]);
	  }
	}
	fclose($groups_file);

	//Remove arquivos de vms do grupo
	$hypervisors_path = "GRUPOS/hypervisors.txt";
	$hypervisors_file = fopen($hypervisors_path, "r");
	$file_content = fgets($hypervisors_file);
	fclose($hypervisors_file);
	$hypervisors = explode(";", $file_content);
	$len = count($hypervisors);
	for($cont = 0; $cont < $len; $cont++){
		$path = $hypervisors[$cont] . "/requests/Groups/" . $_GET['group_name'] . ".txt";
		unlink($path);
	}
}
else{
	if($_GET['group_name']){
	  
	  //coloca o conteudo do GRUPOS.txt na variavel $groups_line
	  $groups_file = fopen("GRUPOS/GRUPOS.txt", "r");
	  $cont = 0;
	  while(!feof($groups_file)){
	    $groups_line[$cont] = fgets($groups_file);
	    $cont++;
	  }
	  $cont--; // $cont = numero de linhas do GRUPOS.txt
	  fclose($groups_file);
	
	  //Altera o GRUPOS.txt
	  $groups_file = fopen("GRUPOS/GRUPOS.txt", "w");
	  $new_group = 1;
	  for($cont2 = 0; $cont2 < $cont; $cont2++){
	    $content = explode(";", $groups_line[$cont2]);
	    if($content[1] == $_GET['group_name']){
	      $groups_line[$cont2] = $_GET['group_dom'] . ";" . $_GET['group_name'] . ";" . $_GET['group_permission'] . ";" . $_GET['group_dashboard'] . ";" . $_GET['group_folder'] . ";\r\n";
	      $new_group = 0;
	    }
	    fputs($groups_file, $groups_line[$cont2]);
	  }
	  if($new_group){
	    $groups_line = $_GET['group_dom'] . ";" . $_GET['group_name'] . ";" . $_GET['group_permission'] . ";" . $_GET['group_dashboard'] . ";" . $_GET['group_folder'] . ";\r\n";
	    fputs($groups_file, $groups_line);
	  }
	  fclose($groups_file);
	
	  //Atera o arquivo de VMs do grupo.
	  $group_path = $hypervisor . "/requests/Groups/" . $_GET['group_name'] . ".txt";
	  $group_file = fopen($group_path, "w");
	  $num_vms = count($_GET['vms']);
	  for($cont = 0; $cont < $num_vms; $cont++){
	    $vm = $_GET['vms'][$cont] . ";";
	    fwrite($group_file, $vm);
	  }
	  fclose($group_file);
	}
	else{
	  if($_GET['username']){
	    $_SESSION['username'] = $_GET['username'];
	    $change = 1;
	  }
	  if($_GET['administrador']!=""){
	    $_SESSION['administrador'] = $_GET['administrador'];
	    $change = 1;
	  }
	  if($_GET['name']){
	    $_SESSION['name'] = $_GET['name'];
	    $change = 1;
	  }
	  if($_GET['lastname']){
	    $_SESSION['lastname'] = $_GET['lastname'];
	    $change = 1;
	  }
	  if($_GET['grupo']){
	    $_SESSION['grupo'] = $_GET['grupo'];
	    $change = 1;
	  }
	  if($_GET['theme']){
	    $_SESSION['theme'] = $_GET['theme'];
	    $change = 1;
	  }
	  if($_GET['dashboard']){
	    $_SESSION['dashboard'] = $_GET['dashboard'];
	    $change = 1;
	  }
	  if($_GET['pasta_vmware']){
	    $_SESSION['pasta'] = $_GET['pasta'];
	    $change = 1;
	  }
	  
	  //Caso tenha tido alguma mudanca, redireciona para a main
	  if($change){
	    header("Location: g_menu.php");
	  }
	}
}

//=============================SETUP=============================

$grupos_path = "GRUPOS/GRUPOS.txt";
$grupos_file = fopen($grupos_path, "r");
$cont = 0;
while(!feof($grupos_file)){
  $grupo[$cont] = fgets($grupos_file);
  $grupo[$cont] = explode(";", $grupo[$cont]);
  $cont++;
}
$grupo_quant = $cont + 1;

//vm list file
if($hypervisor == "hyperv"){
  $file = fopen("$hypervisor/requests/vms_names.txt", "r");
}
else{
  $file = fopen("$hypervisor/requests/vm_names.txt", "r");
}
$cont = 0;
while(!feof($file)){
	$file_line[$cont] = fgets($file);
	$cont++;
}
$vm_quant = $cont - 1;
fclose($file);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Aguarde...</title>
    <link href="/Assets/node_modules/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/Assets/node_modules/gentelella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="/Assets/node_modules/gentelella/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="/Assets/node_modules/gentelella/vendors/iCheck/skins/flat/green.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="/Assets/node_modules/gentelella/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">
      <!-- Setup -->
      <?php include_once("Assets/gentelella_setup.php"); ?>
      <!-- Sidebar -->
      <?php include_once("Assets/gentelella_sidebar.php"); ?>
      <!-- Sidebar -->

      <!-- top navigation -->
      <?php include_once("Assets/gentelella_navbar.php"); ?>
      <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Área Administrador</h3>
              </div>

              <div class="title_right">
                 </div>
               </div>
               <div class="clearfix"></div>
               <div class="row">
                 <div class="col-md-12 col-sm-12 col-xs-12">
                   <div class="x_panel">
                     <div class="x_title">
                       <h2>Grupos</h2>
                       <ul class="nav navbar-right panel_toolbox">
                         <li><a class="close-link"><i class="fa fa-close"></i></a>
                         </li>
                       </ul>
                       <div class="clearfix"></div>
                     </div>

                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                      <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Super_adm</a>
                        </li>
                          <?php for($cont = 1; $cont <= $grupo_quant; $cont++){ if($grupo[$cont][1] != ""){ ?>
                          <li role="presentation" class=""><a href="#tab_content<?php echo $cont+1; ?>" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false"><?php echo $grupo[$cont][1]; ?></a>
                          </li>
                        <?php }} ?>
                        <li role="presentation" class=""><a href="#tab_content<?php echo $cont+1; ?>" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">+</a>
                        </li>
                      </ul>
                      <div id="myTabContent" class="tab-content">

                        <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                          <h1 style="text-align: center"> ADMINISTRADOR </h1>
                          <form method="get" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Username: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="username" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="name" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Sobrenome: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="lastname" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Código de acesso: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="administrador" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Grupo: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="grupo" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Link do dashboard: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="dashboard" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Pasta do VMware/vCloud: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="pasta" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>

                            <div class="ln_solid"></div>
                            <div class="form-group">
                              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <a href="/g_menu.php"><button class="btn btn-primary" type="button">Cancelar</button></a>
                              <button class="btn btn-primary" type="reset">Resetar</button>
                                <button type="submit" class="btn btn-success">Modificar varáveis de sessão</button>
                              </div>
                            </div>

                          </form>
                        </div>
                        <?php for($cont = 1; $cont <= $grupo_quant; $cont++){ if($grupo[$cont][1] != ""){?>
                        <div role="tabpanel" class="tab-pane fade active in" id="tab_content<?php echo $cont+1; ?>" aria-labelledby="profile-tab">

                          <form method="get" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                            <h1 style="text-align: center"> <?php echo $grupo[$cont][1]; ?> </h1>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Grupo: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="group_name" type="text" class="form-control col-md-7 col-xs-12" readonly="readonly" value="<?php echo $grupo[$cont][1]; ?>">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome no domínio: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="group_dom" type="text" class="form-control col-md-7 col-xs-12" value="<?php echo $grupo[$cont][0]; ?>">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Permissão: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="group_permission" type="text" class="form-control col-md-7 col-xs-12" value="<?php echo $grupo[$cont][2]; ?>">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Pasta VMware/vCloud: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="group_folder" type="text" class="form-control col-md-7 col-xs-12" value="<?php echo $grupo[$cont][4]; ?>">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Link do dashboard: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="group_dashboard" type="text" class="form-control col-md-7 col-xs-12" value="<?php echo $grupo[$cont][3]; ?>">
                              </div>
                            </div>

                            <div class="ln_solid"></div>
                            <div class="form-group">
                              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <a href="/g_menu.php"><button class="btn btn-primary" type="button">Cancelar</button></a>
                                <button type="submit" class="btn btn-success">Modificar parâmetros</button>
                                <a href="/g_adm.php?group_name=<?php echo $grupo[$cont][1]; ?>&delete=1"><button class="btn btn-danger" type="button">Remover Grupo</button></a>
                              </div>
                            </div>

                            <br>
                            <h3> Máquinas do grupo no ambiente <?php echo $hypervisor; ?></h3>
                            <br>
                            <div class="table-responsive">
                              <table id="datatable-buttons" class="table table-striped jambo_table bulk_action">

                                <thead>
                                  <tr class="headings">
                                    <th>
                                      <input type="checkbox" id="check-all" class="flat">
                                    </th>
                                      <th class="column-title">Maquina</th>
                                      <th class="column-title">Estado</th>
                                    </th>
                                    <th class="bulk-actions" colspan="7">
                                      <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
                                    </th>
                                  </tr>
                                </thead>

                                <tbody>
                                  <?php
                                  $group_path = "$hypervisor/requests/Groups/" . $grupo[$cont][1] . ".txt";
                                  $group_file = fopen($group_path, "r");
                                  $group_vms = fgets($group_file);
                                  $group_vms = explode(";", $group_vms);
                                  $group_vms_quant = count($group_vms);
                                  fclose($group_file);
		
                                  for($vm_line = 0; $vm_line < $vm_quant; $vm_line++){
                                    $line = $file_line[$vm_line];
                                    if($line!=""){
                                      $line = explode(" ", $line);
                                      $vm = $line[0];
                                      $state = $line[1];
                                      $is_in_group = 0;
                                      for($cont2 = 0; $cont2 < $group_vms_quant; $cont2++){
                                        if($group_vms[$cont2] == $line[0]){
                                          $is_in_group = 1;
                                          $cont2 = $group_vms_quant;
                                        }
                                      }
                                      echo "<tr>";
                                      ?>
                                      <td class="a-center ">
                                        <input type="checkbox" class="flat" name="vms[]" value="<?php echo $vm; ?>" <?php if($is_in_group){ echo "checked"; }?>>
                                      </td>
                                      <?php
                                      echo "<td>" . $line[0] . "</td>";
                                      echo "<td>" . $line[1] . "</td>";
                                      $vm = $line[0];
                                      echo "</tr>";
                                    }
                                  }
                                  ?>
                                </tbody>
                              </table>
                            </div>

                          </form>

                        </div>
                        <?php }} ?>

                        <div role="tabpanel" class="tab-pane fade active in" id="tab_content<?php echo $cont+1; ?>" aria-labelledby="profile-tab">

                          <form method="get" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                            <h1 style="text-align: center"> NOVO GRUPO </h1>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Grupo: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="group_name" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome no domínio: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="group_dom" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Permissão: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="group_permission" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Pasta VMware/vCloud: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="group_folder" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>
                            
                            <div class="form-group">
                              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Link do dashboard: </label>
                              <div class="col-md-6 col-sm-6 col-xs-12">
                                <input name="group_dashboard" type="text" class="form-control col-md-7 col-xs-12">
                              </div>
                            </div>

                            <div class="ln_solid"></div>
                            <div class="form-group">
                              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <a href="/g_menu.php"><button class="btn btn-primary" type="button">Cancelar</button></a>
                                <button type="submit" class="btn btn-success">Criar Grupo</button>
                              </div>
                            </div>

                            <br>
                            <h3> Adicionar máquinas ao novo grupo </h3>
                            <br>
                            <div class="table-responsive">
                              <table id="datatable-buttons" class="table table-striped jambo_table bulk_action">

                                <thead>
                                  <tr class="headings">
                                    <th>
                                      <input type="checkbox" id="check-all" class="flat">
                                    </th>
                                      <th class="column-title">Maquina</th>
                                      <th class="column-title">Estado</th>
                                    </th>
                                    <th class="bulk-actions" colspan="7">
                                      <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
                                    </th>
                                  </tr>
                                </thead>

                                <tbody>
                                  <?php

                                  for($vm_line = 0; $vm_line < $vm_quant; $vm_line++){
                                    $line = $file_line[$vm_line];
                                    if($line!=""){
                                      $line = explode(" ", $line);
                                      $vm = $line[0];
                                      $state = $line[1];
                                      echo "<tr>";
                                      ?>
                                      <td class="a-center ">
                                        <input type="checkbox" class="flat" name="vms[]" value="<?php echo $vm; ?>">
                                      </td>
                                      <?php
                                      echo "<td>" . $line[0] . "</td>";
                                      echo "<td>" . $line[1] . "</td>";
                                      $vm = $line[0];
                                      echo "</tr>";
                                    }
                                  }
                                  ?>
                                </tbody>
                              </table>
                            </div>

                          </form>

                        </div>

                      </div>
                    </div>

                  </div>
                </div>
              </div>

        <!-- /page content -->

        <!-- Linha para fazer o javascript funcionar -->
        <a id="download" href="javascript:void(0);" download="cropped.png"></a>

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            CloudJus Version <?php echo $_SESSION['version']; ?>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="/Assets/node_modules/gentelella/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="/Assets/node_modules/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="/Assets/node_modules/gentelella/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="/Assets/node_modules/gentelella/vendors/nprogress/nprogress.js"></script>
    <!-- iCheck -->
    <script src="/Assets/node_modules/gentelella/vendors/iCheck/icheck.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="/Assets/node_modules/gentelella/build/js/custom.php"></script>
  

    <?php// include_once("../Assets/gentelella_scripts.html"); ?>

  </body>
</html>
