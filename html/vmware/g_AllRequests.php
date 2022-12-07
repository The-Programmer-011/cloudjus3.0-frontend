<?php

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][$_SESSION['hv'] - 1]!="1"){
  header("Location: g_GroupRequests.php");
}
?>

<?php

function scan_time_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array(); 
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }
    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : false;
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php//include_once("../Assets/gentelella_head.html"); ?>
    <title>Requests</title>
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
                <h3>Pedidos</h3>
              </div>

              <div class="title_right">
                 </div>
               </div>
               <div class="clearfix"></div>
               <div class="row">
                 <div class="col-md-12 col-sm-12 col-xs-12">
                   <div class="x_panel">
                     <div class="x_title">
                       <h2>Lista de pedidos</h2>
                       <ul class="nav navbar-right panel_toolbox">
                         <li><a class="close-link"><i class="fa fa-close"></i></a>
                         </li>
                       </ul>
                       <div class="clearfix"></div>
                     </div>

              <div class="" role="tabpanel" data-example-id="togglable-tabs">
                      <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Processados</a>
                        </li>
                        <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Não processados</a>
                        </li>
                        <li role="presentation" class=""><a href="#tab_content3" role="tab" id="profile-tab2" data-toggle="tab" aria-expanded="false">Aguardando aprovação</a>
                        </li>
                      </ul>
                      <div id="myTabContent" class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                          <?php
                          $dir = "requests/Processed";
                          $files_in_dir = scan_time_dir($dir);
                          $size = count($files_in_dir);
                          ?>            
                          <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th></th>
                                <th>Operacao</th>
                                <th>Usuario</th>
                                <th>Nome VM</th>
                                <th>Data</th>
                                <th>Grupo</th>
                              </tr>
                            </thead>
      
      
                            <tbody>
                              <?php
                              for($count=0;$count<$size;$count++){
                                $isRequest = 1;
                                $name = explode(" ", $files_in_dir[$count]);
                                $group_name = explode(".", $name[5]);
                                $group_name = $group_name[0];
                                $filename = $files_in_dir[$count];
                                if($name[0]=="1"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Criacao VM</a></td>";
                                }
                                else if($name[0]=="2"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Criacao VM sem template</a></td>"; 
                                }
                                else if($name[0]=="3"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Remocao VM</a></td>";  
                                }
                                else if($name[0]=="4"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Adicionar disco</a></td>"; 
                                }
                                else if($name[0]=="5"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Alterar numero de processadores</a></td>"; 
                                }
                                else if($name[0]=="6"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Alterar quantidade de memoria</a></td>"; 
                                }
                                else if($name[0]=="7"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Shutdown VM</a></td>"; 
                                }
                                else if($name[0]=="8"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Restart VM</a></td>";  
                                }
                                else if($name[0]=="a"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Power ON VM</a></td>"; 
                                }
                                else if($name[0]=="9"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Power OFF VM</a></td>";  
                                }
                                else if($name[0]=="$1"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Criar Snapshot</a></td>";  
                                }
                                else if($name[0]=="$2"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Reverter Snapshot</a></td>"; 
                                }
                                else if($name[0]=="$3"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Remover Snapshot</a></td>";  
                                }
                                else if($name[0]=="b"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Modifificar Politica de Backup</a></td>";  
                                }
                                else if($name[0]=="c"){
                                  echo "<tr><td></td><td><a href='g_show_request.php?file=$filename' target='_blank'>Período de manutenção</a></td>"; 
                                }
                                else{
                                  $isRequest = 0;
                                }
                                if($isRequest){
                                  echo "<td>" . $name[1] . "</td>";
                                  echo "<td>" . $name[2] . "</td>";
                                  $hora = explode("-", $name[3]);
                                  $data = explode("-", $name[4]);
                                  $ano = explode(".", $data[2]);
                                  echo "<td>" . $ano[0] . "/" . $data[1] . "/" . $data[0] . " " . $hora[0] . ":" . $hora[1] . ":" . $hora[2] . "</td>";
                                  echo "<td>" . $group_name . "</td>";
                                  echo "</tr>";
                                }
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                          <?php
                          $dir = "requests/";
                          $files_in_dir = scan_time_dir($dir);
                          $size = count($files_in_dir);
                          ?>            
                          <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th>Operacao</th>
                                <th>Usuario</th>
                                <th>Nome VM</th>
                                <th>Data</th>
                                <th>Grupo</th>
                              </tr>
                            </thead>
      
      
                            <tbody>
                              <?php
                              for($count=0;$count<$size;$count++){
                                $isRequest = 1;
                                $name = explode(" ", $files_in_dir[$count]);
                                $group_name = explode(".", $name[5]);
                                $group_name = $group_name[0];
                                $filename = $files_in_dir[$count];
                                if($name[0]=="1"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Criacao VM</a></td>";
                                }
                                else if($name[0]=="2"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Criacao VM sem template</a></td>"; 
                                }
                                else if($name[0]=="3"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Remocao VM</a></td>";  
                                }
                                else if($name[0]=="4"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Adicionar disco</a></td>"; 
                                }
                                else if($name[0]=="5"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Alterar numero de processadores</a></td>"; 
                                }
                                else if($name[0]=="6"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Alterar quantidade de memoria</a></td>"; 
                                }
                                else if($name[0]=="7"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Shutdown VM</a></td>"; 
                                }
                                else if($name[0]=="8"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Restart VM</a></td>";  
                                }
                                else if($name[0]=="a"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Power ON VM</a></td>"; 
                                }
                                else if($name[0]=="9"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Power OFF VM</a></td>";  
                                }
                                else if($name[0]=="$1"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Criar Snapshot</a></td>";  
                                }
                                else if($name[0]=="$2"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Reverter Snapshot</a></td>"; 
                                }
                                else if($name[0]=="$3"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Remover Snapshot</a></td>";  
                                }
                                else if($name[0]=="b"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Modifificar Politica de Backup</a></td>";  
                                }
                                else if($name[0]=="c"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Período de manutenção</a></td>"; 
                                }
                                else{
                                  $isRequest = 0;
                                }
                                if($isRequest){
                                  echo "<td>" . $name[1] . "</td>";
                                  echo "<td>" . $name[2] . "</td>";
                                  $hora = explode("-", $name[3]);
                                  $data = explode("-", $name[4]);
                                  $ano = explode(".", $data[2]);
                                  echo "<td>" . $hora[0] . ":" . $hora[1] . ":" . $hora[2] . " " . $data[0] . "/" . $data[1] . "/" . $ano[0] . "</td>";
                                  echo "<td>" . $group_name . "</td>";
                                  echo "</tr>";
                                }
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="profile-tab">
                          <?php
                          $dir = "requests/ToProcess";
                          $files_in_dir = scan_time_dir($dir);
                          $size = count($files_in_dir);
                          ?>            
                          <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead>
                              <tr>
                                <th>Operacao</th>
                                <th>Usuario</th>
                                <th>Nome VM</th>
                                <th>Data</th>
                                <th>Grupo</th>
                              </tr>
                            </thead>
      
      
                            <tbody>
                              <?php
                              for($count=0;$count<$size;$count++){
                                $isRequest = 1;
                                $name = explode(" ", $files_in_dir[$count]);
                                $group_name = explode(".", $name[5]);
                                $group_name = $group_name[0];
                                $filename = $files_in_dir[$count];
                                if($name[0]=="1"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Criacao VM</a></td>";
                                }
                                else if($name[0]=="2"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Criacao VM sem template</a></td>"; 
                                }
                                else if($name[0]=="3"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Remocao VM</a></td>";  
                                }
                                else if($name[0]=="4"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Adicionar disco</a></td>"; 
                                }
                                else if($name[0]=="5"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Alterar numero de processadores</a></td>"; 
                                }
                                else if($name[0]=="6"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Alterar quantidade de memoria</a></td>"; 
                                }
                                else if($name[0]=="7"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Shutdown VM</a></td>"; 
                                }
                                else if($name[0]=="8"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Restart VM</a></td>";  
                                }
                                else if($name[0]=="a"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Power ON VM</a></td>"; 
                                }
                                else if($name[0]=="9"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Power OFF VM</a></td>";  
                                }
                                else if($name[0]=="$1"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Criar Snapshot</a></td>";  
                                }
                                else if($name[0]=="$2"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Reverter Snapshot</a></td>"; 
                                }
                                else if($name[0]=="$3"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Remover Snapshot</a></td>";  
                                }
                                else if($name[0]=="b"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Modifificar Politica de Backup</a></td>";  
                                }
                                else if($name[0]=="c"){
                                  echo "<tr><td><a href='g_show_request.php?file=$filename' target='_blank'>Período de manutenção</a></td>"; 
                                }
                                else{
                                  $isRequest = 0;
                                }
                                if($isRequest){
                                  echo "<td>" . $name[1] . "</td>";
                                  echo "<td>" . $name[2] . "</td>";
                                  $hora = explode("-", $name[3]);
                                  $data = explode("-", $name[4]);
                                  $ano = explode(".", $data[2]);
                                  echo "<td>" . $hora[0] . ":" . $hora[1] . ":" . $hora[2] . " " . $data[0] . "/" . $data[1] . "/" . $ano[0] . "</td>";
                                  echo "<td>" . $group_name . "</td>";
                                  echo "</tr>";
                                }
                              }
                              ?>
                            </tbody>
                          </table>
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
