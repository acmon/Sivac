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

class RelatorioRotinaASeremVacinados extends RelatorioRotina
{
//------------------------------------------------------------------------------
    /**
     * Atributos
     */
     protected static $titulo = 'Indivíduos a serem vacinados em rotina';

     protected static $nota = 'Este relatório tem por finalidade listar os indivíduos
                        a serem vacinados em vacina de rotina, podendo o usuário do
                        sistema selecionar opcionalmente quaisquer filtros apresentados.';

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
        $html = new Html;
        $nomeDaSessao = 'paginacao_relatorio';
        $pagina_atual = $html->TratarPaginaAtual($pagina_atual, $nomeDaSessao);

        $aPartirDe = ($pagina_atual - 1) * Html::LIMITE;

        ////////////////////////////////////////////////////////////////////////

        $post = $this->UltimoPostParaAjax();

        // Setando os padrões (caso o usuário não os informe)
        $vacina_id             = 0;
        $cidade_id             = $_SESSION['cidade_id'];
        $unidade_id            = 0;
        $acs_id                = 0;
        $acamados              = 0;
        $naoResidentes         = 0;
        $numeroDaDoseEscolhida = 0;
        $faixaInicio           = 0;
        $unidadeInicio         = 'day';
        $faixaFim              = 60;
        $unidadeFim            = 'year';
        $datai                 = '01-01-1900';
        $dataf                 = '31-12-2100';

        // É necessário inicializar esta variável, pois de outra forma ela só
        // seria inicializada dentro do "NA" cidade, produzindo um aviso de
        // variável não setada, quando o usuário escolhesse "DA" cidade:
        $sqlNaoResidentes      = '';

        // Caso o usuário informe, usa as informações do formulário:
        if( isset($post['cidade_id'])      && (int)$post['cidade_id']       >  0 ) $cidade_id             = $post['cidade_id'];

        // Obs.: Quando ACS está junto com Unidade, não usamos unidade_id no POST,
        // Usamos "unidade" no POST:
        if( isset($post['vacina_id'])      && (int)$post['vacina_id']       >  0 ) $vacina_id             = $post['vacina_id'];
        if( isset($post['unidade'])        && (int)$post['unidade']         >  0 ) $unidade_id            = $post['unidade'];
        if( isset($post['acs'])            && (int)$post['acs']             >  0 ) $acs_id                = $post['acs'];
        if( isset($post['data_inicio'])    && strlen($post['data_inicio'])  == 10) $datai                 = $post['data_inicio'];
        if( isset($post['data_fim'])       && strlen($post['data_fim'])     == 10) $dataf                 = $post['data_fim'];
        if( isset($post['faixaInicio'])    && (int)$post['faixaInicio']     >  0 ) $faixaInicio           = $post['faixaInicio'];
        if( isset($post['unidadeInicio'])  && strlen($post['faixaInicio'])  >  0 ) $unidadeInicio         = $post['unidadeInicio'];
        if( isset($post['faixaFim'])       && (int)$post['faixaFim']        >  0 ) $faixaFim              = $post['faixaFim'];
        if( isset($post['unidadeFim'])     && strlen($post['faixaFim'])     >  0 ) $unidadeFim            = $post['unidadeFim'];
        if( isset($post['dose_escolhida']) && (int)$post['dose_escolhida']  >  0 ) $numeroDaDoseEscolhida = $post['dose_escolhida'];

        if( isset($post['sexo'])           && strlen($post['sexo'])         >  0 ) $sexo = $post['sexo'];

        if( isset($post['acamados'])       && (Boolean)$post['acamados']   == true) $acamados              = $post['acamados'];
        if( isset($post['naoResidentes'])  && (Boolean)$post['naoResidentes'] == true) $naoResidentes     = $post['naoResidentes'];

        $faixaEmDiasInicial = $this->ConverterUnidadeDeTempoParaDias($faixaInicio, $unidadeInicio);
        $faixaEmDiasFinal   = $this->ConverterUnidadeDeTempoParaDias($faixaFim, $unidadeFim);

        $faixaEmDiasFinal   += $this->InserirFolgaDeDias($unidadeFim);

        $data = new Data;

        $dataInicial = $data->InverterData($datai);
		$dataFinal   = $data->InverterData($dataf);

