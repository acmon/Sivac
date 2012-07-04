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
			&& ValidarCampoSelect(this.unidadeDeSaude, 'unidade de saúde'))\">";
			
	echo '<div id="formAtivo">';
	$adm->ExibirFormularioEditarAdministradorDesabilitado($administradorId);
	echo '</div>';
	
	echo '</form>';
	
}
$adm->ExibirMensagensDeErro();
