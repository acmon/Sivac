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

$pessoa = new PessoaVacinavel();

$pessoa->UsarBaseDeDados();

if(	 count($_POST) && $pessoa->ValidarFormulario('inserirPessoa') ) {
  	
	$pessoa->SetarDados($_POST);
	
	if( $pessoa->VerificarNaoDuplicidadeDePessoa() ) {
	
		// Inserir pessoa retorna o id da pessoa inserida:??????? ainda retorna?????
		if( $inseriu = $pessoa->InserirPessoa() ) $_POST = array();
	}
}

// Essa verifica��o � para o array de dados que vem do vacinar quando n�o existe
// a pessoa buscada:
if( isset($dados) && count($dados) ) {
	
	$pessoa->ExibirFormularioInserirPessoa($dados);
	
	if( isset($estado_id, $cidade_id) ) {
		echo "<script>PesquisarCidades('$estado_id', $cidade_id)</script>";
	}
}

else $pessoa->ExibirFormularioInserirPessoa();

if( isset($inseriu) && $inseriu > 0) {
	
	
	// Se o cara veio de uma busca que n�o retornou resultado,
	// redireciona para vacina-lo:
	if( isset($dados) ) {

		$crip = new Criptografia();
		
		// Vai para o vacinar do cara diretamente:
		if( $vacina_id ) {
			
			$querystring = $crip->Cifrar("pagina=Adm/$irPara&usuario_id=$inseriu&vacina_id=$vacina_id&campanha_id=0");
		}
		
		// Vai para a busca que foi feita, pois uma campanha tem v�rias vacinas: 
		elseif ( $campanha_id ) {
			
			$querystring = $crip->Cifrar('pagina=Adm/listarPessoasVacinaveis');
		}
		
		// Se o cara veio de uma busca para vacinar anteriormente, ent�o n�o
		// exibe a mensagem de "Inserido com sucesso", mas redireciona logo
		// para vacinar:
		if( $vacina_id || $campanha_id ) header("Location: ?$querystring");
	}
	
	$pessoa->ExibirMensagem('Registro inserido com sucesso!');
	
}
$pessoa->ExibirMensagensDeErro();