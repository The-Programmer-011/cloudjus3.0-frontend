<?php

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][1]=="0" || $_SESSION['administrador'][1]>"3"){
  header("Location: /index.php?op=err");
  $_SESSION['denied'] = 1;
}
if(isset($_SESSION['custom'])){
  unset($_SESSION['custom']);
}
if($_SESSION['administrador'][1] == "3"){
  $_SESSION['custom'] = 1;
}

//=============================Validação=============================

$error = 1;
  $error--;
  if($_GET["nome_maquina"]==""){
    $error++;
    $null_name = 1;
  }
  else{
    $error++;
    $name_not_found = 1;
    $_GET['nome_maquina'] = strtoupper($_GET['nome_maquina']);
    $search = $_GET["nome_maquina"];
    $file = fopen("requests/vms_names.txt", "r");
    if($file){
      $error = 0;
      while(!feof($file)){
        $line = fgets($file);
        $word_array = explode(" ", $line);
        if($search == $word_array[0]){
          $repeat_name = 1;
          $error = 1;
        }
      }
    }
    fclose($file);

    $file = fopen("requests/Lists/hosts.txt", "r");
    $cont = 1;
    $bigger_size = 0;
    while(!feof($file)){
      $host = fgets($file);
      if($host=="");
      else{
        //echo $host . "<br>";
        $host = explode(" ", $host);
        $host_num = $cont;
        $host_size = floatval($host[5]);
        if($host_size > $bigger_size){
          $bigger_size = $host_size;
          $bigger_host = $host_num;
        }
        $cont++;
      }
    }
    $host = $bigger_host;
    //echo "<br><br><br>";
    //echo $host . " " . $bigger_size . "<br>";
    fclose($file);

    $file_num = $host - 1;

    if(isset($_GET['datastore'])){
      $file = fopen("requests/Lists/" . $file_num . " disks.txt", "r");
      $cont = 1;
      $bigger_size = 0;
      while(!feof($file)){
        $datastore = fgets($file);
        if($datastore=="");
        else{
          //echo $datastore . "<br>";
          $datastore = explode(" ", $datastore);
          $datastore_num = $cont;
          $datastore_name = $datastore[2];
          $datastore_type = explode("_", $datastore_name);
          $datastore_patch = $datastore_type[3]; //patch para hyperv
          $datastore_type = $datastore_type[0];
          $datastore_size = $datastore[4];
          $datastore_size = floatval($datastore_size);
          if($datastore_type==$_GET['datastore']){
            if(($datastore_size > $bigger_size) && ($datastore_patch == "2048GB")){
              $bigger_size = $datastore_size;
              $bigger_datastore = $datastore_num;
            }
          }
          $cont++;
        }
      }
      //echo "<br><br><br>";
      $data = $bigger_datastore;
      //echo $data . " " . $bigger_size . "<br>";
      fclose($file);
    }

    if(isset($_GET['size'])){
      $size_filename = "../vm_template/" . $_GET['size'];
      $file = fopen($size_filename, "r");
      if($file){
        $line = fgets($file);
        $line = explode(":", $line);
        $cpu = str_replace(";", "", $line[1]);

        $line = fgets($file);
        $line = explode(":", $line);
        $ram = str_replace(";", "", $line[1]);
        $ram = intval($ram) * 1024;
      }
    }
    else{
      $error = 1;
    }

    //echo "<br><br><br>";
    //echo $cpu . " " . $ram . "<br>";

    if(!$error){
      $vm = $_GET['nome_maquina'];
      $template = $_GET['template'];
      header("Location: requests/request_validation.php?nome_maquina=$vm&servidor_host=$host&hardDisk=$data&template=$template&ram=$ram&core_number=$cpu");
      echo "requests/request_validation.php?nome_maquina=$vm&servidor_host=$host&datastore=$data&template=$template&pasta=$pasta&core_number=$cpu&ram=$ram";
    }
  }
  if($repeat_name){
    $notification .= "new PNotify({ title: 'Máquina já existe', text: 'Este nome já está sendo utilizado, favor escolher outro.', type: 'error', styling: 'bootstrap3'});";
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php// include_once("../Assets/gentelella_head.html"); ?>
    <title>Criar instancia (template)</title>
    <link rel="icon" href="/Assets/tab_icon.png">
    <!-- Bootstrap -->
    <link href="/Assets/node_modules/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/Assets/node_modules/gentelella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="/Assets/node_modules/gentelella/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- bootstrap-daterangepicker -->
    <link href="/Assets/node_modules/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <!-- bootstrap-datetimepicker -->
    <link href="/Assets/node_modules/gentelella/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
    <!-- Ion.RangeSlider -->
    <link href="/Assets/node_modules/gentelella/vendors/normalize-css/normalize.css" rel="stylesheet">
    <link href="/Assets/node_modules/gentelella/vendors/ion.rangeSlider/css/ion.rangeSlider.css" rel="stylesheet">
    <link href="/Assets/node_modules/gentelella/vendors/ion.rangeSlider/css/ion.rangeSlider.skinFlat.css" rel="stylesheet">
    <!-- Bootstrap Colorpicker -->
    <link href="/Assets/node_modules/gentelella/vendors/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <!-- PNotify -->
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">

    <link href="/Assets/node_modules/gentelella/vendors/cropper/dist/cropper.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="/Assets/node_modules/gentelella/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md" <?php if($notification){echo 'onload="' . $notification . '"';}?>>

      <!-- Setup -->
      <?php include_once("../Assets/gentelella_setup.php"); ?>
      <!-- Sidebar -->
      <?php include_once("../Assets/gentelella_sidebar.php"); ?>
      <!-- Sidebar -->

      <!-- top navigation -->
      <?php include_once("../Assets/gentelella_navbar.php"); ?>
      <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Criar nova instância (template)</h3>
              </div>

              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Detalhes da VM</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="g_CreateVM.php">Maquinas Customizadas</a>
                          </li>
                          <li><a href="#"><strong>Maquinas de Templates</strong></a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      <?php
                        if(isset($_SESSION['custom'])){
                          echo "<p><i>Obs: Seu pedido será submetido ao fluxo de aprovação antes de ser executado.</i></p>";  
                        }
                      ?>
                      <?php
                        if($repeat_name){
                          echo "<p>* VM já existe </p>";
                        }
                      ?>
                    <form method="get" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                      <br>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome da Máquina <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input name="nome_maquina" type="text" id="first-name" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                                                                 
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Template</label>
                        <div class="col-md-6 col-sm-6 col-xs-122">
                          <select name="template" class="form-control">                            
                            <?php
                            $file = fopen("requests/Lists/0 templates.txt", "r");
                            $cont = 1;
                            while(!feof($file)){
                              $template = fgets($file);
                              if($template=="");
                              else{
                                if($cont==$_GET['template']){
                                  echo "<option value='$cont' selected>$template</option>";
                                }
                                else{
                                  echo "<option value='$cont'>$template</option>";
                                }
                                $cont++;
                              }
                            }
                            fclose($file);
                            ?>
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Zona de Disponibilidade</label>
                        <div class="col-md-6 col-sm-6 col-xs-122">
                          <select name="datastore" class="form-control" value="<?php echo $_GET['datastore'];?>">
                            <?php
                            $file = fopen("requests/Lists/datastore_types.txt", "r");
                            $line = fgets($file);
                            fclose($file);
                            $types = explode(";", $line);
                            $len = count($types);
                            for($count=0; $count<$len; $count++){
                              $datastore_type = $types[$count];
                              if($datastore_type!=""){
                                $num = $count+1;
                                if($datastore_type == $_GET['datastore']){
                                  echo "<option value='$datastore_type' selected> STF Datacenter $num</option>";
                                }
                                else{
                                  echo "<option value='$datastore_type'>STF Datacenter $num</option>";
                                }
                              }
                            }
                            ?>
                          </select>
                        </div>
                      </div>

                      <br>
                      <table style="width: 50%" align="center" class="table table-bordered">
                      <tr>
                        <th>Instância</th>
                        <th>#Cores (vCPU)</th>
                        <th>#RAM (GB)</th>
                        <th style="width: 20%">Selecione:</th>
                      </tr>
                      
                      <?php
                      $vm_templates = scandir("../vm_template");
                      $len = count($vm_templates);
                      for($count=0; $count<$len; $count++){
                        //echo $vm_templates[$count] . "<br>";
                        if($vm_templates[$count]=="." || $vm_templates[$count]=="..");
                        else{
                        $filename = "../vm_template/" . $vm_templates[$count];
                        $file = fopen($filename, "r");
                        if($file){
                          echo "<tr>";
                
                          $size_filename = $vm_templates[$count];
                          $cod = explode(".", $vm_templates[$count]);
                          $cod = explode("_", $cod[0]);
                          $cod = $cod[1];
                          echo "<td>$cod</td>";
                
                          while(!feof($file)){
                            $line = fgets($file);
                            $line = explode(":", $line);
                            $cell = explode(";", $line[1]);
                            echo "<td>" . $cell[0] . "</td>";
                          }
                
                          echo '<td><input type="radio" name="size" value=' . $size_filename . '></td>';
                
                          echo "</tr>";
                          fclose($file);
                        }
                        }
                      }
                      ?>
                
                    </table>

                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <a href="/g_menu.php"><button class="btn btn-primary" type="button">Cancelar</button></a>
						            <button class="btn btn-primary" type="reset">Resetar</button>
                        <a href="g_CreateVM.php?custom=1"><button class="btn btn-info" type="button">Máquina Customizada</button></a>
                          <button type="submit" class="btn btn-success">Criar máquina</button>
                        </div>
                      </div>

                    </form>
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
    <!-- bootstrap-daterangepicker -->
    <script src="/Assets/node_modules/gentelella/vendors/moment/min/moment.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap-datetimepicker -->    
    <script src="/Assets/node_modules/gentelella/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <!-- Ion.RangeSlider -->
    <script src="/Assets/node_modules/gentelella/vendors/ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
    <!-- Bootstrap Colorpicker -->
    <script src="/Assets/node_modules/gentelella/vendors/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
    <!-- jquery.inputmask -->
    <script src="/Assets/node_modules/gentelella/vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    <!-- jQuery Knob -->
    <script src="/Assets/node_modules/gentelella/vendors/jquery-knob/dist/jquery.knob.min.js"></script>
    <!-- Cropper -->
    <script src="/Assets/node_modules/gentelella/vendors/cropper/dist/cropper.min.js"></script>
    <!-- PNotify -->
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.nonblock.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="/Assets/node_modules/gentelella/build/js/custom.php"></script>
  

    <?php// include_once("../Assets/gentelella_scripts.html"); ?>
	
  </body>
</html>
