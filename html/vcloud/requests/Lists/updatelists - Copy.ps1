#vcloud
#1.1
#Adiciona módulo no powershell para utilizar comandos do VMware PowerCLI caso já não esteja
If ((Get-Module -name VMware.VimAutomation.Core -ErrorAction SilentlyContinue) -eq $null) {
	Get-Module -ListAvailable VMware.VimAutomation.Core | Import-Module | Out-Null
}


#Define variáveis para o serevidor vCenter e realiza a conexão. Caso conectado a outro vCenter, é feita a desconexão e então conecta no vcenter informado
$VCENTER = "luziania.rede.stf.gov.br"
$NOME_CLUSTER = "anapolis"

If ($global:DefaultVIServer -eq $null) {
	Connect-VIServer $VCENTER -user "srv_hyperv" -password "ferr@ri386267"
}
ElseIf ($VCENTER -notmatch $global:DefaultVIServer.Name) {
	Disconnect-VIServer $global:DefaultVIServer -Confirm:$false
	Connect-VIServer $VCENTER -user "srv_hyperv" -password "ferr@ri386267"
}

If ($global:DefaultVIServer -eq $null) {
	Exit
}

#===================================HOSTS===================================

$stream = [System.IO.StreamWriter] "C:\DATABASE\vcloud\Lists\hosts.txt"
$lista = Get-VMHost | where {($_.ConnectionState -eq "Connected") -and ($_.Name -ne "10.200.16.213")}   | Sort-Object name | ForEach-Object -Begin {$i=0} -Process {
    $i++
    "{0:D2}. {1} (Memoria Livre: {2} GB)" -f $i,$_.Name,"{0:N2}" -f ($_.MemoryTotalGB - $_.MemoryUsageGB)
} -outvariable listaHosts
ForEach ($item in $lista) {$stream.WriteLine($item)}
#Write-Host $listaHosts[$entradaHost-1].Split()[1] -ForegroundColor Green
$stream.close()

#===================================TEMPLATES===================================

$stream = [System.IO.StreamWriter] "C:\DATABASE\vcloud\Lists\templates.txt"

#Get-VMHost | where {($_.ConnectionState -eq "Connected") -and ($_.Name -ne "10.200.16.213")}

		$lista = Get-Template -Location Templates -NoRecursion | where {($_.Name -ne "Linux Ubuntu 18 LTS Template")} | Sort-Object name | ForEach-Object -Begin {$i=0} -Process {
			$i++
			"{0:D2}. {1}" -f $i,$_.Name
		} -outvariable listaTemplates
		ForEach ($item in $lista) {$stream.WriteLine($item)}
		#Write-Host $listaHosts[$entradaHost-1].Split()[1]
$stream.close()

#===================================DATASTORES===================================

$stream = [System.IO.StreamWriter] "C:\DATABASE\vcloud\Lists\datastores.txt"
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

$stream = [System.IO.StreamWriter] "C:\DATABASE\vcloud\Lists\pastas.txt"
		$lista = Get-Folder -Type VM | where {$_.ParentId -eq "Folder-group-v3"} | Sort-Object name | ForEach-Object -Begin {$i=0} -Process {
			$i++
			"{0:D2}. {1}" -f $i,$_.Name
		} -outvariable listaPastaDestino
		ForEach ($item in $lista) {$stream.WriteLine($item)}
		#Write-Host $listaHosts[$entradaHost-1].Split()[1] -ForegroundColor Green 
#selecionaPastaDestino
$stream.close()