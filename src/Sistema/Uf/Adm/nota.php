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
