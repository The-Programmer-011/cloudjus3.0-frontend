Como adcionar grupos no sistema CloudJus:

Estilo do arquivo:

G-ADM_VIRT_SAIN_ADM;SUPER_ADM;11
|                   |         ||-Digito correspondente ao nivel de acesso ao HyperV
|                   |         |-Digito correspondente ao nivel de acesso ao VMware
|                   |-Nome do grupo no sistema CloudJus
|-Nome do grupo na rede do STF


Codigo de nível de acesso:

1 - Super Adm (SEAIN)
2 - Grupo Adm (todas as funçoes)
3 - Recursos, Estado, Snapshots, criar máquina sob aprovação dos super adm
4 - Estado, Snapshots
5 - Estado
0 - (usuario) Apenas listas e console

==================================================================================================================================

CÓDIGO DOS HYPERVISORS:

Codigo presente na variável de sessão $_SESSION['hv']:

1 - VMware
2 - HyperV

==================================================================================================================================
vARIÁVEIS DE SESSAO:

$_SESSION['username'] - Nome do usuario
$_SESSION['administrador'] - Permissao do usuario (Ver codigo de nivel de acesso)
$_SESSION['name'] - Primeiro nome do usuario
$_SESSION['lastname'] - Sobrenome do usuario
$_SESSION['grupo'] - Nome do grupo que o usuario esta logado
$_SESSION['theme'] - estilo da interface
$_SESSION['hv'] - Codigo do hypervisor que o usuario esta no momento (Ver codigo dos hypervisors)
$_SESSION['denied'] - Essa variavel seta quando um usuario tenta acessar uma pagina que ele nao tem permissao de acessar

==================================================================================================================================

CODIGO DE STATUS DO SCRIPT (_status.txt):

0 - OFFLINE (script nao rodando, ou sem acesso ao servidor)
1 - Running (rodando normalmente)
2 - Busy (Script processando um pedido

_status_list.txt (VMware)

0 - Nao atualizando listas
1 - Atualizando listas


==================================================================================================================================