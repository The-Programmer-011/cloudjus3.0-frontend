<?php

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php//include_once("../Assets/gentelella_head.html"); ?>
    <title>Configuracao</title>
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

    <!-- iCheck -->
    <link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

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
                <h3>Configuração das Instâncias</h3>
              </div>

              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Informações detalhadas</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>

                  <div class="x_content">
                    <table id="datatable-buttons" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Nome da Instância</th>
                          <th>Host</th>
                          <th>Qtd. vCPUs</th>
                          <th>% CPU</th>
                          <th>Mem. RAM(MB)</th>
                          <th>Mem. Usada(%)</th>
                          <th>Disco Aloc.(GB)</th>
                          <th>Disco Usado(GB)</th>
                          <th>HW Version</th>
                          <th>Uptime</th>
                          <th>On/Off</th>
                        </tr>
                      </thead>


                      <tbody>
                        <?php
                        $file = fopen("requests/vms.txt", "r");
                        fgets($file);
                        fgets($file);
                        fgets($file);
                        fgets($file);
                        if($_SESSION['administrador'][0]=="1"){
                          while(!feof($file)){
                            echo "<tr>";
                            $line = fgets($file);
                            $line = str_replace(" ", "", $line);
                            $line = explode("|", $line);
                            for($count=1;$count<=11;$count++){
                              echo "<td>" . $line[$count] . "</td>";
                            }
                            echo "</tr>";
                            fgets($file);
                          }
                        }
                        else{
                          $filename = "requests/Groups/" . $_SESSION['grupo'] . ".txt";
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
                              if($vms[$count]==$line[1] && $vms[$count]!=""){
                                echo "<tr>";
                                for($cont=1;$cont<=11;$cont++){
                                  echo "<td>" . $line[$cont] . "</td>";
                                }
                                echo "</tr>";
                              }
                            }
                            fgets($file);
                          }
                        }
                        fclose($file);
                      ?>
                      </tbody>
                    </table>
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
    <!-- Datatables -->
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/jszip/dist/jszip.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/pdfmake/build/vfs_fonts.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="/Assets/node_modules/gentelella/build/js/custom.min.js"></script>
  

    <?php// include_once("../Assets/gentelella_scripts.html"); ?>
	
  </body>
</html>
