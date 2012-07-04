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
$vacina = new Vacina();

$campanhaId = false;
if( isset($campanhaid)) $campanhaId = (int)$campanhaid;

$vacinaId = false;
if( isset($vacinaid)) $vacinaId = (int)$vacinaid;

$vacina->UsarBaseDeDados();

$vacina->BuscarNomeDaVacina($vacinaId);
$vacina->BuscarNomeDaCampanha($campanhaId);

if(	 $vacina->VerificarSeEmitiuFormulario()
	 && $vacina->ValidarFormulario('inserirCaracteristicaNaVacina')) {

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
		$vacina->SetarFaixaEtariaInicio(Vacina::IDADE_MINIMA,
													Vacina::DIAS);

		$vacina->SetarFaixaEtariaFim(Vacina::IDADE_MAXIMA,
										Vacina::ANOS);
	}

	$vacinaDaCampanhaId = $vacina->SelecionarVacinaDaCampanhaId($campanhaId , $vacinaId);

	$vacina->SetarVacinaDaCampanhaId($vacinaDaCampanhaId);
	$vacina->SetarEtnias($etnia);
	$vacina->SetarEstados($estado);

	if( $vacina->VerificarNaoDuplicidadeDeCaracteristica() ) {

		// True aqui � para atualizar a janela do popup (se n�o passar atualiza a principal)
		if($vacina->InserirCaracteristicaDaVacina($campanhaId, $vacinaDaCampanhaId)) {
			
			$cifrado = $crip->Cifrar("pagina=Adm/editarCampanha&id=$campanhaId");
						
			$vacina->AtualizarJanelas("../?$cifrado");
			
		}

	}
}
$vacina->ExibirFormularioInserirCaracteristica();
if( isset($caracteristicaInserida) && $caracteristicaInserida == 'ok') {

	$vacina->ExibirMensagem('Caracteristica editada com sucesso!');
}

$vacina->ExibirMensagensDeErro();