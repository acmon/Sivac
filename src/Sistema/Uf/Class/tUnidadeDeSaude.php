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

//require_once('./tAgenteImunizador.php');
//------------------------------------------------------------------------------
/**
 * UnidadeDeSaude: Classe que representa uma Unidade de Saúde.
 *
 * Esta classe trata de uma unidade de saúde. Nela pode se  executar  qualquer
 * operação com uma unidade de saúde e seus dados. Cada unidade de saúde pertence
 * a uma só cidade e cada pessoa vacinada deve estar vinculada a uma unidade de saúde.
 * As unidades de saúde são essenciais para prestar assistência a pessoas e vaciná-las.
 *
 * @package Sivac/Class
 *
 * @author Douglas, v1.0,
 *
 * @copyright 2008
 *
 */
class UnidadeDeSaude extends AgenteImunizador
{

	private $_bairro;                           // string
	private $_logradouro;                       // string
	private $_cidade_id;                        // int
	private $_ddd;                              // char
	private $_telefone;                         // string
	private $_cnes;                             // string
	private $_cep;                              // string
	private $_estadoUnidade;
	private $_tipoDaUnidade;
	//--------------------------------------------------------------------------
	public function __construct()
	{
		parent::__construct();
        
	}
	//--------------------------------------------------------------------------
	public function __destruct()
	{
		parent::__destruct();
	}
	//--------------------------------------------------------------------------
	public function SetarDados($post) {

		$clean = Preparacao::GerarArrayLimpo($post, $this->conexao);

		$this->SetarNome      		($clean['nome']);
		$this->SetarTelefone	  	($clean['telefone']);
		$this->SetarCnes      		($clean['cnes']);
		$this->SetarEstadoUnidade 	($_SESSION['estado_id']);
		$this->SetarCidade    		($clean['cidade']);
		$this->SetarDdd   			($clean['ddd']);
		$this->SetarBairro    		($clean['bairro']);
		$this->SetarCep       		($clean['cep']);
		$this->SetarLogradouro    	($clean['logradouro']);
		$this->SetarTipoDaUnidade   ($clean['tipodaunidade']);
	}
	//--------------------------------------------------------------------------
	public function SetarTipoDaUnidade($tipo)
	{
		
		$this->_tipoDaUnidade = $tipo;
		
	}
	//--------------------------------------------------------------------------
	public function SetarEstadoUnidade($estado_id)
	{
		$this->_estadoUnidade = $estado_id;
	}
	//--------------------------------------------------------------------------
	public function SetarBairro($bairro)
	{
		$this->_bairro = $bairro;
	}
	//--------------------------------------------------------------------------
	public function SetarCep($cep)
	{
		$this->_cep = $cep;
	}
	//--------------------------------------------------------------------------
	public function Setarddd($ddd)
	{
		$this->_ddd = $ddd;
	}
	//--------------------------------------------------------------------------
	public function SetarTelefone($telefone)
	{
		$this->_telefone = $telefone;
	}
	//--------------------------------------------------------------------------
	public function SetarCnes($cnes)
	{
		$this->_cnes = $cnes;

	}
	//--------------------------------------------------------------------------
	public function SetarLogradouro($logradouro)
	{
		$this->_logradouro = $logradouro;

	}
	//--------------------------------------------------------------------------
	public function SetarCidade($cidade)
	{
		$this->_cidade_id = $cidade;
	}
	//--------------------------------------------------------------------------

