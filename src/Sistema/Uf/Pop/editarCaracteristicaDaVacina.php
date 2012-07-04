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

////require_once '../autoload.php';
////Sessao::Singleton()->ChecarAcesso();
/*require_once('tVacina.php');
require_once('./tCriptografia.php');*/

//$crip = new Criptografia(); no index

//parse_str($crip->Decifrar($_SERVER['QUERY_STRING']) ); no index

$vacina = new Vacina();

$vacina->UsarBaseDeDados();

$caracDaVacinaId = false;

if( isset($campanhaid, $vacinaid, $configId) ) {

	$vacinaId   =		(int)$vacinaid;
	$campanhaId =		(int)$campanhaid;
	$caracDaVacinaId =	(int)$configId;

	if( $vacina->VerificarSeIdDaCaracteristicaExiste($caracDaVacinaId) ) {

		// TROCAR O NOME PARA "BUSCAR DADOS PARA EDICAO..."
		$vacina->SelecionarDadosParaEditarCaracteristicaDaVacina($caracDaVacinaId);

		if(	 $vacina->VerificarSeEmitiuFormulario()
		 && $vacina->ValidarFormulario('editarCaracteristicaNaVacina') ) {

			$sexo = Array();
			if(isset($_POST['sexo'])) $sexo = $_POST['sexo'];

			$etnia = Array();
			if(isset($_POST['etnia'])) $etnia = $_POST['etnia'];

			$estado = Array();
			if(isset($_POST['estado'])) $estado = $_POST['estado'];

			$vacina->SetarSexo($sexo);

			if( isset($_POST['apenasFaixaEtaria'])) {

				$vacina->SetarFaixaEtariaInicio($_POST['faixaetariainicio'],$_POST['unidadedetempoinicial']);
				$vacina->SetarFaixaEtariaFim($_POST['faixaetariafim'],$_POST['unidadedetempofinal']);
			}
			else {
				$vacina->SetarFaixaEtariaInicio(1, Vacina::DIAS);
				$vacina->SetarFaixaEtariaFim(Vacina::IDADE_MAXIMA,
												Vacina::ANOS);
			}

			$vacina->SetarEtnias($etnia);
			$vacina->SetarEstados($estado);
			if( $vacina->VerificarNaoDuplicidadeDeCaracteristica($caracDaVacinaId) ) {

				if( $vacina->EditarCaracteristicaDaVacina($caracDaVacinaId)) {

					$querystring = $crip->Cifrar("pagina={$arquivo_origem}_listarCaracteristicaDaVacina&vacinaid=$vacinaId&campanhaid=$campanhaId");
	
					unset($vacina);
	
					header("Location: ./?$querystring");
				}

			}
			else {
				// Se existe j� uma caracter�stica exatamente igual, ent�o reseleciona
				// os dados para s� depois exibir o formul�rio:

						// TROCAR O NOME PARA "BUSCAR DADOS PARA EDICAO..."
				$vacina->SelecionarDadosParaEditarCaracteristicaDaVacina($caracDaVacinaId);
			}

		}
		
		
		$vacina->ExibirFormularioEditarCaracteristicaDaVacina();

	}

}

$vacina->ExibirMensagensDeErro();

