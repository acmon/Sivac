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


//require_once './autoload.php';
/*require_once('./tCriptografia.php');*/

/**
 * AgenteImunizador: Classe mãe que representa um Agnete Imunizador.
 *
 * Esta classe trata de um Agente Imunizador que é o mesmo que uma vacina.
 * Nela é possível executar diversas operações com as vacinas baseando-se nas
 * constantes e em dados pré-cadastrados no banco de dados. As vacinas podem
 * pertencer a uma ou mais campanhas, estados e ainda estar restritas a  por sexo,
 * idade ou etnia.
 *
 *
 * @package Sivac/Class
 *
 * @author Maykon Monnerat (maykon_ttd@hotmail.com)
 * 
 * @copyright 2008 
 *
 */

abstract class AgenteImunizador
{
	const MENOR_ANO = 1950;
	const MAIOR_ANO = 2050;
	const NUMERO_DE_ESTADOS = 27;
	const NUMERO_DE_ETNIAS = 5;
	const IDADE_MINIMA = 1;   // dia
	const IDADE_MAXIMA = 120; // anos

	const DIAS    = 1;
	const SEMANAS = 2;
	const MESES   = 3;
	const ANOS    = 4;

    
	
	protected $url;						// String com host montado no construtor

	protected $conexao;					// mysqli
	protected $nome;					// string
	protected $msgDeErro;				// array de mensagens de erro
	protected $estados;					// array de estados

	protected $arquivoGerarIcone;		// String com o local e nome do gerarIcone.php
	
	protected $estadosExistentes = array('AC', 'AL', 'AM', 'AP', 'BA', 'CE',
										'DF', 'ES', 'GO', 'MA', 'MG', 'MS',
										'MT', 'PA', 'PB', 'PE', 'PI', 'PR',
										'RJ', 'RN', 'RO', 'RR', 'RS', 'SC',
										'SE', 'SP', 'TO');

	protected $etniasExistentes = array('Branca', 'Negra', 'Parda',
										'Amarela', 'Indígena');

	//////////////////////// CONSTRUTOR / DESTRUTOR ////////////////////////////

