<?php

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}

$file = fopen("requests/ticket.txt", "r");
$ticket = fgets($file);
fclose($file);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php//include_once("../Assets/gentelella_head.html"); ?>
    <title>Instancias</title>
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
                <h3>Situação Operacional</h3>
              </div>

              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Estado das instâncias</h2>
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
                          <th>Estado</th>
                          <th>Alterar Estado</th>
                        </tr>
                      </thead>


                      <tbody>
                        <?php
                    $file = fopen("requests/vm_names.txt", "r");
                    $file_id = fopen("requests/vm_id.txt", "r");
                    if($_SESSION['administrador'][0]=="1"){
                      while(!feof($file)){
                        $line = fgets($file);
                        $line_id = fgets($file_id);
                        $id = explode(" ", $line_id);
                        $id = $id[1];
                        if($line != ""){
                          echo "<tr>";
                          $line = explode(" ", $line);
                          echo "<td>" . $line[0] . "</td>";
                          echo "<td>" . $line[1] . "</td>";
                          $vm = $line[0];
                          if(strlen($line[1]) == 11){ //11 = PoweredOn
                            echo "<td style='text-align:center'><a href='requests/request_validation.php?nome_maquina=$vm&origin=ShutdownOS.php' target='_blank'><input type='button' class='btn btn-dark btn-xs' value='Desligar'></a> <a href='requests/request_validation.php?nome_maquina=$vm&origin=RestartOS.php' target='_blank'><input type='button' class='btn btn-warning btn-xs' value='Reiniciar'></a>";
//                            echo "<a href='https://luziania.rede.stf.gov.br/ui/webconsole.html?vmId=$id&vmName=$vm2&serverGuid=f0383dfb-443e-40f6-ad98-2582bbd6da21&host=luziania.rede.stf.gov.br&sessionTicket=$ticket&thumbprint=3B:C5:85:CF:D2:22:A3:0A:0F:3B:5A:0C:50:9A:BC:ED:D1:E2:0E:5D&locale=pt-BR' target='_blank'><input type='button' class='btn btn-info btn-xs' value='Console'></a></td>";
                          }
                          else{
                            echo "<td style='text-align:center'><a href='requests/request_validation.php?nome_maquina=$vm&origin=PwrON.php' target='_blank'><input type='button' class='btn btn-success btn-xs' value='Ligar'></a> <a href='g_DelVMConfirmation.php?nome_maquina=$vm' target='_blank'><input type='button' class='btn btn-danger btn-xs' value='Remover'></a></td>"; 
                          }
                          echo "</tr>";
                        }
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
                        $line_id = fgets($file_id);
                        $id = explode(" ", $line_id);
                        $id = $id[1];
                        $line = explode(" ", $line);
                        for($count=0;$count<$len;$count++){
                          //echo $vms[$count] . " = " . $line[0] . "<br>";
                          if($vms[$count]==$line[0] && $vms[$count]!=""){
                            echo "<tr>";
                            echo "<td>" . $line[0] . "</td>";
                            echo "<td>" . $line[1] . "</td>";
                            $vm = $line[0];
                            if($_SESSION['administrador'][0]!="0"){
                              if(strlen($line[1]) == 11){ //11 = PoweredOn
                                echo "<td style='text-align:center'><a href='requests/request_validation.php?nome_maquina=$vm&origin=ShutdownOS.php' target='_blank'><input type='button' class='btn btn-dark btn-xs' value='Desligar'></a> <a href='requests/request_validation.php?nome_maquina=$vm&origin=RestartOS.php' target='_blank'><input type='button' class='btn btn-warning btn-xs' value='Reiniciar'></a>";
//                                echo "<a href='https://luziania.rede.stf.gov.br/ui/webconsole.html?vmId=$id&vmName=$vm2&serverGuid=f0383dfb-443e-40f6-ad98-2582bbd6da21&host=luziania.rede.stf.gov.br&sessionTicket=$ticket&thumbprint=3B:C5:85:CF:D2:22:A3:0A:0F:3B:5A:0C:50:9A:BC:ED:D1:E2:0E:5D&locale=pt-BR' target='_blank'><input type='button' class='btn btn-info btn-xs' value='Console'></a></td>";
                                }
                                else{
                                   echo "<td style='text-align:center'><a href='requests/request_validation.php?nome_maquina=$vm&origin=PwrON.php' target='_blank'><input type='button' class='btn btn-success btn-xs' value='Ligar'></a>";
                                  if($_SESSION['administrador'][0]<="2"){
                                    echo " <a href='g_DelVMConfirmation.php?nome_maquina=$vm' target='_blank'><input type='button' class='btn btn-danger btn-xs' value='Remover'></a></td>";
                                  }
                                }
                              }
                              echo "</td>";
                              echo "</tr>";
                            }
                          }
                        }
                      }
                      fclose($file);
                    ?>
                      </tbody>
                    </table>
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
