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
/**

 *
 * @package Sivac/Class
 *
 * @author Maykon Monnerat (maykon_ttd@hotmail.com), v 1.0, 2008-08
 * 
 *
 * @copyright 2008 
 *
 */

class RelatorioCadernetaDeVacinacao extends Relatorio

{
    private $_dataHoraVacinacao;

//------------------------------------------------------------------------------
    /**
     * Atributos
     */
     protected static $titulo = 'Caderneta de vacinação';

     protected static $nota = 'Este relatório tem por finalidade exibir a caderneta
                        de vacinação do indivíduo, com as doses que já foram
                        registradas pelo sistema, informando também as doses
                        faltantes.';

    private static $temAlgumaVacinaComObs = false;

//------------------------------------------------------------------------------

    /** 
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        
        Depurador::Pre('Classe instanciada: ' . __CLASS__);
    }

//------------------------------------------------------------------------------

    /**
     * Retorna o título do relatório. Necessário pois na classe mãe (Relatorio)
     * é usado $this->Titulo() genericamente para retornar o título de cada
     * filha, o que não funcionaria se chamasse self::$titulo na classe mãe.
     *
     * @return String
     */
    protected function Titulo()
    {
        return self::$titulo;
    }

//------------------------------------------------------------------------------

    /**
     * Retorna a nota do relatório. Necessário pois na classe mãe (Relatorio)
     * é usado $this->Nota() genericamente para retornar o título de cada
     * filha, o que não funcionaria se chamasse self::$nota na classe mãe.
     *
     * @return String
     */
    protected function Nota()
    {
        return self::$nota;
    }

//------------------------------------------------------------------------------

    /**
     * Exibe o relatório desta classe.
     *
     * @param Boolean|int $pagina_atual (opcional) Página usada quando o
     *                    o  relatório está paginado
     * @return Boolean
     */
   public function ExibirRelatorio($pagina_atual = false)
   {
   }

//------------------------------------------------------------------------------

   /**
    * Exibe o formulario para os relatorios de rotina.
    *
    */
   public function ExibirFormulario()
   {
        $crip = new Criptografia();

        // Para criar a variável $tipo e $apagarDaQuery
        parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));

        $end = $crip->Cifrar("pagina=exibirRelatorioPop&tipo=$tipo");

        echo "<h3 align='center'>{$this->Titulo()}</h3>";

        $validarOnClick =	$this->MontarValidacaoDeSubmissaoDoFormulario();

       // return false para não submeter com ENTER:
       // (o formulário será usado com um evento ONCLICK em um botão)
       echo "<form name='relatorioCaderneta' id='relatorioCaderneta' onsubmit='return false'>";

        //==================================================================
        ?>
        <p>
        <div class='CadastroEsq'>Estado onde reside:</div>
        <div class='CadastroDir'><?php $this->SelectEstados(); ?>
        <!-- </select> -->
        </div>
		</p>

        <!-- ############################################################### -->

        <p>
            <div class="CadastroEsq">Cidade:</div>
			<div class='CadastroDir'><?php $this->SelectCidades('cidade'); ?></div>
		</p>
        <?php

        //==================================================================

        ?>
        <p>
            <div class="CadastroEsq">Nome:</div>
            <div class='CadastroDir'><input type="text" name="nome" id="nome" style="width:300px"
                 onkeypress="FormatarNome(this, event)"
                 onkeyup="FormatarNome(this, event)"
                 onkeydown="Mascara('NOME', this, event)"
                 onblur="LimparString(this); ValidarPesquisa(this, 3, true);
                        FormatarNome(this, event)" />
            </div>
        </p>

        <?php

        //==================================================================

        ?>
		<p>
            <div class="CadastroEsq">Mãe: </div>
			<div class='CadastroDir'><input type="text"  name="mae" id="mae" style="width:300px"
                onkeypress="FormatarNome(this, event)"
				onkeyup="FormatarNome(this, event)"
				onkeydown="Mascara('NOME', this, event)"
				onblur="LimparString(this); ValidarPesquisa(this, 3, true);
				FormatarNome(this, event)" />
			</div>
        </p>
        <?php

        //==================================================================

        ?>
        <p>
            <div class="CadastroEsq">CPF:</div>
            <div class="CadastroDir">
			<input type="text" name="cpf" id="cpf" maxlength="14"
                onkeypress="return Digitos(event, this);"
				onkeydown="Mascara('CPF',this,event);"
				onblur="ValidarCpf(this, true);"/>
				<span id="TextoExemplos"><?php
                    echo " Ex.: 474.876.345-07" ?></span>
			</div>
		</p>

        <?php

        //==================================================================
        $botao = new Vacina();
        
        $botao->ExibirBotoesNaoSubmit('buscar',
            "$validarOnClick ListarPessoa(\"listagem\",
                document.relatorioCaderneta.nome.value,
                document.relatorioCaderneta.mae.value,
                \"$tipo\", 0, 0,
                document.relatorioCaderneta.cidade.value, 0, 0,
                document.relatorioCaderneta.cpf.value)");

        echo "</form>";

        echo '<p><div id="listagem" style="clear: both; border: 1px solid white"></div></p>';

        echo "<p><strong>Nota:</strong><blockquote>";
        echo $this->Nota();
        echo "</blockquote></p>";
    }

