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


class RelatorioCampanhaNaoVacinados extends RelatorioCampanha
{
//------------------------------------------------------------------------------
    /**
     * Atributos
     */
     protected static $titulo = 'Indivíduos não vacinados pela campanha';

     protected static $nota = 'Este relatório tem por finalidade listar os indivíduos ainda
                               não vacinados pela campanha, podendo o usuário do sistema
                               selecionar opcionalmente quaisquer filtros apresentados.';

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
        $campanha_id           = 0;
        $cidade_id             = $_SESSION['cidade_id'];
        $unidade_id            = 0;
        $acs_id                = 0;
        $acamados              = 0;
        $naoResidentes         = 0;
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
        if( isset($post['campanha_id'])    && (int)$post['campanha_id']     >  0 ) $campanha_id           = $post['campanha_id'];
        if( isset($post['unidade'])        && (int)$post['unidade']         >  0 ) $unidade_id            = $post['unidade'];
        if( isset($post['acs'])            && (int)$post['acs']             >  0 ) $acs_id                = $post['acs'];
        if( isset($post['data_inicio'])    && strlen($post['data_inicio'])  == 10) $datai                 = $post['data_inicio'];
        if( isset($post['data_fim'])       && strlen($post['data_fim'])     == 10) $dataf                 = $post['data_fim'];
        if( isset($post['faixaInicio'])    && (int)$post['faixaInicio']     >  0 ) $faixaInicio           = $post['faixaInicio'];
        if( isset($post['unidadeInicio'])  && strlen($post['faixaInicio'])  >  0 ) $unidadeInicio         = $post['unidadeInicio'];
        if( isset($post['faixaFim'])       && (int)$post['faixaFim']        >  0 ) $faixaFim              = $post['faixaFim'];
        if( isset($post['unidadeFim'])     && strlen($post['faixaFim'])     >  0 ) $unidadeFim            = $post['unidadeFim'];

        if( isset($post['sexo'])           && strlen($post['sexo'])         >  0 ) $sexo = $post['sexo'];

        if( isset($post['acamados'])       &&(Boolean)$post['acamados']   == true) $acamados              = $post['acamados'];
        if( isset($post['naoResidentes'])  && (Boolean)$post['naoResidentes'] == true) $naoResidentes     = $post['naoResidentes'];

        $faixaEmDiasInicial = $this->ConverterUnidadeDeTempoParaDias($faixaInicio, $unidadeInicio);
        $faixaEmDiasFinal   = $this->ConverterUnidadeDeTempoParaDias($faixaFim, $unidadeFim);

        // Acrescentando a folga (o cara tem 1 ano até 1 ano, 11 meses e 29 dias):
        $faixaEmDiasFinal   += $this->InserirFolgaDeDias($unidadeFim);

        $data = new Data;

        $dataInicial = $data->InverterData($datai);
		$dataFinal   = $data->InverterData($dataf);


        // Quando o usuário quer selecionar as pessoas "DA" cidade:
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

        $sqlAcamados = $acamados ?
          ' AND usuario.acamado  = 0':
          '';


        $strSql = $this->MontarSqlsConfiguracoesCampanha($campanha_id);

        $totalDeRegistros = $this->ExecutarCountSql($strSql
                                                    // Se o usuário escolheu ou não cidade:
                                                     .     $sqlCidade

                                                     // Se o usuário escolheu ou não unidade:
                                                     .     $sqlUnidade

                                                     // Se o usuário escolheu ou não ACS:
                                                     .     $sqlAcs

                            
                                                     // Se o usuário escolheu ou não só exibir pessoas acamadas:
                                                     .     $sqlAcamados

                                                     // Por faixa
                                                     . "AND DATEDIFF((SELECT datafinal FROM campanha WHERE campanha.id = $campanha_id), usuario.nascimento)
                                                        BETWEEN $faixaEmDiasInicial AND $faixaEmDiasFinal "
            
                                                     . ' AND usuario.ativo '

                                                     // Usuarios nao vacinados
                                                     . "AND  usuario.id NOT IN ( SELECT usuariovacinadocampanha.Usuario_id FROM usuariovacinadocampanha
                                                                WHERE usuariovacinadocampanha.Campanha_id = $campanha_id "
                                                                // Por período:
                                                                .    'AND ( DATE(usuariovacinadocampanha.datahoravacinacao) '
                                                                .          "BETWEEN '$dataInicial' AND '$dataFinal' ) )"
                                                    );


