        #1.1
        $stream = [System.IO.StreamWriter] "C:\DATABASE\hyperv\Lists\hosts.txt"
        $SaidaConsole = Invoke-Expression -Command "Get-ClusterNode -Cluster paranhos"
        Write-Host $saidaConsole
        for($linha = 0; $linha -lt  $SaidaConsole.Length; $linha++) 
            {
            $m = Get-WmiObject win32_OperatingSystem -ComputerName $SaidaConsole[$linha] | %{$_.freephysicalmemory}
            [int]$freeMem = $m /1024 /1024
            $hostname = $saidaconsole[$linha]
            $stream.Write($linha)
            $stream.Write(" - ")
            $stream.Write($SaidaConsole[$linha])
            $stream.Write(" Memoria livre: ")
            $stream.Write($freeMem)
            $stream.Write(" GB)")
            $stream.WriteLine("")
                
                $stream2 = [System.IO.StreamWriter] "C:\DATABASE\hyperv\Lists\$linha templates.txt"
                $SaidaConsole2 = Invoke-Command -ComputerName $SaidaConsole[$linha] -ScriptBlock {Get-ChildItem c:\ClusterStorage\Volume1\Templates} 
                for($linha2 = 0; $linha2 -lt  $SaidaConsole2.Length; $linha2++) 
                    {$stream2.Write($linha2)
                    $stream2.Write(" - ")
                    $stream2.WriteLine($SaidaConsole2[$linha2])}
                $stream2.close()


                $stream2 = [System.IO.StreamWriter] "C:\DATABASE\hyperv\Lists\$linha disks.txt"
                $SaidaConsole2 = Invoke-Expression -Command "Get-ClusterSharedVolume -cluster paranhos"
                for($linha2 = 0; $linha2 -lt  $SaidaConsole2.Length; $linha2++) 
                    {
                    [String]$nomevolume = $SaidaConsole2[$linha2]            
                    [int]$espacolivre = (Get-ClusterSharedVolume -Cluster $SaidaConsole[$linha] -name $nomevolume | select -Property Name -ExpandProperty SharedVolumeInfo).Partition.FreeSpace  / 1024 /1024 /1024            
                    $stream2.Write($linha2)
                    $stream2.Write(" - ")
                    $stream2.Write($SaidaConsole2[$linha2])
                    $stream2.Write(" (Free: ")
                    $stream2.Write($espacolivre)
                    $stream2.Write(" GB)")
                    $stream2.WriteLine("")
                }
                $stream2.close()

            }
            $stream.close()