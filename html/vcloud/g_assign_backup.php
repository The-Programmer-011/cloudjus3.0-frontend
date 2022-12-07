<?php

//=============================HEADER=============================

//Inicio de sessao
session_start();

//Caso o usuario nao esteja logado, redireciona para a pagina de login
if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}
//Caso o usuario nao tenha permissao nivel 3 redireciona para a main
if($_SESSION['administrador'][2]=="0" || $_SESSION['administrador'][2]>"2"){
  header("Location: /index.php?op=err");
  $_SESSION['denied'] = 1;
}

//=============================Validação=============================

//Verifica se o o form ja foi preenchido
  $error = 1;
  $error--;
  if($_GET["nome_maquina"]==""){
    $error++;
    $null_name = 1;
  }
  //Caso o form tenha sido preenchido, o php faz a consistencia de dados
  else{
    $error++;
    $name_not_found = 1;
    $_GET['nome_maquina'] = strtolower($_GET['nome_maquina']); //Passa o nome da maquina para maiusculo
    $search = $_GET["nome_maquina"];
    $file = fopen("requests/vm_names.txt", "r"); //Abre o arquivo com as informacoes das maquinas virtuais
    if($file){
      while(!feof($file)){ //Testa se a maquina existe
        $line = fgets($file);
        $word_array = explode(" ", $line);
        if($search == $word_array[0]){
          $name_not_found = 0; //Erro que informa que a maquina nao foi encontrada
          $error = 0;
        }
      }
    }
    fclose($file);

    //Caso o usuario nao seja super_adm do hyperV o script testa se o usuario tem permissao para executar esse comando na maquina escolhida
    if($_SESSION['administrador'][0]!="1"){
      $filename = "requests/Groups/" . $_SESSION['grupo'] . ".txt"; //Abre o arquivo com as maquinas que o grupo do usuario tem permissao
      //echo $filename;
      $file = fopen($filename, "r");
      $unauthorized_machine=1;
      $line = fgets($file);
      $line = explode(";", $line);
      $len = count($line);
      $error++;
      for($count=0;$count<$len;$count++){ //Procura a maquina escolhida no arquivo
        $vm = $line[$count];
        if($_GET['nome_maquina'] == $vm){
          $unauthorized_machine=0; //Erro que informa que o usuario nao tem permissao para executar essa acao nesta maquina
          $error--;
          break;
        }
      }
      fclose($file);
    }
  }

  if($error){
    header("Location: /index.php?op=err");
    $_SESSION['denied'] = 1;
  }

  if($_GET['policy_code']){
    $policy_code = $_GET['policy_code'];
    $vm = $_GET['nome_maquina'];
    header("Location: requests/request_validation.php?nome_maquina=$vm&policy_code=$policy_code");
  }
  
  ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php// include_once("../Assets/gentelella_head.html"); ?>
    <title>Backup</title>
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
                <h3>Backup</h3>
              </div>

              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Máquina: <?php echo $_GET['nome_maquina']; ?></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div>
                    <form method="get" id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome da Máquina <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input list="vms" type="text" name="nome_maquina" id="first-name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $_GET['nome_maquina']; ?>" readonly>
                        </div>
                      </div>

                    <div>
                    <div style="width: 50%; float:left">
                      <br><br><br>
                      <h4>Política atual: <strong style="color:red"><?php if(!isset($_GET['policy'])){ echo "NENHUMA";}?></strong></h4>
                      <?php
                      if(isset($_GET['policy'])){
                        $file = fopen("requests/_backup.txt", "r");
                      
                        while(!feof($file)){
                          $line = fgets($file);
                          $element = explode(",", $line);
                          $vm_name = $element[0];
                          if($vm_name==$_GET['nome_maquina']){
                            $aux = explode(";", $element[1]);
                            $backup_policy = $aux[0];
                            break;
                          }
                        }
                      
                        $vm = $_GET['nome_maquina'];
                      
                        if($backup_policy){
                          $files_in_dir = scandir("../backup/");
                          $len = count($files_in_dir);
                          for($count=2;$count<$len;$count++){
                            if($files_in_dir[$count][0] == $backup_policy){
                              $filename = "../backup/" . $files_in_dir[$count];
                              $backup_file = fopen($filename, "r");
                      
                              //SETANDO VARIAVEIS
                      
                              $line = fgets($backup_file);
                              $line = explode(";", $line);
                              $line = $line[0];
                              $element = explode(":", $line);
                              $days = $element[1];
                      
                              $line = fgets($backup_file);
                              $line = explode(";", $line);
                              $line = $line[0];
                              $element = explode(":", $line);
                              $months = $element[1];
                      
                              $line = fgets($backup_file);
                              $line = explode(";", $line);
                              $line = $line[0];
                              $element = explode(":", $line);
                              $years = $element[1];
                      
                              $line = fgets($backup_file);
                              $line = explode(";", $line);
                              $line = $line[0];
                              $element = explode(":", $line);
                              $policy = $element[1];
                      
                              fclose($backup_file);
                      
                              $aux = explode("_", $files_in_dir[$count]);
                              $aux = explode(".", $aux[1]);
                              $policy_name = $aux[0];
                      
                              //FIM
                      
                              echo '<table class="table table-bordered" style="width: 50%">';
                      
                              echo "<tr>";
                              echo "<th>Tipo</th>";
                              echo "<td>$policy_name</td>";
                              echo "</tr>";
                      
                              echo "<th>Frequencia</th>";
                      
                              if($days){
                                echo "<td>$days dias</td>";
                              }
                              else if($months){
                                echo "<td>$months meses</td>";
                              }
                              else if($years){
                                echo "<td>$years anos</td>";
                              }
                      
                              echo "<tr>";
                              echo "<th>Polítca</th>";
                              echo "<td>$policy</td>";
                              echo "</tr>";
                      
                              echo "</table>";
                              echo "</div>";
                      
                              echo "<br><br>";
                            }
                          }
                        }
                      }
                      ?>
                    </div>

                    <div style="width: 50%; float:left">
                      <?php if(!isset($_GET['policy'])){ echo "<br>";}?>
                      <h4>Nova política:</h4>
                      <table class="table table-bordered" style="width: 50%" class="background">
                          <tr>
                            <th>Nome</th>
                            <th>Backup</th>
                            <th>Política</th>
                            <th style="width: 20%">Selecione:</th>
                          </tr>
                      
                          <?php
                      
                          $files_in_dir = scandir("../backup/");
                          $len = count($files_in_dir);
                          for($count=2;$count<$len;$count++){
                      
                            echo "<tr>";
                      
                            $filename = "../backup/" . $files_in_dir[$count];
                            $backup_file = fopen($filename, "r");
                            $cod = $files_in_dir[$count][0];
                      
                            //SETANDO VARIAVEIS
                      
                            $line = fgets($backup_file);
                            $line = explode(";", $line);
                            $line = $line[0];
                            $element = explode(":", $line);
                            $days = $element[1];
                      
                            $line = fgets($backup_file);
                            $line = explode(";", $line);
                            $line = $line[0];
                            $element = explode(":", $line);
                            $months = $element[1];
                      
                            $line = fgets($backup_file);
                            $line = explode(";", $line);
                            $line = $line[0];
                            $element = explode(":", $line);
                            $years = $element[1];
                      
                            $line = fgets($backup_file);
                            $line = explode(";", $line);
                            $line = $line[0];
                            $element = explode(":", $line);
                            $policy = $element[1];
                      
                            fclose($backup_file);
                            
                            $aux = explode("_", $files_in_dir[$count]);
                            $aux = explode(".", $aux[1]);
                            $policy_name = $aux[0];
                      
                            echo "<td>$policy_name</td>";
                      
                            if($days){
                              echo "<td>$days dias</td>";
                            }
                            else if($months){
                              echo "<td>$months meses</td>";
                            }
                            else if($years){
                              echo "<td>$years anos</td>";
                            }
                      
                            echo "<td>$policy</td>";
                      
                            echo '<td><input type="radio" name="policy_code" value=' . $cod . '></td>';
                      
                            fclose($backup_file);
                      
                            echo "</tr>";
                          }
                      ?>
                      </table>

                    </div>
                    </div>

                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button type="submit" class="btn btn-success">Confirmar Mudança</button>
                          <a href="/vmware/g_backup.php"><button class="btn btn-primary" type="button">Voltar</button></a>
                          <a href="/g_menu.php"><button class="btn btn-primary" type="button">Cancelar</button></a>
                        </div>
                        <br><br>
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
