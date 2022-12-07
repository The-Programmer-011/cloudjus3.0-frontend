<?php

session_start();

if(!isset($_SESSION['username'])){
	header("Location: index.php?op=err");
}


?>

<html>
<head>
	<link rel="icon" href="/Assets/tab_icon.png">
</head>
<title>Cluster Info</title>
<link rel="stylesheet" href="/hyperv/hyperv_style.css">
<body>
<link rel="stylesheet" href="/Assets/hyperv_style.css">
<body>
	<?php include_once("../Assets/navbar.php"); ?>
	<br><br>
	<?php
	if($_SESSION['hv']==1){
		echo '<iframe src="http://dashboard.stf.jus.br/dashboard/db/plataforma-de-virtualizacao-vmware?orgId=1" width="1570" height="768"></iframe>';
	}
	else if($_SESSION['hv']==2){
		echo '<iframe src="http://gama.rede.stf.gov.br" width="1570" height="768"></iframe>';
	}
	?>
</body>
</html>