#setup
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

#main
$file = Get-Content "_vm_names.txt"
$size = $file.Count
for($count=0; $count -lt $size; $count++){
    $vm = $file[$count]
    Write-Host $vm
    $filename = $vm + ".txt"
    if ((Get-Snapshot -VM $vm).count -eq 0){}
    else{
        Get-VM $vm | Get-Snapshot | select vm,name,created,ParentSnapshot,@{N="SizeGB";E={"{0:N2}" -f $_.SizeGB}},IsCurrent > $filename
    }
}