#hyperv
#1.1
Function ajustatabela($size)
    {
    $c = ""
     if($prop.length -lt $size)
               {
               $c += $prop
               for($j = 0; $j -lt ($size - $prop.length); $j++){$c += " "}
               return $c + " | "
               }
           else 
                {
                $c += $prop.substring(0,$size)
                return $c + " | "
                }
    }

    "3" > "C:\DATABASE\hyperv\_list.txt"
    $stream = [System.IO.StreamWriter] "C:\DATABASE\hyperv\vms_names.txt"
    MODE CON:LINES=50 COLS=154
    Write-Host "---Updating VM List---"
    [int]$cont = 0
    $servidorhost2 = Invoke-Expression -Command "Get-ClusterNode -Cluster paranhos"
    foreach($servidorhost in $servidorhost2)
        {
        $vms = (Get-VM -ComputerName $servidorhost).Name   
        foreach($vm in $vms) {
           Write-Host $vm
           $TotalSize = 0     
           $TotalFileSize = 0
           $InfoVm = Get-VM -ComputerName $servidorhost -Name $vm    
           $stream.Write($vm + " ")
           $stream.WriteLine($InfoVm.State)            
           $cont++
           }     
        }
    MODE CON:LINES=25 COLS=80
    $stream.close()
    "0" > "C:\DATABASE\hyperv\_list.txt"