//------------------------------------------------------------------------------

   /**
    * Monta a validação para o "if()" do clique do botão no formulário desta
    * classe.
    *
    * @return String
    */
    private function MontarValidacaoDeSubmissaoDoFormulario()
    {
        // Esta validação é para um botão "não submit", logo, não há return:
        $validacao = 'if( '
                   . 'ValidarCampoSelect(document.relatorioCaderneta.estado, "estado") '
                   . '&& ValidarCampoSelect(document.relatorioCaderneta.cidade, "cidade") '
                   . '&& ( '
                   .     '(ValidarPesquisa(document.relatorioCaderneta.mae, 3, true) '
                   .     '&& ValidarPesquisa(document.relatorioCaderneta.nome)) '
                   .     '|| (ValidarPesquisa(document.relatorioCaderneta.nome, 3, true) '
                   .     '&& ValidarPesquisa(document.relatorioCaderneta.mae))'
                   . ' ) '
                   . '&& ValidarCpf(document.relatorioCaderneta.cpf, true)'
                   . ' ) ';

        return $validacao;
	}

//------------------------------------------------------------------------------

    /**
     * Lista as vacinas para que o usuário escolha quais irão ser exibidas na
     * caderneta de vacinação
     */
	public function ListarVacinasParaCaderneta($listarVacinasMae     = false,
                                               $listarVacinasFilhas  = true)
	{
		$listaDeVacina = $usuario_id = $tipo = false;

		$crip = new Criptografia();

		parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));

        $arr = array(); // Criar um array antes!

		if($usuario_id){
			$usuario_nome = $this->RetornarCampoNome('usuario', $usuario_id);
			$arr = $this->ListarVacinasEspeciais($usuario_id);
		}

		//$end = $crip->Cifrar("pagina=listarVacinasParaCaderneta&tipo=$tipoRelatorio&usuario_id=$usuario_id");
		$end = $crip->Cifrar("pagina=exibirRelatorioPop&tipo=$tipo&usuario_id=$usuario_id");

		echo "<form name='vacinas' id='vacinas' method='post' action='./?$end'>";

		$stmt = $this->conexao->prepare('SELECT id, nome, grupo_id, pertence FROM vacina WHERE ativo ORDER BY grupo_id DESC, nome');

        $id = $nome = $grupo = 0;

		$stmt->bind_result($id, $nome, $grupo, $pertence);

		$stmt->execute();


		echo "<br /><br /><div style='margin: 40px'><h3 align='center'>Vacinas
				para a caderneta</h3><hr />";
		echo "<h4 align='center'>Confirme as vacinas para gerar a caderneta de
				$usuario_nome</h4></div>";

		echo '<div class="CadastroEsq"></div>';
		echo '<div class="CadastroDir">';

		echo '<p><label><input type="checkbox" name="obs" id="obs" /> Exibir observações na caderneta</label></p>';

		$nomeDoGrupo = false;

        $vacina = new Vacina;
        $vacina->UsarBaseDeDados();

		while($stmt->fetch()){

            if($listarVacinasMae    == false && $vacina->CarregarVacinasFilhas($id)) continue;
            if($listarVacinasFilhas == false && $pertence) continue;

			if($nomeDoGrupo != $grupo){
				if($nomeDoGrupo == true) echo "</blockquote>";
				echo "<b>$grupo</b><blockquote style='border:none; background:#fff;'>";
			}
			$nomeDoGrupo = $grupo;

			$checked = false;

			if( isset($_POST["vacina[{$id}]"])
				|| $id == 1 || $id == 2 || $id == 3
				|| $id == 4 || $id == 6 || $id == 28
				|| $id == 13|| $id == 27 || (is_array($arr) && in_array($id, $arr)) ) $checked = 'checked=true';

			echo "<div  style='clear:left' ><label><input type='checkbox' name='vacina[{$id}]'
					id='vacina[{$id}]' $checked />$nome</label></div>";

		}
		echo "</blockquote>";
		echo '</div>';

		$botao = new Vacina();

		$botao->ExibirBotoesDoFormulario('Confirmar');

		echo "</form>";

		echo '<div style="margin: 10px">
			<strong>Nota:</strong><blockquote>Caso nenhuma vacina seja selecionada
			para a caderneta, então todas serão exibidas.</blockquote></div><br /><br />';
	}

