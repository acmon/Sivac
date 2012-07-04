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
	
	// O 2o. agr. � pra dizer qual � o arquivo de origem pra chamar o pop adequado
	$camp->ExibirFormularioInserirVacinasNaCampanha($idCampanha, 'inserirVacinasNaCampanha');
	
	$camp->ExibirBotaoVoltar('Finalizar', 'pagina=Adm/listarCampanhas', 'ok');
	$camp->ExibirMensagensDeErro();
	
	$icones[] = array('listar', 'Listar caracter�sticas da vacina');
	$icones[] = array('adicionar', 'Adicionar caracter�stica � vacina');
	$icones[] = array('detalhes', 'Exibir detalhes desta vacina');
	
	$legenda = new Legenda($icones);
	$legenda->ExibirLegenda();
}

// Se id n�o foi informada ou se n�o existe, volta para a lista de campanhas:
else {
	unset($camp);
	header('Location: listarCampanhas.php');
}
	 // Pimba...xP