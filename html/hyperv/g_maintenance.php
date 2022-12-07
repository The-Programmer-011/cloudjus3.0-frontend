<?php

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][0]=="0" || $_SESSION['administrador'][0]>"4"){
  header("Location: /index.php?op=err");
  $_SESSION['denied'] = 1;
}

//===================================================================

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
      while(!feof($file)){
        $line = fgets($file);
        $word_array = explode(" ", $line);
        if($search == $word_array[0]){
          $name_not_found = 0;
          $error = 0;
          //echo strlen($word_array[1]);
        }
      }
    }
    fclose($file);
    if($_SESSION['administrador'][1]!="1"){
      $filename = "requests/Groups/" . $_SESSION['grupo'] . ".txt";
      //echo $filename;
      $file = fopen($filename, "r");
      $unauthorized_machine=1;
      $line = fgets($file);
      $line = explode(";", $line);
      $len = count($line);
      $error++;
      for($count=0;$count<$len;$count++){
        $vm = $line[$count];
        if($_GET['nome_maquina'] == $vm){
          $unauthorized_machine=0;
          $error--;
          break;
        }
      }
      fclose($file);
    }
  }
  //echo $error;

  if(!$error){
    $vm = $_GET['nome_maquina'];
    if(!$_GET['maintenance']){
      if($_GET['data-inicio'] && $_GET['data-final']){
        $inicio = $_GET['data-inicio'];
        $final = $_GET['data-final'];
        if($_GET['descricao']){
          $descricao = $_GET['descricao'];
        }

        //echo $inicio . "<br>";
        //echo $final . "<br>";

        if($inicio <= $final){
          $motivo = $_GET['motivo'];
          header("Location: requests/request_validation.php?nome_maquina=$vm&data_inicio=$inicio&data_final=$final&descricao=$descricao&motivo=$motivo");
        }
        else{
          $error = 1;
        }
      }
    }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php// include_once("../Assets/gentelella_head.html"); ?>
    <title>Manutenção</title>
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
                <h3>Manutenção</h3>
              </div>

              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Maquina: <?php echo $_GET['nome_maquina'];?></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <?php $vm = $_GET['nome_maquina']; ?>
                    <?php if(!$_GET['data-inicio'] && !$_GET['data-final'] && !$_GET['maintenance']){ ?>
                      <h1>Gostaria de deixar a maquina para manutenção após o desligamento?</h1>
                      <a href="g_maintenance.php?nome_maquina=<?php echo $vm; ?>&maintenance=1"><input type='button' class='btn btn-success' value='yes'></a><a href="requests/request_validation.php?nome_maquina=<?php echo $vm; ?>"><input type='button' class='btn btn-danger' value='no'></a>

                      <?php
                      } 
                      if(isset($_GET['maintenance']) || $error){?>
                          <form method="get" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                          <?php if($error){echo "<strong>*Favor digitar datas válidas*</strong><br>";}?>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome da Máquina <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input list="vms" name="nome_maquina" type="text" id="first-name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $_GET['nome_maquina'];?>" readonly>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Motivo <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input list="vms" name="motivo" type="text" id="first-name" required="required" class="form-control col-md-7 col-xs-12">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Início <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input list="vms" name="data-inicio" type="datetime-local" id="first-name" required="required" class="form-control col-md-7 col-xs-12">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Fim <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                              <input list="vms" name="data-final" type="datetime-local" id="first-name" required="required" class="form-control col-md-7 col-xs-12">
                            </div>
                          </div>

                          <div class="ln_solid"></div>
                            <div class="form-group">
                              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <a href="/g_menu.php"><button class="btn btn-primary" type="button">Cancelar</button></a>
                              <button class="btn btn-primary" type="reset">Resetar</button>
                                <button type="submit" class="btn btn-success">Enviar pedido</button>
                              </div>
                            </div>
                        </form>
                        <?php 
                      }
                    }
                    ?>
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
