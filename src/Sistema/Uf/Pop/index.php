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
require_once('../autoload.php');

Sessao::Singleton()->ChecarAcessoBanco();
$crip = new Criptografia();
$_POST = Seguranca::LimparPost();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Sivac</title>

<?php
if( strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'ie 6') !== false )
	echo '<link rel="stylesheet" type="text/css" href="../Css/cssSistemaDeVacinacaoEspecificoIE6.css" />';	
else
	echo '<link rel="stylesheet" type="text/css" href="../Css/cssSistemaDeVacinacao.css" />';
?>

<script type="text/javascript" src="../Js/ajax.js"> </script>
<script type="text/javascript" src="../Js/validarforms.js"> </script>
<script type="text/javascript" src="../Js/operacoesComJanelas.js"> </script>
<script type="text/javascript" src="../Js/operacoesComForms.js"> </script>

</head>
<body style="background-image: none; margin: 8px; min-width: 650px">
<div id="cabecalhoPopUps">&nbsp;</div>
<div id="conteudoPopUps">
<?php

parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
Depurador::Querystring($crip->Decifrar($_SERVER['QUERY_STRING']));

if (isset($pagina) && file_exists("$pagina.php") ) {

	 // Apagar isso depois (linha toda abaixo)...
	//echo "<var>Querystring:</var><code>
		//{$crip->Decifrar($_SERVER['QUERY_STRING'])}</code>";
	///???????
	
	require_once("$pagina.php");
}
else {
	echo '<div align="center"><strong>Página Inexistente</strong></div>';
}
?>
<div id="rodape" style="width: 100%">
	<?php require_once('../rodape.php')?>
</div>
</div>
<?php Seguranca::VerificarJavaScript('conteudoPopUps')?>

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
</html>