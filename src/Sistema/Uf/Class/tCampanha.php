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

//------------------------------------------------------------------------------
/**
 * Classe usada para criação, edição e exclusão de campanhas de vacinação. É
 * importante observar que uma campanha contém diversas vacinas e que uma vacina
 * pode possuir varias características e que cada característica é um conjunto
 * de atributos (como idade início e fim para vacinação, sexo, estados e etnias
 * envolvidas). Este conjunto pode ser definido por fatores como "surto em
 * tais estados" ou "epidemia em determinadas etnias", além da característica
 * imunizadora presente já em cada vacina.
 *
 * 
 * @package Sivac/Class
 *
 * @author Maykon Monnerat (maykon_ttd@hotmail.com), v 1.0, 2008/07
 *
 * @copyright 2008 
 * 
 */
class Campanha extends AgenteImunizador
{
	private $_vacinas;							// array de vacinas
	private $_dataInicio;						// string
	private $_dataFim;							// string
	private $_obs;								// string

	//--------------------------------------------------------------------------

	// CONSTRUTOR e DESTRUTOR //////////////////////////////////////////////////

	//--------------------------------------------------------------------------
	public function __construct()
	{
		parent::__construct();
		$this->_vacinas = array();
	}
	//--------------------------------------------------------------------------
	public function __destruct()
	{
		parent::__destruct();
	}
	//--------------------------------------------------------------------------

	// SETAR  //////////////////////////////////////////////////////////////////

	//--------------------------------------------------------------------------
	public function SetarDados($post) {

		$clean = Preparacao::GerarArrayLimpo($post, $this->conexao);

		$this->SetarNome      ($clean['nome']);
		$this->SetarDataInicio($clean['dataInicio']);
		$this->SetarDataFim   ($clean['dataFim']);
		$this->SetarObs       ($clean['obs']);
	}
	//--------------------------------------------------------------------------
	public function SetarDataFim($data)
	{
		if( $this->ValidarDataFimDaCampanha($data) ) {

			$dataEscapada =	$this->conexao->real_escape_string( trim($data) );

			list($dia, $mes, $ano) = preg_split('@[^0-9]{1}@', $data);

			$ano = $this->ConverterAnoCurtoParaLongo($ano);

			$this->_dataFim = "$dia/$mes/$ano";
		}
	}
	//--------------------------------------------------------------------------
	public function SetarDataInicio($data)
	{
		if( $this->ValidarDataInicioDaCampanha($data) ) {

			$dataEscapada =	$this->conexao->real_escape_string( trim($data) );

			list($dia, $mes, $ano) = preg_split('@[^0-9]{1}@', $dataEscapada);;

			$ano = $this->ConverterAnoCurtoParaLongo($ano);

			$this->_dataInicio = "$dia/$mes/$ano";
		}
	}
	//--------------------------------------------------------------------------
	public function SetarObs($obs)
	{
		if( $this->ValidarObsDaCampanha($obs) ) {
			$this->_obs = addslashes(trim($obs));
		}
	}

	//--------------------------------------------------------------------------
	public function SetarVacinas(array $vacinas)
	{
		if(count($vacinas) > 0 && count($vacinas) < 5) {
			$this->_vacinas = $vacinas;
			return true;
		}
		return false;
	}
	//--------------------------------------------------------------------------

