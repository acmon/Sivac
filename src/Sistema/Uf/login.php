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


date_default_timezone_set('America/Sao_Paulo');
$hora = date('G');

echo '<br /><br /><br /><br /><center><h3><strong>';

if    ($hora >= 6  && $hora < 12) echo 'Bom dia';
elseif($hora >= 12 && $hora < 18) echo 'Boa tarde';
elseif($hora >= 18)               echo 'Boa noite';
else                              echo 'Boa noite';

if( isset($_SESSION['nome']) && $_SESSION['nome'] != '' ) {
	
	echo ' ' . ucwords(strtolower($_SESSION['nome'])) . '!<br /> Seja bem-vindo ao Sivac.';
	
	echo '</strong></h3></center><br /><br /><br />';
}
else {
	
	echo '! Para acessar o Sivac digite seu login e senha.
		  </strong></h3></center>';
	
	echo '<form id="form1" name="form1" method="post" action="'
		. $_SERVER['REQUEST_URI']
		. '"><p>
			<div class="Login" align="center" style="color:#003d69;">
				<strong>Login de Usu�rio</strong>
			<br />
			<br />
			 	Login:
		    	<input name="login" type="text" id="login" maxlength="25"
		    		style="width:100px;"';
				 	if($_SESSION['tentativas'] > 2) echo'disabled="true"';
				 echo '/>
				 
	    	<br />
	     		Senha:
		     	<input name="senha" type="password" id="senha" maxlength="25"
		     		style="width:100px;"'; 
				if($_SESSION['tentativas'] > 2) echo'disabled="true"';
				echo '/>
	       	<br />
	    	<div align="center">';
	    	
	    		$botao = new Vacina();
	    		
				if($_SESSION['tentativas'] > 2) {
					
					$botao->ExibirBotoesDoFormulario('Entrar', false, 'ok', '#14E', true);
				}
				else {
					
					$botao->ExibirBotoesDoFormulario('Entrar');
				}
				
				echo '</div>
	    	</div>
		</p>
	</form>';
	
	echo '<script>document.form1.login.focus();</script>';
}

if( isset($_POST['login']) && isset($_POST['senha'])
	&& ($_POST['senha']) != '' && ($_POST['login']) != '' ) {
		
	echo '<br /><br />';

	Sessao::Singleton()->Logar();
	Sessao::Singleton()->VerificaTentativas();
}
