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

//require_once('./tAgenteImunizador.php');


//------------------------------------------------------------------------------
/** Vacina: Classe para abstra��o de Imunobiol�gicos (Vacinas)
 *
 * Esta classe controla a aplica��o de vacinas em indiv�duos. Trata tamb�m
 * as datas de aplica��es ideais e reais, tentando ser gen�rica para todas as
 * vacinas.
 *
 * @package Sivac/Class
 *
 * @author Maykon, v 1.0, 2008-09-11 12:36
 *
 * @copyright 2008 
 */
class Vacina extends AgenteImunizador
{

	const MAXIMO_DE_DOSES = 5;

	private $_doses;	 			// int   -	qtd de doses disponiveis, ex. 5000
	private $_aplicacoesIdeais; 	// array -	de datas ideais para aplica��o  ????????
	private $_aplicacoesReais;		// array -	de datas reais que foram aplicadas ??????
	private $_idadeIdealPara1aDose;	// int   -	idade ideal para tomar a 1a. dose
	private $_etnias;				// array -	etnias que podem tomar a vacina

	private $_aplicacoesPorPessoa;	// int
	private $_indicacoes;			// string
	private $_contraIndicacoes;		// string
	private $_composicao;			// string
	private $_viaDeAdministracao;	// string
	private $_precaucoes;			// string
	private $_conservacao;			// string
	private $_idadeDeAplicacao;		// int
	private $_eventosAdversosComuns;// string
	private $_faixaEtariaInicio;	// int
	private $_faixaEtariaFim;		// int
	private $_unidadeEmDias;		// int
	private $_vacina;				// string

	private $_vacinaDaCampanhaId;               // int - Talvez n�o precise! Verificar isso!
	private $_unidadeDeTempfaixaEtariaInicio;	// int
	private $_unidadeDeTempfaixaEtariaFim;		// int
	private $_sexo;								// string
	private $_nomeDaCampanha;					// string
	private $_estados;							// array


	//--------------------------------------------------------------------------
	public function __construct()
	{
		parent::__construct();

		$this->_aplicacoesIdeais = array();
		$this->_aplicacoesReais = array();
		$this->_etnias = array();

	}
	//--------------------------------------------------------------------------
	public function __destruct()
	{
		parent::__destruct();
	}
	//--------------------------------------------------------------------------

	///////////////////////////////// SETAR ////////////////////////////////////

	//--------------------------------------------------------------------------
	public function SetarDados($post) {

		$clean = Preparacao::GerarArrayLimpo($post, $this->conexao);

		$this->SetarNome                   ($clean['nome']);
		$this->SetarAplicacoesPorPessoa    ($clean['aplicacoesPorPessoa']);
		$this->SetarIndicacoes             ($clean['indicacoes']);
		$this->SetarContraIndicacoes       ($clean['contraIndicacoes']);
		$this->SetarComposicao             ($clean['composicao']);
		$this->SetarViaDeAdministracao     ($clean['viaDeAdministracao']);
		$this->SetarEsquemaDeDoses         ($clean['esquemaDeDoses']);
		$this->SetarPrecaucoes             ($clean['precaucoes']);
		$this->SetarConservacao            ($clean['conservacao']);
	    $this->SetarIdadeDeAplicacao       ($clean['idadeDeAplicacao']);
		$this->SetarEventosAdversosComuns  ($clean['eventosAdversosComuns']);
		$this->SetarFaixaEtariaInicio      ($clean['faixaEtariaInicio'], $clean['UnidadeDeTempoInicio']);
		$this->SetarFaixaEtariaFIm         ($clean['faixaEtariaFim'], $clean['UnidadeDeTempoFim']);

		//$vacina->SetarFaixaEtariaInicio($_POST['faixaEtariaInicio'], $_POST['UnidadeDeTempo']);
		//$vacina->SetarFaixaEtariaFim($_POST['faixaEtariaFim'], $_POST['UnidadeDeTempo']);

	}

