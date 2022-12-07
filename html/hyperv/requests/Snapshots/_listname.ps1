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

    Write-Host "---Updating VM List---"
    $stream = [System.IO.StreamWriter] "_vms_names.txt"
    MODE CON:LINES=50 COLS=154
    [int]$cont = 0
    $servidorhost2 = Invoke-Expression -Command "Get-ClusterNode -Cluster paranhos"   
    foreach($servidorhost in $servidorhost2)
        {
        $vms = (Get-VM -ComputerName $servidorhost).Name   
        foreach($vm in $vms) {
           $TotalSize = 0     
           $TotalFileSize = 0
           $InfoVm = Get-VM -ComputerName $servidorhost -Name $vm    
           $stream.WriteLine($vm)           
           $cont++
           }     
        }
    MODE CON:LINES=25 COLS=80
    $stream.close()