        // Quando o usuário quer selecionar as pessoas vacinadas "NA" cidade:
       /* if( $post['tipoDeConsulta'] == 'na')
        {
            // NA cidade
            $sqlCidade = $cidade_id ?
              ' AND usuariovacinado.UnidadeDeSaude_id IN '
              . '(SELECT unidadedesaude.id FROM unidadedesaude, bairro '                    
              .     'WHERE unidadedesaude.Bairro_id = bairro.id '
              .     "AND bairro.Cidade_id = $cidade_id) " :
              '';

            // NA unidade
            $sqlUnidade = $unidade_id ?
              " AND usuariovacinado.UnidadeDeSaude_id = $unidade_id ":
              '';

            // NA unidade do ACS escolhido:
            $sqlAcs = $acs_id ?
              ' AND usuariovacinado.UnidadeDeSaude_id IN '
              . "(SELECT UnidadeDeSaude_id FROM acs WHERE acs.id = $acs_id) " :
              '';

            // não residente NA cidade escolhida:
            if( $naoResidentes )
            {
                $sqlNaoResidentes = ' AND usuario.Bairro_id IN '
                    . " (SELECT id FROM bairro WHERE Cidade_id <> $cidade_id) ";

                // Nesse caso, é preciso zerar a SQL de ACS:
                $sqlAcs     = '';
            }
            else $sqlNaoResidentes = '';

            // Se o usuário tem um nível menor do que o de cidade ele só pode ver os
            // vacinados na unidade dele!
            if( $_SESSION['nivel'] < 10 )
            {
                $sqlUnidade = ' AND usuariovacinado.UnidadeDeSaude_id = '
                            . "{$_SESSION['unidadeDeSaude_id']} ";
            }
        }
*/
        // Quando o usuário quer selecionar as pessoas "DA" cidade:
 //       elseif( $post['tipoDeConsulta'] == 'da')
 //       {
            // DA cidade
            $sqlCidade = $cidade_id ?
              ' AND usuario.id IN '
              . '(SELECT usuario.id FROM usuario, bairro '
              .     'WHERE usuario.Bairro_id = bairro.id '
              .     "AND bairro.Cidade_id = $cidade_id) " :
              '';

            // DA unidade:
            $sqlUnidade = $unidade_id ?
              ' AND usuario.Acs_id IN '
              . "(SELECT id FROM acs WHERE UnidadeDeSaude_id = $unidade_id) " :
              '';

            // Se o usuário tem um nível menor do que o de cidade ele só pode ver os
            // cadastrados na unidade dele!
            if( $_SESSION['nivel'] < 10 )
            {
                $sqlUnidade = ' AND usuario.Acs_id IN '
                . '(SELECT id FROM acs WHERE '
                . "UnidadeDeSaude_id = {$_SESSION['unidadeDeSaude_id']}) ";
            }

            // DO vínculo do ACS escolhido:
            $sqlAcs = $acs_id ?
              " AND usuario.Acs_id = $acs_id " :
              '';
      //  }



        // Opções independentes de "NA" ou "DA":
        $sqlNumeroDaDose = $numeroDaDoseEscolhida ?
                  " AND usuariovacinado.numerodadose = $numeroDaDoseEscolhida ":
                  '';
                  
        $sqlAcamados = $acamados ?
          ' AND usuario.acamado = 0 ':
          '';

        // Montagem da SQL COUNT:
        $sql = 'SELECT COUNT( DISTINCT(usuario.id) ) '

             . 'FROM usuario, vacina '

             . "WHERE usuario.id NOT IN (SELECT usuariovacinado.Usuario_id
                                         FROM   usuariovacinado
                                         WHERE  usuariovacinado.Vacina_id = $vacina_id"
                                         // Por período:
                                         .     ' AND ( DATE(usuariovacinado.datahoravacinacao) '
                                         .          "BETWEEN '$dataInicial' AND '$dataFinal' ) "
                                         // nuemro da dose
                                         . "$sqlNumeroDaDose) "
             // Configurações da vacina
             . " AND vacina.id = $vacina_id "
             . 'AND DATEDIFF(NOW(), usuario.nascimento) '
             . 'BETWEEN vacina.faixaetariainicio AND vacina.faixaetariafim '

             // Se o usuário escolheu ou não cidade:
             .     $sqlCidade

             // Se o usuário escolheu ou não unidade:
             .     $sqlUnidade

             // Se o usuário escolheu ou não ACS:
             .     $sqlAcs

             // Se o usuário escolheu ou não só exibir pessoas acamadas:
             .     $sqlAcamados

