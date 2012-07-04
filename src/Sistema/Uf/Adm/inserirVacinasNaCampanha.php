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
Sessao::Singleton()->ChecarAcesso();
/*require_once('./tCampanha.php');
require_once('./tCriptografia.php');*/

$camp = new Campanha();

$crip = new Criptografia();

parse_str( $crip->Decifrar($_SERVER['QUERY_STRING']) );

$camp->UsarBaseDeDados();

$idCampanha = false;

if( isset($campanha) ) $idCampanha = (int)$campanha;

if( $camp->VerificarSeIdDaCampanhaExiste($idCampanha) ) {

	if( $camp->VerificarSeEmitiuFormulario() ) {
		
		if( isset($_POST['vacinas'])) {

		 	$idCampanha = (int)$campanha;
		 	$camp->InserirVacinasNaCampanha($_POST['vacinas'], $idCampanha);
		}
		
		else {
			$camp->AdicionarMensagemDeErro('Marque alguma vacina antes de adicionar!');
		}

	}
	
	// O 2o. agr. é pra dizer qual é o arquivo de origem pra chamar o pop adequado
	$camp->ExibirFormularioInserirVacinasNaCampanha($idCampanha, 'inserirVacinasNaCampanha');
	
	$camp->ExibirBotaoVoltar('Finalizar', 'pagina=Adm/listarCampanhas', 'ok');
	$camp->ExibirMensagensDeErro();
	
	$icones[] = array('listar', 'Listar características da vacina');
	$icones[] = array('adicionar', 'Adicionar característica à vacina');
	$icones[] = array('detalhes', 'Exibir detalhes desta vacina');
	
	$legenda = new Legenda($icones);
	$legenda->ExibirLegenda();
}

// Se id não foi informada ou se não existe, volta para a lista de campanhas:
else {
	unset($camp);
	header('Location: listarCampanhas.php');
}
	 // Pimba...xP