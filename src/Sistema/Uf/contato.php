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
<div align="center">
<form name="form1" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>"
	onsubmit="return ( ValidarNome(this.nome, 'nome') 
	                && ValidarEmail(this.email)
	                && ValidarTextoLongo(this.mensagem)
	                && ValidarCampoSelect(this.estado, 'estado')
			&& ValidarCampoSelect(this.cidade, 'cidade')
			&& ValidarTelComum(this.telefone, true)
	                && ValidarNome(this.municipio, 'minicípio') )">
   	
	<h3 align="center">Contato</h3>
	
	<?php
		date_default_timezone_set('America/Sao_Paulo');
		$nome = $estado = $municipio = $email = $mensagem = '';
	
		if(isset($_POST['nome'])) {	
	
			$nome      = $_POST['nome'];
			$estado    = $_POST['estado'];
			$cidade = $_POST['cidade'];
			$telefone = $_POST['telefone'];
			$email     = $_POST['email'];
			
			$conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
					$_SESSION['senha']);
		
			$conexao->select_db($_SESSION['banco']);
				
			$resultado = $conexao->prepare('SELECT nome FROM `cidade` WHERE id = ?')
				or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));

			$resultado->bind_param('i', $cidade);
				
			$resultado->bind_result($nomecidade);
				
			$resultado->execute();

			$resultado->fetch();
			
			$resultado->free_result();		
			
			$mensagem  = str_replace(array('\\\r\\\n','\\\r','\\\n'),'<br />', $_POST['mensagem']);
			
			
			if($_POST['estado'] != '') {
				
				if( strlen($_POST['mensagem']) > 10 ) {
					
					if( $_POST['cidade'] != '' ) {
					
						$mail = new Email();
						
						$mail->NovoEmail();
						$mail->SetarDestinatario('contato@rgweb.com.br ,
												  pmonnerat@uol.com.br');
						
						$mail->SetarAssunto('Sivac - Contatos ' . date('d/m/Y H:i'));
						
						
						$mensagemFormatada = $nome . ' - ' . 'tel.:' . $telefone . '<br />';
						$mensagemFormatada .= $estado . ' - ' . $nomecidade . '<br />';
						$mensagemFormatada .= $mensagem;
						
						$mail->SetarMensagem($mensagemFormatada);
												
						$mail->SetarRemetente($email);
						$mail->SetarReplicacao($email);
						$mail->SetarCabecalho();
						
						$mail->Enviar();
						$_POST = array();
						$nome = $estado = $cidade = $email = $mensagem = $telefone = '';
						
						$confirmacao = new Vacina();
						$confirmacao->ExibirMensagem('E-mail enviado com sucesso');
						
						$confirmacao = null;
					
					}
					else {
				
						echo '<div align="center" class="mensagens"><b>
							  Digite uma cidade valida.</b></div>';
				
					}
				}
				else {
				
					echo '<div align="center" class="mensagens"><b>
						  Digite uma mensagem maior.</b></div>';
				}
			
			}
			else {
				
				echo '<div align="center" class="mensagens"><b>
					  Selecione um estado.</b></center></div>';
			}
	
		}	
	?>
	
	<!-- =================================================================== -->

	<p>
	<div class='CadastroEsq'><b>* Nome Completo:</b></div>
	<div class='CadastroDir'><input type="text" name="nome" id="nome" 
		value="<?php echo $nome;?>" style="width:300px;" maxlength="200" 
		onkeypress="FormatarNome(this, event)"
		onkeyup="FormatarNome(this, event)"			
		onkeydown="Mascara('NOME', this, event)"
		onblur="LimparString(this); ValidarNome(this, 'nome'); FormatarNome(this, event)"/></div>
    </p>
	
	<!-- =================================================================== -->

    <p>
        <div class='CadastroEsq'><b>* Estado UF:</b></div>
		<div class='CadastroDir'><select name="estado" id="estado" style="width:305px;
		margin-left:2px;" onblur="ValidarCampoSelect(this, 'estado', false)"
		onchange="PesquisarCidades(this.value)">
			<option value="0">- selecione -</option>
			<?php
				$conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
					$_SESSION['senha']);
		
				$conexao->select_db($_SESSION['banco']);
				
				$resultado = $conexao->prepare('SELECT id, nome FROM `estado`')
				or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));

				$resultado->bind_result($id, $nome);
				
				$resultado->execute();							

				while( $resultado->fetch() ) {

					echo "<option value='$id'>$nome</option>";
					
				}

				$resultado->free_result();
				$conexao->close();
			?>
		</select></div>
	
 	</p>
	
	<!-- =================================================================== -->

	<p>
	<div class='CadastroEsq'><b>* Cidade:</b></div>
		<div class='CadastroDir'><select name="cidade" id="cidade" style="width:305px; margin-left:2px;"
			onblur="ValidarCampoSelect(this, 'cidade', false)">
		</select></div>
	</p>
    <!-- =================================================================== -->
	<p>
		<div class='CadastroEsq'><b>Telefone:</b></div>
		<div class='CadastroDir'>
			<input type="text" name="telefone" id="telefone"
			style="width:150px; margin-left:2px;" maxlength="14"
			onblur="ValidarTelComum(this, true)"
			onchange="document.formulario.telefone.focus()"
			onkeydown="Mascara('TEL', this, event);"
			onkeypress="return Digitos(event, this);"/>
			<span id="TextoExemplos"><?php echo " Ex.: (21) 2555-0555 " ?></span>
			
		</div>
	</p>
		
    <!-- =================================================================== -->
	
	<p>
	<div class='CadastroEsq'><b>* E-mail:</b></div>
	<div class='CadastroDir'><input type="text" name="email" id="email" 
		value="<?php echo $email;?>" style="width:300px;" maxlength="200"
		onkeydown="Mascara('EMAIL', this, event)"
		onkeyup="Mascara('EMAIL', this, event)"
		onblur="LimparString(this); ValidarEmail(this)"	/></div>
    </p>

   	<!-- =================================================================== -->

    <p>
        <div class='CadastroEsq'><b>* Mensagem:</b></div>
		<div class='CadastroDir'><textarea name="mensagem" cols="70" rows="3" id="mensagem" 
		style="width:300px;"
		onblur="LimparString(this); ValidarTextoLongo(this)"><?php
		echo $mensagem;?></textarea></div>
	</p>
    
	<div align="center">
    	<?php
    		$botoes = new Vacina();
    		$botoes->ExibirBotoesDoFormulario('Enviar', 'Limpar');
    	
    	
    	?>
    </div>
	
</form>
</div>