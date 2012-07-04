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