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

class RelatorioPessoasVacinadasPelaCampanha extends Relatorio
{
//------------------------------------------------------------------------------
    /**
     * Atributos
     */
     protected static $titulo = 'Indiv�duos vacinados pela campanha';

     protected static $nota = 'Este relat�rio tem por finalidade listar os indiv�duos
                        vacinados pela campanha, podendo o indiv�duo selecionar
                        opcionalmente quaisquer filtros apresentados.
                        <blockquote style="border:none"><strong>Obs.:</strong>
                        <br />Os filtros de cidade, unidade e agente se referem
                        ao local de vacina��o, e n�o ao local de cadastro do
                        indiv�duo</blockquote>';

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

        $numeroDaDoseEscolhida = $acamados = 0;

        $post = $this->UltimoPostParaAjax();

        // Setando os padr�es (caso o usu�rio n�o os informe)
        $cidade_id             = $_SESSION['cidade_id'];
        $unidade_id            = 0;
        $acs_id                = 0;
        $acamados              = 0;
        $faixaInicio           = 0;
        $unidadeInicio         = 'day';
        $faixaFim              = 120;
        $unidadeFim            = 'year';
        $datai                 = '01-01-1900';
        $dataf                 = '31-12-2100';

        // Caso o usu�rio informe, usa as informa��es do formul�rio:
        if( isset($post['campanha_id'])    && (int)$post['campanha_id']     >  0 ) $campanha_id           = $post['campanha_id'];
        if( isset($post['cidade_id'])      && (int)$post['cidade_id']       >  0 ) $cidade_id             = $post['cidade_id'];

        // Obs.: Quando ACS est� junto com Unidade, n�o usamos unidade_id no POST,
        // Usamos "unidade" no POST:
        if( isset($post['unidade'])        && (int)$post['unidade']         >  0 ) $unidade_id            = $post['unidade'];
        if( isset($post['acs'])            && (int)$post['acs']             >  0 ) $acs_id                = $post['acs'];
        if( isset($post['data_inicio'])    && strlen($post['data_inicio'])  == 10) $datai                 = $post['data_inicio'];
        if( isset($post['data_fim'])       && strlen($post['data_fim'])     == 10) $dataf                 = $post['data_fim'];
        if( isset($post['faixaInicio'])    && (int)$post['faixaInicio']     >  0 ) $faixaInicio           = $post['faixaInicio'];
        if( isset($post['unidadeInicio'])  && strlen($post['faixaInicio'])  >  0 ) $unidadeInicio         = $post['unidadeInicio'];
        if( isset($post['faixaFim'])       && (int)$post['faixaFim']        >  0 ) $faixaFim              = $post['faixaFim'];
        if( isset($post['unidadeFim'])     && strlen($post['faixaFim'])     >  0 ) $unidadeFim            = $post['unidadeFim'];
        if( isset($post['acamados'])       &&(Boolean)$post['acamados']   == true) $acamados              = $post['acamados'];

        $faixaEmDiasInicial = $this->ConverterUnidadeDeTempoParaDias($faixaInicio, $unidadeInicio);
        $faixaEmDiasFinal   = $this->ConverterUnidadeDeTempoParaDias($faixaFim, $unidadeFim);

        // Acrescentando a folga (o cara tem 1 ano at� 1 ano, 11 meses e 29 dias):
        $faixaEmDiasFinal   += $this->InserirFolgaDeDias($unidadeFim);

        $data = new Data;

        $dataInicial = $data->InverterData($datai);
		$dataFinal   = $data->InverterData($dataf);

        $sqlCidade = $cidade_id ?
          ' AND usuariovacinadocampanha.UnidadeDeSaude_id IN '
          . '(SELECT unidadedesaude.id FROM unidadedesaude, bairro '
          .     'WHERE unidadedesaude.Bairro_id = bairro.id '
          .     "AND bairro.Cidade_id = $cidade_id) " :
          '';

        $sqlUnidade = $unidade_id ?
          " AND usuariovacinadocampanha.UnidadeDeSaude_id = $unidade_id ":
          '';