        $arr  = $this->ExecutarConsultaSql(    $strSql
                                        // Se o usuário escolheu ou não cidade:
                                         .     $sqlCidade

                                         // Se o usuário escolheu ou não unidade:
                                         .     $sqlUnidade

                                         // Se o usuário escolheu ou não ACS:
                                         .     $sqlAcs

                   

                                         // Se o usuário escolheu ou não só exibir pessoas acamadas:
                                         .     $sqlAcamados

                                         // Por faixa
                                         . "AND DATEDIFF((SELECT datafinal FROM campanha WHERE campanha.id = $campanha_id), usuario.nascimento)
                                            > $faixaEmDiasInicial "

                                         // Usuarios nao vacinados
                                         . "AND  usuario.id  IN ( SELECT usuariovacinadocampanha.Usuario_id FROM usuariovacinadocampanha
                                                    WHERE usuariovacinadocampanha.Campanha_id = $campanha_id "
                                                    // Por período:
                                                    .    'AND ( DATE(usuariovacinadocampanha.datahoravacinacao) '
                                                    .          "< '$dataInicial' ) )"
                                         . ' AND usuario.ativo '
                                         . ' ORDER BY usuario.nome '
                                         . " LIMIT $aPartirDe, " . Html::LIMITE);


        // Imprime o cabeçalho com os dados específicos escolhidos pelo usuário
        $this->ImprimirCabecalho( $this->CamposParaCabecalhoRelatorio($post, self::$titulo) );

        $this->ControleDePaginacao($totalDeRegistros, __CLASS__);