	public function BuscarDadosParaEdicao($idEdicao){

		$id = $idEdicao;

		$selectEditarUnidade = $this->conexao->prepare("SELECT ddd_id, bairro_id,
		   nome, cnes, telefone, logradouro, cep, TipoDaUnidade_id  
		   FROM unidadedesaude WHERE id = ? AND ativo")
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selectEditarUnidade->bind_param('i', $id);

		$selectEditarUnidade->bind_result($ddd, $bairro_id, $nome, $cnes, $telefone, $logradouro, $cep, $tipoDaUnidade);

		$selectEditarUnidade->execute();

		$selectEditarUnidade->fetch();

		$selectEditarUnidade->close();

		//----------------------------------------------------------------------

		$selectEditarUnidade = $this->conexao->prepare("SELECT cidade_id, nome
		 FROM bairro WHERE id = ? AND ativo") 
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selectEditarUnidade->bind_param('i', $bairro_id);

		$selectEditarUnidade->bind_result($cidade_id, $bairro);

		$selectEditarUnidade->execute();

		$selectEditarUnidade->fetch();

		$selectEditarUnidade->close();

		//----------------------------------------------------------------------

		$selectEstado = $this->conexao->prepare("SELECT estado_id FROM cidade
			WHERE id = ?") 
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selectEstado->bind_param('i', $cidade_id);

		$selectEstado->bind_result($estado_id);

		$selectEstado->execute();

		$selectEstado->fetch();

		$selectEstado->close();

		$this->SetarNome      ($nome);
		$this->SetarTelefone  ($telefone);
		$this->SetarCnes      ($cnes);
		$this->SetarEstadoUnidade ($estado_id);
		$this->SetarCidade    ($cidade_id);
		$this->SetarDdd   	  ($ddd);
		$this->SetarBairro    ($bairro);
		$this->SetarLogradouro($logradouro);
		$this->SetarCep       ($cep);
		$this->SetarTipoDaUnidade ($tipoDaUnidade);
	}
	//////////////////////////////// RETORNAR //////////////////////////////////

	//--------------------------------------------------------------------------
	public function EstadoUnidade()
	{
		return $this->_estadoUnidade;
	}
	//--------------------------------------------------------------------------
	public function Logradouro()
	{
		return $this->_logradouro;
	}
	//--------------------------------------------------------------------------
	public function Cidade()
	{
		return $this->_cidade_id;
	}
	//--------------------------------------------------------------------------
	public function TipoDaUnidade()
	{
		return $this->_tipoDaUnidade;		
	}
	//--------------------------------------------------------------------------
	public function Cnes()
	{
		return $this->_cnes;
	}
	//--------------------------------------------------------------------------
	public function Bairro()
	{
		return $this->_bairro;
	}
	//--------------------------------------------------------------------------
	public function Ddd()
	{
		return $this->_ddd;
	}
	//--------------------------------------------------------------------------
	public function Telefone()
	{
		return $this->_telefone;
	}
	//--------------------------------------------------------------------------
	public function Cep()
	{
		return $this->_cep;
	}
	//--------------------------------------------------------------------------
	public function Estado()
	{
		return $this->_estadoUnidade;
	}
	//--------------------------------------------------------------------------
	public function InserirUnidade($bairro_id)
	{
		$inserirUnidade = $this->conexao->prepare('INSERT INTO `unidadedesaude`
		(id, ddd_id, bairro_id, nome, cnes, telefone, logradouro, cep, ativo, TipoDaUnidade_id)
		VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, 1,?)')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$ddd        = $this->Ddd();
		$nome       = $this->Nome();
		$cnes       = $this->Cnes();
		$telefone   = Preparacao::RemoverSimbolos($this->Telefone());
		$logradouro = $this->Logradouro();
		$cep        = Preparacao::RemoverSimbolos($this->Cep());
		$tipo	    = $this->TipoDaUnidade();

		$inserirUnidade->bind_param('sisssssi', $ddd, $bairro_id,
					    $nome, $cnes, $telefone, $logradouro,
					     $cep, $tipo);

		$inserirUnidade->execute();

		$inserido = $inserirUnidade->affected_rows;
		
		$idUnidadeInserida = $inserirUnidade->insert_id;

		$inserirUnidade->close();

		if($inserido > 0) {
			
			$ddd_id = '00';
			$nomeAgente = 'Não informado';
			
			// Inserir também um agente "Não informado" (padrão) para a unidade:
			
			$sql = 'INSERT INTO `acs`
					(id, Ddd_id, UnidadeDeSaude_id, nome, nascimento, ativo)
					VALUES(NULL, ?, ?, ?, NOW(), 1)';
			
			$stmt = $this->conexao->prepare($sql);
			$stmt->bind_param('sis', $ddd_id, $idUnidadeInserida, $nomeAgente);
			$stmt->execute();
			
			$agentePadraoInserido = $stmt->affected_rows;
			
			$stmt->close();
			
			if( $agentePadraoInserido > 0) return true;
			
			if($agentePadraoInserido < 0) {
				$this->AdicionarMensagemDeErro("Ocorreu algum erro ao cadastrar
					o agente 'Não informado' (padrão) para {$this->Nome()}.");
				return false;
			}
			
			if($agentePadraoInserido == 0) {
				$this->AdicionarMensagemDeErro("O agente 'Não informado' (padrão)
					para {$this->Nome()} não foi cadastrado.");
				return false;
			}	
		}
		
		if($inserido < 0) {
			$this->AdicionarMensagemDeErro("Ocorreu algum erro ao cadastrar {$this->Nome()}.");
			return false;
		}
		
		if($inserido == 0) {
			$this->AdicionarMensagemDeErro("{$this->Nome()} não foi cadastrada.");
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function InserirDescarte($motivo_id, $vacina_id, $unidade_id, $login, $quantidade, $lote, $obs) {
			
			$motivo = $this->conexao->prepare('INSERT INTO `descartevacinadaunidade` (id, MotivoDoDescarte_id, 
			vacinadaunidade_Vacina_id, vacinadaunidade_UnidadeDeSaude_id, login, datahora, quantidade, lote, obs) VALUES
			(NULL, ?, ?, ?, ?, NOW(), ?, ?, ?)');
			
			$motivo->bind_param('iiisiss',$motivo_id, $vacina_id, $unidade_id, $login, $quantidade, $lote, $obs);
			
			//... e executa essa sql
			$motivo->execute();
			
			$idIncluido = $motivo->insert_id;
			
			// Retorna as linahs afetadas pelo execute() para a variável $inserido
			$inserido = $motivo->affected_rows;
			
			// /fecha a conexão
			$motivo->close();
			
			// Se houver resultado...
			if ($inserido > 0) {
				
				//... atualiza o estoque
				return $idIncluido;
			}
			
			if($inserido < 0) {
				
				$this->AdicionarMensagemDeErro("Ocorreu algum erro ao cadastrar.");
				return false;
			}	
		
			if($inserido == 0) {
				
				$this->AdicionarMensagemDeErro("Não foi possível cadastrar esta inserção.");
				return false;
			}
		}
	//--------------------------------------------------------------------------
	private function SelecionarEstados()
	{
		$selectEstado = $this->conexao->prepare("SELECT id, nome FROM estado")
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selectEstado->bind_result($id, $nome);

		$selectEstado->execute();

		while($selectEstado->fetch()) {

			echo '<option value="'.$id.'">'.$nome. "</option>";
		}

		$selectEstado->free_result();
	}
	//--------------------------------------------------------------------------
	public function SelecionarUnidades($tipo = 'todas', $cidade_id)
	{
		$sql = 'SELECT tipodaunidade.nome, unidadedesaude.id, unidadedesaude.nome
				FROM `unidadedesaude`, `bairro`, `cidade`, `tipodaunidade`
				WHERE unidadedesaude.Bairro_id = bairro.id
					AND bairro.Cidade_id = cidade.id
					AND unidadedesaude.TipoDaUnidade_id = tipodaunidade.id
					AND cidade.Estado_id = ?
					AND cidade.id = ?
					AND bairro.ativo
					AND unidadedesaude.ativo
						ORDER BY unidadedesaude.nome';
		
		if ($tipo != 'todas') {
			$sql = "SELECT tipodaunidade.nome, unidadedesaude.id, unidadedesaude.nome 
				FROM `unidadedesaude`, `bairro`, `cidade`, `tipodaunidade`
				WHERE unidadedesaude.Bairro_id = bairro.id
					AND bairro.Cidade_id = cidade.id
					AND unidadedesaude.TipoDaUnidade_id = tipodaunidade.id
					AND cidade.Estado_id = ?
					AND cidade.id = ?
					AND bairro.ativo
					AND unidadedesaude.ativo
					AND TipoDaUnidade_id = tipodaunidade.id
					AND tipodaunidade.nome = '$tipo'
						ORDER BY unidadedesaude.nome";
			
		}
		if ($tipo == 'grupo') {
			$sql = 'SELECT tipodaunidade.nome, unidadedesaude.id, unidadedesaude.nome
				FROM `unidadedesaude`, `bairro`, `cidade`, `tipodaunidade`  
				WHERE unidadedesaude.Bairro_id = bairro.id
					AND bairro.Cidade_id = cidade.id
					AND unidadedesaude.TipoDaUnidade_id = tipodaunidade.id
					AND cidade.Estado_id = ?
					AND cidade.id = ?
					AND bairro.ativo
					AND unidadedesaude.ativo
					ORDER BY tipodaunidade.nome, unidadedesaude.nome';
			
		}
		$grupo_anterior = 'nenhum';
		
		$un = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$estado_id = $_SESSION['estado_banco'];
		
		$un->bind_param('si', $estado_id, $cidade_id);
		
		$un->bind_result($grupo, $id, $nome);

		$un->execute();
		
		echo "<option value='0'>- selecione -</option>";

		while($un->fetch()) {
			
			if($tipo == 'grupo') {
				if($grupo_anterior != $grupo) {
					echo "<optgroup label='$grupo'>";
					$grupo_anterior = $grupo;
				}
				
				echo "\n<option value='$id'>".Html::FormatarMaiusculasMinusculas($nome)."</option>";
				  		
				if($grupo_anterior != $grupo) echo "</optgroup>";
			}
			else
			echo "<option value='$id'>".Html::FormatarMaiusculasMinusculas($nome). "</option>";
		}
							
		$un->free_result();
	}
	//--------------------------------------------------------------------------
	public function PesquisarUnidadesSemTipo($cidade_id)
	{
		$sql = 'SELECT tipodaunidade.nome, unidadedesaude.id, unidadedesaude.nome
				FROM `unidadedesaude`, `bairro`, `cidade`, `tipodaunidade`  
				WHERE unidadedesaude.Bairro_id = bairro.id
					AND bairro.Cidade_id = cidade.id
					AND unidadedesaude.TipoDaUnidade_id = tipodaunidade.id
					AND cidade.Estado_id = ?
					AND cidade.id = ?
					AND bairro.ativo
					AND unidadedesaude.ativo
					ORDER BY tipodaunidade.nome, unidadedesaude.nome';
			
		
		$grupo_anterior = 'nenhum';
		
		$un = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$estado_id = $_SESSION['estado_banco'];
		
		$un->bind_param('si', $estado_id, $cidade_id);
		
		$un->bind_result($grupo, $id, $nome);

		$un->execute();
		
		$un->store_result();
		
		
		if($un->num_rows > 0) {
				
				// apresentamos cada subcategoria dessa forma:
				// "NOME|CODIGO, NOME|CODIGO, NOME|CODIGO|SELECIONADO,...",
				// exatamente da maneira que iremos tratar no JavaScript:
	
				$primeiro = true;
				
				while (  $un->fetch()  ) {
	
					if(!$primeiro) echo ',';
					else echo '- selecione -|0,';
					
					echo Html::FormatarMaiusculasMinusculas($nome) . " ($grupo)|$id";
					$primeiro = false;
				}
			}
			else {
				
				echo '(Nenhuma unidade nesta cidade)|0';
			}

		
		
		
		/*
		echo "<option value='0'>- selecione -</option>";

		while($un->fetch()) {
			
			if($tipo == 'grupo') {
				if($grupo_anterior != $grupo) {
					echo "<optgroup label='$grupo'>";
					$grupo_anterior = $grupo;
				}
				
				echo "\n<option value='$id'>".Html::FormatarMaiusculasMinusculas($nome)."</option>";
				  		
				if($grupo_anterior != $grupo) echo "</optgroup>";
			}
			else
			echo "<option value='$id'>".Html::FormatarMaiusculasMinusculas($nome). "</option>";
		}

		*/		
		$un->free_result();
	}
	
	//--------------------------------------------------------------------------
	public function PesquisarUnidades($cidade_id, $tipo = 'todas')
	{

        $sqlTipoDaUnidade = ($tipo == 'todas') ?
            ''                                 :
            " AND tipodaunidade.nome = '$tipo' ";

      $sql = "SELECT tipodaunidade.nome, unidadedesaude.id, unidadedesaude.nome "
             . 'FROM `unidadedesaude`, `bairro`, `cidade`, `tipodaunidade` '
             . 'WHERE unidadedesaude.Bairro_id = bairro.id '
             .     'AND bairro.Cidade_id = cidade.id '
             .     'AND unidadedesaude.TipoDaUnidade_id = tipodaunidade.id '
             .     "AND cidade.id = $cidade_id "
             .     'AND bairro.ativo '
             .     'AND unidadedesaude.ativo '
             .     'AND TipoDaUnidade_id = tipodaunidade.id '
             .     $sqlTipoDaUnidade
             .     'ORDER BY unidadedesaude.nome';


        $registros = $this->conexao->prepare($sql) or
			die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
        $registros->bind_result($tipo, $id, $nome);
        $registros->execute();
        $registros->store_result();

        if( $registros->num_rows > 0) {

            // apresentamos cada subcategoria dessa forma:
            // "NOME|CODIGO, NOME|CODIGO, NOME|CODIGO|SELECIONADO,...",
            // exatamente da maneira que iremos tratar no JavaScript:

            $primeiro = true;

            while (  $registros->fetch()  ) {

                if(!$primeiro) echo ',';
                else echo '- selecione -|0,';

                echo Html::FormatarMaiusculasMinusculas($nome) . "|$id";
                $primeiro = false;
            }
        }
        else {

            echo '(Nenhuma unidade nesta cidade)|0';
        }

        $registros->free_result();
        $registros->close();
	}
	//--------------------------------------------------------------------------
	private function SelecionarMotivo()
	{
		$sql = 'SELECT id, motivo FROM `motivododescarte` ORDER BY motivo';
		
		$motivo = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$motivo->bind_result($id, $nome);

		$motivo->execute();

		echo "<option value='0'>- selecione -</option>";
		
		while($motivo->fetch()) {

			echo '<option value="'.$id.'">'.Html::FormatarMaiusculasMinusculas($nome). "</option>";
		}
							
		$motivo->free_result();
	}
	//--------------------------------------------------------------------------
	public function SelectsDdd($ddd = false)
	{
		$selectDdd = $this->conexao->prepare("SELECT id FROM ddd")
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selectDdd->bind_result($id);

		$selectDdd->execute();

		if ($ddd) {
			while($selectDdd->fetch()) {
				if($ddd == $id){
					echo '<option value="'.$id.'" selected="true">'.$id. "</option>";
				}
				else {
					echo '<option value="'.$id.'">'.$id. "</option>";
				}
			}
		}
		else {
			while($selectDdd->fetch()) {


				echo '<option value="'.$id.'">'.$id. "</option>";
			}
		}
		$selectDdd->free_result();
	}
	//--------------------------------------------------------------------------
	public function InserirBairro() // Dividir em metodos (VerificarSeExisteBairro, InserirBairro e RetornarBairroId)
	{
		$bairro    = $this->Bairro();
		$cidade_id = $this->Cidade();
		$cep = $this->_cep;

		// Se não existe, insere um bairro com esse nome, e pega a id dele:
		$inserir = $this->conexao->prepare('INSERT INTO `bairro`(id, Cidade_id,
			nome, ativo) VALUES (NULL, ?, ?, 1)')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$inserir->bind_param('is', $cidade_id, $bairro);

		$inserir->execute();

		$inserir->close();

		// Pegando a id do bairro inserido anteriormente:
		$resultado = $this->conexao->prepare('SELECT id FROM `bairro`
			WHERE nome = ?
			AND Cidade_id = ? AND ativo') 
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_param('si',$bairro, $cidade_id);

		$resultado->execute();

		$resultado->bind_result($id);

		$resultado->fetch();

		$bairro_id = $id;

		$resultado->free_result();

		return $bairro_id;

	}
	//--------------------------------------------------------------------------
	public function VerificarBairro() {
		// Faz uma consulta para verificar se o bairro existe anteriormente:
		$bairro_id = 0;

		$resultado = $this->conexao->prepare('SELECT id FROM `bairro`
			WHERE nome = ? AND Cidade_id = ? AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$bairro    = $this->Bairro();
		$cidade_id = $this->Cidade();

		$cep = $this->_cep;

		$resultado->bind_param('si', $bairro, $cidade_id);
		$resultado->bind_result($id);

		$resultado->execute();

		$resultado->store_result();

		$resultado->fetch();

		$existe = $resultado->num_rows;

		$resultado->free_result();

		// Se existe, então usa esse bairro (pega a id dele)
		// Bairro existe:
		if($existe > 0) {

			$bairro_id = $id;
			return $bairro_id;
		}

		// Bairro não existe, insere:
		if($existe == 0) {
			
			$bairro_id = $this->InserirBairro();
			return $bairro_id;
		}
		
		// Erro:
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro("Algum erro ocorreu ao tentar inserir
				o bairro $bairro.");
				
			return false;
		}
		return false;
	}
	//--------------------------------------------------------------------------
	public function RetornarIdBairro() {

		return $this->VerificarBairro();
	}
	//--------------------------------------------------------------------------
	public function AtualizarBairro() // Metodo igual ao inserir bairro
	{

	// Faz uma consulta para verificar se o bairro existe anteriormente:

		$resultado = $this->conexao->prepare('SELECT id FROM `bairro`
			WHERE nome = ? AND Cidade_id = ? AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$bairro    = $this->Bairro();
		$cidade_id = $this->Cidade();

		$resultado->bind_param('si', $bairro, $cidade_id);
		$resultado->bind_result($bairro_id);

		$resultado->execute();

		$resultado->store_result();

		$resultado->fetch();

		$existe = $resultado->num_rows;
		
		

		$resultado->free_result();

		// Se existe, então usa esse bairro (pega a id dele)
		if($existe > 0) {

			return $bairro_id;
			//echo '<script>alert("Bairro existe, Pimba!")</script>';
		}

		if($existe == 0) {
		// Se não existe, insere um bairro com esse nome, e pega a id dele:

			//echo '<script>alert("Bairro não existe, Pimba!")</script>';
			$inserir = $this->conexao->prepare('INSERT INTO `bairro` (id, Cidade_id,
				nome, ativo) VALUES (NULL, ?, ?, 1)')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));


			$inserir->bind_param('is', $cidade_id, $bairro);

			$inserir->execute();

			$inserir->close();

			// Pegando a id do bairro inserido anteriormente:
			// ??????????????? mudar para insert_id ao invés de refazer consulta
			$resultado = $this->conexao->prepare('SELECT id FROM `bairro`
				WHERE nome = ? AND Cidade_id = ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$resultado->bind_param('si',$bairro, $cidade_id);

			$resultado->execute();

			$resultado->bind_result($bairro_id);

			$resultado->fetch();

			$resultado->free_result();
			
			return $bairro_id;
		}
		
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar
				atualizar o bairro.');
			
			return false;
		}

		return false;
	}
	//--------------------------------------------------------------------------
	public function VerificarNaoDuplicidadeDeUnidade($bairro_id, $id = false)
	{
		if($id) {
			$selecUS = $this->conexao->prepare('SELECT id FROM `unidadedesaude`
				WHERE nome = ? AND Bairro_id = ? AND id <> ?  AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$selecUS->bind_param('sii', $this->nome, $bairro_id, $id);
		}
		else {
			$selecUS = $this->conexao->prepare('SELECT id FROM `unidadedesaude`
				WHERE nome = ? AND Bairro_id = ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$selecUS->bind_param('si', $this->nome, $bairro_id);
		}

		$selecUS->execute();
		$selecUS->store_result();

		$registroJaExiste = $selecUS->num_rows;

		$selecUS->free_result();

		if($registroJaExiste > 0) {

			$this->AdicionarMensagemDeErro('Unidade já existe neste local.');
			return false;
		}
		
		if($registroJaExiste < 0) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao verificar não
				duplicidade de unidade de saúde.');
			
			return false;
		}
		if($registroJaExiste == 0) {
			
			// Verificaçao de CNES: --------------------------------------------
			if( !$this->VerificarNaoDuplicidadeDeCnes($id) ) {
	
				$this->AdicionarMensagemDeErro('Registro CNES já existe. Informe
					outro, pois este número deverá ser único para a unidade.');
	
				return false;
			}
		}

		return true;
	}
	//--------------------------------------------------------------------------
	private function VerificarNaoDuplicidadeDeCnes($id = false)
	{
		if($id) {
			$selecUS = $this->conexao->prepare('SELECT id FROM `unidadedesaude`
				WHERE cnes = ? AND id <> ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$selecUS->bind_param('si', $this->_cnes, $id);
		}
		else {
			$selecUS = $this->conexao->prepare('SELECT id FROM `unidadedesaude`
				WHERE cnes = ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$selecUS->bind_param('s', $this->_cnes);
		}

		$selecUS->execute();
		$selecUS->store_result();

		$registroJaExiste = $selecUS->num_rows;

		$selecUS->free_result();

		if($registroJaExiste > 0) return false;
		if($registroJaExiste == 0) return true;
		if($registroJaExiste < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao verificar a
				não duplicidade de CNES');
			return false;
		}

		return true;
	}
	//--------------------------------------------------------------------------
	public function EditarUnidade($bairro_id, $id)
	{
		$editarUnidade = $this->conexao->prepare('UPDATE `unidadedesaude`
		SET ddd_id = ?, bairro_id = ?, nome = ?, cnes = ?, telefone = ?,
		logradouro = ?, cep = ?, TipoDaUnidade_id = ? WHERE id = ?')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$ddd        = $this->Ddd();
		$nome       = $this->Nome();
		$cnes       = $this->Cnes();
		$logradouro = $this->Logradouro();
		$tipoDaUnidade = $this->TipoDaUnidade();

		$cep        = Preparacao::RemoverSimbolos($this->Cep());
		$telefone   = Preparacao::RemoverSimbolos($this->Telefone());
		
		$editarUnidade->bind_param('sisssssii', $ddd, $bairro_id,
					   $nome, $cnes, $telefone, $logradouro,
					   $cep, $tipoDaUnidade,$id);

		$editarUnidade->execute();
		$editado = $editarUnidade->affected_rows;
		$editarUnidade->close();

		if($editado) {
			//$this->ExibirMensagem("{$this->Nome()} atualizada com sucesso.");

			return true;
		}

		else {
			//$this->AdicionarMensagemDeErro("{$this->Nome()} não atualizada.");
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function ValidarNomeDaUnidade($nome)
	{
		$permitidos = " 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
						çÇáéíóúàèìòùâêîôûäëïöüãõÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÃÕ";
		if( strlen($nome) > 2  &&
			strlen($nome) == strspn($nome, $permitidos) ) {
			return true;
		} else {
			$this->AdicionarMensagemDeErro("Unidade $nome é inválida");
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function ValidarCnesDaUnidade($cnes)
	{
		if ( strlen($cnes) == 7 && ctype_digit( $cnes ) ) {
			return true;
		}
		$this->AdicionarMensagemDeErro("CNES $cnes é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarBairroDaUnidade($bairro)
	{
		$permitidos = " 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
		  			  çÇáéíóúàèìòùâêîôûäëïöüãõÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÃÕ.,º";
		if( strlen($bairro) > 2  &&
			strlen($bairro) == strspn($bairro, $permitidos) ) {
			return true;
		} else {
			$this->AdicionarMensagemDeErro("Bairro $bairro é inválido");
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function ValidarLogradouroDaUnidade($logradouro)
	{
		$permitidos = " 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
				 	  çÇáéíóúàèìòùâêîôûäëïöüãõÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÃÕ,.º():;/\\";
		if( strlen($logradouro) > 2  &&
			strlen($logradouro) == strspn($logradouro, $permitidos) ) {
			return true;
		} else {
			$this->AdicionarMensagemDeErro("Logradouro $logradouro é inválido");
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function ValidarTelefoneDaUnidade($telefone)
	{
		$permitidos = " 0123456789-";
		if((
			strlen($telefone) == 9 &&
			strlen($telefone) == strspn($telefone, $permitidos) &&
			$telefone[4] == '-') || strlen($telefone) == 0
		  ) {
			return true;
		} else {
			$this->AdicionarMensagemDeErro("Telefone $telefone é inválido");
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function ValidarCepDaUnidade($cep)
	{
		$permitidos = " 0123456789-";
		if( (strlen($cep) == 9 &&
			strlen($cep) == strspn($cep, $permitidos) &&
			$cep[5] == '-') /*|| strlen($cep) == 0*/ ) {
			return true;
		} else {
			$this->AdicionarMensagemDeErro("CEP $cep é inválido");
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function ExibirListaDeUnidades()
	{
		$sqlCidade = '';
		
		if( Sessao::Permissao('UNIDADES_LISTAR')== 1 )
		$sqlCidade = "AND cidade.id = {$_SESSION['cidade_id']}";
		
		$sql = "SELECT unidadedesaude.id, unidadedesaude.nome AS `Nome da Unidade`,
				cnes AS `CNES`, cidade.nome AS `Cidade`, cidade.Estado_id  AS `Estado`
				FROM unidadedesaude, `bairro`, `cidade` 
				WHERE unidadedesaude.ativo 
					AND bairro.ativo
					AND bairro.id = unidadedesaude.Bairro_id
					AND cidade.id = bairro.Cidade_id
					$sqlCidade
						ORDER BY unidadedesaude.nome";
		
		$UnidadeSaude = $this->conexao->query($sql)
						or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$arr = array();

		$linhas = array();
		$i = 0;
		
		while ( $linha = $UnidadeSaude->fetch_assoc() ) {
			
			//$linhas[] = $linha; print_r($linha); echo '<br>';
			
			$linhas[$i]['id'] = $linha['id'];
			$linhas[$i]['Nome da Unidade'] = $linha['Nome da Unidade'];
			$linhas[$i]['CNES'] = $linha['CNES'];
			// <b></b> usamos por causa do trim() que escapa os espacos e strip_tags  
			// que escapa as tags html
			$linhas[$i]['Cidade'] = $linha['Cidade'] . ' - ' . $linha['Estado'] . ' <b></b>';
			
			$i++;
		}
		
		$UnidadeSaude->free_result();
		
		foreach ( $linhas as $linha ) {
			/*
			$sql = "SELECT *.id FROM  `acs`, 
			`administrador`, `vacinadaunidade`, `usuariovacinado` 
			WHERE acs.UnidadeDeSaude_id = {$linha['id']}
			OR (administrador.UnidadeDeSaude_id = {$linha['id']} 
			OR vacinadaunidade.UnidadeDeSaude_id = {$linha['id']}
			OR usuariovacinado.UnidadeDeSaude_id = {$linha['id']}";
			*/
			/*
			$sql = "SELECT UnidadeDeSaude_id FROM `administrador` WHERE 
			UnidadeDeSaude_id = {$linha['id']} AND ativo";
			
			$unidade_existe = $this->conexao->query($sql)
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
				
			if ($unidade_existe->num_rows == 0) {
				
				$sql = "SELECT UnidadeDeSaude_id FROM `acs` WHERE 
				UnidadeDeSaude_id = {$linha['id']} AND ativo";
			
				$unidade_existe = $this->conexao->query($sql)
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			}*/
			
			$crip = new Criptografia();
			
			$queryStringEditar = $crip->Cifrar("pagina=Adm/editarUnidadeDeSaude&id={$linha['id']}"); 
			$queryStringExcluir = $crip->Cifrar("pagina=Adm/excluirUnidadeDeSaude&id={$linha['id']}"); 

			if ($this->RestricaoDeExclusaoDaUnidade($linha['id'])){

				$acaoExcluir = "<img src='$this->arquivoGerarIcone?imagem=excluir_desab'
					border='0' alt='Unidade de Saúde não pode ser excluída. Há ACS,"
					."Administrador, Usuario Vacinado e Vacina da Unidade ligados a esta Unidade'"
					."title='Unidade de Saúde não pode ser excluída. Há ACS, "
					."Administrador, Usuario Vacinado e Vacina da Unidade ligados a esta Unidade' />";
			}
			else {

				$acaoExcluir = "<a href='?$queryStringExcluir'>"
					. "<img src='$this->arquivoGerarIcone?imagem=excluir' border='0'
					border='0' alt='Excluir esta Unidade de Saúde' title='Excluir esta Unidade de Saúde' /></a>";
			}
			
			$linha['ações'] = " &nbsp;<a href='?$queryStringEditar'>"
				. "&nbsp;<img src='$this->arquivoGerarIcone?imagem=editar' border='0' "
				. "alt='Alterar dados desta Unidade' title='Alterar dados desta Unidade' /></a>" 
				. "&nbsp;$acaoExcluir&nbsp;";
				
			$arr[] = $linha;
			
		}

		Html::CriarTabelaDeArray($arr);
	}
	//--------------------------------------------------------------------------
	public function ExibirOpcaoOperacaoDeEstoque($marcado = false)
	{
		$crip = new Criptografia();
		?>
        <br />
		<h3>Selecione a operação de estoque</h3>
		<form id="formOperacaoDeEstoque" name="formOperacaoDeEstoque">
		<?php
		if ( Sessao::Permissao('UNIDADES_ESTOQUE_ALIMENTAR') ) {
		?>
            <br />

			<label>
                <input type="radio" name="operacoesEstoque" id="<?php echo $crip->Cifrar('pagina=Adm/alimentarUnidadeCentral')?>"
                <?php if($marcado == 1) echo ' checked="true" '?>
                onclick="CarregarPagina(this.id)" />
                Alimentar unidade de saúde central
            </label>
            <br />
				
		<?php
		}
		
		if( Sessao::Permissao('UNIDADES_ESTOQUE_DISPENSAR') ) {
		?>
			<label>            
                <input type="radio" name="operacoesEstoque" id="<?php echo $crip->Cifrar('pagina=Adm/dispensarParaUnidadeSatelite')?>"
                <?php if($marcado == 2) echo ' checked="true" '?>
                onclick="CarregarPagina(this.id)" />
                Dispensar estoque para unidade de saúde satélite
            </label>
            <br />
		<?php
		}
		
		if( Sessao::Permissao('UNIDADES_ESTOQUE_RETORNAR') ) {
		?>
			<label><input type="radio" name="operacoesEstoque" id="<?php echo $crip->Cifrar('pagina=Adm/retornarEstoqueParaCentral')?>"
			<?php if($marcado == 3) echo ' checked="true" '?>
			onclick="CarregarPagina(this.id)" />
			Retornar estoque para unidade de saúde Central</label><br />
		<?php
		}
		
		if( Sessao::Permissao('UNIDADES_ESTOQUE_VISUALIZAR_ESTADO') ) {
		?>
			<label><input type="radio" name="operacoesEstoque" id="<?php echo $crip->Cifrar('pagina=Adm/visualizarEstoqueUnidadesEstado')?>"
			<?php if($marcado == 4) echo ' checked="true" '?>
			onclick="CarregarPagina(this.id)" />
			Visualizar estoque das unidades de saúde dos municípios (estado: <?php echo $_SESSION['estado_banco_nome']?>)</label><br />
		<?php
		}
		if( Sessao::Permissao('UNIDADES_ESTOQUE_VISUALIZAR_MUNICIPIO') ) {
		?>
			<label><input type="radio" name="operacoesEstoque" id="<?php echo $crip->Cifrar('pagina=Adm/visualizarEstoqueUnidadesMunicipio')?>"
			<?php if($marcado == 5) echo ' checked="true" '?>
			onclick="CarregarPagina(this.id)" />
			Visualizar estoque de unidades de saúde do município (<?php echo $_SESSION['cidade_nome'] . '/' . $_SESSION['estado_banco']?>)</label><br />
		<?php
		}
		if( Sessao::Permissao('UNIDADES_ESTOQUE_VISUALIZAR_UNIDADE') ) {
		?>
			<label><input type="radio" name="operacoesEstoque" id="<?php echo $crip->Cifrar('pagina=Adm/visualizarEstoqueUnidadesUnidade')?>"
			<?php if($marcado == 6) echo ' checked="true" '?>
			onclick="CarregarPagina(this.id)" />
			Visualizar estoque da unidade de saúde (<?php
				echo Html::FormatarMaiusculasMinusculas($_SESSION['unidade_nome'])
				. ' - ' . $_SESSION['cidade_nome']
				. '/' . $_SESSION['estado_banco']?>)</label><br />
		<?php
		}
		
		if( Sessao::Permissao('UNIDADES_ESTOQUE_DESCARTAR_MUNICIPIO') ) {
		?>
			<label><input type="radio" name="operacoesEstoque" id="<?php echo $crip->Cifrar('pagina=Adm/descartarVacinaMunicipio')?>"
			<?php if($marcado == 7) echo ' checked="true" '?>
			onclick="CarregarPagina(this.id)" />
			Descartar vacina da unidade de saúde do município (<?php echo $_SESSION['cidade_nome'] . '/' . $_SESSION['estado_banco']?>)</label><br />
		<?php
		}
		
		if( Sessao::Permissao('UNIDADES_ESTOQUE_DESCARTAR_UNIDADE') ) {
		?>
			<label><input type="radio" name="operacoesEstoque" id="<?php echo $crip->Cifrar('pagina=Adm/descartarVacinaUnidade')?>"
			<?php if($marcado == 8) echo ' checked="true" '?>
			onclick="CarregarPagina(this.id)" />
			Descartar vacina da unidade de saúde (<?php
				echo Html::FormatarMaiusculasMinusculas($_SESSION['unidade_nome'])
				. ' - ' . $_SESSION['cidade_nome']
				. '/' . $_SESSION['estado_banco']?>)</label><br />
		<?php
		}
		?>
		</form>
		<hr />
		<?php
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioEstoqueDeVacinas()
	{
		?>
		<h3 align="center">Dispensar estoque para unidade satélite</h3>
		<p>Selecione a unidade central do município, a unidade satélite e vacina
			desejadas e digite a quantidade do estoque que será	adicionado para
			esta unidade de saúde.</p>
			
		<form id="estoqueVacinas" name="estoqueVacinas" method="post"
		action="<?php echo $_SERVER['REQUEST_URI']?>"
		onsubmit="return (ValidarCampoSelect(this.unidadeCentral, 'Unidade Central') 
					&& ValidarCampoSelect(this.unidade, 'Unidade Satélite') 
					&& ValidarCampoSelect(this.vacina, 'Vacina')
					&& ValidarQuantidade(this.quantidade) 
					&& ValidarData(this.datadeenvio)
					&& ValidarLote(this.lote, true)
					&& ValidarData(this.validade, true)
					&& ValidarTextoLongo(this.obs, true))">
		
			<p>
			<div class='CadastroEsq'>*Unidade de saúde central: </div>
			
			<div class='CadastroDir'><select name="unidadeCentral" id="unidadeCentral"
			style="width:300px" 
			onblur="ValidarCampoSelect(this, 'Unidade')">
				<?php $this->SelecionarUnidades('Central', $_SESSION['cidade_id']);?>
			</select></div>
			</p>
			
			<p>
			<div class='CadastroEsq'>*Unidade de saúde: </div>
			
			<div class='CadastroDir'><select name="unidade" id="unidade"
			style="width:300px" 
			onblur="ValidarCampoSelect(this, 'Unidade')">
				<?php $this->SelecionarUnidades('Satélite', $_SESSION['cidade_id']);?>
			</select></div>
			</p>
			
			  <p>
			  <div class='CadastroEsq'>*Vacina: </div> 
			  <div class='CadastroDir'><!-- <select name="vacina" id="vacina"
			  style="width:300px" 
			  onblur="ValidarCampoSelect(this, 'Vacina')"> -->
			  	<?php
			  	/*
			  	$consulta = 'SELECT id, Grupo_id, nome
			  				 FROM `vacina`m
			  				 WHERE Grupo_id <> "Descontinuadas"
			  				 	AND ativo
			  				 		ORDER BY Grupo_id DESC,
			  				 				 nome ASC';
			  	
			  	$sql = $this->conexao->prepare($consulta)
			  		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
			  	$grupo_id_anterior = 'nenhum';
			  	$sql->bind_result($id, $grupo_id, $nome);
			  	$sql->execute();
			  	
			  	echo "\n<option value='0'>- selecione -</option>";
			  	
			  	while ($sql->fetch()) {
			  		
			  		if($grupo_id_anterior != $grupo_id) {
						echo "<optgroup label='$grupo_id'>";
						$grupo_id_anterior = $grupo_id;
					}
			  		echo "\n<option value='$id'>$nome</option>";
			  		
			  		if($grupo_id_anterior != $grupo_id) echo '</optgroup>';
			  	} */

                $vacina =  new Vacina;
                $vacina->UsarBaseDeDados();
                $vacina->ListarVacinas(false, false, true, false); // LIstar apenas vacinas normais e vacinas maes
                
			  	?> 
			 <!-- </select> --> </div>
			  </p>


              <!-- ###################  -->


		  	  <p>
		  	  <div class='CadastroEsq'>Código de Barras</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="codigoDeBarras" id="codigoDeBarras" size="13" maxlength="13"
			  	onblur="produto.value = codigoDeBarras.value.substring(8,12)"/>
			  </div>
			  </p>


		  	  <p>
		  	  <div class='CadastroEsq'>Produto:</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="produto" id="produto" size="13" maxlength="13"
			  	onkeypress="return Digitos(event, this);"
			  	/>
			  </div>
			  </p>

              <!-- ###################  -->
		  
		  	  <p>
		  	  <div class='CadastroEsq'>*Quantidade:</div> 
			  <div class='CadastroDir'>
			  	<input type="text" name="quantidade" id="quantidade" size="5" maxlength="5"
			  	onkeypress="return Digitos(event, this);"
			  	onblur="ValidarQuantidade(this)"/>
			  </div>
			  </p>
			  
			  <p>
			  <div class='CadastroEsq'>*Data de Envio:</div>
				<div class='CadastroDir'>
					<input type="text" name="datadeenvio" maxlength="10"
						onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					    onkeyup="return Mascara('DATA', this, event);"
						onblur="ValidarData(this)"
						 />
					<span id="TextoExemplos">
						<?php echo " Ex.: 01/01/1980 " ?>
					</span>
			  </div>
			  </p>
			  
			  <p>
		  	  <div class='CadastroEsq'>Lote:</div> 
			  <div class='CadastroDir'>
			  	<input type="text" name="lote" id="lote" size="10" maxlength="10"
				onblur="ValidarLote(this, true)"/>
			  </div>
			  </p>
			  
			  <p>
			  <div class='CadastroEsq'>Validade do Lote:</div>
				<div class='CadastroDir'>
					<input type="text" name="validade" maxlength="10"
						onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					    onkeyup="return Mascara('DATA', this, event);"
						onblur="ValidarData(this, true)"
						 />
					<span id="TextoExemplos">
						<?php echo " Ex.: 01/01/1980 " ?>
					</span>
				</div>
				</p>
				
		     <p><div align="center" style="clear:both">Observação:</div></p>
		     <p>
		     <div align="center">
		    	<textarea name="obs" cols="50" rows="5"
			  		style="width:450px;"
			  		onblur="ValidarTextoLongo(this, true)"></textarea>
		     </div>
		     </p>
			  
			  
				<?php $this->ExibirBotoesDoFormulario('Confirmar');?>		  
			  </label>
		</form>
		
	<?php 
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioAlimentarCentral() {
		
		?>
		<h3 align="center">Alimentar unidade central</h3>
		<p>Selecione a cidade, unidade e vacina desejadas e digite a quantidade
			do estoque que será adicionado para esta unidade de saúde.</p>
			
		<form id="alimentarCentral" name="alimentarCentral" method="post"
		action="<?php echo $_SERVER['REQUEST_URI']?>"
		onsubmit="return (ValidarCampoSelect(this.cidade, 'Cidade') 
					&& ValidarCampoSelect(this.unidadeCentral, 'Unidade Central') 
					&& ValidarCampoSelect(this.vacina, 'Vacina')
					&& ValidarQuantidade(this.quantidade) 
					&& ValidarData(this.datadeenvio)
					&& ValidarLote(this.lote, true)
					&& ValidarData(this.validade, true)
					&& ValidarTextoLongo(this.obs, true))" >
		
			<p>

                        <?php if($_SESSION['nivel'] == 10) { ?>
                        
                            <script>PesquisarUnidades(<?php echo $_SESSION['cidade_id']?>, 'Central','unidadeCentral')</script>

                        </p>
                        <?php } else {?>
			<div class='CadastroEsq'>*Cidade: </div>
			
			<div class='CadastroDir'><select name="cidade" id="cidade"
			style="width:300px" 
			onblur="ValidarCampoSelect(this, 'cidade')"
			onchange="PesquisarUnidades(this.value, 'Central','unidadeCentral')">
			</select></div>

                        
			</p>
			
			<script>PesquisarCidades('<?php echo $_SESSION['estado_banco']?>')</script>

                        <?php } ?>
			<p>
			<div class='CadastroEsq'>*Unidade de saúde central: </div>
			
			<div class='CadastroDir'><select name="unidadeCentral" id="unidadeCentral"
			style="width:300px" 
			onblur="ValidarCampoSelect(this, 'Unidade')">
			</select></div>
			</p>
			
			  <p>
			  <div class='CadastroEsq'>*Vacina: </div> 
			  <div class='CadastroDir'><!-- <select name="vacina" id="vacina"
			  style="width:300px" 
			  onblur="ValidarCampoSelect(this, 'Vacina')"> -->
			  	<?php
			  	/*
			  	$consulta = 'SELECT id, Grupo_id, nome
			  				 FROM `vacina`m
			  				 WHERE Grupo_id <> "Descontinuadas"
			  				 	AND ativo
			  				 		ORDER BY Grupo_id DESC,
			  				 				 nome ASC';
			  	
			  	$sql = $this->conexao->prepare($consulta)
			  		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
			  	$grupo_id_anterior = 'nenhum';
			  	$sql->bind_result($id, $grupo_id, $nome);
			  	$sql->execute();
			  	
			  	echo "\n<option value='0'>- selecione -</option>";
			  	
			  	while ($sql->fetch()) {
			  		
			  		if($grupo_id_anterior != $grupo_id) {
						echo "<optgroup label='$grupo_id'>";
						$grupo_id_anterior = $grupo_id;
					}
			  		echo "\n<option value='$id'>$nome</option>";
			  		
			  		if($grupo_id_anterior != $grupo_id) echo '</optgroup>';
			  	}*/

                $vacina =  new Vacina;
                $vacina->UsarBaseDeDados();
                $vacina->ListarVacinas(false, false, true, false); // LIstar apenas vacinas normais e vacinas maes
			  	?>
			 <!-- </select> --> </div>
			  </p>

		  	  <p>
		  	  <div class='CadastroEsq'>*Quantidade:</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="quantidade" id="quantidade" size="5" maxlength="5"
			  	onkeypress="return Digitos(event, this);"
			  	onblur="ValidarQuantidade(this)"/>
			  </div>
			  </p>

              <!-- ###################  -->


		  	  <p>
		  	  <div class='CadastroEsq'>Código de Barras</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="codigoDeBarras" id="codigoDeBarras" size="13" maxlength="13"
			  	onblur="produto.value = codigoDeBarras.value.substring(8,12)"/>
			  </div>
			  </p>


		  	  <p>
		  	  <div class='CadastroEsq'>Produto:</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="produto" id="produto" size="13" maxlength="13"
			  	onkeypress="return Digitos(event, this);"
			  	/>
			  </div>
			  </p>

              <!-- ###################  -->
			  
			  <p>
			  <div class='CadastroEsq'>*Data de Entrada:</div>
				<div class='CadastroDir'>
					<input type="text" name="datadeenvio" id="datadeenvio" maxlength="10"
						onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					    onkeyup="return Mascara('DATA', this, event);"
						onblur="ValidarData(this)"
						 />
					<span id="TextoExemplos">
						<?php echo " Ex.: 01/01/1980 " ?>
					</span>
			  </div>
			  </p>
			  
			  <p>
		  	  <div class='CadastroEsq'>Lote:</div> 
			  <div class='CadastroDir'>
			  	<input type="text" name="lote" id="lote" size="10" maxlength="10"
				onblur="ValidarLote(this, true)"/>
			  </div>
			  </p>
			  
			  <p>
			  <div class='CadastroEsq'>Validade do Lote:</div>
				<div class='CadastroDir'>
					<input type="text" name="validade" id="validade" maxlength="10"
						onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					    onkeyup="return Mascara('DATA', this, event);"
						onblur="ValidarData(this, true)"
						 />
					<span id="TextoExemplos">
						<?php echo " Ex.: 01/01/1980 " ?>
					</span>
				</div>
				</p>
				
		     <p><div align="center" style="clear:both">Observação:</div></p>
		     <p>
		     <div align="center">
		    	<textarea id="obs" name="obs" cols="50" rows="5"
			  		style="width:450px;"
			  		onblur="ValidarTextoLongo(this, true)"></textarea>
		     </div>
		     </p>
			  
			  
				<?php $this->ExibirBotoesDoFormulario('Confirmar');?>		  
			  </label>
		</form>
		
	<?php 
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioRetornarEstoqueParaUnidadeCentral()
	{
		?>
		<h3 align="center">Retornar estoque para unidade central</h3>
		
		<p>Selecione a unidade satélite que devolveu o estoque, a unidade central
			para a qual o estoque será devolvido, a vacina desejada e digite a
			quantidade do estoque que será retornado.</p>
		
		<form id="retornarEstoqueCentral" name="retornarEstoqueCentral" method="post"
		action="<?php echo $_SERVER['REQUEST_URI']?>"
		onsubmit="return (ValidarCampoSelect(this.unidadeCentral, 'Unidade Central') 
					&& ValidarCampoSelect(this.unidade, 'Unidade Satélite') 
					&& ValidarCampoSelect(this.vacina, 'Vacina')
					&& ValidarQuantidade(this.quantidade) 
					&& ValidarData(this.datadeenvio)
					&& ValidarLote(this.lote, true)
					&& ValidarData(this.validade, true)
					&& ValidarTextoLongo(this.obs, true))">

			
			<p>
			<div class='CadastroEsq'>*Unidade satélite: </div>
			
			<div class='CadastroDir'><select name="unidade" id="unidade" 
			style="width:300px"
			onblur="ValidarCampoSelect(this, 'Unidade satélite')">
				<?php $this->SelecionarUnidades('Satélite', $_SESSION['cidade_id']);?>
			</select></div>
			</p>
		
			<p>
			<div class='CadastroEsq'>*Unidade central: </div>
			
			<div class='CadastroDir'><select name="unidadeCentral" id="unidadeCentral"
			style="width:300px" 
			onblur="ValidarCampoSelect(this, 'Unidade central')">
				<?php $this->SelecionarUnidades('Central', $_SESSION['cidade_id']);?>
			</select></div>
			</p>
			
			  <p>
			  <div class='CadastroEsq'>*Vacina: </div> 
			  <div class='CadastroDir'><!-- <select name="vacina" id="vacina"
			  style="width:300px" 
			  onblur="ValidarCampoSelect(this, 'Vacina -->
			  	<?php
			  	/*
			  	$consulta = 'SELECT id, Grupo_id, nome
			  				 FROM `vacina`m
			  				 WHERE Grupo_id <> "Descontinuadas"
			  				 	AND ativo
			  				 		ORDER BY Grupo_id DESC,
			  				 				 nome ASC';
			  	
			  	$sql = $this->conexao->prepare($consulta)
			  		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
			  	$grupo_id_anterior = 'nenhum';
			  	$sql->bind_result($id, $grupo_id, $nome);
			  	$sql->execute();
			  	
			  	echo "\n<option value='0'>- selecione -</option>";
			  	
			  	while ($sql->fetch()) {
			  		
			  		if($grupo_id_anterior != $grupo_id) {
						echo "<optgroup label='$grupo_id'>";
						$grupo_id_anterior = $grupo_id;
					}
			  		echo "\n<option value='$id'>$nome</option>";
			  		
			  		if($grupo_id_anterior != $grupo_id) echo '</optgroup>';
			  	}
                 */

                $vacina =  new Vacina;
                $vacina->UsarBaseDeDados();
                $vacina->ListarVacinas(false, false, true, false); // LIstar apenas vacinas normais e vacinas maes
                

			  	?>
			  <!-- </select> --> </div>
              </p>

              <!-- ###################  -->


		  	  <p>
		  	  <div class='CadastroEsq'>Código de Barras</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="codigoDeBarras" id="codigoDeBarras" size="13" maxlength="13"
			  	onblur="produto.value = codigoDeBarras.value.substring(8,12)"/>
			  </div>
			  </p>


		  	  <p>
		  	  <div class='CadastroEsq'>Produto:</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="produto" id="produto" size="13" maxlength="13"
			  	onkeypress="return Digitos(event, this);"
			  	/>
			  </div>
			  </p>

              <!-- ###################  -->
		  
		  	  <p>
		  	  <div class='CadastroEsq'>*Quantidade:</div> 
			  <div class='CadastroDir'>
			  	<input type="text" name="quantidade" id="quantidade" size="5" maxlength="5"
			  	onkeypress="return Digitos(event, this);"
			  	onblur="ValidarQuantidade(this)"/>
			  </div>
			  </p>
			  
			  <p>
			  <div class='CadastroEsq'>*Data de Envio:</div>
				<div class='CadastroDir'>
					<input type="text" name="datadeenvio" maxlength="10"
						onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					    onkeyup="return Mascara('DATA', this, event);"
						onblur="ValidarData(this)"
						 />
					<span id="TextoExemplos">
						<?php echo " Ex.: 01/01/1980 " ?>
					</span>
			  </div>
			  </p>
			  
			  <p>
		  	  <div class='CadastroEsq'>Lote:</div> 
			  <div class='CadastroDir'>
			  	<input type="text" name="lote" id="lote" size="10" maxlength="10"
				onblur="ValidarLote(this, true)"/>
			  </div>
			  </p>
			  
			  <p>
			  <div class='CadastroEsq'>Validade do Lote:</div>
				<div class='CadastroDir'>
					<input type="text" name="validade" maxlength="10"
						onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					    onkeyup="return Mascara('DATA', this, event);"
						onblur="ValidarData(this, true)"
						 />
					<span id="TextoExemplos">
						<?php echo " Ex.: 01/01/1980 " ?>
					</span>
				</div>
				</p>
				
		     <p><div align="center" style="clear:both">Observação:</div></p>
		     <p>
		     <div align="center">
		    	<textarea name="obs" cols="50" rows="5"
			  		style="width:450px;"
			  		onblur="ValidarTextoLongo(this, true)"></textarea>
		     </div>
		     </p>
			  
			  
				<?php $this->ExibirBotoesDoFormulario('Confirmar');?>		  
			  </label>
		</form>
		
	<?php 
	}
	
	//--------------------------------------------------------------------------
	public function ExibirFormularioDescartarVacinaMunicipio() {
		?>
		<h3 align="center">Descartar vacina do município</h3>
		<p>Selecione a unidade de saúde do descarte, a vacina desejada e digite
			a quantidade para realizar o descarte da vacina nesta unidade.</p>
			
		<form id="descartarVacinaMunicipio" name="descartarVacinaMunicipio"
			method="post" action="<?php echo $_SERVER['REQUEST_URI']?>"
			onsubmit="return (ValidarCampoSelect(this.unidadeCentral, 'Unidade Central')  
					&& ValidarCampoSelect(this.vacina, 'Vacina')
					&& ValidarCampoSelect(this.motivo, 'Motivo do Descarte')
					&& ValidarQuantidade(this.quantidade) 
					&& ValidarLote(this.lote, true)
					&& ValidarTextoLongo(this.obs, true))">
		
			<p>
			<div class='CadastroEsq'>*Unidade de saúde: </div>
			
			<div class='CadastroDir'><select name="unidadeCentral" id="unidadeCentral"
			style="width:300px" 
			onblur="ValidarCampoSelect(this, 'Unidade')">
				<?php $this->SelecionarUnidades('grupo', $_SESSION['cidade_id']);?>
			</select></div>
			</p>
			
			  <p>
			  <div class='CadastroEsq'>*Vacina: </div> 
			  <div class='CadastroDir'><select name="vacina" id="vacina"
			 style="width:300px"
			  onblur="ValidarCampoSelect(this, 'Vacina')">
			  	<?php
			  	
			  	$consulta = 'SELECT DISTINCT(id), Grupo_id, nome
			  				 FROM `vacina` INNER JOIN `vacinadaunidade`
							 ON vacina.id = vacinadaunidade.Vacina_id
			  				 WHERE Grupo_id <> "Descontinuadas"
			  				 	AND ativo
								AND vacinadaunidade.quantidade > 0
			  				 		ORDER BY Grupo_id DESC,
			  				 				 nome ASC';
			  	
			  	$sql = $this->conexao->prepare($consulta)
			  		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
			  	$grupo_id_anterior = 'nenhum';
			  	$sql->bind_result($id, $grupo_id, $nome);
			  	$sql->execute();
			  	
			  	echo "\n<option value='0'>- selecione -</option>";
			  	
			  	while ($sql->fetch()) {
			  		
			  		if($grupo_id_anterior != $grupo_id) {
						echo "<optgroup label='$grupo_id'>";
						$grupo_id_anterior = $grupo_id;
					}
			  		echo "\n<option value='$id'>$nome</option>";
			  		
			  		if($grupo_id_anterior != $grupo_id) echo '</optgroup>';
			  	}
			  	?>
			  </select></div>
			  </p>
			  
			  <p>
			  <div class='CadastroEsq'>*Motivo do descarte: </div>
			
			  <div class='CadastroDir'><select name="motivo" id="motivo"
			  	style="width:300px" 
			   onblur="ValidarCampoSelect(this, 'Unidade')">
				<?php $this->SelecionarMotivo();?>
			  </select></div>
			  </p>



              <!-- ###################  -->


		  	  <p>
		  	  <div class='CadastroEsq'>Código de Barras</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="codigoDeBarras" id="codigoDeBarras" size="13" maxlength="13"
			  	onblur="produto.value = codigoDeBarras.value.substring(8,12)"/>
			  </div>
			  </p>


		  	  <p>
		  	  <div class='CadastroEsq'>Produto:</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="produto" id="produto" size="13" maxlength="13"
			  	onkeypress="return Digitos(event, this);"
			  	/>
			  </div>
			  </p>

              <!-- ###################  -->

		  
		  	  <p>
		  	  <div class='CadastroEsq'>*Quantidade:</div> 
			  <div class='CadastroDir'>
			  	<input type="text" name="quantidade" id="quantidade" size="5" maxlength="5"
			   onkeypress="return Digitos(event, this);"
			   onblur="ValidarQuantidade(this)"/>
			  </div>
			  </p>
			  
			  
			  <p>
		  	  <div class='CadastroEsq'>Lote:</div> 
			  <div class='CadastroDir'>
			  	<input type="text" name="lote" id="lote" size="10" maxlength="10"
			     onblur="ValidarLote(this, true)"/>
			  </div>
			  </p>
			  
				
		     <p><div align="center" style="clear:both">Observação:</div></p>
		     <p>
		     <div align="center">
		    	<textarea name="obs" cols="50" rows="5"
			  		style="width:450px;"
			  		onblur="ValidarTextoLongo(this, true)"></textarea>
		     </div>
		     </p>
			  
			  
				<?php $this->ExibirBotoesDoFormulario('Confirmar');?>		  
			  </label>
		</form>
		
	<?php 
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioDescartarVacinaUnidade() {
		?>
		<h3 align="center">Descartar vacina desta Unidade de Saúde</h3>
		<p>Selecione a unidade de saúde do descarte, a vacina desejada e digite
			a quantidade para realizar o descarte da vacina nesta unidade.</p>
			
		<form id="descartarVacinaMunicipio" name="descartarVacinaMunicipio"
			method="post" action="<?php echo $_SERVER['REQUEST_URI']?>"
			onsubmit="return (ValidarCampoSelect(this.vacina, 'Vacina')
					&& ValidarCampoSelect(this.motivo, 'Motivo do Descarte')
					&& ValidarQuantidade(this.quantidade) 
					&& ValidarLote(this.lote, true)
					&& ValidarTextoLongo(this.obs, true))">
			
			  <p>
			  <div class='CadastroEsq'>*Vacina: </div> 
			  <div class='CadastroDir'><!-- <select name="vacina" id="vacina"
			 style="width:300px"
			  onblur="ValidarCampoSelect(this, 'Vacina')"> -->
			  	<?php
			  	/*
			  	$consulta = 'SELECT DISTINCT(id), Grupo_id, nome
			  				 FROM `vacina`
							 INNER JOIN `vacinadaunidade` ON vacina.id = vacinadaunidade.Vacina_id
							 WHERE Grupo_id <> "Descontinuadas"
			  				 	AND ativo
								AND vacinadaunidade.quantidade > 0
			  				 		ORDER BY Grupo_id DESC,
			  				 				 nome ASC';
			  	
			  	$sql = $this->conexao->prepare($consulta)
			  		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
			  	$grupo_id_anterior = 'nenhum';
			  	$sql->bind_result($id, $grupo_id, $nome);
			  	$sql->execute();
			  	
			  	echo "\n<option value='0'>- selecione -</option>";
			  	
			  	while ($sql->fetch()) {
			  		
			  		if($grupo_id_anterior != $grupo_id) {
						echo "<optgroup label='$grupo_id'>";
						$grupo_id_anterior = $grupo_id;
					}
			  		echo "\n<option value='$id'>$nome</option>";
			  		
			  		if($grupo_id_anterior != $grupo_id) echo '</optgroup>';
			  	} */

                $vacina =  new Vacina;
                $vacina->UsarBaseDeDados();
                $vacina->ListarVacinas(false, false, true, false); // LIstar apenas vacinas normais e vacinas maes

			  	?>
			  <!-- </select> --> </div>
			  </p>
			  
			  <p>
			  <div class='CadastroEsq'>*Motivo do descarte: </div>
			
			  <div class='CadastroDir'><select name="motivo" id="motivo"
			  	style="width:300px" 
			   onblur="ValidarCampoSelect(this, 'Unidade')">
				<?php $this->SelecionarMotivo();?>
			  </select></div>
			  </p>
			

              <!-- ###################  -->


		  	  <p>
		  	  <div class='CadastroEsq'>Código de Barras</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="codigoDeBarras" id="codigoDeBarras" size="13" maxlength="13"
			  	onblur="produto.value = codigoDeBarras.value.substring(8,12)"/>
			  </div>
			  </p>


		  	  <p>
		  	  <div class='CadastroEsq'>Produto:</div>
			  <div class='CadastroDir'>
			  	<input type="text" name="produto" id="produto" size="13" maxlength="13"
			  	onkeypress="return Digitos(event, this);"
			  	/>
			  </div>
			  </p>

              <!-- ###################  -->
		  	  <p>
		  	  <div class='CadastroEsq'>*Quantidade:</div> 
			  <div class='CadastroDir'>
			  	<input type="text" name="quantidade" id="quantidade" size="5" maxlength="5"
			   onkeypress="return Digitos(event, this);"
			   onblur="ValidarQuantidade(this)"/>
			  </div>
			  </p>
			  
			  
			  <p>
		  	  <div class='CadastroEsq'>Lote:</div> 
			  <div class='CadastroDir'>
			  	<input type="text" name="lote" id="lote" size="10" maxlength="10"
			     onblur="ValidarLote(this, true)"/>
			  </div>
			  </p>
			  
				
		     <p><div align="center" style="clear:both">Observação:</div></p>
		     <p>
		     <div align="center">
		    	<textarea name="obs" cols="50" rows="5"
			  		style="width:450px;"
			  		onblur="ValidarTextoLongo(this, true)"></textarea>
		     </div>
		     </p>
			  
			  
				<?php $this->ExibirBotoesDoFormulario('Confirmar');?>		  
			  </label>
		</form>
		
	<?php 
	}
	
	//--------------------------------------------------------------------------
	public function ExibirBotaoEstornarEstoque($idIncluido)
	{
		$crip = new Criptografia();
		
		$qs = $crip->Decifrar($_SERVER['QUERY_STRING']);
		
		$qs = $crip->Cifrar("$qs&idIncluidoExtornar=$idIncluido");
		
		?>
		<form id="form1" name="form1" method="post"
		action="?<?php echo $qs?>" style="float:right;">
		
		<input type="hidden" name='unidadeCentral' id='unidadeCentral'
		value="<?php echo $_POST['unidadeCentral'] ?>" />
		
		<input type="hidden" name='vacina' id='vacina'
		value="<?php echo $_POST['vacina']?>" />

		<input type="hidden" name='quantidade' id='quantidade'
		value="<?php echo $_POST['quantidade']?>" />
		
		<input type="hidden" name='lote' id='lote'
		value="<?php echo $_POST['lote']?>" />
		

		<input type="hidden" name='validade' id='validade'
		value="<?php echo $_POST['validade']?>" />
		
		<input type="hidden" name='obs' id='obs'
		value="<?php echo $_POST['obs']?>" />
		
		<input type="hidden" name='datadeenvio' id='datadeenvio'
		value="<?php echo $_POST['datadeenvio']?>" />

		<input type="hidden" name='unidade' id='unidade'
		value="<?php echo $_POST['unidade']?>" />
		
		<input type="hidden" name='motivo' id='motivo'
		value="0" />
		
		<button name='estornar' type='submit' value='estornar'
			style='color: #14E; width: 130px; margin:10px'>
		<img src='<?php echo $this->arquivoGerarIcone?>?imagem=excluir' alt='Não'
			  style='vertical-align: middle' />Não</button>
		</form>
		<?php
	}
	//--------------------------------------------------------------------------
	public function EstornarEstoque($unidade_id, $vacina_id, $quantidade)
	{
		if((int)$quantidade <= 0) return false;
		
		$decrementar = $this->conexao->prepare ('UPDATE `vacinadaunidade` 
										 SET quantidade = (quantidade - ?)
										 WHERE UnidadeDeSaude_id = ? 
									   	 AND Vacina_id = ?');

		$decrementar->bind_param('iii', $quantidade, $unidade_id, $vacina_id);
		$decrementar->execute();
		$estornado = $decrementar->affected_rows;
		$decrementar->close();
		
		if($estornado) return true;
		
		return false;
	}
	//--------------------------------------------------------------------------
	public function SalvarEstoque($unidade_id, $vacina_id, $quantidade, $codigoDeBarras, $produto)
	{
		
		if( (int)$quantidade <= 0 ) return false;
		
		$sql = $this->conexao->prepare('UPDATE `vacinadaunidade` 
				SET quantidade = (quantidade + ?) 
				WHERE UnidadeDeSaude_id = ? 
				AND Vacina_id = ?')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$sql->bind_param('iii', $quantidade , $unidade_id, $vacina_id);
		$sql->execute();
		
		$atualizou =  $sql->affected_rows;
		$sql->close();
		
		if($atualizou){
			return true;
		}
		
		$sql = $this->conexao->prepare('INSERT INTO `vacinadaunidade` 
			VALUES(?, ?, ?,?,?)') or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$sql->bind_param('iiiss', $unidade_id, $vacina_id, $quantidade, $codigoDeBarras, $produto);
		$sql->execute();
		
		$inseriu =  $sql->affected_rows;
		$sql->close();
		
		if($inseriu) {
			return true;
		}
		return false;
	}
	//--------------------------------------------------------------------------
	public function VerificarEstoque($quantidade, $vacina_id, $unidade_id) {
		
		if ((int)$quantidade <= 0) return false;
		
		$sql = $this->conexao->prepare('SELECT quantidade FROM `vacinadaunidade` WHERE 
		UnidadeDeSaude_id = ? AND Vacina_id = ?');
		
		$sql->bind_param('ii',$unidade_id, $vacina_id);
		
		$sql->bind_result($quantidade_retorno);
		
		$sql->execute();
		
		$sql->fetch();
		
		$sql->free_result();
		
		if ($quantidade_retorno - $quantidade >= 0) return true;
		
		else return false;
		
		
	}
	//--------------------------------------------------------------------------
	public function ExibirEstoqueDoMunicipio($unidade_id)
	{
		$unidade_nome = false;
		$cidade_nome = Html::FormatarMaiusculasMinusculas($_SESSION['cidade_nome']);
		
		if (Sessao::Permissao('UNIDADES_ESTOQUE_VISUALIZAR_ESTADO'))
		{
		
			$sql = $this->conexao->prepare('SELECT vacina.id ,pertence, vacina.nome,
				vacinadaunidade.quantidade, unidadedesaude.nome, unidadedesaude.id
				FROM `vacina`, `vacinadaunidade`, `unidadedesaude`
				WHERE vacina.id = vacinadaunidade.Vacina_id
				AND vacinadaunidade.UnidadeDeSaude_id = unidadedesaude.id 
				AND unidadedesaude.ativo
				AND vacina.ativo
				ORDER BY unidadedesaude.nome, vacina.nome')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
		  	$sql->bind_result($vacina_id, $pertence, $nome, $quantidade, $unidade_nome, $unidade_id);
		
		} elseif (Sessao::Permissao('UNIDADES_ESTOQUE_VISUALIZAR_MUNICIPIO')) {
			
			list($cidade_id) = $this->RetornarCidadeDaUnidade($unidade_id);
			
			$sql = $this->conexao->prepare('SELECT vacina.id ,pertence, vacina.nome,
				vacinadaunidade.quantidade, unidadedesaude.nome, unidadedesaude.id
				FROM `vacina`, `vacinadaunidade`, `unidadedesaude`
				WHERE vacina.id = vacinadaunidade.Vacina_id
				AND vacinadaunidade.UnidadeDeSaude_id = unidadedesaude.id
				AND unidadedesaude.Bairro_id IN (SELECT bairro.id FROM bairro WHERE
								bairro.Cidade_id = ?)
				AND unidadedesaude.ativo
				AND vacina.ativo
				ORDER BY unidadedesaude.nome, vacina.nome')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
			$sql->bind_param('i', $cidade_id);
		  	$sql->bind_result($vacina_id, $pertence, $nome, $quantidade, $unidade_nome, $unidade_id);
			
		} elseif (Sessao::Permissao('UNIDADES_ESTOQUE_VISUALIZAR_UNIDADE'))
		{
			
			$sql = $this->conexao->prepare('SELECT vacina.id ,pertence, vacina.nome,
				vacinadaunidade.quantidade
				FROM `vacina`, `vacinadaunidade`
				WHERE vacina.id = vacinadaunidade.Vacina_id
				AND UnidadeDeSaude_id = ?
				AND vacina.ativo
				ORDER BY vacina.nome')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
			$sql->bind_param('i', $unidade_id);
		  	$sql->bind_result($vacina_id, $pertence, $nome, $quantidade);
			
		}
	  	
	  	$sql->execute();
	  	$sql->store_result();
	  	$existemVacinas = $sql->num_rows;
	  	
		echo '<div id="print"><h3 align="center">Visualizar estoque de vacinas das unidades deste município</h3></div>';
		
	  	if($existemVacinas > 0) {
	  		
	  		$arr = array();
	  		$i = 0;
			  	while ($sql->fetch()) {

                    if($pertence) continue;


                    //$resultado = $this->ExibirEstoqueUnidadeEstado($unidade_id, $vacina_id);
                    //if($resultado < 0) $resultado = trim($resultado,'-');

			  		$arr[$i] = array('Unidade de saude' => $unidade_nome,
							 'Vacina' => $nome,
			  				'Quantidade em estoque' => $quantidade);
                           // 'sql estoque' => $resultado,
                           // 'resultado' => $this->ExibirEstoqueUnidadeEstado($unidade_id, $vacina_id)-$quantidade);

                           /*$arrEstoque[] = Array(
                                        'quantidade' => $resultado,
                                        'vacina_id'  => $vacina_id,
                                        'unidade_id' => $unidade_id );*/
			  	
			  		// Admin nivel 1 não pode ver outras uniades de saude
			  		if ($unidade_nome == false) unset($arr[$i]['Unidade de saude']);
			  		
			  		$i++;
			  		
			  	}

            //==========================
            /*
            $arrResult = Array();

            foreach ($arrEstoque as $arrValor) {

                 
                 $sql = "UPDATE `vacinadaunidade`
                            SET `quantidade` = '{$arrValor['quantidade']}'
                                WHERE `UnidadeDeSaude_id` = {$arrValor['unidade_id']}
                                    AND `Vacina_id` ={$arrValor['vacina_id']}";


                $stmt = $this->conexao->prepare($sql)
                        or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                $stmt->execute();

                $arrResult[] = $stmt->affected_rows;

                $stmt->free_result();

            }
            echo '<pre>';
            print_r($arrEstoque);
            */
            //==========================
            
		  	Html::CriarTabelaDeArray($arr);
            $crip = new Criptografia();

            $end = $crip->Cifrar('pagina=exibirRelatorioPop&tipo=RelatorioEstoqueUnidadesMunicipio&unidade_id='.$unidade_id);
            
		  	echo '<center><p style="padding:10px;"><a href="./Rel/?'.
                        $end.'" target="_blank" ><b>Visualizar para Impressão</b></p></center>';
                        
		  	return true;
	  	}
	  	
	  	if($existemVacinas == 0) {
	  		
			$this->ExibirMensagem('Não existem vacinas estocadas nesta unidade.
	  			Atualize o estoque.');
	  	}
	  	
	  	if($existemVacinas < 0) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao exibir o
				estoque de vacinas desta unidade.');
	  	}
		
	}
	//--------------------------------------------------------------------------
	public function ExibirEstoqueDaUnidade($unidade_id)
	{
		$unidade_nome = false;
		$cidade_nome = Html::FormatarMaiusculasMinusculas($_SESSION['cidade_nome']);
			
		$sql = $this->conexao->prepare('SELECT pertence, vacina.nome,
			vacinadaunidade.quantidade
			FROM `vacina`, `vacinadaunidade`
			WHERE vacina.id = vacinadaunidade.Vacina_id
			AND UnidadeDeSaude_id = ?
			AND vacina.ativo
			ORDER BY vacina.nome')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		  	
		$sql->bind_param('i', $unidade_id);
	  	$sql->bind_result($pertence, $nome, $quantidade);
	  	
	  	$sql->execute();
	  	$sql->store_result();
	  	$existemVacinas = $sql->num_rows;
	  	
		echo '<h3 align="center">Visualizar estoque de vacinas desta Unidade de Saúde</h3>';
		
	  	if($existemVacinas > 0) {
	  		
	  		$arr = array();
	  		$i = 0;
			  	while ($sql->fetch()) {

                    if($pertence) continue; // vacinas filhas são ignoradas

			  		
			  		$arr[$i] = array('Unidade de saude' => $unidade_nome,
							 'Vacina' => $nome,
			  				'Quantidade em estoque' => $quantidade);
			  					   
			  	
			  		// Admin nivel 1 não pode ver outras uniades de saude
			  		if ($unidade_nome == false) unset($arr[$i]['Unidade de saude']);
			  		
			  		$i++;
			  		
			  	}
			  	
		  	Html::CriarTabelaDeArray($arr);
		    $crip = new Criptografia();

            $end = $crip->Cifrar('pagina=exibirRelatorioPop&tipo=RelatorioEstoqueDaUnidade&unidade_id='.$unidade_id);

		  	echo '<center><p style="padding:10px;"><a href="./Rel/?'.
                        $end.'" target="_blank" ><b>Visualizar para Impressão</b></p></center>';
		  	return true;
	  	}
	  	
	  	if($existemVacinas == 0) {
	  		
			$this->ExibirMensagem('Não existem vacinas estocadas nesta unidade.
	  			Atualize o estoque.');
	  	}
	  	
	  	if($existemVacinas < 0) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao exibir o
				estoque de vacinas desta unidade.');
	  	}
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioEstoqueEstado() {
	
	echo '<h3 align="center">Visualizar estoque de unidades do município</h3>';
	
	?>
	<form id="alimentarCentral" name="alimentarCentral" method="post"
		action="<?php echo $_SERVER['REQUEST_URI']?>"
		onsubmit="return (ValidarCampoSelect(this.cidade, 'Cidade') 
					&& ValidarCampoSelect(this.unidadeCentral, 'Unidade Central') 
					&& ValidarCampoSelect(this.vacina, 'Vacina'))" >
		
			<p>
			<div class='CadastroEsq'>*Cidade: </div>
			
			<div class='CadastroDir'><select name="cidade" id="cidade"
			style="width:300px" 
			onblur="ValidarCampoSelect(this, 'cidade')"
			onchange="PesquisarUnidadesSemTipo(this.value, 'unidadeCentral')">
			</select></div>
			</p>
			
			<script>PesquisarCidades('<?php echo $_SESSION['estado_banco']?>')</script>
			<p>
			<div class='CadastroEsq'>*Unidade de Saúde: </div>
			
			<div class='CadastroDir'><select name="unidadeCentral" id="unidadeCentral"
			style="width:300px" 
			onblur="ValidarCampoSelect(this, 'Unidade')">
			</select></div>
			</p>
			
			  <p>
			  <div class='CadastroEsq'>*Vacina: </div> 
			  <div class='CadastroDir'><!-- <select name="vacina" id="vacina"
			  style="width:300px" 
			  onblur="ValidarCampoSelect(this, 'Vacina')"> -->
			  	<?php
			  	/*
			  	$consulta = 'SELECT id, Grupo_id, nome
			  				 FROM `vacina`m
			  				 WHERE Grupo_id <> "Descontinuadas"
			  				 	AND ativo
			  				 		ORDER BY Grupo_id DESC,
			  				 				 nome ASC';
			  	
			  	$sql = $this->conexao->prepare($consulta)
			  		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
			  	$grupo_id_anterior = 'nenhum';
			  	$sql->bind_result($id, $grupo_id, $nome);
			  	$sql->execute();
			  	
			  	echo "\n<option value='0'>- selecione -</option>";
			  	
			  	while ($sql->fetch()) {
			  		
			  		if($grupo_id_anterior != $grupo_id) {
						echo "<optgroup label='$grupo_id'>";
						$grupo_id_anterior = $grupo_id;
					}
			  		echo "\n<option value='$id'>$nome</option>";
			  		
			  		if($grupo_id_anterior != $grupo_id) echo '</optgroup>';
			  	} */
                $vacina =  new Vacina;
                $vacina->UsarBaseDeDados();
                $vacina->ListarVacinas(false, false, true, false); // LIstar apenas vacinas normais e vacinas maes
			  	?>
			  <!-- </select> --> </div>
			  </p>
			  
			  <?php $this->ExibirBotoesDoFormulario('Confirmar');?>		  
			  </label>
		</form>
	<?php

	}
	//----------------------------------------------------------------------------
	public function ExibirEstoqueUnidadeEstado($unidade_id, $vacina_id)
	{
		
		$sqlUnidade = $this->conexao->prepare("SELECT unidadedesaude.nome, tipodaunidade.nome,
					vacina.nome
					FROM `unidadedesaude`, `tipodaunidade`, `vacina` 
					WHERE unidadedesaude.id = ?
					AND vacina.id = ? ")
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$sqlUnidade->bind_param('ii', $unidade_id, $vacina_id);
		$sqlUnidade->bind_result($nome, $tipo, $vacina);
		$sqlUnidade->execute();
		$sqlUnidade->fetch();
		$sqlUnidade->free_result();

		$sqlEntrada = $this->conexao->prepare("SELECT unidadedesaude.nome, tipodaunidade.nome,
					vacina.nome, SUM(quantidade)
					FROM `unidadedesaude`, `tipodaunidade`, `transporte`, `vacina`
					WHERE UnidadeDeSaudeDestino_id = unidadedesaude.id
					AND UnidadeDeSaudeDestino_id = ?
					AND Vacina_id = ?
					AND TipoDaUnidade_id = tipodaunidade.id
					AND Vacina_id = vacina.id GROUP BY unidadedesaude.nome")
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$sqlEntrada->bind_param('ii', $unidade_id, $vacina_id);
		$sqlEntrada->bind_result($nome, $tipo, $vacina, $entrada);
		$sqlEntrada->execute();
		$sqlEntrada->fetch();
		$sqlEntrada->free_result();
		
		if ($entrada == NULL) $entrada = 0;
		
		//==============================================================================
		$sqlSaida = $this->conexao->prepare("SELECT SUM( quantidade ) FROM `transporte` 
									WHERE UnidadeDeSaudeOrigem_id =? AND Vacina_id = ?")
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__)); 
		
		$sqlSaida->bind_param('ii', $unidade_id, $vacina_id);
		$sqlSaida->bind_result($saida);
		$sqlSaida->execute();
		$sqlSaida->fetch();
		$sqlSaida->free_result();
		
		if ($saida == NULL) $saida = 0;
		
		//==============================================================================
		$sqlDescarte = $this->conexao->prepare("SELECT SUM( quantidade ) FROM `descartevacinadaunidade` 
									WHERE vacinadaunidade_UnidadeDeSaude_id = ? AND vacinadaunidade_Vacina_id = ?")
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__)); 
		
		$sqlDescarte->bind_param('ii', $unidade_id, $vacina_id);
		$sqlDescarte->bind_result($descarte);
		$sqlDescarte->execute();
		$sqlDescarte->fetch();
		$sqlDescarte->free_result();
		
		if ($descarte == NULL) $descarte = 0;
		
		//===============================================================================
		$sqlVacinado = $this->conexao->prepare("SELECT COUNT(Vacina_id) AS vacinado FROM 
								`usuariovacinado` WHERE UnidadeDeSaude_id = ? AND Vacina_id = ? AND decrementarestoque = 1")
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__)); 
		
		$sqlVacinado->bind_param('ii',$unidade_id, $vacina_id);
		$sqlVacinado->bind_result($vacinado);
		$sqlVacinado->execute();
		$sqlVacinado->fetch();
		$sqlVacinado->free_result();
		
		if ($vacinado == NULL) $vacinado = 0;

		//===============================================================================
		$sqlVacinadoCampanha = $this->conexao->prepare("SELECT COUNT(Vacina_id) AS vacinado FROM 
								`usuariovacinadocampanha` WHERE UnidadeDeSaude_id = ? AND Vacina_id = ?
                                 AND usuariovacinadocampanha.Usuario_id NOT IN (SELECT usuariovacinado.Usuario_id  FROM
								 `usuariovacinado` WHERE UnidadeDeSaude_id = $unidade_id AND Vacina_id = $vacina_id AND decrementarestoque = 1)
                                 AND decrementarestoque = 1")
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__)); 
		
		$sqlVacinadoCampanha ->bind_param('ii',$unidade_id, $vacina_id);
		$sqlVacinadoCampanha ->bind_result($vacinadoCampanha );
		$sqlVacinadoCampanha ->execute();
		$sqlVacinadoCampanha ->fetch();
		$sqlVacinadoCampanha ->free_result();
		
		if ($vacinadoCampanha == NULL) $vacinadoCampanha = 0;
		
		//================================================================================
		
		//SQL para controlar o estoque independente do número de vacinados
		/*
		$sqlEstoque = $this->conexao->prepare("SELECT quantidade FROM `vacinadaunidade` 
											WHERE UnidadeDeSaude_id = ? AND Vacina_id = ?")
											or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		
		$sqlEstoque->bind_param('ii',$unidade_id, $vacina_id);
		$sqlEstoque->bind_result($estoque);
		$sqlEstoque->execute();
		$sqlEstoque->fetch();
		$sqlEstoque->free_result();
		
		if ($estoque == NULL) {
			$estoque = 0;
			$vacinado = 0;									
		}
		*/									
											
		$estoque = $entrada - $saida - $descarte - $vacinado - $vacinadoCampanha;
		
		//================================================================================
		 
		
		$arr = array();
		$arr[] = array('Unidade de saude' => $nome,
			  		        'Tipo' => $tipo,
			  		        'Vacina' => $vacina,
			  				'Entradas' => $entrada,
			  				'Saídas' => $saida,
			  				'Descartes' => $descarte,
			  				'Vacinados' => $vacinado+$vacinadoCampanha,
			  				'Estoque' => $estoque);
			  				
		  				
		Html::CriarTabelaDeArray($arr);
	  	
	  	//return $estoque;
		
	}
	//--------------------------------------------------------------------------
	public function ExcluirDescarte ($idIncluido) {	
			
		// prepara uma inserção...
		$removerDescarte = $this->conexao->prepare('DELETE FROM `descartevacinadaunidade` WHERE id = ?');
		
		$removerDescarte->bind_param('i', $idIncluido);
		
		//... e executa essa sql
		$removerDescarte->execute();
		
		// Retorna as linahs afetadas pelo execute() para a variável $inserido
		$removido = $removerDescarte->affected_rows;
		
		// /fecha a conexão
		$removerDescarte->close();
		
		// Se houver resultado...
		if ($removido > 0) {
			
			//... atualiza o estoque
			return true;
		}
		
		if($removido < 0) {
			
			$this->AdicionarMensagemDeErro("Ocorreu algum erro ao cadastrar.");
			return false;
		}	
	
		if($removido == 0) {
			
			$this->AdicionarMensagemDeErro("Não foi possível cadastrar esta inserção.");
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioInserirUnidade()
	{
	?>
		<form id="formulario" name="formulario" method="post"
			action="<?php echo $_SERVER['REQUEST_URI']?>"
			onsubmit="return (ValidarCampoSelect(this.cidade. 'Cidade') 
					&& ValidarCampoSelect(this.ddd, 'Ddd', true) 
					&& ValidarCampoSelect(this.estado, 'Estado')
					&& ValidarUnidadeDeSaude(this.nome) 
					&& ValidarCnes(this.cnes)
					&& ValidarCep(this.cep))">
			
		  <h3 align="center">Adicionar Unidade de Saúde</h3>

		 <div style="padding-left:50px;">
		  <p>
			  <div class='CadastroEsq'>*Nome:</div>
		  	<div class='CadastroDir'>
			  	<input type="text" name="nome" id="nome" value="<?php
			  		if( isset($_POST['nome']) ) echo $_POST['nome']?>"
			  		style="width:200px;" maxlength="70" 
			  		onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this); ValidarUnidadeDeSaude(this);
						FormatarNome(this, event)" />
			  </div>
		  </p>
		  <p>
		  	<div class='CadastroEsq'>*CNES:</div>
		  	<div class='CadastroDir'>
		  	<input type="text" name="cnes"
			  		value="<?php if( isset($_POST['cnes']) )
			  		echo $_POST['cnes']?>" style="width:200px;"
			  		maxlength="7" 
			  		onkeypress="return Digitos(event, this);"
						onblur="ValidarCnes(this)" />
		  	</div>
		  </p>
		  <script>PesquisarCidades('<?php echo $_SESSION['estado_banco']?>',
				  <?php echo $_SESSION['cidade_id']?>)</script>
		  <p>
		  	<div class='CadastroEsq'>*Cidade:</div>
		  	<div class='CadastroDir'>
		  	<select name="cidade" id="cidade" style="width:205px;
			  		margin-left:2px;"
					onchange="document.formulario.bairro.value='';
							  document.formulario.logradouro.value='';
							  document.formulario.cep.value='';"
					onblur="ValidarCampoSelect(this, 'cidade')">
					
		  	</select>
		  	</div>
		  </p>
		  <p>
			  <div class='CadastroEsq'>*Bairro:</div>
		  	<div class='CadastroDir'>
			  	<input type="text" name="bairro" value="<?php
			  		if( isset($_POST['bairro']) ) echo $_POST['bairro']?>"
			  		style="width:200px;" maxlength="50" 
			  		onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this);
						ValidarNome(this, 'bairro'); FormatarNome(this, event)" />
			  </div>
		  </p>
		  <p>
			  <div class='CadastroEsq'>*Logradouro:</div>
		  		<div class='CadastroDir'>
			  	<input type="text" name="logradouro" value="<?php
			  		if( isset($_POST['logradouro']) ) echo $_POST['logradouro']?>"
			  		style="width:200px;" maxlength="100" 
			  		onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this);
						ValidarNome(this, 'logradouro'); FormatarNome(this, event)" />
			  </div>
		  </p>
		  <p>
		  <div  class='CadastroEsq'>Telefone:</div>
		  	<div class='CadastroDir'><select name="ddd" id="ddd" style="width:50px; margin-left:2px;"
				onblur="ValidarCampoSelect(this, 'DDD', true)"
				onchange="document.formulario.telefone.focus()">
				<?php
				if( isset($_POST['ddd']) ) {
					$this->SelectsDdd($_POST['ddd']);
				} else {
					$this->SelectsDdd();
				}
				?>
			</select>
				<input type="text" name="telefone" id="telefone"
				maxlength="9" style="width:142px; margin-left:2px;"
				value="<?php if( isset($_POST['telefone']) )
				echo $_POST['telefone']?>"
				onkeypress="return Digitos(event, this);"
				onkeydown="Mascara('TELLOCAL', this, event);"
				onblur="ValidarTelLocal(this, true)" />
		</div>
		</p>
		<p>
		  	<div class='CadastroEsq'>*Cep:</div>
		  	<div class='CadastroDir'>
		  	<input type="text" name="cep"
			  		value="<?php if( isset($_POST['cep']) )
			  		echo $_POST['cep']?>" style="width:200px;"
			  		maxlength="9"
			  		onkeypress="return Digitos(event, this);"
					onkeydown="Mascara('CEP',this,event);"
					onblur="ValidarCep(this)" />
		  	</div>
		</p>
		<p>
			
			<div class='CadastroEsq'>*Tipo da Unidade:</div>
			<div class='CadastroDir'>
			<select id='tipodaunidade' name='tipodaunidade'
			style="width:205px; margin-left:2px;">
			<?php
				if( isset($_POST['tipodaunidade']) && $_POST['tipodaunidade'])
				$this->SelectTipoDaUnidade($_POST['tipodaunidade']);
				else
				$this->SelectTipoDaUnidade(2);
			?>
			</select>
			</div>
			
		</p>
		<br />
		
		<!-- ############################################################### -->
		  <p>
			<!--  <div align="right" style="margin-right:430px;">
			  Data de finalização da campanha: <input type="text"
			  		name="dataFim" value="<?php /*if( isset($_POST['dataFim']) )
			  		echo $_POST['dataFim']*/?>" style="width:200px;"/>
			  </div>
		  </p>
		  <p><center>Texto complementar:</center></p>
		  <p>
		    <div align="center">
		    	<textarea name="obs" cols="50" rows="5"
			  		style="width:450px;"><?php /* if(isset($_POST['obs']))
			  		echo $_POST['obs']*/ ?></textarea>
		    </div>-->
		  
		</p>
		
		</div>
		
		  <p><center>
		  	<?php $this->ExibirBotoesDoFormulario('Confirmar', 'Limpar')?>
		    </center>
		  </p>
		</form>
		<?php
	}

	//--------------------------------------------------------------------------
	public function ExibirFormularioEditarUnidade()
	{
	?>
		<form id="formulario" name="formulario" method="post"
			action="<?php echo $_SERVER['REQUEST_URI']?>"
			onsubmit="return (ValidarUnidadeDeSaude(this.nome)
					&& ValidarCampoSelect(this.estado, 'estado') 
					&& ValidarCampoSelect(this.ddd, 'Ddd', true)
					&& ValidarTelLocal(this.telefone)
					&& ValidarNome(this.logradouro, 'logradouro')
					&& ValidarNome(this.bairro, 'bairro')
					&& ValidarCnes(this.cnes)
					&& ValidarCep(this.cep))">

		  <h3 align="center">Alterar Unidade de Saúde</h3>
		  
		  <div style="padding-left:50px;">
		  
		  <p>
			  <div class='CadastroEsq'>Nome:</div>
		  		<div class='CadastroDir'>
			  	<input type="text" name="nome"
				value="<?php
					if( isset($_POST['nome']) ) {
						echo $_POST['nome'];
			  		} else {
			  			echo Html::FormatarMaiusculasMinusculas($this->Nome());
			  		}
			  		?>"
			  		style="width:200px;" id="nome" 
		onkeypress="FormatarNome(this, event)"
		onkeyup="FormatarNome(this, event)"
		onkeydown="Mascara('NOME', this, event)"
		onblur="LimparString(this); ValidarUnidadeDeSaude(this);
		FormatarNome(this, event)" />
			  </div>
		  </p>
		  <p>
		  	<div class='CadastroEsq'>CNES:</div>
		  		<div class='CadastroDir'>
		  		<input type="text" name="cnes" id="cnes" maxlength="7"
			  		value="<?php if( isset($_POST['cnes']) ) {
								echo $_POST['cnes'];
			  				} else {
			  					echo $this->Cnes();
			  				}?>" style="width:200px;" 
			onkeypress="return Digitos(event, this);"
			onblur="ValidarCnes(this)" />

		  	</div>
		  </p>
		  <p>
		  	<div class='CadastroEsq'>Cidade:</div>
		  		<div class='CadastroDir'>
		  		<select name="cidade" id="cidade" style="width:205px;
			  	margin-left:2px;" 
			  		onfocus="PesquisarCidades(<?php echo $_SESSION['estado_banco']?>,
			  		<?php echo $this->SelecionarCidade(); ?>)"></select>
		  	</div>
		  </p>
		  <p>
			  <div class='CadastroEsq'>Bairro:</div>
		  		<div class='CadastroDir'>
			  	<input type="text" name="bairro"  id="bairro"
				value="<?php echo Html::FormatarMaiusculasMinusculas($this->SelecionarBairro()); ?>"
			  		style="width:200px;" 
			  		onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this);
						ValidarNome(this, 'bairro'); FormatarNome(this, event)" />
			  </div>
		  </p>
		  <p>
			  <div class='CadastroEsq'>Logradouro:</div>
		  		<div class='CadastroDir'>
			  	<input type="text" name="logradouro"
			  	value="<?php echo Html::FormatarMaiusculasMinusculas($this->SelecionarLogradouro()); ?>" id="logradouro"
			  		style="width:200px;" 
			  		onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this);
						ValidarNome(this, 'logradouro'); FormatarNome(this, event)" />
			  </div>
		  </p>
		  <p>
		  <div  class='CadastroEsq'>Telefone:</div>
		  		<div class='CadastroDir'>
		  		<select name="ddd" id="ddd" style="width:50px; margin-left:2px;"
				onblur="ValidarCampoSelect(this, 'DDD', true)"
				onchange="document.formulario.telefone.focus()">
				<?php
				if ( isset( $_POST['ddd'] ) ) {
					$this->SelectsDdd( $_POST['ddd'] );
				} else {
					$this->SelectsDdd( $this->Ddd() );
				}
				?>

			</select>

				<input type="text" name="telefone" id="telefone"
					  style="width:142px; margin-left:2px;"
				maxlength="9" onkeypress="return Digitos(event, this);"
				onkeydown="Mascara('TELLOCAL', this, event);"
				onblur="ValidarTelLocal(this, true)"
				value="<?php
				if ( isset( $_POST['telefone'] ) ) {
					echo $_POST['telefone'];
				} else {
					if($this->Telefone() != '')
					echo Preparacao::InserirSimbolos($this->telefone(), 'TEL');
				}
				?>" />

		</div>
		</p>
		<p>
		  	<div class='CadastroEsq'>*Cep:</div>
		  	<div class='CadastroDir'>
		  	<input type="text" name="cep" id="cep"
			maxlength="9"
				  value="<?php
				  if ( isset( $_POST['cep'] ) ) {
				  		echo $_POST['cep'];
				  } else {
				  		echo Preparacao::InserirSimbolos($this->cep(), 'CEP');
				  }
				  ?>" style="width:200px;" 
			onkeypress="return Digitos(event, this);"
			onkeydown="Mascara('CEP',this,event);"
			onblur="ValidarCep(this)" />
		  	</div>
		</p>
				<p>
			
			<div class='CadastroEsq'>*Tipo da Unidade:</div>
			<div class='CadastroDir'>
			<select id='tipodaunidade' name='tipodaunidade'
			style="width:205px; margin-left:2px;">
			<?php
				if( isset($_POST['tipodaunidade']) && $_POST['tipodaunidade'])
				$this->SelectTipoDaUnidade($_POST['tipodaunidade']);
				else
				$this->SelectTipoDaUnidade($this->TipoDaUnidade());
			?>
			</select>
			</div>
			
		</p>
		
		</div>
		
		<!-- ############################################################### -->
		<br />
		    <p><center>
		  	<?php $this->ExibirBotoesDoFormulario('Confirmar');?>
		    </center>
		  </p>
		</form>
		<?php
	}
	//--------------------------------------------------------------------------
	public function ValidarFormulario($nomeDoFormulario)
	{
		switch($nomeDoFormulario) {

			case 'inserirUnidade':
			case 'editarUnidade':
				
				$nomeValido = $this->ValidarNomeDaUnidade( $_POST['nome'] );
				$cnesValido = $this->ValidarCnesDaUnidade( $_POST['cnes'] );
				$bairroValido = $this->ValidarBairroDaUnidade( $_POST['bairro'] ) ;
				$logradouroValido = $this->ValidarLogradouroDaUnidade( $_POST['logradouro'] );
				$telefoneValido = $this->ValidarTelefoneDaUnidade( $_POST['telefone'] );
				$cepValido = $this->ValidarCepDaUnidade( $_POST['cep'] );

				if( $nomeValido && $cnesValido && $bairroValido
					&& $logradouroValido && $telefoneValido && $cepValido)
				  	
					return true;

				break;

			case 'excluirVacina':
					//--

				 	return true;

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

	//////////////////////////////// EXCLUIR ///////////////////////////////////

	public function ExcluirUnidadeDeSaude($id)
	{
		
		if( $this->RestricaoDeExclusaoDaUnidade($id, true) == true ) return false;
		
		$stmt = $this->conexao->prepare('SELECT id FROM acs WHERE UnidadeDeSaude_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$stmt->bind_param('i', $id);
		$stmt->bind_result($idDoAcs);
		$stmt->execute();
		
		$stmt->store_result();
		
		if( $stmt->num_rows == 1 ){
			
			$stmt->fetch();
			$stmt->free_result();
			
			//$this->conexao->query("DELETE FROM `acs` WHERE id = $idDoAcs");
			$this->conexao->query("UPDATE `acs` SET ativo = 0 WHERE ativo = 1 AND id = $idDoAcs");
			
			if (!$this->conexao->affected_rows) {
				
				$this->AdicionarMensagemDeErro('Erro ao excluir os Agentes Comunitários de Saúde relacionados a esta Unidade de Saúde ');	
				return false;
			
			}
		
		} elseif ( $stmt->num_rows > 1 ){
			
			$this->AdicionarMensagemDeErro('Há Agentes Comunitários de Saúde cadastrados nesta Unidade De Saúde');
			return false;
			
		}
		
		/*$excluir = $this->conexao->prepare('UPDATE `unidadedesaude`
			SET ativo = 0 WHERE id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$excluir->bind_param('i', $id);

		$excluir->execute();

		$excluiu = $excluir->affected_rows;

		$excluir->close();*/
		
		//$this->conexao->query("DELETE FROM `unidadedesaude` WHERE id = $id");
		$this->conexao->query("UPDATE `unidadedesaude` SET ativo = 0 WHERE ativo = 1 AND id = $id");

		if ($this->conexao->affected_rows) {

			return true;
		}

		return false;

		//header('Location: listarUnidades.php');

	}

	//--------------------------------------------------------------------------

	/////////////////////////////// ATUALIZAR //////////////////////////////////

	/*public function AtualizarUnidadeDeSaude($id){

		$cidade_id = $this->Cidade();
		$nome      = $this->nome();
		$cep       = $this->Cep();
		$ddd      = $this->Ddd();
		$nome     = $this->Nome();
		$cnes     = $this->Cnes();
		$telefone = $this->Telefone();

		$atualizarBairroUnidade = $this->conexao->prepare('UPDATE `Bairro`
			SET cidade = ?, nome = ?, cep = ? WHERE id = ? AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$atualizarBairroUnidade->bind_param('iisssi', $cidade_id, $nome, $cep);

		$atualizarBairroUnidade->execute();

		//----------------------------------------------------------------------

		$atualizarUnidadeSaude = $this->conexao->prepare('UPDATE `UnidadeSaude`
		SET ddd_id = ?, bairro_id = ?, nome = ?, cnes = ?, telefone = ?
		WHERE id = ? AND ativo')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$atualizarUnidadeSaude->bind_param('iisssi', $ddd, $bairro_id, $nome,
													      $cnes, $telefone, $id);

		$atualizarUnidadeSaude->execute();
	}*/
	//--------------------------------------------------------------------------
	/**
	 * Mostra os dados de uma campanha conforme o id informado
	 *
	 * @param int $id
	 * @return null
	 */
	public function ExibirDadosDaUnidade($id)
	{
		$unidades = $this->conexao->prepare('SELECT nome, cnes	FROM unidadedesaude
			WHERE id = ? AND ativo ORDER BY nome')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$unidades->bind_param('i', $id);
		$unidades->bind_result($nome, $cnes);
		$unidades->execute();
		$unidades->store_result();
		$existe = $unidades->num_rows;

		if($existe > 0) {

			while ( $unidades->fetch() ) {

				echo "<h4>" . Html::FormatarMaiusculasMinusculas($nome) . "</h4>";
				echo "<p>CNES $cnes</p>";
			}
			$unidades->free_result();
			return true;
		}
		
		$unidades->free_result();
		
		if($existe == 0) {

			$this->AdicionarMensagemDeErro('Unidade de saúde com a identificação
				passada não existe.');
			
			return false;
		}
		
		if($existe < 0) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao exibir os
				dados desta unidade de saúde.');
			
			return false;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Mostra os um formulário de confirmação de exclusão para Unidades de Saúde
	 *
	 * @return null
	 */
	public function ExibirFormularioExcluirUnidade()
	{
		echo "<form method='POST' action='{$_SERVER['REQUEST_URI']}'>";

			// O segundo parâmetro é enviado false para nao exibir o botão reset
			$this->ExibirBotoesDoFormulario('Excluir', false, 'excluir');
		echo '</form>';
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se houve uma tentativa de alteração
	 * se houver devolve o último valor do campo cidade
	 *
	 * @return String
	 */
	public function SelecionarCidade()
	{
		if ( isset($_POST['cidade']) && $_POST['cidade'] != '' ) {
			return $_POST['cidade'];
		}
		return $this->Cidade();
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se houve uma tentativa de alteração
	 * se houver devolve o último valor do campo bairro
	 *
	 * @return String
	 */
	public function SelecionarBairro()
	{
		if ( isset($_POST['bairro']) ) {
			return $_POST['bairro'];
		}
		return $this->Bairro();
	}
	//--------------------------------------------------------------------------
	
	public function RetornarNomeUnidade($id)
	{
		$sql = "SELECT nome FROM `unidadedesaude` WHERE id = $id";
		
		$unidade = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$unidade->bind_result($nome);
		
		$unidade->execute();
		
		$unidade->fetch();
		
		$unidade->free_result();
		
		return $nome;
	}
	//--------------------------------------------------------------------------

	public function RetornarNome($id, $tabela)
	{
		$sql = "SELECT nome FROM `$tabela` WHERE id = $id";

		$unidade = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$unidade->bind_result($nome);

		$unidade->execute();

		$unidade->fetch();

		$unidade->free_result();

		return $nome;
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se houve uma tentativa de alteração
	 * se houver devolve o último valor do campo logradouro
	 *
	 * @return String
	 */
	public function SelecionarLogradouro()
	{
		if ( isset($_POST['logradouro']) ) {
			return $_POST['logradouro'];
		}
		return $this->Logradouro();
	}
	
	public function SelectTipoDaUnidade($valor = false)
	{
		
		$stmt = $this->conexao->prepare('SELECT id, nome FROM `tipodaunidade`');
		$stmt->bind_result($tipoDaUnidadeId, $tipoDaUnidadeNome);
		
		$stmt->execute();
		
		$stmt->store_result();
		
		if( $stmt->num_rows > 0){
			
			if( $valor == false )
				while($stmt->fetch())
				echo '<option value="'.$tipoDaUnidadeId.'" >'.$tipoDaUnidadeNome.'</option>';	
			
			else
				while($stmt->fetch())
					if( $valor == $tipoDaUnidadeId)
					echo '<option value="'.$tipoDaUnidadeId.'" selected="true">'.$tipoDaUnidadeNome.'</option>';	
			
					else			
					echo '<option value="'.$tipoDaUnidadeId.'" >'.$tipoDaUnidadeNome.'</option>';
		
			return true;
		
		}
				
		return false;
		
	}
	
	//----------------------------------------------------------------------
	public function RestricaoDeExclusaoDaUnidade($id, $tratarErros = false)
	{
		
		
		/**
		 * Verificando se há alguma restrição de exclusão,
		 * pois esta será excluída realmente,
		 * mas se houver somente o ACS "Não Informado"
		 * sem ninguém registrado para este
		 * será permitido a exclusão desta unidade de saúde
		 */
		$numeroDeRegistros = 0;
		
		$stmt = $this->conexao->prepare('SELECT id FROM administrador WHERE UnidadeDeSaude_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_param('i', $id);
		$stmt->bind_result($idDoAdministrador);
		
		$stmt->execute();
		$stmt->store_result();
		$numeroDeRegistros = $stmt->num_rows;
		
		$stmt->free_result();
		
		if( $numeroDeRegistros > 0 ) {
			
			if( $tratarErros )
			$this->AdicionarMensagemDeErro('Há Administradores cadastrados nesta Unidade de Saúde.');
			return true;
			
		} 

        //=============================

		$stmt = $this->conexao->prepare('SELECT quantidade
                                            FROM vacinadaunidade WHERE UnidadeDeSaude_id = ?
                                            AND quantidade > 0')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_param('i', $id);
		$stmt->bind_result($quantidade);

		$stmt->execute();
		$stmt->store_result();
		$numeroDeRegistros = $stmt->num_rows;

		$stmt->free_result();

		if( $numeroDeRegistros > 0 ) {

			if( $tratarErros )
			$this->AdicionarMensagemDeErro('Há estoque cadastrado nesta Unidade de Saúde.');
			return true;

		}

        //=======================
		
		$stmt = $this->conexao->prepare('SELECT id, nome FROM acs WHERE UnidadeDeSaude_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$stmt->bind_param('i', $id);
		$stmt->bind_result($idDoAcs, $nomeDoAcs);
		$stmt->execute();
		
		$stmt->store_result();
		
		$numeroDeRegistros = $stmt->num_rows;
		
		if( $numeroDeRegistros > 1 ) {
			
			$stmt->free_result();
			if( $tratarErros )
			$this->AdicionarMensagemDeErro('Há Agentes Comunitários de Saúde cadastrados nesta Unidade de Saúde.');
			return true;
			
		} elseif( $numeroDeRegistros == 1 ) {
			
			$stmt->fetch();
			$stmt->free_result();
			
			if( strtolower($nomeDoAcs) == strtolower('Não Informado') ) {
				
				$stmt = $this->conexao->prepare('SELECT id FROM `usuario`
								WHERE Acs_id = ?');
				
				$stmt->bind_param('i', $idDoAcs);
				$stmt->bind_result($idDoUsuario);
				
				$stmt->execute();
				$stmt->store_result();
				
				$numeroDeRegistros = $stmt->num_rows;
			
				$stmt->free_result();
				
				if( $numeroDeRegistros > 0 ) {
					
					if( $tratarErros )
					$this->AdicionarMensagemDeErro('Há indivíduos cadastrados nesta Unidade de Saúde.');
					return true;
				
				} 
				
			} else {
				if( $tratarErros )
				$this->AdicionarMensagemDeErro('Há um Agente Comunitário de Saúde cadastrado nesta Unidade.');
				return true;
			
			}
		}
		
		return false;
		
	}
	//----------------------------------------------------------------------
	public function RetornarCidadeDaUnidade($unidade)
	{
		
		if( $unidade == '' || !isset( $unidade ) ) return false;
		
		$stmt = $this->conexao->prepare('SELECT cidade.id, cidade.nome FROM `cidade`
						INNER JOIN `bairro` ON bairro.Cidade_id = cidade.id
						INNER JOIN `unidadedesaude` ON unidadedesaude.Bairro_id = bairro.id
						WHERE unidadedesaude.id = ?');
		
		$stmt->bind_param('i', $unidade);
		$stmt->bind_result($idDaCidade, $nomeDaCidade);
		
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows > 0) {
			
			$stmt->fetch();
			$stmt->free_result();
			$arrValores = array($idDaCidade, $nomeDaCidade);
			return $arrValores;
		} else {
			
			$stmt->free_result();
			return false;
			
		}
		
	}
	
}


