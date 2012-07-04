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

class RelatorioRotinaVacinados extends RelatorioRotina
{
//------------------------------------------------------------------------------
    /**
     * Atributos
     */
     protected static $titulo = 'Indiv�duos vacinados em rotina';

     protected static $nota = 'Este relat�rio tem por finalidade listar os indiv�duos
                        vacinados em vacina de rotina,podendo o usu�rio do sistema
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
     * Retorna o t�tulo do relat�rio. Necess�rio pois na classe m�e (Relatorio)
     * � usado $this->Titulo() genericamente para retornar o t�tulo de cada
     * filha, o que n�o funcionaria se chamasse self::$titulo na classe m�e.
     *
     * @return String
     */
    protected function Titulo()
    {
        return self::$titulo;
    }

//------------------------------------------------------------------------------

    /**
     * Retorna a nota do relat�rio. Necess�rio pois na classe m�e (Relatorio)
     * � usado $this->Nota() genericamente para retornar o t�tulo de cada
     * filha, o que n�o funcionaria se chamasse self::$nota na classe m�e.
     *
     * @return String
     */
    protected function Nota()
    {
        return self::$nota;
    }

//------------------------------------------------------------------------------

    /**
     * Exibe o relat�rio desta classe.
     *
     * @param Boolean|int $pagina_atual (opcional) P�gina usada quando o
     *                    o  relat�rio est� paginado
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

        // Setando os padr�es (caso o usu�rio n�o os informe)
        $vacina_id             = 0;
        $cidade_id             = $_SESSION['cidade_id'];
        $unidade_id            = 0;
        $acs_id                = 0;
        $acamados              = 0;
        $naoResidentes         = 0;
        $numeroDaDoseEscolhida = 0;
        $faixaInicio           = 0;
        $unidadeInicio         = 'day';
        $faixaFim              = 120;
        $unidadeFim            = 'year';
        $datai                 = '01-01-1900';
        $dataf                 = '31-12-2100';

        // � necess�rio inicializar esta vari�vel, pois de outra forma ela s�
        // seria inicializada dentro do "NA" cidade, produzindo um aviso de
        // vari�vel n�o setada, quando o usu�rio escolhesse "DA" cidade:
        $sqlNaoResidentes      = '';

        // Caso o usu�rio informe, usa as informa��es do formul�rio:
        if( isset($post['cidade_id'])      && (int)$post['cidade_id']       >  0 ) $cidade_id             = $post['cidade_id'];

        // Obs.: Quando ACS est� junto com Unidade, n�o usamos unidade_id no POST,
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

        if( isset($post['acamados'])       &&(Boolean)$post['acamados']   == true) $acamados              = $post['acamados'];
        if( isset($post['naoResidentes'])  && (Boolean)$post['naoResidentes'] == true) $naoResidentes     = $post['naoResidentes'];

        $faixaEmDiasInicial = $this->ConverterUnidadeDeTempoParaDias($faixaInicio, $unidadeInicio);
        $faixaEmDiasFinal   = $this->ConverterUnidadeDeTempoParaDias($faixaFim, $unidadeFim);

        // Acrescentando a folga (o cara tem 1 ano at� 1 ano, 11 meses e 29 dias):
        $faixaEmDiasFinal   += $this->InserirFolgaDeDias($unidadeFim);

        $data = new Data;

        $dataInicial = $data->InverterData($datai);
		$dataFinal   = $data->InverterData($dataf);

        // Quando o usu�rio quer selecionar as pessoas vacinadas "NA" cidade:
        if( $post['tipoDeConsulta'] == 'na')
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

            // n�o residente NA cidade escolhida:
            if( $naoResidentes )
            {
                $sqlNaoResidentes = ' AND usuario.Bairro_id IN '
                    . " (SELECT id FROM bairro WHERE Cidade_id <> $cidade_id) ";

                // Nesse caso, � preciso zerar a SQL de ACS:
                $sqlAcs     = '';
            }
            else $sqlNaoResidentes = '';

            // Se o usu�rio tem um n�vel menor do que o de cidade ele s� pode ver os
            // vacinados na unidade dele!
            if( $_SESSION['nivel'] < 10 )
            {
                $sqlUnidade = ' AND usuariovacinado.UnidadeDeSaude_id = '
                            . "{$_SESSION['unidadeDeSaude_id']} ";
            }
        }

        // Quando o usu�rio quer selecionar as pessoas "DA" cidade:
        elseif( $post['tipoDeConsulta'] == 'da')
        {
            // DA cidade
            $sqlCidade = $cidade_id ?
              ' AND usuariovacinado.Usuario_id IN '
              . '(SELECT usuario.id FROM usuario, bairro '
              .     'WHERE usuario.Bairro_id = bairro.id '
              .     "AND bairro.Cidade_id = $cidade_id) " :
              '';

            // DA unidade:
            $sqlUnidade = $unidade_id ?
              ' AND usuario.Acs_id IN '
              . "(SELECT id FROM acs WHERE UnidadeDeSaude_id = $unidade_id) " :
              '';

            // Se o usu�rio tem um n�vel menor do que o de cidade ele s� pode ver os
            // cadastrados na unidade dele!
            if( $_SESSION['nivel'] < 10 )
            {
                $sqlUnidade = ' AND usuario.Acs_id IN '
                . '(SELECT id FROM acs WHERE '
                . "UnidadeDeSaude_id = {$_SESSION['unidadeDeSaude_id']}) ";
            }

            // DO v�nculo do ACS escolhido:
            $sqlAcs = $acs_id ?
              " AND usuario.Acs_id = $acs_id " :
              '';
        }

        // Sexo
        $sqlSexo = ($sexo != 'Ambos') ?
          " AND usuario.sexo = '$sexo' ":
          '';


        // Op��es independentes de "NA" ou "DA":
        $sqlNumeroDaDose = $numeroDaDoseEscolhida ?
                  " AND usuariovacinado.numerodadose = $numeroDaDoseEscolhida ":
                  '';
                  
        $sqlAcamados = $acamados ?
          ' AND usuario.acamado ':
          '';

        // Montagem da SQL COUNT:
        $sql = 'SELECT COUNT( DISTINCT(usuario.id) ) '

             . 'FROM usuariovacinado, usuario '

             . 'WHERE usuariovacinado.Usuario_id = usuario.id '

             // Se o usu�rio escolheu ou n�o cidade:
             .     $sqlCidade

             // Se o usu�rio escolheu ou n�o unidade:
             .     $sqlUnidade

             // Se o usu�rio escolheu ou n�o ACS:
             .     $sqlAcs

             // Se o usu�rio escolheu um sexo ou Ambos:
             .     $sqlSexo

             // Se o usu�rio escolheu ou n�o todas as doses:
             .     $sqlNumeroDaDose

             // Se o usu�rio escolheu ou n�o s� exibir pessoas n�o residentes:
             .     $sqlNaoResidentes

             // Se o usu�rio escolheu ou n�o s� exibir pessoas acamadas:
             .     $sqlAcamados

             // Sele��o obrigat�ria de vacina:
             .     "AND usuariovacinado.Vacina_id = $vacina_id "

             // Por faixa et�ria:
             .     'AND ( (usuariovacinado.idadeano*365) + (usuariovacinado.idademes*30)'
             .          "+ (usuariovacinado.idadedia)) >= $faixaEmDiasInicial "
             .     'AND ( (usuariovacinado.idadeano*365) + (usuariovacinado.idademes*30)'
             .          "+ (usuariovacinado.idadedia)) <= $faixaEmDiasFinal "

             // Por per�odo:
             .     'AND ( DATE(usuariovacinado.datahoravacinacao) '
             .          "BETWEEN '$dataInicial' AND '$dataFinal' ) "

             .     'AND usuario.ativo ';

             Depurador::Pre($sql);

        $stmt = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($totalDeRegistros);
        $stmt->execute();
        $stmt->fetch();
        $stmt->free_result();

        // Montagem da SQL:
        $sql = 'SELECT DISTINCT usuario.nome, usuario.mae, usuario.nascimento '

             . 'FROM usuariovacinado, usuario '

             . 'WHERE usuariovacinado.Usuario_id = usuario.id '

             // Se o usu�rio escolheu ou n�o cidade:
             .     $sqlCidade

             // Se o usu�rio escolheu ou n�o unidade:
             .     $sqlUnidade

             // Se o usu�rio escolheu ou n�o ACS:
             .     $sqlAcs

             // Se o usu�rio escolheu um sexo ou Ambos:
             .     $sqlSexo

             // Se o usu�rio escolheu ou n�o todas as doses:
             .     $sqlNumeroDaDose

             // Se o usu�rio escolheu ou n�o s� exibir pessoas n�o residentes:
             .     $sqlNaoResidentes

             // Se o usu�rio escolheu ou n�o s� exibir pessoas acamadas:
             .     $sqlAcamados

             // Sele��o obrigat�ria de vacina:
             .     "AND usuariovacinado.Vacina_id = $vacina_id "

             // Por faixa et�ria:
             .     'AND ( (usuariovacinado.idadeano*365) + (usuariovacinado.idademes*30)'
             .          "+ (usuariovacinado.idadedia)) >= $faixaEmDiasInicial "
             .     'AND ( (usuariovacinado.idadeano*365) + (usuariovacinado.idademes*30)'
             .          "+ (usuariovacinado.idadedia)) <= $faixaEmDiasFinal "

             // Por per�odo:
             .     'AND ( DATE(usuariovacinado.datahoravacinacao) '
             .          "BETWEEN '$dataInicial' AND '$dataFinal' ) "

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
			if(!$mae) $mae = "<em><span style='color: #CCC'>N�o Informada</span></em>";
			$arr[] = array('nome' => $nome,
                           'm�e' => $mae,
                           'nascimento' => $data->InverterData($nascimento));
		}

		$stmt->free_result();

        Depurador::Pre($sql);

        // Montando o cabe�alho para o n�mero da dose:
        $arrCabecalho = Array();

        // Imprime o cabe�alho com os dados espec�ficos escolhidos pelo usu�rio
        $this->ImprimirCabecalho( $this->CamposParaCabecalhoRelatorio($post, self::$titulo) );

		if ($qtdregistro == 0)
        {
			$this->AdicionarMensagem('Nenhum registro encontrado.');
			return true;
		}
        elseif($qtdregistro < 0)
        {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar listar
				os indiv�duos.');
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
     * Exibe o gr�fico desta classe, por per�odo
     */
	public function ExibirGrafico()
    {
            return;
    }

//------------------------------------------------------------------------------