        // Se o usu�rio tem um n�vel menor do que o de cidade ele s� pode ver os
        // vacinados na unidade dele!
        if( $_SESSION['nivel'] < 10 )
        {
            $sqlUnidade = ' AND usuariovacinadocampanha.UnidadeDeSaude_id = '
                        . "{$_SESSION['unidadeDeSaude_id']} ";
        }

        $sqlAcamados = $acamados ?
          ' AND usuario.acamado ':
          '';

        $sqlAcs = $acs_id ?
          ' AND usuariovacinadocampanha.UnidadeDeSaude_id IN '
          . "(SELECT UnidadeDeSaude_id FROM acs WHERE acs.id = $acs_id) " :
          '';

        $sql = 'SELECT COUNT( DISTINCT(usuario.id) ) '
        
             . 'FROM usuario, usuariovacinadocampanha '
             . "WHERE usuariovacinadocampanha.Campanha_id = $campanha_id "
             .     'AND usuariovacinadocampanha.Usuario_id = usuario.id '

             // Se escolheu cidade, inclui o filtro de unidade:
             .     $sqlCidade

             // Se escolheu unidade, inclui o filtro de unidade:
             .     $sqlUnidade

             // Se escolheu acs, inclui o filtro de acs:
             .     $sqlAcs

             // Por faixa et�ria:
             .     'AND ( (usuariovacinadocampanha.idadeano*365) + (usuariovacinadocampanha.idademes*30)'
             .          "+ (usuariovacinadocampanha.idadedia)) >= $faixaEmDiasInicial "
             .     'AND ( (usuariovacinadocampanha.idadeano*365) + (usuariovacinadocampanha.idademes*30)'
             .          "+ (usuariovacinadocampanha.idadedia)) <= $faixaEmDiasFinal "

             // Por per�odo:
             .     'AND ( DATE(usuariovacinadocampanha.datahoravacinacao) '
             .          "BETWEEN '$dataInicial' AND '$dataFinal' ) "

             // Inclui todas as pessoas, ou apenas os acamados:
             .     $sqlAcamados

             .     'AND usuario.ativo ';

        Depurador::Pre($sql);

        $stmt = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($totalDeRegistros);
		$stmt->execute();
		$stmt->fetch();
        $stmt->free_result();

        $sql = 'SELECT DISTINCT usuario.nome, usuario.mae, usuario.nascimento '

             . 'FROM usuario, usuariovacinadocampanha '
             . "WHERE usuariovacinadocampanha.Campanha_id = $campanha_id "
             .     'AND usuariovacinadocampanha.Usuario_id = usuario.id '

             // Se escolheu unidade, inclui o filtro de unidade:
             .     $sqlUnidade

             // Se escolheu acs, inclui o filtro de acs:
             .     $sqlAcs

             // Por faixa et�ria:
             .     'AND ( (usuariovacinadocampanha.idadeano*365) + (usuariovacinadocampanha.idademes*30)'
             .          "+ (usuariovacinadocampanha.idadedia)) >= $faixaEmDiasInicial "
             .     'AND ( (usuariovacinadocampanha.idadeano*365) + (usuariovacinadocampanha.idademes*30)'
             .          "+ (usuariovacinadocampanha.idadedia)) <= $faixaEmDiasFinal "

             // Por per�odo:
             .     'AND ( DATE(usuariovacinadocampanha.datahoravacinacao) '
             .          "BETWEEN '$dataInicial' AND '$dataFinal' ) "

             // Inclui todas as pessoas, ou apenas os acamados:
             .     $sqlAcamados

             .     'AND usuario.ativo '
             .     'ORDER BY usuario.nome '
             .     "LIMIT $aPartirDe, " . Html::LIMITE;

		$stmt = $this->conexao->prepare($sql) or
            die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($nome, $mae, $nascimento);
		$stmt->execute();

		$data = new Data;

		while($stmt->fetch()) $arr[] = array('nome' => $nome,
						      'm�e' => $mae,
						      'nascimento' => $data->InverterData($nascimento));

        $stmt->free_result();

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
}