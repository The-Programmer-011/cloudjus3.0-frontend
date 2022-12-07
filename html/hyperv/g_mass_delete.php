<?php

//=============================HEADER=============================

session_start();

if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}
if($_SESSION['administrador'][$_SESSION['hv']-1]!="1"){
  header("Location: /index.php?op=err");
  $_SESSION['denied'] = 1;
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php//include_once("../Assets/gentelella_head.html"); ?>
    <title>Excluir instancias em lote</title>
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
        <form method="get" action="requests/request_validation.php">
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Excluir Instâncias em Lote</h3>
              </div>

              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>VMs</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  
                  <div class="table-responsive">
                    <table id="datatable-buttons" class="table table-striped jambo_table bulk_action">
                      <thead>
                        <tr class="headings">
                          <th>
                            <input type="checkbox" id="check-all" class="flat">
                          </th>
                          <th class="column-title">Maquina</th>
                          <th class="column-title">Estado</th>
                          <th></th>
                        </tr>
                      </thead>


                      <tbody>
                        <?php
                        $file = fopen("requests/vms_names.txt", "r");
                        while(!feof($file)){
                          $line = fgets($file);
                          if($line!=""){
                            $line = explode(" ", $line);
                            $vm = $line[0];
                            $state = $line[1];
                            if(strstr($state, "Off")){
                              echo "<tr>";
                              ?>
                              <td class="a-center ">
                                <input type="checkbox" class="flat" name="vms[]" value="<?php echo $vm; ?>">
                              </td>
                              <?php
                              echo "<td>" . $line[0] . "</td>";
                              echo "<td>" . $line[1] . "</td>";
                              $vm = $line[0];
                              echo "</tr>";
                            }
                          }
                        }
                        fclose($file);
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <h1 style="color:red">ATENÇÃO, VOCÊ APAGARÁ TODAS AS MÁQUINAS MARCADAS!</h1>
                  <br>
                  <input class="btn btn-danger btn-lg" type="submit" title="APAGAR MÁQUINAS">

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
  

    <?php// include_once("../Assets/gentelella_scripts.html"); ?>
	
  </body>
</html>