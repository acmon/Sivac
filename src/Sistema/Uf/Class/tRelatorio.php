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
 *
 * @package Sivac/Class
 *
 * @author Maykon Monnerat (maykon_ttd@hotmail.com), v 1.0, 2008-08
 *
 * @copyright 2008 
 *
 */
class Relatorio
{
	
	protected $conexao;
	private $msgDeErro; // Array;
	protected $arquivoGerarIcone;

	const LIMITE = 200;
	//--------------------------------------------------------------------------
	public function __construct()
	{
		date_default_timezone_set('America/Sao_Paulo');

		$this->LocalizarArquivoGeradorDeIcone();
		$this->msgDeErro = array();

        //echo '<script>alert("\n\n\n\nMaykon...\n\n\n\n\n\nQuando chegar, começa o RotinaASerem, pra gente poder testar esses gráficos online, o quanto antes. Não sei como isso vai se comportar online, ehehe")</script>';
	}
	//--------------------------------------------------------------------------
	/**
	 * Destrutor - Fecha a conexão com o banco de dados
	 */
	public function __destruct()
	{
		if( isset($this->conexao) ) $this->conexao->close();
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna um Array com a data início e data fim dentro de um período
     * informado, no qual a data início será a primeira data que alguém foi
     * vacinado, e a data final será a última data no qual alguém foi vacinado
	 *
     * @param String $datai
     * @param String $dataf
     * @param String $tabela "usuariovacinado" ou "usuariovacinadocampanha"
	 * @return Array Com novas posições de datai e dataf
	 */
	public function PeriodoQueTemGenteVacinada($datai, $dataf, $tabela)
	{
        $data = new Data();
        $periodoi = $periodof = false;

        $sql = 'SELECT DATE( MIN(datahoravacinacao) ) as datai, '
             .     'DATE( MAX(datahoravacinacao) ) as dataf '
             . "FROM $tabela "
             . "WHERE DATE(datahoravacinacao) BETWEEN '$datai' AND '$dataf'";

        Depurador::Pre($sql);

		$stmt = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($periodoi, $periodof);
		$stmt->execute();
        $stmt->fetch();
        $stmt->free_result();

        $periodos = array();

        if( $periodoi && $periodof )
        {
            $periodos[] = $periodoi;
            $periodos[] = $periodof;
        }
        else
        {
            $periodos[] = $datai;
            $periodos[] = $dataf;
        }

        return $periodos;
    }
	//--------------------------------------------------------------------------
	/**
	 * Envia o POST para que este método possa montar o array para enviar para
     * a impressão do cabeçalho do relatório.  Usado em todas as classes filha.
	 *
     * @param Array $arrCampos
     * @param String $titulo
	 * @return Array
	 */
	public function CamposParaCabecalhoRelatorio(Array $post, $titulo)
	{
        $arrCabecalho = Array();
        
        $arrCabecalho['titulo'] = $titulo;

        if( isset($post['cidade_id']) && $post['cidade_id'] > 0)
        {
            $arrCabecalho['cidade_id'] = $post['cidade_id'];
        }
        elseif( isset($post['cidade']) && $post['cidade'] > 0)
        {
            $arrCabecalho['cidade_id'] = $post['cidade'];
        }
        if( isset($post['vacina_id']) && $post['vacina_id'] != 0 )
        {
            $arrCabecalho['vacina_id'] = $post['vacina_id'];
        }
        if( isset($post['campanha_id']) && $post['campanha_id'] != 0 )
        {
            $arrCabecalho['campanha_id'] = $post['campanha_id'];
        }
        if( isset($post['unidade_id']) && $post['unidade_id'] != 0 )
        {
            $arrCabecalho['unidade_id'] = $post['unidade_id'];
        }
        elseif( isset($post['unidade']) && $post['unidade'] != 0 )
        {
            $arrCabecalho['unidade_id'] = $post['unidade'];
        }
        if( isset($post['acs_id']) && $post['acs_id'] != 0 )
        {
            $arrCabecalho['acs_id'] = $post['acs_id'];
        }
        if( isset($post['acs']) && $post['acs'] != 0 )
        {
            $arrCabecalho['acs_id'] = $post['acs'];
        }
        if( isset($post['acamados']) && (Boolean)$post['acamados'] == true )
        {
            $arrCabecalho['acamados'] = true;
        }
        if( isset($post['naoResidentes']) && (Boolean)$post['naoResidentes'] == true)
        {
            $arrCabecalho['naoResidentes'] = true;
        }
        if( isset($post['dose_escolhida']) && $post['dose_escolhida'] != 0 )
        {
            $arrCabecalho['numeroDaDoseEscolhida'] = $post['dose_escolhida'] ;
        }
        if( isset($post['faixaInicio']) && $post['faixaInicio'] != '' )
        {
            $arrCabecalho['faixaInicio'] = $post['faixaInicio'];
            $arrCabecalho['unidadeInicio'] = $post['unidadeInicio'];
        }
        if( isset($post['faixaFim']) && $post['faixaFim'] != '' )
        {
            $arrCabecalho['faixaFim'] = $post['faixaFim'];
            $arrCabecalho['unidadeFim'] = $post['unidadeFim'];
        }
        if( isset( $post['data_inicio']) &&  $post['data_inicio'] != '' )
        {
            $arrCabecalho['datai'] = $post['data_inicio'];
        }
        if( isset($post['data_fim']) && $post['data_fim'] != '' )
        {
            $arrCabecalho['dataf'] = $post['data_fim'];
        }
        if( isset($post['sexo']) && strpos(strtolower($post['sexo']), 'amb') === false )
        {
            $arrCabecalho['sexo'] = $post['sexo'];
        }
        
        return $arrCabecalho;
    }

//------------------------------------------------------------------------------

	/**
	 * Imprime um cabeçalho com o nome do orgao e em seguida o nome do
	 * responsável
	 *
	 * @param int $qtdRegistros Quantidade de registros (opcional)
	 *
	 * @return null
	 */
	public function ImprimirCabecalho(Array $arrCampos)
	{
        Depurador::Print_r($arrCampos);

        // Se for internet Explorer, coloca uma div com margem inferior, pois o
        // gráfico fica trepando no cabeçalho:
        if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        echo '<div id="timbradoRelatorioIe7">';

        // Se for outro navegador:
        else echo '<div id="timbradoRelatorio">';
		
		echo '<div id="textoTimbrado">';
		echo "<div id='prefeituraTimbrado'>Prefeitura Municipal de $_SESSION[cidade_nome]</div>";
		echo "<div id='secretariaTimbrado'>Secretaria Municipal de Saúde</div>";
		echo "<div id='orgaoTimbrado'>"
			."Relatório gerado por ". Html::FormatarMaiusculasMinusculas($_SESSION['nome'])
			. "</div>";
		echo '</div>'; // Fecha textoTimbrado
		
		echo '<div id="logoTimbrado">';
		echo "<img src='../Imagens/logoVariacao2.png' border='0'
				alt='Logo Sivac' /><br /><br />";
					
		echo '<div>' . date('d/m/Y H\hi') . '</div>';
        
		echo '</div>'; // Fecha logoTimbrado
		
        echo '<div id="rodapeTimbrado">';

        echo '<div id="textoRodapeTimbrado">';

        // Título do relatório:
        echo "<div id='tituloRelatorioTimbrado'>{$arrCampos['titulo']}</div>";

        // Montando as linhas do texto do relatório:
        if( isset( $arrCampos['cidade_id']) ) echo 'Município: ', $this->RetornarCampoNome('cidade', $arrCampos['cidade_id']), '<br />';
        if( isset( $arrCampos['unidade_id']) ) echo 'Unidade: ',$this->RetornarCampoNome('unidadedesaude', $arrCampos['unidade_id']), '<br />';
        if( isset( $arrCampos['acs_id']) ) echo 'Agente: ', $this->RetornarCampoNome('acs', $arrCampos['acs_id']), '<br />';
        if( isset( $arrCampos['campanha_id']) ) echo $this->RetornarCampoNome('campanha', $arrCampos['campanha_id']), '<br />';
        if( isset( $arrCampos['vacina_id']) ) echo 'Vacina: ', $this->RetornarCampoNome('vacina', $arrCampos['vacina_id']);
        if( isset( $arrCampos['numeroDaDoseEscolhida']) ) echo ' (apenas a dose ', $arrCampos['numeroDaDoseEscolhida'], ')<br />';
        elseif( isset( $arrCampos['vacina_id']) ) echo '<br />'; // Se não imprimir o número da dose, mas tiver vacina
        if( isset( $arrCampos['sexo']) ) echo 'Sexo: ', $arrCampos['sexo'], '<br />';
        if( isset( $arrCampos['faixaInicio']) ) echo 'Idade inicial: ', $arrCampos['faixaInicio'], ' (', $this->ConverteUnidadeDeTempoDeInglesPraPortugues($arrCampos['unidadeInicio']), ')<br />';
        if( isset( $arrCampos['faixaFim']) ) echo 'Idade final: ', $arrCampos['faixaFim'], ' (', $this->ConverteUnidadeDeTempoDeInglesPraPortugues($arrCampos['unidadeFim']), ')<br />';
        if( isset( $arrCampos['datai']) ) echo 'Período inicial: ', $arrCampos['datai'], '<br />';
        if( isset( $arrCampos['dataf']) ) echo 'Período final: ', $arrCampos['dataf'], '<br />';
        if( isset( $arrCampos['acamados']) ) echo '(listando somente indivíduos acamados)', '<br />';
        if( isset( $arrCampos['naoResidentes']) ) echo '(listando somente indivíduos não residentes no município)';

        // Fim da montagem das linhas do texto do relatório.

        echo '</div>';

        echo '</div>';

		echo '</div>'; // Fecha timbradoRelatorio
	}
//	--------------------------------------------------------------------------
	private function LocalizarArquivoGeradorDeIcone()
	{
		if(file_exists("{$_SERVER['DOCUMENT_ROOT']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php")) {

			$this->arquivoGerarIcone =
				"http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php";
        }
	}
	//--------------------------------------------------------------------------
	/**
	 * Inicia a conexão com banco de dados
	 */		
	public function UsarBaseDeDados()
	{
		$this->conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
		$_SESSION['senha']);
		$this->conexao->SELECT_db($_SESSION['banco']);
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
	public function AdicionarMensagem($mensagem)
	{
		$agente = new Vacina();
		$agente->ExibirMensagem($mensagem);
		$agente = null;
	}
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

	//----------------------------------------------------------------------
	public function ConverteUnidadeDeTempoDeInglesPraPortugues($unidade)
	{
		switch($unidade) {
			
			case 'day':   return $postUnidadeInicio = 'dia(s)';
			case 'week':  return $postUnidadeInicio = 'semana(s)';
			case 'month': return $postUnidadeInicio = 'mês(es)';
			case 'year':  return $postUnidadeInicio = 'ano(s)';
			default: return false;
		}	
	}

    //--------------------------------------------------------------------------
    public function SelectVacinas($listarVacinasMae     = false,
                                  $listarVacinasFilhas  = true)
	{

	    echo '<select name="vacina_id" id="vacina_id"
	    style="width:305px;"
		onblur="ValidarCampoSelect(this, \'Vacina\')"
        onchange="IncluirDoseEspecifica(true,
                this.value,
                \'incluirDoseEspecifica\');
                document.getElementById(\'todas_doses\').checked=\'true\'">';
		
	
	    $consulta = 'SELECT id, Grupo_id, nome, pertence
			  		FROM `vacina`m
			  		WHERE ativo
			  		ORDER BY Grupo_id DESC,
			  		nome ASC';
			  	
		$sql = $this->conexao->prepare($consulta)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
		$grupo_id_anterior = 'nenhum';
		$sql->bind_result($id, $grupo_id, $nome, $pertence);
		$sql->execute();

		echo "<option value='0'>- selecione -</option>";
		
        $vacina = new Vacina;
        $vacina->UsarBaseDeDados();

		while ($sql->fetch()) {

            if($listarVacinasMae    == false && $vacina->CarregarVacinasFilhas($id)) continue;
            if($listarVacinasFilhas == false && $pertence) continue;

			if($grupo_id_anterior != $grupo_id) {

				echo "<optgroup label='$grupo_id'>";
				$grupo_id_anterior = $grupo_id;
				
			}
			
			echo "\n<option value='$id'>$nome</option>";
			  		
			if($grupo_id_anterior != $grupo_id) echo '</optgroup>';

		}
		echo '</select>';
		
	}
	//--------------------------------------------------------------------------
	/**
	 * Monta um Select com todos os ACS
	 */
	public function SelectACS()
	{
		echo '<select name="acs" id="acs"
			style="width:305px;"
			onblur="ValidarCampoSelect(this, \'ACS\')">';
			
		
			if( Sessao::Permissao('ACS_LISTAR') == 1 ) {
				$resultado = $this->conexao->prepare('SELECT acs.id, acs.nome FROM
				`acs`,`unidadedesaude` WHERE acs.ativo AND unidadedesaude.ativo
				AND acs.UnidadeDeSaude_id = unidadedesaude.id
				ORDER BY nome')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			}
			elseif( Sessao::Permissao('ACS_LISTAR') == 2 ) {
				
				$resultado = $this->conexao->prepare("SELECT acs.id, acs.nome FROM
					`acs` ,`unidadedesaude` WHERE acs.ativo AND unidadedesaude.ativo AND acs.UnidadeDeSaude_id =
					{$_SESSION['unidadeDeSaude_id']} AND acs.UnidadeDeSaude_id = unidadedesaude.id ORDER BY nome")
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			}
			$resultado->execute();
	
			$resultado->bind_result($id, $nome);
	
			echo "<option value='0'>- selecione -</option>";
			while( $resultado->fetch() ) {
	
				echo "<option value='$id'>".Html::FormatarMaiusculasMinusculas($nome)."</option>";
			}
			$resultado->free_result();
			 
		echo '</select>';
	}
	//--------------------------------------------------------------------------
	/**
	 * Monta um Selct com todas as unidades do sistema
	 */
	public function SelectUnidades($comtag = true, $unidadeSelecionada = false)
	{
		if( $comtag )
		echo '<select name="unidade_id" id="unidade_id"
			style="width:305px;"
			onblur="ValidarCampoSelect(this, \'Unidade de Saúde\')">';
		
		if( Sessao::Permissao('RELATORIOS_UNIDADE') == 3 ) {
			
			$unidade = $this->conexao->prepare("SELECT unidadedesaude.id, unidadedesaude.nome FROM
			`unidadedesaude` WHERE unidadedesaude.id = {$_SESSION['unidadeDeSaude_id']} AND unidadedesaude.ativo")
			 or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		} elseif( Sessao::Permissao('RELATORIOS_UNIDADE') == 2 ) {
			
			$unidade = $this->conexao->prepare("SELECT unidadedesaude.id, unidadedesaude.nome FROM
			`unidadedesaude` , `bairro` WHERE bairro.id = unidadedesaude.Bairro_id  
			 AND bairro.Cidade_id = {$_SESSION['cidade_id']} AND unidadedesaude.ativo ORDER BY nome")
			 or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		} elseif( Sessao::Permissao('RELATORIOS_UNIDADE') == 1 ) {
			
			$unidade = $this->conexao->prepare("SELECT unidadedesaude.id, unidadedesaude.nome 
			FROM `unidadedesaude`, `bairro`, `cidade` WHERE unidadedesaude.ativo 
			AND bairro.id = unidadedesaude.Bairro_id AND bairro.Cidade_id = cidade.id
			AND cidade.Estado_id = '{$_SESSION['estado_banco']}' ORDER BY unidadedesaude.nome")
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		} 
	
			//$unidade = $this->conexao->prepare('SELECT id, nome FROM `unidadedesaude` ORDER BY nome')
			//or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			$unidade->bind_result($unidade_id, $unidade_nome);
			$unidade->execute();
			
			$marcado = 0;
			
			echo "<option value='0'>- selecione -</option>";	
			while ($unidade->fetch()) {
				
				if( $unidadeSelecionada !== false && $unidadeSelecionada == $unidade_id ) $marcado = ' selected="true"';
				else $marcado = '';
				echo "<option value='$unidade_id' $marcado>".Html::FormatarMaiusculasMinusculas($unidade_nome)."</option>";
			}
			$unidade->free_result();
		
		if( $comtag )
		echo '</select>';
	}	
	
	//--------------------------------------------------------------------------
	public function SelectCampanha()
	{
        $campanhaRecente_id = $campanhaRecente_nome = false;

		echo '<select name="campanha_id" id="campanha_id"
			style="width:305px;"
			onblur="ValidarCampoSelect(this, \'Campanha\')">';
            
            // Selecionando a campanha mais recente:
            $sql = 'SELECT id, nome '
                 . 'FROM `campanha` '
                 . 'WHERE ativo '
                 . 'ORDER BY id DESC '
                 . 'LIMIT 1';

			$stmt = $this->conexao->prepare($sql)
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$stmt->bind_result($campanhaRecente_id, $campanhaRecente_nome);
			$stmt->execute();
            $stmt->fetch();
            $stmt->free_result();
            
            if( $campanhaRecente_id === false )
            {
                echo '</select>';
                return;
            }

            $campanha_id = $campanha_nome = 0;
            
            // Selecionando as outras campanhas:
            $sql = 'SELECT id, nome '
                 . 'FROM `campanha` '
                 . 'WHERE ativo '
                 . "AND id <> $campanhaRecente_id "
                 . 'ORDER BY datainicio ASC';

			$stmt = $this->conexao->prepare($sql)
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$stmt->bind_result($campanha_id, $campanha_nome);
			$stmt->execute();
            $stmt->store_result();

            echo "<option value='$campanhaRecente_id'>$campanhaRecente_nome</option>";

            if( $stmt->num_rows > 0 )
            {
                echo '<optgroup label="Outras campanhas:">';

                while ( $stmt->fetch() ) {
                    echo "<option value='$campanha_id'>"
                    . Html::FormatarMaiusculasMinusculas($campanha_nome)
                    . "</option>";
                }
                echo '</optgroup>';
            }
            
			$stmt->free_result();
		
		echo '</select>';
	}	
	//--------------------------------------------------------------------------
	/**
	 * Monta um Selct com todos os estados do Brasil
	 */
	public function SelectEstados()
	{
		// name está diferente de id porque o ajax precisa de uma id chamada
		// "cidade", enquanto que o relatório precisa de um post que seja cidade_id
		echo '<select name="estado_id" id="estado"
			style="width:305px;"
			onchange="PesquisarCidades(this.value)"
			onblur="ValidarCampoSelect(this, \'Estado\')">';
			$estado = $this->conexao->prepare('SELECT id, nome FROM `estado` ORDER BY nome')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$estado->bind_result($estado_id, $estado_nome);
			$estado->execute();
			
			echo "<option value='0'>- selecione -</option>";
			while ($estado->fetch()) {
				
				$selecionado = '';
				if($_SESSION['estado_id'] == $estado_id) {

					$selecionado = "selected='true'";
				}
				echo "<option value='$estado_id' $selecionado>$estado_nome</option>";
			}
			$estado->free_result();
		echo '</select>';
	}
	//--------------------------------------------------------------------------
	/**
	 * Monta um select com todas as cidades do estado que o usuário está logado
	 */
	public function SelectCidades($idDaDiv = 'cidade_id')
	{
		// name está diferente de id porque o ajax precisa de uma id chamada
		// "cidade", enquanto que o relatório precisa de um post que seja cidade_id
		echo '<select name="' . $idDaDiv . '" id="' . $idDaDiv . '"
			style="width:305px;"
			onchange="PesquisarUnidades(this.value, \'unidade\')"
			onblur="ValidarCampoSelect(this, \'Cidade\')">';

            $estado_id = $_SESSION['estado_banco'];

            $sql = 'SELECT id, nome '
                 . 'FROM cidade '
                 . "WHERE Estado_id = '$estado_id' ";

			$stmt = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$stmt->bind_result($cidade_id, $cidade_nome);
			$stmt->execute();

			echo "<option value='0'>- selecione -</option>";

            if( $_SESSION['nivel'] < 100 )
            {
                $cidade_id = $_SESSION['cidade_id'];
                $cidade_nome = $_SESSION['cidade_nome'];
                
                echo "<option value='$cidade_id' selected='true'>$cidade_nome</option>";
            }
            else
            {
                while ($stmt->fetch()) {

                    $selecionado = '';
                    if($_SESSION['cidade_id'] == $cidade_id) {

                        $selecionado = "selected='true'";
                    }
                    echo "<option value='$cidade_id' $selecionado>$cidade_nome</option>";
                }
            }
            
			$stmt->free_result();
            
		echo '</select>';
	}

//------------------------------------------------------------------------------

    /**
     * Exibe um SELECT HTML com a opção de rotina e os nomes das campanhas
     *
     * @param int $campanha_id
     */
	public function SelectCampanhaComRotina($campanha_id = false)
	{

	    echo '<select name="campanha_id" id="campanha_id"
	    style="width:305px;"
		onblur="ValidarCampoSelect(this, \'Campanha\')"
        onchange="ListarVacinasDaCampanha(\'vacinasSelecionadas\', this.value)">';

	    $consulta = 'SELECT id, nome
			  		FROM `campanha`
			  		WHERE ativo
			  		ORDER BY datainicio';

		$sql = $this->conexao->prepare($consulta)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$sql->bind_result($id,  $nome);
		$sql->execute();

		echo "<option value='semCampanha'>Rotina</option>";

		while ($sql->fetch()) {


			$selected = false;
			if($campanha_id == $id) $selected = " selected='true' ";
			echo "\n<option value='$id' $selected>"
            . Html::FormatarMaiusculasMinusculas($nome) . "</option>";

		}
		echo '</select>';

	}
    //----------------------------------------------------------------------
	public function SelectVacinasDaCampanha($campanha_id)
	{
        if($campanha_id == 'semCampanha') {
            $this->SelectVacinas();
            return;
        }

	    echo '<select name="vacina_id" id="vacina_id"
	    style="width:305px;"
		onblur="ValidarCampoSelect(this, \'Vacina da Campanha\')">';

	    $consulta = "SELECT vacinadacampanha.Vacina_id, vacina.nome
			  		FROM `vacinadacampanha` , vacina
			  		WHERE vacina.id = vacinadacampanha.Vacina_id
                    AND vacinadacampanha.Campanha_id = '$campanha_id'
                    AND ativo
			  		ORDER BY nome";

		$sql = $this->conexao->prepare($consulta)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$sql->bind_result($id,  $nome);
		$sql->execute();

		echo "<option value='0'>- selecione -</option>";

		while ($sql->fetch()) {


			$selected = false;
			if($campanha_id == $id) $selected = " selected='true' ";
			echo "\n<option value='$id' $selected>$nome</option>";

		}
		echo '</select>';

	}

	//--------------------------------------------------------------------------

	public function ListarVacinasEspeciais($usuario_id)
	{
		$sql = "SELECT vacina.id
			    FROM `usuariovacinado`, `vacina`, `usuario`
				WHERE usuariovacinado.Vacina_id = vacina.id
				AND usuario.id = ? AND usuario.ativo";
				
						
		$stmt = $this->conexao->prepare($sql)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		$stmt->bind_param('i', $usuario_id);
		$stmt->bind_result($id);
		$stmt->execute();
		
		while($linha = $stmt->fetch()) $arr[] = $id;
		$stmt->free_result();
		
		return $arr;
	}
	//--------------------------------------------------------------------------
	public function NotaRelatorios($tipo)
	{
        echo $this->Nota();
	}
	//--------------------------------------------------------------------------
    /**
     * Exibe uma lista com os relatórios do sistema, categorizados
     *
     * @param String $subtipo
     */
	public function ListarRelatorios($subtipo = 'geral')
	{
		
		$crip = new Criptografia();
		
		if ( ( $subtipo == 'geral' ) || ( $subtipo == 'individuo' ) ) {
			
			echo '<fieldset  style="background-color: #f6efff"><legend>Indivíduo</legend><ul>';
		
			$end = $crip->Cifrar("pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioCadernetaDeVacinacao");

            
			echo "<li class='listaRelatorio'><a href='?$end'>"
                 . RelatorioCadernetaDeVacinacao::$titulo
                 . '</a></li>';
            /*

             NÃO FOI TERMINADO (DESNECESSARIO)

			$end = $crip->Cifrar("pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioIndividuosInativados");
           

            
			echo "<li class='listaRelatorio'><a href='?$end'>"
                 . RelatorioIndividuosInativados::$titulo
                 . '</a></li>';
			*/
			//$end = $crip->Cifrar("pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ListarVacinasComDosesVencer");
		 
			echo '</ul></fieldset><br />';
		}
				
        if ( ( $subtipo == 'geral' ) || ( $subtipo == 'rotina' ) ) {
                
			echo '<fieldset style="background-color: #fffeef"><legend>Rotina</legend><ul>';
			
			$end = $crip->Cifrar("pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioRotinaVacinados");
			
			echo "<li class='listaRelatorio'><a href='?$end'>"
                 . RelatorioRotinaVacinados::$titulo
                 . '</a></li>';
			
			$end = $crip->Cifrar("pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioRotinaASeremVacinados");

			echo "<li class='listaRelatorio'><a href='?$end'>"
                 . RelatorioRotinaASeremVacinados::$titulo
                 . '</a></li>';

            echo '</ul></fieldset><br />';
           }

		if ( ( $subtipo == 'geral' ) || ( $subtipo == 'campanha' ) ) {
			echo '<fieldset style="background-color: #effffb"><legend>Campanha</legend><ul>';
			
			//$end = $crip->Cifrar("pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioPessoasVacinadasPelaCampanha");
			$end = $crip->Cifrar("pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioCampanhaVacinados");
			
			echo "<li class='listaRelatorio'><a href='?$end'>"
                 . RelatorioPessoasVacinadasPelaCampanha::$titulo
                 . '</a></li>';
			
			$end = $crip->Cifrar("pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioCampanhaNaoVacinados");

			echo "<li class='listaRelatorio'><a href='?$end'>"
                 . RelatorioCampanhaNaoVacinados::$titulo
                 . '</a></li>';

            echo '</ul></fieldset><br />';
		}
        
        if ( ( $subtipo == 'geral' ) || ( $subtipo == 'intercorrencia' ) ) {
			echo '<fieldset style="background-color: #fff7ef"><legend>Evento Adverso</legend><ul>';
			
			$end = $crip->Cifrar("pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioEventosAdversos");
			
			echo "<li class='listaRelatorio'><a href='?$end'>"
                 . RelatorioEventosAdversos::$titulo
                 . '</a></li>';

			echo '</ul></fieldset><br />';
		}
		
		echo '</ul>';
			
	}
	
//------------------------------------------------------------------------------

    /**
     * Método para listar pessoas que foram buscadas via AJAX
     * (p.ex. em RelatorioCadernetaDeVacina), usado em diversos locais, dentro
     * de uma DIV, para que a busca não efetue um POST.
     *
     * @param String $pesquisa
     * @param String $mae
     * @param String $tipoRelatorio
     * @param String $datai
     * @param String $dataf
     * @param int $cidade_id
     * @param int $unidade
     * @param int $acs
     * @param String $cpf
     * @param int $pagina_atual
     * @return Boolean
     */
    public function ListarPessoa($pesquisa = 0, $mae = 0, $tipoRelatorio = 0, $datai = 0,
			$dataf = 0, $cidade_id = 0, $unidade = 0, $acs = 0, $cpf = 0, $pagina_atual = false)
		{
            $html = new Html;
            $nomeDaSessao = "paginacao_{$tipoRelatorio}";
            
            // No onclick da busca, a sessão fica setada como false, para refazer
            // a busca da página 1 (e não da página que parou na busca anterior)
            if($pagina_atual == false) $_SESSION[$nomeDaSessao] = 1;
                
            $pagina_atual = $html->TratarPaginaAtual($pagina_atual, $nomeDaSessao);
            
            $aPartirDe = ($pagina_atual - 1) * Html::LIMITE;
                
            ////////////////////////////////////////////////////////////////////////
			
				$nome = $this->conexao->real_escape_string(trim($pesquisa));
	
				$explodeCaracteres = explode(' ',$nome);
				$implodeCaracteres = implode('%',$explodeCaracteres);
		
				$nome = "$implodeCaracteres%";
				
				$campos = ' usuario.id, usuario.nome, usuario.nascimento, usuario.mae ';
				$tabelas = ' `usuario` ';
				
				if ($pesquisa) $condicoes= " usuario.nome LIKE '$nome' AND usuario.ativo ";
				else $condicoes= " usuario.ativo ";
				
				
				$explodeCaracteres = explode(' ',$mae);
				$implodeCaracteres = implode('%',$explodeCaracteres);
		
				$nomeMae = "%$implodeCaracteres%";
				
				if($mae) {
					
					$condicoes .= " AND usuario.mae LIKE '$nomeMae'";
				}
				
				
				if($cidade_id) {
					
					$tabelas .= ', `bairro`';
					$condicoes .= " AND bairro.Cidade_id = $cidade_id
									AND usuario.Bairro_id = bairro.id 
									AND bairro.ativo ";
				}
				
				if($unidade) {
					
					$tabelas .= ', `acs`';
					$condicoes .= " AND acs.UnidadeDeSaude_id = $unidade 
									AND usuario.Acs_id = acs.id 
									AND acs.ativo ";
				}
		
				elseif($acs) {
					$tabelas .= ', `acs`';
					$condicoes .= " AND usuario.Acs_id = $acs
									AND usuario.Acs_id = acs.id 
									AND acs.ativo ";
				}
				
				if($cpf) {
					$condicoes .= " AND usuario.cpf = $cpf ";
				}
						
				
				if($datai && $dataf) {
					
					$data = new Data();

					$tabelas .= ", `usuariovacinado` , `intervalodadose`";
					
					$datai = $data->InverterData($datai);
					$dataf = $data->InverterData($dataf);
					
					$condicoes .= "AND usuario.id = usuariovacinado.Usuario_id
					AND intervalodadose.Vacina_id = usuariovacinado.Vacina_id
					AND intervalodadose.numerodadose = usuariovacinado.numerodadose
					
					AND usuariovacinado.numerodadose = (SELECT COUNT( id ) FROM `usuariovacinado`
					WHERE Vacina_id = intervalodadose.Vacina_id AND usuariovacinado.Usuario_id = usuario.id)
					
					AND usuariovacinado.numerodadose < (SELECT COUNT( id ) FROM `intervalodadose`
					WHERE usuariovacinado.Vacina_id = intervalodadose.Vacina_id)
					
					AND DATE_ADD( DATE( datahoravacinacao ) , INTERVAL 
					        (SELECT intervalodadose.diaidealparavacinar  FROM `intervalodadose` WHERE 
					         intervalodadose.Vacina_id = usuariovacinado.Vacina_id 
					         AND intervalodadose.numerodadose = usuariovacinado.numerodadose+1) 
					         +
					         (SELECT intervalodadose.atrasomaximo FROM `intervalodadose` WHERE 
					         intervalodadose.Vacina_id = usuariovacinado.Vacina_id   
					         AND intervalodadose.numerodadose = usuariovacinado.numerodadose+1) 
					DAY) BETWEEN '$datai' AND '$dataf' ";
				}		 
				
				$limite = Html::LIMITE;
                
                ////////////////////////////////////////////////////////////////
                
                $sqlCount = "SELECT COUNT(usuario.id) FROM $tabelas WHERE $condicoes";
                $resultado = $this->conexao->prepare($sqlCount); 
                $resultado->bind_result($totalDeRegistros);
                $resultado->execute();
                $resultado->fetch();
                $resultado->free_result();
                
                ////////////////////////////////////////////////////////////////
                
				$sql = "SELECT $campos FROM $tabelas WHERE $condicoes ORDER BY usuario.nome LIMIT $aPartirDe, $limite";
                
				$data = new Data();
				
				$datai = $data->InverterData($datai);
				$dataf = $data->InverterData($dataf);
			

				$resultado = $this->conexao->prepare($sql);
        
                $resultado->bind_result($id, $nome, $nascimento, $maeConsulta);
        
                $resultado->execute();
        
                $resultado->store_result();
        
                $linhas = $resultado->num_rows;
                
                if ($linhas > 0) {
	
                    $arr = array();
                    
                    $crip = new Criptografia();
                    
                                                    
                    while( $resultado->fetch() ) {
                        
                        $nascimento = $data->InverterData($nascimento);
                        
                        $end = $crip->Cifrar("pagina=exibirRelatorioPop&tipo=$tipoRelatorio&usuario_id=$id");
                        if($datai && $dataf) $end = $crip->Cifrar("pagina=exibirRelatorioPop&tipo=$tipoRelatorio&usuario_id=$id&datai=$datai&dataf=$dataf");
                        
                        if($tipoRelatorio == 'RelatorioCadernetaDeVacinacao') $end = $crip->Cifrar("pagina=listarVacinasParaCaderneta&tipo=$tipoRelatorio&usuario_id=$id");
                        
                        if(!$maeConsulta) $maeConsulta = "<em><span style='color: #CCC'>Não Informada</span></em>";
                        $arr[] = array('id'=>$id, 'nome'=>"<a href='Rel/?$end' target='_blank' >$nome</a>",
                                        'mãe'=>$maeConsulta,'nascimento'=>$nascimento);
				}
	
 				$qtd = count($arr);
				
				if($qtd > 0) {
                    
                    ////////////////////////////////////////////////////////////////////
                    $html->ControleDePaginacao($totalDeRegistros, $nomeDaSessao,
                                    'Relatorio',
                                    "ListarPessoa($pesquisa, $mae, $tipoRelatorio, $datai, $dataf, $cidade_id, $unidade, $acs, $cpf, [paginaAtual])");
                    
                    Html::ExibirInformacoesDeRegistrosEncontrados($totalDeRegistros);
                    ////////////////////////////////////////////////////////////////////            
					Html::CriarTabelaDeArray($arr);
                    
				}
									
				$resultado->free_result();
                
				return $linhas;
            
			}
					
			$resultado->free_result();
            
			if($linhas == 0) {
				
				$this->AdicionarMensagem('Sua busca não retornou resultado');
				return $linhas;
			}
			
			if($linhas < 0) {
				
				$this->AdicionarMensagemDeErro("Algum erro ocorreu ao tentar buscar
					dados com o critério $pesquisa!");
					
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function RetornarCampoNome($tabela, $id)
	{
		
		$sql = "SELECT nome FROM `$tabela` WHERE id = $id";

        $b = $this->conexao->query($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$campo = $b->fetch_assoc(); $nome = $campo['nome'];
		
		$b->free_result();

		return Html::FormatarMaiusculasMinusculas($nome);
	}
	
	//--------------------------------------------------------------------------
	
	public function RetornarCampoMae($id, $tabela = 'usuario')
	{
		
		$sql = "SELECT $tabela.mae FROM `$tabela` WHERE id = $id";
		
		$b = $this->conexao->query($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		//$b->bind_result($nome);
		
		//$b->execute();
		
		$campo = $b->fetch_assoc(); $nome = $campo['mae'];
		
		$b->free_result();
		
		if(!$nome) $nome = "<em><span style='color: #CCC'>Não Informada</span></em>";
		return Html::FormatarMaiusculasMinusculas($nome);
	}
	
	//--------------------------------------------------------------------------

	public function LimparLogDeAcessosAntigos()
	{
		$sql = '';
		
		$log = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));	
	}
	//--------------------------------------------------------------------------
	/**
	 * Este método...
	 *
	 */
	public function MontarLogDeAcessos()
	{
		$sql = 'SELECT id, login, nivel, datahoraconexao, datahoradesconexao,
					navegador, ip, conectado,
					TIMEDIFF(NOW(), datahoradesconexao) AS diferencaDeTempo
					FROM `usuarioconectado`
					ORDER BY id DESC
					LIMIT 500';
		
		$log = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$log->bind_result($id, $login, $nivel, $datahoraconexao, $datahoradesconexao,
			$navegador, $ip, $conectado, $diferencaDeTempo);
		
		$log->execute();
		
		$data = new Data();
		
		$arrConectados = array();
		$arrDesconectados = array();
		$arrNaoDesconectaram = array();
		
		while ($log->fetch()) {
            
            list($hora, $minutos) = explode(':',$diferencaDeTempo);

           // if( $minutos <= 5  && $hora == 0) echo "<br> --> $id, $login, $nivel, $datahoraconexao, $datahoradesconexao,
			//$navegador, $ip, conectado $conectado, minutos $minutos";

			list($dataConexao, $horaConexao) = explode(' ', $datahoraconexao); 
			
            list($dataDesconexao, $horaDesconexao) = explode(' ', $datahoradesconexao);
			list($horaDesconexao, $minDesconexao) = explode(':', $horaDesconexao);
						
			$dataConexao = $data->InverterData($dataConexao);
            
			$dataDesconexao = $data->InverterData($dataDesconexao);
			
			list($horaConexao, $minConexao) = explode(':', $horaConexao);

            if($conectado){

               // echo "<br>Conectado $conectado hora $hora minuto $minutos, ";

				// Se o usuário está realmente conectado:
				if( $minutos <= 5  && $hora == 0) {
                    
					$arrConectados[] = array('login' => "[$login]", 
							'nivel'=> $nivel, 
							'data de entrada' => "$dataConexao $horaConexao:$minConexao", 
							'navegador' => $navegador, 
							'IP' => $ip);
					//echo '<br>', count($arrConectados);
					continue;
				}
				
				// Se o status do usuário está como conectado no banco, porém
				// ele fechou a janela sem desconectar (não está realmente
				// conectado, porém não desconectou clicando em desconectar)
				else {

					
					$arrNaoDesconectaram[] = array('login' => "[$login]", 
							'nivel'=> $nivel, 
							'data de entrada' => "$dataConexao $horaConexao:$minDesconexao", 
							'fechou a janela em' => "$dataDesconexao $horaDesconexao:$minDesconexao", 
							'navegador' => $navegador, 
							'IP' => $ip,
							'duração' => $data->IntervaloDeTempo($datahoradesconexao, $datahoraconexao));
					
					continue;
				}
                
			}
			
			// Se não é conectado nem esqueceu conectado, então lista o log:
			$arrDesconectados[] = array('login' => "[$login]", 
							'nivel'=> $nivel, 
							'data de entrada' => "$dataConexao $horaConexao:$minConexao", 
							'data de saída' => "$dataDesconexao $horaDesconexao:$minDesconexao", 
							'navegador' => $navegador, 
							'IP' => $ip,
							'duração' => $data->IntervaloDeTempo($datahoradesconexao, $datahoraconexao));
		
		}
		
		$qtdConectados = count($arrConectados);
		
		if(count($arrConectados)) {
			
			echo "<h4>Usuários online <small>($qtdConectados)</small></h4>";
						
			if($qtdConectados > 10) echo '<div style="margin: auto; width: 680px;
				height: 200px; overflow: auto;">';
			
			Html::CriarTabelaDeArray($arrConectados);
			
			if($qtdConectados > 10) echo '</div>';
			
			echo '<hr />';
		}
		
		$qtdRegistros = count($arrDesconectados);
		
		if($qtdRegistros) {
			
			echo '<h4>Registro de conexões ',
			($qtdRegistros > 4)?'<small>(máx. 500 últimos)</small></h4>': '</h4>';
			
			
			if($qtdRegistros > 10) echo '<div style="margin: auto; width: 680px;
				height: 200px; overflow: auto;">';
			
			Html::CriarTabelaDeArray($arrDesconectados);
			
			if($qtdRegistros > 10) echo '</div>';
		}
		
		$qtdRegistrosNaoDesconectaram = count($arrNaoDesconectaram);
		
		if($qtdRegistrosNaoDesconectaram) {
			
			echo "<hr /><h4>Usuários que fecharam a janela sem desconectar
				<small>($qtdRegistrosNaoDesconectaram)</small></h4>";
			
			if($qtdRegistrosNaoDesconectaram > 10) echo '<div style="margin: auto; width: 680px;
				height: 200px; overflow: auto;">';
			
			Html::CriarTabelaDeArray($arrNaoDesconectaram);
			
			if($qtdRegistrosNaoDesconectaram > 10) echo '</div>';
		}
	}
	//----------------------------------------------------------------------
    public function InserirFolgaDeDias($unidadeDeTempo)
    {

        switch($unidadeDeTempo) {

			case 'day':
				 return 1;

			case 'week':
				return 6;

			case 'month':
				return 29;

			case 'year':
				return 364;

            default: return 0;
		}
        
    }
//------------------------------------------------------------------------------
    public function RetornarDataFimDaCampanha($campanhaId)
    {
        $sql  = "SELECT datafinal FROM campanha WHERE id = $campanhaId";
        $stmt = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        $data = false;
        $stmt->bind_result($data);
		$stmt->execute();
        $stmt->fetch();
        $stmt->free_result();

        return $data;

    }
    //--------------------------------------------------------------------------
    public function IncrementaDiasEmUmaDataAPartirDaUnidade($dataPassada, $unidade)
    {
        $data = new Data();
        
        switch($unidade) {

			case 'day':
				 $dataIncrementada = $data->IncrementarData($dataPassada, 1, 'day');
				break;

			case 'week':
				 $dataIncrementada = $data->IncrementarData($dataPassada, 6, 'day');
				break;

			case 'month':
				 $dataIncrementada = $data->IncrementarData($dataPassada, 29, 'day');
				break;

			case 'year':
				 $dataIncrementada = $data->IncrementarData($dataPassada, 364, 'day');
				break;
		}

        return $dataIncrementada;
    }
    //--------------------------------------------------------------------------
    public function RetornarUnidadeDeTempo($dias)
    {
        if     (($dias%365) == 0) return 'year';
        elseif (($dias%30)  == 0) return 'month';
        elseif (($dias%7)   == 0) return 'week';
        else                      return 'year';
        
    }
    //--------------------------------------------------------------------------
    /**
     * @param String $faixa
     * @param String $unidadeDeTempo
     * 
     * Acrescentando para o esquema de + 11 meses e 29 dias (considerando que um
     * ano bisexto tem 366 dias e é preciso ainda menos um dia, usar 365. O mesmo
     * vale para outras unidades de tempo, mas neste caso são exatas - 1 dia:
     */
    private function InserirFolgaDeFaixaEtaria($faixa, $unidadeDeTempo)
    {
        $data = new Data();
        
        switch($unidadeDeTempo) {

            case 'year':

                // Separa o ano da data (recebida no formado YYYY/MM/DD):
                $anoDaFaixa = strtok($faixa, '/-.');

                // Se o ano anterior ao da faixa for ano bisexto, subtrai de 366
                // da faixa:
                if( $this->AnoBisexto($anoDaFaixa) ) {

                    return $data->DecrementarData($faixa, 365);
                }

                // Se não for ano bisexto, subtrai 364
                else {

                    return $data->DecrementarData($faixa, 364);
                }

            case 'month':


                // Separa o ano da data (recebida no formado YYYY/MM/DD):
                $anoDaFaixa = strtok($faixa, '/-.');

                // Pegando o mes da faixa:
                $mesDaFaixa = (int)strtok('/-.');
             
                $meses = array(1  => 31,
                               2  => 28 + (int)$this->AnoBisexto($anoDaFaixa),
                               3  => 31,
                               4  => 30,
                               5  => 31,
                               6  => 30,
                               7  => 31,
                               8  => 31,
                               9  => 30,
                               10 => 31,
                               11 => 30,
                               12 => 31);

                return $data->DecrementarData($faixa, $meses[$mesDaFaixa] - 1);

            case 'week':

                return $data->DecrementarData($faixa, 6);


            default:
                return $faixa;
        }
    }
    //--------------------------------------------------------------------------
    /**
     * @param int $ano
     * @return bool
     *
     * Verifica se um ano é ou não bisexto:
     */
    private function AnoBisexto($ano)
    {
        if( ($ano % 4 == 0 && $ano % 100 != 0) || $ano % 400 == 0 ) {

            return true;
        }
        return false;
    }
    //--------------------------------------------------------------------------
    public function ConverterUnidadeDeTempoParaDias($qtdTempo, $unidadeTempo)
    {
        switch($unidadeTempo) {

			case 'day':   return $qtdTempo;
			case 'week':  return $qtdTempo * 7;
			case 'month': return $qtdTempo * 30;
			case 'year':  return $qtdTempo * 365;
			default: return 1;
		}
    }
    //--------------------------------------------------------------------------
    /**
     * Retorna o ultimo POST do formulario.  Necessario retornar atraves deste
     * metodo, pois o Ajax descarta o POST do PHP, e se o mesmo nao existir,
     * sera usado o POST guardado na sessao.
     *
     * @return Array
     */
    protected function UltimoPostParaAjax()
    {
        if( !isset($_SESSION['ultimoPost']) ) $_SESSION['ultimoPost'] = array();

        if( isset($_POST) && count($_POST) > 0 ) $_SESSION['ultimoPost'] = $_POST;

        return $_SESSION['ultimoPost'];
    }

    //--------------------------------------------------------------------------

    /**
     * Retorna o ultimo POST do formulario.  Necessario retornar atraves deste
     * metodo, pois o Ajax descarta o POST do PHP, e se o mesmo nao existir,
     * sera usado o POST guardado na sessao.
     *
     * @return Array
     */
    protected function ControleDePaginacao($totalDeRegistros, $class)
    {
        $html = new Html;

        $html->ControleDePaginacao($totalDeRegistros, 'paginacao_relatorio',
                        $class,
                        "ExibirRelatorio([paginaAtual])",
                        '300px');

        Html::ExibirInformacoesDeRegistrosEncontrados($totalDeRegistros);
    }
    //--------------------------------------------------------------------------

}
