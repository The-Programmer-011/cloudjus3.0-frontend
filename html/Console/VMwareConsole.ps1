#V.1.22
$ErrorActionPreference = "SilentlyContinue"
#Set-ExecutionPolicy -Scope CurrentUser Unrestricted
if(Get-ChildItem -Path $env:USERPROFILE\Downloads -Filter *VMwareConsole*){
    Remove-Item $env:USERPROFILE\Downloads\* -Include *VMwareConsole* -Force
}
#=====================================================Funcoes=====================================================

Function VMexiste ($vm) {
	$vm = $vm.ToUpper()
	If (Get-VM | where {$_.name -eq $vm}) {
		return $true
	} Else {
		return $false
	}
}

Function AbrirConsoleVM()
{
	If (!(VMexiste $vm)) {
		Do {$vm = Read-Host "`nNome da máquina virtual"} while ((VMexiste $vm) -eq $false)
    
		$ticket = (get-view (get-view serviceinstance).Content.SessionManager).AcquireCloneTicket()
		$vmid = (Get-VM $vm).Id.tostring().replace("VirtualMachine-","")
		[string]$ticket = (get-view (get-view serviceinstance).Content.SessionManager).AcquireCloneTicket()
		$VMRC = Get-ChildItem -Path "${Env:ProgramFiles(x86)}","${Env:ProgramFiles}" -Filter vmrc.exe -Recurse -ErrorAction SilentlyContinue | foreach {$_.FullName} | Select-Object -first 1
		Start-Process "$VMRC" vmrc://clone:$ticket@$VCENTER/?moid=$vmid
	}
	Else {
		$ticket = (get-view (get-view serviceinstance).Content.SessionManager).AcquireCloneTicket()
		$vmid = (Get-VM $vm).Id.tostring().replace("VirtualMachine-","")
		[string]$ticket = (get-view (get-view serviceinstance).Content.SessionManager).AcquireCloneTicket()
		$VMRC = Get-ChildItem -Path "${Env:ProgramFiles(x86)}","${Env:ProgramFiles}" -Filter vmrc.exe -Recurse -ErrorAction SilentlyContinue | foreach {$_.FullName} | Select-Object -first 1
		Start-Process "$VMRC" vmrc://clone:$ticket@$VCENTER/?moid=$vmid
	}
}

#=====================================================Setup=====================================================

#Teste de verificação de instalação do POWERCLI e VMRC nas versões corretas
Function FUN_VERIFICA_SOFTWARE ($NOME_SOFTWARE,$VERSAO_SOFTWARE,$CAMINHO_INSTALADOR_SOFTWARE) {
	$LISTA_SOFTWARES_INSTALADOS = Get-ItemProperty HKLM:\Software\Wow6432Node\Microsoft\Windows\CurrentVersion\Uninstall\*
	#If (!($LISTA_SOFTWARES_INSTALADOS | where {($_.displayname -eq "$NOME_SOFTWARE") -and ($_.DisplayVersion -like "$VERSAO_SOFTWARE")})) {
	If (!($LISTA_SOFTWARES_INSTALADOS | where {($_.displayname -eq "$NOME_SOFTWARE") -and ([int](($_.DisplayVersion).Split(".")[0]) -ge "$VERSAO_SOFTWARE")})) {
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

#FUN_VERIFICA_SOFTWARE "VMware PowerCLI" "6.5.*" "\\arquivos\bds\SERVIDORES\SOFTWARES\VMWARE.Virtualizacao\VMware vSphere CLI - Command Line Interface\VMware-PowerCLI-6.5.0-4624819.exe"
#FUN_VERIFICA_SOFTWARE "VMware Remote Console" "9.*" "\\arquivos\bds\SERVIDORES\SOFTWARES\VMWARE.Virtualizacao\VMRC\VMware-VMRC-9.0.0-4288332.msi"
FUN_VERIFICA_SOFTWARE "VMware PowerCLI" "6" "\\arquivos\bds\SERVIDORES\SOFTWARES\VMWARE.Virtualizacao\VMware vSphere CLI - Command Line Interface\VMware-PowerCLI-6.5.0-4624819.exe"
FUN_VERIFICA_SOFTWARE "VMware Remote Console" "9" "\\arquivos\bds\SERVIDORES\SOFTWARES\VMWARE.Virtualizacao\VMRC\VMware-VMRC-9.0.0-4288332.msi"


#Adiciona módulo no powershell para utilizar comandos do VMware PowerCLI caso já não esteja
If ((Get-Module -name VMware.VimAutomation.Core -ErrorAction SilentlyContinue) -eq $null) {
	Get-Module -ListAvailable VMware.VimAutomation.Core | Import-Module | Out-Null
}

#Configura o PowerCLI para não informar sobre o certificado não confiável do servidor vCenter
If (!(Get-PowerCLIConfiguration | where {($_.scope -eq "User") -and ($_.InvalidCertificateAction -eq "Ignore")}) ){
	Set-PowerCLIConfiguration -InvalidCertificateAction ignore -Scope User,Session -confirm:$false | Out-Null
}

#Define variáveis para o serevidor vCenter e realiza a conexão. Caso conectado a outro vCenter, é feita a desconexão e então conecta no vcenter informado
$VCENTER = "orizona.rede.stf.gov.br"

#Coleta as credenciais

$username = "rede\srv_hyperv"
$password = "ferr@ri386267"

$securePassword = ConvertTo-SecureString $password -AsPlainText -Force
$CREDENTIAL = New-Object System.Management.Automation.PSCredential $username, $securePassword

If ($global:DefaultVIServer -eq $null) {
	Connect-VIServer $VCENTER -Credential $CREDENTIAL | out-null
}
ElseIf ($VCENTER -notmatch $global:DefaultVIServer.Name) {
	Disconnect-VIServer $global:DefaultVIServer -Confirm:$false
	Connect-VIServer $VCENTER -Credential $CREDENTIAL | out-null
}

If ($global:DefaultVIServer -eq $null) {
	Exit
}

#=====================================================main=====================================================
Write-Host "CloudJus Console V. 1.22"
#instalar requisitos
Start-Process -FilePath "\\pontebranca\bds\SERVIDORES\SCRIPTS e UTILITARIOS\HyperV\requisitos\requisitos.bat" -Verb runAs -Wait

$user = "rede\"

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
    $vms_file = "\\britania\vmware\Groups\" + $vms_group_file + ".txt"
    $vms = Get-Content $vms_file
}

$vm_list = $vms

Do {$vm = Read-Host "`nNome da máquina virtual"} while ((VMexiste $vm) -eq $false)
if($adm -eq 1){
    Write-Host "Conectando..."
    AbrirConsoleVM
}
else{
$vms = $vm_list.Split(";")
$len = $vms.Count
for($count=0; $count -lt $len; $count++){
if($vm -eq $vms[$count]){
Write-Host "Conectando..."
    AbrirConsoleVM
    exit
    }
    }
    Write-Host "Voce nao tem permissao para acessar essa vm!"
    pause
}