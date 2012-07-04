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


require_once('../autoload.php');
//Sessao::Singleton()->ChecarAcesso();

session_start();

$relatorio = new Relatorio();
$relatorio->usarBaseDeDados();
$relatorio->SetarOrgao('Prefeitura Municipal');
$relatorio->SetarResponsavel('Dr. Celso');
//$relatorio->ListarUsuariosVacinadosPorCidade(1,1);
//$relatorio->ListarUsuariosNaoVacinadosPorCidade(1,2); 
//$relatorio->ListarUsuariosVacinadosPorUnidade(1,1); 
//$relatorio->ListarUsuariosNaoVacinadosPorUnidade(2,1); 
//$relatorio->ListarUsuariosVacinadosPorAgente(3,1); 
//$relatorio->ListarUsuariosNaoVacinadosPorAgente(3,1);
//$relatorio->ListarVacinasPorUsuario(21); 
//$relatorio->SemVacinaPorCidade( 1, 1, '01/01/2009', '31/12/2040' ); 
//$relatorio->SemVacinaPorUnidade( 1, 1, '01/01/2009', '31/12/2040' ); 
//$relatorio->SemVacinaPorAcs( 1, 1, '01/01/2009', '31/12/2040' ); 
//$relatorio->CriarCadernetaDeVacinacao(26); 
//$relatorio->ListarUsuariosComDosesVencer('1','11/01/1945','31/12/2050'); 
//$relatorio->ListarUsuariosComDosesVencerPorCidade('9','3613','11/01/1945','31/12/2050');
$relatorio->ListarVacinasComDosesVencer('24','11/01/1945','31/12/2050');
//$relatorio->ComVacinaPorCidade( 3613, 1, '01/01/1945', '31/12/2040' ); 
//$relatorio->ComVacinaPorUnidade( 1, 1, '01/01/1945', '31/12/2040' ); 
//$relatorio->ComVacinaPorAcs( 1, 1, '01/01/1945', '31/12/2040' ); 
$relatorio->ExibirMensagensDeErro();
?>