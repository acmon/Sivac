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
				// Se existe já uma característica exatamente igual, então reseleciona
				// os dados para só depois exibir o formulário:

						// TROCAR O NOME PARA "BUSCAR DADOS PARA EDICAO..."
				$vacina->SelecionarDadosParaEditarCaracteristicaDaVacina($caracDaVacinaId);
			}

		}
		
		
		$vacina->ExibirFormularioEditarCaracteristicaDaVacina();

	}

}

$vacina->ExibirMensagensDeErro();