             // Por faixa
             . "AND DATEDIFF(NOW(), usuario.nascimento)
                BETWEEN $faixaEmDiasInicial AND $faixaEmDiasFinal ";

             Depurador::Pre($sql);

        $stmt = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($totalDeRegistros);
        $stmt->execute();
        $stmt->fetch();
        $stmt->free_result();

        // Montagem da SQL:
        $sql = 'SELECT DISTINCT usuario.nome, usuario.mae, usuario.nascimento '

                        . 'FROM usuario, vacina '

             . "WHERE usuario.id NOT IN (SELECT usuariovacinado.Usuario_id
                                         FROM   usuariovacinado
                                         WHERE  usuariovacinado.Vacina_id = $vacina_id"
                                         // Por período:
                                         .     ' AND ( DATE(usuariovacinado.datahoravacinacao) '
                                         .          "< '$dataFinal' ) "
                                         // nuemro da dose
                                         . "$sqlNumeroDaDose) "
             // Configurações da vacina
             . " AND vacina.id = $vacina_id "
             . 'AND DATEDIFF(NOW(), usuario.nascimento) '
             . 'BETWEEN vacina.faixaetariainicio AND vacina.faixaetariafim '

             // Se o usuário escolheu ou não cidade:
             .     $sqlCidade

             // Se o usuário escolheu ou não unidade:
             .     $sqlUnidade

             // Se o usuário escolheu ou não ACS:
             .     $sqlAcs

             // Se o usuário escolheu ou não só exibir pessoas acamadas:
             .     $sqlAcamados

             // Por faixa
             . "AND DATEDIFF(NOW(), usuario.nascimento)
                BETWEEN $faixaEmDiasInicial AND $faixaEmDiasFinal "

             .     'AND usuario.ativo '

             .     'ORDER BY usuario.nome '

             .     "LIMIT $aPartirDe, " . Html::LIMITE;


		$stmt = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($nome, $mae, $nascimento);

		$stmt->execute();

		$stmt->store_result();
		$qtdregistro = $stmt->num_rows;

		while ($stmt->fetch())
        {
			if(!$mae) $mae = "<em><span style='color: #CCC'>Não Informada</span></em>";
			$arr[] = array('nome' => $nome,
                           'mãe' => $mae,
                           'nascimento' => $data->InverterData($nascimento));
		}

		$stmt->free_result();

        Depurador::Pre($sql);

        // Imprime o cabeçalho com os dados específicos escolhidos pelo usuário
        $this->ImprimirCabecalho( $this->CamposParaCabecalhoRelatorio($post, self::$titulo) );

		if ($qtdregistro == 0)
        {
			$this->AdicionarMensagem('Nenhum registro encontrado.');
			return true;
		}
        elseif($qtdregistro < 0)
        {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar listar
				os indivíduos.');
			return false;
		}
        else
        {
            $this->ControleDePaginacao($totalDeRegistros, __CLASS__);
			Html::CriarTabelaDeArray($arr);
            return true;
        }
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

        $validarSubmissao =	$this->MontarValidacaoDeSubmissaoDoFormulario();

        echo "<form name='relatorioRotina' id='relatorioRotina' method='post'
        action='./Rel/?$end' target='_blank' onsubmit=\"$validarSubmissao\">";

        //==================================================================
        ?>
        <p><div class="CadastroEsq">Vacina:  </div>
            <div class='CadastroDir' id="vacinasSelecionadas"><?php $this->SelectVacinas(true, false); ?> </div>
            <?php


