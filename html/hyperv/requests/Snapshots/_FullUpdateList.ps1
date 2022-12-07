Function ListarSnapshot()
{   Do {$vm = Read-Host "`nNome da máquina virtual"} while (-not(VMexiste))
    Get-ClusterNode -cluster paranhos | foreach {get-vm -ComputerName $_.name | get-vmsnapshot | where {$_.VMName -eq $vm}} | Out-String
    Write-Host "`nCOMANDO EXECUTADO COM SUCESSO!!!"
    pause
}

$file = Get-Content "c:\Scripts\Snapshots\_vms_names.txt"
$size = $file.Count
for($count=0; $count -lt $size; $count++){
    $vm = $file[$count]
    Write-Host $vm
    $filename = $vm + ".txt"
    $stream = [System.IO.StreamWriter] $filename
    $snap_info = Get-ClusterNode -cluster paranhos | foreach {get-vm -ComputerName $_.name | get-vmsnapshot | where {$_.VMName -eq $vm}} | Format-list | Out-String
    $stream.WriteLine($snap_info)
    #Get-ClusterNode -cluster paranhos | foreach {get-vm -ComputerName $_.name | get-vmsnapshot | where {$_.VMName -eq $vm}} | Out-String  > $filename
    $stream.close()
}