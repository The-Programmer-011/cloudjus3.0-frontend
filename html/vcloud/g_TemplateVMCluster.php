<?php

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}

if(isset($_SESSION['custom'])){
  unset($_SESSION['custom']);
}

if($_SESSION['administrador'][2] == "3"){
  $_SESSION['custom'] = 1;
}

if(($_SESSION['administrador'][2]=="0" || $_SESSION['administrador'][2]>"2") && !isset($_SESSION['custom'])){
  header("Location: /index.php?op=err");
  $_SESSION['denied'] = 1;
}

//=============================Validação=============================

  if($_GET['nome_maquina']!=""){
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
        $host_size = floatval($host[4]);
        if($host_size > $bigger_size){
          $bigger_size = $host_size;
          $bigger_host = $host_num;
        }
        $cont++;
      }
    }
    $host = $bigger_host;
    fclose($file);

    if(isset($_GET['datastore'])){
      $file = fopen("requests/Lists/datastores.txt", "r");
      $cont = 1;
      $bigger_size = 0;
      while(!feof($file)){
        $datastore = fgets($file);
        if($datastore=="");
        else{
          //echo $datastore . "<br>";
          $datastore = explode(" ", $datastore);
          $datastore_num = $cont;
          $datastore_name = $datastore[1];
          $datastore_type = explode("_", $datastore_name);
          $datastore_type = $datastore_type[0];
          $datastore_size = str_replace(".", "", $datastore[4]);
          $datastore_size = floatval($datastore_size);
          if($datastore_type==$_GET['datastore']){
            if($datastore_size > $bigger_size){
              $bigger_size = $datastore_size;
              $bigger_datastore = $datastore_num;
            }
          }
          $cont++;
        }
      }
      $data = $bigger_datastore;
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

    $file = fopen("requests/Lists/pastas.txt", "r");
    $cont = 1;
    echo $_SESSION['pasta'] . "<br>";
    while(!feof($file)){
      $pasta = fgets($file);
      //echo $pasta . "<br>";
      if($pasta=="");
      else{
        if(strpos($pasta, $_SESSION['pasta'])){
          $pasta_num = $cont;
        }
        $cont++;
      }
    }
    $pasta = $pasta_num;
    fclose($file);

    if(!$error){
      $vm = strtolower($_GET['nome_maquina']);
      $template = $_GET['template'];
      $quantidade = $_GET['quantidade'];
      header("Location: requests/request_validation.php?nome_maquina=$vm&quantidade=$quantidade&permission=1&servidor_host=$host&datastore=$data&template=$template&pasta=$pasta&ram=$ram&core_number=$cpu");
      echo "requests/request_validation.php?nome_maquina=$vm&servidor_host=$host&datastore=$data&template=$template&pasta=$pasta&core_number=$cpu&ram=$ram";
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php// include_once("../Assets/gentelella_head.html"); ?>
    <title>Criar instancias em lote (Template)</title>
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

    <link href="/Assets/node_modules/gentelella/vendors/cropper/dist/cropper.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="/Assets/node_modules/gentelella/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">

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
                <h3>Criar Instâncias em Lote (Template)</h3>
              </div>

              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Templates</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="g_CreateVMCluster.php">Maquinas Customizadas</a>
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
                    <form method="get" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                      <?php
                        if(isset($_SESSION['custom'])){
                          echo "<p><i>Obs: Seu pedido será submetido ao fluxo de aprovação antes de ser executado.</i></p>";  
                        }
                      ?>
                      <br>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome da Máquina <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input name="nome_maquina" type="text" id="first-name" required="required" class="form-control col-md-7 col-xs-12" pattern="[a-zA-Z]*">
                        </div>
                      </div>
                                                                 
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Template</label>
                        <div class="col-md-6 col-sm-6 col-xs-122">
                          <select name="template" class="form-control">                            
                            <?php
                            $file = fopen("requests/Lists/templates.txt", "r");
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

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Número de máquinas <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" class="form-control col-md-7 col-xs-12" name="quantidade" min="1" max="5" value="1" required>
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
                        <a href="g_CreateVMCluster.php?custom=1"><button class="btn btn-info" type="button">Máquina Customizada</button></a>
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

    <!-- Custom Theme Scripts -->
    <script src="/Assets/node_modules/gentelella/build/js/custom.min.js"></script>
  

    <?php// include_once("../Assets/gentelella_scripts.html"); ?>
	
  </body>
</html>
