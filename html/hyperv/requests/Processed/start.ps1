#===========================================

Remove-Item $env:USERPROFILE\Downloads\* -Include *HyperVConsole* -Force
#criando console.bat

$user = "rede\" + $env:UserName + "_a"

while ($domain.name -eq $null){
$cred = Get-Credential $user #Read credentials
 $username = $cred.username
 $password = $cred.GetNetworkCredential().password

 # Get current domain using logged-on user's credentials
 $CurrentDomain = "LDAP://" + ([ADSI]"").distinguishedName
 $domain = New-Object System.DirectoryServices.DirectoryEntry($CurrentDomain,$UserName,$Password)

if ($domain.name -eq $null)
{
 write-host "Authentication failed - please verify your username and password."
}
else
{
 write-host "Successfully authenticated with domain $domain.name"
}
}

$username = $username.Split("\")
$user = $username[1]
Write-Host $user

$groups = Get-ADPrincipalGroupMembership $user | select name
$len = $groups.Count

$user = '"' + $user + '"'

$fileGroups = Get-Content "\\britania\Scripts\GRUPOS.txt"
$file_len = $fileGroups.Count

for($count_file = 0; $count_file -lt $file_len; $count_file++){
    $fileGroup = $fileGroups[$count_file]
    $fileGroup = $fileGroup.Split(";")
    $vms_group_file = $fileGroup[1]
    $fileGroup = $fileGroup[0]
    for($count=0; $count -lt $len; $count++){
        if ($groups[$count] -like "*$fileGroup*"){
            Write-Host "Faz parte do grupo $fileGroup"
            $break = 1
            break
        }
    }
    if($break){break}
}

if($vms_group_file -eq "SUPER_ADM"){
    $adm = 1
}
else{
    $vms_file = "\\britania\Scripts\Groups\" + $vms_group_file + ".txt"
    $vms = Get-Content $vms_file
    $vms = '"' + $vms + '"'
}

$stream = [System.IO.StreamWriter] "\\castro\apl\InfraTI\Cloudjus\console.bat"

$stream.WriteLine("powershell -f \\castro\apl\InfraTI\Cloudjus\core.ps1")

$stream.close()

#criando core.ps1

$stream = [System.IO.StreamWriter] "\\castro\apl\InfraTI\Cloudjus\core.ps1"

$stream.Write('$adm = ')
$stream.WriteLine($adm)
$stream.Write('$vm_list = ')
$stream.WriteLine($vms)
$stream.WriteLine('Start-Process -FilePath "\\pontebranca\bds\SERVIDORES\SCRIPTS e UTILITARIOS\HyperV\requisitos\requisitos.bat" -Verb runAs -Wait')
$stream.WriteLine('$ErrorActionPreference = "SilentlyContinue"')
$stream.WriteLine('Function VMexiste ()')
$stream.WriteLine('{   $vm = $vm.ToUpper()')
$stream.WriteLine('    $SaidaConsole = Get-ClusterNode -Cluster paranhos')
$stream.WriteLine('    for($i = 0; $i -lt $SaidaConsole.length; $i++) {')
$stream.WriteLine('        [String]$buscaVM = get-vm -ComputerName $SaidaConsole[$i] -Name $vm | select name # where {$_.status -like "*operating*"} |')
$stream.WriteLine('        $buscaVM = $buscaVM.ToUpper() ')
$stream.WriteLine('        if($buscaVM.Contains($vm)) {return $true} ')
$stream.WriteLine('    } }')
$stream.WriteLine('Function Host_da_VM ()')
$stream.WriteLine('{   $vm = $vm.ToUpper()  ')
$stream.WriteLine('    $SaidaConsole = Get-ClusterNode -Cluster paranhos ')
$stream.WriteLine('    for($i = 0; $i -lt $SaidaConsole.length; $i++) {')
$stream.WriteLine('        [String]$buscaVM = get-vm -ComputerName $SaidaConsole[$i] -Name $vm | select name')
$stream.WriteLine('        $buscaVM = $buscaVM.ToUpper() ')
$stream.WriteLine('        if($buscaVM.Contains($vm)) {return $SaidaConsole[$i]}')
$stream.WriteLine('}}')
$stream.WriteLine('    Do {$vm = Read-Host "`nNome da maquina virtual"} while (-not(VMexiste))')
$stream.WriteLine('if($adm -eq 1){')
$stream.WriteLine('Write-Host "Conectando..."')
$stream.WriteLine('    [String]$servidorHost = Host_da_VM ')
$stream.WriteLine('    vmconnect.exe $servidorHost $vm')
$stream.WriteLine('exit')
$stream.WriteLine('}')
$stream.WriteLine('else{')
$stream.WriteLine('$vms = $vm_list.Split(";")')
$stream.WriteLine('$len = $vms.Count')
$stream.WriteLine('for($count=0; $count -lt $len; $count++){')
$stream.WriteLine('if($vm -eq $vms[$count]){')
$stream.WriteLine('Write-Host "Conectando..."')
$stream.WriteLine('    [String]$servidorHost = Host_da_VM ')
$stream.WriteLine('    vmconnect.exe $servidorHost $vm')
$stream.WriteLine('exit')
$stream.WriteLine('}')
$stream.WriteLine('}')
$stream.WriteLine('Write-Host "Voce nao tem permissao para acessar essa vm!"')
$stream.WriteLine('}')
$stream.WriteLine('pause')

$stream.close()

$username = "rede\srv_hyperv"
$password = "ferr@ri386267"

$securePassword = ConvertTo-SecureString $password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential $username, $securePassword

Start-Process -Filepath "\\castro\apl\InfraTI\Cloudjus\console.bat" -Credential $credential -NoNewWindow

Start-Sleep 10

#Remove-Item \\castro\apl\InfraTI\Cloudjus\console.bat
#Remove-Item \\castro\apl\InfraTI\Cloudjus\core.ps1

pause