//------------------------------------------------------------------------------

	/**
	 * Exibe os dados para o cabeçalho da Caderneta de vacina.
	 *
	 * @param int $usuarioId
	 */
	public function ExibirCabecalhoCaderneta($usuarioId)
    {


        $sql = 'SELECT usuario.nome, usuario.nascimento, acs.nome, '
		     .     'unidadedesaude.nome, usuario.mae '
             . 'FROM usuario INNER JOIN acs ON usuario.Acs_id = acs.id '
             .     'INNER JOIN unidadedesaude ON acs.UnidadeDeSaude_id = unidadedesaude.id '
             .     'WHERE usuario.id = ? '
		     .         'AND usuario.ativo '
             .         'AND acs.ativo '
             .         'AND unidadedesaude.ativo';

		$a = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$a->bind_param('i', $usuarioId);
		$a->bind_result($nome, $nascimento, $acs, $unidadedesaude, $mae);
		$a->execute();
		$a->store_result();

		if ( $a->num_rows < 0 ) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar
			listar os dados do indivíduo.');

			$a->free_result();
			return false;
		}

		if ( $a->num_rows > 0 ) {

			$a->fetch();

			$arrUsuario = array();
			$arrResponsaveis = array();

			$data = new Data();

			if(!$mae) $mae = "<em><span style='color: #CCC'>Não Informada</span></em>";
			$arrUsuario[] = array('Nome' => $nome,
								  'Mãe'	 => $mae,
							  'Nascimento' => $data->InverterData($nascimento));

			$arrResponsaveis[] = array('Unidade' => $unidadedesaude,
									   'Agente Comunitário' => $acs);


		} elseif ( $a->num_rows == 0 ) {

			$this->AdicionarMensagem('Nenhum registro encontrado para este indivíduo');
			$a->free_result();
			return true;
		}

		$a->free_result();

		echo '<br /><center style="font-size: 14px;"><b>Caderneta de Vacinação</b></center>';

		Html::CriarTabelaDeArray($arrUsuario);

		echo '<div style="font-size: 9px; line-height: 10px;">';
		echo '<p>';

		Html::CriarTabelaDeArray($arrResponsaveis);

		echo '<br /></p>';
    }
	//--------------------------------------------------------------------------
	/**
	 * Cria uma caderneta com os dados de vacinação para cada usuário
	 *
	 * @param int $usuario
	 * @return true/false
	 */
	public function Caderneta($usuario , $listaDeVacinas = false)
	{
		$this->ExibirCabecalhoCaderneta($usuario);

		$arrVacinas = array();

		$frame = new Html();
        $data  = new Data();

		$sql = "SELECT usuario.nome, usuario.nascimento, acs.nome,
		unidadedesaude.nome, usuario.mae FROM usuario INNER JOIN
		acs ON usuario.Acs_id = acs.id INNER JOIN unidadedesaude ON
		acs.UnidadeDeSaude_id = unidadedesaude.id WHERE usuario.id = ?";

		$a = $this->conexao->prepare($sql) or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$a->bind_param('i', $usuario);

		$a->bind_result($nome, $nascimento, $acs, $unidadedesaude, $mae);

		$a->execute();

		$a->store_result();

		if ( $a->num_rows < 0 ) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar
			listar os dados do indivíduo.');

			$a->free_result();

			$a->close();

			return true;
		}

		if ( $a->num_rows > 0 ) {

			$a->fetch();

			$arrUsuario = array();
			$arrResponsaveis = array();


			$arrUsuario[] = array('Nome' => $nome,
								  'Mãe'	 => $mae,
							  'Nascimento' => $nascimento);

			$arrResponsaveis[] = array('Unidade' => $unidadedesaude,
									   'Agente Comunitário' => $acs);


		} elseif ( $a->num_rows == 0 ) {

			$this->AdicionarMensagem('Nenhum registro encontrado para este indivíduo');

			$a->free_result();

			return false;
		}

		$a->free_result();


		$arrVacinas = array();

		//$crip = new Criptografia();

		//parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));

	
		$sql = "SELECT id, nome, aplicacoesporpessoa, faixaetariainicio,
		faixaetariafim FROM vacina WHERE vacina.ativo ";



		$a = $this->conexao->prepare($sql) or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$a->bind_result($idvacina, $vacina, $aplicacoes, $faixaetariainicio,
						$faixaetariafim);

		$a->execute();

		$a->store_result();

		if ( $a->num_rows < 0 ) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar
			listar as vacinas do indivíduo.');

			$a->free_result();

			$a->close();

			return false;

		}

		if ( $a->num_rows > 0 ) {

			while( $a->fetch() ) {
				$arrVacinas[] = array('id' => $idvacina,
									  'Vacina' => $vacina,
									  'Aplicações' => $aplicacoes,
									  'FaixaInicial' => $faixaetariainicio,
									  'FaixaFinal' => $faixaetariafim,
									  'intervalos' => array() );
			}



		} elseif ( $a->num_rows == 0 ) {

			$this->AdicionarMensagem('Nenhum vacina encontrada.');

			$a->free_result();

			$a->close();

			return true;
		}

		$a->free_result();


		if((count($arrVacinas))%2){
			$col1 = (count($arrVacinas)) / 2  ;
			$col2 = ((count($arrVacinas)-4) / 2);
		}
		else {
			$col1 = count($arrVacinas) / 3;
			$col2 = count($arrVacinas)  ;
		}

		echo '<center><table width="700px" border="0">';
		echo '<tr>';
		echo '<td valign="top">';



		for ($i = 1; $i <= $col2; $i++) {

			if(isset($arrVacinas[$i])) {

				$vacinaDoisPontos = true;

                // verifica o tipo (soro|vacina) melhorar isso! 
				if(!substr_count(strtolower($arrVacinas[$i]['Vacina']), 'vacina') && !substr_count(strtolower(' '.$arrVacinas[$i]['Vacina']), ' soro ')) $vacinaDoisPontos = 'Vacina: ';
				echo "<b><center>$vacinaDoisPontos ".str_ireplace(' soro ', ' Soro: ', " {$arrVacinas[$i]['Vacina']}")."</center></b>";

                $this->ListarDoses($usuario, $arrVacinas[$i]['id'], $nascimento);
				if(isset($_POST['obs']) && $_POST['obs'] != '')
					echo "<center>", $this->ListarObs($usuario, $arrVacinas[$i]['id'], false, 0, false), "</center>";
				echo '<br />';
			}
		}

		echo '</td>';
		echo '<td valign="top">';

		for ($j = $col1+1; $j <= $col1+$col2-2; $j++) {

			if(isset($arrVacinas[$j])) {
				$vacinaDoisPontos = true;

                // verifica o tipo (soro|vacina) melhorar isso!
				if(!substr_count(strtolower($arrVacinas[$j]['Vacina']), 'vacina') && !substr_count(strtolower(' '.$arrVacinas[$j]['Vacina']), ' soro ')) $vacinaDoisPontos = 'Vacina: ';
				echo "<b><center>$vacinaDoisPontos ".str_ireplace(' soro ', ' Soro: ', " {$arrVacinas[$j]['Vacina']}")."</center></b>";

				$this->ListarDoses($usuario, $arrVacinas[$j]['id'], $nascimento);
				if(isset($_POST['obs']) && $_POST['obs'] != '')
					echo "<center>", $this->ListarObs($usuario, $arrVacinas[$j]['id'], false, 1, false), "</center>";
				echo '<br />';
			}
		}


        if( self::$temAlgumaVacinaComObs) {
            $icones[] = array('listar', 'Visualizar Observações');
            $legenda = new Legenda($icones);
            $legenda->ExibirLegenda();
        }

		echo '</td>';
		echo '</tr>';
		echo '</table></center>';
		echo '</div>';


	}