             if( true /*????  VERIRIFCAR SE É RELATORIO DE QUNATIDADE  ????*/ ) {

                 echo '<div id="incluirDoseEspecifica"></div>';

                 echo "<p><div class='CadastroEsq'></div>
                    <div class='CadastroDir'><label><input type='checkbox'
                    checked='true' id='todas_doses' name='todas_doses'
                    onclick='IncluirDoseEspecifica(this.checked,
                    document.getElementById(\"vacina_id\").value,
                    \"incluirDoseEspecifica\")' />
                    Incluir todas as doses da vacina</label></div></p>";
             }

            ?>

        </p>
        <?php

        //==================================================================

        ?>
        <br />
        <div style="background-color: #eaeff3; display: table; clear: both; margin-left: 123px; width: 520px; padding-bottom: 10px">


            <div style="background-color: #fff; display: table; width: 140px; float: left; margin: 8px; padding: 3px">
                <label><input type="radio" name="tipoDeConsulta" value="na"
                    style="vertical-align: bottom;" disabled="true"
                    onclick="
                    var arr = new Array('naoResidentes', 'labelNaoResidentes');
                    TratarHabilitacaoAoSelecionar(!this.checked, arr);
                    arr = new Array('acs');
                    TratarHabilitacaoAoSelecionar(this.checked, arr);
                    document.getElementById('labelNaoResidentes').style.color = '#000'"/>Vacinados
                    na</label>
            </div>
            <div style="display: table; float: left; margin-top: 8px; padding-top: 3px">|</div>
            <div style="background-color: #fff; display: table; width: 140px; float: left; margin: 8px; padding: 3px">
            <label><input type="radio" name="tipoDeConsulta" value="da" checked="true" 
                    onclick="document.getElementById('naoResidentes').checked = false;
                    var arr = new Array('naoResidentes', 'labelNaoResidentes');
                    TratarHabilitacaoAoSelecionar(this.checked, arr);
                    arr = new Array('unidade');
                    TratarHabilitacaoAoSelecionar(!this.checked, arr);
                    arr = new Array('acs');
                    TratarHabilitacaoAoSelecionar(!this.checked, arr);
                    document.getElementById('labelNaoResidentes').style.color = '#CCC'"
                style="vertical-align: bottom;" />Com vínculo de</label>
            </div>
            <div style="clear: both"><hr /></div>

        <p>
        <div class="CadastroEsq" style="width: 125px">Cidade:</div>
        <div class='CadastroDir'><?php $this->SelectCidades(); ?></div> 
        <div class='CadastroDir'>
            <label id="labelNaoResidentes"><input type="checkbox" name="naoResidentes"
                id="naoResidentes" disabled="true" >
                Listar apenas indivíduos não residentes neste município</label>
        </div>
        </p>

        <?php

        //==================================================================

        ?>
        <p><div class="CadastroEsq" style="width: 125px">Unidade de Saúde:</div>
            <div class='CadastroDir'>

            <select name="unidade" id="unidade" style="width:305px;
				margin-left:2px;"
				onblur="ValidarCampoSelect(this, 'unidade de saúde', false)"
				onchange="PesquisarAcs(this.value)">
                    <?php $this->SelectUnidades(false); ?>
			 	</select>

            </div>

        </p>
        <?php

        //==================================================================

        ?>
       <p><div class="CadastroEsq" style="width: 125px">ACS: </div>
				<div class='CadastroDir'><select name="acs" id="acs"
                                 style="width:305px;" 
				onblur="ValidarCampoSelect(this, 'ACS')"
                >
				<option value="0">- selecione -</option></select></div>

        </p>

        </div>
        <?php

        //==================================================================

        ?>
        <p>
            <div class="CadastroEsq">
                Sexo:
            </div>

            <div class="CadastroDir" >
                <label><input type="radio" name="sexo" value="Ambos" checked="true" style="vertical-align: bottom;" />Ambos</label>
                <label><input type="radio" name="sexo" value="F" style="vertical-align: bottom;" />F</label>
                <label><input type="radio" name="sexo" value="M" style="vertical-align: bottom;" />M</label>
            </div>
        </p>

        <?php

        //==================================================================

        ?>
        <p><div class="CadastroEsq">Data entre:</div>
            <div class='CadastroDir'>
                <input type="text" name="data_inicio" id="data_inicio"
                size="10" maxlength="10"
                onblur="ValidarData(this, true)"
                onkeypress="return Digitos(event, this);"
                    onkeydown="return Mascara('DATA', this, event);"
                onkeyup="return Mascara('DATA', this, event);"
                value="<?php if (isset($_POST['data_inicio']))
                echo $_POST['data_inicio'] ?>" />

                e

                <input type="text" name="data_fim" id="data_fim"
                size="10" maxlength="10"
                onkeypress="return Digitos(event, this);"
                    onkeydown="return Mascara('DATA', this, event);"
                onkeyup="return Mascara('DATA', this, event);"
                onblur="ValidarData(this, true)"
                value="<?php if (isset($_POST['data_fim']))
                echo $_POST['data_fim'] ?>" />
            </div>

        </p>
        <?php

        //==================================================================
            ?>
            <p>
                <div class="CadastroEsq">Idade entre:</div>
                <div class='CadastroDir'>
                <input type="text" name="faixaInicio"  id="faixaInicio" style="width:50px" maxlength="7"
                onkeypress="return Digitos(event, this);"
                onblur="ValidarFaixaEtaria(document.escolherRelatorio.faixaInicio,
                document.escolherRelatorio.unidadeInicio,this,document.escolherRelatorio.unidadeFim,true)"/>
                <select name="unidadeInicio" id="unidadeInicio">
                <option value="day">Dia(s)</option>
                <option value="week">Semana(s)</option>
                <option value="month">Mês(s)</option>
                <option value="year">Ano(s)</option>
                </select>

