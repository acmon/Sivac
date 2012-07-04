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


//require_once '../autoload.php';
Sessao::Singleton()->ChecarAcesso();
/*require_once('./tCampanha.php');
require_once('./tCriptografia.php');*/

// $crip = new Criptografia(); no index

// parse_str($crip->Decifrar($_SERVER['QUERY_STRING']) ); no index


if( isset($id) ) {

	$querystring = $crip->Cifrar("pagina=Adm/editarCampanha&id=$id&arquivo_origem=$arquivo_origem");

	$campanha = new Campanha();
	
	$uri = $campanha->Url() . "/Uf?$querystring"; 
	
	$campanha->UsarBaseDeDados();

	if ($campanha->VerificarSeEmitiuFormulario()) {
		
		$arrayDeVacinas = isset($_POST['vacinas']) ? $_POST['vacinas'] : array();
		$campanha->ExibirFormularioEditarVacinaDaCampanha($arrayDeVacinas, $id, $arquivo_origem);
		
		$campanha->AtualizarJanelas($uri);
		
		echo '<script>window.close()</script>';
	}
	else {
		$campanha->ExibirFormularioEditarVacinaDaCampanha(array(), $id, $arquivo_origem);
		
		$campanha->AtualizarJanelas($uri);
	}
	
		$icones[] = array('listar', 'Listar caracter�sticas da vacina');
		$icones[] = array('adicionar', 'Adicionar caracter�stica � vacina');
		$icones[] = array('detalhes', 'Exibir detalhes desta vacina');
		
		$legenda = new Legenda($icones);
		$legenda->ExibirLegenda();
		

}

$campanha->ExibirMensagensDeErro();