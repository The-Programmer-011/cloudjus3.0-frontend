<?php

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}

if(isset($_GET['custom'])){
  $_SESSION['custom'] = 1;
}

if($_SESSION['administrador'][1]=="0" || $_SESSION['administrador'][1]>"3"){
  header("Location: /index.php?op=err");
  $_SESSION['denied'] = 1;
}

if($_SESSION['administrador'][1]=="2" || $_SESSION['administrador'][1]=="3"){
  if(!isset($_SESSION['custom'])){
    header("Location: g_TemplateVMCluster.php");
  }
}

//=============================Validação=============================

  if($_GET['nome_maquina'] != ""){
    $_GET['nome_maquina'] = strtoupper($_GET['nome_maquina']);
    $vm = $_GET['nome_maquina'];
    $host = $_GET['servidor_host'];
    $disk = $_GET['hardDisk'];
    $template = $_GET['template'];
    $core = $_GET['core_number'];
    $ram = $_GET['ram'];
    $quantidade = $_GET['quantidade'];
    header("Location: /hyperv/requests/request_validation.php?nome_maquina=$vm&servidor_host=$host&hardDisk=$disk&template=$template&core_number=$core&ram=$ram&quantidade=$quantidade", true,  301 );
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php// include_once("../Assets/gentelella_head.html"); ?>
    <title>Criar VM</title>
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
                <h3>Criar VM</h3>
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
                          <li><a href="#"><strong>Maquinas Customizadas</strong></a>
                          </li>
                          <li><a href="g_TemplateVM.php">Maquinas de Templates</a>
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
                      <br>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome da Máquina <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input name="nome_maquina" type="text" id="first-name" required="required" class="form-control col-md-7 col-xs-12" pattern="[a-zA-Z]*">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Servidor Host</label>
                        <div class="col-md-6 col-sm-6 col-xs-122">
                          <select name="servidor_host" class="form-control" value="<?php echo $_GET['servidor_host'];?>">                            
                            <?php
                            $file = fopen("requests/Lists/hosts.txt", "r");
                            $cont = 1;
                            while(!feof($file)){
                              $host = fgets($file);
                              if($host=="");
                              else{
                                if($cont==$_GET['servidor_host']){
                                  echo "<option value='$cont' selected>$host</option>";
                                }
                                else{
                                  echo "<option value='$cont'>$host</option>";
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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Repositório</label>
                        <div class="col-md-6 col-sm-6 col-xs-122">
                          <select name="hardDisk" class="form-control" value="<?php echo $_GET['hardDisk'];?>">                            
                            <?php
                            $file_name = "requests/Lists/0 disks.txt";
                            $file = fopen($file_name, "r");
                            $cont = 1;
                            while(!feof($file)){
                              $disk = fgets($file);
                              if($disk=="");
                              else{
                                if($cont==$_GET['hardDisk']){
                                  echo "<option value='$cont' selected>$disk</option>";
                                }
                                else{
                                  echo "<option value='$cont'>$disk</option>";
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
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Template</label>
                        <div class="col-md-6 col-sm-6 col-xs-122">
                          <select name="template" class="form-control">                            
                            <?php
                            $file_name = "requests/Lists/0 templates.txt";
                            $file = fopen($file_name, "r");
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

                      <div class="col-md-2">
                      </div>

                      <?php if(isset($_SESSION['custom'])){ ?>
                        <div class="col-md-2">
                          <p>vCPUs</p>
                          <input name="core_number" class="knob" data-width="100" data-height="120" data-angleOffset=-125 data-angleArc=250 data-fgColor="#26B99A"   data-rotation="clockwise" data-min="1" data-max="4" value="1">
                        </div>
                      <?php }else{ ?>
                        <div class="col-md-2">
                          <p>vCPUs</p>
                          <input name="core_number" class="knob" data-width="100" data-height="120" data-angleOffset=-125 data-angleArc=250 data-fgColor="#26B99A"   data-rotation="clockwise" data-min="1" data-max="8" value="1">
                        </div>
                      <?php } ?>

                      <div class="col-md-2">
                        <p>Memória</p>
                        <input name="ram" class="knob" data-width="100" data-height="120" data-angleOffset=-125 data-angleArc=250 data-fgColor="#26B99A"   data-rotation="clockwise" data-min="512" data-max="16384" data-step="512" value="1024">
                      </div>

                      <div class="col-md-2">
                        <p>Quantidade de Máquinas</p>
                        <input name="quantidade" class="knob" data-width="100" data-height="120" data-angleOffset=-125 data-angleArc=250 data-fgColor="#26B99A"   data-rotation="clockwise" data-min="1" data-max="10" value="1">
                      </div>

                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <a href="/g_menu.php"><button class="btn btn-primary" type="button">Cancelar</button></a>
						            <button class="btn btn-primary" type="reset">Resetar</button>
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
