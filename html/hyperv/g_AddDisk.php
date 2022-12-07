<?php

//=============================HEADER=============================

//Inicio de sessao
session_start();

//Caso o usuario nao esteja logado, redireciona para a pagina de login
if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}
//Caso o usuario nao tenha permissao nivel 3 redireciona para a main
if($_SESSION['administrador'][1]=="0" || $_SESSION['administrador'][1]>"3"){
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
    $_GET['nome_maquina'] = strtoupper($_GET['nome_maquina']); //Passa o nome da maquina para maiusculo
    $search = $_GET["nome_maquina"];
    $file = fopen("requests/vms_names.txt", "r"); //Abre o arquivo com as informacoes das maquinas virtuais
    if($file){
      while(!feof($file)){ //Testa se a maquina existe
        $line = fgets($file);
        $word_array = explode(" ", $line);
        if($search == $word_array[0]){
          $name_not_found = 0; //Erro que informa que a mauina nao foi encontrada
          $error = 0;
          //echo strlen($word_array[1]);
          //O script testa se a maquina esta ligada ou desligada pelo tamanho da palavra
          if(strlen($word_array[1]) == 9){ //9 eh para "Running", 5 eh para "off"
            $machine_on = 1; //Erro que informa que a maquina estal ligada
            $error = 1;
            break;
          }
        }
      }
    }
    fclose($file);
    //Caso o usuario nao seja super_adm do hyperV o script testa se o ousuario tem permissao para executar esse comando na maquina escolhida
    if($_SESSION['administrador'][1]!="1"){
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

    //Caso nao tenha sido encontrado nenhum erro no form, o script redireciona para a proxima pagina
    if(!$error){
      $vm = $_GET['nome_maquina'];
      $disk = $_GET['disk'];
      header("Location: requests/request_validation.php?nome_maquina=$vm&disk=$disk");
    }
  }
  if($name_not_found){
    $notification .= "new PNotify({ title: 'Máquina não encontrada', text: 'A máquina que você está procurando não existe.', type: 'error', styling: 'bootstrap3'});";
  }
  else if($unauthorized_machine){
    $notification .= "new PNotify({ title: 'Acesso não autorizado', text: 'Está máquina não pertence ao seu grupo.', type: 'error', styling: 'bootstrap3'});";
  }
  else if($machine_on){
    $notification .= "new PNotify({ title: 'Máquina ligada', text: 'É preciso desligar a máquina antes de aumentar o disco.', type: 'error', styling: 'bootstrap3'});";
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php// include_once("../Assets/gentelella_head.html"); ?>
    <title>Adicionar volume de bloco</title>
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
    <!-- PNotify -->
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">

    <link href="/Assets/node_modules/gentelella/vendors/cropper/dist/cropper.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="/Assets/node_modules/gentelella/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md" <?php if($notification){echo 'onload="' . $notification . '"';}?>>

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
                <h3>Adicionar Volume de Bloco</h3>
              </div>

              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Adicionar Disco</h2>
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
                      if($name_not_found){
                        echo "* maquina nao encontrada!";
                      }
                      else if($unauthorized_machine){
                        echo "* a máquina não pertence ao grupo $group!<br><br>";
                      }
                      else if($machine_on){
                        echo "* a maquina deve estar desligada para ser deletada!";
                      }
                      ?>
                      <br>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome da Máquina <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input list="vms" name="nome_maquina" type="text" required="required" class="form-control col-md-7 col-xs-12">
                          <datalist id="vms">
                            <?php
                            $filename = "requests/Groups/" . $_SESSION['grupo'] . ".txt";
                            //echo $filename;
                            $file = fopen($filename, "r");
                            $line = fgets($file);
                            $line = explode(";", $line);
                            $len = count($line);
                            for($count=0;$count<$len;$count++){
                              $vm = $line[$count];
                              echo "<option value='$vm'>";
                            }
                            fclose($file);
                            ?>
                          </datalist>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Tamanho do Disco (GB): <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" name="disk" value="<?php echo $_GET['disk'];?>" min="1" max="65536" required class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <a href="/g_menu.php"><button class="btn btn-primary" type="button">Cancelar</button></a>
						            <button class="btn btn-primary" type="reset">Resetar</button>
                          <button type="submit" class="btn btn-success">Confirmar</button>
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
    <!-- PNotify -->
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.nonblock.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="/Assets/node_modules/gentelella/build/js/custom.php"></script>
  

    <?php// include_once("../Assets/gentelella_scripts.html"); ?>
	
  </body>
</html>
