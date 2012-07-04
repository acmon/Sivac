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

$pessoa = new PessoaVacinavel();

$pessoa->UsarBaseDeDados();

$crip = new Criptografia();

parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));

if (isset($id)) {
	
	$pessoa->ExibirFormularioEditarPessoa($id);
	
	$botao = new Form();
	
	$botao->BotaoFechar();
}
	
if($_POST && isset($id) && $pessoa->ValidarFormulario('editarPessoa') ) {
	
	$pessoa->SetarDados($_POST);
	
	if( $pessoa->VerificarNaoDuplicidadeDePessoa($id) ) {
		
		if( $pessoa->EditarPessoa($id) ) {
			
			$qs = $crip->Cifrar("pagina=$opener&usuario_id=$id&vacina_id=$vacina_id&campanha_id=$campanha_id");

			echo "<script>
			
			window.opener.location = '../?$qs';
			window.close();
			
			</script>";
			
		}
	}
}

$pessoa->ExibirMensagensDeErro();
