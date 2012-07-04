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



?>
<script language="javascript" src="./operacoesComJanelas.js"> </script>
<?php
//require_once '../autoload.php';
Sessao::Singleton()->ChecarAcesso();
/*require_once('./tCampanha.php');
require_once('./tPreparacao.php');
require_once('./tCriptografia.php');*/

$campanha = new Campanha();

$crip = new Criptografia();

parse_str( $crip->Decifrar($_SERVER['QUERY_STRING']) ); // Cria a vari�vel $id

$campanha->UsarBaseDeDados();


if( isset($id)) {

	if( $campanha->VerificarSeIdDaCampanhaExiste($id) ) {

		if(	 $campanha->VerificarSeEmitiuFormulario()
		  && $campanha->ValidarFormulario('editarCampanha') ) {

		  	$campanha->SetarDados($_POST);

		  	/*
			$campanha->SetarNome      ($_POST['nome']);
			$campanha->SetarDataInicio($_POST['dataInicio']);
			$campanha->SetarDataFim   ($_POST['dataFim']);
			$campanha->SetarObs       ($_POST['obs']);
			*/

			if( $campanha->VerificarNaoDuplicidadeDeCampanha($id) ) {
				$campanha->EditarCampanha($id);
			}
		}

		if( isset($excluir) ) {

			$campanhaId = $id;
			$vacinaId  = $excluir;

			$campanha->ExcluirVacinaDaCampanha($campanhaId, $vacinaId);
		}

		$campanha->ExibirFormularioEditarCampanha($id);
		
		$icones[] = array('listar', 'Listar caracter�sticas da vacina');
		$icones[] = array('adicionar', 'Adicionar caracter�stica � vacina');
		$icones[] = array('detalhes', 'Exibir detalhes desta vacina');
		$icones[] = array('excluir', 'Excluir a vacina desta campanha');
		
		
		////---------------------------- ????
		$botaoVoltar = new Form();
		echo '<center>';
		$botaoVoltar->BotaoVoltarHistorico();
		echo '</center>';
		////---------------------------- ?????
		
		
		
		$legenda = new Legenda($icones);
		$legenda->ExibirLegenda();
		
		//$campanha->ExibirBotaoVoltar();
		
		$campanha->ExibirMensagensDeErro();
	}
}
// Se id n�o foi informada ou se n�o existe, volta para a lista de campanhas:
else{
	unset($campanha);
	header('Location: listarCampanhas.php');
}
