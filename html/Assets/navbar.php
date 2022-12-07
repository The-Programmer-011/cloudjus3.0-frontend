		<?php
		//Seta o nome dos hypervisors e o diretorio atual
		$dir = getcwd();
		if($_SESSION['hv']==1){
			$hypervisor = "vmware";
		}
		else if($_SESSION['hv']==2){
			$hypervisor = "hyperv";
		}
		else if($_SESSION['hv']==3){
			$hypervisor == "ovm";
		}
		if(isset($_SESSION['windows'])){
			echo '<link rel="stylesheet" href="/Assets/windows_style.css">';
		}
		//caso o seja o stf e o eegg do windows XP nao esteja ativo, carrega o css do tema do stf e seta a cor azul e o logo do stf.
		if($_SESSION['theme']=="stf" && !isset($_SESSION['windows']) && !isset($_SESSION['synth'])){
			echo '<link rel="stylesheet" href="/Assets/stf_style.css">';
			$color = "#3399ff";
			$logo = "stf_logo.png";
		}else{ //Caso contario o logo eh setado como o logo do CloudJus
			$logo = "logo.png";
		?>

		<style>

		header{
 		 position: fixed;
 		 top: 1;
 		 width: 100%;
		}

		.listupdate:hover{
			background-color: #ffd11a;
			color: black;
		}

		</style>

		<header>
		<?php
		if($_SESSION['hv']==1){//Caso o hypervisor seja o VMware, eh setada a cor padrao verde, o nome do hypervisor 'vmware' e carregado o css do vmware
			echo '<link rel="stylesheet" href="/Assets/vmware_style.css">';
			$hypervisor = "vmware";
			$color = "#00cc44";
		}
		else if($_SESSION['hv']==2){//Caso o hypervisor seja o HyperV, eh setada a cor padrao azul, o nome do hypervisor 'hyperv' e carregado o css do HyperV
			echo '<link rel="stylesheet" href="/Assets/hyperv_style.css">';
			$hypervisor = "hyperv";
			$color = "#3399ff";
		}
		else if($_SESSION['hv']==3){//Caso o hypervisor seja o OVM, eh setada a cor padrao vermelha, o nome do hypervisor 'ovm' e carregado o css do OVM
			echo '<link rel="stylesheet" href="/Assets/ovm_style.css">';
			$hypervisor = "ovm";
			$color = "#ff3300";	
		}
		if(isset($_SESSION['windows'])){ //Seta o Windows XP
			echo '<link rel="stylesheet" href="/Assets/windows_style.css">';
			$color = "#00cc44";
		}
		if(isset($_SESSION['synth'])){ //Seta o Windows XP
			echo '<link rel="stylesheet" href="/Assets/synth_style.css">';
			$color = "#00cc44";
		}
		}

		//Abre o arquivo _status.txt dependendo do diretorio atual
		if($dir == "/var/www/html"){
			$status = fopen("$hypervisor/requests/_status.txt", "r");
		}
		else if($dir == "/var/www/html/$hypervisor"){
			$status = fopen("requests/_status.txt", "r");	
		}
		else if($dir == "/var/www/html/$hypervisor/requests"){
			$status = fopen("_status.txt", "r");	
		}
		else if($dir == "/var/www/html/$hypervisor/requests/Snapshots"){
			$status = fopen("../_status.txt", "r");	
		}

		//Seta a variavel de status com o conteudo da _status.txt
		if($status){
			$status_num = fgets($status);
			$status_num = $status_num[2];
			fclose($status);
		}

		//Abre o arquivo _status_list.txt dependendo do diretorio atual
		//1 para ONLINE
		//2 para OCUPADO
		//0 para OFFLINE
		if($dir == "/var/www/html"){
			$status_list = fopen("$hypervisor/requests/_status_list.txt", "r");
		}
		else if($dir == "/var/www/html/$hypervisor"){
			$status_list = fopen("requests/_status_list.txt", "r");	
		}
		else if($dir == "/var/www/html/$hypervisor/requests"){
			$status_list = fopen("_status_list.txt", "r");	
		}
		else if($dir == "/var/www/html/$hypervisor/requests/Snapshots"){
			$status_list = fopen("../_status_list.txt", "r");	
		}
		//Seta a variavel de status com o conteudo da _status_list.txt
		//0 para standby e 1 para atualizando as listas
		if($status_list){
			$status_list_num = fgets($status_list);
			$status_list_num = $status_list_num[2];
			fclose($status_list);
		}
		
		//Seta a variavel de grupo
		$group = $_SESSION['grupo'];

		?>

		<!-- Codigo da navbar -->
		<!--Grande parte desse codigo nao sera necesario mexer-->
		<!--
		
		Caso seja necessario fazer alguma alteracao, basta visualizar o padrao do codigo:
		Ex.
		<div class="dropdown">
			<button class="dropbtn">Gerenciar <-- (((((NOME DO DROPDOWN))))) 
				<i class="fa fa-caret-down"></i>
			</button>
		<div class="dropdown-content">
			<a href="/<?php// echo $hypervisor; ?>/CreateVM.php">Criar maquina virtual</a> <--}--- opcoes do dropdown. Geralemnte sao links para
			<a href="/<?php// echo $hypervisor; ?>/DelVM.php">Excluir maquina virtual</a>  <--}    outras paginas
		</div>
		</div>
		
		Todo bloco eh um dropdown.
		Voce pode copiar e colar esse template para criar novos dropdowns
		-->

		<!--Dropdown do Ambiente-->
		<div class="navbar" style="overflow:hidden">
		<div class="dropdown">
			<button class="<?php if($_SESSION['windows']){echo 'startmenu';} else{echo 'dropbtn';} ?>" style="background-color:<?php echo $color;?>;color:white"><?php if($_SESSION['windows']){echo "<img src='/Assets/windows_logo.png' class='win'> <strong>S T A R T</strong>";} else{ echo "Ambiente";}?>
				<i class="fa fa-caret-down"></i>
			</button>
		<!--Opcoes de hypervisors-->
		<div class="dropdown-content">
			<a href="/main.php?hv=1"><?php if($_SESSION['hv']==1){echo "<strong style='color:$color'>Producao - VMware</strong>";}else{echo "Producao - VMware";}?></a>
			<a href="/main.php?hv=2"><?php if($_SESSION['hv']==2){echo "<strong style='color:$color'>Desenvolvimento - Hyper-V</strong>";}else{echo "Desenvolvimento - Hyper-V";}?></a>
			<a href="/main.php?hv=3"><?php if($_SESSION['hv']==3){echo "<strong style='color:$color'>Oracle VM (em dev)</strong>";}else{echo "Oracle VM (em dev)";}?></a>
		</div>
		</div>

		<!--Dropdown de Gerenciamento-->
		<div class="dropdown">
			<button class="dropbtn">Gerenciar 
				<i class="fa fa-caret-down"></i>
			</button>
		<div class="dropdown-content">
			<a href="/<?php echo $hypervisor; ?>/CreateVM.php">Criar maquina virtual</a>
			<a href="/<?php echo $hypervisor; ?>/CreateVMCluster.php">Criar maquinas virtuais em Bloco</a>
			<a href="/<?php echo $hypervisor; ?>/DelVM.php">Excluir maquina virtual</a>
			<a href="/<?php echo $hypervisor; ?>/backup.php">Proteção de dados</a>
			<a href="/<?php echo $hypervisor; ?>/start_maintenance.php">Período de Manutenção</a>
		</div>
		</div>
		
		<!--Dropdown de Recursos-->
		<div class="dropdown">
			<button class="dropbtn">Recursos
				<i class="fa fa-caret-down"></i>
			</button>
		<div class="dropdown-content">
			<a href="/<?php echo $hypervisor; ?>/AddDisk.php">Adicionar outro disco para uma maquina virtual</a>
			<a href="/<?php echo $hypervisor; ?>/AltCore.php">Alterar a quantidade de processadores de uma VM</a>
			<a href="/<?php echo $hypervisor; ?>/AltMem.php">Alterar a quantidade de memoria de uma VM</a>
		</div></div>

		<!--Dropdown de Estado-->
		<div class="dropdown">
			<button class="dropbtn">Estado
				<i class="fa fa-caret-down"></i>
			</button>
		<div class="dropdown-content">
			<a href="/<?php echo $hypervisor; ?>/ShutdownOS.php">Shutdown sistema operacional</a>
			<a href="/<?php echo $hypervisor; ?>/RestartOS.php">Restart sistema operacional</a>
			<a href="/<?php echo $hypervisor; ?>/PwrON.php">Power ON maquina virtual</a>
			<a href="/<?php echo $hypervisor; ?>/PwrOFF.php">Power OFF maquina virtual</a>
		</div></div>

		<!--Dropdown das Listas-->
		<div class="dropdown">
			<button class="dropbtn">Listar
				<i class="fa fa-caret-down"></i>
			</button>
		<div class="dropdown-content">
			<a href="/<?php echo $hypervisor; ?>/ListInfoVMs.php">Listar todas informacoes das maquinas virtuais</a>
			<a href="/<?php echo $hypervisor; ?>/SmallList.php">Listar informacoes basicas das vms</a>
			<a href="/<?php echo $hypervisor; ?>/ListInfoVM.php">Listar informacoes de uma VM</a>
			<a href="/<?php echo $hypervisor; ?>/Cluster.php">Listar informacoes do Cluster</a>
		</div></div>

		<!-- Dropdown dos Snapshots-->
		<div class="dropdown">
			<button class="dropbtn">Snapshots
				<i class="fa fa-caret-down"></i>
			</button>
		<div class="dropdown-content">
			<a href="/<?php echo $hypervisor; ?>/ListSnap.php">Gerenciar snapshots de uma VM</a>
			<a href="/<?php echo $hypervisor; ?>/CreateSnap.php">Criar snapshot de uma VM</a>
		</div></div>

		<!-- Dropdown da parte do usuario-->
		<div class="dropdown" style="float:right">
			<button class="dropbtn"><?php if($_SESSION['administrador'][$_SESSION['hv'] - 1]!="1"){echo '<img class="user" src="/Assets/user.png">';} else{echo '<img class="adm" src="/Assets/adm.png">';}?>   <?php echo $_SESSION['name']; ?>
				<i class="fa fa-caret-down"></i>
			</button>
		<div class="dropdown-content" style="right:0">
			<?php echo "<a><strong style='color:$color'>$group</strong></a>"?> <!--Nome do Grupo-->
			<a href="/<?php echo $hypervisor; ?>/manage_requests.php">Pedidos</a> <!--Pagina de pedidos-->
			<a href="/<?php echo $hypervisor; ?>/AllRequests.php">Todos Pedidos</a> <!--Pagina de todos os pedidos-->
			
			<!-- Caso seja super_adm do hypervisor, o usuario pode trocar o tema e requisitar a atualizacao completa das listas do hypervisor atual e entrar na pagina de aprovacao de pedidos -->
			<?php if($_SESSION['administrador'][$_SESSION['hv'] - 1]=="1"){?>
				<?php if($status_list_num != 1){ ?>
				<a href="/manual_update.php" class="listupdate">Atualizar Listas</a>
				<?php } ?> 
				<a href="/<?php echo $hypervisor; ?>/ToProcess.php">Processar Pedidos</a>
				<a href="/main.php?theme=dark" class="dark">Dark</a>
				<a href="/main.php?theme=stf" class="stf">STF</a>
			<?php } ?>
			<a href="/logout.php" id="logout">Logout</a>
		</div></div>
		<div>
			<input style="float:right; margin-right: 10px" type="button" value="Refresh" class="refresh" onclick='window.location.reload(true);'>
		</div>
		<div>
			<p style="float:right; margin-right: 20px">Status: 
			<?php
			//Mostra o status da interface
			if($status_num == 1){
				if($_SESSION['theme']=="stf"){
					echo "<span style='color:$color'>ONLINE</span>";	
				}
				else{
					echo "<span style='color:#40ff00'>ONLINE</span>";
				}
			}
			else if($status_num == 2){
				echo "<span style='color:#ff9933'>OCUPADO</span>";
			}
			else if($status_num == 0){
				echo "<span style='color:red'>OFFLINE</span>";
			}
			if($status_list_num == 1){
				if($_SESSION['theme']=="stf"){
					echo "<span> - </span><span style='color:$color'>Atualizando Listas</span>";	
				}
				else{
					echo "<span> - </span><span style='color:#ffff00'>Atualizando Listas</span>";
				}
			}
			?>
		</p>
		</div>
		<div>
			<!-- Mostra o ambiente atual -->
			<p style="float:right; margin-right: 20px"><?php
				if($_SESSION['hv']==1){
					echo "<strong style='color:$color'>Producao - VMware</strong>";
				}
				else if($_SESSION['hv']==2){
					echo "<strong style='color:$color'>Desenvolvimento - Hyper-V</strong>";
				}
				else if($_SESSION['hv']==3){
					echo "<strong style='color:$color'>Oracle VM (em dev)</strong>";
				}
			?></p>
		</div>
		<!-- Contem o link de download do console dos hypervisors -->
		<div>
			<a href="/Console/
			<?php
				if($_SESSION['hv']==1){
					echo 'VMwareConsole.exe';
				}
				else if($_SESSION['hv']==2){
					echo 'HyperVConsole.exe';
				}
			?>
			" id="console">Abrir Console</a>
		</div>
		<div>
			<a href="/g_menu.php" id="console">Versão <?php echo $_SESSION['version']; ?></a>
		</div>
		<?php if(isset($_SESSION['denied'])){?>
		<div>
			<p><strong style="background-color: #ff471a; color: white; padding: 14px 16px; margin-left: 20px;border-radius: 5px;">ACESSO NEGADO!</strong></p>
		</div>
		<?php } ?>
	</div>
	</header>
	<br>
	<?php

	//Mostra o Logo do STF ou do CloudJus dependendo do tema atual
	if($_SESSION['theme'] != "stf"){
		echo "<br><br><br>";
	}
	if(isset($_SESSION['synth'])){
		echo '<a  href="/main.php"><img src="https://media.giphy.com/media/2LL3TFHsKWdFe/giphy.gif" class="synthlogo"></a>';
	}
	else if(isset($_SESSION['windows'])){ ?> <a  href="/main.php"><img src="/Assets/windowslogo.png" class="winlogo"></a> <?php }else{?>
	<a  href="/main.php"><img class="stflogo" src="/Assets/<?php echo $logo;?>"></a>
	<?php
	//Mostra o header de boas-vindas
	}
	if($_SESSION['hv']==1){
		echo "<h1 style='color:$color'>Ambiente de Produção - VMware - $group</h1>";
	}
	else if($_SESSION['hv']==2){
		echo "<h1 style='color:$color'>Ambiente de Desenvolvimento - Hyper-V - $group</h1>";
	}
	else if($_SESSION['hv']==3){
		echo "<h1 style='color:$color'>Ambiente Oracle VM (em dev) - $group</h1>";
	}
	?>
	<!--The_Programmer-->
