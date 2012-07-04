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

parse_str( $crip->Decifrar($_SERVER['QUERY_STRING']) ); // Cria a variável $id

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
		
		$icones[] = array('listar', 'Listar características da vacina');
		$icones[] = array('adicionar', 'Adicionar característica à vacina');
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
// Se id não foi informada ou se não existe, volta para a lista de campanhas:
else{
	unset($campanha);
	header('Location: listarCampanhas.php');
}
