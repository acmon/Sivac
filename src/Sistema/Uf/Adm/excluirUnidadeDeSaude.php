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


Sessao::Singleton()->ChecarAcesso();
/*require_once('./tUnidadeDeSaude.php');
require_once('./tHtml.php');
require_once('./tCriptografia.php');*/

$crip = new Criptografia();

parse_str($crip->Decifrar($_SERVER['QUERY_STRING']) ); // Cria a vari�vel $id

if( isset($id) ) {

	$unidadeDeSaude = new UnidadeDeSaude();

	$unidadeDeSaude->UsarBaseDeDados();

	//$unidadeDeSaude->ExcluirUnidadeDeSaude($id);

	echo '<h3 align="center">Confirma��o para excluir</h3>';

	if( $unidadeDeSaude->VerificarSeEmitiuFormulario() ) {
		
		if ($unidadeDeSaude->ExcluirUnidadeDeSaude($id)) {
			
			$cifrado = $crip->Cifrar('pagina=Adm/listarUnidades');
			echo "<script>window.location ='?$cifrado'</script>";	
		}
		
		
	}

	$unidadeDeSaude->ExibirDadosDaUnidade($id);
	$unidadeDeSaude->ExibirFormularioExcluirUnidade();
	$unidadeDeSaude->ExibirBotaoVoltar('Unidades', 'pagina=Adm/listarUnidades');
	$unidadeDeSaude->ExibirMensagensDeErro();
}

// Se id n�o foi informada, volta imediatamente para a lista de campanhas:
else{
	unset($unidadeDeSaude);
	
}

?>