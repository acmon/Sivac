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
 * Administrador: Classe para a manipulação dos dados do administrador
 *
 * @package Sivac/Class
 *
 * @author Douglas, v 1.0, 2009-01-02 12:52
 *
 * @copyright 2008 
 *
 */
 class Administrador extends Pessoa 
{
	protected $unidadeDeSaude_id;		// String
	private $_login;					// String
	private $_senha;					// String
	
	
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
	/**
	 * Seta o login do administrador
	 *
	 * @param string $login login do administrador
	 */

	public function SetarLogin($login)
	{
		$this->_login = $login;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta a senha do administrador
	 *
	 * @param string $senha senha do administrador
	 */
	public function SetarSenha($senha)
	{
		$this->_senha = $senha;
	}
	//--------------------------------------------------------------------------
	public function SetarDados($post)
	{
		
		$clean = Preparacao::GerarArrayLimpo($post, $this->conexao);

		parent::SetarDados($clean);
		
		if( isset($clean['unidadeDeSaude']) && $clean['unidadeDeSaude'] > 0)
			$this->SetarUnidadeDeSaude($clean['unidadeDeSaude']);
		
		// Se o administrador não é nível 1000, então o $_POST['unidadeDeSaude']
		// não existe, pois ele não pode escolher a unidade (tem que ser a dele)
		else $this->SetarUnidadeDeSaude($_SESSION['unidadeDeSaude_id']);
		
		$this->SetarLogin($clean['login']);
		$this->SetarSenha($clean['senha']);
	}
	//-------------------------------------------------------------------------
	//////////////////////////////// RETORNAR /////////////////////////////////

	//--------------------------------------------------------------------------
	public function Login()
	{
		return $this->_login;
	}
	//--------------------------------------------------------------------------
	public function Senha()
	{
		return $this->_senha;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna a unidade de determinada pessoa.
	 *
	 * @return string
	 */
	public function UnidadeDeSaude()
	{
		return $this->unidadeDeSaude_id;
	}
	
	///////////////////////////////// EDITAR //////////////////////////////////
	/**
	 * Edita os dados de determinada pessoa.
	 *
	 */
	public function EditarAdministrador($idAdministrador)
	{
		$crip = new Criptografia();
		
		
		$unidadeDeSaude_id  = $this->UnidadeDeSaude();
		$nome       		= $this->Nome();
		$login       		= $this->Login();
		$senha       		= $crip->Senha( $this->Senha() );

		$atualizar = $this->conexao->prepare('UPDATE `administrador` SET 
			nome = ?, login = ?, senha = ?, UnidadeDeSaude_id = ? WHERE id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$atualizar->bind_param('sssii', $nome, $login, $senha,
										$unidadeDeSaude_id, $idAdministrador);

		$atualizar->execute();

		$sucesso = $atualizar->affected_rows;
	
		if($sucesso > 0) {
			
			$_SESSION['nome'] = $nome;
			$_SESSION['unidadeDeSaude_id'] = $unidadeDeSaude_id;
            $_SESSION['unidade_nome'] = $this->RetornarCampoNome('unidadedesaude', $unidadeDeSaude_id);


            header("Location: ?{$_SERVER['QUERY_STRING']}");
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

	///////////////////////////////// EXIBIR //////////////////////////////////

	public function ExibirConfirmarSenha($idDaDiv = 'formAtivo')
	{
        
		?>
		<div class='CadastroEsq'>
		<span style="color: #3A5">Confirme a sua senha:</span>
		</div>
		<div class='CadastroDir' style="position: relative">
			<input type="password" name="senhaValidacao" id=senhaValidacao value="<?php
		if ( isset( $_POST['senhaValidacao'] ) ) {
			echo $_POST['senhaValidacao'];
		}
		?>" maxlength="10" style="width:200px"
			onblur="ValidarSenha(this)"
			onfocus="MudarPropriedade(this, 'background', 'white')"
			onkeydown="Mascara('SENHA', this, event)"
			onkeyup="ValidarSenhaDoAdministrador('<?php echo $idDaDiv?>',
				'<?php echo $_SESSION['login_adm']?>', this.value)"
			
			/>
		</div>
		<div id="ok"></div>
		<br />
		<?php
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioEditarAdministrador($id)
	{

		list($nome, $login, $senha, $unidadeDeSaude_id) =
							$this->SelecionarDadosAdministrador($id);

		?>
		<p>

				<!-- ####################################################### -->

				<div class='CadastroEsq'>
					Nome:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="nome" id=nome value="<?php
						if ( isset( $_POST['nome'] ) ) {
							echo $_POST['nome'];
						} else {
							echo $nome;
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

				<!-- ####################################################### -->

				<div class='CadastroEsq'>
					Login:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="login" id=login value="<?php
						if ( isset( $_POST['login'] ) ) {
							echo $_POST['login'];
						} else {
							echo $login;
						}
						?>" maxlength="100"
						style="width:300px;" 
						onkeydown="Mascara('LOGIN', this, event)"
						onchange='alert("Atenção\n\nCaso você altere o login, "
							+ "será necessário que você se desconecte e então "
							+ "conecte novamente ao sistema com a nova "
							+ "identificação (alterada para "
							+ this.value
							+ ").\n\nLogo, proceda com cautela.")'
						onblur="LimparString(this); ValidarLogin(this)"
					/>
				</div>
				<br />			

				<!-- ####################################################### -->

				<div class='CadastroEsq'>
					Senha:
				</div>
				<div class='CadastroDir'>
					<input type="password" name="senha" id=senha maxlength="100"
						style="width:300px;" 
						onkeydown="Mascara('SENHA', this, event)"
						onblur="LimparString(this); ValidarSenha(this)"
					/>
				</div>
				<br />			

				<!-- ####################################################### -->

				<div class='CadastroEsq'>
					Confirme a senha:
				</div>
				<div class='CadastroDir'>
					<input type="password" name="contrasenha" id="contrasenha"
						maxlength="100"
						style="width:300px;" 
						onkeydown="Mascara('SENHA', this, event)"
						onblur="LimparString(this);
								ValidarContraSenha(this, 'senha')"
					/>
				</div>
				<br />			

				<!-- ####################################################### -->
				
				<?php if(Sessao::Permissao('ADMINISTRADORES_EDITAR')) { ?>
				<div class='CadastroEsq'>
					Unidade de Saúde:
				</div>
				<div class='CadastroDir'>
					<select name="unidadeDeSaude" id="unidadeDeSaude"
						style="width:305px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'unidade de saúde')">
							<?php if ( isset( $_POST['unidadeDeSaude'] ) ) 
									$unidadeId = $_POST['unidadeDeSaude'];
								
								else $unidadeId = $unidadeDeSaude_id;
								
								$this->SelecionarUnidadeDeSaude($unidadeId);
							?>
					</select>
				</div>
				<br />
				<?php }?>

				<!-- ####################################################### -->

				<?php
					$botao = new Vacina();
					$botao->ExibirBotoesDoFormulario('Confirmar', 'Limpar');
				?>

		 	</p>
	<?php
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioEditarAdministradorDesabilitado($id)
	{
		list($nome, $login, $senha, $unidadeDeSaude_id) =
							$this->SelecionarDadosAdministrador($id);
		?>
		<p>
			<!-- ########################################################### -->

			<div class='CadastroEsq'>
				Nome:
			</div>
			<div class='CadastroDir'>
				<input type="text" disabled="true" value="<?php echo $nome ?>"
				style="width:300px"/>
			</div>
			<br />

			<!-- ########################################################### -->

			<div class='CadastroEsq'>
				Login:
			</div>
			<div class='CadastroDir'>
				<input type="text" disabled="true" value="<?php	echo $login ?>"
				style="width:300px;" />
			</div>
			<br />			

			<!-- ########################################################### -->

			<div class='CadastroEsq'>
				Senha:
			</div>
			<div class='CadastroDir'>
				<input type="password" disabled="true" style="width:300px;"
					value="**********" />
			</div>
			<br />			

			<!-- ########################################################### -->

			<div class='CadastroEsq'>
				Confirme a senha:
			</div>
			<div class='CadastroDir'>
				<input type="password" disabled="true" style="width:300px;"
					value="**********" />
			</div>
			<br />			

			<!-- ########################################################### -->
			
			<?php if(Sessao::Permissao('ADMINISTRADORES_EDITAR')) { ?>
			<div class='CadastroEsq'>
				Unidade de Saúde:
			</div>
			<div class='CadastroDir'>
					<!-- Campo desabilitado, não precisa validar -->
				<select disabled="true" style="width:305px; margin-left:2px;">
				<?php $this->SelecionarUnidadeDeSaude($unidadeDeSaude_id); ?>
				</select>
			</div>
			<br />
			<?php }?>

			<br/>
			<!-- ########################################################### -->

			<?php
				$botao = new Vacina();
				$botao->ExibirBotoesDoFormulario('Confirmar', 'Limpar',
					'ok', '#14E', true); // true para botoes Desabilitados
			?>
	 	</p>
		<?php
	}
	
	//--------------------------------------------------------------------------
	///////////////////////////////// SELECIONAR ///////////////////////////////
	private function SelecionarDadosAdministrador($id)
	{
		$resultado = $this->conexao->prepare('SELECT nome,
			login, senha, UnidadeDeSaude_id FROM `administrador` WHERE id = ?
			AND administrador.ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_param('s', $id);

		$resultado->bind_result($nome, $login, $senha, $unidadeDeSaude_id);

		$resultado->execute();

		$resultado->store_result();

		$resultado->fetch();

		$existe = $resultado->num_rows; 

		$resultado->free_result();
		
		if($existe > 0) {

			return array($nome, $login, $senha, $unidadeDeSaude_id);
		}
		
		if($existe == 0) {
			
			$this->AdicionarMensagemDeErro('Não foi possível selecionar os dados
				do administrador. A identificação do mesmo parece não existir.');
		}
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar os
				dados do administrador.');
		}
		return false;
	}
	//--------------------------------------------------------------------------
	private function SelecionarUnidadeDeSaude($unidadeDeSaude_id = false)
	{
		$resultado = $this->conexao->prepare('SELECT id, nome FROM
			`unidadedesaude` WHERE ativo ORDER BY nome')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$resultado->execute();

		$resultado->bind_result($id, $nome);

		while( $resultado->fetch() ) {

			$selecionado = '';
			if($unidadeDeSaude_id == $id) {
				$selecionado = 'selected="true"';
			}
			echo "<option value='$id' $selecionado>$nome</option>";
		}
		$resultado->free_result();
	}
	//--------------------------------------------------------------------------
	private function ValidarLogin($login)
	{
		if(strlen($login) > 4) return true;
		
		$this->AdicionarMensagemDeErro("A identificação \"$login\" está pequena
			demais");

		return false;	
	}
	//--------------------------------------------------------------------------
	private function ValidarSenha($senha, $contrasenha)
	{

		if(strlen($senha) > 3 && $senha === $contrasenha) return true;
		
		$this->AdicionarMensagemDeErro("A senha é inválida ou for confirmada
			incorretamente.");

		return false;
	}
	//--------------------------------------------------------------------------
	private function ValidarUnidadeDeSaude($unidade_id)
	{
		$stmt = $this->conexao->prepare('SELECT id FROM `unidadedesaude` WHERE id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_param('i', $unidade_id);
		
		$stmt->execute();
		
		$stmt->store_result();
		
		$registros = $stmt->num_rows;
		
		$stmt->free_result();
		
		if($registros == 1) return true;
		
		if($registros == 0) {

			$this->AdicionarMensagemDeErro('Esta unidade não existe!');
		}
		
		if($registros < 0) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao verificar a
				unidade de saúde.');
		}

		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarFormulario($nomeDoFormulario)
	{
		switch($nomeDoFormulario) {

			case 'editarAdministrador':

				$nomeValido = $this->ValidarNomeDaPessoa( $_POST['nome'] );
				$loginValido = $this->ValidarLogin( $_POST['login'] );
				$senhaValida  = $this->ValidarSenha( $_POST['senha'], $_POST['contrasenha'] );
				
				$unidadeValida = true;
				// Para validar a escolha, se o nível do administrador for Master:
				if(isset($_POST['unidadeDeSaude'])) {
					$unidadeValida = $this->ValidarUnidadeDeSaude($_POST['unidadeDeSaude']);
				} 
				
				if( $nomeValido && $loginValido && $senhaValida && $unidadeValida) {
						
					return true;
				}
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
	public function IdDoAdministradorAtual()
	{
		$login = $_SESSION['login_adm'];
		
		$stmt = $this->conexao->prepare('SELECT id FROM `administrador` WHERE
			login = ? AND ativo');
		
		$stmt->bind_param('s', $login);
		$stmt->bind_result($id);
		$stmt->execute();
		$stmt->store_result();
		$existe = $stmt->num_rows;
		$stmt->fetch();
		$stmt->free_result();
		
		if( $existe == 1 ) return $id;
		
		if( $existe == 0 ) {

			$this->AdicionarMensagemDeErro('Administrador com identificação
				inexistente.');
		}
		
		if( $existe < 0 ) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao verificar a
				identificação do administrador.');
		}
		
		return false;
	}
	//--------------------------------------------------------------------------
	public function VerificarNaoDuplicidadeDeAdministrador($id)
	{
		if($id) {
		
			$login = $this->Login();
			
			$stmt = $this->conexao->prepare('SELECT id FROM `administrador`
				WHERE login = ? AND id <> ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
	
			$stmt->bind_param('si', $login, $id);
	
			$stmt->execute();
			$stmt->store_result();
	
			$registroJaExiste = $stmt->num_rows;
	
			$stmt->free_result();
	
			if($registroJaExiste == 0) return true;
			
			if($registroJaExiste > 0) {
				
				$this->AdicionarMensagemDeErro("Já existe o administrador com a
					identificação \"$login\". Escolha outro login.");
				
			}
			
			if($registroJaExiste < 0 ) {
				
				$this->AdicionarMensagemDeErro("Algum erro ocorreu ao verificar a
					não duplicidade de administrador (identificação $login)");
			}
		}
		return false;
	}
	//--------------------------------------------------------------------------
	public function AutenticarAdministrador($login, $senha) // para ajax:
	{
		$crip = new Criptografia();
		$senha = $crip->Senha($senha);
		
		$stmt = $this->conexao->prepare('SELECT id FROM `administrador`
			WHERE login = ? and senha = ? AND ativo');

		$stmt->bind_param('ss', $login, $senha);
		$stmt->bind_result($administradorId);
		$stmt->execute();
		$stmt->store_result();
		$existe = $stmt->num_rows;
		
		if($existe > 0) $stmt->fetch();
		
		else $administradorId = false;
		
		$stmt->free_result();
		return $administradorId;
	}
}