//------------------------------------------------------------------------------
    public function RetonarVacinaFilhaCerta($usuario_id,$nascimento,$vacina_id)
    {
        $sql = "SELECT DISTINCT id, faixaetariainicio, faixaetariafim
                    FROM `vacina`
                        WHERE vacina.ativo AND pertence = $vacina_id
                            ORDER BY faixaetariainicio, faixaetariafim";

		$vacinas = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));


		$vacinasFilhas_ids = $arrVacinas = array();

		$vacinas->bind_result($id, $faixaetariainicio, $faixaetariafim);
		$vacinas->execute();
		$vacinas->store_result();
		$existem_vacinas = $vacinas->num_rows;

		while ( $vacinas->fetch() ) $arrVacinas[] = $id;
        
        $vacinas->free_result();

        //=========

        foreach ( $arrVacinas as $id )
        {
            $sql = "SELECT DISTINCT id
                        FROM `usuariovacinado`
                            WHERE Vacina_id = $id 
                              AND Usuario_id = $usuario_id";

            $usuario = $this->conexao->prepare($sql)
                or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $usuario->execute();
            $usuario->store_result();
            $usuarioVacinado = $usuario->num_rows;

            if ($usuarioVacinado >= 1) return false;
            
            $usuario->free_result();
        }
        
        //=========

        foreach ( $arrVacinas as $id )
        {
            $sql = "SELECT DISTINCT id
                        FROM `vacina`
                            WHERE vacina.id = $id
                              AND DATEDIFF(NOW(), '$nascimento')
                                  BETWEEN faixaetariainicio AND faixaetariafim";

            $vacinas = $this->conexao->prepare($sql)
                or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $vacinas->execute();
            $vacinas->store_result();
            $vacinaEncaixa = $vacinas->num_rows;

            if ($usuarioVacinado >= 1) return true;
            $vacina_id = $id;
            $vacinas->free_result();
        }
        
        return $vacina_id;
    }
