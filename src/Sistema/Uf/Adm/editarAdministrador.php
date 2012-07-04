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



$adm = new Administrador();

$adm->UsarBaseDeDados();

if (isset($login)) {
	
	$administradorId = $adm->IdDoAdministradorAtual();
		
	if($_POST && isset($login) && $adm->ValidarFormulario('editarAdministrador') ) {
		
		$adm->SetarDados($_POST);
		
		if( $adm->VerificarNaoDuplicidadeDeAdministrador($administradorId) ) {
			
			if( $adm->EditarAdministrador($administradorId) ) {
				
				$adm->ExibirMensagem('Cadastro atualizado com sucesso!');
				
			}
		}
	}
	echo '<h3 align="center">Alterar dados do Administrador</h3>';
	$adm->ExibirConfirmarSenha();
	
	// Coloquei form aqui por causa do ajax (request_uri apontava pra uri do ajax)
	echo "<form id='f' name='f' method='post'
		action='{$_SERVER['REQUEST_URI']}'
		onsubmit=\"return (ValidarNome(this.nome, 'nome')
			&& ValidarLogin(this.login)
			&& ValidarSenha(this.senha)
			&& ValidarContraSenha(this.contrasenha, 'senha')
			&& ValidarCampoSelect(this.unidadeDeSaude, 'unidade de sa�de'))\">";
			
	echo '<div id="formAtivo">';
	$adm->ExibirFormularioEditarAdministradorDesabilitado($administradorId);
	echo '</div>';
	
	echo '</form>';
	
}
$adm->ExibirMensagensDeErro();