		if($totalDeRegistros <  0 )
        {
			$this->AdicionarMensagemDeErro('Erro encontrado ao tentar acessar
			os dados do banco de dados');
			return false;
		}
		if($totalDeRegistros == 0 ) $this->AdicionarMensagem('Nenhum registro encontrado');
		if($totalDeRegistros >  0 ) Html::CriarTabelaDeArray($arr);

		return true;
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

        for( $i=1; $i<$intBarras; $i++ )
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

    /**
     * Exibe o formulario para os relatorios de campanha.
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

        echo "<form name='relatorioCampanha' id='relatorioCampanha' method='post'
        action='./Rel/?$end' target='_blank' onsubmit=\"$validarSubmissao\">";

        //==================================================================
        ?>
			<p><div class="CadastroEsq">Campanha:</div>
                <div class='CadastroDir'><?php $this->SelectCampanha() ?><div>
                <hr /></div></div>
			</p>
        <?php

        //==================================================================

        ?>
        <br />
        <div style="background-color: #eaeff3; display: table; clear: both; margin-left: 123px; width: 520px; padding-bottom: 10px">


            <div style="background-color: #fff; display: table; width: 140px; float: left; margin: 8px; padding: 3px">
                <label><input type="radio" name="tipoDeConsulta" value="na" disabled="true"
                    style="vertical-align: bottom;"
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
            <label id="labelNaoResidentes"><input type="checkbox" name="naoResidentes" disabled="true"
                id="naoResidentes">
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

    $this->MontarSqlsConfiguracoesCampanha(8);

   }

   //---------------------------------------------------------------------------
   /**
    *
    * @param int $campanha_id
    */
   public function MontarSqlsConfiguracoesCampanha($campanha_id)
   {
        $arrVacinasDaCampanha = $this->ArrayDeVacinasDaCampanha($campanha_id);

        $arrConfiguracaoDaVacina = $this->ArrayDeConfiguracoesDaVacina($arrVacinasDaCampanha);

        $arrStrSqlConfig = $this->MontarArrDeSql($arrConfiguracaoDaVacina, $campanha_id);

        return $this->MontarStringSql($arrStrSqlConfig);
        
       // return $this->ExecutarCountSql($sqlConfig);
   }
   //---------------------------------------------------------------------------
   /**
    *
    * @param Array $arrStrSqlConfig
    * @return String
    */
   public function MontarStringSql($arrStrSqlConfig)
   {
        $sqlEtnia   = ' etnia.id = usuario.Etnia_id ';
        $sqlEstado  = ' usuario.Bairro_id = bairro.id AND bairro.Cidade_id = cidade.id
                        AND ';

        $sqlConfig = $sqlEtnia .' AND '. $sqlEstado;

        $i = 0;

        $sqlConfig .= ' ( ';

        if(!is_array($arrStrSqlConfig)) $arrStrSqlConfig =  array('0');

        foreach($arrStrSqlConfig as $arrConfiguracao)
        {

            if(!is_array($arrConfiguracao)) $arrConfiguracao =  array('0');

            $j = 0;

            if($i > 0) $sqlConfig .= ' OR ( ';
            else       $sqlConfig .= ' ( ';
            foreach ( $arrConfiguracao as $config)
            {
               if($j > 0 && strlen($config) > 0) $sqlConfig .= ' AND ';
               $sqlConfig .=  $config;
               $j++;
            }
            $sqlConfig .= ' ) ';
            $i++;


        }
        $sqlConfig .= ' ) ';

        return $sqlConfig;
   }
   //---------------------------------------------------------------------------
   /**
    *
    * @param Array  $arrVacinasDaCampanha
    * @return Array 
    */
   public function ArrayDeConfiguracoesDaVacina($arrVacinasDaCampanha)
   {
        if(!is_array($arrVacinasDaCampanha)) $arrVacinasDaCampanha =  array('0');
        foreach($arrVacinasDaCampanha as $idVacinaDaCampanha)
        {

            $sql = "SELECT id, VacinaDaCampanha_id, idadeInicio,	idadeFinal, sexo, etnias, estados
                        FROM configuracaodavacina WHERE VacinaDaCampanha_id = $idVacinaDaCampanha ";

            $stmt = $this->conexao->prepare($sql) or
                die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $stmt->bind_result($id, $VacinaDaCampanha_id, $idadeInicio, $idadeFinal, $sexo, $etnias, $estados);
            $stmt->execute();

            $data = new Data;

            while($stmt->fetch()) {

                $arrConfiguracaoDaVacina[]  = array('idadeInicio' => $idadeInicio,
                                                    'idadeFinal'  => $idadeFinal,
                                                    'sexo'        => $sexo,
                                                    'etnias'      => $etnias,
                                                    'estados'     => $estados
                                                   );

            }

            $stmt->free_result();

        }

        return $arrConfiguracaoDaVacina;
   }
   //---------------------------------------------------------------------------
   /**
    *
    * @param Int $campanha_id
    * @return Array 
    */
   public function ArrayDeVacinasDaCampanha($campanha_id)
   {
        $sql = "SELECT id, Vacina_id  FROM vacinadacampanha WHERE Campanha_Id = $campanha_id ";

        $stmt = $this->conexao->prepare($sql) or
            die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($idVacinaDaCampanha, $Vacina_id );
		$stmt->execute();

		while($stmt->fetch()) $arrVacinasDaCampanha[]  = $idVacinaDaCampanha;

        $stmt->free_result();

        return $arrVacinasDaCampanha;
        
   }
   //---------------------------------------------------------------------------
   /**
    *
    * @param String $strTexto
    * @return String
    */
   public function AdicionarAspas($strTexto)
   {
       return "'". str_replace(', ',"', '", $strTexto). "'";
   }
   //---------------------------------------------------------------------------
   /**
    *
    * @param Array  $arrConfiguracaoDaVacina
    * @param Int $campanha_id
    * @return Array
    */
   public function MontarArrDeSql($arrConfiguracaoDaVacina, $campanha_id)
   {
        $strSqlEtnias  = '';
        $strSqlEstados = '';

        if(!is_array($arrConfiguracaoDaVacina)) return;

        foreach($arrConfiguracaoDaVacina as $arrConfiguracao)
        {

            $idadeInicio       = $arrConfiguracao['idadeInicio'];
            $idadeFinal        = $arrConfiguracao['idadeFinal'];

            $strSqlFaixa       = " DATEDIFF((SELECT datafinal FROM campanha WHERE campanha.id = $campanha_id), usuario.nascimento) "
                               . " BETWEEN $idadeInicio AND $idadeFinal ";

            $sexo              = " ( usuario.sexo = '{$arrConfiguracao['sexo']}' OR 'ambos' = '{$arrConfiguracao['sexo']}' )";

            $strEtnias = $this->AdicionarAspas($arrConfiguracao['etnias']);
            if($arrConfiguracao['etnias'] != 'todas' ) $strSqlEtnias = "  etnia.nome IN ($strEtnias) ";
            $sqlEtnia = '';

            $strEstados = $this->AdicionarAspas($arrConfiguracao['estados']);
            if($arrConfiguracao['estados'] != 'todos' ) $strSqlEstados = "  cidade.Estado_id IN ($strEstados) ";
            $sqlEstado = '';

            $arrStrSqlConfig[] = Array('faixa'    =>  $strSqlFaixa,
                                    'sexo'        =>  $sexo,
                                    'etnias'      =>  $strSqlEtnias,
                                    'estados'     =>  $strSqlEstados);

        }

        return $arrStrSqlConfig;
   }
   //---------------------------------------------------------------------------
   /**
    *
    * @param String $strSql
    * @return Array 
    */
   public function ExecutarConsultaSql($strSql)
   {
       $sqlUsuario = 'SELECT DISTINCT usuario.nome, usuario.mae, usuario.nascimento
                      FROM usuario, etnia, cidade, bairro WHERE ';

        $stmt = $this->conexao->prepare($sqlUsuario.$strSql) or
            die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        $stmt->bind_result($nome, $mae, $nascimento);
        $stmt->execute();

        $data = new Data;

        while($stmt->fetch()) {

            $arr[]  = array('nome' => $nome,
                            'mae'  => $mae,
                            'nasc' => $data->InverterData($nascimento));

        }

        $stmt->free_result();

        return $arr;
   }
   //---------------------------------------------------------------------------
   /**
    *
    * @param String $strSql
    * @return Int
    */
   public function ExecutarCountSql($strSql)
   {
       $sqlUsuario = 'SELECT  COUNT( DISTINCT usuario.nome)
                     FROM usuario, etnia, cidade, bairro WHERE ';

        $stmt = $this->conexao->prepare($sqlUsuario.$strSql) or
            die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        $stmt->bind_result($count);
        $stmt->execute();

        $data = new Data;

        $stmt->fetch();

        $stmt->free_result();

        return $count;
   }
   //---------------------------------------------------------------------------
   public function MontarSqlsConfiguracoesCampanhaAntigo()
   {
       /*
       $sql = "SELECT id, VacinaDaCampanha_id, idadeInicio,	idadeFinal, sexo, etnias, estados
                    FROM configuracaodavacina, "

       $stmt = $this->conexao->prepare($sql) or
            die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($id, $VacinaDaCampanha_id, $idadeInicio, $idadeFinal, $sexo, $etnias, $estados);
		$stmt->execute();

		$data = new Data;

		while($stmt->fetch()) $arr[] = array('nome' => $nome,
						      'mãe' => $mae,
						      'nascimento' => $data->InverterData($nascimento));

        $stmt->free_result();
        
        */

        $campanha_id = 8;

       $sql = "SELECT id, Vacina_id  FROM vacinadacampanha WHERE Campanha_Id = $campanha_id ";

       $stmt = $this->conexao->prepare($sql) or
            die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($idVacinaDaCampanha, $Vacina_id );
		$stmt->execute();

		$data = new Data;

		while($stmt->fetch()) {
           // echo "id = $idVacinaDaCampanha, Vacina = $Vacina_id <br />";
            $arrVacinasDaCampanha[]  = $idVacinaDaCampanha;

        }

        $stmt->free_result();

        foreach($arrVacinasDaCampanha as $idVacinaDaCampanha)
        {

            //============================
            $sql = "SELECT id, VacinaDaCampanha_id, idadeInicio,	idadeFinal, sexo, etnias, estados
                        FROM configuracaodavacina WHERE VacinaDaCampanha_id = $idVacinaDaCampanha ";

            $stmt = $this->conexao->prepare($sql) or
                die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $stmt->bind_result($id, $VacinaDaCampanha_id, $idadeInicio, $idadeFinal, $sexo, $etnias, $estados);
            $stmt->execute();

            $data = new Data;

            while($stmt->fetch()) {
               // echo "$id, $VacinaDaCampanha_id, $idadeInicio, $idadeFinal, $sexo, $etnias, $estados <br />";

                $arrEtnias  = explode(', ', $etnias);
                $arrEstados = explode(', ', $estados);

                $arrConfiguracaoDaVacina[]  = array('idadeInicio' => $idadeInicio,
                                                    'idadeFinal'  => $idadeFinal,
                                                    'sexo'        => $sexo,
                                                    'etnias'      => $arrEtnias,
                                                    'estados'     => $arrEstados);

                $arrEtnias = $arrEstados = array();

            }

            $stmt->free_result();

            //============================

        }
        echo '<pre>';


       print_r($arrConfiguracaoDaVacina);

       // $temp[0] = $arrConfiguracaoDaVacina[0];

       // print_r($temp);
       // foreach($temp as $arrConfiguracao)
        foreach($arrConfiguracaoDaVacina as $arrConfiguracao)
        {

            $idadeInicio = $arrConfiguracao['idadeInicio'];
            $idadeFinal = $arrConfiguracao['idadeFinal'];
            $sexo = $arrConfiguracao['sexo'];
            $arrEtnias = $arrConfiguracao['etnias'];
            $arrEstados = $arrConfiguracao['estados'];

            //****************
            if(count($arrEtnias) > 1) foreach($arrEtnias as &$strEtnia) $strEtnia = "etnia.nome = '$strEtnia' OR ";
            else $arrEtnias[0] = "todas = '{$arrEtnias[0]}' ";
            $arrEtnias[count($arrEtnias)-1] = str_replace('OR', '', $arrEtnias[count($arrEtnias)-1]); ;
            //****************

            //****************
            if(count($arrEstados) > 1) foreach($arrEstados as &$strEstados) $strEstados = "estado.id = '$strEstados' OR ";
            else $arrEstados[0] = "todos = '{$arrEstados[0]}' ";
            $arrEstados[count($arrEstados)-1] = str_replace('OR', '', $arrEstados[count($arrEstados)-1]); ;
            //****************

            $arrSqlConfig[] = Array('idadeInicio' => $idadeInicio,
                                    'idadeFinal'  =>  $idadeFinal,
                                    'sexo'        =>  $sexo,
                                    'etnias'      =>  $arrEtnias,
                                    'estados'     =>  $arrEstados);
            
        }

      //  print_r($arrSqlConfig);
        echo '<hr />';

        $sqlEtnia  = ' AND etnia.id = usuario.Etnia_id';
        $sqlEstado = ' AND usuario.Bairro_id = bairro.id AND bairro.Cidade_id = cidade.id
                       AND cidade.Estado_id = estado.id';
        foreach($arrSqlConfig as $arrConfiguracao)
        {

            $idadeInicio        = $arrConfiguracao['idadeInicio'];
            $idadeFinal        = $arrConfiguracao['idadeFinal'];

            $strSqlFaixa       = " AND DATEDIFF((SELECT datafinal FROM campanha WHERE campanha.id = $campanha_id), usuario.nascimento) "
                               . " BETWEEN $idadeInicio AND $idadeFinal ";

            $sexo              = " AND usuario.sexo = '{$arrConfiguracao['sexo']}' ";

            
            $strSqlEtnias      = $sqlEtnia . ' AND ('
                               . implode('', $arrConfiguracao['etnias']) .') ';
            $sqlEtnia = '';
                              
            $strSqlEstados     = $sqlEstado. ' AND ('
                               . implode('', $arrConfiguracao['estados']).') ';

            $sqlEstado = '';

            $arrStrSqlConfig[] = Array('faixa'    =>  $strSqlFaixa,
                                        'sexo'    =>  $sexo,
                                        'etnias'  =>  $strSqlEtnias,
                                        'estados' =>  $strSqlEstados);

        }

        //print_r($arrStrSqlConfig);

        $sqlConfig = '';
        foreach($arrStrSqlConfig as $arrConfiguracao)
        {

           foreach ( $arrConfiguracao as $config)
           {
               $sqlConfig .=  $config;
           }
           
           
        }

        $sqlConfig =  stristr($sqlConfig, ' AND');
        //echo '=====> '.$sqlConfig;

        // etnia
        // etnia.id = usuario.Etnia_id


        // estado, bairro, cidade
        //AND usuario.Bairro_id = bairro.id AND bairro.Cidade_id = cidade.id
        //AND cidade.Estado_id = estado.id
        
   }
   //---------------------------------------------------------------------------

}