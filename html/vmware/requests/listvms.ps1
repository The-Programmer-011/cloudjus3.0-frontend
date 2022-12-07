#vmware
#1.2
"4" > "C:\DATABASE\vmware\_list.txt"

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

    $stream = [System.IO.StreamWriter] "C:\DATABASE\vmware\vms.txt"
	MODE CON:LINES=50 COLS=154
    Write-Host "---Updating VM List---"
	$stream.WriteLine(" +----------------------+------------+---------+------+--------+------+---------+---------+-----+----------------+----+")
	$stream.WriteLine(" |   Máquina Virtual    |    Host    |    Nr   |  %   |  Mem.  |  %   |  Disco  |  Disco  | HW  |     Uptime     | On |")
	$stream.WriteLine(" |                      |            | Núcleos | CPU  |  Assoc | Mem. |Aloc.(GB)|Usado(GB)| Vers|                | Off|")
	$stream.WriteLine(" +----------------------+------------+---------+------+--------+------+---------+---------+-----+----------------+----+")
	[int]$cont = 0
	$servidorhost2 = Get-Cluster trindade | Get-VMHost
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
    "0" > "C:\DATABASE\vmware\_list.txt"