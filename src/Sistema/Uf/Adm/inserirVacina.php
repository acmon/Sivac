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



?><script language="javascript" src="ajax.js"> </script>
<?php
require_once '../autoload.php';
Sessao::Singleton()->ChecarAcesso();

$vacina = new Vacina();

$vacina->ExibirFormularioInserirVacina();

if(  $vacina->VerificarSeEmitiuFormulario()
   && $vacina->ValidarFormulario('inserirVacina')) {

	$vacina->UsarBaseDeDados();

	$vacina->SetarDados($_POST);

	$vacina->InserirVacina();

	$Vacina_id = $vacina->SelecionarIdDaVacina();

	$aplicacoes = $vacina->IntervalosDasDoses();

	for($i=1; $i <= $aplicacoes; $i++) {

		if(isset($_POST["intervalo$i"]) && $_POST["intervalo$i"] != '') {

			$intervalo = $vacina->ConvertUnidTempParaDias($_POST["intervalo$i"],
		    			 $_POST["unidadeDeTempoDaDose$i"]);

			$atraso = $vacina->ConvertUnidTempParaDias($_POST["intervaloAtraso$i"],
		    			 $_POST["unidadeDeTempoDoAtraso$i"]);

			$vacina->InserirIntervaloDaDose($Vacina_id, $intervalo, $i, $atraso);
	 	}
	}
}

$vacina->ExibirMensagensDeErro();

?>