//------------------------------------------------------------------------------

    /**
     * Método que lista as doses ideais para a caderneta de vacina. Parecido com
     * o do "Vacinar"
     *
     * @param int $usuario_id
     * @param int $vacina_id
     * @param String $nascimento
     * @param int $campanha_id
     */
    public function ListarDoses($usuario_id, $vacina_id, $nascimento, $campanha_id = 2)
    {
        $vacina_id = $this->RetonarVacinaFilhaCerta($usuario_id,$nascimento,  $vacina_id);

        // Array que recebe dose, data ideal, validade da dose...
        $arrDoses = array();

        PessoaVacinavel::ReiniciarReforco();
        $pessoaVacinavel = new PessoaVacinavel();

        $pessoaVacinavel->UsarBaseDeDados();

        // Retorna array bidimensional com os seguintes dados:
        // - $diasIdeal
        // - $dose
        // - $diasAtrasoMax
        // - $nomeVacina
        $arrConfiguracoes = $pessoaVacinavel->CriarArrayDeConfiguracoesDeDoses($vacina_id);

        // Recupera o nascimento do usuario
       // list($nascimento) = $pessoaVacinavel->ExibirDadosDeVacinacaoDoUsuario($usuario_id);
        $this->_dataHoraVacinacao = $dataUltimaDose = $nascimento;
        

       $data = new Data();

        // Verifica se precisa listar a coluna "Validade da dose" para o usuário
        $listarValidade = $pessoaVacinavel->ListarValidadeDaDose($vacina_id);

        $pessoaVacinavel->_vacinarHabilitado = false;

        // Guarda as doses ideais baseadas no nascimento. Útil quando a data ideal
        // para vacinar não se baseia na anterior, mas na dose base:
        $datasDosesIdeais = array();

        $novaDataDoseIdeal = 1;

        $i = 0; // Contador para aplicar estilo à linha inteira:
        foreach($arrConfiguracoes as $configuracao)
        {
            list($diasIdeal, $dose, $diasAtrasoMax, $nomeVacina, $doseBase) = $configuracao;

            $tipoDaDose = $pessoaVacinavel->VerificarTipoDaDose($vacina_id, $dose);

            if($diasAtrasoMax == '43800') $diasAtrasoMax = 'Indeterminado';
            //if($diasAtrasoMax == '43800') $dataAtrasoMax = 'Indeterminado';

            // Cria o botão de aplicar vacina:
            if($dose > $pessoaVacinavel->VerificarUltimaDose($usuario_id, $vacina_id))
            {
                $botaoAplicar = ''; // vazio
            }
            else
            {
                // Pega data/hora e aproveita só a data, invertendo em seguida:
                list($dataVacinacao) = explode(' ',
                      $pessoaVacinavel->DataHoraVacinacao($usuario_id, $vacina_id, $dose));

                $this->_dataHoraVacinacao = $dataVacinacao;
                $dataVacinacao = $data->InverterData($dataVacinacao);

               // $dataVacinacao = $data->InverterData($dataVacinacao);


                // Então o botão "aplicar" dá lugar a data de vacinação:
                $botaoAplicar = $dataVacinacao;
                $pessoaVacinavel->_campoEstoqueHabilitado = false;

                //Obs da dose:

                $obsDaDose = $this->VerificarSeExisteObs($usuario_id, $vacina_id, $dose);

                $crip = new Criptografia();

                $qsObs = $crip->Cifrar("pagina=exibirObs&usuario_id=$usuario_id&vacina_id="
                                        ."$vacina_id&numerodadose=$dose&tipo");

                if($obsDaDose && !isset($_POST['obs'])) {

                    $botaoAplicar .= "<a href='?$qsObs' target='_blank'>
                        <img src='{$this->arquivoGerarIcone}?imagem=listar'
                        alt='Observações da dose' title='Observações da dose' width='15px' border='0' /></a>";
                    self::$temAlgumaVacinaComObs = true;
                 }
            }

            $dataAtrasoMax = $diasAtrasoMax;
            if( $diasAtrasoMax != 'Indeterminado' ) $dataAtrasoMax = $data->IncrementarData($dataUltimaDose, $diasIdeal-$diasAtrasoMax);


            // Verifica se a doseBase é a padrão, ou seja: a DOSE ANTERIOR
            if( $doseBase == 0 )
            {
                $datasDosesIdeais[$dose] = $data->IncrementarData($dataUltimaDose, $diasIdeal);
            }

            // Se não for a anterior, incrementa os dias baseando-se na data da
            // dose base (no caso de hepatite B, a 3a. dose é 180 dias após a 1a)
            else
            {
                $datasDosesIdeais[$dose] = $data->IncrementarData($datasDosesIdeais[$doseBase], $diasIdeal);
            }

            $dataDoseIdeal = $data->InverterData($datasDosesIdeais[$dose]);
            if( $dataAtrasoMax != 'Indeterminado' ) $dataAtraso = $data->InverterData($dataAtrasoMax);
            else $dataAtraso = $dataAtrasoMax;

            $arrProximaDose = $pessoaVacinavel->ProximaDose($usuario_id, $vacina_id);

            if( $arrProximaDose )
            {
                list($proximaDose, $ultimaDoseAplicada) = $arrProximaDose;

                $proximaDosePosterior = $ultimaDoseAplicada;

                if($ultimaDoseAplicada == $dose) $novaDataDoseIdeal = $proximaDose;
                elseif($proximaDosePosterior < $dose) $novaDataDoseIdeal = $data->IncrementarData($proximaDose, $diasIdeal);
                else $novaDataDoseIdeal =  ' * ';

                //-------


                if($doseBase > 0){
                    list($novaDataDoseIdeal) = explode(' ',
                        $pessoaVacinavel->DataHoraVacinacao($usuario_id, $vacina_id, $doseBase));

                   if($novaDataDoseIdeal)$novaDataDoseIdeal = $data->IncrementarData($novaDataDoseIdeal, $diasIdeal);

                   if( $data->CompararData($novaDataDoseIdeal, '>', $dataUltimaVacinacao )
                        && $dataUltimaVacinacao > 0)
                    {
                       $novaDataDoseIdeal = $data->IncrementarData($dataUltimaVacinacao, 30);
                    }

                    
                }
                // data de aplicacao da dose
                list($dataUltimaVacinacao) = explode(' ', $pessoaVacinavel->DataHoraVacinacao($usuario_id, $vacina_id, $dose));


                //-------

                // Se for uma data, inverte para o formato brasileiro:
                if( strlen($novaDataDoseIdeal) == 10 )
                {
                    $novaDataDoseIdeal = ($novaDataDoseIdeal);
                }
            }

            if( $listarValidade )
            {
                // Listando a coluna "Validade da dose":
                $arrDoses[$dose] = array('dose'          => $dose,
                                 //   'ideal para vacinar' => $diasIdeal,
                                    'data para vacinar'  => $dataDoseIdeal, //  baseado no nascimento
                                    'data validade'      => $dataAtraso,
                                  //  'validade da dose'   => $diasAtrasoMax,
                                    'nova data'          => $novaDataDoseIdeal,
                                    'aplicação'          =>$botaoAplicar);
            }
            else
            {
                $arrDoses[$dose] = array('dose'          => $dose,
                                  //  'ideal para vacinar' => $diasIdeal,
                                    'data para vacinar'  => $dataDoseIdeal, //  baseado no nascimento
                                    'nova data'          => $novaDataDoseIdeal,
                                    'aplicação'          =>$botaoAplicar);
            }

            if( $arrProximaDose ) unset($arrDoses[$dose]['data validade']);
            else                  unset($arrDoses[$dose]['nova data']);


            // Formatação da linha inteira para os textos e estilos de acordo
            // com o tipo de dose. ATENÇÃO: O método usa um ponteiro!
            $pessoaVacinavel->AplicarEstiloParaLinha($arrDoses[$dose], $tipoDaDose);

            $dataUltimaDose = $datasDosesIdeais[$dose];

             if(substr_count(strtolower(" $nomeVacina"), ' soro ')){

                unset($arrDoses[$dose]['data para vacinar']);
             }

        }

        $this->ExibirFormVacinacao($arrDoses, 0, $usuario_id, $vacina_id);

    }

