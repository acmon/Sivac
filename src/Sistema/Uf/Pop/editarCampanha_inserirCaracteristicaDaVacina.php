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

		// True aqui é para atualizar a janela do popup (se não passar atualiza a principal)
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