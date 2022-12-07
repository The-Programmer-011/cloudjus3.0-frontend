<?php
$VERSION = "2.11.6";
//$BETA = "BETA";
$VMWARE = "cst-VCT-52490373-af01-9b85-0f8a-67a25f97ca05--tp-3E-1E-9C-F2-E8-22-B8-85-CA-53-D9-5A-57-53-1D-9E-5B-13-9E-35";
$VCLOUD = "cst-VCT-520529a0-12c7-4ff2-488e-cd30482d6445--tp-3B-C5-85-CF-D2-22-A3-0A-0F-3B-5A-0C-50-9A-BC-ED-D1-E2-0E-5D";

//Caso o usuario tenha acessado a pagina nao segura, redireciona para a pagina https
if (empty($_SERVER['HTTPS'])) {
    header('Location: https://cloudjus.stf.jus.br/');
    exit;
}

?>

<?php
/**
 * Created by Joe of ExchangeCore.com
 * E Gabriel Rodrigues
 */

//Inicio de sessao header
session_start();
if(isset($_SESSION['username'])){ //Caso o usuario ja esteja logado ele redireciona para a pagina main.php
    header("Location: g_menu.php");
}

//Setup teste de conexao ldap
if(isset($_POST['username']) && isset($_POST['password'])){
    $adServer = "ldap://ad.stf.jus.br";
    $domain = "@rede.stf.gov.br";
    
    $ldap = ldap_connect($adServer);
    $username = $_POST['username'];
    $password = $_POST['password'];

    $username = explode("@", $username);
    if($username[0] == "synthit"){
        $username = $username[1];
        $synth = 1;
    }
    else{
        $username = $username[0];
    }

    $ldaprdn = $username.$domain;

    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

    $bind = @ldap_bind($ldap, $ldaprdn, $password);

    //Verifica se a conexao foi bem sucedida
    if ($bind) {
        $filter="(sAMAccountName=$username)";
        $result = ldap_search($ldap,"DC=rede,DC=stf,DC=gov,DC=br",$filter);
        ldap_sort($ldap,$result,"sn");
        $info = ldap_get_entries($ldap, $result);
        for ($i=0; $i<$info["count"]; $i++)
        {
           if($info['count'] > 1)
               break;
            echo "<p>You are accessing <strong> ". $info[$i]["sn"][0] .", " . $info[$i]["givenname"][0] ."</strong><br /> (" . $info[$i]["samaccountname"][0] .")</p>\n";
            echo '<pre>';
            //Acessa o arquivo GRUPOS.txt e coloca todos os grupos do arquivo em um vetor
            $group_file = fopen("GRUPOS/GRUPOS.txt", "r");
            $count = 0;
            while(!feof($group_file)){
            	$groups[$count] = fgets($group_file);
            	$count++;
            }
            fclose($group_file);
            $gp_len = count($groups); //$gp_len contem o numero de grupos da interface

            $usergroup = $info[$i]["memberof"]; //Acessa a variavel ldap contendo todos os grupos da rede do STF que o usuario faz parte
            $len = count($usergroup); //$len contem o numero de grupos que o usuario esta presente na rede do STF
            //Para cada grupo presente no arquivo GRUPOS.txt, o programa varre toda todo vetor $usergroup verificando se esse esta presente.
            for($count=0;$count<$gp_len;$count++){
            	$grupo = explode(";", $groups[$count]);
            	for($cont=0;$cont<$len;$cont++){
            		echo $grupo[0] . " " . $grupo[1] . " " . $grupo[2] . "<br>";
            		if(strpos($usergroup[$cont], $grupo[0])){ //Caso o programa ache um dos grupos da arquivo GRUPOS.txt no vetor $usergroup:
            			$group = $grupo[1]; //Seta o nome do grupo
            			$adm = $grupo[2][0] . $grupo[2][1] . $grupo[2][2]; //Seta a permissao
                        $dashboard = $grupo[3]; //Contem o link do grafana para o dashboard
                        $pasta = $grupo[4]; //Contem o nome da pasta do grupo no VMware
            			$hasgroup = 1; //Seta essa variavel que sai dos dois loops
            			break;
            		}
            	}
            	if($hasgroup){
            		break;
            	}
            }
            if(!$hasgroup){ //Caso o usuario nao esteja em nenhum grupo ele se torna um usuario normal
            	$adm = "000";
            	$group = "Usuario";
            }
            //var_dump($info); //Para mais informacoes do ldap e para debugging, descomente essa linha.
            echo $i . " " . $info[$i];
            echo '</pre>';
            $userDn = $info[$i]["distinguishedname"][0];
            //inicializa a sessao e seta as variaveis globais de sessao
            session_start();
            $_SESSION['username'] = $username; //variavel contendo o username
            $_SESSION['administrador'] = $adm; //variavel contendo o codigo de acesso [0] - VMware, [1] - HyperV
            $_SESSION['name'] = $info[$i]["givenname"][0]; //Variavel contendo o primeiro nome do usuario
            $_SESSION['lastname'] = $info[$i]["sn"][0]; //Variavel contendo o ultimo nome do usuario
            $_SESSION['grupo'] = $group; //Variavel contendo o grupo do usuario
            $_SESSION['hv'] = "1"; //Variavel contendo o hypervisor. 1 - VMware, 2 - HyperV
            //Caso o usuario seja super_adm o tema eh setado como dark, caso contrario eh setado o tema default do STF
            if($group == "SUPER_ADM"){
                $_SESSION['theme'] = "dark";
            }
            else{
                $_SESSION['theme'] = "stf";
            }
            $_SESSION['dashboard'] = $dashboard; //Variavel que contem o link do dashboard do VMware
            $_SESSION['pasta'] = $pasta; //Variavel que contem a pasta de destino das VMs do VMware
            $_SESSION['t_vmware'] = $VMWARE; //Ticket de acesso ao console html do vmware
            $_SESSION['t_vcloud'] = $VCLOUD; //Ticket de acesso ao console html do vcloud
            if($synth){
                $_SESSION['synth'] = "1";
            }
            $_SESSION['version'] = $VERSION;
            if($BETA != ""){
                $_SESSION['BETA'] = $BETA;
            }
            header("Location: g_menu.php"); //Redireciona para a escolha do hypervisor (para debugging, comente essa linha)
        }
        @ldap_close($ldap);
    } else {
        $notification = "new PNotify({ title: 'Senha incorreta', text: 'Verifique seu usuário e senha. Caso tenha dúvidas clique na opção -Como entar?-', type: 'error', styling: 'bootstrap3'});";
    }

}
?>

