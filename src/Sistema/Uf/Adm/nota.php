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

$nota = new Nota();

$nota->UsarBaseDeDados();

if( isset($mensagem) && $mensagem != '' ) {
		
		$nota->ExibirMensagem('Nota cadastrada com sucesso!');
		
}

$nota->ExibirSelectVacina();

if(isset( $_POST['vacina_id'] ) && ( $_POST['vacina_id'] <> 0 ) ){
	
	echo '<script>ListarNotaDaVacina(document.getElementById(\'vacina_id\').value, \'NotasDaVacina\', true)</script>';
	 
}

if( isset( $_POST['titulo'] ) && $_POST['titulo'] <> '' &&
    isset( $_POST['descricao'] ) && $_POST['descricao'] <> '' &&
	isset( $_POST['vacina_id'] )){
    
    	$nota->NovoRegistro();

    	if ($nota->SetarTitulo($_POST['titulo']) &&
    		$nota->SetarDescricao($_POST['descricao']) &&
    		$nota->SetarVacina($_POST['vacina_id']) &&
    		$nota->SetarDataDeCriacao() &&
   			$nota->SetarUsuario() &&
   			$nota->SetarAtivo(true)){
  		
    			$nota->InserirNota();
    			
    			$cifrado = $crip->Cifrar('pagina=Adm/nota&mensagem=confirmacao');
			 
				echo "<script>window.location = '?$cifrado'</script>";

    		}
		
    }
    
if(( isset( $_POST['titulo'] ) &&  $_POST['titulo'] == '') || 
    (isset( $_POST['descricao'] ) && $_POST['descricao'] == '' )){
    	
    	$nota->AdicionarMensagemDeErro('Preencha todos os campos');
    	
    }

$nota->ExibirMensagensDeErro();
