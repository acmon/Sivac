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



?><!--<script language="javascript" src="ajax.js"> </script>-->
<?php
Sessao::Singleton()->ChecarAcesso();

/*require_once('./tUnidadeDeSaude.php');
require_once('./tPreparacao.php');*/

$unidadeDeSaude = new UnidadeDeSaude;

$unidadeDeSaude->UsarBaseDeDados();

if(	 $unidadeDeSaude->VerificarSeEmitiuFormulario()
  && $unidadeDeSaude->ValidarFormulario('inserirUnidade') ) {

  	$unidadeDeSaude->SetarDados($_POST);

	$bairro_id = $unidadeDeSaude->RetornarIdBairro();

	if( $unidadeDeSaude->VerificarNaoDuplicidadeDeUnidade($bairro_id) ) {

		if($unidadeDeSaude->InserirUnidade($bairro_id)) {
			$unidadeDeSaude->ExibirMensagem("Unidade cadastrada com sucesso.");
			$_POST = array();
		}
	}

}
$unidadeDeSaude->ExibirFormularioInserirUnidade();
//$unidadeDeSaude->ExibirBotaoVoltar();

$unidadeDeSaude->ExibirMensagensDeErro();