<!--
    <form action="#" method="POST">
        <strong><label for="username">Username: </label><input id="username" type="text" name="username" /> 
        <label for="password">Password: </label><input id="password" type="password" name="password" />        <input type="submit" name="submit" value="Submit" /></strong>
    </form> -->


<!-- Pagina de login -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>

    body{
      background-image: url("/Assets/light_background.jpg");
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
      background-position: center;
    }

    </style>

    <title>CloudJus</title>

<!-- Bootstrap -->
    <link href="/Assets/node_modules/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/Assets/node_modules/gentelella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="/Assets/node_modules/gentelella/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="/Assets/node_modules/gentelella/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="/Assets/node_modules/gentelella/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- PNotify -->
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="/Assets/node_modules/gentelella/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login" <?php if($notification){echo 'onload="' . $notification . '"';}?> style='background-image: url("/Assets/light_background.jpg");
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
      background-position: center;'>
    <br><img src="/Assets/stf_logo.png" style="max-width: 10%;">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form method="post">
              <h1>Login</h1>
              <div>
                <input type="text" class="form-control" placeholder="Username" name="username" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" name="password" required="" />
              </div>
              <div>
                <input type="submit" class="btn btn-default submit" href="index.php" value="Entrar">
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                
                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-cloud"></i> Cloudjus</h1>
                  <p>CloudJus Version <?php echo $VERSION; ?></p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>

    <?php include_once("Assets/gentelella_scripts.html"); ?>
    <!-- PNotify -->
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="/Assets/node_modules/gentelella/vendors/pnotify/dist/pnotify.nonblock.js"></script>
    <!-- jQuery -->
    <script src="/Assets/node_modules/gentelella/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="/Assets/node_modules/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="/Assets/node_modules/gentelella/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="/Assets/node_modules/gentelella/vendors/nprogress/nprogress.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="/Assets/node_modules/gentelella/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="/Assets/node_modules/gentelella/vendors/iCheck/icheck.min.js"></script>

  </body>
</html>

<!--The_Programmer-->
<!--Joe of ExchangeCore.com-->
<!--Kleyton Castro-->
