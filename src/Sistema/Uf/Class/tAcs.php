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


//require('tAgenteImunizador.php');

/**
 * Pessoa: Classe mãe que representa uma pessoa.
 *
 * Esta classe trata de uma pessoa vacinada ou não. Seta, Recupera, Lista , Edita ,
 * Exibe, Seleciona e Valida uma pessoa ou seus dados. Por aqui pode-se acessar
 * todos os dados pessoais de uma  determinada pessoa e usá-los em outras classes
 * ou arquivos que necessitem dessas informações.
 *
 *
 * @package Sivac/Class
 *
 * @author Maykon Monnerat (maykon_ttd@hotmail.com), v 1.0, 2008-08-27 15:43
 *
 * @copyright 2008 
 *
 */
 class Acs extends Pessoa 
{
	protected $unidadeDeSaude_id;      //String
	
	
	//--------------------------------------------------------------------------
	/**
	 * Seta a uniadde de saúde do agente.
	 *
	 * @param string $unidadedesaude
	 */
	public function SetarUnidadeDeSaude($unidadeDeSaude_id)
	{
		$this->unidadeDeSaude_id = $unidadeDeSaude_id;
	}
	//--------------------------------------------------------------------------
	public function SetarDados($post)
	{
		
		$clean = Preparacao::GerarArrayLimpo($post, $this->conexao);

		//print_r($clean);

		parent::SetarDados($clean);
		
		if( isset($clean['unidadeDeSaude']) && $clean['unidadeDeSaude'] > 0)
			$this->SetarUnidadeDeSaude($clean['unidadeDeSaude']);
		
		// Se o administrador não é nível 1000, então o $_POST['unidadeDeSaude']
		// não existe, pois ele não pode escolher a unidade (tem que ser a dele)
		else $this->SetarUnidadeDeSaude($_SESSION['unidadeDeSaude_id']);
	}
	//-------------------------------------------------------------------------
	//////////////////////////////// RETORNAR /////////////////////////////////

	
	//--------------------------------------------------------------------------
	/**
	 * Retorna o nome da profissao de determinada pessoa.
	 *
	 * @return string
	 */
	public function UnidadeDeSaude()
	{
		return $this->unidadeDeSaude_id;
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Insere uma nova pessoa.
	 *
	 */
	public function InserirAcs()
	{

		$inserir = $this->conexao->prepare("INSERT INTO `acs` (id, Ddd_id,
			UnidadeDeSaude_id, nome, nascimento, telefone, cpf, email, ativo)
			VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, 1)")
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$data = new Data();

		$ddd        		= $this->Ddd();
		$unidadeDeSaude_id  = $this->UnidadeDeSaude();
		$nome       		= $this->Nome();		
		$nascimento 		= $data->InverterData($this->nascimento);
		$telefone   		= Preparacao::RemoverSimbolos($this->Telefone());
		$cpf        		= Preparacao::RemoverSimbolos($this->Cpf());
		$email				= $this->Email();
		
		$inserir->bind_param('sisssss',$ddd, $unidadeDeSaude_id, $nome, 
						$nascimento, $telefone, $cpf, $email);			
		
		$inserir->execute();
		
		$sucesso = $inserir->affected_rows;

		if($sucesso > 0) {
			
			return true;
		}
		
		return false;
	}

