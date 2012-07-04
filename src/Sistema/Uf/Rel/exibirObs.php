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

$crip = new Criptografia();

Sessao::Singleton()->ChecarAcesso();

$relatorio = new RelatorioCadernetaDeVacinacao();

$relatorio->usarBaseDeDados();

$qsUsuario = $crip->Cifrar("pagina=exibirObs&usuario_id=$usuario_id&vacina_id=$vacina_id&numerodadose=$numerodadose&tipo&usuario");
$qsVacina  = $crip->Cifrar("pagina=exibirObs&usuario_id=$usuario_id&vacina_id=$vacina_id&numerodadose=$numerodadose&tipo&vacina");


echo "<h3 align='center'>Observações</h3><p><h4>";
echo "<p style='padding-left:25px'> Usuário: ", $relatorio->RetornarCampoNome('usuario', $usuario_id), "</p>";

if(isset($vacina))	$numerodadose = false;
if(isset($usuario))	$numerodadose = false;

if(!isset($vacina))	$vacina_id = false;
else echo "<p style='padding-left:25px'> Vacina: ", $relatorio->RetornarCampoNome('vacina', $vacina_id), "</p>";

echo "<a href='?$qsUsuario' style='padding-left:25px' >Exibir todas as observações do indivíduo</a>";
echo "<a href='?$qsVacina'  style='padding-left:25px'>Exibir observações da Vacina</a>";
echo "</h4>"; 


echo "<div style='padding-left:50px' >";
$relatorio->ListarObs($usuario_id, $vacina_id, $numerodadose);
echo '<br /></div>';