                e

                <input type="text" name="faixaFim"  id="faixaFim" style="width:50px" maxlength="7"
                onkeypress="return Digitos(event, this);"
                onblur="ValidarFaixaEtaria(document.escolherRelatorio.faixaInicio,
                document.escolherRelatorio.unidadeInicio,this,document.escolherRelatorio.unidadeFim,true)"/>
                <select name="unidadeFim" id="unidadeFim">
                <option value="day">Dia(s)</option>
                <option value="week">Semana(s)</option>
                <option value="month">Mês(s)</option>
                <option value="year">Ano(s)</option>
                </select>
                </div>
            </p>
            <?php
        //------------------------------------------------------------------
            ?>
            <p>
                <div class="CadastroEsq">
                    Tipo de Exibição:
                </div>

                <div class="CadastroDir" >
                    <label><input type="radio" name="tipoExibicao" value="lista" checked="true" style="vertical-align: bottom;" />Lista</label>
                    <label><input type="radio" name="tipoExibicao" value="grafico" style="vertical-align: bottom;" />Gráfico</label>
                </div>
            </p>
            <p>
                <div class="CadastroEsq"></div>

                <div class="CadastroDir" >
                    <label>
                    <input type="checkbox" name="acamados" style="vertical-align: bottom;" />
                    Exibir apenas indivíduos acamados
                    </label>
                </div>
            </p>
         <?php

    $botao = new Vacina();
    $botao->ExibirBotoesDoFormulario('Confirmar');

    echo "</form>";

    echo "<p><strong>Nota:</strong><blockquote>";
       echo $this->Nota();
    echo "</blockquote></p>";

   }
 //------------------------------------------------------------------------------

    /**
     * Exibe o gráfico desta classe, por período
     */
	public function ExibirGrafico()
    {
        return;
    }

//------------------------------------------------------------------------------

    /**
     * Recebe duas datas e calcula o número de períodos ótimos para a exibição
     * em um gráfico de barras.
     *
     * @param String $dataInicial
     * @param String $dataFinal
     */
    private function CalcularBarrasDePeriodo($dataInicial, $dataFinal,
                $intBarras = 7, $cumulativo = true)
    {
        $periodos = array();
        $data = new Data();

        $intDias = $data->Diferenca($dataFinal, $dataInicial);

        // Não será possível gerar um gráfico com menos de 7 dias:
        if( $intDias < $intBarras )
        {
            $intDias = $intBarras;
        }

        // Se a quantidade de dias dividida por 7 for exata, usa o incremento:
        if( $intDias % $intBarras == 0 )
        {
            $incremento    = $intDias / $intBarras;
            $diasRestantes = 0;
        }

        // Senão, usa o incremento e os dias restantes para o período final:
        else
        {
            $incremento    = (int)($intDias / $intBarras);
            $diasRestantes = $intDias % $intBarras;
        }

        for( $i=0; $i<$intBarras; $i++ )
        {
            if( $cumulativo ) $datai = $dataInicial;
            else $datai = $data->IncrementarData($dataInicial, $incremento * $i);
            $dataf = $data->IncrementarData($dataInicial, $incremento * (1+$i) );

            if( $i == ($intBarras - 1) )
            {
                $dataf = $data->IncrementarData($dataf, $diasRestantes);
            }

            $diaMesAnoI = (int)substr($datai, 8) . '/' . (int)substr($datai, 5, 2) . '/' . substr($datai, 2, 2);
            $diaMesAnoF = (int)substr($dataf, 8) . '/' . (int)substr($dataf, 5, 2) . '/' . substr($dataf, 2, 2);

            $periodos[$i]['dataInicio'] = $datai;
            $periodos[$i]['dataFim']    = $dataf;

            if( $i == 0 ) $periodos[$i]['rotulo'] = "$diaMesAnoI a $diaMesAnoF";
            else          $periodos[$i]['rotulo'] = "até $diaMesAnoF";
        }

        return $periodos;
    }
//------------------------------------------------------------------------------

}