#vcloud
#1.1.1
"4" > "C:\DATABASE\vcloud\_list.txt"

Function ajustatabela($size) {
	$c = ""
	if($prop.length -lt $size) {
		$c += $prop
		for($j = 0; $j -lt ($size - $prop.length); $j++){$c += " "}
		return $c + " | "
	}
	else {
		$c += $prop.substring(0,$size)
		return $c + " | "
	}
}

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

    $stream = [System.IO.StreamWriter] "C:\DATABASE\vcloud\vms.txt"
	MODE CON:LINES=50 COLS=154
    Write-Host "---Updating VM List---"
	$stream.WriteLine(" +----------------------+------------+---------+------+--------+------+---------+---------+-----+----------------+----+")
	$stream.WriteLine(" |   Máquina Virtual    |    Host    |    Nr   |  %   |  Mem.  |  %   |  Disco  |  Disco  | HW  |     Uptime     | On |")
	$stream.WriteLine(" |                      |            | Núcleos | CPU  |  Assoc | Mem. |Aloc.(GB)|Usado(GB)| Vers|                | Off|")
	$stream.WriteLine(" +----------------------+------------+---------+------+--------+------+---------+---------+-----+----------------+----+")
	[int]$cont = 0
	$servidorhost2 = Get-Cluster $NOME_CLUSTER | Get-VMHost
	foreach($servidorhost in $servidorhost2) {
		$vms = Get-VM | Sort-Object name
		foreach($vm in $vms) {
            if($cont -eq 0){
                $first_vm = $vm
            }
			$TotalSize = 0
			$TotalFileSize = 0
			$TotalSize = $vm | foreach {"{0:N2}" -f $_.ProvisionedSpaceGB}
			$TotalFileSize = $vm | foreach {"{0:N2}" -f $_.UsedSpaceGB}
			$linha = " | "
			$prop = $vm.name
			$linha += ajustatabela(20)
			$prop = ($vm.VMHost).tostring().split(".")[0]
			$linha += ajustatabela(10)
			[string]$prop = $vm.NumCpu
			$linha += ajustatabela(7)
			[string]$prop = "{0:N2}" -f ($vm | Get-Stat -Stat cpu.usage.average | Measure-Object Value -Average | foreach {$_.average})
			$linha += ajustatabela(4)
			[string]$prop = $vm.MemoryMB
			$linha += ajustatabela(6)
			[string]$prop = "{0:N2}" -f ($vm | Get-Stat -Stat mem.usage.average | Measure-Object Value -Average | foreach {$_.average})
			$linha += ajustatabela(4)
			$prop = $TotalSize
			$linha += ajustatabela(7)
			$prop = $TotalFileSize
			$linha += ajustatabela(7)
			$prop = $vm.version
			$linha += ajustatabela(3)
			$uptime = (New-TimeSpan -Seconds (get-vm $vm | select -ExpandProperty ExtensionData | Select -ExpandProperty summary | select -ExpandProperty quickstats | foreach {$_.uptimeseconds})).tostring()
			$prop = $uptime
			$linha += ajustatabela(14)
			$prop = $vm.PowerState
			if($prop -eq "PoweredOn") {$linha += "On |"}
			Else {$linha += "Off|"}
            if(($cont -ne 0) -and ($vm -eq $first_vm)){
                $break_ord = 1
                break
            }
            Write-Host $linha
			$stream.WriteLine($linha)
			$stream.WriteLine(" +----------------------+------------+---------+------+--------+------+---------+---------+-----+----------------+----+")           
            $cont++
		}
        if($break_ord -eq 1){
            break
        }     
	}
	MODE CON:LINES=25 COLS=80
    $stream.close()
    "0" > "C:\DATABASE\vcloud\_list.txt"