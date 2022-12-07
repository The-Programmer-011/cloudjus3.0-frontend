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
	<h1>Informacoes do cluster</h1>
	<iframe src="http://dashboard.stf.jus.br/dashboard/db/plataforma-de-virtualizacao-vmware?orgId=1" width="1590" height="500" frameBorder="0"></iframe>
</body>
</html>