    /**
     * Recebe duas datas e calcula o n�mero de per�odos �timos para a exibi��o
     * em um gr�fico de barras.
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

        // N�o ser� poss�vel gerar um gr�fico com menos de 7 dias:
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

        // Sen�o, usa o incremento e os dias restantes para o per�odo final:
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
            else          $periodos[$i]['rotulo'] = "at� $diaMesAnoF";
        }

        return $periodos;
    }
//------------------------------------------------------------------------------

    /**
     * Recebe duas datas e calcula o n�mero de per�odos �timos para a exibi��o
     * em um gr�fico de barras.
     *
     * @param String $dataInicial
     * @param String $dataFinal
     */
    private function CalcularBarrasDePeriodo_outraAbordagem($dataInicial, $dataFinal)
    {
        $periodos = array();
        $data = new Data();

        $unidadeParaCalculo = $data->Diferenca($dataFinal, $dataInicial, true);

        if( $unidadeParaCalculo )
        {
            list($valorParaIncremento) = explode(' ', $unidadeParaCalculo);

            // Incrementar em ANOS
            if( strpos($unidadeParaCalculo, 'ano(s)') !== false )
            {
                $unidadeParaCalculo = 'ano';

                for($i=0; $i < $valorParaIncremento; $i++)
                {
                    $periodos[$i]['dataInicio'] = $data->IncrementarData($dataInicial, $i, 'year');
                    $periodos[$i]['dataFim']    = $data->IncrementarData($dataInicial, 1+$i, 'year');
                }

                // Se o per�odo da data final � menor do que o per�odo final passado:
                if( $data->CompararData($periodos[$i-1]['dataFim'], '<', $dataFinal) )
                {
                 // Cria mais um per�odo com menos de um ano:
                 $periodos[$i]['dataInicio'] = $data->IncrementarData($dataInicial, $i, 'year');
                 $periodos[$i]['dataFim']    = $dataFinal;
                }

                // Sen�o...
                else
                {
                 // Reescreve a �ltima data como per�odo final:
                 $periodos[--$i]['dataFim'] = $dataFinal;
                }
            }

            // Incrementar em MESES
            elseif( strpos($unidadeParaCalculo, 'mes(es)') !== false )
            {
                $unidadeParaCalculo = 'mes';

                for($i=0; $i<$valorParaIncremento; $i++)
                {
                    $periodos[$i]['dataInicio'] = $data->IncrementarData($dataInicial, $i, 'month');
                    $periodos[$i]['dataFim']    = $data->IncrementarData($dataInicial, 1+$i, 'month');
                }

                // Se o per�odo da data final � menor do que o per�odo final passado:
                if( $data->CompararData($periodos[$i-1]['dataFim'], '<', $dataFinal) )
                {
                    // Cria mais um per�odo com menos de um ano:
                    $periodos[$i]['dataInicio'] = $data->IncrementarData($dataInicial, $i, 'month');
                    $periodos[$i]['dataFim']    = $dataFinal;
                }

                // Sen�o...
                else
                {
                    // Reescreve a �ltima data como per�odo final:
                    $periodos[--$i]['dataFim'] = $dataFinal;
                }
            }

            // Incrementar em DIAS
            elseif( strpos($unidadeParaCalculo, 'dia(s)') !== false )
            {
                $unidadeParaCalculo = 'dia';

                for($i=$j=0; ; $i++)
                {
                    $periodos[$i]['dataInicio'] = $data->IncrementarData($dataInicial, $j, 'day');
                    $periodos[$i]['dataFim']    = $data->IncrementarData($dataInicial, $j+=5, 'day');

                    if( $data->CompararData($periodos[$i]['dataFim'], '>', $dataFinal) ) break;
                }

                // Reescreve a �ltima data como per�odo final:
                $periodos[$i]['dataFim'] = $dataFinal;
            }
        }

        // Per�odo aceitos ser�o de 2 a 7, para a montagem do gr�fico:
        if( count($periodos) > 1 && count($periodos) < 8 ) return $periodos;

        return false;
    }
//------------------------------------------------------------------------------

}