//------------------------------------------------------------------------------


    /**
     * Verifica se existe observações na dose da vacina passada para o usuário
     * informado.
     * 
     * @param int $usuario_id
     * @param int $vacina_id
     * @param int $numeroDaDose
     * @param int $numeroCoCiclo
     * @return Boolean
     */
	public function VerificarSeExisteObs($usuario_id, $vacina_id, $numeroDaDose,
        $numeroCoCiclo = 1)
	{
		$sql = "SELECT obs
					FROM usuariovacinado
						WHERE Usuario_id = ?
						AND Vacina_id = ?
						AND numerodadose = ?
						AND numerodociclo = ?";

		$stmt = $this->conexao->prepare($sql);
		$stmt->bind_param('iiii', $usuario_id, $vacina_id, $numeroDaDose, $numeroCoCiclo);
		$stmt->bind_result($obs);
		$stmt->execute();
		$stmt->fetch();
		$stmt->free_result();

		return $obs;

	}

//------------------------------------------------------------------------------

    /**
     * Lista as observações na caderneta da pessoa.
     * 
     * @param int $usuario_id
     * @param int $vacina_id
     * @param int $numeroDaDose
     * @param int $numeroCoCiclo
     * @param Boolean $exibirNomeVacina
     */
	public function ListarObs($usuario_id, $vacina_id, $numeroDaDose = false,
							  $numeroCoCiclo = 1, $exibirNomeVacina = true)
	{

		$vacinaid = false;

		if($numeroDaDose) $numeroDaDose = " AND numerodadose = $numeroDaDose ";
		if($vacina_id) $vacinaid = " AND Vacina_id = $vacina_id ";
		if($numeroCoCiclo) $numeroCoCiclo = " AND numerodociclo = $numeroCoCiclo ";

		$sql = "SELECT obs , Vacina_id, numerodadose, numerodociclo
					FROM usuariovacinado
						WHERE Usuario_id = ?
						$numeroCoCiclo
						$numeroDaDose
						$vacinaid ORDER BY Vacina_id, numerodadose";

		$stmt = $this->conexao->prepare($sql);
		$stmt->bind_param('i', $usuario_id);
		$stmt->bind_result($obs, $vacina_id, $numerodadose, $numerodociclo);
		$stmt->execute();

		$vacinaAnterior = 1;
		$arr = array();
		while ($stmt->fetch()){

			$vacinaid = false;
			if($vacina_id != $vacinaAnterior && strlen($obs) > 3 && $exibirNomeVacina) $vacinaid = $vacina_id;
			//echo $this->RetornarCampoNome('vacina', $vacina_id);

			if(strlen($obs) > 3) {
				if($exibirNomeVacina)
				$arr[] =array("<p><b>Ciclo: $numerodociclo - Dose $numerodadose: </b>$obs</p>", $vacinaid);
				else $arr[] =array("<b>Ciclo: $numerodociclo - Dose $numerodadose: </b>$obs<br />", $vacinaid);
				$vacinaAnterior = $vacina_id;
			}
		}

		foreach ($arr as $valorArr) {

			list($linha, $vacina_id) = $valorArr;
			if($vacina_id) echo "<h3>",$this->RetornarCampoNome('vacina', $vacina_id), "</h3>";
			echo $linha;

		}

		$stmt->free_result();

	}

