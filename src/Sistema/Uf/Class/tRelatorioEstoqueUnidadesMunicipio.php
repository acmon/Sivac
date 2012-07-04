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
class RelatorioEstoqueUnidadesMunicipio extends Relatorio
{
//------------------------------------------------------------------------------

    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();

        Depurador::Pre('Classe instanaciada: ' . __CLASS__);
    }

//------------------------------------------------------------------------------

    public function ExibirRelatorio()
    {
        $crip = new Criptografia();
        parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
        
        $this->ImprimirCabecalho(Array('cidade_id'=>$_SESSION['cidade_id'],'titulo'=>'Estoque de vacinas das unidades deste município'));
        $this->ExibirEstoqueDoMunicipio($_SESSION['cidade_id']);

       // echo "<h3>-------->{$_SESSION['cidade_id']}!!! {$crip->Decifrar($_SERVER['QUERY_STRING'])} </h3>";
    }
 //-----------------------------------------------------------------------------
    public function ExibirEstoqueDoMunicipio($unidade_id)
	{
		$unidade_nome = false;
		$cidade_nome = Html::FormatarMaiusculasMinusculas($_SESSION['cidade_nome']);

		if (Sessao::Permissao('UNIDADES_ESTOQUE_VISUALIZAR_ESTADO'))
		{

			$sql = $this->conexao->prepare('SELECT vacina.nome,
				vacinadaunidade.quantidade, unidadedesaude.nome
				FROM `vacina`, `vacinadaunidade`, `unidadedesaude`
				WHERE vacina.id = vacinadaunidade.Vacina_id
				AND vacinadaunidade.UnidadeDeSaude_id = unidadedesaude.id
				AND unidadedesaude.ativo
				AND vacina.ativo  AND pertence = 0
				ORDER BY unidadedesaude.nome, vacina.nome') 
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		  	$sql->bind_result($nome, $quantidade, $unidade_nome);

		} elseif (Sessao::Permissao('UNIDADES_ESTOQUE_VISUALIZAR_MUNICIPIO')) {

			list($cidade_id) = $this->RetornarCidadeDaUnidade($unidade_id);

			$sql = $this->conexao->prepare('SELECT vacina.nome,
				vacinadaunidade.quantidade, unidadedesaude.nome
				FROM `vacina`, `vacinadaunidade`, `unidadedesaude`
				WHERE vacina.id = vacinadaunidade.Vacina_id
				AND vacinadaunidade.UnidadeDeSaude_id = unidadedesaude.id
				AND unidadedesaude.Bairro_id IN (SELECT bairro.id FROM bairro WHERE
								bairro.Cidade_id = ?)
				AND unidadedesaude.ativo
				AND vacina.ativo AND pertence = 0
				ORDER BY unidadedesaude.nome, vacina.nome')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$sql->bind_param('i', $cidade_id);
		  	$sql->bind_result($nome, $quantidade, $unidade_nome);

		} elseif (Sessao::Permissao('UNIDADES_ESTOQUE_VISUALIZAR_UNIDADE'))
		{

			$sql = $this->conexao->prepare('SELECT vacina.nome,
				vacinadaunidade.quantidade
				FROM `vacina`, `vacinadaunidade`
				WHERE vacina.id = vacinadaunidade.Vacina_id
				AND UnidadeDeSaude_id = ?
				AND vacina.ativo  AND pertence = 0
				ORDER BY vacina.nome')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$sql->bind_param('i', $unidade_id);
		  	$sql->bind_result($nome, $quantidade);

		}

	  	$sql->execute();
	  	$sql->store_result();
	  	$existemVacinas = $sql->num_rows;

	  	if($existemVacinas > 0) {

	  		$arr = array();
	  		$i = 0;
			  	while ($sql->fetch()) {

			  		$arr[$i] = array('Unidade de saude' => $unidade_nome,
							 'Vacina' => $nome,
			  				'Quantidade em estoque' => $quantidade);


			  		// Admin nivel 1 não pode ver outras uniades de saude
			  		if ($unidade_nome == false) unset($arr[$i]['Unidade de saude']);

			  		$i++;

			  	}


		  	Html::CriarTabelaDeArray($arr);
           
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
}