	///////////////////////////////// EDITAR //////////////////////////////////
	/**
	 * Edita os dados de determinada pessoa.
	 *
	 */
	public function EditarAcs($id)
	{
		
		$data = new Data();
		
		$ddd        		= $this->Ddd();
		$unidadeDeSaude_id  = $this->UnidadeDeSaude();
		$nome       		= $this->Nome();
		$nascimento 		= $data->InverterData($this->nascimento);
		$telefone   		= Preparacao::RemoverSimbolos($this->Telefone());
		$cpf        		= Preparacao::RemoverSimbolos($this->Cpf());
		$email				= $this->Email();
		$cep 				= Preparacao::RemoverSimbolos($this->Cep());


        //die("$ddd, $unidadeDeSaude_id, $nome, $nascimento,
		//								   $telefone, $cpf, $email, $id");

		$atualizar = $this->conexao->prepare('UPDATE `acs` SET 
			Ddd_id = ?, UnidadeDeSaude_id = ?, nome = ?,
			nascimento = ?, telefone = ?, cpf = ?, email = ?, ativo = 1
			WHERE id = ?') or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$atualizar->bind_param('sisssssi', $ddd, $unidadeDeSaude_id, $nome, $nascimento,
										   $telefone, $cpf, $email, $id);

		$atualizar->execute();

		$sucesso = $atualizar->affected_rows;
	
		if($sucesso > 0) {

			return true;
		}
		if($sucesso == 0) {

			$this->AdicionarMensagemDeErro("Atualização não efetuada. Nenhuma
				alteração parece ter sido feita para $nome.");
				
			return false;
		}
		if($sucesso < 0) {
			
			$this->AdicionarMensagemDeErro("Algum erro ocorreu ao atualizar os
				dados de $nome.");
				
			return false;
		}
	}

	///////////////////////////////// EXCLUIR /////////////////////////////////
	/**
	 * Exclui uma pessoa.
	 *
	 */
	public function ExcluirAcs($id)
	{

		$excluir = $this->conexao->prepare("UPDATE `acs` SET `ativo`= 0 WHERE `id`= ?")
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$excluir->bind_param('i',$id);

		$excluir->execute();

		$excluiu = $excluir->affected_rows;

		$excluir->close();

		if ($excluiu){

			return true;

		}

		return false;
	}
	///////////////////////////////// EXIBIR //////////////////////////////////

	public function ExibirFormularioEditarAcs($id)
	{

		$dados = $this->SelecionarDadosAcs($id);

		list($ddd_id, $unidadeDeSaude_id, $nome, $nascimento, $email, $telefone, $cpf) = $dados;

		$crip = new Criptografia();

		$desc = $crip->Decifrar($_SERVER['QUERY_STRING']);

		$end = $crip->Cifrar("$desc&id=$id");

		?>
		<h3 align="center">Alterar dados do ACS</h3>
		<form id="formulario" name="formulario" method="post"
		action="?<?php echo $end; ?>"
			onsubmit="return (ValidarNome(this.nome, 'nome')
					&& ValidarData(this.datadenasc)
					&& ValidarCampoSelect(this.ddd, 'DDD', true)
					&& ValidarTelLocal(this.telefone, true)
					&& ValidarCpf(this.cpf, true)
					&& ValidarEmail(this.email, true)
					&& ValidarCampoSelect(this.unidadeDeSaude, 'unidade de saúde'))">

			<p>

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Nome:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="nome" id=nome value="<?php
						if ( isset( $_POST['nome'] ) ) {
							echo Html::FormatarMaiusculasMinusculas($_POST['nome']);
						} else {
							echo Html::FormatarMaiusculasMinusculas($nome);
						}
						?>" maxlength="100"
						style="width:300px;" onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this); ValidarNome(this, 'nome');
						FormatarNome(this, event)"
					/>
				</div>
				<br />

				<!-- ############################################################### -->

				
				<div class='CadastroEsq'>
					*Data de Nascimento:
				</div>
				<div class='CadastroDir'>
				<?php

				$data = new Data();

				?>
					<input type="text" name="datadenasc" value="<?php
						echo $data->InverterData($nascimento); ?>"
						maxlength="10" <?php echo @$desabilitarCampo; ?>
						onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
				        onkeyup="return Mascara('DATA', this, event);"
						onblur="ValidarData(this)"/>
					<span id="TextoExemplos">
						<?php if(@$desabilitarCampo == '') {

							echo 'Ex.: 01-01-1980';
						}
						else {
							echo 'A data não pode ser alterada!';
						}
						?>
					</span>
				</div>

				<!-- ############################################################### -->

				<br />
				<div class='CadastroEsq'>
					DDD:
				</div>
				<div class='CadastroDir'>
					<select name="ddd" id="ddd" style="width:50px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'DDD', true)"
						onchange="document.formulario.telefone.focus()">
						<option value=""></option>
						<?php
							$this->SelecionarDdd($ddd_id);
						?>
					</select>
					Telefone: <input type="text" name="telefone" id="telefone"
						value="<?php if($telefone != '' && $telefone != '00000000') {
							echo Preparacao::InserirSimbolos($telefone, 'TEL');
						} ?>" maxlength="9" onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('TELLOCAL', this, event);"
						onblur="ValidarTelLocal(this, true)"/>
					<span id="TextoExemplos">
						<?php echo " Ex.: 2222-2222 " ?>
					</span>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					CPF:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="cpf" id="cpf" value="<?php if($cpf != 0 ) {
					echo Preparacao::InserirSimbolos($cpf, 'CPF'); }?>"
						maxlength="14" onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('CPF',this,event);"
						onblur="ValidarCpf(this, true);"
					/>
					<span id="TextoExemplos">
						<?php echo " Ex.: 999.999.999-99 " ?>
					</span>
				</div>
				<br />

				<!-- ############################################################### -->
				<div class='CadastroEsq'>
					E-mail:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="email" id="email" value="<?php if(isset($email)) {
					echo $email; }?>"	maxlength="70" size="40" 
						onkeydown="Mascara('EMAIL',this,event);"
						onblur="ValidarEmail(this, true);" />
				</div>
				<br />

				<!-- ############################################################### -->
				
				<?php if( Sessao::Permissao('ACS_EDITAR') == 1 ) { ?>
				<div class='CadastroEsq'>
					*Unidade de Saúde:
				</div>
				<div class='CadastroDir'>
					<select name="unidadeDeSaude" id="unidadeDeSaude" style="width:305px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'unidade de saúde')">
							<option value=""></option>
							<?php if ( isset( $_POST['unidadeDeSaude'] ) && $_POST['unidadeDeSaude'] != '' ) {
								$this->SelecionarUnidadeDeSaude( $_POST['unidadeDeSaude'] );
							} else {
								
								$this->SelecionarUnidadeDeSaude($unidadeDeSaude_id);
							}
							?>
					</select>
				</div>
				<br />
				<?php }?>

				<br/>
				<!-- ############################################################### -->

					<!--<input type="submit" name="cadastrar" value="     Editar     " />
					<input type="reset" name="apagar" value="   Apagar   " />-->
				<?php

					$botao = new Vacina();
					$botao->ExibirBotoesDoFormulario('Confirmar');

				?>

		 	</p>
		</form>
	<?php
	}
	//--------------------------------------------------------------------------

	public function ExibirFormularioInserirAcs()
	{
		?>
		<h3 align="center">Adicionar ACS</h3>
		<form id="formulario" name="formulario" method="post" action="?<?php echo $_SERVER['QUERY_STRING']; ?>"
		 onsubmit="return (ValidarNome(this.nome, 'nome')
					&& ValidarData(this.datadenasc)
					&& ValidarCampoSelect(this.ddd, 'DDD', true)
					&& ValidarTelLocal(this.telefone, true)
					&& ValidarCpf(this.cpf, true)
					&& ValidarTelLocal(this.telefone, true) 
					&& ValidarEmail(this.email, true)
					&& ValidarCampoSelect(this.unidadeDeSaude, 'unidade de saúde'))">

			<p>
				<!-- ############################################################### -->

				(*) Campos Obrigatórios. <br /><br />

				<div class='CadastroEsq'>
					*Nome:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="nome" id="nome" maxlength="100" style="width:300px;"
						onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this); ValidarNome(this, 'nome');
						FormatarNome(this, event)"
						value="<?php if (isset($_POST['nome'])) echo $_POST['nome'] ?>"
					/>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Data de Nascimento:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="datadenasc" maxlength="10"
						onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
				        onkeyup="return Mascara('DATA', this, event);"
						onblur="ValidarData(this)"
						value="<?php if (isset($_POST['datadenasc']))
						echo $_POST['datadenasc'] ?>" />
					<span id="TextoExemplos">
						<?php echo " Ex.: 01/01/1980 " ?>
					</span>
				</div>

				<!-- ############################################################### -->

				<br />
				<div class='CadastroEsq'>
					DDD:
				</div>
				<div class='CadastroDir'>
					<select name="ddd" id="ddd" style="width:50px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'DDD', true)"
						onchange="document.formulario.telefone.focus()">

					<?php if ( isset( $_POST['ddd'] ) && $_POST['ddd'] != '' ) {
						$this->SelecionarDdd( $_POST['ddd'] );
					} else {
						$this->SelecionarDdd();
					}
					?>
					</select>
					Telefone: <input type="text" name="telefone" id="telefone"
						maxlength="9" onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('TELLOCAL', this, event);"
						onblur="ValidarTelLocal(this, true);"
						value="<?php if (isset($_POST['telefone']))
						echo $_POST['telefone'] ?>" />
					<span id="TextoExemplos">
						<?php echo " Ex.: 2555-0555 " ?>
					</span>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					CPF:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="cpf" maxlength="14"
						onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('CPF',this,event);"
						onblur="ValidarCpf(this, true);"
					value="<?php if (isset($_POST['cpf'])) echo $_POST['cpf'] ?>"
					/>
					<span id="TextoExemplos">
						<?php echo " Ex.: 474.876.345-07" ?>
					</span>
				</div>
				<br />

				<!-- ############################################################### -->
				<div class='CadastroEsq'>
					E-mail:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="email" id="email" value="<?php
					if(isset($_POST['email'])) { echo $_POST['email']; }?>"
					maxlength="70" size="40" 
						onkeydown="Mascara('EMAIL',this,event);"
						onblur="ValidarEmail(this, true);" />
				</div>
				<br />
				
				<!-- ############################################################### -->

				<?php if(Sessao::Permissao('ACS_INSERIR')) { ?>
				<div class='CadastroEsq'>
					*Unidade de Saúde:
				</div>
				<div class='CadastroDir'>
					<select name="unidadeDeSaude" id="unidadeDeSaude" style="width:305px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'unidade de saúde')">
							<option value=""></option>
							<?php if ( isset( $_POST['unidadeDeSaude'] ) && $_POST['unidadeDeSaude'] != '' ) {
								$this->SelecionarUnidadeDeSaude( $_POST['unidadeDeSaude'] );
							} else {
								$this->SelecionarUnidadeDeSaude();
							}
							?>
					</select>
				</div>
				<br />
				<?php }?>

				<!-- ############################################################### -->
					<?php

						$botao = new Vacina();
						$botao->ExibirBotoesDoFormulario('Inserir', 'Limpar');

					?>

		 	</p>
		</form>
		<?php
	}
	//--------------------------------------------------------------------------
	
	public function ExibirFormularioExcluirAcs($id)
	{
		?>
		<div align="left">
			<form id="formulario1" name="formulario1" method="post"
			  action="<?php echo $_SERVER['REQUEST_URI']?>">
			<?php

				$dados = $this->SelecionarDadosAcs($id);
				
				$data = new Data();
				
				$nome = $dados[2];
				$nascimento = $dados[3];
				$nascimento = $data->InverterData($nascimento);

				echo '<h3 align="center">Confirmação para excluir</h3>';
				echo "<h4>" . Html::FormatarMaiusculasMinusculas($nome) . "</h4>";
				echo "<b>Nascimento: </b>$nascimento ";

				echo "<br /><br />";

				$botao = new Vacina();

				$botao->ExibirBotoesDoFormulario('Excluir', false, 'excluir');

				echo '<hr />';

				$botao->ExibirBotaoVoltar('Voltar', 'pagina=Adm/listarAcs');

			?>
			</form>

		</div>
		<?php
	}
	///////////////////////////////// SELECIONAR ///////////////////////////////
	private function SelecionarDadosAcs($id)
	{
		$resultado = $this->conexao->prepare('SELECT Ddd_id,
		UnidadeDeSaude_id, nome, nascimento, email, telefone,
		cpf FROM `acs` WHERE ativo AND id = ?')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_param('i', $id);

		$resultado->bind_result($ddd_id, $unidadeDeSaude_id, $nome, $nascimento, $email, $telefone, $cpf);

		$resultado->execute();

		$resultado->store_result();

		$resultado->fetch();

		$existe = $resultado->num_rows;

		$resultado->free_result();
		
		if($existe > 0) {

			return array($ddd_id, $unidadeDeSaude_id, $nome, $nascimento, $email,
						 $telefone, $cpf);

		}
		
		if($existe == 0) {
			
			$this->AdicionarMensagemDeErro('Não foi possível selecionar os dados
				deste indivíduo. A identificação do mesmo parece não existir.');
		}
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar os
				dados deste indivíduo.');
		}
		return false;
	}
	//--------------------------------------------------------------------------
	private function SelecionarUnidadeDeSaude($unidadeDeSaude_id = false)
	{
		$cidade_id = $_SESSION['cidade_id'];
		
		$parametro = 0;
		
		if ( Sessao::Permissao('ACS_INSERIR') == 2 ) {
			
			$sql = 'SELECT unidadedesaude.id, unidadedesaude.nome
				FROM `unidadedesaude`
				WHERE unidadedesaude.ativo
				AND unidadedesaude.id = ?';
			
			$parametro = $_SESSION['unidadeDeSaude_id'];
			
		} elseif ( Sessao::Permissao('ACS_INSERIR') == 1 ) {
			
			$sql = 'SELECT unidadedesaude.id, unidadedesaude.nome
				FROM `unidadedesaude`, `bairro`
				WHERE unidadedesaude.ativo
					AND bairro.ativo
					AND bairro.Cidade_id = ?
					AND unidadedesaude.Bairro_id = bairro.id
					
						ORDER BY nome';
			
			$parametro = $cidade_id;
			
		} else return false;
		
		$resultado = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$resultado->bind_param('i', $parametro);
		$resultado->bind_result($id, $nome);	
			
		$resultado->execute();

		while( $resultado->fetch() ) {

			$selecionado = '';
			if($unidadeDeSaude_id == $id) {
				$selecionado = 'selected="true"';
			}
			echo "<option value='$id' $selecionado>"
				. Html::FormatarMaiusculasMinusculas($nome) . "</option>";
		}
		$resultado->free_result();
	}
	//--------------------------------------------------------------------------

	public function ValidarFormulario($nomeDoFormulario)
	{
		switch($nomeDoFormulario) {

			case 'inserirAcs':
			case 'editarAcs':

				$nomeValido = $this->ValidarNomeDaPessoa( $_POST['nome'] );
				$nascValido = $this->ValidarDataDaPessoa( $_POST['datadenasc'] );
				$dddValido  = $this->ValidarDddDaPessoa( $_POST['ddd'] );
				$telValido  = $this->ValidarTelefoneDaPessoa( $_POST['telefone'] );
				$cpfValido  = $this->ValidarCpfDaPessoa( $_POST['cpf'] );
				$emailValido= $this->ValidarEmail($_POST['email'], true /*opcional*/);
				
				$unidadeValida = true;
				// Para validar a escolha, se o nível do administrador for Master:
				if(isset($_POST['unidadeDeSaude'])) {
					$unidadeValida = $this->ValidarUnidadeDeSaude($_POST['unidadeDeSaude']);
				} 
				
				if( $nomeValido && $nascValido && $dddValido && $telValido &&
					$cpfValido && $emailValido ) {

					return true;
				} else 
				break;

			default:
				$this->AdicionarMensagemDeErro('Formulário inexistente');
				return false;
		}

		$this->AdicionarMensagemDeErro('O formulário contém um ou mais dados
			inválidos e não pode ser submetido');

		return false;
	}
	
	//--------------------------------------------------------------------------	
	public function ValidarUnidadeDeSaude($unidade)
	{
		if ( $unidade != '' ) {
			return true;
		}
		$this->AdicionarMensagemDeErro('Selecione uma Unidade de Saúde');
		return false;
	}
	//--------------------------------------------------------------------------
	public function VerificarNaoDuplicidadeDeAcs($id = false)
	{
		$data = new Data(); // Para inverter a data
		
		$nome = $this->Nome();
		$nasc = $data->InverterData($this->nascimento);
		
		// Busca não restrita por causa de CPF nulos ou "0":
		$cpf  = '%' . Preparacao::RemoverSimbolos($this->Cpf()) . '%';

		if($id) {
			$stmt = $this->conexao->prepare('SELECT id FROM `acs`
				WHERE ativo AND nome = ? AND nascimento = ? AND cpf LIKE ?
				AND id <> ?')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$stmt->bind_param('sssi', $nome, $nasc, $cpf, $id);
		}
		else {
			$stmt = $this->conexao->prepare('SELECT id FROM `acs`
				WHERE ativo AND nome = ? AND nascimento = ? AND cpf LIKE ?')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$stmt->bind_param('sss', $nome, $nasc, $cpf);
		}

		$stmt->execute();
		$stmt->store_result();

		$registroJaExiste = $stmt->num_rows;

		$stmt->free_result();

		// Para exibir o CPF na mensagem de erro para o usuário do sistema:
		if( strlen($this->Cpf()) > 5 ) $cpf = ", CPF: {$this->Cpf()}";
		else                           $cpf = " (CPF não preenchido)";
		
		if($registroJaExiste > 0) {
			
			$this->AdicionarMensagemDeErro("Já existe o registro de $nome,
				nascido em {$this->nascimento} $cpf.");
			
			return false;
			
		}
		if($registroJaExiste == 0) return true;
		if($registroJaExiste < 0) {
			
			$this->AdicionarMensagemDeErro("Algum erro ocorreu ao verificar a
				não duplicidade de ACS ($nome,
				nascido em {$this->nascimento} $cpf.)");
			
			return false;
		}

		return true;
	}
	//--------------------------------------------------------------------------
	public function ExibirListaDeAcs()
	{
		$linkParaExcluir = false;
		
		// Administrador não master, exibe só o editar e os ACS da unidade dele:
		if( Sessao::Permissao('ACS_LISTAR') == 3 ) {
			
			$sql = "SELECT id, nome AS `Nome`, nascimento AS `Data de Nascimento`
					FROM `acs`
					WHERE acs.UnidadeDeSaude_id = {$_SESSION['unidadeDeSaude_id']}
						AND acs.nome <> 'Não informado'
						AND ativo
							ORDER BY acs.nome";
		}
		elseif( Sessao::Permissao('ACS_LISTAR') == 2 ) {

			$sql = "SELECT acs.id, acs.nome AS `Nome`,
						   acs.nascimento AS `Data de Nascimento`,
						   unidadedesaude.nome AS `Unidade`
					FROM `acs`, `unidadedesaude`
					WHERE acs.UnidadeDeSaude_id = unidadedesaude.id
                        AND  unidadedesaude.Bairro_id IN
                        (SELECT bairro.id FROM bairro WHERE
                        bairro.Cidade_id = {$_SESSION['cidade_id']} )
						AND acs.nome <> 'Não informado'
						AND acs.ativo
						AND unidadedesaude.ativo
							ORDER BY acs.nome";

			$linkParaExcluir = 'pagina=Adm/excluirAcs';

            //print_r($_SESSION);
		}
		// Administrador nível master:
		elseif( Sessao::Permissao('ACS_LISTAR') == 1 ) {
			
			$sql = "SELECT acs.id, acs.nome AS `Nome`,
						   acs.nascimento AS `Data de Nascimento`,
						   unidadedesaude.nome AS `Unidade`
					
					FROM `acs`, `unidadedesaude`
					WHERE acs.UnidadeDeSaude_id = unidadedesaude.id
						AND acs.nome <> 'Não informado'
						AND acs.ativo
						AND unidadedesaude.ativo
							ORDER BY acs.nome";
			
			$linkParaExcluir = 'pagina=Adm/excluirAcs';
		}
		
		$acs = $this->conexao->query($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$arr = array();

		while ( $linha = $acs->fetch_assoc() ) {
			
			if($linha['Nome'] != ' NÃO INFORMADO' ) {
			
				$data = new Data();
				
				$linha['Data de Nascimento'] = $data->InverterData($linha['Data de Nascimento']);
				
				$sql = "SELECT Acs_id FROM `usuario` WHERE Acs_id = {$linha['id']}
					AND ativo";
				
				$acs_existe = $this->conexao->query($sql)
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
								
				$crip = new Criptografia();
				
				$queryStringEditar = $crip->Cifrar("pagina=Adm/editarAcs&id={$linha['id']}"); 
				$queryStringExcluir = $crip->Cifrar("pagina=Adm/excluirAcs&id={$linha['id']}"); 
	
				if ($acs_existe->num_rows > 0){
	
					$acaoExcluir = "<img src='$this->arquivoGerarIcone?imagem=excluir_desab'
						border='0' alt='ACS não pode ser excluído. Há indivíduos ligados a este agente'
						title='ACS não pode ser excluído. Há indivíduos ligados a este agente' />";
				}
				else {
	
					$acaoExcluir = "<a href='?$queryStringExcluir'>"
						. "<img src='$this->arquivoGerarIcone?imagem=excluir' border='0'
						border='0' alt='Excluir este ACS' title='Excluir este ACS' /></a>";
				}
				
				$linha['ações'] = " &nbsp;<a href='?$queryStringEditar'>"
					. "&nbsp;<img src='$this->arquivoGerarIcone?imagem=editar' border='0' "
					. "alt='Alterar dados deste ACS' title='Alterar dados deste ACS' /></a>" 
					. "&nbsp;$acaoExcluir&nbsp;";
					
				$arr[] = $linha;
				
				$acs_existe->free_result();
			
			}
		}	
		$acs->free_result();

		Html::CriarTabelaDeArray($arr);
		
		if(count($arr)) return true;
		return false;
		
	}
}