//------------------------------------------------------------------------------

    /**
     * Gera uma linha do array para que sejam exibidas as doses na caderneta de
     * vacina, parecido com a tabela na sessão "Vacinar" do sistema
     *
     * @param int $numerodadose
     * @param int $usuario_id
     * @param int $vacina_id
     * @param String $textoParaDoseIdeal
     * @param String $atraso
     * @param String $textoParaNovaDoseIdeal
     * @param String $novoAtraso
     * @return Array
     */
	public function GerarArrayParaTabelaDeDoses($numerodadose, $usuario_id,
				$vacina_id, $textoParaDoseIdeal, $atraso,
				$textoParaNovaDoseIdeal, $novoAtraso)
	{

        return 0;
		$pessoa = new PessoaVacinavel();

		$data = new Data();

		$atrasoFormatoBR = $data->InverterData($atraso);

		if( isset($novoAtraso) && $novoAtraso != '-') {

			$novoAtrasoFormatoBR = $data->InverterData($novoAtraso);

			$podeVacinar = $data->CompararData($novoAtraso, '>=');
		}
		elseif ($novoAtraso == '-') {
			$podeVacinar = true;
		}
		else $podeVacinar = false;

		$anoAtual = date('Y');

		if ($novoAtraso != '-' && $textoParaNovaDoseIdeal != '-') {

			if($pessoa->ValidadeIndeterminadaDaDose($textoParaNovaDoseIdeal, $novoAtraso)) {
				$novoAtrasoFormatoBR = 'Indeterminado';

			}

		}

		if ($atraso != '-' && $textoParaDoseIdeal != '-') {

			if($pessoa->ValidadeIndeterminadaDaDose($textoParaDoseIdeal, $atraso)) {
				$atrasoFormatoBR = 'Indeterminado';
			}

		}

		$tomouVacina = $this->PessoaTomouVacina($usuario_id, $vacina_id);

		if(isset($atraso) && !$tomouVacina) {



			$podeVacinar = $data->CompararData($atraso, '>=');

		}

		$datahoravacinacao = $this->DataHoraVacinacao($usuario_id,
									$vacina_id, $numerodadose, 1);

		$ultimaDose = $novoAtrasoFormatoBR = false;

		$ultimaDose = $this->VerificarUltimaDose($usuario_id, $vacina_id);

		$pessoa = new PessoaVacinavel();
		$pessoa->UsarBaseDeDados();

		if($this->TestarSeDoseFoiAplicada($numerodadose, $usuario_id, $vacina_id)) {



			if( $pessoa->CompararDosesDependentes($usuario_id, $vacina_id, $numerodadose, 1, false) == 0 ) {

				$dataDaVacinacao = $data->InverterData($datahoravacinacao);

				$campoVacinar = "Vacinado em $dataDaVacinacao";

			} else

				$campoVacinar = '<b>x</b>';


			$textoParaNovaDoseIdeal = '-';

			$novoAtrasoFormatoBR = '-';

		} else {

			$campoVacinar = '';

			if($pessoa->VerificarRestricao($usuario_id, $vacina_id) == true){

				$campoVacinar = '<b>x</b>';
				$textoParaNovaDoseIdeal = '-';
				$novoAtrasoFormatoBR = '-';

			}
		}

		if(strlen($campoVacinar) > 8) {

			$obsDaDose = $this->VerificarSeExisteObs($usuario_id, $vacina_id, $numerodadose);

			$crip = new Criptografia();

			$qsObs = $crip->Cifrar("pagina=exibirObs&usuario_id=$usuario_id&vacina_id="
									."$vacina_id&numerodadose=$numerodadose&tipo");
			if($obsDaDose && !isset($_POST['obs'])) {

				$campoVacinar .= "<a href='?$qsObs' target='_blank'>
					<img src='{$this->arquivoGerarIcone}?imagem=listar'
					alt='Observações da dose' title='Observações da dose' width='15px' border='0'></a>";

                self::$temAlgumaVacinaComObs = true;
            }
		}

		if ($ultimaDose > 0) {
			if($pessoa->VerificarRestricao($usuario_id, $vacina_id) == true)
			$linhaDaTabelaDeDoses = array('dose'	=> "{$numerodadose}ª",
						   	'data ideal'			=> $textoParaDoseIdeal,
						  //'validade da dose'		=> $atraso,
						   //	'nova data ideal'	 	=> $textoParaNovaDoseIdeal,
						   	'nova validade'			=> $novoAtrasoFormatoBR,
						   	'data de aplicação'		=> $campoVacinar);
			else
			$linhaDaTabelaDeDoses = array('dose'	=> "{$numerodadose}ª",
						   	'data ideal'			=> $textoParaDoseIdeal,
						  //'validade da dose'		=> $atraso,
						   	'nova ideal'	 	=> $textoParaNovaDoseIdeal,
						   	'nova validade'			=> $novoAtrasoFormatoBR,
						   	'data de aplicação'		=> $campoVacinar);

		} elseif($pessoa->VerificarRestricao($usuario_id, $vacina_id) == true) {
			$linhaDaTabelaDeDoses = array('dose'	=> "{$numerodadose}ª",
						   	'data ideal'	=> $textoParaDoseIdeal,
						   	'validade da dose'		=> $atrasoFormatoBR,
							'data de aplicação' => $campoVacinar);
		} else {
			$linhaDaTabelaDeDoses = array('dose'	=> "{$numerodadose}ª",
						   	'data ideal'	=> $textoParaDoseIdeal,
						   	'validade da dose'		=> $atrasoFormatoBR,
							'data de aplicação' => '');

		}

        /////////////////////

        if( $this->VerificarTipoDaDose($vacina_id, $numerodadose) == 2) {

            foreach($linhaDaTabelaDeDoses as $chave => &$linha) {

                $linha = $this->AplicarEstiloDoseDeReforco($linha, 'Reforço');

                if($chave == 'dose')
                    $linha = $this->AplicarEstiloDoseDeReforco('R'. self::$numeroDoReforco++, 'Reforço');
            }
        }

        if( $this->VerificarTipoDaDose($vacina_id, $numerodadose) == 3) {

            foreach($linhaDaTabelaDeDoses as $chave => $linha) {

                $linha = $this->AplicarEstiloDoseEspecial($linha, 'Dose especial');

                if($chave == 'dose')
                    $linha = $this->AplicarEstiloDoseEspecial('(esp.)', 'Dose especial');
            }

            $linhaDaTabelaDeDoses['data ideal']     = '';
            $linhaDaTabelaDeDoses['nova ideal']     = '';
            $linhaDaTabelaDeDoses['nova validade']  = '';
        }

		return $linhaDaTabelaDeDoses;
	}

