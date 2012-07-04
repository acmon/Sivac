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