	// RETORNOS ////////////////////////////////////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Retorna a data final no formato dd/mm/aaaa
	 *
	 * @return string
	 */
	public function DataFim()
	{
		return $this->_dataFim;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna a data inicial no formato dd/mm/aaaa
	 *
	 * @return string
	 */
	public function DataInicio()
	{
		return $this->_dataInicio;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna um texto de observação da campanha
	 *
	 * @return string
	 */
	public function Obs()
	{
		return stripslashes($this->_obs);
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna um array com as vacinas da campanha
	 *
	 * @return array
	 */
	public function Vacinas()
	{
		return $this->_vacinas;
	}
	//--------------------------------------------------------------------------

	// VALIDAR  ////////////////////////////////////////////////////////////////
	//--------------------------------------------------------------------------
	/**
	 * Verifica se o nome da campanha é valido
	 *
	 * @param String $nomeCampanha
	 * @return boolean
	 */
	private function ValidarNomeDaCampanha( $nome ) {
		$permitidos = " 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
					  çÇáéíóúàèìòùâêîôûäëïöüãõÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÃÕ";
		if( strlen($nome) > 2  &&
			strlen($nome) == strspn($nome, $permitidos) ) {
			return true;
		} else {
			$this->AdicionarMensagemDeErro("Campanha $nome é inválida");
			return false;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se a data no formato dd/mm/aa ou dd/mm/aaaa é válida. A validação
	 * de uma data de fim da campanha também verifica se a data informada é
	 * menor que a atual. Caso seja, a data não é considerada válida.
	 *
	 * @param String $dataFim
	 * @return boolean
	 */ 
	private function ValidarDataFimDaCampanha($dataFim) {

		list($dia, $mes, $ano) = preg_split('@[^0-9]{1}@', $dataFim);

		if(isset($dia, $mes, $ano)) {
			$ano = $this->ConverterAnoCurtoParaLongo($ano);
			$intData = $ano . $mes . $dia;
		}

		date_default_timezone_set('America/Sao_Paulo');

		if( isset($intData)
		    && $this->ValidarData($dataFim)
		    && $intData >= date('Ymd') ) {

			return true;
		}

		$this->AdicionarMensagemDeErro("Verifique se a data de fim é valida
			($dataFim) e se a mesma não é antiga demais.");

		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Valida a data de início. A data inicial não poderá ter o ano menor que o
	 * especificado na classe base como MENOR_ANO.
	 *
	 * @param String $dataInicio
	 * @return boolean
	 */
	private function ValidarDataInicioDaCampanha($dataInicio) {

		list($dia, $mes, $ano) = preg_split('@[^0-9]{1}@', $dataInicio);

		if(isset($dia, $mes, $ano)) {
			$ano = $this->ConverterAnoCurtoParaLongo($ano);
		}

		if(  $this->ValidarData($dataInicio)  ) {
			return true;
		}

		$this->AdicionarMensagemDeErro("Verifique se a data de início
						($dataInicio) é valida.");

		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Valida o formulário, de acordo com o nome passado. O nome é necessário
	 * pois as regras podem mudar de formulário para formulário.
	 *
	 * @param String $nomeDoFormulario Nome do formulário
	 * @return boolean
	 */
	public function ValidarFormulario($nomeDoFormulario)
	{
		switch($nomeDoFormulario) {

			case 'inserirCampanha':
			case 'editarCampanha' :
				if( $this->ValidarNomeDaCampanha       ($_POST['nome'])
				 && $this->ValidarDataInicioDaCampanha ($_POST['dataInicio'])
				 && $this->ValidarDataFimDaCampanha    ($_POST['dataFim'])
				 && $this->ValidarObsDaCampanha        ($_POST['obs'])  ) {

				 	return true;
				}

				break;

			case 'excluirCampanha':
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
	/**
	 * Valida a observação digitada pelo usuário. A validação é a aplicada em
	 * "texto longo".
	 *
	 * @param String $obs Texto de observação
	 * @return boolean
	 */
	private function ValidarObsDaCampanha($obs)
	{
		// Acrescentar mais validações:
		if(  strlen(trim($obs)) > 5 || strlen(trim($obs)) == 0 ) return true;

		$this->AdicionarMensagemDeErro("O texto de observação fornecido
			é inválido. Deixe-o em branco ou preencha corretamente.");
		return false;
	}
	//--------------------------------------------------------------------------

	// EXIBIR  /////////////////////////////////////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Exibe os dados da campanha
	 *
	 * @param int $id Identificador da campanha passada.
	 */
	public function ExibirDadosDaCampanha($id)
	{
		$campanhas = $this->conexao->prepare('SELECT nome, datainicio, datafinal
			FROM campanha WHERE id = ? AND ativo ORDER BY nome')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$campanhas->bind_param('i', $id);
		$campanhas->bind_result($nome, $dataInicio, $dataFinal);
		$campanhas->execute();
		$campanhas->store_result();
		$existe = $campanhas->num_rows;

		if($existe > 0) {

			while ( $campanhas->fetch() ) {

				echo "<p>Campanha $nome</p>";
				echo "<p>Início em {$this->InverterData($dataInicio)}</p>";
				echo "<p>Final em {$this->InverterData($dataFinal)}</p>";
			}
			$campanhas->free_result();
			return true;
		}
		
		$campanhas->free_result();
		
		if($existe == 0) {
			
			$this->AdicionarMensagemDeErro('Campanha inexistente.');
			return false;
		}
		
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Problemas ao exibir os dados desta
				campanha. Tente novamente mais tarde');
			return false;
		}
		
		return false;
	}
	//--------------------------------------------------------------------------
	public function ExibirListaDeVacinasDaCampanha($id)
	{
		$vacinas = $this->conexao->prepare('SELECT nome FROM vacina,
			vacinadacampanha WHERE vacina.id = vacinadacampanha.Vacina_id AND
			vacinadacampanha.Campanha_id = ? AND vacina.ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$vacinas->bind_param('i', $id);
		$vacinas->bind_result($nome);
		$vacinas->execute();
		$vacinas->store_result();
		$existe = $vacinas->num_rows;

		if($existe > 0) {

			echo '<fieldset><legend>Vacinas desta campanha</legend>';

			while ( $vacinas->fetch() ) {

				echo "<li>$nome</li>";
			}

			echo '</fieldset>';
			$vacinas->free_result();
			
			return true;
		}
		
		$vacinas->free_result();
		
		if($existe == 0) {
			
			echo '<p>Não há vacinas nesta campanha</p>';
			
			return true;
		}
		
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Problemas ao exibir vacinas desta
				campanha. Tente novamente mais tarde.');
			
			return false;
		}
		
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe uma lista com as diversas campanhas existentes.
	 *
	 */
	public function ExibirListaDeCampanhas()
	{
		$campanhas = $this->conexao->query('SELECT id, nome,
			datainicio, datafinal
			FROM campanha WHERE ativo ORDER BY datainicio')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$arr = array();

		$crip = new Criptografia();
		
		while ( $linha = $campanhas->fetch_assoc() ) {
							$data = new Data();
				
			$linha['datainicio'] = $data->InverterData($linha['datainicio']);
			$linha['datafinal'] = $data->InverterData($linha['datafinal']);
			
			$end = $crip->Cifrar("pagina=Adm/editarCampanha&id={$linha['id']}");
			
			$editar  = "<a href='?$end'><img src='{$this->arquivoGerarIcone}?imagem=editar'
						alt='Vacina não pode ser excluída. Existem indivíduos vacinados' border='0' /></a>";
			
			$end = $crip->Cifrar("pagina=Adm/excluirCampanha&id={$linha['id']}");
			
			$excluir = "<a href='?$end'><img src='{$this->arquivoGerarIcone}?imagem=excluir'
						alt='Vacina não pode ser excluída. Existem indivíduos vacinados' border='0' /></a>";
			
			if ($this->VerificarDosesAplicadasPorCampanha($linha['id']))
			$excluir ="<img src='{$this->arquivoGerarIcone}?imagem=excluir_desab'
							alt='Vacina não pode ser excluída. Existem indivíduos vacinados' border='0' />";
					
			$end = $crip->Cifrar("pagina=Adm/visualizarCampanha&id={$linha['id']}");		
			
			$vizualizar  = "<a href='?$end'><img src='{$this->arquivoGerarIcone}?imagem=listar'
						alt='Vacina não pode ser excluída. Existem indivíduos vacinados' border='0' /></a>";
			
			
			if( !Sessao::Permissao('CAMPANHAS_EDITAR') )
				$editar = false;
				
			if( !Sessao::Permissao('CAMPANHAS_EXCLUIR') )
				$excluir = false;
			
			if( !Sessao::Permissao('CAMPANHAS_LISTAR') )
				$vizualizar = false;
				
			
			
			$arr[] = array ('Nome da Campanha' => $linha['nome'], 
							'Data Inicial' => $linha['datainicio'], 
							'Data Final' => $linha['datafinal'],
							'Ações' => "{$editar}{$excluir}{$vizualizar}" );
		}

		$campanhas->free_result();

		if(count($arr)) {

			echo '<h3><center>Campanhas</center></h3>';
		
			/*$editarCampanha = false;
			$excluirCampanha = false;
			$vizualizarCampanha = false;
			
			if( Sessao::Permissao('CAMPANHAS_EDITAR') )
				$editarCampanha = 'pagina=Adm/editarCampanha';
				
			if( Sessao::Permissao('CAMPANHAS_EXCLUIR') )
				$excluirCampanha = 'pagina=Adm/excluirCampanha';
			
			if( Sessao::Permissao('CAMPANHAS_LISTAR') )
				$vizualizarCampanha = 'pagina=Adm/visualizarCampanha';
				
			if ($this->VerificarDosesAplicadasPorCampanha($campanha_id)) {
			
			$excluirCampanha ="<img src='{$this->arquivoGerarIcone}?imagem=excluir_desab'
							alt='Vacina não pode ser excluída. Existem usuários vacinados' border='0' />";	
			
						
				}	
				
			*/
			//Html::CriarTabelaDeArray($arr, $editarCampanha, $excluirCampanha, $vizualizarCampanha );
			Html::CriarTabelaDeArray($arr);
		
			return true;
			
		} else {
		
			echo '<h3>Nenhuma campanha cadastrada.</h3>';
			return false;
		
		}
	}
	//--------------------------------------------------------------------------
	public function VerificarDosesAplicadasPorCampanha($campanha_id)
	{
		
		$sql = 'SELECT COUNT(id) FROM `usuariovacinadocampanha` WHERE Campanha_id = ?';
		$stmt = $this->conexao->prepare($sql);
		$stmt->bind_param('i', $campanha_id);
		$stmt->bind_result($qtd);
		
		$qtd = 0;
		
		$stmt->execute();
		$stmt->fetch();
		$stmt->free_result();
		
		return $qtd; 
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Exibe uma lista em forma de tabela HTML com as vacinas da campanha X. Se
	 * a vacina faz parte do array, então a mesma apresenta-se como marcada.
	 *
	 * @param array $arr Array com as vacinas
	 * @param int|bool $campanha_id Identificador da campanha
	 */
	public function ExibirTabelaDeVacinas(array $arr, $campanha_id = false,
		$arquivo_origem)
	{

	  $selecVacina = $this->conexao->prepare('SELECT id, nome,  Grupo_id, pertence FROM `vacina`
	  	WHERE ativo ORDER BY grupo_id DESC, nome') or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
	  
      $selecVacina->bind_result($vacinaId, $nome, $grupo, $pertence);
      $selecVacina->execute();

      echo '<table width="600" border="1px" bordercolor="#CCCCCC" rules="cols"
      	cellpadding="0" cellspacing="0">';

	  echo '<th bgcolor="#E0E0E0">Vacina</th>';
	  echo '<th bgcolor="#E0E0E0" colspan="3">Características</th>';

	  $cont = 0;
	  $nomeDoGrupo = false;
	  
      while ($selecVacina->fetch()) {
      	//===========

        $vacina = new Vacina;
        $vacina->UsarBaseDeDados();

        if($pertence) continue;

      	if($nomeDoGrupo != $grupo){
				//if($nomeDoGrupo == true) echo "</blockquote>";
				//echo "<b>$grupo</b><blockquote style='border:none; background:#fff;'>";
				echo "<tr><td style='padding-top:10px; padding-bottom:10px; padding-left:20px;' align='left'><b>$grupo</b></td><td></td><td></td></tr>";
		}
			$nomeDoGrupo = $grupo;
			
      	//===========

      	$cont++;
		if($cont%2 != 0)	$corcelulas = "bgcolor='#ffffff'";
		else		 		$corcelulas = "bgcolor='#F0F0F0'";

      	if($campanha_id) {
			$qtdCaracteristicas =
				$this->ContarNumeroDeCaracteristicas($campanha_id, $vacinaId);
      	}
      	else {
      		$qtdCaracteristicas = 0;
      	}

		$check = array();
		$cor = '#CCC';
		$linkListar = "<label><img style='vertical-align:middle' alt='listar (0)'
      							src='{$this->arquivoGerarIcone}?imagem=listar_desab' border='0' /> (0)</label>";

		$linkAdicionar = "<label><img style='vertical-align:middle' alt='listar (0)'
      							src='{$this->arquivoGerarIcone}?imagem=adicionar_desab' border='0' /></label>";

		$crip = new Criptografia();

		if( in_array($vacinaId, $arr) ) {

			$querystring_listar =
			$crip->Cifrar("pagina={$arquivo_origem}_listarCaracteristicaDaVacina&vacinaid=$vacinaId&campanhaid=$campanha_id");

			$querystring_inserir =
			$crip->Cifrar("pagina={$arquivo_origem}_inserirCaracteristicaDaVacina&vacinaid=$vacinaId&campanhaid=$campanha_id");

			$check = 'checked="true" disabled="true"';
			$cor = '#000';
			$linkListar = "<a href='javascript:
				AbrirJanela(\"" . $this->url . "/Uf/Pop?$querystring_listar\",
      							250, 250)'><img style='vertical-align:middle'
      							src='{$this->arquivoGerarIcone}?imagem=listar' border='0'
      							alt='listar ($qtdCaracteristicas)' />
      							($qtdCaracteristicas)</a>";

			$linkAdicionar = "<a href='javascript:
				AbrirJanela(\"" . $this->url . "/Uf/Pop?$querystring_inserir\",
      							250, 250)'><img
      							src='{$this->arquivoGerarIcone}?imagem=adicionar' border='0'
      							alt='adicionar' />
      							</a>";
		}

	      	echo "<tr $corcelulas><td align='left'><label><input name='vacinas[]' type='checkbox'
	      		value='$vacinaId' $check  style='margin-left:50px; '/>$nome</label></td>";

      	//----------------------------------------------------------------------
      	//$end = $crip->Cifrar("id=$vacinaId");

      	$querystring_exibir_detalhe_vacina =
      	$crip->Cifrar("pagina=detalhesVacina&id=$vacinaId");

      	
      	//----------------------------------------------------------------------

      	echo "<td width='65px' align='center' style='color:$cor'>$linkListar</td>
      			<td width='30px' align='center' style='color:$cor'>$linkAdicionar</td>";

	      	echo "<td width='30px'  align='center'>
      		<a href='javascript:AbrirJanela(\""
      			. $this->url . "/Uf/Pop?$querystring_exibir_detalhe_vacina\",
      						200, 200, 700, 460)'><img
      							src='{$this->arquivoGerarIcone}?imagem=detalhes' border='0'
      							alt='detalhes' /></a></td></tr>";
      	//----------------------------------------------------------------------
      }
      $selecVacina->free_result();
   	  echo '</table>';
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe as vacinas da campanha X, para que se possa escolher quais editar
	 * ou excluir da campanha.
	 *
	 * @param int $id Identificador da campanha
	 */
	private function ExibirVacinasDaCampanha($id, $arquivo_origem)
	{
		$crip = new Criptografia();

		$selecVacinaDaCampanha = $this->conexao->prepare('SELECT
			vacinadacampanha.Vacina_id, vacina.nome FROM `vacinadacampanha`,
			`vacina` WHERE vacina.id = vacinadacampanha.Vacina_id
			AND Campanha_id = ? AND vacina.ativo ORDER BY vacina.nome')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selecVacinaDaCampanha->bind_param('i', $id);
	    $selecVacinaDaCampanha->bind_result($idVacina, $nomeVacina);
	    $selecVacinaDaCampanha->execute();

	    $selecVacinaDaCampanha->store_result();
	    $resultado = $selecVacinaDaCampanha->num_rows;

	    if(	$resultado > 0 ) {

			echo "<fieldset  style='width:700px;'>
				<legend>Vacinas selecionadas: $resultado </legend>";

	      	echo '<table width="645" border="0px" cellpadding="0" cellspacing="0">';
			$cont = 0;
	      	$cor  = '';

	      	echo '<th>Vacina</th>';
	      	echo '<th colspan="4">Características</th>';
	      	
			while ( $selecVacinaDaCampanha->fetch() ){

				if($cont%2 == 0 ) $cor = 'bgcolor="#EFEFEF"';
				else              $cor = 'bgcolor="#FFFFFF"';

				echo '<tr ' . $cor . '>';

				echo '<td align="left"><input name="vacinas[]" type="checkbox" value="'
					  .$idVacina.'" checked="checked" disabled="true"/>'
					  .$nomeVacina.'</td>';
				//----------------------------------------------------
				// Maykon 12:32 2008/11/17

				if($id) {
					$qtdCaracteristicas =
						$this->ContarNumeroDeCaracteristicas($id, $idVacina);
		      	}
		      	else {
		      		$qtdCaracteristicas = 0;
		      	}

		      	$querystring_listar =
		      		$crip->Cifrar("pagina={$arquivo_origem}_listarCaracteristicaDaVacina&vacinaid=$idVacina&campanhaid=$id");

		      	$querystring_inserir =
		      		$crip->Cifrar("pagina={$arquivo_origem}_inserirCaracteristicaDaVacina&vacinaid=$idVacina&campanhaid=$id");

				$linkListar = "<a href='javascript:
				AbrirJanela(\"Pop?$querystring_listar\",
      							250, 250)'>
			      			<img src='{$this->arquivoGerarIcone}?imagem=listar' border='0'
			      			style='vertical-align:middle'
			      			alt='Listar($qtdCaracteristicas)'> ($qtdCaracteristicas)</a>";

				$linkAdicionar = "<a href='javascript:
				AbrirJanela(\"Pop?$querystring_inserir\",
      							250, 250)'>
      							<img src='{$this->arquivoGerarIcone}?imagem=adicionar' border='0'
			      				alt='Adicionar'> </a>";

				echo '<td width="65px" align="center">'.$linkListar.'</td>';
				echo '<td width="30px" align="center">'.$linkAdicionar.'</td>';

				//----------------------------------------------------
				//echo '<td align="center">Listar (0)</td>';
				//echo '<td align="center">Adicionar</td>';

				$querystring_exibir_caracteristica_vacina =
				      	$crip->Cifrar("pagina=detalhesVacina&id=$idVacina");

				echo "<td width='30px'><center>
					  <a href='javascript:AbrirJanela(\"Pop?$querystring_exibir_caracteristica_vacina\",
					  200, 200, 700, 460)'><img src='{$this->arquivoGerarIcone}?imagem=detalhes'
					  alt='detalhes' border='0'/>
						</a></center></td>";

				$sql = "(
                            SELECT id
                            FROM `usuariovacinado`
                            WHERE Campanha_id = $id
                                AND Vacina_id = $idVacina
                        )
                        UNION
                        (
                            SELECT id
                            FROM `usuariovacinadocampanha`
                            WHERE Campanha_id = $id
                                AND Vacina_id = $idVacina
                        )";
							
				
				$naoPodeExcluir = $this->conexao->query($sql) 
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
				
				if($naoPodeExcluir->num_rows > 0) {
					echo "<td width='30px'><center>
						  <a href='javascript:' title='Vacina não pode ser excluída. Existem indivíduos vacinados'>
							<img src='{$this->arquivoGerarIcone}?imagem=excluir_desab'
							alt='Vacina não pode ser excluída. Existem indivíduos vacinados' border='0' />
						  </a></center></td>";
	
					echo '</tr>';
					
				}
				else {
					$excluir = $crip->Cifrar("pagina=Adm/editarCampanha&id=$id&excluir=$idVacina");
	
					echo "<td width='30px'><center>
						  <a href='?$excluir'>
							<img src='{$this->arquivoGerarIcone}?imagem=excluir' alt='excluir' border='0' />
						  </a></center></td>";
	
					echo '</tr>';
				}
				$cont++;
			}

			$selecVacinaDaCampanha->free_result();

			echo '</table>';

			echo '</fieldset><br />';
			
	   	}
	   	
	   	$selecVacinaDaCampanha->free_result();
	   	
	   	if($resultado == 0) {
	   		
	   		echo '<p>Não há vacinas nesta campanha.</p>';
	   		
	   	}
	   	
	   	if( $resultado < 0 ) {
	   		
	   		$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar exibir
	   			vacinas desta campanha.');
	   		
	   		return false;
	   	}

		$querystring_editar_vacinas_campanha =
				      	$crip->Cifrar("pagina=editarVacinasDaCampanha&id=$id&arquivo_origem=editarVacinasDaCampanha");

	 	echo "<a href='javascript:AbrirJanela(\"Pop?$querystring_editar_vacinas_campanha\")'>
	  		Adicionar Vacina</a>";
	  		
	  	return true;
	  	
	}
	//--------------------------------------------------------------------------

	// EXIBIR FORMULÁRIO ///////////////////////////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Exibe o formulário que é a interface para o usuário editar a campanha
	 *
	 * @param int $id Identificação da campanha a ser exibida
	 */
	public function ExibirFormularioEditarCampanha($id)
	{
		$this->BuscarCampanhaParaEdicao($id);

		$dataInvertidaInicio = $this->InverterData($this->_dataInicio);
		$dataInvertidaFinal  = $this->InverterData($this->_dataFim);

			$crip = new Criptografia;
			$pagina = $crip->Decifrar($_SERVER['QUERY_STRING']);
			list($enderecoSemExcluir) = explode('&excluir', $pagina);
			$enderecoSemExcluir = $crip->Cifrar($enderecoSemExcluir);
		
		?>
		<form name="form1" method="post" action="?<?php echo $enderecoSemExcluir
			// Foi necessario remover o excluir da querystring, pois ao alterar
			// a campanha q antes excluia uma vacina dava problema por causa do
			// excluir na querystring que fazia a vacina ser excluida duas vezes
			?>"
			onsubmit="return (ValidarFaixaDeDatas(this.dataInicio,this.dataFim) &&
				LimparString(this.nome) && ValidarNomeCampanha(this.nome) &&
				FormatarNome(this.nome, event) && ValidarData(dataInicio) && ValidarData(dataFim))"
			>

		<h3 align="center">Alterar dados da campanha</h3>

		<div style="padding-left:50px">
		
			<p>
				<div class='CadastroEsq'>Nome:</div>
				
			  	<div class='CadastroDir'>
				<input type="text" name="nome" value="<?php
				if( isset($_POST['nome']) ) echo Html::FormatarMaiusculasMinusculas($_POST['nome']);
				else echo Html::FormatarMaiusculasMinusculas($this->nome);
				?>" style="width:200px;" maxlength="70" 
				onkeypress="FormatarNome(this, event)"
				onkeyup="FormatarNome(this, event)"
				onkeydown="Mascara('NOME', this, event)"
				onblur="LimparString(this); ValidarNomeCampanha(this);
				FormatarNome(this, event)"/>
				</div>
			</p>
	
			<p>
			<div class='CadastroEsq'>
		  		Data de inicio da campanha:</div>
		  		
			  	<div class='CadastroDir'>
		  		<input type="text" name="dataInicio"
		 		value="<?php
		 		if( isset($_POST['dataInicio']) ) echo $_POST['dataInicio'];
				else echo $dataInvertidaInicio;?>"
		 		style="width:200px;" maxlength="10" 
		 		onkeypress="return Digitos(event, this);"
		        onkeydown="return Mascara('DATA', this, event);"
		        onkeyup="return Mascara('DATA', this, event);"
				onblur="ValidarData(this)"
				id="dataInicio" />
		  		</div>
		 	</p>
	
		 	<p>
			<div class='CadastroEsq'>
				Data de finalização da campanha:</div>
				
			  	<div class='CadastroDir'>
			  	<input type="text" name="dataFim"
				value="<?php
				if( isset($_POST['dataFim']) ) echo $_POST['dataFim'];
				else echo $dataInvertidaFinal;?>"
				style="width:200px;" maxlength="10"
				onkeypress="return Digitos(event, this);"
		        onkeydown="return Mascara('DATA', this, event);"
		        onkeyup="return Mascara('DATA', this, event);"
				onblur="ValidarData(this)" 
				id="dataFim" />
				</div>
			</p>
		
		</div>
		<p>
		<div align="center" style="clear:both">
		<?php $this->ExibirVacinasDaCampanha($id, 'editarCampanha'); ?>
		</div>
		</p>
		
	  <p><div align="center" style="clear:both">Texto complementar:</div></p>
	  <p>
	    <div align="center">
	    	<textarea name="obs" cols="50" rows="5" style="width:450px;"><?php
				if( isset($_POST['obs']) ) echo $_POST['obs'];
				else {
						$obsCampanha = str_replace(array('\\r\\n','\\r','\\n'),PHP_EOL, $this->_obs);
						echo stripslashes($obsCampanha);
				}		
				?></textarea>
				
	    </div>
	  </p>

	  <p><center>
	  	<?php $this->ExibirBotoesDoFormulario('Confirmar', 'Desfazer')?>
	    </center>
	  </p>
	</form> <?php
	}

	//--------------------------------------------------------------------------

	public function ExibirFormularioEditarVacinaDaCampanha($vacinas, $idCampanha,
		$arquivo_origem)
	{

	echo '<center><form id="form1" name="form1" method="post"
											 action="'.$_SERVER['REQUEST_URI'].'">';

		echo '<h3 align="center">Adicionar vacina à campanha</h3>';

		$arr = $this->SelecionarIdsDasVacinasDaCampanha($idCampanha);
		$this->ExibirTabelaDeVacinas($arr, $idCampanha, $arquivo_origem);

		if($this->VerificarSeEmitiuFormulario()) {

			if(isset($vacinas)) {

				$this->InserirVacinasNaCampanha($vacinas, $idCampanha);
			}
		}

		$this->ExibirBotoesDoFormulario('Adicionar', 'Desmarcar');

	echo '</form></center>';

	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe o formulário para a confirmação de exclusão da campanha.
	 *
	 */
	public function ExibirFormularioExcluirCampanha()
	{
		echo "<form method='POST' action='{$_SERVER['REQUEST_URI']}'>";

			// O segundo parâmetro é enviado false para nao exibir o botão reset
			$this->ExibirBotoesDoFormulario('Excluir', false, 'excluir');
		echo '</form>';
		echo '<hr />';
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe o formulário para que o usuário digite os dados para inserir uma
	 * campanha.
	 *
	 */
	public function ExibirFormularioInserirCampanha()
	{
		?>
		<form id="form1" name="form1" method="post"
			action="<?php echo $_SERVER['REQUEST_URI']?>"
			onsubmit="return (ValidarFaixaDeDatas(this.dataInicio,this.dataFim) &&
		LimparString(this.nome) && ValidarNomeCampanha(this.nome) &&
		FormatarNome(this.nome, event) && ValidarData(dataInicio) && ValidarData(dataFim))">
		<h3 align="center">Adicionar campanha</h3>

		<div style="padding-left:50px;">
		  <p>
			  <div class='CadastroEsq'>Nome:</div>
		  		<div class='CadastroDir'>
			  	<input type="text" name="nome" value="<?php
			  		if( isset($_POST['nome']) ) echo $_POST['nome']?>"
			  		style="width:300px;" maxlength="70"
			  		onkeypress="FormatarNome(this, event)"
					onkeyup="FormatarNome(this, event)"
					onkeydown="Mascara('NOME', this, event)"
					onblur="LimparString(this); ValidarNomeCampanha(this);
					FormatarNome(this, event)" />
			  </div>
		  </p>
		  <p>
		  	<div class='CadastroEsq'>Data de inicio da campanha:</div>
			  	<div class='CadastroDir'>
			  	<input type="text" name="dataInicio"
			  		value="<?php if( isset($_POST['dataInicio']) )
			  		echo $_POST['dataInicio']?>" style="width:200px;"
			  		maxlength="10" id="dataInicio"
			  		onkeypress="return Digitos(event, this);"
			        onkeydown="return Mascara('DATA', this, event);"
			        onkeyup="return Mascara('DATA', this, event);"
					onblur="ValidarData(this);ValidarFaixaDeDatas(this, document.form1.dataFim, true);" />
		  	</div>
		  </p>
		  <p>
			  <div class='CadastroEsq'>Data de finalização da campanha:</div>
		  		<div class='CadastroDir'>
			  	<input type="text"
			  		name="dataFim" value="<?php if( isset($_POST['dataFim']) )
			  		echo $_POST['dataFim']?>" style="width:200px;"
			  		maxlength="10" id="dataFim"
			  		onkeypress="return Digitos(event, this);"
			        onkeydown="return Mascara('DATA', this, event);"
			        onkeyup="return Mascara('DATA', this, event);"
					onblur="ValidarData(this);ValidarFaixaDeDatas(document.form1.dataInicio, this, true);" />
			  </div>
		  </p>
		  
		  </div>
		  <p><div align="center" style="clear:both">Texto complementar:</div></p>
		  <p>
		    <div align="center">
		    	<textarea name="obs" cols="50" rows="5"
			  		style="width:450px;"><?php if(isset($_POST['obs']))
			  		echo $_POST['obs']?></textarea>
		    </div>
		  </p>
		  <p><center>
		  	<?php $this->ExibirBotoesDoFormulario('Avançar', 'Apagar')?>
		    </center>
		  </p>
		</form>
		<?php
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe o formulário para que o usuário possa inserir vacinas na campanha X
	 *
	 * @param int $idCampanha Identificação da campanha
	 */
	public function ExibirFormularioInserirVacinasNaCampanha($idCampanha,
		$arquivo_origem)
	{
		?>
		<center>
		<form id="form1" name="form1" method="POST"
		action="<?php echo $_SERVER['REQUEST_URI']?>">

		<h3 align="center">Adicionar vacina à campanha</h3>

		<?php

		$arr = $this->SelecionarIdsDasVacinasDaCampanha($idCampanha);

		$this->ExibirTabelaDeVacinas($arr, $idCampanha, $arquivo_origem);

		echo '<br />';

		$this->ExibirBotoesDoFormulario('Adicionar', 'Desmarcar', 'adicionar');

		?>

		</form>
		</center>
		<?php
	}
	//--------------------------------------------------------------------------

	// VERIFICAR ///////////////////////////////////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Verifica se a campanha a ser inserida ou editada já não existe no banco
	 * de dados. Uma duplicidade de campanha é indicada pelo mesmo nome e mesma
	 * data inicial. O parâmetro $id so é usado quando a campanha é editada.
	 *
	 * @param int $id Parâmetro opcional - usado somente na edição
	 * @return boolean
	 */
	public function VerificarNaoDuplicidadeDeCampanha($id = false)
	{

	 	$dataInvertidaInicio =	$this->InverterData($this->_dataInicio);

		if($id) {
			$selecCampanha = $this->conexao->prepare('SELECT id FROM `campanha`
				WHERE nome = ? AND datainicio = ? AND id <> ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$selecCampanha->bind_param('ssi', $this->nome, $dataInvertidaInicio,
																		$id);
		}
		else {
			$selecCampanha = $this->conexao->prepare('SELECT id FROM `campanha`
				WHERE nome = ? AND datainicio = ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$selecCampanha->bind_param('ss', $this->nome, $dataInvertidaInicio);
		}

		$selecCampanha->execute();
		$selecCampanha->store_result();

		$registroJaExiste = $selecCampanha->num_rows;

		$selecCampanha->free_result();

		if($registroJaExiste > 0) {

			$this->AdicionarMensagemDeErro('Campanha já existe. Verifique os
				nomes e as datas.');
			return false;
		}
		
		if($registroJaExiste == 0) {
			
			return true; // Campanha ainda não existe. Não é duplicada.
		}
			
			
		if($registroJaExiste < 0) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao verificar a
				não duplicidade da campanha');
		}
		
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica se a $id passada por parâmetro existe na tabela "campanha"
	 *
	 * @param int $id Identificador da campanha
	 * @return boolean
	 */
	public function VerificarSeIdDaCampanhaExiste($id)
	{
		$selecCampanha = $this->conexao->prepare('SELECT id FROM `campanha`
			WHERE id = ? AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selecCampanha->bind_param('i', $id);
		$selecCampanha->execute();
		$selecCampanha->store_result();

		$idExiste = $selecCampanha->num_rows;

		$selecCampanha->free_result();

		if($idExiste > 0) return true; // Existe
		
		if($idExiste == 0) return false; // Não existe
		
		if($idExiste < 0) { // Erro!
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao verificar se a
				identificação desta campanha existe');
			return false;
		}

		return false;
	}
	//--------------------------------------------------------------------------

	///////////////////////////////// SELECIONAR ////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Seleciona a campanha para que os dados sejam atribuídos à instância da
	 * campanha usada.
	 *
	 * @param int $id Identificação da campanha
	 */
	private function BuscarCampanhaParaEdicao($id)
	{

		$selecCampanha = $this->conexao->prepare('SELECT nome, datainicio,
			datafinal, obs FROM `campanha` WHERE id = ? AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selecCampanha->bind_param('i', $id);

		$selecCampanha->bind_result($nome, $dataInicio, $dataFinal, $obs);

		$selecCampanha->execute();

		$selecCampanha->fetch();

		$this->nome = $nome;
		$this->_dataInicio = $dataInicio;
		$this->_dataFim = $dataFinal;
		$this->_obs = $obs;

		$selecCampanha->free_result();
	}

	//-------------------------------------------------------------------------
	/**
	 * Seleciona as Ids das vacinas para serem usadas na exibição das vacinas
	 * que foram inseridas. Retorna um array com as ids das vacinas.
	 *
	 * @param int $idCampanha Identificação da campanha
	 * @return Array
	 */
	public function SelecionarIdsDasVacinasDaCampanha($idCampanha)
	{
		$idVacinasDaCampanha = array();
		
		$selecVacinaDaCampanha = $this->conexao->prepare('SELECT Vacina_id FROM
		  	`vacinadacampanha` WHERE Campanha_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$selecVacinaDaCampanha->bind_param('i', $idCampanha);
		$selecVacinaDaCampanha->bind_result($idVacCamp);
		$selecVacinaDaCampanha->execute();
		
		$selecVacinaDaCampanha->store_result();
		
		$existe = $selecVacinaDaCampanha->num_rows;
		
		if($existe > 0) {
			
			$idVacinasDaCampanha = array();
		
			while ($selecVacinaDaCampanha->fetch()){
		
				$idVacinasDaCampanha[] = $idVacCamp;
			}
		     	
			$selecVacinaDaCampanha->free_result();
			
			return $idVacinasDaCampanha;
		     	
		}
		
		$selecVacinaDaCampanha->free_result();
		
		if($existe == 0) {
			
			// Não há vacinas para essa campanha: retorna array vazio;
			return array();
		}
			
		if($existe < 0) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar
				recuperar as identificações das vacinas desta campanha.');
			
			return false;
		}
		
		return false;
	}
	//--------------------------------------------------------------------------

	// INSERIR /////////////////////////////////////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Insere uma campanha no banco de dados
	 *
	 */
	public function InserirCampanha()
	{
		$dataInvertidaInicio = $this->InverterData($this->_dataInicio);
		$dataInvertidaFim	 = $this->InverterData($this->_dataFim);

		$inserirCampanha = $this->conexao->prepare('INSERT INTO `campanha` VALUES
			(NULL, ?, ?, ?, ?, 1)')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$inserirCampanha->bind_param('ssss', $this->nome, $dataInvertidaInicio,
		 											$dataInvertidaFim, $this->_obs);
		$inserirCampanha->execute();

		$inseridoComSucesso = $inserirCampanha->affected_rows;

		$inserirCampanha->close();

		if( $inseridoComSucesso ) {

			$selecCampanha = $this->conexao->prepare('SELECT id FROM `campanha`
				WHERE nome = ? AND datainicio = ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$selecCampanha->bind_param('ss', $this->nome, $dataInvertidaInicio);
			$selecCampanha->bind_result($id);
			$selecCampanha->execute();
			$selecCampanha->fetch();

			$selecCampanha->free_result();

			$crip = new Criptografia();

			$querystring = $crip->Cifrar("pagina=Adm/inserirVacinasNaCampanha&campanha=$id");

			// Sempre logo antes de um header/Location, fechar a conexão:
			$this->conexao->close();

			echo "<script> window.location = '?$querystring'</script>";
		}
		else {
			$this->AdicionarMensagemDeErro('Campanha não inserida. Um erro
												desconhecido ocorreu.');
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Insere automaticamente a característica inicial da vacina. Esta é uma
	 * característica padrão, na qual a faixa etária vai da mínima (definida
	 * em AgenteImunizador::IDADE_MINIMA) até a faixa máxima (também definida em
	 * AgenteImunizador::IDADE_MAXIMA). Retorna true se a característica foi
	 * inserida com sucesso.
	 *
	 * @param int $vacinaDaCampanhaId
	 * @return boolean
	 */
	private function InserirCaracteristicaInicialDaVacina($vacinaDaCampanhaId)
	{
 		$idadeInicial = self::IDADE_MINIMA;
 		$idadeFinal   = self::IDADE_MAXIMA * 365;	// 120 anos;
 		$sexo         = 'ambos';					// todos os sexos
 		$etnias       = 'todas';					// todas as etnias
 		$estados      = 'todos';					// todos os estados

		$inserirCaracInicial = $this->conexao->prepare('INSERT
			INTO `configuracaodavacina` (id, VacinaDaCampanha_id,
			idadeInicio, idadeFinal, sexo, etnias, estados)
			VALUES (NULL, ?, ?, ?, ?, ?, ?)')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$inserirCaracInicial->bind_param('iiisss',
			$vacinaDaCampanhaId, $idadeInicial, $idadeFinal,
			$sexo, $etnias, $estados);

		$inserirCaracInicial->execute();
		$inseriu = $inserirCaracInicial->affected_rows;
		$inserirCaracInicial->close();

		if($inseriu) return true;

		$this->AdicionarMensagemDeErro('Configuração não inserida');
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Insere uma ou mais vacinas na campanha X.
	 *
	 * @param array $vacinas Array contendo as ids das vacinas a serem inseridas
	 * @param int $idCampanha Identificação da campanha
	 */
	public function InserirVacinasNaCampanha(Array $vacinas, $idCampanha)
	{
		if($count = count($vacinas)) {

			$inserirVacinaDaCampanha = $this->conexao->prepare('INSERT INTO
		  	`vacinadacampanha` (id, Vacina_id, Campanha_id) VALUES (NULL, ?, ?)')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			for($i=0; $i < $count; $i++) {

				if($vacinas[$i]) {

					$inserirVacinaDaCampanha->bind_param('ii', $vacinas[$i],
															$idCampanha);

					$inserirVacinaDaCampanha->execute();
					$inseriu = $inserirVacinaDaCampanha->affected_rows;

					if($inseriu > 0) {

						$vacinaDaCampanha = $this->conexao->prepare('SELECT id
							FROM `vacinadacampanha`
							WHERE Campanha_id = ? AND Vacina_id = ?')
							or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

					 	$vacinaDaCampanha->bind_param('ii', $idCampanha, $vacinas[$i]);
					 	$vacinaDaCampanha->bind_result($vacinaDaCampanhaId);
					 	$vacinaDaCampanha->execute();
					 	$vacinaDaCampanha->store_result();
					 	$vacinaDaCampanha->fetch();
					 	$idVacinaDaCampanhaExiste = $vacinaDaCampanha->num_rows;
					 	$vacinaDaCampanha->free_result();

					 	if($idVacinaDaCampanhaExiste > 0) {

					 		$this->InserirCaracteristicaInicialDaVacina($vacinaDaCampanhaId);
					 		//return true;
					 	}
					 	
					 	
					 	if($idVacinaDaCampanhaExiste == 0) {
					 		
					 		$this->AdicionarMensagemDeErro('Não existe campanha
					 			para adicionar vacinas.');
					 		
					 		
					 		return false;
					 	}
					 		
					 	if($idVacinaDaCampanhaExiste < 0) {
					 		
					 		$this->AdicionarMensagemDeErro('Algum erro ocorreu
					 			ao adicionar vacinas à campanha.');
					 		
					 		
					 		return false;
					 	}
					}
					if($inseriu == 0) {
						
						$this->AdicionarMensagemDeErro('Não foram inseridas
							vacinas nesta campanha.');
						
						
						return false;
					}
					
					if($inseriu < 0) {
						
						$this->AdicionarMensagemDeErro('Algum erro ocorreu ao
							inserir vacinas nesta campanha.');
						
						
						$inserirVacinaDaCampanha->close();
						return false;	
					}
				}
			}
			$inserirVacinaDaCampanha->close();
		}
		else {
			$this->AdicionarMensagemDeErro('Marque alguma vacina antes de confirmar!');
			return false;
		}
	}
	/*
	//--------------------------------------------------------------------------
	public function InserirVacinaDaCampanha($vacinas, $idCampanha)
	{
		//die('Aaaahh 1!');

		  $count = count($vacinas);

		  $inserirVacinaDaCampanha = $this->conexao->prepare('INSERT INTO
		  		`vacinadacampanha` (id, Vacina_id, Campanha_id) VALUES (NULL, ?, ?)')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		  for($i=0; $i < $count; $i++) {

			 if($vacinas[$i]) {

			 	//die((string)$vacinas[$i]);

				 $inserirVacinaDaCampanha->bind_param('ii', $vacinas[$i], $idCampanha);
				 $inserirVacinaDaCampanha->execute();

			}
		}
	}
	*/

	//--------------------------------------------------------------------------

	// EDITAR  /////////////////////////////////////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Edita a campanha passada por parâmetro
	 *
	 * @param int $id Identificação da campanha
	 */
	public function EditarCampanha($id)
	{

		$AtualizarCampanha = $this->conexao->prepare('UPDATE `campanha` SET
			nome = ?, datainicio = ?, datafinal = ?, obs = ?
			WHERE id = ?') or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$dataInvertidaInicio = $this->InverterData($this->_dataInicio);
		$dataInvertidaFinal  = $this->InverterData($this->_dataFim);

		$AtualizarCampanha->bind_param('ssssi', $this->nome, $dataInvertidaInicio,
		 		$dataInvertidaFinal, $this->_obs, $id);

	    $AtualizarCampanha->execute();

	    $atualizado = $AtualizarCampanha->affected_rows;

	    $AtualizarCampanha->free_result();

	    if( $atualizado > 0) {
	    	
	    	$this->ExibirMensagem('Campanha atualizada com sucesso!');
	    	return true;
	    }
	    
	    if( $atualizado == 0) {
	    	
	    	$this->AdicionarMensagemDeErro('Nenhuma modificação efetuada!');
	    	return false;
	    }
	    
	    if( $atualizado < 0) {
	    	
	    	$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar
	    		atualizar a campanha.');

	    	return false;
	    }
	}

	//--------------------------------------------------------------------------

	// EXCLUIR  ////////////////////////////////////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Exclui a campanha (passada por parâmetro) do banco de dados
	 *
	 * @param int $id Identificação da campanha
	 */
	public function ExcluirCampanha($campanhaId)
	{
		
		$ocorreuErro = false;

		$registros = $this->conexao->query("SELECT Vacina_id FROM `vacinadacampanha`
				WHERE Campanha_id = $campanhaId")
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$existemVacinasDestaCampanha = $registros->num_rows;

		if( $existemVacinasDestaCampanha > 0 ) {

			while($linha = $registros->fetch_assoc()) {

				$vacinasId[] = $linha['Vacina_id'];

				if( $this->conexao->errno != 0 ) {
					$ocorreuErro = true;
					break;
				}
			}
			$registros->free_result();


			foreach($vacinasId as $vacinaId) {

				// Para excluir uma campanha, tem que antes excluir todas as
				// vacinas inseridas nesta campanha e também, antes, excluir
				// todas as características desta vacina, desta campanha.
				if ($this->ExcluirVacinaDaCampanha($campanhaId, $vacinaId) == false) {
					$ocorreuErro = true;
					break;
				}
			}
		}
		
		if( $existemVacinasDestaCampanha < 0 ) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao verificar se
				existem vacinas nesta campanha');
			
			return false;
		} 

		if( !$ocorreuErro ) {
			
			$iconeDaCampanha = $this->conexao->prepare("DELETE FROM `iconedacampanha` WHERE Campanha_id = $campanhaId")
                               or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $iconeDaCampanha->execute();
			if( $iconeDaCampanha->errno == 0 ) {
 
				// Fechou só pq vai redirecionar logo abaixo:
				$iconeDaCampanha->close(); 


                $this->conexao->query("DELETE FROM `campanha` WHERE id = $campanhaId")
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                if( $this->conexao->errno == 0 ) {

                    $this->conexao->close();

                    $crip = new Criptografia();
                    $querystring = $crip->Cifrar('pagina=Adm/listarCampanhas');
                    echo "<script>window.location = '?$querystring'</script>";
                }
			}
		}

		$this->AdicionarMensagemDeErro('Algum erro desconhecido ocorreu
										ao remover a campanha');
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Exclui a vacina passada por parâmetro da campanha específica, também
	 * passada por parâmetro
	 *
	 * @param int $campanhaId Identificação da campanha
	 * @param int $vacinaId Identificação da vacina
	 */
	public function ExcluirVacinaDaCampanha($campanhaId, $vacinaId)
	{

		$stmt = $this->conexao->prepare('SELECT id FROM
			`usuariovacinadocampanha` WHERE Campanha_id = ? AND Vacina_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_param('ii', $campanhaId, $vacinaId);
		$stmt->bind_result($vacinaDaCampanhaId);
		$stmt->execute();
		$stmt->store_result();
		$alguemVacinado = $stmt->num_rows;
		$stmt->fetch();
		$stmt->free_result();

       
		$selecVacinaDaCampanha = $this->conexao->prepare('SELECT id FROM
			`vacinadacampanha` WHERE Campanha_id = ? AND Vacina_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selecVacinaDaCampanha->bind_param('ii', $campanhaId, $vacinaId);
		$selecVacinaDaCampanha->bind_result($vacinaDaCampanhaId);
		$selecVacinaDaCampanha->execute();
		$selecVacinaDaCampanha->store_result();
		$vacinaDaCampanhaExiste = $selecVacinaDaCampanha->num_rows;
		$selecVacinaDaCampanha->fetch();
		$selecVacinaDaCampanha->free_result();

        if($alguemVacinado == 0) {

            if($vacinaDaCampanhaExiste > 0) {

                $selecCaracDaVacinaDaCampanha = $this->conexao->prepare('SELECT id FROM
                    `configuracaodavacina` WHERE VacinaDaCampanha_id = ?')
                    or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                $selecCaracDaVacinaDaCampanha->bind_param('i', $vacinaDaCampanhaId);
                $selecCaracDaVacinaDaCampanha->bind_result($caracDaVacinaDaCampanhaId);
                $selecCaracDaVacinaDaCampanha->execute();
                $selecCaracDaVacinaDaCampanha->store_result();
                $caracDaVacinaDaCampanhaExiste = $selecCaracDaVacinaDaCampanha->num_rows;
                $selecCaracDaVacinaDaCampanha->fetch();
                $selecCaracDaVacinaDaCampanha->free_result();

                if($caracDaVacinaDaCampanhaExiste > 0) {

                    $excluiCaracs = $this->conexao->prepare('DELETE FROM
                        `configuracaodavacina` WHERE VacinaDaCampanha_id = ?')
                        or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                    $excluiCaracs->bind_param('i', $vacinaDaCampanhaId);
                    $excluiCaracs->execute();
                    $caracFoiExcluida = $excluiCaracs->affected_rows;
                    $excluiCaracs->close();

                    if($caracFoiExcluida > 0) {

                        $excluiVacinaDaCampanha = $this->conexao->prepare('DELETE FROM
                            `vacinadacampanha` WHERE id = ?')
                            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                        $excluiVacinaDaCampanha->bind_param('i', $vacinaDaCampanhaId);

                        $excluiVacinaDaCampanha->execute();

                        $excluiVacinaDaCampanha->close();

                        return true;
                    }
                    else {
                        $this->AdicionarMensagemDeErro('As configurações para esta
                            vacina não puderam ser excluídas');
                    }
                }
                else {
                    $this->AdicionarMensagemDeErro('Não existem configurações para
                        esta vacina');
                }
            }

            else {

                $this->AdicionarMensagemDeErro('A vacina solicitada para exclusão
                    nao existe');
            }
        }
        else {
            $this->AdicionarMensagemDeErro('Essa Campanha não pode ser excluida, pois já existem indivíduos vacinados por ela!');
        }

		return false;
	}
	//--------------------------------------------------------------------------

	// MÉTODOS DIVERSOS ////////////////////////////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Metodo que serve unicamente para contar o número de características da
	 * vacina X. Ele usa uma conexão interna, pois uma conexão anterior já está
	 * sendo usada antes da chamada deste método.
	 *
	 * @param int $campanhaId Identificador da campanha
	 * @param int $vacinaId Identificador da vacina
	 * @return int Quantidade de características da vacina
	 */
	public function ContarNumeroDeCaracteristicas($campanhaId, $vacinaId)
	{

		$conexaoInterna = mysqli_connect($_SESSION['local'], $_SESSION['login'],
			$_SESSION['senha']);


		$conexaoInterna->select_db($_SESSION['banco']);


      	$selectVacina = $conexaoInterna->query("SELECT id FROM `vacinadacampanha`
      		WHERE Campanha_id = $campanhaId AND Vacina_id = $vacinaId")
      		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

      	$existem = $selectVacina->num_rows;
      	
      	if($existem > 0) {

			$resultado = $selectVacina->fetch_assoc();
			$vacinaDaCampanhaId = $resultado['id'];
			$selectVacina->free_result();

			$caracs = $conexaoInterna->query("SELECT id FROM `configuracaodavacina`
				WHERE VacinaDaCampanha_id = $vacinaDaCampanhaId")
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

	      	$qtdCaracteristicas = $caracs->num_rows;
	      	$caracs->free_result();
	      	$conexaoInterna->close();

	      	return $qtdCaracteristicas;
      	}
		     
      	$selectVacina->free_result();
		$conexaoInterna->close();
      	
		if($existem == 0) {

      		return 0;
      	}
      	if($existem < 0) {
      		
      		$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar contar
      			o número de características para esta vacina.');
      		
      		return false;
      	}
	}
	//--------------------------------------------------------------------------
	public function VisualizarCampanha($campanhaId)
	{
		$stmt = $this->conexao->prepare('SELECT nome, datainicio, datafinal, obs
			FROM `campanha` WHERE id = ? AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_param('i', $campanhaId);
		
		$stmt->bind_result($nomeCampanha, $datainicioCampanha,
			$datafinalCampanha, $obsCampanha);
			
		$stmt->execute();
		
		$stmt->store_result();
		
		$existe = $stmt->num_rows;
		
		
		if($existe > 0) {

			$stmt->fetch();
			$stmt->free_result();
			
			echo "<h3>$nomeCampanha</h3>";
			
			$data = new Data();
			
			$dataInicioCamp = $data->InverterData($datainicioCampanha);
			$dataFinalCamp = $data->InverterData($datafinalCampanha);
			
			echo "Início da campanha: $dataInicioCamp -
					Final da campanha: $dataFinalCamp";
			
			$obsCampanha = str_replace(array('\\\r\\\n','\\\r','\\\n'),'<br />', $obsCampanha);
					
			echo strlen($obsCampanha) ?
				"<p><fieldset><legend>Obs:</legend><blockquote>$obsCampanha</blockquote></fieldset></p>" :
				'';
			

			
			$stmt = $this->conexao->prepare('SELECT vacinadacampanha.id,
				vacina.nome, vacina.id FROM `vacina`, `vacinadacampanha` WHERE
				vacinadacampanha.Vacina_id = vacina.id
				AND vacinadacampanha.Campanha_id = ? AND vacina.ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
					
			$stmt->bind_param('i', $campanhaId);
			
			$stmt->bind_result($vacinaDaCampanhaId, $nomeVacina, $vacinaId);
			
			$stmt->execute();
		
			$stmt->store_result();
			
			$existe = $stmt->num_rows;
			
			if($existe > 0) {
				
				$crip = new Criptografia();
				
				while( $stmt->fetch() ) {
					
				$link = $crip->Cifrar("pagina=detalhesVacina&id=$vacinaId");
				
				
				echo "<a href='javascript:AbrirJanela(\"./Pop/?$link\",
					  200, 200, 700, 460)'><h3>$nomeVacina</p></h3></a>";
				
					
					$linhas = $this->conexao->query("SELECT idadeinicio,
						idadefinal, sexo, etnias, estados FROM
						`configuracaodavacina` WHERE
						VacinaDaCampanha_id = $vacinaDaCampanhaId")
						or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
						
					$existe = $linhas->num_rows;
					
					if($existe > 0) {

						echo "<h3>Características <small>($existe)</small></h3>";
						
						$i = 0;
						while( $linha = $linhas->fetch_object() ) {

							 $i++;
							
							echo "<p><fieldset>
							<legend><em>{$i}ª Característica</em></legend><ul>";
							
							$idadeInicio = $this->ConverterDiasParaUnidadeDeTempo($linha->idadeinicio);
							$idadeFinal = $this->ConverterDiasParaUnidadeDeTempo($linha->idadefinal);
							
							if($idadeFinal == '120 ano(s)') $idadeFinal = 'indeterminada'; 
							
							echo "<li>Idade início: {$idadeInicio}</li>";
							echo "<li>Idade final: {$idadeFinal}</li>";
							echo "<li>Sexo: {$linha->sexo}</li>";
							echo "<li>Etnias: {$linha->etnias}</li>";
							echo "<li>Estados: {$linha->estados}</li>";
							
							
							echo '</ul></fieldset></p>';
						}
					}
					
					
					if($existe <= 0) {
						
						$this->AdicionarMensagemDeErro("Não existem características
							para $vacinaNome");
							
						return false;
					}
				}
				$stmt->free_result();
			}
			
			// Sem vacina para a campanha:
			if($existe == 0) {

				$this->AdicionarMensagemDeErro("Não foram encontradas vacinas
					para $nomeCampanha. Contacte o administrador
					do Sivac para informá-lo sobre esta campanha.");
					
				return false;
			}
			
			// Visualizar vacinas deu erro:
			if($existe < 0) {

				$this->AdicionarMensagemDeErro("Ocorreu um erro ao tentar
					visualizar as vacinas para a $nomeCampanha. Recarregue
					a página e se o problema persistir,
					tente novamente mais tarde.");
					
				return false;
			}
			
			// Libera do select vacina
			$stmt->free_result();
		}
		
		// Não existe a campanha:
		if($existe == 0) {
			
			$this->AdicionarMensagemDeErro('Campanha inexistente');
			
			return false;
		}
		
		// Erro ao recuperar campanha:
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Ocorreu um erro ao tentar recuperar
				os dados da campanha. Recarregue a página ou tente novamente
				mais tarde.');
			
			return false;
		}
		
		// Libera do Select campanha
		if( isset($stmt) ) $stmt->free_result();
		
		return true;
		
	}
	//--------------------------------------------------------------------------

	public function SelecionarIconesUltimasCampanhas()
	{
		/*$sql = 'SELECT Campanha_id, icone, nome, datainicio, datafinal
				FROM iconedacampanha, campanha
					WHERE campanha.id = Campanha_id AND iconedacampanha.id > 3 -- tirar isso ==> iconedacampanha.id > 3
						ORDER BY iconedacampanha.id 
                LIMIT 4';*/
        
		$sql = 'SELECT Campanha_id, icone, nome, datainicio, datafinal '
		     . 'FROM iconedacampanha, campanha '
		     . 'WHERE campanha.id = Campanha_id '
             .     'AND campanha.datafinal >= CURRENT_DATE() '
             .     'AND campanha.ativo '
             .     'ORDER BY campanha.datainicio, campanha.id  '
             .     'LIMIT 10';
		
		$stmt = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_result($campanha_id, $icone, $nome, $datainicio, $datafinal);
		$stmt->execute();
		$stmt->store_result();
		$qtdIcones = $stmt->num_rows;
		
		if($qtdIcones < 0) {
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao recuperar
				os ícones de campanha.');
			return false;
		}
		
		if($qtdIcones == 0) {
			$stmt->free_result();
			return true; // Se não tem ícones, não faz nada.
		}
		
		if($qtdIcones > 0) {
			
			echo '<div id="icones">';
			
			$data = new Data();
			$crip = new Criptografia();
				   
			while ( $stmt->fetch() ) {

                // Se hoje está fora de dataInicio e dataFim da campanha, ddesabilita:
                if(!$this->VerificarPeriodoDaCampanha($campanha_id)){
                    list($iconeNome, $iconeExtencao) = explode('.', $icone);
                    $icone = $iconeNome.'Disable.'.$iconeExtencao;

                   $title = Html::FormatarMaiusculasMinusculas($nome)
				   . ' fora do período (de ' . $data->InverterData($datainicio)
				   . ' a ' . $data->InverterData($datafinal) . ')';

                    $querystring = $crip->Cifrar("pagina=Adm/listarPessoasVacinaveis"
                        . "&campanhaDoIcone=$campanha_id&campanhaDoIconeNome=$nome");

                echo "<img src='./Imagens/$icone'
                            border='0' width='50px'	height='50px' style='margin-left:10px'
                            title='$title' alt='$title'>";
                }
				else {
                   
                    $title = Html::FormatarMaiusculasMinusculas($nome)
                       . ' (de ' . $data->InverterData($datainicio)
                       . ' a ' . $data->InverterData($datafinal) . ')';

                    $querystring = $crip->Cifrar("pagina=Adm/listarPessoasVacinaveis"
                        . "&campanhaDoIcone=$campanha_id&campanhaDoIconeNome=$nome");

                    echo "<a href='?$querystring'><img src='./Imagens/$icone'
                            border='0' width='50px'	height='50px' style='margin-left:10px'
                            title='$title' alt='$title'></a>";
                }
				
			}
			
			echo '</div>';
		}
						
		$stmt->free_result();

	}
    //--------------------------------------------------------------------------
    public function VerificarPeriodoDaCampanha($campanha_id)
    {
        $sql = " SELECT COUNT(id) AS campanhas
                    FROM campanha
                        WHERE campanha.id = $campanha_id
                        AND CURRENT_DATE() BETWEEN datainicio AND datafinal
                        AND ativo";


        $rs = $this->conexao->query($sql)
        or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
        $linha = $rs->fetch_assoc();
        $rs->free_result();

        return $linha['campanhas'];

    }
	//--------------------------------------------------------------------------
}