<?php

//Header de inicio de sessao
session_start();

if(!isset($_SESSION['username'])){ //caso o usuario nao esteja logado, redireciona para a pagina de login
	header("Location: index.php?op=err");
}
if(isset($_GET['hv'])){ //Caso exista um request de modificao de hyperisor, ele modifica a variavel de sessao 'hv' (hypervisor)
	if($_GET['hv']=="windows"){
		if(!isset($_SESSION['windows'])){
			$_SESSION['windows'] = 1;
		}
		else{
			unset($_SESSION['windows']);
		}
	}
	else{
		$_SESSION['hv'] = $_GET['hv'];
		$hypervisor = $_SESSION['hv'];
	}
}
if(isset($_GET['theme'])){ //Caso exista um request de alteracao de tema, ele modifca a variavel de sessao 'theme'
	$_SESSION['theme'] = $_GET['theme'];
}
if(isset($_SESSION['custom'])){
	unset($_SESSION['custom']);
}
if(isset($_SESSION['operation'])){
	unset($_SESSION['operation']);
}
if(isset($_SESSION['pedido_manuntencao'])){
	unset($_SESSION['pedido_manuntencao']);
}
if(isset($_SESSION['pedido'])){
	unset($_SESSION['pedido']);
}
if(isset($_SESSION['gentelella'])){
	unset($_SESSION['gentelella']);
}
?>


<!DOCTYPE html>
<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<!--<title>CPAV - Console de Provisionamento do Ambiente Virtual</title>-->
<title>CloudJus - <?php if($_SESSION['hv']==1){echo "VMware";}else{echo "HyperV";}?></title>
<style>
	a, a:hover, a:active{
		color:white;
		text-decoration: none;
	}
</style>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<body>
	<?php include_once("Assets/navbar.php"); //Carrega a navbar?> 
	<?php
		//Caso a variavel 'denied' esteja setada, ele deseta.
		//o usuario ja foi alertado na navbar
		if(isset($_SESSION['denied'])){ 
			unset($_SESSION['denied']);
		}
	?>
	<!--Mensagem de boas vindas-->
	<?php echo "<h2>Bem-vi<a href='main.php?hv=windows' style='cursor: text;'>n</a>do, " . $_SESSION['name'] . " " . $_SESSION['lastname'] . "!</h2>"; ?>
	<?php
	if($_SESSION['hv']==2){ //Caso seja a interface HyperV, mostra se o script esta rodando ou nao
		echo "<h3>Status:</h3>";
		$status = fopen("hyperv/requests/_status.txt", "r"); //Abre o arquivo _status.txt e pega seu conteudo (Ver codigo de status no readme)
		if($status){
			$status_num = fgets($status);
			$status_num = $status_num[2];
			if($status_num == 1){
				echo "<h4 style='color:$color'>Running</h4>";
			}
			else if($status_num == 2){
				echo "<h4 style='color:#ff9933'>Busy</h4>";
			}
			else if($status_num == 0){
				echo "<h4 style='color:red'>OFFLINE</h4>";
			}
			fclose($status);
		}
		else{ //Caso nao seja possivel abrir o arquivo:
			echo "<h4 style='color:red'>Cannot reach master server</h4>";
		}
	}
	//echo $_SESSION['administrador'];
	if($_SESSION['administrador']!="11" && $_SESSION['administrador']!="22"){ //Isso é mostrado caso o usuario nao seja adm de grupo ou super adm
		echo "<p>Obs: Sua conta está limitada para alguns recursos.</p>";
	}
	if($_SESSION['theme']=="stf"){ //Caso o tema seja STF o grafana abrira na cor clara
		$grafana_theme = "&theme=light";
	}
	else{ //Caso contrario sera aberto o grafana escuro
		$grafana_theme = "&theme=dark";
	}
	if($_SESSION['hv']=="2" || $_SESSION['hv']=="3" || isset($_SESSION['windows']));
	/*else{ //Abre o dashboard
		$grafana_link = "https://vmware/ui";
		//echo $grafana_link;
		echo "<iframe src=" . $grafana_link . " width='100%' height='500' frameBorder='0'></iframe>";
	}
	*/
	?>

	<iframe src="https://luziania.rede.stf.gov.br/ui/#?extensionId=vsphere.core.administration.roleView" width='100%' height='500' frameBorder='0'></iframe>

	<script type="text/javascript">
		new MutationObserver(function(mutations) {
	    mutations.some(function(mutation) {
	      if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
	        console.log(mutation);
	        console.log('Old src: ', mutation.oldValue);
	        console.log('New src: ', mutation.target.src);
	        window.location.href = "https://cloudjusd.stf.jus.br/g_menu.php";
	        return true;
	      }

	      return false;
	    });
	  }).observe(document.body, {
	    attributes: true,
	    attributeFilter: ['src'],
	    attributeOldValue: true,
	    characterData: false,
	    characterDataOldValue: false,
	    childList: false,
	    subtree: true
	  });
	</script>
</body>
</html>

<object data="menu.html" id="boxmenu"></object>

<!-- The_Programmer -->