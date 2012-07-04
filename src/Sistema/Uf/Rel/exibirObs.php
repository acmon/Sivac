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

$crip = new Criptografia();

Sessao::Singleton()->ChecarAcesso();

$relatorio = new RelatorioCadernetaDeVacinacao();

$relatorio->usarBaseDeDados();

$qsUsuario = $crip->Cifrar("pagina=exibirObs&usuario_id=$usuario_id&vacina_id=$vacina_id&numerodadose=$numerodadose&tipo&usuario");
$qsVacina  = $crip->Cifrar("pagina=exibirObs&usuario_id=$usuario_id&vacina_id=$vacina_id&numerodadose=$numerodadose&tipo&vacina");


echo "<h3 align='center'>Observa��es</h3><p><h4>";
echo "<p style='padding-left:25px'> Usu�rio: ", $relatorio->RetornarCampoNome('usuario', $usuario_id), "</p>";

if(isset($vacina))	$numerodadose = false;
if(isset($usuario))	$numerodadose = false;

if(!isset($vacina))	$vacina_id = false;
else echo "<p style='padding-left:25px'> Vacina: ", $relatorio->RetornarCampoNome('vacina', $vacina_id), "</p>";

echo "<a href='?$qsUsuario' style='padding-left:25px' >Exibir todas as observa��es do indiv�duo</a>";
echo "<a href='?$qsVacina'  style='padding-left:25px'>Exibir observa��es da Vacina</a>";
echo "</h4>"; 


echo "<div style='padding-left:50px' >";
$relatorio->ListarObs($usuario_id, $vacina_id, $numerodadose);
echo '<br /></div>';