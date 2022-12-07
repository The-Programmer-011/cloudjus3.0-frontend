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

    Write-Host "---Updating VM id List---"
    $stream = [System.IO.StreamWriter] "C:\DATABASE\vcloud\vm_id.txt"
	[int]$cont = 0
	$servidorhost2 = Get-Cluster $NOME_CLUSTER | Get-VMHost
	foreach($servidorhost in $servidorhost2) {
		$vms = Get-VM | Sort-Object name
		foreach($vm in $vms) {
            if($cont -eq 0){
                $first_vm = $vm
            }
			$prop = $vm.name
            Write "$prop "
            $stream.Write($prop + " ")
            $prop = (Get-VM $vm.name).Id.tostring().replace("VirtualMachine-","")
            $stream.WriteLine($prop)
            Write "$prop`n"
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
    $stream = [System.IO.StreamWriter] "C:\DATABASE\vcloud\ticket.txt"
    [string]$ticket = (get-view (get-view serviceinstance).Content.SessionManager).AcquireCloneTicket()
    $stream.WriteLine($ticket)
    $stream.close()