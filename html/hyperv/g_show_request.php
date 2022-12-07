<?php

function FilterJSON($filename){
  $file = fopen($filename, "r");
  if(!$file){
    //echo $filename . "<br>";
    $file = fopen("requests/Processed/$filename", "r");
  }
  if(!$file){
    echo "<h1>ERROR</h1>";
  }
  $json = fread($file, filesize($filename));
  $json = str_replace("\n", "", $json);
  $json = str_replace(" ", "", $json);
  $json = str_replace("\0", "", $json);
  $json = str_replace("\t", "", $json);
  $json = str_replace("\x0B", "", $json);
  $json = explode("{", $json);
  $json = $json[1];
  $json = "{" . $json;
  fclose($file);
  return $json;
}

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php// include_once("../Assets/gentelella_head.html"); ?>
    <title>Show request</title>
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
                <h3>Visualizar pedido</h3>
              </div>

              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Detalhes do pedido</h2>
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
                    
                      if($_GET['file']){
                        $filename = $_GET['file'];
                        $filename = "requests/" . $filename;
                        $json = FilterJSON($filename);
                        $request = json_decode($json);
                        $cod = $request->{'operation'};
                        if($request->{'snapshot'} == 1){
                          if($cod == 1){
                            echo "Pedido de criacao de snapshot<br>";
                          }
                          else if($cod == 2){
                            echo "Pedido de revercao de snapshot<br>";
                          }
                          else if($cod[1] == 3){
                            echo "Pedido de remocao de snapshot<br>";
                          }
                        }
                        else{
                          if($cod==1 || $cod==2){
                            echo "Pedido de criacao de maquina virtual:<br>";
                          }
                          else if($cod==3){
                            echo "Pedido de eliminacao de maquina virtual<br>";
                          }
                          else if($cod == 4){
                            echo "Pedido de alteracao de tamanho de disco<br>";
                          }
                          else if($cod == 5){
                            echo "Pedido de alteracao do numero de processadores<br>";
                          }
                          else if($cod == 6){
                            echo "Pedido de alteracao de quantidade de memoria<br>";
                          }
                          else if($cod == 7){
                            echo "Pedido de shutdown<br>";
                          }
                          else if($cod == 8){
                            echo "Pedido de restart<br>";
                          }
                          else if($cod == 10){
                            echo "Pedido de desligamento (Power OFF)<br>";
                          }
                          else if($cod == 9){
                            echo "Pedido para ligar uma VM<br>";
                          }
                          else if($cod==11){
                            echo "Pedido de modificação de política de backup<br>";
                          }
                          else if($cod==12){
                            echo "Período de manutenção<br>";
                          }
                        }
                        
                        echo "Nome da maquina: " . $request->{'vm'} . "<br>";
                    
                        if($request->{'host'}){
                          $unit = $request->{'host'};
                          $host = fopen("requests/Lists/hosts.txt", "r");
                          for($count=0; $count<$unit; $count++){
                            $host_name = fgets($host);
                          }
                          $host_name = explode(" ", $host_name);
                          echo "Host: $host_name[2]<br>";
                          fclose($host);
                    
                          $unit = fgets($file);
                          if(strpos($_GET['file'], "Processed")!=FALSE){
                            $unit = intval($unit[1]);
                          }

                          $unit = $request->{'disk'};
                          $data = fopen("requests/Lists/0 disks.txt", "r");
                          for($count=0; $count<$unit; $count++){
                            $datastore_name = fgets($data);
                          }
                          $datastore = explode(" ", $datastore_name);
                          echo "Datastore: $datastore[2]<br>";
                          fclose($data);
                          
                          $unit = fgets($file);
                          if(strpos($_GET['file'], "Processed")!=FALSE){
                            $unit = intval($unit[1]);
                          }

                          $unit = $request->{'template'};
                          $template = fopen("requests/Lists/0 templates.txt", "r");
                          for($count=0; $count<$unit; $count++){
                            $template_name = fgets($template);
                          }
                          $template_name = explode(" ", $template_name);
                          echo "Template: $template_name[2]<br>";
                          fclose($template);
                    
                          $unit = $request->{'cpu'};
                          echo "Numero de processadores: " . $unit . "<br>";
                    
                          $unit = $request->{'ram'};
                          echo "Quantidade de memoria RAM (MB): " . $unit . "<br>";
                        }

                        if($cod==4){
                          $unit = $request->{"disk"};
                          echo "Disco aumentado em $unit Gb<br>";
                        }
                        else if($cod==5){
                          $unit = $request->{"cpu"};
                          echo "Novo numero de processadores: $unit<br>"; 
                        }
                        else if($cod==6){
                          $unit = $request->{"ram"};
                          echo "Nova quantidade de memoria: $unit Mb<br>";
                        }
                        else if($request->{'snapshot'} == 1){
                          $unit = $request->{"snap"};
                          echo "Nome do snapshot: $unit<br>";
                        }
                        else if($cod==11){
                          $unit = $request->{'backup'};
                          $dir = scandir("../backup");
                          $len = count($dir);
                          for($aux=0;$aux<$len;$aux++){
                            //echo $dir[$aux][0] . "=" . $unit[0] . " " . strlen($unit) . "<br>";
                            if($dir[$aux][0]==$unit){
                              $policy = $dir[$aux];
                              $policy = explode("_", $policy);
                              $policy = explode(".", $policy[1]);
                              $policy = $policy[0];
                              echo "Nova polítca de backup: $policy<br>";
                            }
                          }
                        }
                        else if($cod==12){
                          $unit = $request->{'start'};
                          echo "Começo da manutenção: " . $unit . "<br>";
                          $unit = $request->{'end'};
                          echo "Final da manutenção: " . $unit . "<br>";
                          $unit = $request->{'id'};
                          echo "ID de manutenção: " . $unit;
                        }
                        fclose($file);
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
