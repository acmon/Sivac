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

//require_once '../autoload.php';

//Sessao::Singleton()->ChecarAcesso();

//$crip = new Criptografia(); // Já ta criado no index.

// parse_str($crip->Decifrar($_SERVER['QUERY_STRING']) ); no index

$vacina = new Vacina();

$vacina->UsarBaseDeDados();

$vacinaId = $campanhaId = false;



if( isset($campanhaid, $vacinaid) ) {

	$querystring = $crip->Cifrar("pagina=editarVacinasDaCampanha&id=$campanhaid&arquivo_origem=editarVacinasDaCampanha");
	
	$vacina->AtualizarJanelas("?$querystring");
	
	$vacina->ExibirListaDeCaracteristicas($vacinaid, $campanhaid, 'editarVacinasDaCampanha');

	$icones[] = array('editar', 'Alterar caracteristica da vacina');
	$icones[] = array('excluir', 'Excluir caracteristica da vacina');
	
	$legenda = new Legenda($icones);
	$legenda->ExibirLegenda();
	
	$vacina->ExibirMensagensDeErro();
}
else {
	$vacina->ExibirMensagem('Características não podem ser recuperadas');
}