//------------------------------------------------------------------------------
    /**
     * Exibe o formulário de vacinação da caderneta
     *
     * @param Array $arrDoses
     * @param int $campanha_id
     * @param int $usuario_id
     * @param int $vacina_id
     */
    public function ExibirFormVacinacao($arrDoses, $campanha_id, $usuario_id, $vacina_id)
    {
        $pessoaVacinavel = new PessoaVacinavel();
        $pessoaVacinavel->UsarBaseDeDados();

        $ultimaDoseAplicada = $pessoaVacinavel->VerificarUltimaDose($usuario_id, $vacina_id);
        $numerodadose       = $ultimaDoseAplicada ;
		$cicloAtual         = $pessoaVacinavel->CicloAtual($usuario_id, $vacina_id);

		$crip = new Criptografia();

		if( isset($campanha_id) && ctype_digit($campanha_id) && $campanha_id > 0 )
        {
            $this->ExibirVacinarSimples($usuario_id, $vacina_id, $campanha_id);
		}
        else
        {
		    Html::CriarTabelaDeArray($arrDoses, 0, 0, 0, 300, $corTh = '#b8c8d9', $regua = 'vertical',
                $atributos = 'border="1" bgcolor="#ffffff" align="center" cellspacing="0" frame="box" bordercolor="#b8c8d9"');
		}


		$pessoaImunizada = $pessoaVacinavel->PessoaFoiImunizada($usuario_id, $vacina_id);
    }
}