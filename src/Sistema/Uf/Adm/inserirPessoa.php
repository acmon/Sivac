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

// Essa verificação é para o array de dados que vem do vacinar quando não existe
// a pessoa buscada:
if( isset($dados) && count($dados) ) {
	
	$pessoa->ExibirFormularioInserirPessoa($dados);
	
	if( isset($estado_id, $cidade_id) ) {
		echo "<script>PesquisarCidades('$estado_id', $cidade_id)</script>";
	}
}

else $pessoa->ExibirFormularioInserirPessoa();

if( isset($inseriu) && $inseriu > 0) {
	
	
	// Se o cara veio de uma busca que não retornou resultado,
	// redireciona para vacina-lo:
	if( isset($dados) ) {

		$crip = new Criptografia();
		
		// Vai para o vacinar do cara diretamente:
		if( $vacina_id ) {
			
			$querystring = $crip->Cifrar("pagina=Adm/$irPara&usuario_id=$inseriu&vacina_id=$vacina_id&campanha_id=0");
		}
		
		// Vai para a busca que foi feita, pois uma campanha tem várias vacinas: 
		elseif ( $campanha_id ) {
			
			$querystring = $crip->Cifrar('pagina=Adm/listarPessoasVacinaveis');
		}
		
		// Se o cara veio de uma busca para vacinar anteriormente, então não
		// exibe a mensagem de "Inserido com sucesso", mas redireciona logo
		// para vacinar:
		if( $vacina_id || $campanha_id ) header("Location: ?$querystring");
	}
	
	$pessoa->ExibirMensagem('Registro inserido com sucesso!');
	
}
$pessoa->ExibirMensagensDeErro();