	//--------------------------------------------------------------------------
	/**
	 * Atribui � propriedade _aplicacoesIdeais um conjunto de datas ideais para
	 * a aplica��o do imunobiol�gico.  Este conjunto � passado como um array de
	 * datas v�lidas
	 *
	 * @param array $datas Datas v�lidas para aplica��o
	 * @return bool Verdadeiro se as datas s�o v�lidas. Falso caso contr�rio
	 */
	public function SetarAplicacoesPorPessoa($aplicacoesPorPessoa)
	{
		if( $this->ValidarAplicacoesPorPessoa($aplicacoesPorPessoa)) {
			$this->_aplicacoesPorPessoa = $aplicacoesPorPessoa;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta contra indica��es que ficam nos detalhes de cada vacina.
	 *
	 * @param string $contraIndicacoes texto com as contra indica��es
	 */
	public function SetarContraIndicacoes($contraIndicacoes)
	{
		if( $this->ValidarContraIndicacoes($contraIndicacoes)) {
			$this->_contraIndicacoes = $contraIndicacoes;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta a composi��o de cada vacina.
	 *
	 * @param string $composicao texto com a composi��o da vacina
	 */
	public function SetarComposicao($composicao)
	{
		if( $this->ValidarComposicao($composicao)) {
			$this->_composicao = $composicao;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o texto que explica como conserver determinada vacina
	 *
	 * @param string $conservacao texto com explica��o da conserva��o
	 */
	public function SetarConservacao  ($conservacao)
	{
		if( $this->ValidarConservacao($conservacao )) {
			$this->_conservacao = $conservacao;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta as datas ideais de aplica��o baseadas nas reais
	 *
	 * @param array $datas datas ideais
	 * @return retorna true se validar data retornar verdadeiro e a quantidade de
	 * datas for menor que o numero MAXIMO_DE_DOSES e maior que 0
	 */
	public function SetarDatasIdeaisDeAplicacao(array $datas) //???????????
	{

		foreach ($datas as $data) {
			if( $this->ValidarData($data) == false) return false;
		}

		if(count($datas) <= self::MAXIMO_DE_DOSES && count($datas) > 0)
			$this->_aplicacoesIdeais = $datas;

		return true;
	}
	//--------------------------------------------------------------------------
	/**
	 * M�todo que adiciona uma data de aplica��o ao array _aplicacoesReais. Esta
	 * data n�o poder� ser, somada com as aplica��es anteriores, em quantidade
	 * maior do que a quantidade de aplica��es permitidas.
	 *
	 * @param string $data Data em que a vacina foi aplicada
	 */
	private function SetarDataRealDeAplicacao($data) //?????????
	{
		if($this->ValidarData($data)
			&& count($this->_aplicacoesReais) <= self::MAXIMO_DE_DOSES) {

			$this->_aplicacoesReais[] = $data;
		}
	}

	//--------------------------------------------------------------------------
	/**
	 * Atribui a quantidade de doses para o imunobiol�gico
	 *
	 * @param int $doses Quantidade de doses
	 */
	public function SetarDoses($doses)
	{
		if((int)$doses > 0)	$this->_doses = (int)$doses;
	}
	//--------------------------------------------------------------------------
	/**
	 * Texto com descri��o do esquema de doses
	 *
	 * @param string $esquemaDeDoses texto com o esquema
	 */
	public function SetarEsquemaDeDoses ($esquemaDeDoses )
	{
		if( $this->ValidarViaDeAdministracao($esquemaDeDoses )) {
			$this->_esquemaDeDoses = $esquemaDeDoses;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta os eventos adversos mais comuns de determinada vacina
	 *
	 * @param string $eventosAdversosComuns texto com os eventos adversos
	 */
	public function SetarEventosAdversosComuns($eventosAdversosComuns)
	{
		if( $this->ValidarEventosAdversosComuns($eventosAdversosComuns )) {
			$this->_eventosAdversosComuns = $eventosAdversosComuns;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta um array com as etnias de determinada vacina
	 *
	 * @param array $etnias etnias
	 */
	public function SetarEtnias($etnias)
	{
		if( count($etnias) ) $this->_etnias = $etnias;
		else                 $this->_etnias = $this->etniasExistentes;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta um array com os estados de determinada vacina
	 *
	 * @param array $estados estados
	 */
	public function SetarEstados($estados)
	{
		if( count($estados) ) $this->_estados = $estados;
		else                  $this->_estados = $this->estadosExistentes;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta a idade inicial em que a vacina pode ser aplicada
	 *
	 * @param int $idade idade inicial
	 * @param int $unidadeDeTempo numero correspondende a unidade de tempo da idade
	 */
	public function SetarFaixaEtariaInicio($idade, $unidadeDeTempo)
	{

		if( $this->ValidarFaixaEtaria($idade, $unidadeDeTempo) ) {
			$this->_faixaEtariaInicio = $this->ConvertUnidTempParaDias($idade, $unidadeDeTempo);

		}

	}
	//--------------------------------------------------------------------------
	/**
	 * Seta a idade final em que a vacina pode ser aplicada
	 *
	 * @param int $idade idade final
	 * @param int $unidadeDeTempo numero correspondende a unidade de tempo da idade
	 */
	public function SetarFaixaEtariaFim($idade, $unidadeDeTempo)
	{

		if( $this->ValidarFaixaEtaria($idade, $unidadeDeTempo) ) {

			$this->_faixaEtariaFim = $this->ConvertUnidTempParaDias($idade, $unidadeDeTempo);
		}


		//$this->_faixaEtariaFim = $idade;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta texto com as indica��es para determinada vacina
	 *
	 * @param string $indicacoes texto com as indica��es
	 */
	public function SetarIndicacoes($indicacoes)
	{
		if( $this->ValidarIndicacoes($indicacoes)) {
			$this->_indicacoes = $indicacoes;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta texto com a idade ideal para aplica��o de determinada vacina
	 *
	 * @param string $idadeDeAplicacao
	 */
	public function SetarIdadeDeAplicacao($idadeDeAplicacao)
	{
		if( $this->ValidarIdadeDeAplicacao($idadeDeAplicacao )) {
			$this->_idadeDeAplicacao = $idadeDeAplicacao;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta texto com precau��es de determinada vacina
	 *
	 * @param string $precaucoes texto com as precau��es
	 */
	public function SetarPrecaucoes  ($precaucoes)
	{
		if( $this->ValidarPrecaucoes($precaucoes )) {
			$this->_precaucoes = $precaucoes;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta texto que explica os locais onde a vacina deve ser aplicada
	 *
	 * @param string $viaDeAdministracao texto com locais de aplica��o
	 */
	public function SetarViaDeAdministracao($viaDeAdministracao)
	{
		if( $this->ValidarViaDeAdministracao($viaDeAdministracao)) {
			$this->_viaDeAdministracao = $viaDeAdministracao;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta a vacina de tal campanha id
	 *
	 * @param int $id campanha_id
	 */
	// Talvez n�o precise! Verificar isso!
	public function SetarVacinaDaCampanhaId($id)
	{
		$this->_vacinaDaCampanhaId = $id;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o ou os sexos de acordo com o que foi marcado.
	 * Se foram marcados todos ou nenhum seta ambos se n�o seta o que foi marcado.
	 *
	 * @param unknown_type $sexo
	 */
	public function SetarSexo($sexo)
	{
		if(count($sexo) == 0)				$this->_sexo = 'ambos';
		elseif(isset($sexo[0],$sexo[1]))	$this->_sexo = 'ambos';
		elseif(isset($sexo[0]))				$this->_sexo = 'F';
		elseif(isset($sexo[1]))				$this->_sexo = 'M';

	}

	//--------------------------------------------------------------------------

	///////////////////////////////// RETORNAR /////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * retorna a quantidade de aplica��es por pessoa
	 *
	 * @return int
	 */
	public function AplicacoesPorPessoa()
	{
		return $this->_aplicacoesPorPessoa;
	}
	//--------------------------------------------------------------------------
	/**
	 * retorna o nome da campanha
	 *
	 * @return string
	 */
	public function Campanha()
	{
		return $this->_nomeDaCampanha;
	}
	//--------------------------------------------------------------------------
	/**
	 * retorna a composi��o da vacina
	 *
	 * @return string
	 */
	public function Composicao()
	{
		return $this->_composicao;
	}
	//--------------------------------------------------------------------------
	/**
	 * retorna texto com explica��o de como conservar determinada vacina
	 *
	 * @return string
	 */
	public function Conservacao()
	{
		return $this->_conservacao;
	}

	//--------------------------------------------------------------------------
	/**
	 * retorna texto com as contra indica��es de determinada vacina
	 *
	 * @return string texto com contraindica��es
	 */
	public function ContraIndicacoes()
	{
		return $this->_contraIndicacoes;
	}

	//--------------------------------------------------------------------------
	/**
	 * Retorna um array com as datas ideais de aplica��o.  Se o par�metro $dose
	 * for passado, ent�o retornar� a data daquela dose espec�fica.
	 *
	 * @param bool|string $dose Falso ou a dose desejada (ex. 2)
	 * @return array|string Datas ideais de aplica��o, ou data da dose passada
	 */
	public function DatasIdeaisDeAplicacao($dose = false)
	{
		if ($dose - 1 > 0 && $dose <= count($this->_aplicacoesIdeais)) {

			return $this->_aplicacoesIdeais[$dose - 1];
		}

		return $this->_aplicacoesIdeais;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna um array com as datas reais de aplica��o.  Se o par�metro $dose
	 * for passado, ent�o retornar� a data daquela dose espec�fica.
	 *
	 * @param bool|string $dose Falso ou a dose desejada (ex. 2)
	 * @return array|string Datas reais de aplica��o, ou data da dose passada
	 */
	public function DatasReaisDeAplicacao($dose = false)
	{
		if ($dose - 1 > 0 && $dose <= count($this->_aplicacoesReais)) {

			return $this->_aplicacoesReais[$dose - 1];
		}
		return $this->_aplicacoesReais;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna a quantidade de doses deste imunobiol�gico
	 *
	 * @return int Quantidade de doses
	 */
	public function Doses()
	{
		return $this->_doses;
	}
	//--------------------------------------------------------------------------
	/**
	 * retorna  o esquema de doses de determinada vacina.
	 *
	 * @return string texto com esquema de doses
	 */
	public function EsquemaDeDoses()
	{
		return $this->_esquemaDeDoses;
	}
	//--------------------------------------------------------------------------
	/**
	 * retorna texto com os eventos adversos mais comuns de determinada vacina
	 *
	 * @return string eventos adversos
	 */
	public function eventosAdversosComuns()
	{
		return $this->_eventosAdversosComuns;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retona string de estados que vem do banco de dados ou array com os mesmo
	 *
	 * @return string/array
	 */
	public function Estados()
	{
		return $this->_estados;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retona string de etnias que vem do banco de dados ou array com as mesmas
	 *
	 * @return string/array
	 */
	public function Etnias()
	{
		return $this->_etnias;
	}
	//--------------------------------------------------------------------------
	protected function FaixaEtariaInicio()
	/**
	 * Retona retorna a faixa etaria inicial
	 *
	 * @return int
	 */
	{
		return $this->_faixaEtariaInicio;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna a faixa etaria final de determinada vacina
	 *
	 * @return int
	 */
	protected function FaixaEtariaFim()
	{
		return $this->_faixaEtariaFim;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna as indica��es de determinada vacina
	 *
	 * @return string
	 */
	public function indicacoes()
	{
		return $this->_indicacoes;
	}
	//--------------------------------------------------------------------------
	/**
	 * retorna texto com a idade de aplica��o
	 *
	 * @return string
	 */
	public function IdadeDeAplicacao ()
	{
		return $this->_idadeDeAplicacao;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o numero maximo de doses
	 *
	 * @return int maximo de doses
	 */
	public function IntervalosDasDoses()
	{
		return self::MAXIMO_DE_DOSES;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna determinadas precau��es que devem ser tomadas para determinada vacina
	 *
	 * @return string
	 */
	public function Precaucoes()
	{
		return $this->_esquemaDeDoses;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna a unidade de tempo convertida para dias
	 *
	 * @param int $quantidade tempo
	 * @param int $unidade numero da correspondente a unidade de tempo
	 * @return int tempo convertido para dias
	 */
	public function RetornarUnidadeDeTempoConvertida($quantidade, $unidade)
	{
		$this->_unidadeEmDias =
		$this->ConvertUnidTempParaDias($quantidade, $unidade);

		return $this->_unidadeEmDias;
	}
	//--------------------------------------------------------------------------
	/**
	 * Vacina um indiv�duo existente (esse m�todo n�o pode ficar nesta classe)
	 *
	 * @param int $pessoa Identifica��o da pessoa no banco de dados
	 * @return bool Verdadeiro se a pessoa foi vacinada ou falso caso contr�rio
	 */
	public function Vacinar($pessoa)       //????????????????
	{
		// Se a pessoa existe no banco de dados...
		date_default_timezone_set('America/Sao_Paulo');
		$this->SetarDataRealDeAplicacao( date('d/m/Y') );
		return true;

		// Sen�o existe, retorna falso:
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o texto que explica onde determinada vacina deve ser aplicada
	 *
	 * @return string texto
	 */
	public function ViaDeAdministracao()
	{
		return $this->_viaDeAdministracao;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o id da vacina da cmapanha
	 *
	 * @return int
	 */
	public function VacinaDaCampanhaId()
	{
		return $this->_vacinaDaCampanhaId;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna Id da tabela VacinaDaCampanaha se existir a vacina e a campanha id
	 *
	 * @param int $campanhaId id da campanha
	 * @param int $vacinaId id da vacina
	 * @return int|bool id da vacinaDaCampanha
	 */
	public function RetornarVacinaDaCampanhaId($campanhaId, $vacinaId)
	{
		$id = $this->conexao->prepare('SELECT id FROM `vacinadacampanha`
			WHERE Campanha_id = ? AND Vacina_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$id->bind_param('ii', $campanhaId, $vacinaId);
		$id->bind_result($vacinaDaCampanhaId);
		$id->execute();
		$id->store_result();
		$sucesso = $id->num_rows;
		$id->fetch();
		$id->free_result();

		if($sucesso > 0) return $vacinaDaCampanhaId;
		
		if($sucesso == 0){
			$this->AdicionarMensagemDeErro("N�o existe nenhuma vacina da 
							 campanha que corresponda com esses dados.");
			return false; 
		} 
		
		if($sucesso < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar essa 
															vacina da campanha.');
			return false; 
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o sexo (F, M ou ambos)
	 *
	 * @return string sexo
	 */
	public function Sexo()
	{
		return $this->_sexo;
	}
	//--------------------------------------------------------------------------
	public function BuscarNomeDaVacina($vacinaId)
	{
		$stmt = $this->conexao->prepare('SELECT nome FROM `vacina` WHERE id = ?
			AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_param('i', $vacinaId);
		$stmt->bind_result($nomeVacina);
		$stmt->execute();
		$stmt->store_result();
		$existe = $stmt->num_rows;
		
		if($existe > 0) {
			
			$stmt->fetch();
		}
		
		$stmt->free_result();
		
		if($existe <= 0) {
			
			$nomeVacina = false;
		}
		$stmt->free_result();
		
		$this->nome = $nomeVacina;
		
		return $nomeVacina;
	}
	//--------------------------------------------------------------------------
	public function BuscarNomeDaCampanha($campanhaId)
	{
		$stmt = $this->conexao->prepare('SELECT nome FROM `campanha` WHERE id = ?
			AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_param('i', $campanhaId);
		$stmt->bind_result($nomeCampanha);
		$stmt->execute();
		$stmt->store_result();
		$existe = $stmt->num_rows;
		
		if($existe > 0) {
			
			$stmt->fetch();
		}
		
		$stmt->free_result();
		
		if($existe <= 0) {
			
			$nomeCampanha = false;
		}
		$stmt->free_result();
		
		$this->_nomeDaCampanha = $nomeCampanha;
		
		return $nomeCampanha;
	}
	//--------------------------------------------------------------------------

	///////////////////////////////// VALIDAR //////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Valida as aplica��es por pessoas e retorna true se forem validas e
	 * false com mensagem de erro se n�o forem
	 *
	 * @param int $aplicacoesPorPessoa
	 * @return bool
	 */
	protected function ValidarAplicacoesPorPessoa($aplicacoesPorPessoa)
	{
		for($i=1; $i <= $aplicacoesPorPessoa; $i++) {

			if( empty($_POST["intervalo{$i}"])
			 || empty($_POST["unidadeDeTempoDaDose{$i}"])
			 || (int)$_POST["intervalo{$i}"] < 1 ) {

			 	$this->AdicionarMensagemDeErro("A dose $i foi informada
			 	    como \"{$_POST["intervalo{$i}"]}\" e � inv�lida para o tempo
			 	    expresso em {$_POST["unidadeDeTempoDaDose{$i}"]}.");

			 	return false;
			 }
		}

		return true;
	}
	//--------------------------------------------------------------------------
	/**
	 * Valida o texto de composi��o da vacina e retorna true ou false
	 *
	 * @param string $composicao texto com composi��o
	 * @return bool
	 */
	protected function ValidarComposicao($composicao)
	{
		// Acrescentar mais valida��es:
		if(strlen($composicao) > 1 || strlen($composicao) == 0) return true;

		$this->AdicionarMensagemDeErro('Composi��o inv�lida!');
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Valida texto com as contra indica��es de determinada vacina
	 *
	 * @param string $contraIndicacoes texto com contra indica��es
	 * @return bool
	 */
	protected function ValidarContraIndicacoes($contraIndicacoes)
	{
		// Acrescentar mais valida��es:
		if(strlen($contraIndicacoes) > 1 || strlen($contraIndicacoes) == 0) return true;
		$this->AdicionarMensagemDeErro('Contra-indica��es inv�lidas!');
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Valida o texto de conserva��o de determinada vacina
	 *
	 * @param string $conservacao texto com a conserva��o da vacina
	 * @return bool
	 */
	protected function ValidarConservacao($conservacao )
	{
		// Acrescentar mais valida��es:
		if(strlen($conservacao) > 1 || strlen($conservacao) == 0) return true;

		$this->AdicionarMensagemDeErro('Conserva��o inv�lida!');
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Valida o texto com o esquema de dosagem de determinada vacina
	 *
	 * @param string $esquemaDeDoses texto com o esquema de doses
	 * @return bool
	 */
	protected function ValidarEsquemaDeDoses($esquemaDeDoses )
	{
		// Acrescentar mais valida��es:
		if(strlen($esquemaDeDoses ) > 1 || strlen($esquemaDeDoses) == 0) return true;

		$this->AdicionarMensagemDeErro('Esquema de doses inv�lido!');
		return false;
	}	//----------------------------------------------------------------------
	/**
	 * Valida o texto com os eventos adversos mais comuns de uma vacina
	 *
	 * @param string $eventosAdversosComuns texto com os eventos adversos
	 * @return bool
	 */
	protected function ValidarEventosAdversosComuns($eventosAdversosComuns )
	{
		// Acrescentar mais valida��es:
		if(strlen($eventosAdversosComuns) > 1
		|| strlen($eventosAdversosComuns) == 0) return true;

		$this->AdicionarMensagemDeErro('Eventos adversos inv�lidos!');
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Valida se os POSTs dos formul�rios foram emitidos se foram retorna true se n�o false
	 *
	 * @param string $nomeDoFormulario nome do formul�rio
	 * @return bool
	 */
	public function ValidarFormulario($nomeDoFormulario)
	{
		switch($nomeDoFormulario) {

			case 'inserirVacina':

				if( $this->ValidarNome                 ($_POST['nome'])
				 && $this->ValidarAplicacoesPorPessoa  ($_POST['aplicacoesPorPessoa'])
				 && $this->ValidarComposicao           ($_POST['composicao'])
				 && $this->ValidarConservacao          ($_POST['conservacao'])
				 && $this->ValidarContraIndicacoes     ($_POST['contraIndicacoes'])
				 && $this->ValidarEsquemaDeDoses       ($_POST['esquemaDeDoses'])
				 && $this->ValidarEventosAdversosComuns($_POST['eventosAdversosComuns'])
				 && $this->ValidarIdadeDeAplicacao     ($_POST['idadeDeAplicacao'])
				 && $this->ValidarIndicacoes           ($_POST['indicacoes'])
				 && $this->ValidarPrecaucoes           ($_POST['precaucoes'])
				 && $this->ValidarViaDeAdministracao   ($_POST['viaDeAdministracao'])

				 ){

				 	return true;
				}

				break;

			case 'inserirCaracteristicaNaVacina':
			case 'editarCaracteristicaNaVacina':

				// Se o cara marcou "apenas faixa et�ria"
			 	if( isset($_POST['apenasFaixaEtaria'])) {

			 		// Se a faixa et�ria � v�lida:
			 		if( $this->ValidarFaixaEtaria($_POST['faixaetariainicio'],
			 									  $_POST['unidadedetempoinicial'])
			 		&& $this->ValidarFaixaEtaria ($_POST['faixaetariafim'],
			 									  $_POST['unidadedetempofinal']) ){

			 			return true;
					}
					// Se a faixa et�ria n�o � v�lida:
					else {
						return false;
					}
			 	}
			 	// Se n�o foi marcado "apenas faixa et�ria" ent�o � v�lida:
			 	else {
			 		return true;
			 	}
				break;

			default:
				$this->AdicionarMensagemDeErro('Formul�rio inexistente');
				return false;
		}

		$this->AdicionarMensagemDeErro('O formul�rio cont�m um ou mais dados
			inv�lidos e n�o pode ser submetido');

		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se a idade de aplica��o � v�lida e retorna true caso n�o seja retorna false
	 *
	 * @param int $idadeDeAplicacao
	 * @return bool
	 */
	protected function ValidarIdadeDeAplicacao($idadeDeAplicacao )
	{
		// Acrescentar mais valida��es:
		if(strlen($idadeDeAplicacao) > 1) return true;
		$this->AdicionarMensagemDeErro('Idade de aplica��o inv�lida!');
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se o texto das indica��es � v�lido se for retorna true se n�o retorna false
	 *
	 * @param string $indicacoes texto com as indica��es
	 * @return bool
	 */
	protected function ValidarIndicacoes($indicacoes)
	{
		// Acrescentar mais valida��es:
		if(strlen($indicacoes) > 1 || strlen($indicacoes) == 0) return true;

		$this->AdicionarMensagemDeErro('Indica��es inv�lidas!');
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se o texto que indica as formas poss�veis de aplica��o para
	 * determinada vacina � v�lido
	 *
	 * @param string $viaDeAdministracao texto com as vias de administra��o
	 * @return bool
	 */
	protected function ValidarViaDeAdministracao($viaDeAdministracao)
	{
		// Acrescentar mais valida��es:
		if(strlen($viaDeAdministracao) > 1 || strlen($viaDeAdministracao) == 0) return true;

		$this->AdicionarMensagemDeErro('Via de administra��o inv�lida!');
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se o texto com as precau��o que devem ser tomadas para
	 * determinada vacina � v�lido se for retorna true se n�o false
	 *
	 * @param string $precaucoes
	 * @return bool
	 */
	protected function ValidarPrecaucoes($precaucoes )
	{
		// Acrescentar mais valida��es:
		if(strlen($precaucoes) > 1 || strlen($precaucoes) == 0) return true;

		$this->AdicionarMensagemDeErro('Precau��es inv�lidas!');
		return false;
	}
	//--------------------------------------------------------------------------

	//////////////////////////////// EXIBIR ////////////////////////////////////
	/**
	 * Monta uma tabela com todos os dados extras de determinada vacina para ajudar
	 * na vacina��o
	 *
	 * @param int $id id da vacina que ter� seus detalhes exibidos
	 */
	//--------------------------------------------------------------------------
	public function ExibirDetalhesDaVacina($id)
	{
		$selecVacina = $this->conexao->prepare('SELECT nome, aplicacoesporpessoa,
			faixaetariainicio, faixaetariafim, indicacoes, contraindicacoes,
			composicao, viadeadministracao,	esquemadedosagem, precaucoes,
			conservacao, idadedeaplicacao, eventosadversoscomuns
			FROM `vacina` WHERE id = ? AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selecVacina->bind_param('i', $id);

		$selecVacina->bind_result($nome, $aplicacoesPorPessoa,
			$faixaEtariaInicio, $faixaEtariaFim, $indicacoes,
			$contraIndicacoes, $composicao,	$viaDeAdministracao,
			$esquemaDeDosagem, $precaucoes, $conservacao,
			$idadeDeAplicacao, $eventosAdversosComuns); 

		$selecVacina->execute();

		$selecVacina->fetch();

        echo "<br /><center><strong>$nome</strong></center>";

        //=========== NOTA TECNICA ==========
        $crip = new Criptografia();

        parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
        
        if($pagina == 'Adm/vacinas')
        {
           

            $queryString = $crip->Cifrar("pagina=notasDaVacina&vacina_id={$id}");

            list($local)  = explode('index', $_SERVER['PHP_SELF']);

            ?>
                <div style="padding-left: 10px;">
                    <button onclick="javascript: AbrirJanela('<?php echo $local."Pop/?$queryString"; ?>', 250, 250)">Notas T�cnicas</button>
                </div>
            <?php
        }
        //===================================

		echo '<br />';
		echo '<table width="98%" border="1px" cellpadding="10" cellspacing="0"
				bordercolor="#CCCCCC" rules="rows" align="center">';

		echo "<tr><td><strong>Nome:</strong></td>
		          <td style='text-align:justify'>$nome</td>
		      </tr>";

		echo "<tr><td><strong>Aplica��es por indiv�duo:</strong></td>
		          <td style='text-align:justify'>$aplicacoesPorPessoa</td>
		      </tr>";

		echo '<tr><td><strong>Faixa et�ria:</strong></td><td>'
			   . $this->ConverterDiasParaUnidadeDeTempo($faixaEtariaInicio)
			   . ' a ' . $this->ConverterDiasParaUnidadeDeTempo($faixaEtariaFim)
			   . '</td>
			  </tr>';

		echo "<tr><td><strong>Indica��es:</strong></td>
		          <td style='text-align:justify'>$indicacoes</td>
		      </tr>";

		echo "<tr><td><strong>Contra Indica��es:</strong></td>
		          <td style='text-align:justify'>$contraIndicacoes</td>
		      </tr>";

		echo "<tr><td><strong>Composi��o:</strong></td>
		          <td style='text-align:justify'>$composicao</td>
		      </tr>";

		echo "<tr><td><strong>Via de Administra��o:</strong></td>
		          <td style='text-align:justify'>$viaDeAdministracao</td>
		      </tr>";

		echo "<tr><td><strong>Esquema de dosagem:</strong></td>
		          <td style='text-align:justify'>$esquemaDeDosagem</td>
		      </tr>";

		echo "<tr><td><strong>Precau��es:</strong></td>
		          <td style='text-align:justify'>$precaucoes</td>
		      </tr>";

		echo "<tr><td><strong>Conserva��o:</strong></td>
		          <td style='text-align:justify'>$conservacao</td>
		      </tr>";

		echo "<tr><td><strong>Idade de Aplica��o:</strong></td>
		          <td style='text-align:justify'>$idadeDeAplicacao</td>
		      </tr>";

		echo "<tr><td><strong>Eventos Adversos mais comuns:</strong></td>
		          <td style='text-align:justify'>$eventosAdversosComuns</td>
		      </tr>";

		echo '</table>';
	}
	//--------------------------------------------------------------------------
	/**
	 * Listas as caracter�sticas de determinada vacina e campanha e exibe
	 * op��es para as mesmas (editar e excluir)
	 *
	 * @param int $idVacina id da vacina que ter� as caracteristicas listada
	 * @param int $idCampanha id da canpanha que pertence a vacina
	 */
	public function ExibirListaDeCaracteristicas($idVacina, $idCampanha = false,
		$arquivo_origem)
	{

		$crip = new Criptografia();

		parse_str($crip->Decifrar($_SERVER['QUERY_STRING']) );

		$caracDaVacinaId = false;
		if( isset($configId) ) $caracDaVacinaId = (int)$configId;

		$selectCaracDeNomeDaVacina = $this->conexao->prepare('SELECT nome FROM
			`vacina` WHERE id = ? AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selectCaracDeNomeDaVacina->bind_param('i', $idVacina);
		$selectCaracDeNomeDaVacina->bind_result($nomeDaVacina);
		$selectCaracDeNomeDaVacina->execute();
		$selectCaracDeNomeDaVacina->store_result();
		$nomeExiste = $selectCaracDeNomeDaVacina->num_rows;
		$selectCaracDeNomeDaVacina->fetch();
		$selectCaracDeNomeDaVacina->free_result();

		if($nomeExiste > 0) {

			echo "<h3><center>$nomeDaVacina</center></h3>";
			
			$sql = 'SELECT
				configuracaodavacina.id AS `configId`,
				configuracaodavacina.idadeInicio,
				configuracaodavacina.idadeFinal,
				configuracaodavacina.sexo,
				configuracaodavacina.etnias AS `Etnia`,
				configuracaodavacina.estados AS `Estado`

				FROM `configuracaodavacina`, `vacina`, `vacinadacampanha`

				WHERE vacina.ativo

				AND vacina.id = vacinadacampanha.Vacina_id
				AND configuracaodavacina.VacinaDaCampanha_id = vacinadacampanha.id

				AND vacinadacampanha.Vacina_id = ' . $idVacina . '
				AND vacinadacampanha.Campanha_id = ' . $idCampanha;

			$selectCaracDeVacina = $this->conexao->query($sql)
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$corFundoTitulo = 'bgcolor="#E0E0E0"';

			echo "<center>
				  <table width='645px' border='1px' cellpadding='0'
				  	cellspacing='0' bordercolor='#CCCCCC' rules='cols'>
				  <th $corFundoTitulo>Caracter�stica</th>
				  <th $corFundoTitulo>Idade Inicial</th>
				  <th $corFundoTitulo>Idade Final</th>
				  <th $corFundoTitulo>Sexo</th>
				  <th $corFundoTitulo>Etnia</th>
				  <th $corFundoTitulo>Estado UF</th>
				  <th colspan='2'$corFundoTitulo>A��es</th>";

			$selecionou = $selectCaracDeVacina->num_rows;
				  
			if($selecionou > 0) {
				$cont = 1;

				while ( $linha = $selectCaracDeVacina->fetch_assoc() ){

				 if($cont%2 != 0) $cor = 'bgcolor="#ffffff"';
				 else 			  $cor = 'bgcolor="#F0F0F0"';

				 $editar  = $crip->Cifrar("pagina=editarCaracteristicaDaVacina&vacinaid=$idVacina&campanhaid=$idCampanha&configId={$linha['configId']}&arquivo_origem={$arquivo_origem}");
				 $excluir = $crip->Cifrar("pagina=excluirCaracteristicaDaVacina&vacinaid=$idVacina&campanhaid=$idCampanha&configId={$linha['configId']}&arquivo_origem={$arquivo_origem}");

				 echo"<tr $cor>
					<td align='center'>{$cont}</td>
					<td align='center'>{$this->ConverterDiasParaUnidadeDeTempo($linha['idadeInicio'])}</td>
					<td align='center'>{$this->ConverterDiasParaUnidadeDeTempo($linha['idadeFinal'])}</td>
					<td align='center'>{$linha['sexo']}</td>
					<td align='center'>{$linha['Etnia']}</td>
					<td align='center'>{$linha['Estado']}</td>
					<td align='center'>

						<a href='?$editar'>
							<img src='{$this->arquivoGerarIcone}?imagem=editar' alt='editar' border='0' />
						</a>
					</td>
					<td align='center'>
					   	<a href='?$excluir'>
							<img src='{$this->arquivoGerarIcone}?imagem=excluir' alt='excluir' border='0' />
						</a>
					</td>
					</tr>";
				 $cont++;
				}
				echo '</table>
					  </center>';
				
				return true;
			}
			
			$selectCaracDeVacina->free_result();
			
			if($selecionou == 0) {
				
				$this->AdicionarMensagemDeErro('N�o existem caracter�sticas para esta vacina');
				return  false;
			}
			
			if($selecionou < 0) {
				
				$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar essa caracter�sticas;');				
				return  false;
			}

		} 
		
		// Se o nome n�o existe:
		if($nomeExiste == 0) {
			$this->AdicionarMensagemDeErro('Vacina n�o pode ser encontrada');
			return false;
		}
		
		if($nomeExiste < 0){
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar essa vacina!');
			return false;
		}
	}
	//--------------------------------------------------------------------------
	/*
	 * Exibe o fomul�rio de edi��o de uma caracteristica de determinada vacina e campanha
	 * pega os dados dos retuns que foram setados no arquivo editar por outros m�todos.
	 *
	 */
	//public function EditarCaracteristicaDaVacina()
	public function ExibirFormularioEditarCaracteristicaDaVacina()
	{
	?>
	<div align="left" style="padding-left:8px">
	<br />
	<h2 align="center"><?php echo ucfirst(strtolower($this->Campanha())) ?></h2>
	<h3 align="center"><?php echo $this->Nome() ?></h3>
	
	<h4 align="center">Os indiv�duos que ser�o inclu�dos na faixa vacin�vel da popula��o</h4>

	<form name="editarConfig" id="editarConfig" method="POST"
		action="<?php echo $_SERVER['REQUEST_URI']?>">

	<div class="conjuntoConfiguracao">
	  <div class="linhaFormulario">

	  <label>
	  <input type="checkbox" checked="true" name="apenasFaixaEtaria" id="apenasFaixaEtaria" />
	  Ter�o idade entre
	  </label>
	  <input type="text" name="faixaetariainicio" id="faixaetariainicio"
			 value="<?php 
			 			
			 	if(isset($_POST['faixaetariainicio'])) echo $_POST['faixaetariainicio'];
			 	else echo $this->_faixaEtariaInicio;
			 		
			 		?>" style="width:50px"
			 maxlength="3" />
	  		 <?php $this->SelelctUnidadesDeTempo($this->_unidadeDeTempfaixaEtariaInicio, 'inicial') ?>
	  e
	  <input type="text" name="faixaetariafim" id="faixaetariafim"
			 value="<?php
				
				if(isset($_POST['faixaetariafim'])) echo $_POST['faixaetariafim'];
			 	else echo $this->_faixaEtariaFim;			  
			  
					?>" style="width:50px"
			 maxlength="3" />
	  		 <?php $this->SelelctUnidadesDeTempo($this->_unidadeDeTempfaixaEtariaFim, 'final') ?>


	  </div>
	<p>
	  <div class="linhaFormulario">
			<?php $this->ExibirSexo($this->_sexo); ?>
	  </div>
	</p>
	<div class="linhaFormulario">

		<div id="listarEtnias" align="left">
		<?php $this->ListarEtnias($this->Etnias());?>
		</div>

		<div id="listarEstados" align="left">
		<?php $this->ListarEstados($this->Estados());?>
		</div>

	</div>
	</div>
	<?php $this->ExibirBotoesDoFormulario('Salvar', 'Limpar');?>
	</form>
	</div>
	<?php
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe o formulario para inserir uma vacina no sistema e n�o em uma campanha
	 *
	 */
	public function ExibirFormularioInserirVacina()
	{
	?>
	  <form id="form1" name="form1" method="post" action="">
	  <div align="center">Adicionar vacina</div>
	  <p>
		<div align="right" style="margin-right:390px;">Nome:
			<input type="text" name="nome" size="58"
			value="<?php if(isset($_POST['nome'])) echo $_POST['nome']?>"
			maxlength="70" />
		  </div>
	  </p>
	  <p>

	  <p><center>Via de Administra��o:</center></p>
	  <p>
	    <div align="right" style="margin-right:390px;">
	    	<textarea  name="viaDeAdministracao" cols="50" rows="3"><?php
	    	if(isset($_POST['viaDeAdministracao']))
	    		echo $_POST['viaDeAdministracao']?></textarea>
	    </div>
	  </p>

	  <p>
	  <div align="right" style="margin-right:590px;">Doses por indiv�duo:
	    <select name="aplicacoesPorPessoa" onchange="AdicionarDoses(this.value)"
	    		onblur="ValidarCampoSelect(this, 'Doses por indiv�duo')">
	     	<?php  $this->NumeroDeAplicacoesPorPessoa() ?>
	    </select>
	   </div>
	  </p>
	  <p>
	  		<div id="cadaDose" align="center"></div>
	  </p>
	  <p>
	  <div align="right" style="margin-right:470px;">Faixa Et�ria de
	  <input type="text" name="faixaEtariaInicio" maxlength="3" size="5"
	     value="<?php if(isset($_POST['faixaEtariaInicio']))
	     	echo $_POST['faixaEtariaInicio']?>" />
	   <select name="UnidadeDeTempoInicio">
	      <option value="1">Dia(s)</option>
	      <option value="2">Semana(s)</option>
	      <option value="3">M�s(es)</option>
	      <option value="4" selected="true">Ano(s)</option>
	    </select> a
	  <input type="text" name="faixaEtariaFim" maxlength="5" size="5"
	     value="<?php if(isset($_POST['faixaEtariaFim']))
	     	echo $_POST['faixaEtariaFim']?>" />

	   <select name="UnidadeDeTempoFim">
	      <option value="1">Dia(s)</option>
	      <option value="2">Semana(s)</option>
	      <option value="3">M�s(es)</option>
	      <option value="4" selected="true">Ano(s)</option>
	    </select>
	   </div>
	  </p>

	  <p><center>Indica��es:</center></p>
	  <p>
	    <div align="right" style="margin-right:390px;">
	    	<textarea  name="indicacoes" cols="50" rows="3"><?php
	    	if(isset($_POST['indicacoes']))
	    		echo $_POST['indicacoes']?></textarea>
	    </div>
	  </p>

	  <p><center>Contra Indica��es:</center></p>
	  <p>
	    <div align="right" style="margin-right:390px;">
	    	<textarea  name="contraIndicacoes" cols="50" rows="3"><?php
	    	if(isset($_POST['contraIndicacoes']))
	    		echo $_POST['contraIndicacoes']?></textarea>
	    </div>
	  </p>

	  <p><center>Composi��o:</center></p>
	  <p>
	    <div align="right" style="margin-right:390px;">
	    	<textarea  name="composicao" cols="50" rows="3"><?php
	    	if(isset($_POST['composicao']))
	    		echo $_POST['composicao']?></textarea>
	    </div>
	  </p>

	  <p><center>Esquema de dosagem:</center></p>
	  <p>
	    <div align="right" style="margin-right:390px;">
	    	<textarea  name="esquemaDeDoses" cols="50" rows="3"><?php
	    	if(isset($_POST['esquemaDeDoses']))
	    		echo $_POST['esquemaDeDoses']?></textarea>
	    </div>
	  </p>

	  <p><center>Precau��es:</center></p>
	  <p>
	    <div align="right" style="margin-right:390px;">
	    	<textarea  name="precaucoes" cols="50" rows="3"><?php
	    	if(isset($_POST['precaucoes']))
	    		echo $_POST['precaucoes']?></textarea>
	    </div>
	  </p>

	  <p><center>Conserva��o:</center></p>
	  <p>
	    <div align="right" style="margin-right:390px;">
	    	<textarea  name="conservacao" cols="50" rows="3"><?php
	    	if(isset($_POST['conservacao']))
	    		echo $_POST['conservacao']?></textarea>
	    </div>
	  </p>

	  <p><center>Idade de aplica��o:</center></p>
	  <p>
	    <div align="right" style="margin-right:390px;">
	    	<textarea  name="idadeDeAplicacao" cols="50" rows="3"><?php
	    	if(isset($_POST['idadeDeAplicacao']))
	    		echo $_POST['idadeDeAplicacao']?></textarea>
	    </div>
	  </p>

	  <p><center>Eventos Adversos Comuns:</center></p>
	  <p>
	    <div align="right" style="margin-right:390px;">
	    	<textarea  name="eventosAdversosComuns" cols="50" rows="3"><?php
	    	if(isset($_POST['eventosAdversosComuns']))
	    		echo $_POST['eventosAdversosComuns']?></textarea>
	    </div>
	  </p>

	  <p><center>
	  	<?php $this->ExibirBotoesDoFormulario('Adicionar', 'Desfazer')?>
	    </center>
	  </p>
	</form>
 	<?php
	}

	/**
	 * Exibe o formulario para inserir uma caracter�stica em uma vacina
	 * cadastrada em uma campanha
	 *
	 */
	public function ExibirFormularioInserirCaracteristica()
	{
	?>
	<div align="left">
	
	<h2 align="center"><?php echo ucfirst(strtolower($this->Campanha())) ?></h2>
	<h3 align="center"><?php echo $this->Nome() ?></h3>

	<div style="padding-left:8px;">

	<h4 align="center">Os indiv�duos que ser�o inclu�dos na faixa vacin�vel da popula��o</h4>

	<form name="editarConfig" id="editarConfig" method="POST"
		action="<?php echo $_SERVER['REQUEST_URI']?>">

	<div class="conjuntoConfiguracao">

		<div class="linhaFormulario">
		<label>
			<input type="checkbox" name="apenasFaixaEtaria" id="apenasFaixaEtaria"
			onclick="ExibirIdade('exibirIdade', this.checked)" />
			Ter�o apenas idade entre
		</label>
		<div id='exibirIdade'></div>
		</div><!-- Fecha "linhaFormulario" -->

		<div class="linhaFormulario">
		<label>
			<input type="checkbox" name="sexo" id="sexo"
			onclick="ExibirSexo('exibirSexo', this.checked)" />
			Ser�o apenas do sexo
		</label>
		<div id='exibirSexo'></div>
		</div><!-- Fecha "linhaFormulario" -->

		<div class="linhaFormulario">
		<label>
			<input type="checkbox" name="apenasEtnias" id="apenasEtnias"
			onclick="ListarEtnias('listaDeEtnias', this.checked)" />
			Ser�o apenas das etnias
		</label>
		<div id='listaDeEtnias'></div>
		</div><!-- Fecha "linhaFormulario" -->

		<div class="linhaFormulario">
		<label>
			<input type="checkbox" name="apenasEstados" id="apenasEstados"
			onclick="ListarEstados('listaDeEstados', this.checked)" />
			Residir�o apenas nos estados
		</label>
		<div id='listaDeEstados'></div>
		</div><!-- Fecha "linhaFormulario" -->

	</div><!-- Fecha "conjuntoConfiguracao" -->

	<?php $this->ExibirBotoesDoFormulario('Salvar', 'Limpar');?>
	</form>
	</div>
	</div>
	<?php
	}

	////////////////////////////// VERIFICAR ///////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Verifica a n�o duplicidade de uma vacina e retorna true se n�o existir
	 *
	 * @return bool
	 */
	public function VerificarNaoDuplicidadeDeVacina()
	{
		$selecVacina = $this->conexao->prepare('SELECT nome FROM `Vacina`
			WHERE nome = ? AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selecVacina->bind_param('s', $this->nome);

		$selecVacina->execute();
		$selecVacina->store_result();

		$registroJaExiste = $selecVacina->num_rows;

		$selecVacina->free_result();

		if($registroJaExiste == 0) return true;
		
		// Vacina n�o existe
	
		if($registroJaExiste > 0) {
			
			// Vacina existe
			
			return false;
		}
		
		if($registroJaExiste < 0) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao verificar a 
												n�o duplicidade da vacina.');
			return false;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica a n�o duplicadade da caracter�stica de uma vacina de determinda campanha
	 * se n�o existe retorna false caso exista retorna true. Funciona para o editar
	 * e excluir
	 *
	 * @param int $id id da configura��o da vacina (usado somente no editar)
	 * @return bool
	 */
	public function VerificarNaoDuplicidadeDeCaracteristica($id = false)
	{
		$vacinaDaCampanhaId = $this->VacinaDaCampanhaId();
		$idadeInicio =  $this->FaixaEtariaInicio();
		$idadeFinal = $this->FaixaEtariaFim();
		$sexo = $this->Sexo();
		$etnias = implode(', ', $this->Etnias() );
		$estados = implode(', ', $this->Estados() );

		if( count($this->Etnias()) == self::NUMERO_DE_ETNIAS
			||  count($this->Etnias()) == 0 ) {

				$etnias = 'todas';
		}
		if( count($this->Estados()) == self::NUMERO_DE_ESTADOS
			||  count($this->Estados()) == 0 ) {

				$estados = 'todos';
		}
		if($id) {
			$carac = $this->conexao->prepare('SELECT id FROM `configuracaodavacina`
						WHERE id <> ?
						AND VacinaDaCampanha_id = ? AND idadeInicio = ?
						AND idadeFinal = ? AND sexo = ? AND etnias = ?
						AND estados = ?')
						or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$carac->bind_param('iiiisss', $id, $vacinaDaCampanhaId, $idadeInicio,
									  $idadeFinal, $sexo, $etnias, $estados);
		}
		else {
			$carac = $this->conexao->prepare('SELECT id FROM `configuracaodavacina`
						WHERE VacinaDaCampanha_id = ? AND idadeInicio = ?
						AND idadeFinal = ? AND sexo = ? AND etnias = ?
						AND estados = ?')
						or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$carac->bind_param('iiisss', $vacinaDaCampanhaId, $idadeInicio,
									  $idadeFinal, $sexo, $etnias, $estados);
		}

		$carac->execute();
		$carac->store_result();
		$existe = $carac->num_rows;
		$carac->free_result();

		// Existe
		if( $existe > 0 ) {
			
			$this->AdicionarMensagemDeErro('Uma configura��o com esta caracter�stica
				j� existe para esta vacina nesta campanha');
			
			return false;
		}
		
		// N�o existe
		if( $existe == 0 ) {
			
			return true;
		}

		if( $existe < 0 ) {
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar a 
										configura��o desta vacina na campanha');

			return false;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se determinada id da vacina existe e retorna true caso n�o
	 * exista retorna false
	 *
	 * @param int $id id da vacina a ser verificada
	 * @return bool
	 */
	public function VerificarSeIdDaVacinaExiste($id)
	{
		$selecVacina = $this->conexao->prepare('SELECT id FROM `vacina`
			WHERE id = ? AND ativo') 
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selecVacina->bind_param('i', $id);
		$selecVacina->execute();
		$selecVacina->store_result();

		$idExiste = $selecVacina->num_rows;
		
		$selecVacina->free_result();
		
		// Existe
		if($idExiste > 0) return true;
		
		if($idExiste == 0) {
			
			$this->AdicionarMensagemDeErro('N�o existe nenhuma vacina 
													correnpondente com essa id');
			return false;
		}
		
		if($idExiste < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar o 
																id da vacina');
			return false;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se determinada id da configura��o existe e retorna true caso n�o
	 * exista retorna false
	 *
	 * @param int $id id da configura��o a ser verificada
	 * @return bool
	 */
	public function VerificarSeIdDaCaracteristicaExiste($id)
	{
		$carac = $this->conexao->prepare('SELECT id FROM `configuracaodavacina`
			WHERE id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$carac->bind_param('i', $id);
		$carac->execute();
		$carac->store_result();
		$existe = $carac->num_rows;
		$carac->free_result();

		// Existe
		if($existe > 0) return true;
		
		if($existe == 0) {
			
			$this->AdicionarMensagemDeErro('N�o existe nenhuma configura��o 
													correnpondente com essa id');
			return false;
		}
		
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar o 
														id desta configura��o');
			return false;
		}
	}
	//--------------------------------------------------------------------------
	// Remover depois esse m�todo por causa do Ajax
	/*
	private function NumeroDeIntervalos()
	{
		$aplicacoes = self::MAXIMO_DE_DOSES;




		for($i=1; $i <= $aplicacoes; $i++) {

		   echo
		   '<div align="right" style="margin-right:470px;">Inervalo Dose '.$i.'
		   <input type="text" name="intervalo'.$i.'" maxlength="5" size="5"/>
		   <select name="UnidadeDeTempoIntervalo'.$i.'">
		     <option value="dias">Dia(s)</option>
		     <option value="semanas">Semana(s)</option>
		     <option value="meses">M�s(es)</option>
		     <option value="anos" selected="true">Ano(s)</option>
		   </select>
		   </div>';
		  }
	}
	*/
	//--------------------------------------------------------------------------

	//////////////////////////////// SELECIONAR ////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Seleciona os dados a partir de uma id de determinada caracter�stica para editalos
	 *
	 * @param int $caracVacinaId id da caracter�stica
	 */

	// TROCAR O NOME PARA "BUSCAR DADOS PARA EDITAR..."
	public function SelecionarDadosParaEditarCaracteristicaDaVacina($caracVacinaId = false)
	{
		$selecDadosCaracVacina = $this->conexao->prepare('SELECT
	  					vacina.nome AS `nomeVacina`,
						campanha.nome AS `nomeCampanha` ,
						configuracaodavacina.idadeInicio,
						configuracaodavacina.idadeFinal,
						configuracaodavacina.sexo,
						configuracaodavacina.etnias,
						configuracaodavacina.estados,
						vacinadacampanha.id

						FROM `vacina`,`vacinadacampanha`,
						`campanha`, `configuracaodavacina`

						WHERE configuracaodavacina.VacinaDaCampanha_id = vacinadacampanha.id
						AND vacina.id = vacinadacampanha.Vacina_id
						AND campanha.id = vacinadacampanha.Campanha_id
						AND configuracaodavacina.id = ?')
						or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selecDadosCaracVacina->bind_param('i', $caracVacinaId);

		$selecDadosCaracVacina->bind_result($vacina, $campanha, $idadeInicial,
		$idadeFinal, $sexo, $stringEtnias, $stringEstados, $vacinaDaCampanhaId);

		$selecDadosCaracVacina->execute();

		//echo $selecDadosCaracVacina->error;

		$selecDadosCaracVacina->fetch();
		$selecDadosCaracVacina->free_result();

		//echo $vacina.'-'.$campanha.'-'.$idadeInicial.'-'.$idadeFinal.'-'.$sexo.'-'.$caracVacinaId;

		$this->_sexo = $sexo;

		$this->nome = $vacina;

		$this->_nomeDaCampanha = $campanha;

		$this->_vacinaDaCampanhaId = $vacinaDaCampanhaId;

		$etnias = explode(', ', $stringEtnias);
		if( $stringEtnias == 'todas' ) $etnias = $this->etniasExistentes;

		$this->SetarEtnias($etnias);

		$estados = explode(', ', $stringEstados);
		if( $stringEstados == 'todos' ) $estados = $this->estadosExistentes;

		$this->SetarEstados($estados);


  		//______________________________________________________________________

		$idadeInicial = $this->ConverterDiasParaUnidadeDeTempo($idadeInicial);

		$idadeInicial = explode(' ', $idadeInicial);

		$this->_faixaEtariaInicio = $idadeInicial[0];
		$this->_unidadeDeTempfaixaEtariaInicio = $idadeInicial[1];


  		//______________________________________________________________________

		$idadeFinal = $this->ConverterDiasParaUnidadeDeTempo($idadeFinal);

		$idadeFinal = explode(' ', $idadeFinal);

		$this->_faixaEtariaFim = $idadeFinal[0];
		$this->_unidadeDeTempfaixaEtariaFim = $idadeFinal[1];

	}
	//--------------------------------------------------------------------------
	/**
	 * Seleciona e retona a id de uma vacina a partir de seu nome anteriormente setado
	 *
	 * @return int id da vacina
	 */
	public function SelecionarIdDaVacina()
	{
	  $selecVacina = $this->conexao->prepare('SELECT id FROM `vacina` WHERE nome = ? AND ativo')
	  	or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

      $selecVacina->bind_param('s', $this->nome);

      $selecVacina->bind_result($id);

      $selecVacina->execute();

      $selecVacina->fetch();

      $Vacina_id = $id;

      $selecVacina->free_result();

      return $Vacina_id;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seleciona e retorna o id de uma vacinada da campanha
	 *
	 * @param int $campanhaid id de determida campanha
	 * @param int $vacinaid id de determinada vacina
	 * @return int id da vacina da campanha
	 */
	public function SelecionarVacinaDaCampanhaId($campanhaid, $vacinaid)
	{
		$vacinaDacampanhaId = $this->conexao->prepare('SELECT id FROM `vacinadacampanha`
			WHERE campanha_id = ? AND vacina_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$vacinaDacampanhaId->bind_param('ii', $campanhaid, $vacinaid);

		$vacinaDacampanhaId->bind_result($vacinaDacampanha_id);

		$vacinaDacampanhaId->execute();

		$vacinaDacampanhaId->fetch();

		return $vacinaDacampanha_id;
	}
	//--------------------------------------------------------------------------
	/**
	 * Monta o Select da unidade de tempo para o form
	 *
	 * @param string $unidade unidade de tempo
	 * @param string $inicialOuFinal se a unidade � final ou inicial
	 */
	public function SelelctUnidadesDeTempo($unidade, $inicialOuFinal)
	{
		$selected = 'selected="true"';

		echo "<select name=\"unidadedetempo$inicialOuFinal\"
						id=\"unidadedetempo$inicialOuFinal\">";

			if(trim($unidade) == 'dia(s)') {
				echo "<option value=\"1\" $selected>dia(s)</option>";
			}
			else {
				echo "<option value=\"1\">dia(s)</option>";
			}

			if(trim($unidade) == 'semana(s)') {
				echo "<option value=\"2\" $selected>semana(s)</option>";
			}
			else {
				echo "<option value=\"2\">semana(s)</option>";
			}

			if(trim($unidade) == 'mes(es)') {
				echo "<option value=\"3\" $selected>mes(es)</option>";
			}
			else {
				echo "<option value=\"3\">mes(es)</option>";
			}

			if(trim($unidade) == 'ano(s)') {
				echo "<option value=\"4\" $selected>ano(s)</option>";
			}

			else {
				echo "<option value=\"4\">ano(s)</option>";
			}

		echo '</select>';
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe os checks do sexo para o inserir e editar
	 *
	 * @param string $sexo sexo(F, M, ambos)
	 */
	public function ExibirSexo($sexo = false) // Usado na tAjax:
	{

		$checkf = $checkm = ' ';

		if($sexo == 'ambos')			$checkf = $checkm = ' checked="true" ';
		if($sexo == 'F')				$checkf = ' checked="true" ';
		if($sexo == 'M')				$checkm = ' checked="true" ';

		echo '<div style="width: 440px">';

		echo '<fieldset><legend>Configure o(s) sexo(s) abaixo</legend>';

		echo '<label><input type="checkbox"'
			. $checkf.' name="sexo[0]" id="sexo[0]" />Feminino</label>';

		echo '<label><input type="checkbox"'
			. $checkm.'name="sexo[1]" id="sexo[1]" />Masculino</label>';

		echo '</fieldset>';

		echo '</div>';
	}
	//--------------------------------------------------------------------------

	////////////////////////////////// INSERIR /////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Insere uma caracteristica em uma vacina
	 *
	 * @param int $campanhaId id da campanha
	 * @param int $vacinaDaCampanhaId id da vacina da campanha
	 */
	public function InserirCaracteristicaDaVacina($campanhaId,
		$vacinaDaCampanhaId)
	{
		if (empty($_POST['apenasEtnias'])) {
			$this->SetarEtnias($this->etniasExistentes);
		}

		if (empty($_POST['apenasEstados'])) {
			$this->SetarEstados($this->estadosExistentes);
		}

		$idadeInicio =  $this->FaixaEtariaInicio();
		$idadeFinal = $this->FaixaEtariaFim();
		$sexo = $this->Sexo();
		$etnias = implode(', ', $this->Etnias() );
		$estados = implode(', ', $this->Estados() );


		if( count($this->Etnias()) == self::NUMERO_DE_ETNIAS
			||  count($this->Etnias()) == 0 ) {

				$etnias = 'todas';
		}
		if( count($this->Estados()) == self::NUMERO_DE_ESTADOS
			||  count($this->Estados()) == 0 ) {

				$estados = 'todos';
		}

		$inserir = $this->conexao->prepare('INSERT INTO `configuracaodavacina`
			(id, VacinaDaCampanha_id, idadeInicio, idadeFinal, sexo,
			etnias, estados)
			VALUES(NULL, ?, ?, ?, ?, ?, ?)')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$inserir->bind_param('iiisss', $vacinaDaCampanhaId, $idadeInicio,
			$idadeFinal, $sexo, $etnias, $estados);

		$inserir->execute();
		$inseriu = $inserir->affected_rows;

		$inserir->close();

		if( $inseriu > 0 ) {
			
			return true;
		}

		$this->AdicionarMensagemDeErro('Caracter�stica n�o inserida');
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Insere um intervalo para uma dose de determinada vacina que pertence
	 * a determinada campanha
	 *
	 * @param int $Vacina_id id da vacina que vai receber o intervalo
	 * @param int $intervalo intervalo de tempo em dias
	 * @param int $i posi�ao no array do intervalo
	 */
	public function InserirIntervaloDaDose($Vacina_id, $diaidealparavacinar,
											$numerodadose, $atrasomaximo)
	{
		if(!$atrasomaximo) $atrasomaximo = 43800;

		$inserirIntervalodaDose = $this->conexao->prepare('INSERT INTO
				`intervalodadose`
				(id, Vacina_id, diaidealparavacinar, numerodadose, atrasomaximo)
				VALUES (NULL, ?, ?, ?, ?)')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

	    if($diaidealparavacinar) {

		    $inserirIntervalodaDose->bind_param('iiii', $Vacina_id,
		    	$diaidealparavacinar, $numerodadose, $atrasomaximo);

		    $inserirIntervalodaDose->execute();
	    }
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria  uma vacina completa no sistema incluindo os textos complementares
	 * (Composi��o, Indica��es, Conserva��o, Contra Indica��es, etc.)
	 *
	 */
	public function InserirVacina()
	{
		if($this->VerificarNaoDuplicidadeDeVacina()) {
		$inserirVacnia = $this->conexao->prepare('INSERT INTO `vacina` VALUES
 	  		(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

	    $inserirVacnia->bind_param('siiisssssssss', $this->nome, $this->_aplicacoesPorPessoa,
	    $this->_faixaEtariaInicio, $this->_faixaEtariaFim, $this->_indicacoes,
	    $this->_contraIndicacoes, $this->_composicao,  $this->_viaDeAdministracao,
	    $this->_esquemaDeDoses, $this->_precaucoes, $this->_conservacao,
	    $this->_idadeDeAplicacao, $this->_eventosAdversosComuns);

	    $inserirVacnia->execute();

	    if($inserirVacnia->affected_rows){

			$this->ExibirMensagem("$this->nome adicionada com sucesso!");
	    }
	    else {
	   		$this->AdicionarMensagemDeErro('Vacina n�o foi inserida. Tente novamente!');
	    }
	    $inserirVacnia->close();
		}
		else {
			$this->AdicionarMensagemDeErro("$this->nome n�o foi inserida. Vacina j� existe!");
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe a parte do formulario para adicionar doses doses a uma vacina
	 * quando o ajax � acionado.
	 *
	 */

	public function AdicionarDoses() // Para o Ajax, m�todo chamado na tAjax
	{
		parse_str($_SERVER['QUERY_STRING']); // Cria a vari�vel $qtd

		if($qtd) {

			echo '<div align="left" style="width: 560px">
				<fieldset><legend>Configure a(s) dose(s) abaixo</legend>';

			for($i=1; $i <= $qtd; $i++) {

				$intervaloPost = "intervalo{$i}";
				$unidTempoPost = "unidadeDeTempoDaDose{$i}";

				$intervaloAtrasoPost = "intervaloAtraso{$i}";
				$unidTempoAtrasoPost = "unidadeDeTempoDoAtraso{$i}";

				$textoLabel = ' ap�s a dose ' . ($i - 1) . '.';
				if($i == 1) $textoLabel = ' de nascido.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';

				?>
				<div>
				Dose <?php echo $i?>:
				<input type="text" name="<?php echo $intervaloPost?>"
					maxlength="5" style="width: 40px; text-align: center" />
				<select name="<?php echo $unidTempoPost?>">
					<option value="1">Dia(s)</option>
					<option value="2">Semana(s)</option>s
					<option value="3">M�s(es)</option>
					<option value="4" selected="true">Ano(s)</option>
				</select>
				<?php echo $textoLabel, ' Atraso m�ximo: ';?>

				<input type="text" name="<?php echo $intervaloAtrasoPost?>"
					id="<?php echo $intervaloAtrasoPost?>"
					maxlength="5" style="width: 40px; text-align: center" />

				<select name="<?php echo $unidTempoAtrasoPost?>">
					<option value="1">Dia(s)</option>
					<option value="2">Semana(s)</option>s
					<option value="3">M�s(es)</option>
					<option value="4" selected="true">Ano(s)</option>
				</select>

				</div>
				<?php
			}
			echo '</fieldset></div>';
		}
	}
	////////////////////////////////// EDITAR //////////////////////////////////
	//--------------------------------------------------------------------------
	/**
	 * Edita uma determinada caracter�stica de uma vacina e campanha
	 *
	 * @param int $caracDaVacinaId id da caracteristica da vacina a ser editada
	 * @return bool retorna true se foi atualizada e false se nao foi
	 */
	public function EditarCaracteristicaDaVacina($caracDaVacinaId)
	{
		$idadeInicio =  $this->FaixaEtariaInicio();
		$idadeFinal = $this->FaixaEtariaFim();
		$sexo = $this->Sexo();
		$etnias = implode(', ', $this->Etnias() );
		$estados = implode(', ', $this->Estados() );


		if( count($this->Etnias()) == self::NUMERO_DE_ETNIAS
			||  count($this->Etnias()) == 0 ) {

				$etnias = 'todas';
		}
		if( count($this->Estados()) == self::NUMERO_DE_ESTADOS
			||  count($this->Estados()) == 0 ) {

				$estados = 'todos';
		}

		$editar = $this->conexao->prepare('UPDATE `configuracaodavacina`
								 SET	idadeInicio = ?, idadeFinal = ?,
								 		sexo = ?, etnias = ?, estados = ?
								 WHERE	id = ?')
								or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
								
		$editar->bind_param('iisssi', $idadeInicio,
			$idadeFinal, $sexo, $etnias, $estados, $caracDaVacinaId);

		$editar->execute();
		$atualizou = $editar->affected_rows;
		$editar->close();

		if($atualizou  > 0 ) return true;
				
		if($atualizou  <= 0 ) {
			$this->AdicionarMensagemDeErro('Caracter�stica n�o atualizada');
			return false;
		}
		
	}
	//--------------------------------------------------------------------------

	///////////////////////////////// EXCLUIR //////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Exclui uma caracter�stica de vaicina de determinada campanha
	 *
	 * @param int $campanhaId id da campanha
	 * @param int $vacinaId id da vacina que a caracter�stica pertence
	 * @param int $caracId id da caracter�stica da campanha
	 * @return bool retorna true se excluiu e false se n�o
	 */
	public function ExlcuirCaracteristicaDaVacina($campanhaId, $vacinaId, $caracId)
	{
		// Primeiro verificar se tem mais de uma caracter�stica para esta vacina:

		$vacinaDaCampanhaId = $this->RetornarVacinaDaCampanhaId($campanhaId,
																$vacinaId);

		$caracs = $this->conexao->prepare('SELECT id FROM `configuracaodavacina`
					WHERE VacinaDaCampanha_id = ?')
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		//die((string)$vacinaDaCampanhaId);

		$caracs->bind_param('i', $vacinaDaCampanhaId);
		$caracs->execute();
		$caracs->store_result();
		$QtdCaracs = $caracs->num_rows;
		$caracs->free_result();

		if($QtdCaracs > 1) {

			$excluir = $this->conexao->prepare('DELETE FROM `configuracaodavacina`
				WHERE id = ?') 
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$excluir->bind_param('i', $caracId);
			$excluir->execute();
			$caracExcluida = $excluir->affected_rows;
			$excluir->close();

			if($caracExcluida) {

				return true;
			}
			else { // Se a caracter�stica da vacina n�o foi exclu�da:

				$this->AdicionarMensagemDeErro('A caracter�stica n�o pode ser
					exclu�da');
			}

		} 
		// Se quantidade de Caracter�sticas � igual a 1:
		if($QtdCaracs == 1){
			$this->AdicionarMensagemDeErro('Esta caracter�stica n�o pode ser
				exclu�da, por ser <em>a �nica</em> desta vacina (toda vacina deve
				possuir no m�nimo uma caracter�stica padr�o). Edite esta ou crie
				mais caracter�sticas para que voc� possa escolher quais excluir.');
		
			return false;
		}
		// Se n�o tem caracter�stica:
		if($QtdCaracs == 0){
			$this->AdicionarMensagemDeErro('N�o existem caracter�sticas.');
		
			return false;
		}
		if($QtdCaracs < 0) {
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar as 
													configura��o desta vacina');
			return false;
		}
	}
	//--------------------------------------------------------------------------

	//////////////////////////////// DIVERSOS ///////////////////////////////////

	//--------------------------------------------------------------------------
	/*
	public function ExibirFormularioDeCaracteristicaDaVacinaDaCampanha()

	{   echo '<form id="form0" name="form0" method="POST">';

		$this->SelecionarVacinaParaCaracteristica(28);
		$this->ExibirBotoesDoFormulario('Adicionar', 'Desfazer');

		echo '</form>';
	}
	*/
	//--------------------------------------------------------------------------
	/*public function SelecionarVacinaParaCaracteristica($idCampanha)
	{
	  $selecVacina = $this->conexao->prepare('SELECT vacina.id, vacina.nome, campanha.nome
		FROM `Vacina`,`VacinaDaCampanha`, campanha
		WHERE vacina.id = VacinaDaCampanha.Vacina_id AND
		campanha.id = VacinaDaCampanha.Campanha_id AND
		VacinaDaCampanha.Campanha_id = ?')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

      $selecVacina->bind_param('i', $idCampanha);

      $selecVacina->bind_result($id, $nome, $campanha);

      $selecVacina->execute();

      echo $selecVacina->error;

      while ($selecVacina->fetch()){
      	//echo '<option value="'.$id.'">'.$nome.'</option>';
      	$this->ExibirFormularioDeCaracteristica($nome, $campanha);
      }
      $selecVacina->free_result();
	}*/

	//--------------------------------------------------------------------------
	/**
	 * Monta os options do select com o numero de doses que vem da constante MAXIMO_DE_DOSES
	 *
	 */
	private function NumeroDeAplicacoesPorPessoa()
	{
		$aplicacoes = self::MAXIMO_DE_DOSES;

		for($i=0; $i <= $aplicacoes; $i++) {

			echo '<option value="'.$i.'">'.$i.'</option>';
		}

	}

	//--------------------------------------------------------------------------
	/**
	 * Conecta na base de dados e pesquisa as cidades do estado escolhido.
	 * Para o Ajax, m�todo chamado na tAjax
	 *
	 */
	public function PesquisarIntercorrencia()
	{
		parse_str($_SERVER['QUERY_STRING']); // Cria a vari�vel $codigodoestado
											 // e $codigocidade

		if( isset($codigodaVacina) ) {

			$registros = $this->conexao->prepare('SELECT id, eventoadverso FROM `intercorrencia`
				WHERE vacina_id = ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
				
			$registros->bind_param('i', $codigodaVacina);
			$registros->bind_result($id, $nome);
			$registros->execute();
			$registros->store_result();
			$qtdRegistros = $registros->num_rows;
			
			if($qtdRegistros > 0) {

				// apresentamos cada subcategoria dessa forma:
				// "NOME|CODIGO, NOME|CODIGO, NOME|CODIGO|SELECIONADO,...",
				// exatamente da maneira que iremos tratar no JavaScript:

				while (  $registros->fetch()  ) {

					// O primeiro registro (antes da virgula) fica em branco:

					echo ",$nome|$id";
				}
				$registros->free_result();
				return true;
			}
			
			$registros->free_result();
			
			if($qtdRegistros < 0){
				
				$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar 
															  o evento adverso');
				return false;
			}
			
			if($qtdRegistros == 0){
				
				$this->AdicionarMensagemDeErro('N�o existem eventos adversoss para
																	essa vacina');
				return false;
			}
			
		}
	}

	//--------------------------------------------------------------------------
	/**
	 * Para o Ajax, m�todo chamado na tAjax. Exibe a lista de vacinas quando o
	 * usu�rio escolhe vacinar sem campanha.
	 *
	 * @param array $arrayVacinas
	 */
	public function ListarVacinas($listarDescontinuadas = false, 
                                  $retroativo           = false,
                                  $listarVacinasMae     = true,
                                  $listarVacinasFilhas  = true)
	{
		if (!$listarDescontinuadas) $listarDescontinuadas = " AND grupo_id <> 'Descontinuadas' ";
		else $listarDescontinuadas = '';

        /*
		if (!$retroativo) $retroativo = " WHERE
			vacinadaunidade.Vacina_id = vacina.id
			AND quantidade > 0 AND ";

		else $retroativo = ' WHERE ';

		$sql = "SELECT DISTINCT id, Grupo_id, nome

					FROM `vacina`, `vacinadaunidade`

					$retroativo
					vacinadaunidade.UnidadeDeSaude_id = ?
					$listarDescontinuadas
					AND vacina.ativo

						ORDER BY grupo_id DESC, nome";
		*/

        $sql = "SELECT DISTINCT id, Grupo_id, nome, pertence
                FROM `vacina`
                WHERE vacina.ativo
                ORDER BY grupo_id DESC, nome";

		$vacinas = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$unidade_id = $_SESSION['unidadeDeSaude_id'];

		$vacina_id = 0;
		if( isset($_SESSION['listarPessoasVacinaveis']['vacina'])) {
			$vacina_id = $_SESSION['listarPessoasVacinaveis']['vacina'];
		}

		$grupo_id_anterior = 'nenhum';

		//$vacinas->bind_param('i', $unidade_id);
		$vacinas->bind_result($id, $grupo_id, $nome, $pertence);
		$vacinas->execute();
		$vacinas->store_result();
		$existem_vacinas = $vacinas->num_rows;

        if( $existem_vacinas > 0 ) {

        
			echo "<select name='vacina' id='vacina' style='width: 305px'
				onchange='CarregarVacinasFilhas(\"vacinasFilhas\", this.value);'
                onchange='ValidarCampoSelect(this, \"vacina\");
					SetarTexto(\"listaDePessoas\", \"\")'

				onblur='ValidarCampoSelect(this, \"vacina\")'>";

			$selecionada = '';
			if($vacina_id == 0) $selecionada = "selected='true'";

			echo "\n\t<option value='0' $selecionada>- selecione -</option>";

			while ( $vacinas->fetch() ) {

                if($listarVacinasMae    == false && $this->CarregarVacinasFilhas($id)) continue;
                if($listarVacinasFilhas == false && $pertence) continue;

				if($grupo_id_anterior != $grupo_id) {
					echo "<optgroup label='$grupo_id'>";
					$grupo_id_anterior = $grupo_id;
				}

				$selecionada = '';
				if($vacina_id == $id) $selecionada = "selected='true'";

				echo "\n\t<option value='$id' $selecionada>$nome</option>";

				if($grupo_id_anterior != $grupo_id) echo '</optgroup>';

			}

			echo "</select>";

			$vacinas->free_result();

			return true;
		}

		$vacinas->free_result();

		if( $existem_vacinas == 0 ) {

			echo '<p>N�o existem vacinas com estoque nesta unidade.</p>';

			return false;
		}

		if( $existem_vacinas < 0 ) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar listar
				as vacinas para esta unidade.');

			return false;
		}
        

	}//--------------------------------------------------------------------------
	/**

	 */
	public function CarregarVacinasFilhas($pertence)
	{

        $sql = "SELECT DISTINCT id, Grupo_id, nome
                FROM `vacina`
                WHERE vacina.ativo AND pertence = $pertence 
                ORDER BY grupo_id DESC, nome";

		$vacinas = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));


		$vacinaFilha_id = 0;
		if( isset($_SESSION['listarPessoasVacinaveis']['VacinaFilha_id'])) {
			$vacinaFilha_id = $_SESSION['listarPessoasVacinaveis']['VacinaFilha_id'];
		}

		$dados = '';
	
		$vacinas->bind_result($id, $grupo_id, $nome);
		$vacinas->execute();
		$vacinas->store_result();
		$existem_vacinas = $vacinas->num_rows;

		if( $existem_vacinas > 0 ) {

			$dados .= "<select name='vacinaFilha' id='vacinaFilha' style='width: 305px'>";

			$dados .= "\n\t<option value='0'>- selecione -</option>";


			while ( $vacinas->fetch() ) {

                $selecionado = '';
                if($id == $vacinaFilha_id ) $selecionado = " selected='true' ";
				$dados .= "\n\t<option value='$id' $selecionado >$nome</option>";

			}

			$dados .= "</select>";

			$vacinas->free_result();

			return $dados;
		}

		$vacinas->free_result();

		if( $existem_vacinas == 0 ) {

			return false;
		}

		if( $existem_vacinas < 0 ) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar listar
				as vacinas para esta unidade.');

			return false;
		}
	}
	//----------------------------------------------------------------------
	public function VacinaPertenceAoGrupo($vacina_id, $grupo_id)
	{
		if( !(int)$vacina_id ) return false;
		
		$stmt = $this->conexao->prepare('SELECT id FROM vacina WHERE
						id = ? AND Grupo_id = ? AND ativo');
		
		$stmt->bind_param('is', $vacina_id, $grupo_id);
		$stmt->bind_result($vacinaId);
		
		$stmt->execute();
		$stmt->store_result();
		
		if( $stmt->num_rows >0 ){
			
			$stmt->fetch();
			$stmt->free_result();
			return true;
			
		}
		
		$stmt->free_result();
		
		return false;
		
	}
	//----------------------------------------------------------------------
	public function VacinaPertence($vacina_id)
	{
		if( !(int)$vacina_id ) return false;

		$stmt = $this->conexao->prepare('SELECT pertence FROM vacina WHERE
						id = ? AND ativo');

		$stmt->bind_param('i', $vacina_id);
		$stmt->bind_result($vacinaId);

		$stmt->execute();
		$stmt->store_result();

		if( $stmt->num_rows >0 ){

			$stmt->fetch();
			$stmt->free_result();
			return $vacinaId;

		}

		$stmt->free_result();

		return false;

	}
	//--------------------------------------------------------------------
	
	public function SelectVacinas($listarVacinasMae     = true,
                                  $listarVacinasFilhas  = true)
	{
				
		$sql = "SELECT DISTINCT vacina.id, Grupo_id, nome, pertence
					FROM `vacina`, `vacinadaunidade`
						WHERE vacinadaunidade.UnidadeDeSaude_id = ?
						AND vacina.ativo
							ORDER BY grupo_id DESC, nome";
		
		$vacinas = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$unidade_id = $_SESSION['unidadeDeSaude_id'];
		
		$vacina_id = 0;
		if(isset($_POST['vacina'])) $vacina_id = $_POST['vacina'];
		
		$grupo_id_anterior = 'nenhum';
		
		$vacinas->bind_param('i', $unidade_id);
		$vacinas->bind_result($id, $grupo_id, $nome, $pertence);
		$vacinas->execute();
		$vacinas->store_result();
		$existem_vacinas = $vacinas->num_rows;

		if( $existem_vacinas > 0 ) {

			echo "<select name='vacina' id='vacina' style='width: 305px'
				onchange='ValidarCampoSelect(this, \"vacina\");
					SetarTexto(\"listaDePessoas\", \"\")'
				onblur='ValidarCampoSelect(this, \"vacina\")'>";

			$selecionada = '';
			if($vacina_id == $id) $selecionada = "selected='true'";
			
			echo "\n\t<option value='0' $selecionada>- selecione -</option>";
			
			while ( $vacinas->fetch() ) {

            if($listarVacinasMae    == false && $this->CarregarVacinasFilhas($id)) continue;
            if($listarVacinasFilhas == false && $pertence) continue;


				if($grupo_id_anterior != $grupo_id) {
					echo "<optgroup label='$grupo_id'>";
					$grupo_id_anterior = $grupo_id;
				}

				$selecionada = '';
				if($vacina_id == $id) $selecionada = "selected='true'";

				echo "\n\t<option value='$id' $selecionada>$nome</option>";
				
				if($grupo_id_anterior != $grupo_id) echo '</optgroup>';

			}

			echo "</select>";
			
			$vacinas->free_result();
			
			return true;
		}
		
		$vacinas->free_result();
		
		if( $existem_vacinas == 0 ) {

			echo '<p>N�o existem vacinas com estoque nesta unidade.</p>';
			
			return false;
		}
		
		if( $existem_vacinas < 0 ) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar listar
				as vacinas para esta unidade.');
			
			return false;
		}
	}
	//----------------------------------------------------------------------
	
	public function ExibirFormularioVisualizarVacinas()
	{
		?>
		<form name="VisualizarVacinas" id="VisualizarVacinas" method="POST"
		action="<?php echo $_SERVER['REQUEST_URI']?>"  onsubmit=" return (ValidarCampoSelect(this.vacina, 'vacina', false))" >
			<center><h3>Visualizar Vacina</h3>			
			Vacina: <?php $this->SelectVacinas(true, false); ?>
			</center>
			<p><br />
		    <?php $this->ExibirBotoesDoFormulario('Visualizar');?>
			</p>
		</form>
		<?php
	}
	
	//---------------------------------------------------------------------
	
	public function VisualizarVacinas($vacina_id)
	{
		$this->ExibirDetalhesDaVacina($vacina_id);
	}

    //--------------------------------------------------------------------------
    public function ListarDosesDaVacina($codigodaVacina)
    {

		$sql = "SELECT COUNT(id)
                    FROM intervalodadose
                        WHERE Vacina_id = $codigodaVacina";

		$stmt = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));


        $qtd = false;

		$stmt->bind_result($qtd);
		$stmt->execute();
		$stmt->store_result();
        $stmt->fetch();
        $stmt->free_result();

		if( $qtd > 0 ) {
            
            echo '<p><div class="CadastroEsq">Dose desejada: </div>';
            echo '<div class="CadastroDir">';
            
            echo '<select id="dose_escolhida" name="dose_escolhida">';
            
            $pessoaVacinavel = new PessoaVacinavel();
            $pessoaVacinavel->UsarBaseDeDados();

            for($i=1; $i<=$qtd; $i++) {

                if( $pessoaVacinavel->VerificarTipoDaDose($codigodaVacina, $i) == 2) {

                    static $numeroDoReforco = 1;

                    echo "<option value='$i'>"
                        . 'Refor�o ' . $numeroDoReforco++
                        . "</option>";
                }
                elseif( $pessoaVacinavel->VerificarTipoDaDose($codigodaVacina, $i) == 3) {

                    static $doseEspecial = 1;
                    
                    echo "<option value='$i'>"
                        . 'Dose especial ' . $doseEspecial++
                        . "</option>";
                }
                else echo "<option value='$i'>{$i}� dose</option>";
            }

            echo '</select>';
            echo '</div>';
            echo '</p>';
        }
    }
}