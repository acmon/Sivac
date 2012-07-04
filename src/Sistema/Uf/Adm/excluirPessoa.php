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

// Verifica se o usu�rio tomou alguma dose:
if( strlen($pessoa->DataHoraUltimaVacinacao($usuarioId = $id)) > 5 )
{
	$queryStringInativar = $crip->Cifrar("pagina=Adm/inativarPessoa&usuarioId=$id");

    // Redirecionando para inativa��o com motivo:
    echo "<script>window.location='?$queryStringInativar'</script>";
}

// Se o usu�rio n�o tomou nenhuma vacina, ent�o pode realmente inativ�-lo:
elseif ($_POST && isset($id)) {
	
	if($pessoa->ExcluirPessoa($id)) {
	
		$qs = $crip->Cifrar('pagina=Adm/listarPessoa');
		
		echo "<script>window.location='?$qs'</script>";
	}
	
	else $pessoa->AdicionarMensagemDeErro('N�o foi possivel excluir o indiv�duo!');
}

$pessoa->ExibirFormularioExcluirPessoa($id);

$pessoa->ExibirMensagensDeErro();

