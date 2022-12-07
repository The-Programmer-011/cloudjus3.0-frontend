<?php
//=============================Funções=============================

function FormatString($string, $start){
  $size = strlen($string);
  $count=$start;
  $newString = "";  
  while($count<$size){
    $newString = $newString . $string[$count];
    $count+=2;
  }
  return $newString;
}

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][0]=="0" || $_SESSION['administrador'][0]>"4"){
  header("Location: /index.php?op=err");
  $_SESSION['denied'] = 1;
}

//=============================Setup de variáveis=============================

$url = $_SERVER['HTTP_REFERER'];
  $url2 = explode("/", $url);
  $url3 = explode("?", $url2[4]);
  $origin = $url3[0];
  $_GET['nome_maquina'] = strtolower($_GET['nome_maquina']);
  $time = getdate ($timestamp = time());
  $log = $time['hours'] . "-" . $time['minutes'] . "-" . $time['seconds'] . " " . $time['mday'] . "-" . $time['mon'] . "-" . $time['year'];
  $snapdate = "(" . $time['hours'] . "-" . $time['minutes'] . "-" . $time['seconds'] . "-" . $time['mday'] . "-" . $time['mon'] . "-" . $time['year'] . ")";

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php// include_once("../Assets/gentelella_head.html"); ?>
    <title>Manage Snapshots</title>
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
      <?php include_once("../../../Assets/gentelella_setup.php"); ?>
      <!-- Sidebar -->
      <?php include_once("../../../Assets/gentelella_sidebar.php"); ?>
      <!-- Sidebar -->

      <!-- top navigation -->
      <?php include_once("../../../Assets/gentelella_navbar.php"); ?>
      <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Gerenciar Snapshots</h3>
              </div>

              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Snaphots de <?php echo $_GET['nome_maquina']; ?></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <form method="get" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                      <?php

                      if($origin == "g_CreateSnap.php"){
                        $status = fopen("../_status.txt", "r");
                        if($status){
                          $status_num = fgets($status);
                          $status_num = $status_num[2];
                          fclose($status);
                        }
                        $filename = "../Requests/$1 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
                        $file = fopen($filename, "w");
                        if(!$file){
                          echo "ERROR<br>";
                        }
                        fwrite($file, "{\n");
                        fwrite($file, '"snapshot":1' . ",\n");
                        fwrite($file, '"operation":1' . ",\n");
                        fwrite($file, '"vm":"' . $_GET['nome_maquina'] . '"' . ",\n");
                        $_GET['snap'] = str_replace(' ', '', $_GET['snap']);
                        $snap_name = $_GET['snap'] . $snapdate;
                        $snap_name = str_replace(' ', '', $snap_name);
                        fwrite($file, '"snap":"' . $snap_name . '"' . ",\n");
                        fwrite($file, '"user":"' . $user . '"' . ",\n");
                        fwrite($file, '"log":"' . $log . '"' . ",\n");
                        fwrite($file, '"group":"' . $group . '"' . "\n");
                        fwrite($file, "}");
                        fclose($file);
                        if($status_num == 1){
                          echo "<h1 style='color:#00cc44'>Snapshot criado!</h1>";
                          echo "<p>Nome: $snap_name</p>";
                        }
                        else if($status_num == 2){
                          echo "<h1 style='color:#ff9900'>Servidores ocupados, seu snapshot sera feito em breve.</h1>";
                          echo "<p>Nome: $snap_name</p>";
                        }
                        else if($status_num == 0){
                          echo "<h1 style='color:#ff3300'>Servidores OFFLINE</h1>";
                          echo "<p>Seu pedido sera armazenado e sera executado assim que for possivel.</p>";
                          echo "<p>Nome: $snap_name</p>";
                        }
                      }
                      else if($origin == "g_ListSnap.php"){
                      $vm = $_GET['nome_maquina'];
                      $vm_file = $_GET['nome_maquina'] . ".txt";
                      $file = fopen($vm_file, "r");
                      if(!$file){
                        echo "<h1>Nao ha snapshots a mostrar.</h1>";
                      }
                      else{
                        $count=0;
                        fgets($file) . "<br>";
                        echo "<table class='table table-bordered'>";
                        while(!feof($file)){
                          fgets($file) . "<br>";
                          fgets($file) . "<br>";
                          $line = fgets($file);
                          $line = explode(":", $line);
                          $snap = str_replace(' ', '', $line[1]);
                          $snap = FormatString($snap, 2);
                          if($snap){
                            echo "<tr>";
                            echo "<td>";
                            echo "Snapshot: " . $snap . "<br>";
                            echo fgets($file) . "<br>";
                            echo fgets($file) . "<br>";
                            echo fgets($file) . "<br>";
                            echo fgets($file) . "<br>";
                            echo "</td>";
                  
                            echo "<td>";
                            echo "<a href='_g_confirmation.php?nome_maquina=$vm&snap=$snap&op=2' target='_blank'><input type='button' value='reverter' class='btn btn-warning'></a> <a href='_g_confirmation.php?nome_maquina=$vm&snap=$snap&op=3'   target='blank'><input type='button' value='deletar' class='btn btn-danger'></a>" .  "<br>";
                            echo "</td>";
                            echo "</tr>";
                          }
                          else{
                            fgets($file);
                            fgets($file);
                            fgets($file);
                            fgets($file);
                          }
                        }
                        echo "</table>";
                        }
                        fclose($file);
                      }
                      else{
                        $status = fopen("../_status.txt", "r");
                        if($status){
                          $status_num = fgets($status);
                          $status_num = $status_num[2];
                          fclose($status);
                        }
                        if(!$_SESSION['administrador']){
                          header("Location: /index.php?op=err");
                          $_SESSION['denied'] = 1;
                        }
                        $snapshot_filename = $_GET['nome_maquina'] . ".txt";
                        /*
                        $snapshot_file = fopen($snapshot_filename, "r");
                        fgets($snapshot_file);
                        fgets($snapshot_file);
                        fgets($snapshot_file);
                        for($count=1; $count<=$_GET['snap']; $count++){
                          $line = fgets($snapshot_file);
                          $line = FormatString($line, 1);
                          $line = explode(" ", $line);
                          $vm = $line[0];
                          $cont = 1;
                          while($line[$cont]==""){
                            $cont++;
                          }
                          $snapshot_name = $line[$cont];
                        }
                        fclose($snapshot_file);
                      */
                        if($_GET['op']==2){
                          if($status_num == 1){
                            echo "<h1>Snapshot revertida com sucesso!</h1>";
                          }
                          else if($status_num == 2){
                            echo "<h1 style='color:#ff9900'>Servidores ocupados, seu pedido sera executado em breve.</h1>";
                          }
                          else if($status_num == 0){
                            echo "<h1 style='color:#ff3300'>Servidores OFFLINE</h1>";
                            echo "<p>Seu pedido sera armazenado e sera executado assim que for possivel.</p>";
                          }
                          $filename = "../Requests/$2 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
                          $file = fopen($filename, "w");
                          fwrite($file, "{\n");
                          fwrite($file, '"snapshot":1' . ",\n");
                          fwrite($file, '"operation":2' . ",\n");
                          fwrite($file, '"vm":"' . $_GET['nome_maquina'] . '"' . ",\n");
                          $snap_name = str_replace(' ', '', $_GET['snap']);
                          fwrite($file, '"snap":"' . $snap_name . '"' . ",\n");
                          fwrite($file, '"user":"' . $user . '"' . ",\n");
                          fwrite($file, '"log":"' . $log . '"' . ",\n");
                          fwrite($file, '"group":"' . $group . '"' . "\n");
                          fwrite($file, "}");
                          fclose($file);
                        }
                        else if($_GET['op'] == 3){
                          if($status_num == 1){
                            echo "<h1>Snapshot deletada com sucesso!</h1>";
                          }
                          else if($status_num == 2){
                            echo "<h1 style='color:#ff9900'>Servidores ocupados, seu pedido sera executado em breve.</h1>";
                          }
                          else if($status_num == 0){
                            echo "<h1 style='color:#ff3300'>Servidores OFFLINE</h1>";
                            echo "<p>Seu pedido sera armazenado e sera executado assim que for possivel.</p>";
                          }
                          $filename = "../Requests/$3 " . $_SESSION['username'] . " " . $_GET['nome_maquina'] . " " . $log . " " . $_SESSION['grupo'] . ".json";
                          $file = fopen($filename, "w");
                          fwrite($file, "{\n");
                          fwrite($file, '"snapshot":1' . ",\n");
                          fwrite($file, '"operation":3' . ",\n");
                          fwrite($file, '"vm":"' . $_GET['nome_maquina'] . '"' . ",\n");
                          $snap_name = str_replace(' ', '', $_GET['snap']);
                          fwrite($file, '"snap":"' . $snap_name . '"' . ",\n");
                          fwrite($file, '"user":"' . $user . '"' . ",\n");
                          fwrite($file, '"log":"' . $log . '"' . ",\n");
                          fwrite($file, '"group":"' . $group . '"' . "\n");
                          fwrite($file, "}");
                          fclose($file);
                        }
                      }
                      ?>
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
