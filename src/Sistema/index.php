<?php

/*
 *  Sivac - Sistema Online de Vacinação     
 *  Copyright (C) 2012  IPPES  - Institituto de Pesquisa, Planejamento e Promoção da Educação e Saúde   
 *  www.sivac.com.br                     
 *  ippesaude@uol.com.br                   
 *                                                                    
 *  Este programa e software livre; você pode redistribui-lo e/ou     
 *  modificá-lo sob os termos da Licença Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a versão 2 da      
 *  Licença como (a seu critério) qualquer versão mais nova.          
 *                                                                    
 *  Este programa é distribuído na expectativa de ser útil, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia implícita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licença Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Você deve ter recebido uma copia da Licença Publica Geral GNU     
 *  junto com este programa; se não, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licença no diretório Sistema/licenca_en.txt 
 *                                Sistema/licenca_pt.txt 
 */



ob_start('ob_gzhandler');
require_once './Uf/autoload.php';

$conexao = new mysqli($_SERVER['HOST_SIVAC'],
                      $_SERVER['USER_SIVAC'],
                      $_SERVER['PASS_SIVAC'],
                      $_SERVER['BD_SIVAC'],
                      $_SERVER['PORT_SIVAC']);

echo "<pre>";
//print_r($_SERVER);
echo "</pre>";

//$conexao->select_db($_SERVER['BD']);

if( isset($_POST['estado']) && $_POST['estado'] != '0') {
	
	$estado_id = $_POST['estado'];
	
	$rs = $conexao->prepare('SELECT localbd, bd, loginbd, senhabd, nome 
		FROM `estadoqueusa`, `estado`
		
		WHERE estadoqueusa.Estado_id = estado.id
		AND Estado_id = ?')
		
		or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
	
	$rs->bind_param('s', $estado_id);
	
	$rs->bind_result($local, $banco, $login, $senha, $estado_nome);
	
	$rs->execute();
		
	$rs->store_result();
	
	$existe = $rs->num_rows;
	
	if($existe > 0) {
		
		$rs->fetch();
		
		Sessao::Singleton()->GravarSessaoBanco($local, $banco,
									$login, $senha, $estado_id, $estado_nome);
		
		$rs->free_result();
		
		header('Location: ./Uf/');
	}
	
	$rs->free_result();
	
	if($existe == 0) {
		
		echo '<script>
		alert("Não existem estados cadastrados para usar o sistema.")
		</sctipt>';
	}
	
	if($existe < 0) {
		
		echo '<script>
		alert("Algum erro ocorreu ao selecionar os estados que usam o sistema.")
		</sctipt>';
	}
	
	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<script type="text/javascript" src="./Uf/Js/operacoesComJanelas.js"> </script>
<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sivac</title>

<?php
if( strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'ie 6') !== false )
	echo '<link rel="stylesheet" type="text/css" href="./Uf/Css/cssSistemaDeVacinacaoEspecificoIE6.css" />';	
else
	echo '<link rel="stylesheet" type="text/css" href="./Uf/Css/cssSistemaDeVacinacao.css" />';
?>

</head>
<body>
	<div id="paginaToda2" style="width: 770px;">
		<div class="paginaInicial"
			style="background-image: url(./Uf/Imagens/pagInicial01.png)">
			
			<div style="margin-top: 35px; margin-left: 60px">

			<?php
			/*
			$sql = 'SELECT estado.id, estado.nome '
				 . 'FROM `estado`, `estadoqueusa` '
				 . 'WHERE estadoqueusa.Estado_id = estado.id '
				 . 'ORDER BY estado.id';
			*/	 
			$sql = 'SELECT estado.id, estado.nome FROM `estado`';
			
			$selecao = $conexao->prepare($sql) 
				or die(Bd::TratarErroSql($conexao->error . 'o erro tal', __FILE__, __LINE__));
			
			$selecao->bind_result($estado_id, $estado_nome);
			
			$selecao->execute();
			
			$selecao->store_result();
			
			if($selecao->num_rows) {
				
				echo '<h4 style=" color: #000; font-family:"Tahoma">Selecione o seu Estado</h4>';
				
				echo '<form id="formulario" name="formulario" method="post" '
					. "action='{$_SERVER['PHP_SELF']}'>";

				echo '<div style="width: 160px; height: 28px; clear: none; float: left">';
				echo '<select id="estado" name="estado"
					style="width: 160px; height: 28px; border: 1px solid #ABC">';
				echo '<option value="0">- selecione -</option>';
				
				while( $selecao->fetch() ) {
					echo "<option value='$estado_id'>$estado_nome</option>";
				}
				echo '</select></div>&nbsp;&nbsp;&nbsp;';
				
				echo "<button type='submit' 
						style='color: #14E; width: 110px; height: 28px'>";
				echo "<img src='./Uf/gerarIcone.php?imagem=ok' alt='Prosseguir'
				 		style='vertical-align: middle' />";
				echo 'Prosseguir';
				echo '</button>';
				
				echo '</form>';

			}
			else {
				echo 'Estados indisponíveis no momento. Aguarde alguns instantes
				      e entre no sistema novamente.';
			}
			$selecao->free_result();
			$conexao->close();
			?>
			</div>
			
			
			
			</div>
		<div class="paginaInicial"
			style="background-image: url(./Uf/Imagens/pagInicial02.png)"></div>
		<div class="paginaInicial"
			style="background-image: url(./Uf/Imagens/pagInicial03.png)"></div>
		<div class="paginaInicial"
			style="background-image: url(./Uf/Imagens/pagInicial04.png)"></div>
	</div>
	<?php Seguranca::VerificarJavaScript('paginaToda2')?>

    <!-- Conjunto para a exibição das mensagens de erro do preenchimento do form -->
    <div class="msgErro" id="containerDeMensagem">
        <div class="barraDeTituloMsgErro"  id="tituloMsgErro" title="Fechar"
             onmousemove="document.getElementById('containerDeMensagem').style.visibility = 'hidden';
             document.getElementById('tituloMsgErro').style.visibility = 'hidden';
             document.getElementById('mensagemDeErro').style.visibility = 'hidden';">
            &nbsp;Erro
        </div>
        <div class="corpoMsgErro" id="mensagemDeErro"></div>
    </div>
    <!-- Fim do conjunto para a exibição das mensagens de erro do form -->
    
</body>