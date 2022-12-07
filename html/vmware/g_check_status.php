<?php

//=============================HEADER=============================

//Inicio de sessao
session_start();

//Caso o usuario nao esteja logado, redireciona para a pagina de login
if(!isset($_SESSION['username'])){
  header("Location: /index.php?op=err");
}
//Caso o usuario nao tenha permissao nivel 3 redireciona para a main
if($_SESSION['administrador'][$_SESSION['hv'] - 1]!="1"){
  header("Location: /index.php?op=err");
  $_SESSION['denied'] = 1;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php// include_once("../Assets/gentelella_head.html"); ?>
    <title>Checar status do script</title>
    <!-- Path do icone de tab -->
    <link rel="icon" href="/Assets/tab_icon.png">
    <!-- include dos css -->
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
      <?php include_once("Assets/gentelella_setup.php"); ?>
      <!-- Sidebar -->
      <?php include_once("Assets/gentelella_sidebar.php"); ?>
      <!-- Sidebar -->

      <!-- top navigation -->
      <?php include_once("Assets/gentelella_navbar.php"); ?>
      <!-- /top navigation -->

        <!-- page content -->
        <!-- include dos css -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Status dos scripts</h3>
              </div>

              <!-- Nova "janela" na pagina -->
              <div class="title_right">
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Status</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div> 
                  </div>
                  <div class="x_content">
                    <?php
                    //Abre o arquivo _status.txt e verifica se ele existe
                    $file = fopen("$hypervisor/requests/_status.txt", "w");
                    //Se o arquivo existe, é escrito o codigo 0 no arquivo.
                    //Se o scrip estiver rodando, a cada segundo o valor desse arquivo será alterado para 1
                    //Caso o script esteja travado ou nao rodando o valor continuara sendo 0
                    // 1 = ONLINE, 0 = OFFLINE
                    if($file){
                      fwrite($file, "0");
                      fclose($file);
                      sleep(2);
                    }
                    //Caso nao seja possivel abrir o arquivo significa que a conexao com o britania nao esta ativa e eh preciso fazer um mount
                    else{
                    	$no_connection = 1;
                    }
                    echo "<h1>Status: "; 
                    if($no_connection){
                    	echo "<span style='color:red'>Conexão com Britania offline</span>";
                    }
                    else{ //Abre o arquivo novamente apos 2 segundos e imrime online caso o valor seja 1 ou offline caso o valor seja 0
                    	$file = fopen("$hypervisor/requests/_status.txt", "r");
                    	$line = fgets($file);
                    	if(strpos($line, "1") != FALSE){
                    		echo "<span style='color:#40ff00'>ONLINE</span>"; //#40ff00 = verde
                    	}
                    	else{
                    		echo "<span style='color:red'>OFFLINE</span>";
                    	}
                      echo "</h1>";
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
