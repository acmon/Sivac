<?php

/*
 *  Sivac - Sistema Online de Vacina��o     
 *  Copyright (C) 2012  IPPES  - Institituto de Pesquisa, Planejamento e Promo��o da Educa��o e Sa�de   
 *  www.sivac.com.br                     
 *  ippesaude@uol.com.br                   
 *                                                                    
 *  Este programa e software livre; voc� pode redistribui-lo e/ou     
 *  modific�-lo sob os termos da Licen�a Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a vers�o 2 da      
 *  Licen�a como (a seu crit�rio) qualquer vers�o mais nova.          
 *                                                                    
 *  Este programa � distribu�do na expectativa de ser �til, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia impl�cita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licen�a Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Voc� deve ter recebido uma copia da Licen�a Publica Geral GNU     
 *  junto com este programa; se n�o, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licen�a no diret�rio Sistema/licenca_en.txt 
 *                                Sistema/licenca_pt.txt 
 */


ob_start('ob_gzhandler');
require_once('./autoload.php');

Sessao::Singleton()->ChecarAcessoBanco();
$crip = new Criptografia();
$_POST = Seguranca::LimparPost();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sivac 2.1</title>

<?php
if( strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'ie 6') !== false )
	echo '<link rel="stylesheet" type="text/css" href="Css/cssSistemaDeVacinacaoEspecificoIE6.css" />';	
else
	echo '<link rel="stylesheet" type="text/css" href="Css/cssSistemaDeVacinacao.css" />';

?>
<link rel="stylesheet" type="text/css" href="Css/cssMenuVertical.css" />


<script type="text/javascript" src="./Js/ajax.js"> </script>
<script type="text/javascript" src="./Js/validarforms.js"> </script>
<script type="text/javascript" src="./Js/operacoesComJanelas.js"> </script>
<script type="text/javascript" src="./Js/operacoesComForms.js"> </script>

<!-- As linhas de coment�rio abaixo s�o para incluir o javascript que faz
funcionar o menu com 3 n�veis (necess�rio somente por causa do IE6) -->
<!--[if lte IE 6]>
<script src="./Js/menuDropDown.js" type="text/javascript"> </script>
<![endif]-->

<!--[if lte IE 7]>
<script src="./Js/DesabilitarCampanhaEspecificoIE.js" type="text/javascript"> </script>
<![endif]-->

</head>
<body>
	<div id="paginaToda">
	<div id="curvaTopoEsquerdo"></div>
		<div id="topo">
			<?php 
			require_once('topo.php');

			$pagina = '';
			parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
			?>
		</div>
		<div id="corpo">
			<div id="menuLateralEsquerdo">
				<!-- <div class="menuVertical">
					<?php // require("menuLateralEsquerdo.php"); ?>
					
				</div>
               -->
                <div style="width: 128px; height: 200px;">
                    <?php require("menu.php"); ?>
                </div> 
				<?php

                //##########
                //$qsTesteBanco = $crip->Cifrar("pagina=multiplicarBanco");
				//if($_SESSION['estado_banco'] == 'AM') echo "<a href='?$qsTesteBanco'>Teste Banco (APAGAR ISSO)</a>";

                //-->  Link para fazer com que o banco fique muito grande!
                //##########

                if( isset($_SESSION['nome']))
					Sessao::Singleton()->ExibirInformacoesDeAcesso();?>
			</div>
			<div id="conteudoDaPagina">

			<?php if(isset($_SESSION['nome'])) {

				$qsAjuda = $crip->Cifrar("mapeamento=$pagina");
				
				// S� atualizar o intervalo se o usu�rio est� conectado
				echo '<script>Intervalo()</script>';
				
				// Mostrar a ajuda somente para usu�rios conectados:
				echo "<div align='right' style='clear:none;float:right;'><a href='./Aj?$qsAjuda'
					target='_blank' title='Ajuda do Sivac'><img src='./Imagens/ajuda.jpg'
					border='0' alt='Ajuda do Sivac'></a></div>";
					
				/*echo "<div align='right' style='clear:none;float:right;'><a href='./Aj/'
					target='_blank' title='Ajuda do Sivac'><img src='./Imagens/ajuda.jpg'
					border='0' alt='Ajuda do Sivac'></a></div>";*/
					
				// Exibir icones campanhas
                if(Sessao::Permissao('INDIVIDUOS_VACINAR')) {
                    $campanha = new Campanha();
                    $campanha->UsarBaseDeDados();
                    $campanha->SelecionarIconesUltimasCampanhas();
                }
			}
				//Sessao::Singleton()->MostrarUsuario();
				//echo "<pre>"; print_r($_SESSION);echo "</pre>";
				
				if (isset($pagina) && strlen($pagina) > 2) {
					
					Sessao::Singleton()->MostrarUnidade();

					//  Apagar isso depois (linha toda abaixo)...
					//echo "<var>Querystring:</var><code>
					//	{$crip->Decifrar($_SERVER['QUERY_STRING'])}</code>";
					//????
					
					Depurador::Querystring($crip->Decifrar($_SERVER['QUERY_STRING']));

					if(file_exists("$pagina.php")) {
						
						require_once("$pagina.php");
					}
					else {
						echo '<div align="center"><strong>
							  P�gina Inexistente</strong></div>';
					}
				}
				else {
					require_once("inicio.php");
				}

                //----------------
                // #TODOS - N�o apagar (para atualizar as datas e idades no banco)
                // descomente estas cinco linhas abaixo:
                /*
                $data = new Data();
                $data->UsarBaseDeDados();
                $data->AtualizarIdadeAnoMesDiaDaTabela('usuariovacinado', 1);
                $data->AtualizarIdadeAnoMesDiaDaTabela('usuariovacinadocampanha', 1);
                $data->AtualizarProximaDoseNoBanco(1);
                */
                //----------------
				?>
			</div>
		</div>
		<div id="rodape">
			<?php require_once('rodape.php')?>
		</div>
	</div>
	<?php Seguranca::VerificarJavaScript('paginaToda')?>
	<script>SetarFocoPrimeiroInput();</script>

    <!-- Conjunto para a exibi��o das mensagens de erro do preenchimento do form -->
    <div class="msgErro" id="containerDeMensagem">
        <div class="barraDeTituloMsgErro"  id="tituloMsgErro" title="Fechar"
             onmousemove="document.getElementById('containerDeMensagem').style.visibility = 'hidden';
             document.getElementById('tituloMsgErro').style.visibility = 'hidden';
             document.getElementById('mensagemDeErro').style.visibility = 'hidden';">
            &nbsp;Erro
        </div>
        <div class="corpoMsgErro" id="mensagemDeErro"></div>
    </div>
    <!-- Fim do conjunto para a exibi��o das mensagens de erro do form -->

</body>
</html>
