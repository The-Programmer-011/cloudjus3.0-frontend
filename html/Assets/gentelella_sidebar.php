    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col menu_fixed">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="/g_menu.php" class="site_title"><i class="fa fa-cloud"></i> <span>Cloud.Jus</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="<?php echo $profile_img; ?>" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2><?php echo $name; ?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3><span class="<?php echo $hv_badge_code; ?>"><?php echo $hypervisor; ?></span></h3>
                <ul class="nav side-menu">
                   <li><a><i class="<?php echo $hv_icon; ?>"></i> Ambiente <span class="fa fa-chevron-down"></span></a>
                     <ul class="nav child_menu">
                       <li><a href="/g_menu.php?hv=3">Prod: vCloud (HCI)</a></li>
                       <li><a href="/g_menu.php?hv=1">Dev-Q/A: VMware (Bladecenter)</a></li>
                       <li><a href="/g_menu.php?hv=2">Legacy: Hyper-V (Bladecenter)</a></li>
                     </ul>
                   </li>
                  <?php if($_SESSION['administrador'][$_SESSION['hv'] - 1] <= "3" && !$guest){ ?>
                    <li><a><i class="fa fa-cogs"></i> Gerenciar <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/<?php echo $hypervisor; ?>/g_CreateVM.php">Criar Nova Instância (VM)</a></li>
                        <li><a href="/<?php echo $hypervisor; ?>/g_CreateVMCluster.php">Criar Instâncias em Lote</a></li>
                        <li><a href="/<?php echo $hypervisor; ?>/g_DelVM.php">Excluir Instância</a></li>
                        <?php if ($_SESSION['administrador'][$_SESSION['hv']-1] == "1"){ ?>
                          <li><a href="/<?php echo $hypervisor; ?>/g_mass_delete.php">Excluir Instâncias em Lote</a></li>
                        <?php } ?>
                        <li><a href="/<?php echo $hypervisor; ?>/g_backup.php">Proteção de Dados</a></li>
                        <li><a href="/<?php echo $hypervisor; ?>/g_start_maintenance.php">Período de Manutenção</a></li>
                      </ul>
                    </li>
                  <?php } ?>
                  <?php if($_SESSION['administrador'][$_SESSION['hv'] - 1] <= "3" && !$guest){ ?>
                    <li><a><i class="fa fa-database"></i> Recursos <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/<?php echo $hypervisor; ?>/g_AddDisk.php">Adicionar volume de bloco</a></li>
                        <li><a href="/<?php echo $hypervisor; ?>/g_AltCore.php">Alterar núcleos de processamento</a></li>
                        <li><a href="/<?php echo $hypervisor; ?>/g_AltMem.php">Alterar memória RAM</a></li>
                      </ul>
                    </li>
                  <?php } ?>
                  <?php if($_SESSION['administrador'][$_SESSION['hv'] - 1] <= "5" && !$guest){ ?>
                    <li><a><i class="fa fa-power-off"></i> Estado <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/<?php echo $hypervisor; ?>/g_ShutdownOS.php">Shutdown S.O. VM (soft mode)</a></li>
                        <li><a href="/<?php echo $hypervisor; ?>/g_RestartOS.php">Restart S.O. VM (soft mode)</a></li>
                        <li><a href="/<?php echo $hypervisor; ?>/g_PwrON.php">Power ON VM</a></li>
                        <li><a href="/<?php echo $hypervisor; ?>/g_PwrOFF.php">Power OFF VM</a></li>
                      </ul>
                    </li>
                  <?php } ?>
                  <li><a><i class="fa fa-list"></i> Informações <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/<?php echo $hypervisor; ?>/g_SmallList.php">Instâncias</a></li>
                      <li><a href="/<?php echo $hypervisor; ?>/g_ListInfoVMs.php">Configuração</a></li>
                      <?php if($_SESSION['hv'] == "1"){?>
                      <li><a href="<?php echo $hypervisor; ?>/g_grafana.php">Grafana</a></li>
                      <?php } ?>
                      <!--<li><a href="/g_zabbix.php" target="_blank">Zabbix</a></li>-->
                      <li><a href="https://monitoramento.stf.jus.br" target="_blank">Zabbix</a></li>
                    </ul>
                  </li>
                  <?php if($_SESSION['administrador'][$_SESSION['hv'] - 1] <= "4" && !$guest){ ?>
                    <li><a><i class="fa fa-camera"></i> Snapshots <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/<?php echo $hypervisor; ?>/g_CreateSnap.php">Criar Snapshot</a></li>
                        <li><a href="/<?php echo $hypervisor; ?>/g_ListSnap.php">Gerenciar Snapshots</a></li>
                      </ul>
                    </li>
<!--                <?php } ?>
                    <li><a><i class="fa fa-caret-square-o-right"></i> Console <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/<?php echo $hypervisor; ?>/g_console.php">Fazer login no <?php echo $hypervisor;?></a></li>
                      </ul>
                    </li> -->
                    <?php if($_SESSION['administrador'][$_SESSION['hv'] - 1] == "1"){ ?>
                    <li><a><i class="fa fa-cog"></i> Administrador <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/manual_update.php">Atualização Completa</a></li>		
                        <li><a href="/fast_update.php">Atualização VM/Estado</a></li>
                      </ul>
                    </li>
                    <?php } ?>
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a href="/main.php" data-toggle="tooltip" data-placement="top" title="Versão Anterior">
                <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Recarregar página" onclick='window.location.reload(true);'>
                <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Abrir console" href="<?php echo $console; ?>">
                <span class="glyphicon glyphicon-modal-window" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="/logout.php">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>
