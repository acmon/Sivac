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


//require('tBd.php');
//require('tHtml.php');

class Nota
{
	//--------------------------------------------------------------------------
	private $_identificador = 0;
	private $_titulo;
	private $_descricao;
	private $_usuario = 0;
	private $_vacina = 0;
	private $_dataDeCriacao;
	private $_ativo = true;
	
	private $_carregado = false;
	private $_alterado = false;
	private $_novo = false;
	
	private $conexao;
	
	protected $url;					
	protected $msgDeErro;	
	protected $arquivoGerarIcone;

    
	//--------------------------------------------------------------------------
	public function ExibirSelectVacina()
	{
		
		$vacina_id = 0;
		if(isset($_POST['vacina_id'])) $vacina_id = $_POST['vacina_id'];
		
		?>
		<!--form name="form1" method="post" action="<?php //echo $_SERVER['REQUEST_URI']?>" -->
		
		<div class='CadastroEsq'>Vacina:</div>
		<div class='CadastroDir'><?php $this->SelectVacinas($vacina_id);?>&nbsp;</div>

		<br />
		<p><div id="NotasDaVacina" >&nbsp;</div></p>
		<?php
		
	}	
	//--------------------------------------------------------------------------
	public function ExibirFormularioInserirNota($vacinaSelecionada = false)
	{

		?>
		<span style="clear:left;">
		<h3 align="center" >Cadastrar Nota Técnica</h3>
		</span>
		
		<p align="center">
		<fieldset style="width:650px; margin: auto" align="center">
		<legend>Inserir nova nota:</legend>
		
		<form name="inserirNota" id="inserirNota"
			method="post" action=""
			onsubmit="return( ValidarNome(this.titulo, 'título') && ValidarTextoLongo(this.descricao, false))">
		<div>
		
			<p>
				<div class='CadastroEsq'>Título:</div>
				
			  	<div class='CadastroDir'>
				<input type="text" name="titulo" id="titulo" value="<?php
				if( isset($_POST['titulo']) ) echo $_POST['titulo']?>" 
				style="width:200px;" maxlength="70" 
				onkeypress="FormatarNome(this, event)"
				onkeyup="FormatarNome(this, event)"
				onkeydown="Mascara('NOME', this, event)"
				onblur="LimparString(this); ValidarNome(this, 'título');
				FormatarNome(this, event)"/>
				</div>
			</p>
		<input type="hidden" name="vacina_id" id="vacina_id"
		value="<?php if( $vacinaSelecionada ) echo $vacinaSelecionada; ?>">
		</div>
	  <p><div align="center" style="clear:both">Descrição:</div></p>
	  <p>
	    <div align="center">
	    	<textarea name="descricao" cols="50" rows="5"
			onblur="LimparString(this); ValidarTextoLongo(this, false)"
			id="descricao" style="width:450px;"><?php
				if( isset($_POST['descricao']) ) echo $_POST['descricao']?></textarea>
	    </div>
	  </p>
	  	  
	  <p><center>

	  	<?php $this->ExibirBotoesDoFormulario('Confirmar', 'Limpar')?>
	    </center>
	  </p>
	  </fieldset>
	  </p>
	  </form>
	  <?php
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioAlterarNota()
	{
	
		?>
		<form name="alterarNota" id="alterarNota" method="post"
			action="<?php echo $_SERVER['REQUEST_URI']?>"
			onsubmit="return( ValidarNome(this.titulo, 'título') && ValidarTextoLongo(this.descricao, false))">

		<h3 align="center">Alterar Nota Técnica</h3>

		<div style="padding-left:50px">
		
			<p>
				<div class='CadastroEsq'>Título:</div>
				
			  	<div class='CadastroDir'>
				<input type="text" name="titulo" id="titulo"
				value="<?php echo $this->_titulo; ?>" 
				style="width:200px;" maxlength="70" 
				onkeypress="FormatarNome(this, event)"
				onkeyup="FormatarNome(this, event)"
				onkeydown="Mascara('NOME', this, event)"
				onblur="LimparString(this); ValidarNome(this, 'título');
				FormatarNome(this, event)"/>
				</div>
			</p>
	
		</div>
		
	  <p><div align="center" style="clear:both">Descrição:</div></p>
	  <p>
	    <div align="center">
	    	<textarea name="descricao" cols="50" rows="5" 
			id="descricao" style="width:450px;"
			onblur="LimparString(this); ValidarTextoLongo(this, false)" ><?php
			echo $this->_descricao; ?></textarea>
	    </div>
	  </p>
	  	  
	  <p><center>
	  	<?php $this->ExibirBotoesDoFormulario('Confirmar')?>
	    </center>
	  </p>
	</form> <?php	
		
	}
	//--------------------------------------------------------------------------	
	public function InserirNota()
	{
		if(( $this->_novo) && ($this->_titulo <> '') && ($this->_descricao <> ''))
		{
			$objeto = $this->conexao->prepare('SELECT id FROM notadavacina
											   WHERE titulo = ? 
											   AND datadecriacao = ?
											   AND ativo
											   AND Administrador_id = ?
											   AND Vacina_id = ?')
  			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
  			
  			$objeto->bind_param('ssii', $this->_titulo, $this->_dataDeCriacao, 
			  					$this->_usuario, $this->_vacina);
			  					
			$objeto->bind_result($idAntigo);
  			
  			$objeto->execute();
  			
  			$objeto->store_result();
  			
  			$controlador = $objeto->num_rows;
  			
  			$objeto->free_result();
  			
  			if ($controlador == 0) {
  				
  				$objeto = $this->conexao->prepare('INSERT INTO notadavacina(titulo,
				  								   descricao, datadecriacao, ativo,
					 							   Administrador_id, Vacina_id)
					 							   VALUES(?,?,?,?,?,?)')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
				$objeto->bind_param('sssiii', $this->_titulo, $this->_descricao, 
									$this->_dataDeCriacao, $this->_ativo, 
									$this->_usuario, $this->_vacina);
									
				$objeto->execute();
				
				$this->_identificador = $objeto->insert_id;
				
				$this->_novo = false;
				$this->_alterado = false;
				$this->_carregado = true;	
	  				
  			} else {
  				
  				return false;
  				
  			}
		
			$_POST = array();
			
			return true;	
		
		} else {
			
			return false;
			
		}
		
	}
	//--------------------------------------------------------------------------	
	public function AlterarNota()
	{
	
		if( ($this->_alterado) && ($this->_carregado) && 
			($this->_titulo <> '') && ($this->_descricao <> '') )
		{
			$objeto = $this->conexao->prepare('UPDATE notadavacina SET titulo = ?,
												descricao = ?
												WHERE id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$objeto->bind_param('ssi', $this->_titulo, 
								$this->_descricao, $this->_identificador);
			$objeto->execute();
			
			$this->_novo = false;
			$this->_alterado = false;
			$this->_carregado = true;
			
			$objeto->close();
			return true;
		
			$_POST = array();
					
		} else {
			
			return false;
			
		}
				
	}
	//--------------------------------------------------------------------------	
	public function ExcluirNota()
	{
		if($this->_carregado)
		{
			$objeto = $this->conexao->prepare('UPDATE notadavacina SET ativo = false WHERE id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$objeto->bind_param('i', $this->_identificador);
			$objeto->execute();
			
			$this->SetarValorPadrao();
			$this->_novo = false;
			$this->_alterado = false;
			$this->_carregado = false;
			
			$objeto->close();
			return true;				
		}

	} 
	//--------------------------------------------------------------------------	
	public function CarregarDados($identificador)
	{
		$objeto = $this->conexao->prepare('SELECT titulo, descricao, 
										   datadecriacao, ativo, Administrador_id,
										   Vacina_id FROM notadavacina WHERE id = ?')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$objeto->bind_param('i', $identificador);
		
		$objeto->bind_result($titulo, $descricao, $dataDeCriacao, $ativo, 
							$usuario, $vacina);
		
		$objeto->execute();
		
		$objeto->store_result();
	
		if( $objeto->num_rows > 0 ){
			
			$objeto->fetch();
			$this->_identificador = $identificador;
			$this->SetarTitulo($titulo);
			$this->SetarDescricao($descricao);
			$this->_dataDeCriacao = $dataDeCriacao;
			$this->SetarAtivo($ativo);
			$this->SetarUsuario($usuario);
			$this->SetarVacina($vacina);
			
			$this->_alterado = false;
			$this->_novo = false;
			$this->_carregado = true;
			
			$objeto->close();
			return true;
		
		}
		$objeto->close();				
		return false;
			
	}
	//--------------------------------------------------------------------------
	public function SelectVacinas( $selecao = false )
	{
        if(isset($_SESSION['navegacao']['vacina_id'])) $vacinaIdSelecionada = $_SESSION['navegacao']['vacina_id'];

		/*echo '<select name="vacina_id" id="vacina_id"
			style="width:305px;"
			onblur="ValidarCampoSelect(this, \'Vacina\')"
			onchange="ListarNotaDaVacina(this.value, \'NotasDaVacina\')">';
		
			$vacina = $this->conexao->prepare('SELECT id, nome FROM `vacina` ORDER BY nome')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$vacina->bind_result($vacina_id, $vacina_nome);
			$vacina->execute();
			echo "<option value='0'>- selecione -</option>";
			
			echo $selecao;
			
			if( $selecao == false ){
			
				while ($vacina->fetch()) {
				
				echo "<option value='$vacina_id'>$vacina_nome</option>";
				
				}
				
			} else {
				
				while ($vacina->fetch()) {
				
					if( $selecao == $vacina_id ) {
				
						echo "<option value='$vacina_id' selected='true'>$vacina_nome</option>";
				
					} else {
					
						echo "<option value='$vacina_id'>$vacina_nome</option>";
					
					}
					
				}
								
			}
			
			$vacina->free_result();
		echo '</select>';*/

		echo '<select name="vacina_id" id="vacina_id"
			style="width:305px;"
			onblur="ValidarCampoSelect(this, \'Vacina\')"
			onchange="ListarNotaDaVacina(this.value, \'NotasDaVacina\');">';
		
			$consulta = 'SELECT id, Grupo_id, nome, pertence
			FROM `vacina`m
			WHERE ativo
			ORDER BY Grupo_id DESC,
			nome ASC';
		
			$vacina = $this->conexao->prepare($consulta)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$grupo_id_anterior = 'nenhum';
			$vacina->bind_result($id, $grupo_id, $nome, $pertence);;
			$vacina->execute();
			
			echo "<option value='0'>- selecione -</option>";
			
			echo $selecao;
			 
			if( $selecao == false ){

				while ($vacina->fetch()) {

                $selecionado = '';
				if($vacinaIdSelecionada == $id) $selecionado = ' selected="true" ';
 
                    if($pertence) continue;

					if($grupo_id_anterior != $grupo_id) {

						echo "<optgroup label='$grupo_id'>";
						$grupo_id_anterior = $grupo_id;
				
					}

				echo "\n<option value='$id' $selecionado>$nome</option>";
				
				if($grupo_id_anterior != $grupo_id) echo '</optgroup>';
				
				}
				
			} else {


				while ($vacina->fetch()) {

                $selecionado = '';
				if($vacinaIdSelecionada == $id) $selecionado = ' selected="true" ';


					if( $selecao == $vacina_id ) {
				
						echo "\n<option value='$id' $selecionado>$nome</option>";
				
					} else {
					
						if($grupo_id_anterior != $grupo_id) {

							echo "<optgroup label='$grupo_id'>";
							$grupo_id_anterior = $grupo_id;
				
						}
						
						echo "\n<option value='$id' $selecionado>$nome</option>";
						
						if($grupo_id_anterior != $grupo_id) echo '</optgroup>';
					
					}
					
				}
								
			}
			
			$vacina->free_result();
			
		echo '</select>';
        if(isset($_SESSION['navegacao']['vacina_id'])) echo "<script>ListarNotaDaVacina({$_SESSION['navegacao']['vacina_id']}, 'NotasDaVacina');</script>";
	}

	//--------------------------------------------------------------------------	
	public function SetarTitulo($titulo)
	{
		$this->_titulo = $titulo;
		$this->VerificarModificacao();
		
		return true;	
	}
	//--------------------------------------------------------------------------
	public function SetarDescricao($descricao)
	{
		$this->_descricao = $descricao;
		$this->VerificarModificacao();	
		
		return true;
	}
	//--------------------------------------------------------------------------
	public function SetarUsuario($usuario = false)
	{
		if( $this->ValidarUsuario($usuario) ){
			
			$this->VerificarModificacao();
			return true;
		
		}
		return false;
				
	}
	//--------------------------------------------------------------------------
	public function SetarVacina($vacina)
	{
		if( $this->ValidarVacina($vacina) ){
			$this->_vacina = $vacina;
			$this->VerificarModificacao();
			return true;
		}
		return false;	
	}
	//--------------------------------------------------------------------------
	public function SetarDataDeCriacao($data = false)
	{
		date_default_timezone_set('America/Sao_Paulo');
		
		if($data){
			
			
			list($dia, $mes, $ano) = explode('/', $data);
				
		} else {
			
			list($dia, $mes, $ano) = explode('/', date('d/m/Y'));
			
		}
		
		
		if( checkdate($mes, $dia, $ano) ){
			$this->_dataDeCriacao = $ano . '/' . $mes . '/' . $dia;
			$this->VerificarModificacao();
			return true;
		}
		return false;
		
	}
	//--------------------------------------------------------------------------
	public function SetarAtivo($ativo)
	{
		if($ativo)
		$this->_ativo = true;
		else
		$this->_ativo = false;
		$this->VerificarModificacao();
		
		return true;
	}
	//--------------------------------------------------------------------------
	public function SetarDados($titulo, $descricao, $dataDeCriacao, $usuario, $vacina, $ativo)
	{

		$tempTitulo = $this->PegarTitulo();
		$tempDescricao = $this->PegarDescricao();
		$tempDataDeCriacao = $this->PegarDataDeCriacao();
		$tempUsuario = $this->PegarUsuario();
		$tempVacina = $this->PegarVacina();
		$tempAtivo = $this->PegarAtivo();

		if ( ($this->SetarVacina($vacina)) && ($this->SetarUsuario($usuario))){
		
			$this->SetarTitulo($titulo);
			$this->SetarDescricao($descricao);
			$this->SetarDataDeCriacao($dataDeCriacao);
			$this->SetarAtivo($ativo);
			
		} else {
		
			$this->_titulo = $tempTitulo;
			$this->_descricao = $tempDescricao;
			$this->_dataDeCriacao = $tempDataDeCriacao;
			$this->_usuario = $tempUsuario;
			$this->_vacina = $tempVacina;
			$this->_ativo = $tempAtivo;	
			
			return false;
		}

		$this->VerificarModificacao();
		
		return true;
		
	}
	//--------------------------------------------------------------------------
	private function ValidarUsuario($usuario = false)
	{
		
		$identificacao = $this->PegarIdAdministrador($usuario);
		if($identificacao){
			
			$this->_usuario = $identificacao;
			
			return true;
			
		} else {
			
			return false;
			
		}
		 
	}
	//--------------------------------------------------------------------------
	private function ValidarVacina($vacina)
	{
		$objeto = $this->conexao->prepare('SELECT nome FROM vacina WHERE id = ?
											AND ativo')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$objeto->bind_param('i', $vacina);
		$objeto->bind_result($nome);
		$objeto->execute();
		
		$objeto->store_result();
		
		if( $objeto->num_rows > 0 ){
			
			$objeto->fetch();
			$objeto->close();
			
			return $nome;
		} else {
			$objeto->close();
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function PegarIdentificador()
	{
		return $this->_identificador;
	}
	//--------------------------------------------------------------------------
	public function PegarTitulo()
	{
		return $this->_titulo;
	}
	//--------------------------------------------------------------------------
	public function PegarDescricao()
	{
		return $this->_descricao;
	}
	//--------------------------------------------------------------------------
	public function PegarUsuario()
	{
		return $this->_usuario;
	}
	//--------------------------------------------------------------------------
	public function PegarVacina()
	{
		return $this->_vacina;
	}
	//--------------------------------------------------------------------------
	public function PegarDataDeCriacao()
	{
		return $this->_dataDeCriacao;
	}
	//--------------------------------------------------------------------------
	public function PegarAtivo()
	{
		return $this->_ativo;
	}
	//--------------------------------------------------------------------------
	private function SetarValorPadrao()
	{
		
		$this->_identificador = 0;
		$this->_titulo = '';
		$this->_descricao = '';
		$this->_usuario = 0;
		$this->_vacina = 0;
		$this->_ativo = true;
		$this->_dataDeCriacao;
		$this->_alterado = false;
		$this->_novo = false;
		$this->_carregado = false;

	}
	//--------------------------------------------------------------------------
	public function UsarBaseDeDados()
	{
		
		
		$this->conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
			$_SESSION['senha']);


		$this->conexao->select_db($_SESSION['banco']);
		
		/*
		$this->conexao = mysqli_connect('localhost', 'root', 'abc123');


		$this->conexao->select_db('sivac_bduf');
		*/
		
	}
	//--------------------------------------------------------------------------
	public function NovoRegistro()
	{
		$this->SetarValorPadrao();
		$this->_novo = true;
		$this->_alterado = false;
		$this->_carregado = false;
	}
	//--------------------------------------------------------------------------
	private function VerificarModificacao()
	{
		
		if( !$this->_novo ) {
			
			$this->_alterado = true;
			
		} else {
			
			$this->_alterado = false;
			
		}
		
	}
	//--------------------------------------------------------------------------
	private function PegarIdAdministrador($login = false)
	{
		
		if($login){
			$administrador = $login;	
		} else {
			$administrador = $_SESSION['login_adm'];
		}
		
		$nivel = 0;
		
		if( Sessao::Permissao('NOTAS_CADASTRAR') )
			$nivel = $_SESSION['nivel'];
		else return false;
				
		$objeto = $this->conexao->prepare('SELECT id FROM administrador WHERE login = ?
						AND ativo AND nivel = ?')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$objeto->bind_param('ii', $administrador, $nivel);
		$objeto->bind_result($identificacao);
		$objeto->execute();
		
		$objeto->store_result();
		
		if( $objeto->num_rows > 0 ){
			
			$objeto->fetch();
			$objeto->close();
			
			return $identificacao;
		} else {
			
			$objeto->close();
			
			return false;
		}		
	}
	//--------------------------------------------------------------------------
	public function __construct()
	{
		$this->msgDeErro = array();
		
		// ????? Tirar o 1000 depois.
		$this->url = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA;
		
		$this->LocalizarArquivoGeradorDeIcone();
		
		
	}
	//--------------------------------------------------------------------------
	public function __destruct()
	{
		if( isset($this->conexao) ) $this->conexao->close();
	}
	//--------------------------------------------------------------------------
	private function LocalizarArquivoGeradorDeIcone()
	{
		if(file_exists("{$_SERVER['DOCUMENT_ROOT']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php")) {
			
			$this->arquivoGerarIcone =
				"http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php";
        }
	}
	//--------------------------------------------------------------------------
	public function ExibirBotoesDoFormulario( $submit = false, $reset = false,
	$imagemSubmit = 'ok', $corDoTexto ='#14E', $desabilitados = false)
	{

		if($desabilitados) $desabilitados = " disabled='true' ";
		else $desabilitados = '';
		echo '<div align="center" style="clear:both">';
		if($submit) {
			
			echo "<button type='submit' name='enviar' $desabilitados
			      style='color: $corDoTexto; width: 130px; margin:10px'>";
			echo "<img src='{$this->arquivoGerarIcone}?imagem=$imagemSubmit' alt='ok'
				  style='vertical-align: middle' />";
			echo $submit;
			echo '</button>';
		}
		if($reset) {

			echo "<button type='reset' name='reset' $desabilitados
			      style='color: $corDoTexto; width: 130px; margin:10px'>";
			echo "<img src='{$this->arquivoGerarIcone}?imagem=desmarcar' alt='reset'
				  style='vertical-align: middle' />";
			echo $reset;
			echo '</button>';
		}
		echo '</div>';
	}
	//--------------------------------------------------------------------------
	public function ListarNotaDaVacina( $vacina_id, $leitura = false, $exibirLegenda = false)
	{
			if($vacina_id) {
                $_SESSION['navegacao']['vacina_id'] = $vacina_id;
            }
            else {
                 $vacina_id = $_SESSION['navegacao']['vacina_id'];
            }

			//if(!$vacina_id) return false;
		
			$vacina = $this->conexao->prepare('SELECT id, titulo, descricao,
											   DATE(datadecriacao) 
	  										   FROM `notadavacina` 
											   WHERE Vacina_id = ?
											   AND ativo
											   ORDER BY titulo, datadecriacao')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$vacina->bind_param('i', $vacina_id);
			$vacina->bind_result($id, $titulo, $descricao, $dataDeCriacao);
			$vacina->execute();
			
			$data = new Data();
			
			while ($vacina->fetch()) {
				
			//$descricao = Seguranca::CorrigirEscapamentos($descricao);
			
			$crip = new Criptografia();
			
			$queryStringEditar = $crip->Cifrar("pagina=Adm/editarNota&id={$id}"); 
			$queryStringExcluir = $crip->Cifrar("pagina=Adm/excluirNota&id={$id}"); 
			$queryStringVisualizar = $crip->Cifrar("pagina=visualizarNota&id={$id}"); 

		    if(strlen($descricao) > 50) $descricao = substr($descricao, 0, 50).'...'; 
			
                if(!$leitura) {


                    $acaoVisualizar = "<a href='javascript:
                    AbrirJanela(\"Pop?$queryStringVisualizar\",
                                    250, 250)'>"
                    . "<img src='$this->arquivoGerarIcone?imagem=listar' border='0'
                    border='0' alt='Vizualizar esta Nota' title='Visualizar nota'/></a>";

                    $acaoExcluir = "<a href='?$queryStringExcluir'>"
                        . "<img src='$this->arquivoGerarIcone?imagem=excluir' border='0'
                        border='0' alt='Excluir esta Nota' title='Excluir nota' /></a>";

                    $acaoEditar = "<a href='?$queryStringEditar'>"
                        . "<img src='$this->arquivoGerarIcone?imagem=editar' border='0' "
                        . "alt='Alterar dados desta Nota' title='Alterar dados desta nota' /></a>";


                    $arr[] = array('id' => $id, 'Titulo' => $titulo, 'Nota' => $descricao,
                                    'Data de Cadastro' => $data->InverterData($dataDeCriacao),
                                    'ações' =>"{$acaoVisualizar}{$acaoEditar}{$acaoExcluir}");
                    } else {
                        $acaoVisualizar = "<a href='javascript:
                        AbrirJanela(\"?$queryStringVisualizar\",	250, 250)'>"
                        . "<img src='$this->arquivoGerarIcone?imagem=listar' border='0'
                        border='0' alt='Excluir esta Nota' title='Visualizar nota'/></a>";

                        $arr[] = array('id' => $id, 'Titulo' => $titulo, 'Nota' => $descricao,
                                    'Data de Cadastro' => $data->InverterData($dataDeCriacao),
                                    'ações' =>"$acaoVisualizar");

                    }

                }
                $vacina->free_result();


            echo '<span style="clear:left;"></span>';

            if( isset($arr) && (count($arr) > 0) ){

                echo '<br /><h3 align="center" >Notas Cadastradas</h3>';

                Html::CriarTabelaDeArray($arr);

            }
            elseif( $vacina_id <> 0 )
                echo '<br /><p><center>Nenhuma nota cadastrada para esta vacina</p></center>';

			//???? No metodo ListarNotaDaVacina exibe o form tbm????????????????
			if(!$leitura) $this->ExibirFormularioInserirNota($vacina_id);  



            if($exibirLegenda && isset($arr) && (count($arr) > 0)) {

            $icones[] = array('listar', 'Visualizar Nota Técnica');
            $icones[] = array('editar', 'Alterar Nota Técnica');
            $icones[] = array('excluir','Excluir Nota Técnica');


            $legenda = new Legenda($icones);
            $legenda->ExibirLegenda();

            }

	}
	//--------------------------------------------------------------------------
		//--------------------------------------------------------------------------
	/**
	 * Exibir uma ou várias mensagens de erro
	 *
	 */
	public function ExibirMensagensDeErro(
                                       $tituloDaJanela = 'Erro',
                                       $eventoDeVisibilidade = 'onmousemove')
	{
		if( count($this->msgDeErro) ) {

                        // Container para a barra de título e o corpo da mensagem
			echo '<div class="msgErro" id="containerDeMensagem"
                                style="visibility: visible">';

                        // Barra de título:
                        echo '<div class="barraDeTituloMsgErro" id="tituloMsgErro"
                              title="Fechar" '
                        . $eventoDeVisibilidade
                        . '="document.getElementById(\'containerDeMensagem\').style.visibility = \'hidden\';'
                        . 'document.getElementById(\'tituloMsgErro\').style.visibility = \'hidden\';'
                        . 'document.getElementById(\'mensagemDeErro\').style.visibility = \'hidden\';">'
                        . '&nbsp;'
                        . $tituloDaJanela
                        . '</div>';

                        // Corpo da mensagem:
                        echo '<div class="corpoMsgErro" id="mensagemDeErro">';
			echo 'Corrija o(s) erro(s) abaixo:';

                        // Exibindo a lista de erros:
			echo '<ul>';
			foreach ($this->msgDeErro as $mensagem) {
				echo "<li>$mensagem</li>";
			}
			echo '</ul>';

			echo '</div></div>';
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe uma mensagem
	 *
	 * @param String $mensagem
	 */
	public function ExibirMensagem($mensagem,
                                       $tituloDaJanela = 'Informação',
                                       $eventoDeVisibilidade = 'onmousemove'
                                       )
	{
                // Container para a barra de título e o corpo da mensagem
		echo '<div class="msgErro" id="containerDeMensagem"
                        style="visibility: visible">';

                // Barra de título:
		echo '<div class="barraDeTituloMsgErro" id="tituloMsgErro" title="Fechar" '
                        . $eventoDeVisibilidade
                        . '="document.getElementById(\'containerDeMensagem\').style.visibility = \'hidden\';'
                        . 'document.getElementById(\'tituloMsgErro\').style.visibility = \'hidden\';'
                        . 'document.getElementById(\'mensagemDeErro\').style.visibility = \'hidden\';">'
                        . '&nbsp;'
                        . $tituloDaJanela
                        . '</div>';

                // Corpo da mensagem:
		echo '<div class="corpoMsgErro" id="mensagemDeErro">', $mensagem, '</div>';

                // Fechando o container:
		echo '</div>';
	}

	//--------------------------------------------------------------------------
	public function ExibirFormularioExcluirNota($nota)
	{	 
	?>
	<div align="left">
		<form id="excluirNota" name="excluirNota" method="post"
		  action="<?php echo $_SERVER['REQUEST_URI']?>">
		<?php

			$dados = $this->SelecionarDadosNota($nota);

			$data = new Data();
			
			$titulo = $dados[1];
			$dataDeCriacao = $dados[3];

			echo '<h3 align="center">Confirmação para excluir</h3>';
			echo "<h4>" . Html::FormatarMaiusculasMinusculas($titulo) . "</h4>";
			echo "<b>Data de criação: </b>{$data->InverterData($dataDeCriacao)} ";
			echo "<br /><pre>$dados[2]</pre>";

			echo "<br /><br />";

			$botao = new Vacina();

			$botao->ExibirBotoesDoFormulario('Excluir', false, 'excluir');

			echo '<hr />';

			$botao->ExibirBotaoVoltar('Voltar', 'pagina=Adm/nota');

		?>
		</form>

	</div>
	<?php
	}
	//--------------------------------------------------------------------------
	public function SelecionarDadosNota($id)
	{
		
		$resultado = $this->conexao->prepare('SELECT notadavacina.id,
		notadavacina.titulo, notadavacina.descricao, 
		notadavacina.datadecriacao, notadavacina.Administrador_id, 
		notadavacina.Vacina_id
		 FROM `notadavacina` WHERE ativo AND id = ?')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_param('i', $id);

		$resultado->bind_result($notaDaVacina_id, $titulo, $descricao, 
								$dataDeCriacao, $usuario, $vacina);

		$resultado->execute();

		$resultado->store_result();

		$resultado->fetch();

		$existe = $resultado->num_rows;

		$resultado->free_result();
		
		if($existe > 0) {

			return array($notaDaVacina_id, $titulo, $descricao, 
						 $dataDeCriacao, $usuario, $vacina);

		}
		
		if($existe == 0) {
			
			$this->AdicionarMensagemDeErro('Não foi possível selecionar os dados
				desta nota. A identificação do mesmo parece não existir.');
		}
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar os
				dados desta nota.');
		}
		return false;
		
	}
	//--------------------------------------------------------------------------
	
	public function VisualizarNota($id)
	{
		
		list($notaDaVacina_id, $titulo, $descricao, 
			 $dataDeCriacao, $usuario, $vacina_id) = $this->SelecionarDadosNota($id);
		
		$vacina_nome = $this->RetornarCampoNome('vacina', $vacina_id);
			 
		echo "<br /><h3 align='center'>$vacina_nome</h3>";
		
		$descricaoTratada = nl2br($descricao);
		
		echo "<fieldset align='center' style='width:500px'><legend>Título: 
			$titulo</legend><div style='padding:25px'>Descrição: $descricaoTratada</div></fieldset>";
	}
	
	//--------------------------------------------------------------------------
	
	public function RetornarCampoNome($tabela, $id)
	{
		
		$sql = "SELECT nome FROM `$tabela` WHERE id = $id";
		
		$b = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$b->bind_result($nome);
		
		$b->execute();
		
		$b->fetch();
		
		$b->free_result();

		return Html::FormatarMaiusculasMinusculas($nome);
	}
	
	//--------------------------------------------------------------------------
	
	public function ExibirLinkNota($id)
	{
        
		$crip = new Criptografia();
		
		$queryString = $crip->Cifrar("pagina=notasDaVacina&vacina_id={$id}");
		?>
		<div style="position: absolute; right: 10px; top: 5px;">
			<button onclick="javascript: AbrirJanela('?<?php echo $queryString; ?>', 250, 250)">Notas Técnicas</button>
		</div>
		<?php
		
	}
	//--------------------------------------------------------------------------
	public function AdicionarMensagemDeErro($mensagem)
	{
		if( !in_array($mensagem, $this->msgDeErro) ) {
			$this->msgDeErro[] = $mensagem;
		}
	}
	//--------------------------------------------------------------------------
}
?>