﻿#Adiciona módulo no powershell para utilizar comandos do VMware PowerCLI caso já não esteja
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

    Write-Host "---Updating VM List---"
    $stream = [System.IO.StreamWriter] "vm_names.txt"
	MODE CON:LINES=50 COLS=154
	[int]$cont = 0
	$servidorhost2 = Get-Cluster trindade | Get-VMHost
	foreach($servidorhost in $servidorhost2) {
		$vms = Get-VM | Sort-Object name
		foreach($vm in $vms) {
            if($cont -eq 0){
                $first_vm = $vm
            }
			$prop = $vm.name
            $stream.WriteLine($prop)
            if(($cont -ne 0) -and ($vm -eq $first_vm)){
                $break_ord = 1
                break
            }         
            $cont++
		}
        if($break_ord -eq 1){
            break
        }     
	}
	MODE CON:LINES=25 COLS=80
    $stream.close()