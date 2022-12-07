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

    "4" > "C:\DATABASE\hyperv\_list.txt"
    $stream = [System.IO.StreamWriter] "C:\DATABASE\hyperv\vms.txt"
    MODE CON:LINES=50 COLS=154
    Write-Host "---Updating VM List---"
    $stream.WriteLine(" --------------------------------------------------------------------------------------------------------------------------------------------------------")
    $stream.WriteLine(" |  Maquina Virtual     |    Host    |    Nr   |  %   |   Mem. |  Mem.  |  Mem.  | Mem.|   Mem. |   Mem.  |VHD Aloc.|VHD Usado| Gen | HA  | Uptime | On |")
    $stream.WriteLine(" |                      |            | Nucleos | CPU  |  Assoc | Demand |Startup | Din |   Min  |   Max   |   (GB)  |  (GB)   |     |     | (Dias) | Off|")
    $stream.WriteLine(" +----------------------+------------+---------+------+--------+--------+--------+-----+--------+---------+---------+---------+-----+-----+--------+----+")
    [int]$cont = 0
    $servidorhost2 = Invoke-Expression -Command "Get-ClusterNode -Cluster paranhos"   
    foreach($servidorhost in $servidorhost2)
        {
        $vms = (Get-VM -ComputerName $servidorhost).Name   
        foreach($vm in $vms) {
           $TotalSize = 0     
           $TotalFileSize = 0
           $InfoVm = Get-VM -ComputerName $servidorhost -Name $vm
           $InfoVHDS =(get-vhd -ComputerName $servidorhost -VMId (Get-VM -ComputerName $servidorhost -Name $vm).vmid)
           $vhdsSize = $InfoVHDS.Size
           $vhdsFileSize = $InfoVHDS.FileSize
           foreach($item in $vhdsSize){$TotalSize += $item}           
           foreach($item in $vhdsFileSize){$TotalFileSize += $item}
           $linha = " | "      
           $prop = $vm             
           $linha += ajustatabela(20)                             
           $prop = $InfoVm.ComputerName
           $linha += ajustatabela(10)
           $prop = $InfoVm.ProcessorCount
           $linha += ajustatabela(7)             
           $prop =  $InfoVm.CPUUsage           
           $linha += ajustatabela(4)                      
           [string]$prop = $InfoVm.MemoryAssigned /1024 /1024              
           $linha += ajustatabela(6)            
           [string]$prop = $InfoVm.MemoryDemand /1024 /1024           
           $linha += ajustatabela(6)             
           [string]$prop = $InfoVm.MemoryStartup /1024 /1024          
           $linha += ajustatabela(6)            
           $prop = $InfoVm.DynamicMemoryEnabled
           if($prop -eq $true) {$linha += "Sim | "}
           else {$linha += "Nao | "}                     
          [string]$prop = $InfoVm.MemoryMinimum /1024 /1024
           if($InfoVm.DynamicMemoryEnabled -eq $true){$linha += ajustatabela(6)}
           else {$linha += "0      | " }           
          [string]$prop = $InfoVm.MemoryMaximum /1024 /1024
           if($InfoVm.DynamicMemoryEnabled -eq $true){$linha += ajustatabela(7)}
           else {$linha += "0       | " }                      
           [string]$prop = $TotalSize /1024 /1024 /1024
           $linha += ajustatabela(7)           
           [string]$prop = $TotalFileSize /1024 /1024 /1024
           $linha += ajustatabela(7)
           $prop = $InfoVm.Generation
           $linha += ajustatabela(3)           
           $prop = $InfoVm.IsClustered
           if($prop -eq $true) {$linha += "Sim | "}
           else {$linha += "Nao | "}                  
           $prop = $InfoVm.Uptime.Days           
           $linha += ajustatabela(6)              
           [string]$prop = $InfoVm.State
           if($prop -eq "Running") {$linha += "On |"}
           Else {$linha += "Off|"}
           Write-Host $linha                           
           $stream.WriteLine($linha)              
           $stream.WriteLine(" +----------------------+------------+---------+------+--------+--------+--------+-----+--------+---------+---------+---------+-----+-----+--------+----+")
           $cont++           
           }     
        }
    MODE CON:LINES=25 COLS=80
    $stream.close()
    "0" > "C:\DATABASE\hyperv\_list.txt"