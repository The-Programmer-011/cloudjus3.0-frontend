<?php

//=============================Funções=============================

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

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][$_SESSION['hv'] - 1]!="1"){
  header("Location: GroupRequests.php");
}

//=============================Processamento=============================

if(isset($_GET['operation']) && isset($_GET['requests'])){
  $len = count($_GET['requests']);
  if($_GET['operation']=="del"){
    for($count=0;$count<$len;$count++){
      $filename = $_GET['requests'][$count];
      echo $filename . "<br>";
      rename("requests/ToProcess/" . $filename, "requests/Requests/x" . $filename);
      echo "Aprove";
    }
  }
  else if($_GET['operation']=="aprov"){
    for($count=0;$count<$len;$count++){
      $filename = $_GET['requests'][$count];
      echo $filename . "<br>";
      rename("requests/ToProcess/" . $filename, "requests/Requests/" . $filename);
      echo "denied";
    }
  }
  sleep(3);
  header("Location: g_ToProcess.php");
}


else{
  $checkbox = '<td class="a-center "><input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>"></td>';
  ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>To Process</title>
    <!-- Bootstrap -->
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
      <?php include_once("../Assets/gentelella_setup.php"); ?>
      <!-- Sidebar -->
      <?php include_once("../Assets/gentelella_sidebar.php"); ?>
      <!-- Sidebar -->

      <!-- top navigation -->
      <?php include_once("../Assets/gentelella_navbar.php"); ?>
      <!-- /top navigation -->

        <!-- page content -->   
        <?php
        $dir = "requests/ToProcess";
        $files_in_dir = scan_time_dir($dir);
        $size = count($files_in_dir);
        ?>
        <form method="get">
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Aprovar Pedidos</h3>
              </div>

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Lista de pedidos</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>

                  <div class="x_content">
                    <p>Selecione os pedidos a serem aprovados ou descartados</p>

                    <div class="table-responsive">
                      <table class="table table-striped jambo_table bulk_action">
                        <thead>
                          <tr class="headings">
                            <th>
                              <input type="checkbox" id="check-all" class="flat">
                            </th>
                              <th class="column-title">Operacao</th>
                              <th class="column-title">Usuario</th>
                              <th class="column-title">Nome VM</th>
                              <th class="column-title">Data</th>
                              <th class="column-title">Grupo</th>
                            </th>
                            <th class="bulk-actions" colspan="7">
                              <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
                            </th>
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
                            $filename_path = "/ToProcess/" . $files_in_dir[$count];
                            echo "<tr>";
                            if($name[0]=="1"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Criacao VM</td>";
                            }
                            else if($name[0]=="2"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Criacao VM sem template</td>";  
                            }
                            else if($name[0]=="3"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Remocao VM</td>"; 
                            }
                            else if($name[0]=="4"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Adicionar disco</td>";  
                            }
                            else if($name[0]=="5"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Alterar numero de processadores</td>";  
                            }
                            else if($name[0]=="6"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Alterar quantidade de memoria</td>";  
                            }
                            else if($name[0]=="7"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Shutdown VM</td>";  
                            }
                            else if($name[0]=="8"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Restart VM</td>"; 
                            }
                            else if($name[0]=="a"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Power ON VM</td>";  
                            }
                            else if($name[0]=="9"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Power OFF VM</td>"; 
                            }
                            else if($name[0]=="$1"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Criar Snapshot</td>"; 
                            }
                            else if($name[0]=="$2"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Reverter Snapshot</td>";  
                            }
                            else if($name[0]=="$3"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename_path' target='_blank'>Remover Snapshot</td>"; 
                            }
                            else if($name[0]=="b"){
                              ?>
                              <td class="a-center ">
                                     <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                                   </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename'>Modi target='_blank'fificar Politica de Backup</td>";  
                            }
                            else if($name[0]=="c"){
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="requests[]" value="<?php echo $filename; ?>">
                              </td>
                              <?php
                              echo "<td><a href='g_show_request.php?file=$filename'>Perí target='_blank'odo de manutenção</td>"; 
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
                              echo "</a>";
                            }
                            echo "</tr>";
                          }
                        
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <input class="flat" type="radio" name="operation" value="del"> Descartar<br>
                  <input class="flat" type="radio" name="operation" value="aprov" checked> Confirmar<br>
                  <br>
                  <input class="btn btn-success" type="submit" title="Processar">
                  <a href="/g_menu.php"><input class="btn btn-default" type="button" Value="Cancelar"></a>

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
  </form>

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
	
  </body>
</html>
<?php } ?>