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


require_once '../autoload.php';
/*require_once('./tVacina.php');
require_once('./tCriptografia.php');*/

// $crip = new Criptografia(); no index

//parse_str($crip->Decifrar($_SERVER['QUERY_STRING']) ); no index

$vacina = new Vacina();

$vacinaId = $campanhaId = false;

if( isset($campanhaid, $vacinaid) ) {
	$vacinaId   = $vacinaid;
	$campanhaId = $campanhaid;

	$vacina->UsarBaseDeDados();

	// Aqui teoricamente s� precisaria do configId:=====================
	$excluida = $vacina->ExlcuirCaracteristicaDaVacina($campanhaId, $vacinaId, $configId);

	if($excluida) {

		$end = $crip->Cifrar("pagina={$arquivo_origem}_listarCaracteristicaDaVacina&vacinaid=$vacinaId&campanhaid=$campanhaId");

		unset($vacina);
		header("Location: ./?$end");
	}
	else {
		$vacina->ExibirListaDeCaracteristicas($vacinaId, $campanhaId, $arquivo_origem);
	}
}
else {
	$vacina->ExibirMensagem('Caracter�stica inexistente');
}

$vacina->ExibirMensagensDeErro();

