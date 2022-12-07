#1.1
#Teste de verificação de instalação do POWERCLI e VMRC nas versões corretas
Function FUN_VERIFICA_SOFTWARE ($NOME_SOFTWARE,$VERSAO_SOFTWARE,$CAMINHO_INSTALADOR_SOFTWARE) {
	$LISTA_SOFTWARES_INSTALADOS = Get-ItemProperty HKLM:\Software\Wow6432Node\Microsoft\Windows\CurrentVersion\Uninstall\*
	If (!($LISTA_SOFTWARES_INSTALADOS | where {($_.displayname -eq "$NOME_SOFTWARE") -and ($_.DisplayVersion -like "$VERSAO_SOFTWARE")})) {
		Write-Host "`nNão foi possível detectar o software " -ForegroundColor Red -NoNewline; & Write-Host """$NOME_SOFTWARE""" -ForegroundColor Yellow -nonewline; & Write-Host " em seu computador. Para executar este script é necessário instalá-lo." -ForegroundColor Red
		Do {$PERGUNTA = Read-Host "`nDeseja instalar o ""$NOME_SOFTWARE"" agora? [S] ou [N]"} while (($PERGUNTA -ne "S") -and ($PERGUNTA -ne "N"))
		If ($PERGUNTA -eq "S"){
			Write-Host "Por favor, aguarde alguns instantes até a instalação ser iniciada." -ForegroundColor Green
			Start-Process "$CAMINHO_INSTALADOR_SOFTWARE" -Wait
		}
		Else {
			Write-Host "Caso deseje instalá-lo posteriormente, o caminho do instalador encontra-se em:" -ForegroundColor Green
			Write-Host """$CAMINHO_INSTALADOR_SOFTWARE""" -ForegroundColor Yellow
			pause
			Exit
	    }
    }
}

FUN_VERIFICA_SOFTWARE "VMware PowerCLI" "6.5.*" "\\arquivos\bds\SERVIDORES\SOFTWARES\VMWARE.Virtualizacao\VMware vSphere CLI - Command Line Interface\VMware-PowerCLI-6.5.0-4624819.exe"
FUN_VERIFICA_SOFTWARE "VMware Remote Console" "9.*" "\\arquivos\bds\SERVIDORES\SOFTWARES\VMWARE.Virtualizacao\VMRC\VMware-VMRC-9.0.0-4288332.msi"


#Adiciona módulo no powershell para utilizar comandos do VMware PowerCLI caso já não esteja
If ((Get-Module -name VMware.VimAutomation.Core -ErrorAction SilentlyContinue) -eq $null) {
	Get-Module -ListAvailable VMware.VimAutomation.Core | Import-Module | Out-Null
}


#Define variáveis para o serevidor vCenter e realiza a conexão. Caso conectado a outro vCenter, é feita a desconexão e então conecta no vcenter informado
$VCENTER = "vmware.stf.jus.br"

If ($global:DefaultVIServer -eq $null) {
	Connect-VIServer $VCENTER
}
ElseIf ($VCENTER -notmatch $global:DefaultVIServer.Name) {
	Disconnect-VIServer $global:DefaultVIServer -Confirm:$false
	Connect-VIServer $VCENTER
}

If ($global:DefaultVIServer -eq $null) {
	Exit
}

#===================================HOSTS===================================

$stream = [System.IO.StreamWriter] "C:\DATABASE\vmware\Lists\hosts.txt"
$lista = Get-VMHost | where {$_.ConnectionState -eq "Connected"} | Sort-Object name | ForEach-Object -Begin {$i=0} -Process {
    $i++
    "{0:D2}. {1} (Memoria Livre: {2} GB)" -f $i,$_.Name,"{0:N2}" -f ($_.MemoryTotalGB - $_.MemoryUsageGB)
} -outvariable listaHosts
ForEach ($item in $lista) {$stream.WriteLine($item)}
#Write-Host $listaHosts[$entradaHost-1].Split()[1] -ForegroundColor Green
$stream.close()

#===================================TEMPLATES===================================

$stream = [System.IO.StreamWriter] "C:\DATABASE\vmware\Lists\templates.txt"
		$lista = Get-Template -Location Templates -NoRecursion | Sort-Object name | ForEach-Object -Begin {$i=0} -Process {
			$i++
			"{0:D2}. {1}" -f $i,$_.Name
		} -outvariable listaTemplates
		ForEach ($item in $lista) {$stream.WriteLine($item)}
		#Write-Host $listaHosts[$entradaHost-1].Split()[1]
$stream.close()

#===================================DATASTORES===================================

$stream = [System.IO.StreamWriter] "C:\DATABASE\vmware\Lists\datastores.txt"
		$array = @()
		$i = 0
		$lista = Get-Datastore | where {$_.ParentFolderId -ne "Folder-group-s211"} | Where-Object {$_.FreeSpaceGB -gt $maior} | Sort-Object name | ForEach-Object -Process {
		#Identifica ID da pasta pai do datastore
		$folder_id = Get-Folder -id $_.ParentFolderId | Select-Object -ExpandProperty Id
		#Reseta as variáveis
		$folder_path = $null
		$parentFolder_id = $null
		$folder_obj = $null
		$parentFolder_id = $null
		$folder_name = $null

		#Loop para coletar informação das pastas onde estão os datastores
		do{
			if (!$parentFolder_id){
				#Identifica ID da pasta pai do pasta do datastore
				$folder_obj = Get-Folder -id $folder_id
				$parentFolder_id = $folder_obj | Select-Object -ExpandProperty ParentId
				$folder_name = $folder_obj.Name
			}
			else{
				$folder_obj = Get-Folder -id $parentFolder_id
				$parentFolder_id = $folder_obj | Select-Object -ExpandProperty ParentId
				$folder_name = $folder_obj.Name
			}
			#Monta o path do datastore
			$folder_path = $folder_name + "\" + $folder_path
		}
		#Loop até encontrar a pasta raiz com nome 'datastore', último nível de pasta
		while (($folder_name -ne "datastore"))
			if(($folder_path -notlike "*NoShow*")){
				#write-host $_.name
				$i = $i + 1
				#$array = $array + $_.name
				"{0:D2}. {1} (Espaço Livre: {2} GB)" -f $i,$_.Name,"{0:N2}" -f $_.FreeSpaceGB
			}
		} -outvariable listaDatastores
		ForEach ($item in $lista) {$stream.WriteLine($item)}
		#Write-Host $listaDatastores[$entradaDatastore-1].Split()[1] -ForegroundColor Green
$stream.close()
  
#===================================PASTAS===================================

$stream = [System.IO.StreamWriter] "C:\DATABASE\vmware\Lists\pastas.txt"
		$lista = Get-Folder -Type VM | where {$_.ParentId -eq "Folder-group-v22"} | Sort-Object name | ForEach-Object -Begin {$i=0} -Process {
			$i++
			"{0:D2}. {1}" -f $i,$_.Name
		} -outvariable listaPastaDestino
		ForEach ($item in $lista) {$stream.WriteLine($item)}
		#Write-Host $listaHosts[$entradaHost-1].Split()[1] -ForegroundColor Green 
#selecionaPastaDestino
$stream.close()