	//--------------------------------------------------------------------------
	public function __construct()
	{
		$this->msgDeErro = array();
		
		$this->url = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA;
		
		$this->LocalizarArquivoGeradorDeIcone();
		
		
	}
	//--------------------------------------------------------------------------
	public function __destruct()
	{
		if( isset($this->conexao) ) $this->conexao->close();
	}
	//--------------------------------------------------------------------------
	public function LocalizarArquivoGeradorDeIcone()
	{
		if(file_exists("{$_SERVER['DOCUMENT_ROOT']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php")) {
			
			$this->arquivoGerarIcone =
				"http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php";
		}

        return $this->arquivoGerarIcone;
	}
	//////////////////////////////// SETAR /////////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Monta um array com os Estados Existentes
	 *
	 * @param array $estados
	 * @return boolean
	 */
	public function SetarEstado(array $estados)
	{
		if(count($estados) <= self::NUMERO_DE_ESTADOS && count($estados) > 0) {
			foreach ($estados as $estado) {

				if( !in_array($estado, $this->estadosExistentes) ) return false;

			}
		}
		$this->estados = $estados;
		return true;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta nome da campanha, vacina ou qualquer outro agente imunizador
	 *
	 * @param String $nome
	 */
	public function SetarNome($nome)
	{
		if( $this->ValidarNome($nome)) {

			$this->nome = $this->conexao->real_escape_string( trim($nome) );
		}
	}

	/////////////////////////////////RETORNOS //////////////////////////////////

	// Para os pops:
	public function Url()
	{
		return $this->url;
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Retorna um array com os estados nos quais atuam esta vacina
	 *
	 * @return array Estados nos quais a vacina é usada
	 */
	public function Estados()
	{
		return $this->estados;
	}
	//--------------------------------------------------------------------------
	public function Nome()
	{
		return $this->nome;
	}
	
	public function Conexao()
	{
		return $this->conexao;
	}

	///////////////////////////////// VALIDAR //////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Validar a data digitada pelo usuário
	 *
	 * @param String $data
	 * @return boolean
	 */
	protected function ValidarData($data)
	{
		list($dia, $mes, $ano) = preg_split('@[^0-9]{1}@', $data);

		if( isset($dia, $mes, $ano) ) {

			$ano = $this->ConverterAnoCurtoParaLongo($ano);

			$verificarAno = ($ano > self::MENOR_ANO && $ano < self::MAIOR_ANO);

			$completa = checkdate($mes, $dia, $ano);

			if($verificarAno && $completa)	return true;
		}

		$this->AdicionarMensagemDeErro("Verifique se a data $data é valida.");
		return false;
	}

	//--------------------------------------------------------------------------
	/**
	 * Validar o nome digitado pelo usuário
	 *
	 * @param String $nome
	 * @return boolean
	 */
	protected function ValidarNome($nome)
	{
		// Acrescentar mais validações:
		if(strlen($nome) > 1) return true;

		$this->AdicionarMensagemDeErro("Verifique o preenchimento do nome.
			(\"$nome\" é inválido)");

		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Validar a idade entre 1 e 100 anos
	 *
	 * @param int $idade
	 * @param int $unidadeDeTempo
	 * @return boolean
	 */
	protected function ValidarFaixaEtaria($idade, $unidadeDeTempo)
	{

		switch($unidadeDeTempo) {

			case self::DIAS :
				if($idade > 0 && $idade < (365 * self::IDADE_MAXIMA)
					|| $idade > 0 && $idade < (360 * self::IDADE_MAXIMA) ) return true;
				$unidadeDeTempo = 'dias';
				break;

			case self::SEMANAS :
				if($idade > 0 && $idade < (52 * 7 * self::IDADE_MAXIMA) ) return true;
				$unidadeDeTempo = 'semanas';
				break;

			case self::MESES :
				if($idade > 0 && $idade < (12 * 30 * self::IDADE_MAXIMA) ) return true;
				$unidadeDeTempo = 'meses';
				break;

			case self::ANOS :
				if($idade > 0 && $idade <= (self::IDADE_MAXIMA) ) return true;
				$unidadeDeTempo = 'anos';
				break;

			default:
				return false;
		}

		$this->AdicionarMensagemDeErro("Faixa etária inválida para o valor
			\"$idade\" (fornecido em $unidadeDeTempo)");

		return false;
	}
	///////////////////////////////// EXIBIR ///////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Exibir o botao ok do formulário com a sua respectiva imagem
	 *
	 * @param boolean $submit
	 * @param boolean $reset
	 * @param String $imagemSubmit
	 * @param String $corDoTexto
	 */
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
	/**
	 * Exibe os botoes marcar todos, desmarcar e inverter seleção
	 *
	 * @param String $form
	 * @param String $check
	 * @param boolean $etqMarcar
	 * @param boolean $etqDesmarcar
	 * @param boolean $etqInverter
	 */
	public function ExibirBotoesParaChecks($form, $check, $etqMarcar = false,
		$etqDesmarcar = false, $etqInverter = false)
	{
		$formulario = 'document.' . $form;
		
		echo '<div align="center" style="clear:both">';
		if($etqMarcar) {
			echo "<button name='marcar'
					onclick='MarcarTodas($formulario, \"$check\")'
					type='button' style=\"width:135px;\">$etqMarcar</button>";
		}
		if($etqDesmarcar) {
			echo "<button name='desmarcar'
					onclick='DesmarcarTodas($formulario, \"$check\")'
					type='button' style=\"width:135px;\">$etqDesmarcar</button>";
		}
		if($etqInverter) {
			echo "<button name='inverter'
					onclick='InverterSelecao($formulario, \"$check\")'
					type='button' style=\"width:135px;\">$etqInverter</button>";
		}
		echo '</div>';
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe os botao que não submete o formulario
	 *
	 * @param String $titulo
	 * @param String $onclick
	 */
	public function ExibirBotoesNaoSubmit($titulo, $onclick, $corDoTexto ='#14E')
	{	
		echo '<div align="center" style="clear:both">'; 
		echo "<button name='naoSubmit'
				onclick='$onclick'
				 style='color: $corDoTexto; width: 130px; margin:10px'
				type='button'>";
		
		echo "<img src='{$this->arquivoGerarIcone}?imagem=pesquisar' alt='ok'
			  style='vertical-align: middle' />";
			
		echo "$titulo</button>";
		echo '</div>';
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibir o botão voltar no formulário
	 *
	 * @param String $texto
	 * @param String $imagem
	 * @param String $corDoTexto
	 * @param String $link
	 */
	public function ExibirBotaoVoltar( $texto = 'Campanhas',
		$link = 'pagina=Adm/listarCampanhas', $imagem = 'listar',
		$corDoTexto ='#14E')
	{
		$crip = new Criptografia();
		$querystring = $crip->Cifrar($link);

		echo '<div align="center" style="clear:both">';
		echo "<button name='listar' type='button'
			  style='color: $corDoTexto; width: 130px; margin:10px'
		      onclick=\"window.location='?$querystring'\">";
		      
		echo "<img src='{$this->arquivoGerarIcone}?imagem=$imagem' alt='listar'
			  style='vertical-align: middle' />";
		echo "$texto";
		echo '</button>';
		echo '</div>';
	}

	//--------------------------------------------------------------------------
	/**
	 * Exibir o botão buscar no formulário
	 *
	 * @param String $texto
	 * @param String $imagem
	 * @param String $corDoTexto
	 */
	public function ExibirBotaoBuscar( $texto = 'Buscar', $imagem = 'pesquisar',
		$corDoTexto ='#14E')
	{
		echo '<div align="center" style="clear:both">';
		echo "<button name='buscar' type='submit'
			  style='color: $corDoTexto; width: 130px; margin:10px'>";
			  
		echo "<img src='{$this->arquivoGerarIcone}?imagem=$imagem' alt='listar'
			  style='vertical-align: middle' />";
		echo "$texto";
		echo '</button>';
		echo '</div>';
	}

	//--------------------------------------------------------------------------
	/**
	 * Para o Ajax, método chamado na tAjax. Exibe os campos para o usuário digitar
	 * a idade no Ajax
	 *
	 */
	public function ExibirIdade()
	{
		?>
		<div style="width: 440px">
			  <fieldset><legend>Configure a Faixa Etária abaixo</legend>

		<input type="text" name="faixaetariainicio" id="faixaetariainicio"
			  style="width:50px" />
	  		 <?php $this->SelelctUnidadesDeTempo('dia(s)', 'inicial') ?>
	  e
	  <input type="text" name="faixaetariafim" id="faixaetariafim"
			 style="width:50px" />
	  		 <?php $this->SelelctUnidadesDeTempo('dia(s)', 'final') ?>


		</fieldset></div>
		<?php
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


	//////////////////////////////// VERIFICAR ////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Verifica se o formulário foi emitido
	 *
	 * @return boolean
	 */
	public function VerificarSeEmitiuFormulario()
	{
		if( count($_POST) ) return true;

		return false;
	}

	///////////////////////////////// LISTAR //////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Para o Ajax, método chamado na tAjax. Exibe os campos para o usuário marcar
	 * as etnias no Ajax
	 *
	 * @param array $arrayEtnias
	 */
	public function ListarEtnias( Array $arrayEtnias = array())
	{
		echo '<div style="width: 440px">
			  <fieldset><legend>Configure a(s) Etnia(s) abaixo</legend>';

		$etnias = $this->conexao->prepare('SELECT id, nome FROM `etnia`')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$etnias->bind_result($id, $nome);
		$etnias->execute();

		while ($etnias->fetch())
		{
			$checked = ' ';
			if( in_array($nome, $arrayEtnias) ) $checked = ' checked="true" ';

			echo '<label><input type="checkbox"' . $checked
					. 'name="etnia[]" value="'.$nome.'" />'
					. $nome. '<br /></label>';

		}

		$etnias->free_result();

		$this->ExibirBotoesParaChecks('editarConfig', 'etnia[]', 'Todas as etnias',
			'Nenhuma etnia', 'Inverter seleção');

		echo '</fieldset></div>';
	}
	//--------------------------------------------------------------------------
	/**
	 * Para o Ajax, método chamado na tAjax. Exibe os campos para o usuário marcar
	 * os estados no Ajax
	 *
	 * @param array $arrayEstados
	 */
	public function ListarEstados( Array $arrayEstados = array() )
	{
		echo '<div style="width: 440px">
			  <fieldset><legend>Configure o(s) Estado(s) abaixo</legend>';

		$estados = $this->conexao->prepare('SELECT id, nome FROM `estado`')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$estados->bind_result($id, $nome);
		$estados->execute();

		while ($estados->fetch())
		{
			$checked = ' ';
			if( in_array($id, $arrayEstados) ) $checked = ' checked="true" ';

			echo '<label><input type="checkbox"' . $checked
					. 'name="estado[]" value="'.$id.'" />'
					. $nome. '<br /></label>';

		}

		$estados->free_result();

		$this->ExibirBotoesParaChecks('editarConfig', 'estado[]', 'Todos os estados',
			'Nenhum estado', 'Inverter seleção');

		echo '</fieldset></div>';

	}

	//////////////////////////////// CONVERTER ////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Se o ano for passado com somente duas casas ao invés de quatro:
	 *
	 * @param int $ano
	 * @return int
	 */
	protected function ConverterAnoCurtoParaLongo($ano)
	{
		if($ano >= 50 && $ano < 100) $ano += 1900;
		elseif($ano < 50)            $ano += 2000;

		return $ano;
	}
	//--------------------------------------------------------------------------
	/**
	 * Converte os dias passados para a unidade de tempo (anos, meses, semanas
	 * ou dias)
	 *
	 * @param int $dias
	 * @return boolean|string
	 */
	public function ConverterDiasParaUnidadeDeTempo($dias)
	{

		if ($dias > 0) {

            // Verifica a quantidade de dias que foram adicionadas no caso do
            // ano bisexto (1 dia a cada 4 anos no sistema):
            $diasDoAnoBisexto = $dias % 365;
            $anos = ($dias - $diasDoAnoBisexto) / 365;

            Depurador::Pre("Dias dos anos bisextos: $diasDoAnoBisexto; Anos: $anos");
            // Esta operação considera que de 4 em 4 anos é adicionado um dia
            // para o ano bisexto:
			if($dias % 365 == $diasDoAnoBisexto && $dias > 365) {  // $dias > 365 NAO SEI SE ESTA CERTO
				return ($dias - $diasDoAnoBisexto) / 365 . ' ano(s)';
			}

            // Considerar também ANOS se o usuário colocou p.ex 12 MESES (de 30
            // dias cada, o que dá 360 dias exatos):
			if($dias % 360 == 0) {
				return ($dias) / 360 . ' ano(s)';
			}

			if($dias % 30 == 0) {
				return $dias / 30 . ' mes(es)';
			}

			if($dias % 7 == 0) {
				return $dias / 7 . ' semana(s)';
			}

			return $dias . ' dia(s)';
		}
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Converte dias para unidade de tempo
	 *
	 * @param int $quantidade
	 * @param int $unidade
	 * @return int
	 */
	public function ConvertUnidTempParaDias($quantidade, $unidade)
	{

		//  Ahhhh!!!!  die($quantidade. $unidade .self::DIAS. self::SEMANAS. self::MESES. self::ANOS);

		switch ($unidade) {

			case self::DIAS :
            case 'day':
				return $quantidade;

			case self::SEMANAS :
            case 'week':
				return  $quantidade * 7;

			case self::MESES :
            case 'month':
				return  $quantidade * 30;

			case self::ANOS :
            case 'year':
                // Adiciona os dias para o ano bisexto (1 dia a cada 4 anos):
                $diasParaAnoBisexto = $quantidade % 4;
				return $quantidade * 365 + $diasParaAnoBisexto;

			default: return $quantidade;
		}

	}
	////////////////////////// MÉTODOS DIVERSOS ////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Conexão com a Base de Dados
	 *
	 */
	public function UsarBaseDeDados()
	{
		$this->conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
			$_SESSION['senha']);


		$this->conexao->select_db($_SESSION['banco']);

		//echo mysqli_connect_error(); die;
	}
	//--------------------------------------------------------------------------
	/**
	 * Antes de adicionar a mensagem, verifica se a mesma já existe:
	 *
	 * @param String $mensagem
	 */
	public function AdicionarMensagemDeErro($mensagem)
	{
		if( !in_array($mensagem, $this->msgDeErro) ) {
			$this->msgDeErro[] = $mensagem;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Inverte a data para o padrão americano
	 *
	 * @param int $data
	 * @return int
	 */
	public function InverterData($data)
	{
		list($p1, $p2, $p3) = preg_split('@[^0-9]{1}@', $data);

		if(  isset($p1, $p2, $p3)  ) return "$p3/$p2/$p1";
	}

	//--------------------------------------------------------------------------
	/**
	 * Conecta na base de dados e pesquisa as cidades do estado escolhido.
	 * Para o Ajax, método chamado na tAjax
	 *
	 */
	public function PesquisarCidades()
	{
		parse_str($_SERVER['QUERY_STRING']); // Cria a variável $codigodoestado
							// e $codigocidade
							
		if( isset($codigodoestado, $codigocidade) ) {

			$sql = 'SELECT id, nome
					FROM `cidade`
					WHERE Estado_id = ?
						ORDER BY nome';
			
			$registros = $this->conexao->prepare($sql) or
				die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$registros->bind_param('s', $codigodoestado);
			$registros->bind_result($id, $nome);
			$registros->execute();

			// apresentamos cada subcategoria dessa forma:
			// "NOME|CODIGO, NOME|CODIGO, NOME|CODIGO|SELECIONADO,...",
			// exatamente da maneira que iremos tratar no JavaScript:

			$primeiro = true;
			
			while (  $registros->fetch()  ) {

				// O primeiro registro (antes da virgula) fica em branco:
				if($codigocidade == $id) $selecionado = 1;
				else $selecionado = 0;

				if(!$primeiro) echo ',';
				else echo '- selecione -|0,';
				
				echo $nome. "|$id|$selecionado";
				$primeiro = false;
			}

			$registros->free_result();
			$registros->close();
		}
		//if( $codigodoestado == 0 ) echo '- selecione -|0,';
	}
	//--------------------------------------------------------------------------
	public function PesquisarAcs()
	{
		parse_str($_SERVER['QUERY_STRING']); // Cria a variável $codigodoestado
											 // e $codigocidade
		
		if( isset($codigodaunidade, $codigoacs) ) {

			$sql = 'SELECT id, nome
					FROM `acs`
					WHERE UnidadeDeSaude_id = ?
						AND acs.ativo ORDER BY nome';
			
			$registros = $this->conexao->prepare($sql) or
				die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$registros->bind_param('s', $codigodaunidade);
			$registros->bind_result($id, $nome);
			$registros->execute();

			// apresentamos cada subcategoria dessa forma:
			// "NOME|CODIGO, NOME|CODIGO, NOME|CODIGO|SELECIONADO,...",
			// exatamente da maneira que iremos tratar no JavaScript:
			
			echo '- selecione -|0';

			while (  $registros->fetch()  ) {

				if($codigoacs == $id) $selecionado = 1;
				else $selecionado = 0;
				
				echo ",", Html::FormatarMaiusculasMinusculas($nome) . "|$id|$selecionado";
			}

			$registros->free_result();
			$registros->close();
		}
	}
	//--------------------------------------------------------------------------	/**
	/**
	 * Esta função foi necessária pois não foi possível usar sequencialmente
	 * PesquisarCidades(); PesquisarAcs();, por causa do Ajax ser assíncrono.
	 * Assim, tivemos de retornar o resultado todo ao mesmo tempo e depois dividir
	 * ainda mais.
	 */
	public function PesquisarCidadesEAcs()
	{
		//resultado da pesquisa de cidades (sera a posicao 0  do array)
		$this->PesquisarCidades();
		
		// divide o resultado
		echo ';';  
		
		//resultado da pesquisa de acs (sera a posicao 1 do array)
		$this->PesquisarAcs();
	}
	//--------------------------------------------------------------------------

	/**
	 * Atualiza uma janela com os valores adicionados em outra janela
	 *
	 * @param String $janelaAnterior URI da janela que deve ser atualizada
	 * @param boolean $propriaJanela Se a janela atual deve ser atualizada
	 */
	public function AtualizarJanelas($janelaAnterior, $propriaJanela = false)
	{
		echo '<script language="javascript">';

		// Atualiza a janela anterior:
		echo "AtualizarOutraJanela('$janelaAnterior');";

		// Se a própria janela deve ser atualizada:
		if($propriaJanela) {

			echo "location.href = '{$_SERVER['REQUEST_URI']}';";
		}
		echo '</script>';
	}
	//--------------------------------------------------------------------------
}