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


//require('tAgenteImunizador.php');

/**
 * Pessoa: Classe mãe que representa uma pessoa.
 *
 * Esta classe trata de uma pessoa vacinada ou não. Seta, Recupera, Lista , Edita ,
 * Exibe, Seleciona e Valida uma pessoa ou seus dados. Por aqui pode-se acessar
 * todos os dados pessoais de uma  determinada pessoa e usá-los em outras classes
 * ou arquivos que necessitem dessas informações.
 *
 *
 * @package Sivac/Class
 *
 * @author Maykon Monnerat maykon_ttd@hotmail.com, v 1.0, 2008-10-27
 *
 * @copyright 2008 
 *
 */
abstract class Pessoa
{
	protected $nome;				//String
	protected $sexo;				//String (F ou M)
	protected $nascimento;			//String
	protected $telefone;			//String
	protected $email;				//String
	protected $cpf;					//String
	protected $mae;					//String
	protected $pai;					//String
	protected $etnia;				//String
	protected $estado;				//String
	protected $bairro;              //String
	protected $acs;					//String
	protected $cartaoSus;			//String
	protected $prontuario;          //String
	protected $ddd;                 //String
	protected $cidade;              //String
	protected $cep;                 //String
	protected $profissao;           //String
	protected $logradouro;          //String
	protected $acamado;				//boolean
	protected $vacinavel;			//boolean
	
	protected $msgDeErro;			//Array
	protected $conexao;				//Objeto

    
	
	const LIMITE = 2; // Constante para tamanho de página
		
	protected $arquivoGerarIcone;	// String com o local e nome do gerarIcone.php
	
	//////////////////////// CONSTRUTOR / DESTRUTOR ////////////////////////////

	public function Forzao()
	{
		foreach($this as $chave => $parametro) {
			
			echo "<li>$chave => $parametro</li>";
		}
	}
	//--------------------------------------------------------------------------
	public function __construct()
	{
		$this->msgDeErro = array();
		$this->LocalizarArquivoGeradorDeIcone();
	}
	//--------------------------------------------------------------------------
	public function __destruct()
	{
		if( isset($this->conexao) ) $this->conexao->close();
	}
	//--------------------------------------------------------------------------
	private function LocalizarArquivoGeradorDeIcone()
	{
		if(file_exists("{$_SERVER['DOCUMENT_ROOT']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php")) {
			
			$this->arquivoGerarIcone =
				"http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php";
        }
	}
	
	///////////////////////////////// SETAR ///////////////////////////////////
	public function BuscarDadosDaPessoa($id = 1)
	{
		/*
		$camarada = $this->conexao->prepare('SELECT etnia_id, nome, sexo, nascimento FROM `usuario` WHERE id = ? AND ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$camarada->bind_param('i', $id);

		$camarada->bind_result($etnia_id, $nome, $sexo, $nascimento);

		$camarada->execute();

		$camarada->fetch();

		$this->SetarSexo($sexo);
		$this->SetarNome($nome);
		$this->SetarNascimento($nascimento);

		$camarada->free_result();

		$etnia = $this->BuscarEtniaDaPessoa($etnia_id);
		$this->SetarEtnia($etnia);
		$estado = $this->BuscarEstadoDaPessoa(1);
		$this->SetarEstado($estado);
		*/

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
    //--------------------------------------------------------------------------
	public function CriarInformacao($mensagem,
                                     $tituloDaJanela = 'Dica',
                                     $eventoDeVisibilidade = 'onclick'
                                     )
	{
        // Container para a barra de título e o corpo da mensagem
		echo '<div class="containerInformacao" id="containerInformacao"
                        style="visibility: \'hidden\'">';

                // Barra de título:
		echo '<div class="tituloInformacao" id="tituloInformacao" title="Fechar" '
                        . $eventoDeVisibilidade
                        . '="document.getElementById(\'containerInformacao\').style.visibility = \'hidden\';'
                        . 'document.getElementById(\'tituloInformacao\').style.visibility = \'hidden\';'
                        . 'document.getElementById(\'corpoInformacao\').style.visibility = \'hidden\';">'
                        . '&nbsp;'
                        . $tituloDaJanela
                        . '</div>';

                // Corpo da mensagem:
		echo '<div class="corpoInformacao" id="corpoInformacao">', $mensagem, '</div>';

                // Fechando o container:
		echo '</div>';
	}
    //--------------------------------------------------------------------------
    /**
     * @param array $ids
     *
     * Exibe as divs que estão escondidas, recebendo um array com estas ids.
     * Geralmente é um container com uma barra de título com um evento de fechar
     * a janela e uma mensagem.
     */
	public function ExibirInformacao(Array $ids)
	{
        // Ebibe as divs de informação:
        foreach($ids as $id) {

            echo "document.getElementById('$id').style.visibility = 'visible';";
        }
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
	public function BuscarEtniaDaPessoa($etnia_id)
	{
		/*
		$Etniacamarada = $this->conexao->prepare('SELECT nome FROM `etnia` WHERE id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$Etniacamarada->bind_param('i', $etnia_id);

		$Etniacamarada->bind_result($nome);

		$Etniacamarada->execute();

		$Etniacamarada->fetch();

		$Etniacamarada->free_result();

		return $nome;
		*/
	}
	//--------------------------------------------------------------------------
	public function BuscarEstadoDaCidade($cidade_id) {

		$cidade = $this->conexao->prepare('SELECT estado_id FROM `cidade` WHERE
			cidade.id = ?') or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$cidade->bind_param('i', $cidade_id);
		$cidade->bind_result($estadoSigla);
		$cidade->execute();
		$cidade->fetch();
		$cidade->free_result();

		return $estadoSigla;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o nascimento de uma pessoa.
	 *
	 * @return string
	 */
	public function Nascimento($usuario_id)
    {
        $nascimento = false;
        
		$sql = 'SELECT nascimento '
             . 'FROM `usuario` '
             . 'WHERE id = ? ';

        $cidade = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$cidade->bind_param('i', $usuario_id);
		$cidade->bind_result($nascimento);
		$cidade->execute();
		$cidade->fetch();
		$cidade->free_result();

		return $nascimento;
	}
	//--------------------------------------------------------------------------
	public function BuscarNomeDaPessoa($usuario_id)
	{
		$stmt = $this->conexao->prepare('SELECT nome FROM `usuario` WHERE usuario.id = ?')
						or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
						
		$stmt->bind_param('i', $usuario_id);
		$stmt->bind_result($usuario_nome);
		$stmt->execute();
		$stmt->fetch();
		$stmt->free_result();

		return $usuario_nome;	
	}
	//--------------------------------------------------------------------------
	public function BuscarEstadoDaPessoa($endereco_id)
	{
		/*
		$Estadocamarada = $this->conexao->prepare('SELECT cidade.estado_id
			  FROM `bairro`, `cidade`, `endereco`
			  WHERE bairro.id = endereco.bairro_id
			  AND   cidade.id = bairro.cidade_id
			  AND   endereco.id = ?
			  AND bairro.ativo')
			  or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$Estadocamarada->bind_param('i', $endereco_id);

		$Estadocamarada->bind_result($estado_id);

		$Estadocamarada->execute();

		$Estadocamarada->fetch();

		return $estado_id;
		*/
	}
	//--------------------------------------------------------------------------
	public function BuscarCidadeDaPessoa($endereco_id)
	{
		/*
		$Cidadecamarada = $this->conexao->prepare('SELECT cidade.id
		  FROM `bairro`, `cidade`, `endereco`
		  WHERE bairro.id = endereco.bairro_id
		  AND   cidade.id = bairro.cidade_id
		  AND   endereco.id = ? AND bairro.ativo')
		  or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$Cidadecamarada->bind_param('i', $endereco_id);

		$Cidadecamarada->bind_result($cidade_id);

		$Cidadecamarada->execute();

		$Cidadecamarada->fetch();

		return $cidade_id;
		*/
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o ACS.
	 *
	 * @param string $nome  nome do acs
	 */

	public function SetarACS($acs)
	{
		$this->acs = $acs;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o ACS.
	 *
	 * @param string $nome  nome do acs
	 */

	public function SetarBairro($bairro)
	{
		$this->bairro = $bairro;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o numero do cartão SUS.
	 *
	 * @param string $nome  numero do cartão
	 */

	public function SetarCartaoSus($cartaoSus)
	{
		$this->cartaoSus = $cartaoSus;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o nome de uma pessoa.
	 *
	 * @param string $nome nome da pessoa
	 */

	public function SetarNome($nome)
	{
		$this->nome = $nome;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o sexo de determinada pessoa.
	 *
	 * @param string $sexo sexo da pessoa (F ou M)
	 */
	public function SetarSexo($sexo)
	{
		$this->sexo = $sexo;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o a data de nascimento de determinada pessoa.
	 *
	 * @param string $nascimento data de nascimento
	 */
	public function SetarNascimento($nascimento)
	{
		$this->nascimento = $nascimento;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o telefone de uma pessoa.
	 *
	 * @param string $telefone telefone (xxxx-xxxx)
	 */
	public function SetarTelefone($telefone)
	{
		$this->telefone = $telefone;
	}

	//--------------------------------------------------------------------------
	/**
	 * Seta o e-mail de determinada vacina.
	 *
	 * @param string $email e-mail (xxxx@xxxx.xxx)
	 */
	public function SetarEmail($email)
	{
		$this->email = $email;
	}

	//--------------------------------------------------------------------------
	/**
	 * Seta o CPF de determinada pessoa.
	 *
	 * @param string $cpf CPF (xxx.xxx.xxx-xx)
	 */
	public function SetarCpf($cpf)
	{
		$this->cpf = $cpf;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o nome da mãe de determinada pessoa.
	 *
	 * @param unknown_type $mae nome da mãe
	 */
	public function SetarMae($mae)
	{
		$this->mae = $mae;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta a etnia de uma pessoa.
	 *
	 * @param string $etnia
	 */
	public function SetarEtnia($etnia)
	{
		$this->etnia = $etnia;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o Estado UF de uma pessoa.
	 *
	 * @param string $estado
	 */
	public  function SetarEstado($estado)
	{
		$this->estado = $estado;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o nome do pai de uma pessoa.
	 *
	 * @param string $pai nome do pai
	 */
	public function SetarPai($pai)
	{
		$this->pai = $pai;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o numero de prontuario de uma pessoa.
	 *
	 * @param string $prontuario numero do prontuario
	 */
	public function SetarProntuario($prontuario)
	{
		$this->prontuario = $prontuario;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o numero do ddd de uma pessoa.
	 *
	 * @param string $ddd numero do ddd
	 */
	public function SetarDdd($ddd)
	{
		$this->ddd = $ddd;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o nome do cidade de uma pessoa.
	 *
	 * @param string $cidade nome da cidade
	 */
	public function SetarCidade($cidade)
	{
		$this->cidade = $cidade;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o numero do cep de uma pessoa.
	 *
	 * @param string $cep numero do cep
	 */
	public function SetarCep($cep)
	{
		$this->cep = $cep;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o nome da profissão de uma pessoa.
	 *
	 * @param string $pprofissao nome da profissao
	 */
	public function SetarProfissao($profissao)
	{
		$this->profissao = $profissao;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta o logradouro da pessoa.
	 *
	 * @param string $logradouro nome do logradouro
	 */
	public function SetarLogradouro($logradouro)
	{
		$this->logradouro = $logradouro;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta se o usuário está acamado.
	 *
	 * @param boolean $acamado
	 */
	public function SetarAcamado($acamado)
	{
		$this->acamado = $acamado;
	}
	//--------------------------------------------------------------------------
	/**
	 * Seta se o usuário está acamado.
	 *
	 * @param boolean $acamado
	 */
	public function SetarVacinavel($vacinavel)
	{
		$this->vacinavel = $vacinavel;
	}
	//--------------------------------------------------------------------------
	public function SetarDados($post)
	{

		$clean = Preparacao::GerarArrayLimpo($post, $this->conexao);

		if(isset($clean['nome'])) $this->SetarNome(Html::FormatarMaiusculasMinusculas($clean['nome']));
		if(isset($clean['etnia'])) $this->SetarEtnia($clean['etnia']);
		if(isset($clean['prontuario'])) $this->SetarProntuario($clean['prontuario']);
		if(isset($clean['sexo'])) $this->SetarSexo($clean['sexo']);
		if(isset($clean['datadenasc'])) $this->SetarNascimento($clean['datadenasc']);
		if(isset($clean['telefone'])) $this->SetarTelefone($clean['telefone']);
		if(isset($clean['cartaosus'])) $this->SetarCartaoSus($clean['cartaosus']);
		if(isset($clean['cpf'])) $this->SetarCpf($clean['cpf']);
		if(isset($clean['ddd'])) $this->SetarDdd($clean['ddd']);
		if(isset($clean['estadouf'])) $this->SetarEstado($clean['estadouf']);
		if(isset($clean['endereco'])) $this->SetarLogradouro($clean['endereco']);
		if(isset($clean['cidade'])) $this->SetarCidade($clean['cidade']);
		if(isset($clean['bairro'])) $this->SetarBairro($clean['bairro']);
		if(isset($clean['cep'])) $this->SetarCep($clean['cep']);
		if(isset($clean['profissao'])) $this->SetarProfissao($clean['profissao']);
		if(isset($clean['nomedamae'])) $this->SetarMae($clean['nomedamae']);
		if(isset($clean['nomedopai'])) $this->SetarPai($clean['nomedopai']);
		if(isset($clean['acs'])) $this->SetarACS($clean['acs']);
		if(isset($clean['email'])) $this->SetarEmail($clean['email']);
		if(isset($clean['acamado'])) $this->SetarAcamado($clean['acamado']);
		
        if(isset($clean['vacinavel'])) $this->SetarVacinavel($clean['vacinavel']);
        else                           $this->SetarVacinavel(1);
	}
	//-------------------------------------------------------------------------
	//////////////////////////////// RETORNAR /////////////////////////////////

	//--------------------------------------------------------------------------
	/**
	 * Retorna o e-mail da pessoa.
	 *
	 * @return string
	 */
	public function Email()
	{
		return $this->email;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o nome da pessoa.
	 *
	 * @return string
	 */
	public function Etnia()
	{
		return $this->etnia;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o Estado UF de uma pessoa.
	 *
	 * @return string
	 */
	public function  Estado()
	{
		return $this->estado;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o Logradouro.
	 *
	 * @return string
	 */

	public function Logradouro()
	{
		return $this->logradouro;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o ACS.
	 *
	 * @return string
	 */

	public function ACS()
	{
		return $this->acs;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o Bairro.
	 *
	 * @return string
	 */

	public function Bairro()
	{
		return $this->bairro;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o numero do cartão SUS.
	 *
	 * @return string
	 */

	public function CartaoSus()
	{
		return $this->cartaoSus;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna a etnia;
	 *
	 * @return string
	 */

	public function Nome()
	{
		return $this->nome;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o sexo de determinada pessoa.
	 *
	 * @return string
	 */
	public function Sexo()
	{
		return $this->sexo;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o telefone de uma pessoa.
	 *
	 * @return string
	 */
	public function Telefone()
	{
		return $this->telefone;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o CPF de uma pessoa
	 *
	 * @return string
	 */
	public function Cpf()
	{
		return $this->cpf;
	}

	//--------------------------------------------------------------------------
	/**
	 * Retorna o nome da mãe de determinada pessoa.
	 *
	 * @return string
	 */
	public function Mae()
	{
		return $this->mae;
	}

	//--------------------------------------------------------------------------
	/**
	 * Retorna o nome do pai de determinada pessoa.
	 *
	 * @return string
	 */
	public function Pai()
	{
		return $this->pai;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o numero do prontuario de determinada pessoa.
	 *
	 * @return string
	 */
	public function Prontuario()
	{
		return $this->prontuario;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o numero do ddd de determinada pessoa.
	 *
	 * @return string
	 */
	public function Ddd()
	{
		return $this->ddd;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o nome da cidade de determinada pessoa.
	 *
	 * @return string
	 */
	public function Cidade()
	{
		return $this->cidade;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o numero do cep de determinada pessoa.
	 *
	 * @return string
	 */
	public function Cep()
	{
		return $this->cep;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna o nome da profissao de determinada pessoa.
	 *
	 * @return string
	 */
	public function Profissao()
	{
		return $this->profissao;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna se a pessoa está de cama.
	 *
	 * @return boolean
	 */
	public function Acamado()
	{
		return $this->acamado;
	}
	//--------------------------------------------------------------------------
	/**
	 * Retorna se a pessoa foi inativada (com motivo e data, gravada na
     * tabela "usuarioinativado").
	 *
	 * @return boolean
	 */
	public function Vacinavel()
	{
		return $this->vacinavel;
	}
	//--------------------------------------------------------------------------
	//////////////////////////////// INSERIR //////////////////////////////////

	private  function InserirBairro()
	{	
		$cidade = $this->Cidade();
		$bairro = $this->Bairro();

		$inserir = $this->conexao->prepare('INSERT INTO `bairro`(id, Cidade_id,
			nome, ativo) VALUES (NULL, ?, ?, 1)')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		
		$inserir->bind_param('is', $cidade, $bairro);

		$inserir->execute();

		if($inserir->affected_rows) {

			return $this->conexao->insert_id;

		}
		return false;
	}

	//--------------------------------------------------------------------------
	/**
	 * Insere uma nova pessoa.
	 *
	 */
	public function InserirPessoa()
	{

		$bairro_id = $this->VerificarSeBairroExiste();

			if(!$bairro_id )
				$bairro_id = $this->InserirBairro();

		// Com a id do endereço recuperada, é só inserir um usuário:
		$inserir = $this->conexao->prepare("INSERT INTO `usuario` (id, Etnia_id,
			Ddd_id, logradouro, cep, Bairro_id, Ocupacao_cbo_id, Acs_id, nome,
			prontuario, sexo, nascimento, telefone, cartaosus, cpf, email, mae,
			pai, ativo, acamado) VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
			?, ?, ?, 1, ?)") or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$data = new Data();

		$etnia      = $this->Etnia();
		$ddd        = $this->Ddd();
		$profissao  = $this->Profissao();
		$acs_id     = $this->Acs();
		$nome       = $this->Nome();
		$prontuario = $this->Prontuario();
		$sexo       = $this->Sexo();
		$nascimento = $data->InverterData($this->nascimento);
		$telefone   = Preparacao::RemoverSimbolos($this->Telefone());
		$cartaoSus  = $this->CartaoSus();
		$cpf        = Preparacao::RemoverSimbolos($this->Cpf());
		$mae        = $this->Mae();
		$pai        = $this->Pai();
		$logradouro = $this->Logradouro();
		$email		= $this->Email();
		$cep 		= Preparacao::RemoverSimbolos($this->Cep());
		$acamado	= $this->Acamado();

		$inserir->bind_param('isssisissssssssssi',$etnia, $ddd, $logradouro, $cep,
			$bairro_id, $profissao, $acs_id, $nome, $prontuario, $sexo,
			$nascimento, $telefone, $cartaoSus, $cpf, $email, $mae, $pai, $acamado);			
		
		$inserir->execute();
		
		$id_inserida = $inserir->insert_id;
/*
		echo("$etnia, $ddd, $logradouro, $cep,
					$profissao, $acs_id, $nome, $prontuario, $sexo, $nascimento,
					$telefone, $cartaoSus, $bairro_id, $cpf, $email, $mae, $pai");
*/
		
		
		$sucesso = $inserir->affected_rows;

		if($sucesso > 0) {
			
			return $id_inserida;
		}
		
		return false;
	}

	///////////////////////////////// EDITAR //////////////////////////////////
	/**
	 * Edita os dados de determinada pessoa.
	 *
	 */
	public function EditarPessoa($id)
	{
		$bairro_id = $this->VerificarSeBairroExiste();

		if(!$bairro_id ) $bairro_id = $this->InserirBairro();
		
		$data = new Data();

		$etnia      = $this->Etnia();
		$ddd        = $this->Ddd();
		$profissao  = $this->Profissao();
		$acs_id     = $this->Acs();
		$nome       = $this->Nome();
		$prontuario = $this->Prontuario();
		$sexo       = $this->Sexo();
		$nascimento = $data->InverterData($this->nascimento);
		$telefone   = Preparacao::RemoverSimbolos($this->Telefone());
		$cartaoSus  = $this->CartaoSus();
		$cpf        = Preparacao::RemoverSimbolos($this->Cpf());
		$mae        = $this->Mae();
		$pai        = $this->Pai();
		$logradouro = $this->Logradouro();
		$email		= $this->Email();
		$cep 		= Preparacao::RemoverSimbolos($this->Cep());
		$acamado	= $this->Acamado();
		$vacinavel	= $this->Vacinavel();

        $sql = 'UPDATE `usuario` '

             . 'SET Etnia_id = ?, Ddd_id = ?, logradouro = ?, cep = ?, '
             .     'Ocupacao_cbo_id = ?, Acs_id = ?, nome = ?, prontuario = ?, '
             .     'sexo = ?, nascimento = ?, telefone = ?, cartaosus = ?, '
		     .     'Bairro_id = ?, cpf = ?, email = ?, mae = ?, pai = ?, '
             .     'ativo = 1, acamado = ?, vacinavel = ? '

             .         'WHERE id = ?';

		$atualizar = $this->conexao->prepare($sql);

		$atualizar->bind_param('issssissssssissssiii',$etnia, $ddd, $logradouro,
                $cep, $profissao, $acs_id, $nome, $prontuario, $sexo,
                $nascimento, $telefone, $cartaoSus, $bairro_id, $cpf, $email,
                $mae, $pai, $acamado, $vacinavel, $id);

		$atualizar->execute();

		$atualizar->store_result();

		$sucesso = $atualizar->affected_rows;
	
		if($sucesso > 0) {

            // Apaga os dados da sessão, para que atualize a busca quando voltar
            unset($_SESSION['listarPessoa']['arr']);
			return true;
		}
		if($sucesso == 0) {

			$this->AdicionarMensagemDeErro("Atualização não efetuada. Nenhuma
				alteração parece ter sido feita para $nome.");
				
			return false;
		}
		if($sucesso < 0) {
			
			$this->AdicionarMensagemDeErro("Algum erro ocorreu ao atualizar os
				dados de $nome.");
				
			return false;
		}
	}

	///////////////////////////////// EXCLUIR /////////////////////////////////
	/**
	 * Exclui uma pessoa.
	 *
	 */
	public function ExcluirPessoa($id)
	{

		$excluir = $this->conexao->prepare("UPDATE `usuario` SET `ativo`= 0
			WHERE `id`= ?") or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$excluir->bind_param('i',$id);

		$excluir->execute();

		$excluiu = $excluir->affected_rows;

		$excluir->close();

		if ($excluiu)
        {
            // Apaga os dados da sessão, para que atualize a busca quando voltar
            unset($_SESSION['listarPessoa']['arr']);
			return true;
		}

		return false;
	}
	///////////////////////////////// EXIBIR //////////////////////////////////

	public function ExibirFormularioEditarPessoa($id)
	{

		$dados = $this->SelecionarDadosUsuario($id);

		list($etnia_id, $ddd_id, $bairro_id, $ocupacao_id,
			$acs_id, $nome, $prontuario, $sexo, $nascimento,
			$email, $telefone, $logradouro, $cep, $cartaosus,
			$cpf, $mae, $pai, $unidadeDeSaude_id, $acamado,
            $vacinavel) = $dados;

		$crip = new Criptografia();

		$desc = $crip->Decifrar($_SERVER['QUERY_STRING']);

		$end = $crip->Cifrar("$desc&id=$id");

		?>
		<h3 align="center">Alterar dados do Indivíduo</h3>
		<form id="formulario" name="formulario" method="post"
		action="?<?php echo $end; ?>"
			onsubmit="return (ValidarNome(this.nome, 'nome')
					&& ValidarSexo(this.sexo)
					&& ValidarAcamado(this.acamado)
					&& ValidarCampoSelect(this.etnia, 'etnia')
					&& ValidarData(this.datadenasc)
					&& ValidarCampoSelect(this.ddd, 'DDD', true)
					&& ValidarTelLocal(this.telefone, true)
					&& ValidarCpf(this.cpf, true)
					&& ValidarEmail(this.email, true)
					&& ValidarCampoSelect(this.estadouf, 'estado')
					&& ValidarNome(this.endereco, 'logradouro')
					&& ValidarCampoSelect(this.cidade, 'cidade')
					&& ValidarNome(this.bairro, 'bairro')
					&& ValidarCep(this.cep, true)
					&& ValidarCartaoSus(this.cartaosus, true)
					&& ValidarProntuario(this.prontuario, true)
					&& ValidarCampoSelect(this.profissao, 'profissão')
					&& ValidarCampoSelect(this.unidade, 'unidade de saúde')
					&& ValidarCampoSelect(this.acs, 'agente comunitário de saúde (ACS)')
					&& ValidarNome(this.nomedamae, 'nome da mãe')
					&& ValidarNome(this.nomedopai, 'nome do pai', true) )">

			<p>

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Nome:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="nome" id=nome value="<?php
						if ( isset( $_POST['nome'] ) ) {
							echo Html::FormatarMaiusculasMinusculas($_POST['nome']);
						} else {
							echo Html::FormatarMaiusculasMinusculas($nome);
						}
						?>" maxlength="100"
						style="width:300px;" onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this); ValidarNome(this);
						FormatarNome(this, event)"
					/>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Sexo:
				</div>
				<div class='CadastroDir'>
					<?php

						if($sexo == 'F') {
							$selecionadof = 'checked="true"';
							$selecionadom = '';
						}
						else {
							$selecionadom = 'checked="true"';
							$selecionadof = '';
						}

					if ( isset( $_POST['sexo'] ) ) {
						if ( $_POST['sexo'] == 'F' ) {
							$selecionadof = 'checked="true"';
							$selecionadom = '';
						} else {
							$selecionadom = 'checked="true"';
							$selecionadof = '';
						}
					}
					?>
					<input type='radio' name='sexo' id='sexo' <?php echo $selecionadof ?>
					value='F' onblur='ValidarSexo(this)' />Feminino
					<input type='radio' name='sexo' id='sexo' <?php echo $selecionadom ?>
					value='M' onblur='ValidarSexo(this)' />Masculino

				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Etnia:
				</div>
				<div class='CadastroDir'>
					<select name="etnia" id="etnia" style="width:305px; margin-left:2px;"
					onblur="ValidarCampoSelect(this, 'etnia', false)">
						<option value=""></option>
						<?php $this->SelecionarEtnia($etnia_id);	?>
					</select>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Data de Nascimento:
				</div>
				<div class='CadastroDir'>
				<?php

				$data = new Data();

				?>
					<input type="text" name="datadenasc" id="datadenasc" value="<?php
						echo $data->InverterData($nascimento); ?>"
						maxlength="10" <?php echo @$desabilitarCampo; ?>
						onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					    onkeyup="return Mascara('DATA', this, event);"
						onblur="ValidarData(this)"/>
					<span id="TextoExemplos">
						<?php if(@$desabilitarCampo == '') {

							echo 'Ex.: 01/01/1980';
						}
						else {
							echo 'A data não pode ser alterada!';
						}
						?>
					</span>
				</div>

				<!-- ############################################################### -->

				<br />
				<div class='CadastroEsq'>
					DDD:
				</div>
				<div class='CadastroDir'>
					<select name="ddd" id="ddd" style="width:50px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'DDD', true)"
						onchange="document.formulario.telefone.focus()">
						<option value=""></option>
						<?php
							$this->SelecionarDdd($ddd_id);
						?>
					</select>
					Telefone: <input type="text" name="telefone" id="telefone"
						value="<?php if($telefone != '' && $telefone != '00000000') {
							echo Preparacao::InserirSimbolos($telefone, 'TEL');
						} ?>" maxlength="9" onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('TELLOCAL', this, event);"
						onblur="ValidarTelLocal(this, true)"/>
					<span id="TextoExemplos">
						<?php echo " Ex.: 2222-2222 " ?>
					</span>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					CPF:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="cpf" id="cpf" value="<?php if($cpf != 0 ) {
					echo Preparacao::InserirSimbolos($cpf, 'CPF'); }?>"
						maxlength="14" onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('CPF',this,event);"
						onblur="ValidarCpf(this, true);"
					/>
					<span id="TextoExemplos">
						<?php echo " Ex.: 999.999.999-99 " ?>
					</span>
				</div>
				<br />

				<!-- ############################################################### -->
				<div class='CadastroEsq'>
					E-mail:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="email" id="email" value="<?php if(isset($email)) {
					echo $email; }?>"	maxlength="70" size="40" 
						onkeydown="Mascara('EMAIL',this,event);"
						onblur="ValidarEmail(this, true);" />
				</div>
				<br />

				<!-- ############################################################### -->
				<?php

				$dados_endereco = $this->SelecionarEstadoCidade($id);

				list($estado_id, $cidade_id) = $dados_endereco;

				?>
				<div class='CadastroEsq'>
					*Estado UF:
				</div>
				<div class='CadastroDir'>
					<select name="estadouf" id="estadouf" style="width:305px; margin-left:2px;"
					onblur="ValidarCampoSelect(this, 'estado', false)"
					onchange="PesquisarCidades( this.value, document.formulario.cidade.value );
							document.formulario.cidade.value='';
							document.formulario.bairro.value='';
							document.formulario.endereco.value='';
							document.formulario.cep.value='';">

						<option value="0">- selecione -</option>
						<?php
							$this->SelecionarEstado($estado_id);
						?>
			  		</select>
		  		</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Cidade:
				</div>
				<div class='CadastroDir'>
					<select name="cidade" id="cidade" style="width:305px; margin-left:2px;"
					onblur="ValidarCampoSelect(this, 'cidade', false)"
					onfocus="PesquisarCidades( document.formulario.estadouf.value, <?php
																	echo $cidade_id; ?>)"
							onchange="document.formulario.bairro.value='';
							document.formulario.endereco.value='';
							document.formulario.cep.value='';">
					</select>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Logradouro:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="endereco" id="endereco"
					value="<?php

					list($logradouro, $bairro_id) = $this->SelecionarEnderecoBairroId($id);
					echo Html::FormatarMaiusculasMinusculas($logradouro);

					?>" maxlength="100" style="width:300px;"

						onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this);
						ValidarNome(this, 'logradouro'); FormatarNome(this, event)"/>
				</div>
				<br />


				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Bairro:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="bairro"  id="bairro" value="<?php
					
					$bairro = $this->SelecionarBairro($id);
					$cep = $this->SelecionarCep($id);

					echo Html::FormatarMaiusculasMinusculas($bairro);

					?>" maxlength="50"
						onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this);
						ValidarNome(this, 'bairro'); FormatarNome(this, event)"/>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					CEP:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="cep"  id="cep"
						value="<?php echo Preparacao::InserirSimbolos($cep,'CEP');
						?>" maxlength="9" onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('CEP',this,event);"
						onblur="ValidarCep(this, true)"
					/>
				</div>
				<br />

				<!-- ############################################################### -->
				<div class='CadastroEsq'>
					Cartão SUS:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="cartaosus"  id="cartaosus" maxlength="15"
					value="<?php echo $cartaosus; ?>" size="20"
					onkeypress="return Digitos(event, this);"
						onblur="ValidarCartaoSus(this, true)"
					/>
				</div>
				<br />

				<!-- ############################################################### -->
				<div class='CadastroEsq'>
					Prontuário:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="prontuario" id="prontuario" maxlength="10"
					value="<?php if($prontuario != 0) { echo $prontuario;} ?>" size="15"
					onkeypress="return Digitos(event, this);"
						onblur="ValidarProntuario(this, true)"
					/>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					Profissão:
				</div>
				<div class='CadastroDir'>
					<select name="profissao" id="profissao" style="width:305px;
					margin-left:2px;" onblur="ValidarCampoSelect(this, 'profissão', false)">
						<option value="1">NÃO INFORMADO</option>
						<?php

						$this->SelecionarOcupacao($ocupacao_id);

						?>
					</select>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Nome da mãe:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="nomedamae" id="nomedamae" maxlength="100"
						value="<?php echo Html::FormatarMaiusculasMinusculas($mae) ?>" style="width:300px;"
						onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this); ValidarNome(this, 'nome da mãe');
						FormatarNome(this, event)"
					/>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					Nome do pai:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="nomedopai" id="nomedopai" maxlength="100"
					value="<?php echo Html::FormatarMaiusculasMinusculas($pai) ?>" style="width:300px;"
						onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this); ValidarNome(this, 'nome do pai', true);
						FormatarNome(this, event)"
					/>
				</div>
				<br />

				<!-- ############################################################### -->
				
				<div class='CadastroEsq'>
					*Unidade de Saúde:
				</div>
				<div class='CadastroDir'>
					<select name="unidade" id="unidade" style="width:305px;
					margin-left:2px;"
                    onblur="ValidarCampoSelect(this, 'unidade de saúde', false)"
					onchange="PesquisarAcs(this.value)">
						<option value="0">- selecione -</option>
						<?php
													
						if ( isset( $_POST['unidade'] ) && $_POST['unidade'] != '' ) {
							$this->SelectsUnidades( $_POST['unidade'] );
							if ( isset( $_POST['acs'] ) && $_POST['acs'] != '' ) {
								echo "<script>PesquisarAcs(document.getElementById( 'unidade' ).value, {$_POST['acs']})</script>";
							} else {
								echo "<script>PesquisarAcs(document.getElementById( 'unidade' ).value)</script>";
							}
						}
						else {
							$this->SelectsUnidades($unidadeDeSaude_id);
						}
						?>
			  		</select>
		  		</div>
				<br />
				
				<!-- ############################################################### -->
				
				<div class='CadastroEsq'>
					*Agente Comunitário de Saúde:
				</div>
				<div class='CadastroDir'>
					<select name="acs" id="acs" style="width:305px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'agente comunitário de saúde (ACS)')">
					</select>
				</div>
				<br />
				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					Acamado:
				</div>
				<div class='CadastroDir'>
					<?php
						if($acamado == 1) {
							$selecionados = 'checked="true"';
							$selecionadon = '';
						}
						else {
							$selecionadon = 'checked="true"';
							$selecionados = '';
						}

					if ( isset( $_POST['acamado'] ) ) {

						if ( $_POST['acamado'] == 1 ) {
							$selecionados = 'checked="true"';
							$selecionadon = '';
						} else {
							$selecionadon = 'checked="true"';
							$selecionados = '';
						}
					}
					?>
					<input type='radio' name='acamado'  id='acamado' <?php echo $selecionados ?>
					value='1' onblur='ValidarAcamado(this)' />Sim
					<input type='radio' name='acamado'  id='acamado'<?php echo $selecionadon ?>
					value='0' onblur='ValidarAcamado(this)' />Não

				</div>
				<br />

				<!-- ############################################################### -->

                <?php
                // Só pode exibir o "Ativo para vacinar" se $vacinavel for 0,
                // pois o usuário só poderá desativar a pessoa com motivo,
                // clicando em "Excluir" no DataGrid, e informando um motivo
                if(!$vacinavel)
                {

                ?>
				<div class='CadastroEsq'>
					Ativo para vacinar:
				</div>
				<div class='CadastroDir'>
					<?php
						if($vacinavel == 1) {
							$selecionados = 'checked="true"';
							$selecionadon = '';
						}
						else {
							$selecionadon = 'checked="true"';
							$selecionados = '';
						}

					if ( isset( $_POST['vacinavel'] ) ) {

						if ( $_POST['vacinavel'] == 1 ) {
							$selecionados = 'checked="true"';
							$selecionadon = '';
						} else {
							$selecionadon = 'checked="true"';
							$selecionados = '';
						}
					}
					?>
					<input type='radio' name='vacinavel'  id='vacinavel' <?php echo $selecionados ?>
					value='1' onblur='ValidarAcamado(this)' />Sim
					<input type='radio' name='vacinavel'  id='vacinavel'<?php echo $selecionadon ?>
					value='0' onblur='ValidarAcamado(this)' />Não

				</div>
				<br />
                <?php
                }
                ?>
				<!-- ############################################################### -->

					<!--<input type="submit" name="cadastrar" value="     Editar     " />
					<input type="reset" name="apagar" value="   Apagar   " />-->
				<?php

					$botao = new Vacina();
					$botao->ExibirBotoesDoFormulario('Confirmar');
				
				   

				?>

		 	</p>
		</form>
		<script>
			PesquisarCidadesEAcs('<?php echo $estado_id?>',
								<?php echo $cidade_id?>,
								<?php echo $unidadeDeSaude_id?>,
								<?php echo $acs_id?>);
		</script>
		
		
		
	<?php
	
	 //$this->SetarValoresDoPostParaOsCampos();
	
	//print_r($_POST);
	
			
	}
	//--------------------------------------------------------------------------
	public function SetarValoresDoPostParaOsCampos()
	{
		if(isset($_POST)){
		
		foreach($_POST as $chave => $valor)
				if(strlen($valor)) {
						echo "<script>
								if(document.getElementById('$chave').type == 'radio' ||
								   document.getElementById('$chave').type == 'text' ) {
								   
								   document.getElementById('$chave').value = '$valor';
								 
								}
								else
								{
								   document.getElementById('$chave').selectedIndex = $valor;
								}
							  </script>";
				}
		}
		/*
		 
		else{
				var SelectObject = document.getElementById('$chave');
				var Value = '$valor';
				for(index = 0; 
				index < 2; 
				index++) {
						if(SelectObject[index].value == Value)
						SelectObject.selectedIndex = Value;
						alert(Value);
				}
	   }
						
		*/
		//elseif(document.getElementById('$chave').type == 'select-one'){
						
			// document.getElementById('$chave').selectedIndex.value = '$valor';
						
		//}
						
		//alert(document.getElementById('$chave').type);
	}
	//--------------------------------------------------------------------------

	public function ExibirFormularioInserirPessoa(Array $dados = array() )
	{
		if(count($dados) )
		
			list($nomePesquisado, $maePesquisada,
				 $datadenascPesquisada, $cpfPesquisado) = $dados;

        // Para exibir a última aba do cadastro:
		if( isset($_POST['ultimaAbaCadastro']) ) {
			$ultimaAba = $_POST['ultimaAbaCadastro'];
		}

		elseif( isset($_SESSION['InserirPessoa']['ultimaAbaCadastro']) ) {

			$ultimaAba = $_SESSION['InserirPessoa']['ultimaAbaCadastro'];
		}
        else {

            $ultimaAba = 'aba1';
        }

		?>

		<h3 align="center">Cadastro</h3>
		<form id="formulario" name="formulario" method="post"
			action="?<?php echo $_SERVER['QUERY_STRING']; ?>"
			onkeypress="return ImpedirSubmitComEnter(event)"
		 	onsubmit="return (ValidarNome(this.nome, 'nome')
					&& ValidarSexo(this.sexo)
					&& ValidarAcamado(this.acamado)
					&& ValidarCampoSelect(this.etnia, 'etnia')
					&& ValidarData(this.datadenasc)
					&& ValidarCampoSelect(this.ddd, 'DDD', true)
					&& ValidarTelLocal(this.telefone, true)
					&& ValidarCpf(this.cpf, true)
					&& ValidarEmail(this.email, true)
					&& ValidarCampoSelect(this.estadouf, 'estado')
					&& ValidarNome(this.endereco, 'logradouro')
					&& ValidarCampoSelect(this.cidade, 'cidade')
					&& ValidarNome(this.bairro, 'bairro')
					&& ValidarCep(this.cep, true)
					&& ValidarCartaoSus(this.cartaosus, true)
					&& ValidarProntuario(this.prontuario, true)
					&& ValidarCampoSelect(this.profissao, 'profissão')
					&& ValidarCampoSelect(this.unidade, 'unidade de saúde')
					&& ValidarCampoSelect(this.acs, 'agente comunitário de saúde (ACS)')
					&& ValidarNome(this.nomedamae, 'nome da mãe')
					&& ValidarNome(this.nomedopai, 'nome do pai', true)   )">

			<p>
				<!-- ############################################################### -->
                <input type="hidden" id="ultimaAbaCadastro" name="ultimaAbaCadastro" value="aba1" />

                <div id="containerCadastro" align="center">
				<img src="./Imagens/botaoBuscaAbaBasico.jpg" width="120" height="23"
						onmouseover="this.src='./Imagens/botaoBuscaAbaBasicoSobre.jpg'"
						onmouseout="this.src='./Imagens/botaoBuscaAbaBasico.jpg'"
						onclick="ExibirAba('aba1', 'containerCadastro','ultimaAbaCadastro');
                            document.getElementById('containerCadastro').style.height = '500px';
                            document.getElementById('ultimaAbaCadastro').value = 'aba1';"
				/><img src="./Imagens/botaoBuscaAbaAvancado.jpg" width="120" height="23"
						onmouseover="this.src='./Imagens/botaoBuscaAbaAvancadoSobre.jpg'"
						onmouseout="this.src='./Imagens/botaoBuscaAbaAvancado.jpg'"
						onclick="ExibirAba('aba2','containerCadastro','ultimaAbaCadastro');
                            document.getElementById('containerCadastro').style.height = '370px';
                            document.getElementById('ultimaAbaCadastro').value = 'aba2';"
				/><br />
                <div class="abaCadastro" id="aba1">
				<h6 align="left" style="margin: 5px; margin-left:140px">(*) Campos Obrigatórios.</h6>

				<div class='CadastroEsq'>
					*Nome:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="nome" id="nome" maxlength="100" style="width:300px;"
						onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this); ValidarNome(this, 'nome');
						FormatarNome(this, event)"
						value="<?php
							if (isset($_POST['nome'])) echo $_POST['nome'];
							elseif (isset($nomePesquisado)) echo $nomePesquisado;
						?>"
					/>
				</div>
				<br />

                <!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Nome da mãe:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="nomedamae" maxlength="100" style="width:300px;"
						onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this); ValidarNome(this, 'nome da mãe');
						FormatarNome(this, event)"
						value="<?php
							if (isset($_POST['nomedamae']))	echo $_POST['nomedamae'];
							elseif ( isset($maePesquisada)) echo $maePesquisada;
							?>"
					/>
				</div>
				<br />
				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Data de Nascimento:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="datadenasc" maxlength="10"
						onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					    onkeyup="return Mascara('DATA', this, event);"
						onblur="ValidarData(this)"
						value="<?php
							if (isset($_POST['datadenasc'])) echo $_POST['datadenasc'];
							elseif (isset($datadenascPesquisada)) echo $datadenascPesquisada;
						?>" />
					<span id="TextoExemplos">
						<?php echo " Ex.: 01/01/1980 " ?>
					</span>
				</div>
                
				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Sexo:
				</div>
				<div class='CadastroDir'>
					<?php
					if ( isset( $_POST['sexo'] ) && $_POST['sexo'] != '' ) {
						if ( $_POST['sexo'] == 'F' ) {
							echo "<input type='radio' name='sexo' value='F'
							onblur='ValidarSexo(this)' checked />Feminino
							<input type='radio' name='sexo' value='M'
							onblur='ValidarSexo(this)' />Masculino";
						} else {
							echo "<input type='radio' name='sexo' value='F'
							onblur='ValidarSexo(this)' />Feminino
							<input type='radio' name='sexo' value='M'
							onblur='ValidarSexo(this)' checked />Masculino";
						}
					} else {
						echo "<input type='radio' name='sexo' value='F'
							onblur='ValidarSexo(this)' />Feminino
							<input type='radio' name='sexo' value='M'
							onblur='ValidarSexo(this)' />Masculino";
					}

					?>
				</div>
				<br />

                <!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Etnia:
				</div>
				<div class='CadastroDir'>
					<select name="etnia" id="etnia" style="width:305px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'etnia', false)">
						<option value="0">- selecione -</option>
						<?php if ( isset( $_POST['etnia'] ) && $_POST['etnia'] != '' ) {
								    $this->SelecionarEtnia( $_POST['etnia'] );
							  } else {
							  		$this->SelecionarEtnia();
							  }
						?>
					</select>
				</div>
				<br />

				<!-- ############################################################### -->
				
				<div class='CadastroEsq'>
					*Estado UF:
				</div>
				<div class='CadastroDir'>
					<select name="estadouf" id="estadouf" style="width:305px; margin-left:2px;"
					onblur="ValidarCampoSelect(this, 'estado', false)"
					onchange="PesquisarCidades(this.value, <?php echo $_SESSION['cidade_id']; ?>)">
						<option value="0">- selecione -</option>
						<?php
						
						$crip = new Criptografia();
						parse_str($crip->Decifrar($_SERVER['QUERY_STRING']), $arrGet);
							
						if ( isset( $_POST['estadouf'] ) && $_POST['estadouf'] != '' ) {
							$this->SelecionarEstado( $_POST['estadouf'] );
							if ( isset( $_POST['cidade'] ) && $_POST['cidade'] != '' ) {
								echo "<script>PesquisarCidades(document.getElementById( 'estadouf' ).value, $_POST[cidade])</script>";
							} else {
								echo "<script>PesquisarCidades(document.getElementById( 'estadouf' ).value)</script>";
							}
						}
 
						// Pega o estado de "dados", que veio de uma busca por nome na hora
						// de vacinar, que não existia:
						elseif ( isset( $arrGet['estado_id'] ) ) {
							$this->SelecionarEstado( $arrGet['estado_id'] );
							if ( isset( $_POST['cidade'] ) && $_POST['cidade'] != '' ) {
								echo "<script>PesquisarCidades(document.getElementById( 'estadouf' ).value, $_POST[cidade])</script>";
							} else {
								echo "<script>PesquisarCidades(document.getElementById( 'estadouf' ).value)</script>";
							}
						}	
						else {
							$this->SelecionarEstado($_SESSION['estado_id']);
						}
						?>
			  		</select>
		  		</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Cidade:
				</div>
				<div class='CadastroDir'>
					<select name="cidade" id="cidade" style="width:305px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'cidade', false)">
					</select>
				</div>
				<br />

				<!-- ############################################################### -->

                <div class='CadastroEsq'>
					*Bairro:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="bairro" maxlength="100"
						onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
                        onclick="if(this.value == 'Não informado') this.value=''"
						onblur="LimparString(this); ValidarNome(this, 'bairro');
						FormatarNome(this, event)"
						value="<?php if (isset($_POST['bairro'])) echo $_POST['bairro']; else echo 'Não informado'; ?>"
				 />
				</div>
				<br />

                <!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Logradouro:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="endereco" maxlength="100" style="width:300px;"
						onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
                        onclick="if(this.value == 'Não informado') this.value=''"
						onblur="LimparString(this);
						ValidarNome(this, 'logradouro'); FormatarNome(this, event)"
						value="<?php if (isset($_POST['endereco'])) echo $_POST['endereco']; else echo 'Não informado'; ?>" />
				</div>
				<br />

                <!-- ############################################################### -->

				<div class='CadastroEsq'>
					*Profissão:
				</div>
				<div class='CadastroDir'>
					<select name="profissao" id="profissao" style="width:305px;
						margin-left:2px;" onblur="ValidarCampoSelect(this, 'profissão')">
						<option value="1">NÃO INFORMADO</option>
							<?php
							if ( isset( $_POST['profissao'] ) && $_POST['profissao'] != '' ) {
								$this->SelecionarOcupacao( $_POST['profissao'] );
							} else {
								$this->SelecionarOcupacao();
							}
							?>
					</select>
				</div>
				<br />
                <!-- ############################################################### -->
				<?php
				if( Sessao::Permissao('INDIVIDUOS_CADASTRAR') == 1 ){
				?>

				<div class='CadastroEsq'>
					*Unidade de Saúde:
				</div>
				<div class='CadastroDir'>
					<select name="unidade" id="unidade" style="width:305px;
					margin-left:2px;"
					onblur="ValidarCampoSelect(this, 'unidade de saúde', false)"
					onchange="PesquisarAcs(this.value)">

						<option value='0'>- selecione -</option>
						<?php
						if ( isset( $_POST['unidade'] ) && $_POST['unidade'] != '' ) {
							$this->SelectsUnidades( $_POST['unidade'] );
							if ( isset( $_POST['acs'] ) && $_POST['acs'] != '' ) {
								echo "<script>PesquisarAcs(document.getElementById( 'unidade' ).value, $_POST[acs])</script>";
							} else {
								echo "<script>PesquisarAcs(document.getElementById( 'unidade' ).value)</script>";
							}
						}
						else { 
							$this->SelectsUnidades($_SESSION['unidadeDeSaude_id']);
						}
						?>
			  		</select>

		  		</div>
				<?php 
				} elseif ( Sessao::Permissao('INDIVIDUOS_CADASTRAR') == 2 )
					echo "<script>PesquisarAcs({$_SESSION['unidadeDeSaude_id']});</script>";

				?>
				<br />
				<!-- ############################################################### -->
				<div class='CadastroEsq'>
					*Agente Comunitário de Saúde:
				</div>
				<div class='CadastroDir'>
					<select name="acs" id="acs" style="width:305px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'agente comunitário de saúde (ACS)')">
                        <option value="<?php echo $this->RetornarAcsPadraoUnidade($_SESSION['unidadeDeSaude_id']); ?>">Não Informado</option>
					</select>
				</div>
				<br />

				<!-- ############################################################### -->
                <!-- ############################################################### -->
                <?php 

                    $botao = new Vacina();
                    $botao->ExibirBotoesDoFormulario('Inserir', 'Limpar');

                    $voltar = new Form();
                    $voltar->BotaoVoltarHistorico();

                ?>
                </div><div class="abaCadastro" id="aba2">

                <!-- ############################################################### -->
				<!-- ############################################################### -->



				<br />
				<div class='CadastroEsq'> 
					DDD:
				</div>
				<div class='CadastroDir'>
					<select name="ddd" id="ddd" style="width:50px; margin-left:2px;"
						onblur="ValidarCampoSelect(this, 'DDD', true)"
						onchange="document.formulario.telefone.focus()">

					<?php if ( isset( $_POST['ddd'] ) && $_POST['ddd'] != '' ) {
						$this->SelecionarDdd( $_POST['ddd'] );
					} else {
						$this->SelecionarDdd();
					}
					?>
					</select>
					Telefone: <input type="text" name="telefone" id="telefone"
						maxlength="9" onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('TELLOCAL', this, event);"
						onblur="ValidarTelLocal(this, true)"
						value="<?php if (isset($_POST['telefone']))
						echo $_POST['telefone'] ?>" />
					<span id="TextoExemplos">
						<?php echo " Ex.: 2555-0555 " ?>
					</span>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					CPF:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="cpf" maxlength="14"
						onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('CPF',this,event);"
						onblur="ValidarCpf(this, true);"
					value="<?php
						if (isset($_POST['cpf'])) echo $_POST['cpf'];
						elseif (isset($cpfPesquisado)) echo $cpfPesquisado;
						?>"
					/>
					<span id="TextoExemplos">
						<?php echo " Ex.: 474.876.345-07" ?>
					</span>
				</div>
				<br />

				<!-- ############################################################### -->
				<div class='CadastroEsq'>
					E-mail:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="email" id="email" value="<?php
					if(isset($_POST['email'])) { echo $_POST['email']; }?>"
					maxlength="70" size="40"
						onkeydown="Mascara('EMAIL',this,event);"
						onblur="ValidarEmail(this, true);" />
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					CEP:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="cep" id="cep" maxlength="9"
						onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('CEP',this,event);"
						onblur="ValidarCep(this, true)"
						value="<?php if (isset($_POST['cep'])) echo $_POST['cep'] ?>"
					/>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					Cartão SUS:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="cartaosus" maxlength="15"
						onkeypress="return Digitos(event, this);"
						onblur="ValidarCartaoSus(this, true)"
						value="<?php if (isset($_POST['cartaosus']))
						echo $_POST['cartaosus'] ?>"
					/>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					Prontuário:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="prontuario" maxlength="10"
						onkeypress="return Digitos(event, this);"
						onblur="ValidarProntuario(this, true)"
						value="<?php if (isset($_POST['prontuario']))
						echo $_POST['prontuario'] ?>"
					/>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					Nome do pai:
				</div>
				<div class='CadastroDir'>
					<input type="text" name="nomedopai" maxlength="100" style="width:300px;"
						onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this); ValidarNome(this, 'nome do pai', true);
						FormatarNome(this, event)"
						value="<?php if (isset($_POST['nomedopai']))
						echo $_POST['nomedopai'] ?>"
					/>
				</div>
				<br />

				<!-- ############################################################### -->

				<div class='CadastroEsq'>
					Acamado:
				</div>
				<div class='CadastroDir'>
					<?php
						if ( isset($_POST['acamado']) && $_POST['acamado'] == 1 ) {
							echo "<input type='radio' name='acamado' value='1'
							onblur='ValidarAcamado(this)' checked />Sim
							<input type='radio' name='Acamado' value='0'
							onblur='ValidarAcamado(this)' />Não";
						} else {
							echo "<input type='radio' name='acamado' value='1'
							onblur='ValidarAcamado(this)' />Sim
							<input type='radio' name='acamado' value='0'
							onblur='ValidarAcamado(this)' checked />Não";
						}
					
					?>
				</div>

				<br />

				<!-- ############################################################### -->
				<!-- ############################################################### -->
				<!-- ############################################################### -->
				  	<?php

						$botao = new Vacina();
						$botao->ExibirBotoesDoFormulario('Inserir', 'Limpar');
						
						$voltar = new Form();
						$voltar->BotaoVoltarHistorico();

					?>         </div> </div>
                <!-- ############################################################### -->
				<!-- ############################################################### -->
				<!-- ############################################################### -->
				

		 	</p>

		</form>

        <script>

            

            <?php if($ultimaAba == 'aba1') {
                ?>
                    document.getElementById('containerCadastro').style.height = '500px';
            <?php
            }
            else {
                ?>
                    document.getElementById('containerCadastro').style.height = '370px';
            <?php
            }
            ?>
            ExibirAba('<?php echo $ultimaAba ?>', 'containerCadastro', 'ultimaAbaCadastro');
            PesquisarCidades(document.getElementById('estadouf').value, <?php echo $_SESSION['cidade_id']; ?>);
		</script>
		
		<?php
	}
	//--------------------------------------------------------------------------
    public function RetornarAcsPadraoUnidade($unidadeId)
    {
        $stmt = $this->conexao->prepare("SELECT 
                                            id FROM `acs`
                                        WHERE
                                            nome = 'Não Informado'
                                            AND UnidadeDeSaude_id = '$unidadeId'")
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        $stmt->bind_result($acs_id);
        $stmt->execute();
        $stmt->fetch();
        $stmt->free_result();

        return $acs_id;

    }
	//--------------------------------------------------------------------------

	public function ExibirFormularioListarCampanhasBuscarPessoa($listarDescontinuadas = 0,
			$retroativo = 0) // Vacinar pessoa
	{

		$pesquisa = $mae = $cpf = $nasc = $emAtraso = '';
			
		// Para pesquisa de nome:
		if( isset($_POST['pesquisa'])) {
			$pesquisa = $_POST['pesquisa'];
		}
		
		elseif( isset($_SESSION['listarPessoasVacinaveis']['pesquisa'])) {
			$pesquisa = $_SESSION['listarPessoasVacinaveis']['pesquisa'];
		}
		
		// Para pesquisa de mae:
		if( isset($_POST['mae'])) {
			$mae = $_POST['mae'];
		}
		
		elseif( isset($_SESSION['listarPessoasVacinaveis']['mae'])) {
			$mae = $_SESSION['listarPessoasVacinaveis']['mae'];
		}
		
		if ($mae == 'vazio') $mae = '';
		
		// Para pesquisa com cpf:
		if( isset($_POST['cpf']) && strlen($_POST['cpf']) == 14 ) {
			$cpf = $_POST['cpf'];
		}
		
		elseif( isset($_SESSION['listarPessoasVacinaveis']['cpf'])
			&& strlen($_SESSION['listarPessoasVacinaveis']['cpf']) == 11) {
				
			$cpf = Preparacao::InserirSimbolos(
				$_SESSION['listarPessoasVacinaveis']['cpf'], 'CPF');
		}
	
		$data = new Data();
		
		// Para pesquisa com nascimento:
		if( isset($_POST['datadenasc']) && strlen($_POST['datadenasc']) == 10) {
			$nasc = $_POST['datadenasc'];
		}
		
		elseif( isset($_SESSION['listarPessoasVacinaveis']['datadenasc']) &&
			strlen($_SESSION['listarPessoasVacinaveis']['datadenasc']) == 10) {
			$nasc = $data->InverterData($_SESSION['listarPessoasVacinaveis']['datadenasc']);
		}
				
		// Para pesquisa de pessoas apenas com doses em atraso:
		if( isset($_POST['emAtraso']) && $_POST['emAtraso'] == 'on') {
			$emAtraso = $_POST['emAtraso'];
		}
		
		elseif( isset($_SESSION['listarPessoasVacinaveis']['emAtraso']) &&
			$_SESSION['listarPessoasVacinaveis']['emAtraso'] == 'on') {
			$emAtraso = $_SESSION['listarPessoasVacinaveis']['emAtraso'];
		}
		
		?>

			<form id="formulario2" name="formulario2" method="post"
				action="<?php echo $_SERVER['REQUEST_URI']?>"
				onsubmit="return (((ValidarPesquisa(this.pesquisa, 3 , true) && ValidarPesquisa(this.mae, 3, false)) ||
						(ValidarPesquisa(this.pesquisa, 3 , false) && ValidarPesquisa(this.mae, 3, true)))
						&& ValidarData(this.datadenasc, true)
						&& ValidarCpf(this.cpf, true)
                        && ValidarVacinaFilha(this.vacina, this.vacinaFilha)
						&& ValidarCampoSelect(this.campanha, 'campanha')
						&& ValidarCampoSelect(this.vacina, 'vacina') )">
				<div>
					<?php
					 if( $retroativo == 1 ) {
						
						echo "<input type='hidden' name='campanha' id='campanha' value='semCampanha' />";
						echo "<script>ListarVacinas(\"divVacinas\", 'semCampanha', $listarDescontinuadas, $retroativo);
							SetarTexto(\"listaDePessoas\", \"\")</script>";
						
					 } else {
						
						echo "<div class='CadastroEsq'>
						Campanha:
						</div>
						<div class='CadastroDir'>";
						$this->SelectCampanhas($listarDescontinuadas, $retroativo);
						echo '</div><br />';
						 
					 }
					?>
					
					<div id="divVacinas">&nbsp;</div>
					<div id="vacinasFilhas">&nbsp;
                    <?php

                    $crip = new Criptografia();
                    parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
                  //  print_r($_SESSION['listarPessoasVacinaveis']);
                       if(isset($_SESSION['listarPessoasVacinaveis']['vacina'])
                           && !isset($campanhaDoIcone)
                           && $_SESSION['listarPessoasVacinaveis']['campanha'] == 'semCampanha' ){

                            $vacina = new Vacina;
                            $vacina->UsarBaseDeDados();
                            $consulta = $vacina->CarregarVacinasFilhas($_SESSION['listarPessoasVacinaveis']['vacina']);
                           if($consulta) {
                            echo '<div class="CadastroEsq">Tipo de Aplicação: </div><div class="CadastroDir">';
                               echo $consulta;
                            echo '</div>';

                            }
                        }
                    ?>
                    </div>
					
					
					<div class="CadastroEsq">
						Nome:
					</div>

					<div class="CadastroDir">
						<input type="text" name="pesquisa" id="pesquisa"
							value="<?php echo $pesquisa?>"
							style="width:300px;" onkeypress="FormatarNome(this, event)"
							onkeyup="FormatarNome(this, event)"
							onkeydown="Mascara('NOME', this, event)"
							onblur="LimparString(this); ValidarPesquisa(this, 3 , true);
									FormatarNome(this, event)"
							 />
					</div>
					
					<br />
					<div class="CadastroEsq">
						Mãe:
					</div>

					<div class="CadastroDir">
						<input type="text" name="mae" id="mae"
							value="<?php echo $mae?>"
							style="width:300px;" onkeypress="FormatarNome(this, event)"
							onkeyup="FormatarNome(this, event)"
							onkeydown="Mascara('NOME', this, event)"
							onblur="LimparString(this); ValidarPesquisa(this, 3, true);
									FormatarNome(this, event)"
							 />
					</div>
					
					<p>
					<div class="CadastroEsq">
						Nascimento:
					</div>
					
					<div class='CadastroDir'>
						<input type="text" name="datadenasc" maxlength="10"
							onkeypress="return Digitos(event, this);"
					        onkeydown="return Mascara('DATA', this, event);"
						    onkeyup="return Mascara('DATA', this, event);"
							onblur="ValidarData(this,true)"
							value="<?php echo $nasc;?>" />
						<span id="TextoExemplos">
							<?php echo " Ex.: 01/01/1980 " ?>
						</span>
					</div>
					</p>		
					
					<p>
					<div class="CadastroEsq">
						CPF:
					</div>
					
					<div class="CadastroDir">
						<input type="text" name="cpf" maxlength="14"
						onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('CPF',this,event);"
						onblur="ValidarCpf(this, true);"
						value="<?php echo $cpf;?>" />
							<span id="TextoExemplos">
								<?php echo " Ex.: 474.876.345-07" ?>
							</span>
					</div>
					</p>
					<p>
					<div class="CadastroEsq"></div>
					
					<div class="CadastroDir">
						<label>
							<input type="checkbox" name="emAtraso" id="emAtraso" <?php
							if($emAtraso == 'on') echo ' checked="true" '; ?> />
							Listar somente indivíduos com doses em atraso
						</label>
					</div>
					</p>
					<p>
					<div class="CadastroEsq"></div>
					
					<div class="CadastroDir">
						<label>
							<input type="checkbox" name="nestaCidade" id="nestaCidade" checked="checked" 
							onclick="ExibirBuscaPorEstadoCidade('buscarPorEstadoCidade', this.checked)"/>
							Habitantes de <?php echo "{$_SESSION['cidade_nome']}/{$_SESSION['estado_id']}" ?>
						</label>
					</div>
					
					<div id="buscarPorEstadoCidade"></div>
					
					<div class="CadastroEsq"></div>
					
					
					<!--
					<div class="CadastroDir">
					<fieldset style='color:#666'>
						<legend>Opções Alternativas</legend>
						<label>
							<?php /*
							$checado = '';
							if( isset($_POST['retroativo']) && $_POST['retroativo'] == 'on')
								$checado = 'checked="true"';
							*/
							?>
							<input type="checkbox" name="retroativo" id="retroativo" <?php //echo $checado?>
							onclick="ExibirCampoSenhaAdm('confirmarSenhaRetroativo', this.checked); 
									 if(this.checked) alert('Só utilize para lançamentos de informações\n' +
											 				 'retroativas na caderneta de vacinação.');" />
							Apenas Atualizar Caderneta de Vacinação
						</label>
					</fieldset>	
					</div>
					-->
					
					<!-- Para conter o input aonde digita a senha -->
					<div id="confirmarSenhaRetroativo">
					<?php 
					if( isset($_POST['retroativo']) && $_POST['retroativo'] == 'on') {
						
						$adm = new Administrador();
						$adm->ExibirConfirmarSenha('hidden');
					}
					?>
					</div>
					
					<!-- Para conter o input hidden dizendo que a senha ta ok -->
					<div id="hidden"></div>
					</p>
					
					<?php

					$botao = new Vacina();

					$botao->ExibirBotaoBuscar('buscar');

					if( isset($_POST['senhaOk']) && $_POST['senhaOk'] == 'ok' &&
						$_POST['campanha'] == 'semCampanha' ) {
						
						echo '<strong>Nota:</strong><blockquote>Os indivíduos
							listados abaixo serão vacinados retroativamente.
							Caso não seja esta a sua intenção, desmarque a
							opção "Retroativo" e execute <em>novamente</em> a
							busca.</blockquote>';
					}
					
					?>

				</div>
			</form>
		<br />
		<script> 
            
            //document.getElementById('vacinasFilhas').innerHTML = "<?php //$crip = new Criptografia(); echo $crip->Cifrar($consulta) ?>";
            //var conteudo  = document.getElementById('vacinasFilhas').innerHTML;
            //conteudo = Decifrar(conteudo);
            //alert(conteudo.length);

			if(document.formulario2.campanha.value != "semCampanha")
				document.getElementById("retroativo").disabled = true;
			else
				document.getElementById("retroativo").disabled = false;

            
		</script>	
		<?php
	}

	//---------------------------------------------------------------------------
	private function SelectCampanhas($listarDescontinuadas = 0, $retroativo = 0) // ExibirFormularioListarCampanhasBuscarPessoa
	{
		$crip = new Criptografia();
		parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
		
		$campanha_selecionada = 0;
		if( isset($campanhaDoIcone) ) {
				
				$campanha_selecionada = $campanhaDoIcone;
		}
		
		elseif( isset($_POST['campanha'])) {
			$campanha_selecionada = $_POST['campanha'];
		}
		
		elseif( isset($_SESSION['listarPessoasVacinaveis']['campanha'])) {
			$campanha_selecionada = $_SESSION['listarPessoasVacinaveis']['campanha'];
		}
		
		if( $retroativo <> 0 )
			$campanha_selecionada = 0;
		
		echo "\n<select name='campanha' id='campanha' style='width: 305px'
			onchange='ListarVacinas(\"divVacinas\", this.value, $listarDescontinuadas, $retroativo);
			SetarTexto(\"listaDePessoas\", \"\");'
			onblur=\"ValidarCampoSelect(this, 'Campanha')\">";


		if( isset($campanhaDoIcone) ) {
				
		    echo "<option value='$campanhaDoIcone'>"
				. Html::FormatarMaiusculasMinusculas($campanhaDoIconeNome)
				. '</option></select>';
			return;
		}

		echo "<option value='0'>- selecione -</option>";

		$selecionada = '';
		if( $campanha_selecionada == 0 ) {
			
			$selecionada = " selected='true' ";
		}
		
		echo "\n\t<option value='semCampanha' $selecionada>Rotina</option>";
		
		if( $retroativo == 0 ){
			
			$camp = $this->conexao->prepare('SELECT id, nome FROM `campanha` WHERE
				ativo ORDER BY datainicio')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$camp->bind_result($campanha_id, $campanha_nome);
			$camp->execute();
            $camp->store_result();

            $campanha = new Campanha();
            $campanha->UsarBaseDeDados();
			while ($camp->fetch()) {

                $desabilitada = ' disabled="true" title="Campanha fora do período" ';
                
                // Se hoje está entre dataInicio e dataFim da campanha, habilita:
                if($campanha->VerificarPeriodoDaCampanha($campanha_id)) $desabilitada = '';
				
				$selecionada = '';
				if( $campanha_selecionada == $campanha_id ) {
					
					$selecionada = " selected='true' ";
				}
				
				echo "\n\t<option value='$campanha_id' $selecionada $desabilitada>".Html::FormatarMaiusculasMinusculas($campanha_nome)."</option>";
			}
			$camp->free_result();	
			
		}
		
		echo "\n</select>";
		
		if($campanha_selecionada == 0) {
			
			echo "<script>
			ListarVacinas('divVacinas', document.formulario2.campanha.value,
				$listarDescontinuadas, $retroativo);
			</script>";
		}
	}

	//--------------------------------------------------------------------------
    public function VerificarSePessoaExiste($pesquisa, $mae, $cpf, $nasc,
               $cidade_id, $vacinavel = false)
	{
		$nome = $this->conexao->real_escape_string(trim($pesquisa));
		$mae = $this->conexao->real_escape_string(trim($mae));

		$explodeCaracteres = explode(' ',$nome);
		$implodeCaracteres = implode('%',$explodeCaracteres);

		$nome = "%$implodeCaracteres%";

		$explodeCaracteres = explode(' ',$mae);
		$implodeCaracteres = implode('%',$explodeCaracteres);

		$mae = "%$implodeCaracteres%";
		
		$data = new Data();
		
		
		$sqlMae = false;
		if($mae != '%vazio%') $sqlMae = "AND (usuario.mae LIKE '$mae' OR '$mae' = '%vazio%')";
		
		$sqlNome = false;
		if( strlen($nome) > 4) $sqlNome = "AND usuario.nome LIKE '$nome'";

        $sqlVacinavel = $vacinavel ? 'AND usuario.vacinavel ' : '';

		$sql = "SELECT COUNT(usuario.id)
				FROM `usuario`, `cidade`, `bairro`
				WHERE (usuario.cpf = ? OR '$cpf' = 'vazio')
				AND (usuario.nascimento = ? OR '$nasc' = 'vazio')
				$sqlMae
				AND usuario.Bairro_id = bairro.id
				AND bairro.Cidade_id = cidade.id
				AND cidade.id = ?
                AND usuario.ativo
                AND bairro.ativo
				$sqlNome
                $sqlVacinavel";
				
				$resultado = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));


	    $resultado->bind_param('ssi', $cpf, $nasc, $cidade_id);
	    
		$numeroDeIndividuos = false;
		$resultado->bind_result($numeroDeIndividuos);

		$resultado->execute();

		$resultado->fetch();
		
		$resultado->free_result();
		
		return $numeroDeIndividuos;
	}
    //--------------------------------------------------------------------------
    /**
     * Exibe o formulário para a inativação da pessoa. Uma pessoa inativada não
     * deverá ser listada quando for buscada para vacinar, porém ela deve estar
     * contida nos relatórios de vacinação, se um dia foi vacinada.
     *
     * @param $id Identificação da pessoa
     */
	public function ExibirFormularioInativarPessoa($id)
	{
		?>
		<div align="left">
			<form id="inativarPessoa" name="inativarPessoa" method="post"
			  action="<?php echo $_SERVER['REQUEST_URI']?>"
              onsubmit="return (ValidarTextoLongo(this.motivo, false, 5, true)
                       && ValidarData(this.dataDesligamento) )">
			<?php

                // Recebendo a lista com os dados do usuário:
				list($etnia_id, $edd_id, $bairro_id,
		             $ocupacao_id, $acs_id, $nome,
                     $prontuario, $sexo, $nascimento,
                     $email, $telefone,	$logradouro,
                     $cep, $cartaosus, $cpf, $mae, $pai)

                     = $this->SelecionarDadosUsuario($id);


                // Formatando os dados recebidos:
				$nome = HTML::FormatarMaiusculasMinusculas($nome);
				$mae = HTML::FormatarMaiusculasMinusculas($mae);
                $prontuario = $prontuario ? $prontuario : 'Não informado';
                $cpf = $cpf ? $cpf : 'Não informado';
                $cartaosus = $cartaosus ? $cartaosus : 'Não informado';

				$data = new Data();

                echo '<h4>Detalhes</h4><ul>';
				echo "<li><strong>Mãe:</strong> $mae</li>";
				echo "<li><strong>Nascimento:</strong> ". $data->InverterData($nascimento);
				echo "<li><strong>Prontuário:</strong> $prontuario</li>";
				echo "<li><strong>CPF:</strong> $cpf</li>";
				echo "<li><strong>Cartão SUS:</strong> $cartaosus</li>";
                echo '</ul>';

                ?>
                <div align="center" style="clear:both"><b>Data de inativação:</b></div>

                <div align="center" style="clear:both"><input type="text"
                        name="dataDesligamento" id="dataDesligamento"
                        maxlength="10"
                        onkeypress="return Digitos(event, this);"
                        onkeydown="return Mascara('DATA', this, event);"
                        onkeyup="return Mascara('DATA', this, event);"
                        onblur="ValidarData(this,true)"
                        value="<?php if( isset($_POST['dataDesligamento']) )
                                        echo $_POST[ 'dataDesligamento']?>" /></div>

                <br />
                <div align="center" style="clear:both"><b>Motivo da inativação:</b>
                <div align="left" style="width:150px;">

                <!-- <textarea name="motivo" id="motivo"
                     cols="50" rows="5" style="width:450px;"><?php /*
                     if( isset($_POST['motivo']) )
                     echo $_POST[ 'motivo'] */ ?>
                </textarea> -->
                    <p>
                    <label><input type="radio" name="motivo" value="Mudança de Cidade"
                           checked="true" style="vertical-align: bottom;">Mudança de Cidade
                    </label>
                    <br />

                    <label><input type="radio" name="motivo" value="Óbito"
                           checked="true" style="vertical-align: bottom;">Óbito
                    </label>
                    <br />

                    <label><input type="radio" name="motivo" value="Duplicidade"
                           checked="true" style="vertical-align: bottom;">Duplicidade
                    </label>
                    <br />
                    </p>

                 </div>
                 </div>
                <?php

				$botao = new Vacina();

				$botao->ExibirBotoesDoFormulario('Inativar', false, 'excluir');

                // Monta a nota com o sexo certo - excluído(a), registrado(a)...
                if( strtoupper($sexo) == 'M' )
                {
                    $excluido_a   = 'excluído';
                    $registrado_a = 'registrado';
                    $vacinado_a   = 'vacinado';
                    $ele_a        = 'ele';
                    $inativad_a   = 'inativado';
                }
                else
                {
                    $excluido_a   = 'excluída';
                    $registrado_a = 'registrada';
                    $vacinado_a   = 'vacinada';
                    $ele_a        = 'ela';
                    $inativad_a   = 'inativada';
                }

                echo "<strong>Nota:</strong><blockquote>$nome não pode ser
							$excluido_a automaticamente, pois foi $registrado_a pelo
                            sistema como $vacinado_a. Por isso $ele_a deve ser
                            $inativad_a com data e motivo.</blockquote>";
				echo '<hr />';

				$botao->ExibirBotaoVoltar('Voltar', 'pagina=Adm/listarPessoa');

			?>
			</form>
		</div>
		<?php
    }
    //--------------------------------------------------------------------------
	public function ExibirFormularioListarPessoa() // Buscar pessoa
	{
		$pesquisa = $cpf = $nasc = $mae = '';
		
		if( isset($_POST['pesquisa'])) $pesquisa = $_POST['pesquisa'];
		
        elseif( isset($_SESSION['listarPessoa']['pesquisa']))
            $pesquisa = $_SESSION['listarPessoa']['pesquisa'];
		
		
		if( isset($_POST['mae'])) {
			$mae = $_POST['mae'];
		}
		
		elseif( isset($_SESSION['listarPessoa']['mae'])) {
			$mae = $_SESSION['listarPessoa']['mae'];
		}

		// Para pesquisa com cpf:
		if( isset($_POST['cpf']) && strlen($_POST['cpf']) == 14 ) {
			$cpf = $_POST['cpf'];
		}

		elseif( isset($_SESSION['listarPessoa']['cpf'])
			&& strlen($_SESSION['listarPessoa']['cpf']) == 11) {

			$cpf = Preparacao::InserirSimbolos(
				$_SESSION['listarPessoa']['cpf'], 'CPF');
		}

		// Para exibir a última aba de pesquisa:
		if( isset($_POST['ultimaAba']) ) {
			$ultimaAba = $_POST['ultimaAba'];
		}
		
		elseif( isset($_SESSION['listarPessoa']['ultimaAba']) ) {
				
			$ultimaAba = $_SESSION['listarPessoa']['ultimaAba'];
		}
        else {

            $ultimaAba = 'aba1';
        }
	
		$data = new Data();
		
		// Para pesquisa com nascimento:
		if( isset($_POST['datadenasc']) && strlen($_POST['datadenasc']) == 10) {
			$nasc = $_POST['datadenasc'];
		}
		
		elseif( isset($_SESSION['listarPessoa']['datadenasc']) &&
			strlen($_SESSION['listarPessoa']['datadenasc']) == 10) {
			$nasc = $data->InverterData($_SESSION['listarPessoa']['datadenasc']);
		}
		
		Html::PrecarregarImagens(
				array('./Imagens/botaoBuscaAbaBasicoSobre.jpg',
					  './Imagens/botaoBuscaAbaAvancadoSobre.jpg') );
		
		//print_r(parse_url($_SERVER['HTTP_REFERER']));
		?>

		

			<form id="formulario2" name="formulario2" method="post" onsubmit=" return ValidarPesquisaNomeParecido(nomeParecido_avancado, 10, true);"
				action="<?php echo $_SERVER['REQUEST_URI']?>"
				 >
				
				<input type="hidden" id="ultimaAba" name="ultimaAba" value="aba1" />
		
                <div id="container" align="center">
				<img src="./Imagens/botaoBuscaAbaBasico.jpg" width="120" height="23"						onmouseover="this.src='./Imagens/botaoBuscaAbaBasicoSobre.jpg'"
						onmouseout="this.src='./Imagens/botaoBuscaAbaBasico.jpg'"						onclick="ExibirAba('aba1', 'container','ultimaAba');
                            document.getElementById('container').style.height = '250px';                            document.getElementById('ultimaAba').value = 'aba1';"
				/><img src="./Imagens/botaoBuscaAbaAvancado.jpg" width="120" height="23"						onmouseover="this.src='./Imagens/botaoBuscaAbaAvancadoSobre.jpg'"
						onmouseout="this.src='./Imagens/botaoBuscaAbaAvancado.jpg'"						onclick="ExibirAba('aba2', 'container', 'ultimaAba');
                            document.getElementById('container').style.height = '420px';
                            document.getElementById('ultimaAba').value = 'aba2';"
				/><br />
				<div class="aba" id="aba1">
					<p>
					<div class="CadastroEsq">
						Nome:
					</div> 
					<div class='CadastroDir'>
					<input type="text" name="pesquisa" id="pesquisa"
						value="<?php echo $pesquisa?>"
						style="width:300px;" onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						value="<?php echo $pesquisa?>"/>
					</div>

					<div class="CadastroEsq">
						Mãe:
					</div> 
					<div class='CadastroDir'>
					<input type="text" name="mae" id="mae" 						value="<?php echo $mae?>"
						style="width:300px;" onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						value="<?php $mae?>"/>
					</div>

					<div class="CadastroEsq">
						Nascimento:
					</div>
					
					<div class='CadastroDir'>
						<input type="text" name="datadenasc" maxlength="10"
							onkeypress="return Digitos(event, this);"
					        onkeydown="return Mascara('DATA', this, event);"
						    onkeyup="return Mascara('DATA', this, event);"
							onblur="ValidarData(this,true)"
							value="<?php echo $nasc;?>" />
						<span id="TextoExemplos">
							<?php echo " Ex.: 01/01/1980 " ?>
						</span>
					</div>

					<div class="CadastroEsq">
						CPF:
					</div>
					
					<div class="CadastroDir">
						<input type="text" name="cpf" maxlength="14"
						onkeypress="return Digitos(event, this);"
						onkeydown="Mascara('CPF',this,event);"
						onblur="ValidarCpf(this, true);"
						value="<?php echo $cpf;?>" />
							<span id="TextoExemplos">
								<?php echo " Ex.: 474.876.345-07" ?>
							</span>
					</div><br />

					<div class="CadastroEsq"></div>
					
					<div class="CadastroDir">
						<label>
							<input type="checkbox" name="nestaCidade" id="nestaCidade" checked="checked" 
							onclick="ExibirBuscaPorEstadoCidade('buscarPorEstadoCidade', this.checked)"/>
							Habitantes de <?php echo "{$_SESSION['cidade_nome']}/{$_SESSION['estado_id']}" ?>
						</label>
					</div>
					</p>
					<div id="buscarPorEstadoCidade"></div>
					</div> <!-- fechando class="aba" id="aba1" -->
					
					<!-- --------------------------------------------------- -->
					<!-- ---------------   A  B  A    2   ------------------ -->
					<!-- --------------------------------------------------- -->
					
					<div class="aba" id="aba2">
				    <p>				
				
				    <div class="CadastroEsq">
						Cidade de atendimento:
					</div>
				    <div class='CadastroDir'>
					<select name="cidade_avancado" 					style="width:305px;"
						onblur="ValidarCampoSelect(this, 'cidade', false)"
						onchange="PesquisarUnidadesSemTipo(this.value, 'unidade_avancado');
                                  document.getElementById('acs_avancado').value=0">
						
						<?php
						if ( isset( $_POST['cidade_avancado'] ) && $_POST['cidade_avancado'] ) {
							$this->SelectCidades( $_POST['cidade_avancado'] );
						}
						else {
							$this->SelectCidades();
						}
						?>
						
					</select>
					</div><br />
					<!-- ---------------------------------------------------- -->
				    <div class="CadastroEsq">
						Unidade de atendimento:
					</div>
				    <div class='CadastroDir'>
					<select name="unidade_avançado" id="unidade_avançado"
						style="width:305px"
						onblur="ValidarCampoSelect(this, 'unidade de saúde', true)"
						onchange="PesquisarAcs(this.value, '', 'acs_avancado')">
						
						<option value='1'>- selecione -</option>
						<?php
						if ( isset( $_POST['unidade_avancado'] ) && $_POST['unidade_avancado']  != '') {
							$this->SelectsUnidades( $_POST['unidade_avancado'] );
							if ( isset( $_POST['acs_avancado'] ) && $_POST['acs_avancado'] != '') {
								echo "<script>PesquisarAcs(document.getElementById('unidade_avancado').value, $_POST[acs_avancado], 'acs_avancado')</script>";
							} else {
								echo "<script>PesquisarAcs(document.getElementById('unidade_avancado').value, '', 'acs_avancado')</script>";
							}
						}
						else {
							$this->SelectsUnidades();
						}
						?></select>
						
					</div><br />
					<!-- ---------------------------------------------------- -->
				    <div class="CadastroEsq">
						ACS responsável:
					</div>
				    <div class='CadastroDir'>
					<select name="acs_avancado" 		style="width:305px;"></select>
					</div><br />


				    <!-- ---------------------------------------------------- -->
                    <p>
					<div class="CadastroEsq">Sexo:</div>					<div class='CadastroDir'>
                        <select name="sexo_avancado" id="sexo_avancado"						style="width:305px;">
                            <?php
                            $selecao = true;
                            ?>
                            <option value="0">Ambos</option>
                            <option value="M" <?php
                                if($selecao == 'M') echo ' selected="true"'
                                ?>>Masculino</option>
                            <option value="F" <?php
                                if($selecao == 'F') echo ' selected="true"'
                                ?>>Feminino</option>
                            
                        </select>
					</div><br />
					</p>

				    <!-- ---------------------------------------------------- -->
					<div class='CadastroDir' style="width:730px;" align="center" >
						<hr style="width:500px"/>
					</div>
				    <!-- ---------------------------------------------------- -->
					
					<div class="CadastroEsq">Idade entre:</div>
					<div class='CadastroDir'>
					<input type="text" name="faixaInicio_avancado" 
						id="faixaInicio_avancado" style="width:50px" maxlength="7"
                    onkeypress="return Digitos(event, this);"                    onblur="ValidarFaixaEtaria(document.formulario2.faixaInicio_avancado,                    document.formulario.unidadeInicio_avancado, this, document.formulario.unidadeFim_avancado, true)"/>
					<select name="unidadeInicio_avancado" id="unidadeInicio_avancado">
					<option value="day">Dia(s)</option>
					<option value="week">Semana(s)</option>
					<option value="month">Mês(s)</option>
					<option value="year">Ano(s)</option>
					</select>
					e
					<input type="text" name="faixaFim_avancado" id="faixaFim_avancado"
						style="width:50px" maxlength="7"
                    onkeypress="return Digitos(event, this);"                    onblur="ValidarFaixaEtaria(document.formulario2.faixaInicio_avancado,                    document.formulario.unidadeInicio_avancado, this, document.formulario.unidadeFim_avancado, true)"/>
					<select name="unidadeFim_avancado" id="unidadeFim_avancado">
					<option value="day">Dia(s)</option>
					<option value="week">Semana(s)</option>
					<option value="month">Mês(s)</option>
					<option value="year">Ano(s)</option>
					</select>
					</div><br />
					</p>
					<!-- ---------------------------------------------------- -->
					<div class='CadastroDir' style="width:730px;" align="center" >
						<hr style="width:500px"/>
					</div>				    <!-- ---------------------------------------------------- -->
				    <p>
					<div class="CadastroEsq">Aniversariante em:</div>
					<div class='CadastroDir'>Dia					<input type="text" name="diaaniversario_avancado"
					        onblur="ValidarDia(this, true);"
						id="diaAniversario_avancado" style="width:50px" maxlength="2"
                    onkeypress="return Digitos(event, this);"                    onblur="ValidarFaixaEtaria(document.escolherRelatorio.faixaInicio,                    document.escolherRelatorio.unidadeInicio,this,document.escolherRelatorio.unidadeFim,true)"/>

					Mês
					<input type="text" name="mesAniversario_avancado" id="mesAniversario_avancado"
						onblur="ValidarMesBaseadoNoDia(this, document.formulario2.diaAniversario_avancado);"
						style="width:50px" maxlength="2"
                    onkeypress="return Digitos(event, this);"
                    onblur="ValidarFaixaEtaria(document.escolherRelatorio.faixaInicio,
                    document.escolherRelatorio.unidadeInicio,this,document.escolherRelatorio.unidadeFim,true)"/>
					</div><br /> 					</p>
					<!-- ---------------------------------------------------- -->
					<div class='CadastroDir' style="width:730px;" align="center" >
						<hr style="width:500px"/>
					</div>
				    <!-- ---------------------------------------------------- -->
				    <p>
					<div class="CadastroEsq"></div>
                    <div class='CadastroDir'>
                        Busca de nome parecido
                        <img
                            src="./Imagens/iconeInformacao.png"
                            alt="Informação"
                            onmousemove="<?php $this->ExibirInformacao(
                                Array('containerInformacao',
                                      'tituloInformacao',
                                      'corpoInformacao')
                                  );?>"
                        />

                    </div>
					<div class="CadastroEsq">Nome:</div>
					<div class='CadastroDir'>
					<input type="text" name="nomeParecido_avancado"                        value="<?php
                        $nivelDeSemelhancaSelecionado = 1;
                        
                        ?>"
						id="nomeParecido_avancado"
                        style="width:300px;"
                        onkeypress="FormatarNome(this, event)"
						onkeyup="FormatarNome(this, event)"
						onkeydown="Mascara('NOME', this, event)"
						onblur="LimparString(this); ValidarPesquisaNomeParecido(this, 10, true);
								FormatarNome(this, event)" maxlength="60"/>
    				</div><br />
					<div class="CadastroEsq">Folga de semelhança:</div>
					<div class='CadastroDir'>1
                        <input type="radio" name="semelhanca_avancado" value="1"                                id="semelhanca_avancado_1" <?php
                                   if($nivelDeSemelhancaSelecionado == 1)
                                       echo 'checked="false"';
                               ?>
                               title="1 - Quase idêntico"/>
                        <input type="radio" name="semelhanca_avancado " value="2"
                               id="semelhanca_avancado_2" <?php
                                   if($nivelDeSemelhancaSelecionado == 2)
                                       echo 'checked="false"';                               ?>
                               title="2 - Muito parecido"/>
                        <input type="radio" name="semelhanca_avancado" value="3"
                               id="semelhanca_avancado_3" <?php
                                   if($nivelDeSemelhancaSelecionado == 3)                                       echo 'checked="true"';
                               ?>
                               title="3 - Parecido"/>
                        <input type="radio" name="semelhanca_avancado" value="4"
                               id="semelhanca_avancado_4" <?php
                                   if($nivelDeSemelhancaSelecionado == 4)                                       echo 'checked="true"';
                               ?>
                               title="4 - Alguma semelhança"/>
                        <input type="radio" name="semelhanca_avancado" value="5"
                               id="semelhanca_avancado_5" <?php
                                   if($nivelDeSemelhancaSelecionado == 5)
                                       echo 'checked="true"';
                               ?>
                               title="5 - Traços de semelhança"/>
                        <input type="radio" name="semelhanca_avancado" value="6"
                               id="semelhanca_avancado_6" <?php
                                   if($nivelDeSemelhancaSelecionado == 6)                                       echo 'checked="false"';
                               ?>
                               title="6 - Com alguma semelhança"/>
                        <input type="radio" name="semelhanca_avancado" value="7"
                               id="semelhanca_avancado_6" <?php
                                   if($nivelDeSemelhancaSelecionado == 7)                                       echo 'checked="false"';
                               ?>
                               title="7 - Com alguma semelhança"/>
                        <input type="radio" name="semelhanca_avancado" value="8"
                               id="semelhanca_avancado_6" <?php
                                   if($nivelDeSemelhancaSelecionado == 8)
                                       echo 'checked="false"';
                               ?>
                               title="8 - Traços de semelhança"/>
                        <input type="radio" name="semelhanca_avancado" value="9"
                               id="semelhanca_avancado_6" <?php
                                   if($nivelDeSemelhancaSelecionado == 9)                                       echo 'checked="false"';
                               ?>
                               title="9 - Com pouca semelhança"/>
                        <input type="radio" name="semelhanca_avancado" value="10"
                               id="semelhanca_avancado_6" <?php
                                   if($nivelDeSemelhancaSelecionado == 10)
                                       echo 'checked="true"';
                               ?>
                               title="10 - Com muito pouca semelhança"/> 10
    				</div><br />
								  
					</p>
					</div> <!-- fechando class="aba" id="aba2" -->
					
				</div><!-- fechando id="container" -->
					<?php
                    $this->CriarInformacao('<div>Esta busca é útil para encontrar
                        duplicidades e nomes com grafia incorreta.</div><div
                        style="padding-top: 5px"><strong style="color: maroon">
                        Nota:</strong> Inserindo poucas letras, a busca retorna
                        sempre nomes curtos, enquanto que muitas letras retornam
                        nomes grandes.</div><div style="padding-top: 5px">
                        <strong>Ex.:</strong></div><ul style="margin: 0px;
                        padding-left: 20px"><li><code style="color: maroon">
                        Josessa</code> retornará nomes como
                        <code style="color: maroon">José Sá</code> ou <code
                        style="color: maroon">Rose Mar</code> (mas nunca retorna
                        <code style="color: maroon">Josessa Oliveira da Silva
                        </code>)</li><li style="padding-top: 5px"><code
                        style="color: maroon">Maria Ana Alves Mourão Cebatto
                        </code> retorna nomes como <code style="color: maroon">
                        Mariana Alvezz Moura Selibato</code></li></ul><div
                        style="padding-top: 5px"><strong style="color: maroon">
                        Obs:</strong> Procure filtrar ao máximo o seu critério
                        (escolhendo p.ex.: Unidade, ACS, sexo, etc.), pois o
                        mecanismo é um processo de inteligência artificial e
                        pode demorar certo tempo, dependendo da quantidade de
                        indivíduos cadastrados no sistema.</div>');

					$botao = new Vacina();

					$botao->ExibirBotaoBuscar('buscar');
					?>
					
			</form>

		<br />
		<script>
            <?php if($ultimaAba == 'aba1') {
                ?>document.getElementById('container').style.height = '250px';
            <?php
            }
            else {
                ?>document.getElementById('container').style.height = '420px';
            <?php
            }
            ?>
            ExibirAba('<?php echo $ultimaAba ?>','container','ultimaAba');
		</script>
		<?php

      
	}
	//--------------------------------------------------------------------------
	
	public function BuscaAvancada($cidade_id, $unidade_id, $acs_id, $faixaI, $unidadeI,
								  $faixaF, $unidadeF, $dia, $mes, $sexo_avancado,
                                  $nomeParecido_avancado,
                                  $semelhanca_avancado, $pagina_atual = true)
	{

		/////////////////////////////////////////// --- Tratamento de paginação:

		$html = new Html;
		$nomeDaSessao = 'paginacao_listarPessoaAvancado';
		$pagina_atual = $html->TratarPaginaAtual($pagina_atual, $nomeDaSessao);

		$aPartirDe = 15;

		///////////////////////////////////// --- Fim do tratamento de paginação

        // Para guardar o array de resultados na sessao-------------------------
       if( isset($_SESSION['listarPessoa']['ar']) &&
            count($_SESSION['listarPessoa']['ar'])) {

            $totalDeRegistros = $_SESSION['listarPessoa']['totalDeRegistros'];
            $arr = $_SESSION['listarPessoa']['ar'];

			Html::CriarTabelaDeArray($arr);
            
            $html->ControleDePaginacao($totalDeRegistros, $nomeDaSessao,
                        'PessoaVacinavel',
                        "BuscaAvancada($cidade_id, $unidade_id, $acs_id, $faixaI, $unidadeI, $faixaF, $unidadeF, $mae, $mes, $sexo_avancado, $nomeParecido_avancado, $semelhanca_avancado, [paginaAtual])");

            $html->ExibirInformacoesDeRegistrosEncontrados($totalDeRegistros);

            return count($arr);
        }
        // Fim da guarda do array na sessao-------------------------------------
		//echo '<h3> $cidade_id, $unidade_id, $acs_id, $faixai, $unidadeI, $faixaf, $unidadeF, $dia,  $mes </h3>';
		//echo "<h1> $cidade_id, $unidade_id, $acs_id, $faixaI, $unidadeI, $faixaF, $unidadeF, $dia, $mes</h1>";
		
		
		
		$condicoes = $tabelas = false;
		
		// Se foi escolhido um ACS, não precisa nem de unidade nem de cidade
		if($acs_id > 0) {
				
				$condicoes = " AND usuario.Acs_id = $acs_id ";
		}
		
		// Senão, se foi escolhida uma unidade, não precisa de cidade
		elseif($unidade_id > 1)
		{
				$tabelas = ', acs';
				$condicoes = " AND usuario.Acs_id = acs.id";		
		}
		
		// Senão, verifica se uma cidade foi escolhida
		elseif($cidade_id > 1) {
				
				$condicoes = "AND usuario.Acs_id = acs.id
				AND acs.UnidadeDeSaude_id = unidadedesaude.id
				AND unidadedesaude.Bairro_id = bairro.id
				AND bairro.Cidade_id = $cidade_id
				AND bairro.ativo
				AND unidadedesaude.ativo
				AND acs.ativo";
				/*
				$condicoes = "AND usuario.Bairro_id = bairro.id
				AND bairro.Cidade_id = $cidade_id
				AND bairro.ativo";
				*/
				
				$tabelas = ', unidadedesaude, bairro';
		}

		if( $faixaI || $faixaF) {

           
                $tvacina = new Vacina();
                
                $faixaIEmDias = $tvacina->ConvertUnidTempParaDias($faixaI, $unidadeI);




                
                $faixaFEmDias = $tvacina->ConvertUnidTempParaDias($faixaI,
                        $unidadeI);

                $data = new Data();
				//$faixaI = $data->ConverterUnidadeDeTempoParaData($faixaIEmDias, 'day');
				//$faixaF = $data->ConverterUnidadeDeTempoParaData($faixaFEmDias, 'day');
				//---------------------
                // DATA_SUB(CURDATE(),INTERVAL 10 day)

                //$faixaF = $tvacina->ConvertUnidTempParaDias($faixaF + 1,
                        //$unidadeF);


                //---------------------
                // A faixa etária final deve ser de uma unidade de tempo a mais,
                // subtraída de um dia, pois p.ex. quando a pessoa tem 18 anos,
                // pode-se dizer que ela tem 18 anos até que ela faça 19, ou seja
                // 18 anos, 11 meses e 29 dias.
                
		
		$condicoes .= " AND usuario.nascimento BETWEEN
                                   DATE_SUB(
                                        DATE_SUB(CURDATE(),INTERVAL $faixaI $unidadeI),
                                        INTERVAL 1 WEEK
                                    )";
				//$condicoes .= " AND usuario.nascimento BETWEEN '$faixaF' AND '$faixaI'";

		}
		
		if($dia) $condicoes .= " AND DAY(usuario.nascimento) > $dia";

		if($mes) $condicoes .= " AND YEAR(usuario.nascimento) = $mes";

        if($sexo_avancado) $condicoes .= " AND usuario.sexo = ' $sexo_avancado ' ";

   

		$sql = "SELECT id, usuario.nome, usuario.mae, usuario.nascimento
				FROM usuario $tabelas
				WHERE usuario.ativo $condicoes
                        ORDER BY usuario.nome ";
		
		$sqlCount = "SELECT COUNT( usuario.id) FROM usuario $tabelas
						WHERE usuario.ativo = 0 $condicoes";
		

        // Se o usuário digitou algum nome para a busca por nome parecido:
        if($nomeParecido_avançado) {

            $sugestoes = new SugestaoDeTexto();
            $sugestoes->UsarBaseDeDados();
            $arrSugestoes = $sugestoes->ArrayDeDadosBaseadoEmSql($sql);

            $nomesSugeridos = $sugestoes->Sugestoes($nomeParecido_avancado,
                                                    $arrSugestoes,
                                                    $semelhanca_avancado,
                                                    5);
        }

        // Só coloca limite de paginação se não está buscando por nome parecido:
        else $sql .= "  AND ativo LIMIT 5, " . Html::LIMITE;
		//echo "<hr>$sqlCount"; /////// ???????????????????????????????
		//echo "<hr>$sql<hr>"; //////// ??????????????????????????????????????

        // Este array irá conter os dados da listagem de pessoas:
        $arr = array();

        // Se a pessoa digitou algum nome no campo de busca por nome parecido:
        if( isset($nomesSugeridos) ) {

            if( $nomesSugeridos === true) {

                $this->ExibirMensagem("O nome \"$nomeParecido_avancado\"
                    já existe no sistema exatamente como foi digitado.
                    Use a busca simples para nomes exatos.");
            }

            elseif( count($nomesSugeridos) > 0) {

                $totalDeRegistros  = count($nomesSugeridos);
                
                $html->ExibirInformacoesDeRegistrosEncontrados($totalDeRegistros);
                echo '<br/><br/>';
                
                $data = new Data;
                $arr = array(1);
                $crip = new Criptografia();
              //  if( Sessao::Permissao('INDIVIDUOS_EXCLUIR') && Sessao::Permissao('INDIVIDUOS_EDITAR') )
                if (count($nomesSugeridos) > 0)
                {

                    for($i = 1; $i < count($nomesSugeridos); $i++) {

                        $id         = $nomesSugeridos[$i]['id'];
                        $nome       = $nomesSugeridos[$i]['nome'];
                        $nascimento = $nomesSugeridos[$i]['nascimento'];

                        $end = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id");

                        $acaoExcluir = '';
                        $acaoEditar = '';

                        $queryStringEditar = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id");
                        $queryStringExcluir = $crip->Cifrar("pagina=Adm/excluirPessoa&id=$id");

                        if(Sessao::Permissao('INDIVIDUOS_EDITAR'))
                            $acaoEditar = "<a href='$queryStringEditar'>"
                            . "<img src='$this->arquivoGerarIcone?imagem=editar' border='0'
                            border='0' alt='Alterar este Indivíduo' title='Alterar este Indivíduo' /></a>";


                        if(Sessao::Permissao('INDIVIDUOS_EXCLUIR')) {
                            
                            $acaoExcluir = "<a href='$queryStringExcluir'>"
                            . "<img src='$this->arquivoGerarIcone?imagem=excluir' border='0'
                            border='0' alt='Excluir este Indivíduo' title='Excluir este Indivíduo' /></a>";

                            if($this->VerificarSePessoaFoiVacinada($id))
                            $acaoExcluir = "<img src='$this->arquivoGerarIcone?imagem=excluir_desab'
                                border='0' alt='Indivíduo não pode ser excluido, pois já foi vacinado'
                                title='Indivíduo não pode ser excluido, pois já foi vacinado' />";
                        }

                        $arr[$i] = array('Nome'     => "<a href='$end'>$mae</a>",
                                       'Mãe'        => $mae,
                                       'Nascimento' => $data->InverterData($nascimento),
                                       'Ações'      => "$acaoEditar $acaoExcluir"
                        );
                    }

                }
                /*
                elseif( !Sessao::Permissao('INDIVIDUOS_EXCLUIR') && Sessao::Permissao('INDIVIDUOS_EDITAR') )
                {
                    
                    while($stmt->fetch()) {

                    $end = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id");

                    $acaoEditar = '';

                    $queryStringEditar = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id");

                    if( Sessao::Permissao('INDIVIDUOS_EDITAR') ) {

                        $acaoEditar = "<a href='?$queryStringEditar'>"
                            . "<img src='$this->arquivoGerarIcone?imagem=editar' border='0'
                            border='0' alt='Alterar este Indivíduo' title='Alterar este Indivíduo' /></a>";
                    }

                    $arr[] = array('Nome' => "<a href='?$end'>$nome</a>",
                                   'Mãe' => $mae,
                                   'Nascimento' => $data->InverterData($nascimento),
                                   'Ações' => "$acaoEditar");
                    }
                     
                     
                }

                elseif( !Sessao::Permissao('INDIVIDUOS_EXCLUIR') && !Sessao::Permissao('INDIVIDUOS_EDITAR') )
                {
                   
                   while($stmt->fetch()) {

                        $arr[] = array('Nome' => "<a href='?$end'>$nome</a>",
                                       'Mãe' => $mae,
                                       'Nascimento' => $data->InverterData($nascimento));
                    }
                    
                    
                }*/

            }
            else {

                $this->ExibirMensagem("O nome \"$nomeParecido_avancado\"
                    não se parece com nenhum nome cadastrado anteriormente.
                    Verifique se os filtros (como Unidade de atendimento, ACS, sexo,
                    etc.) foram informados corretamente. Em caso afirmativo,
                    tente aumentar a folga de semelhança. A fornecida
                    foi $semelhanca_avancado.");
            }
        }

        // Se não está procurando por nome parecido,
        else {
            // Contando os registros para a paginação:
            $stmt = $this->conexao->prepare($sqlCount)
                or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $totalDeRegistros = 0;
            $stmt->bind_result($totalDeRegistros);

            $stmt->execute();
            $stmt->fetch();
            $stmt->free_result();

            if($totalDeRegistros == 0) return 1; // Só prossegue se tiver registros.

            // Fim da contagem dos registros para a apaginação.

            $stmt = $this->conexao->prepare($sql)
                or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $stmt->bind_result($id, $mae, $nome, $nascimento);
            $stmt->execute();
            $existe = $stmt->num_rows;

            if($existe > 0) {

                $html->ControleDePaginacao($totalDeRegistros, $nomeDaSessao,
                            'PessoaVacinavel',
                            "BuscaAvancada($cidade_id, $unidade_id, $acs_id, $faixaI, $unidadeI, $faixaF, $unidadeF, $dia, $mes, $sexo_avancado, $nomeParecido_avancado, $semelhanca_avancado, [paginaAtual])");

                $html->ExibirInformacoesDeRegistrosEncontrados($totalDeRegistros);
                echo '<br /><br />';

                $data = new Data;
                $arr = array();
                $crip = new Criptografia();

               // if( Sessao::Permissao('INDIVIDUOS_EXCLUIR') && Sessao::Permissao('INDIVIDUOS_EDITAR') )
                while($stmt->fetch()) {

                    $end = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id");

                    $acaoExcluir = '';
                    $acaoEditar = '';

                    $queryStringEditar = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id");
                    $queryStringExcluir = $crip->Cifrar("pagina=Adm/excluirPessoa&id=$id");

                    if(Sessao::Permissao('INDIVIDUOS_EDITAR'))
                    $acaoEditar = "<a href='?$queryStringEditar'>"
                        . "<img src='$this->arquivoGerarIcone?imagem=editar' border='0'
                        border='0' alt='Alterar este Indivíduo' title='Alterar este Indivíduo' /></a>";


                    if(Sessao::Permissao('INDIVIDUOS_EXCLUIR')) {

                        $acaoExcluir = "<a href='?$queryStringExcluir'>"
                            . "<img src='$this->arquivoGerarIcone?imagem=excluir' border='0'
                            border='0' alt='Excluir este Indivíduo' title='Excluir este Indivíduo' /></a>";

                        if($this->VerificarSePessoaFoiVacinada($id))
                        $acaoExcluir = "<img src='$this->arquivoGerarIcone?imagem=excluir_desab'
                            border='0' alt='Indivíduo não pode ser excluido, pois já foi vacinado'
                            title='Indivíduo não pode ser excluido, pois já foi vacinado' />";
                    
                    
                    }
                    
                    $arr[] = array('nome' => "<a href='?$end'>$nome</a>",
                                   'mãe' => $mae,
                                   'nascimento' => $data->InverterData($nascimento),
                                   'ações' => "$acaoEditar $acaoExcluir");
                }
                /*
                elseif( !Sessao::Permissao('INDIVIDUOS_EXCLUIR') && Sessao::Permissao('INDIVIDUOS_EDITAR') )
                while($stmt->fetch()) {

                    $end = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id");

                    $acaoEditar = '';

                    $queryStringEditar = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id");

                    if( Sessao::Permissao('INDIVIDUOS_EDITAR') ) {

                        $acaoEditar = "<a href='?$queryStringEditar'>"
                            . "<img src='$this->arquivoGerarIcone?imagem=editar' border='0'
                            border='0' alt='Alterar este Indivíduo' title='Alterar este Indivíduo' /></a>";
                    }

                    $arr[] = array('nome' => "<a href='?$end'>$nome</a>",
                                   'mãe' => $mae,
                                   'nascimento' => $data->InverterData($nascimento),
                                   'ações' => "$acaoEditar");
                }

                elseif( !Sessao::Permissao('INDIVIDUOS_EXCLUIR') && !Sessao::Permissao('INDIVIDUOS_EDITAR') )
                while($stmt->fetch()) {

                    $arr[] = array('nome' => "<a href='?$end'>$nome</a>",
                                   'mãe' => $mae,
                                   'nascimento' => $data->InverterData($nascimento));
                }*/
            }

            $stmt->free_result();
            //Depurador::Pre($sql);
        }
		if( count($arr) ) {

            $_SESSION['listarPessoa']['arr'] = $arr;
            $_SESSION['listarPessoa']['totalDeRegistros'] = $totalDeRegistros;
            Html::CriarTabelaDeArray($arr);
        }
		
	}
	
	//--------------------------------------------------------------------------
	public function ExibirFormularioExcluirPessoa($id)
	{
		?>
		<div align="left">
			<form id="formulario1" name="formulario1" method="post"
			  action="<?php echo $_SERVER['REQUEST_URI']?>">
			<?php

				$dados = $this->SelecionarDadosUsuario($id);

				$nome = $dados[5];
				$nascimento = $dados[8];
				$mae = $dados[15];

				$data = new Data();

				echo '<h3 align="center">Confirmação para excluir</h3>';
				echo "<br /><br /><b>Nome:</b> $nome ";
				echo "<br /><br /><b>Mãe:</b> $mae ";
				echo "<br /><br /><b>Nascimento: </b>". $data->InverterData($nascimento);

				echo "<br /><br />";

				$botao = new Vacina();

				$botao->ExibirBotoesDoFormulario('Excluir', false, 'excluir');

				echo '<hr />';

				$botao->ExibirBotaoVoltar('Voltar', 'pagina=Adm/listarPessoa');

			?>
			</form>

		</div>
		<?php
	}
	///////////////////////////////// SELECIONAR ///////////////////////////////
	private function SelecionarDadosUsuario($id)
	{
        $sql = 'SELECT Etnia_id, Ddd_id, Bairro_id, Ocupacao_cbo_id, Acs_id, nome, '
             . 'prontuario, sexo, nascimento, email, telefone, logradouro, cep, '
             . 'cartaosus, cpf, mae, pai, acamado, vacinavel '
             .     'FROM `usuario` '
             .     'WHERE id = ? '
             .     'AND ativo';

		$resultado = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_param('i', $id);

		$resultado->bind_result($etnia_id, $ddd_id, $bairro_id, $ocupacao_id,
		$acs_id, $nome, $prontuario, $sexo, $nascimento, $email, $telefone,
		$logradouro, $cep, $cartaosus, $cpf, $mae, $pai, $acamado, $vacinavel);

		$resultado->execute();

		$resultado->store_result();

		$resultado->fetch();

		$existe = $resultado->num_rows;

		$resultado->free_result();
		
		if($existe > 0) {
			
			$sql = 'SELECT UnidadeDeSaude_id FROM `acs` WHERE id = ?';
			
			$stmt = $this->conexao->prepare($sql)
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$stmt->bind_param('i', $acs_id);
			$stmt->bind_result($unidadeDeSaude_id);
			$stmt->execute();
			$stmt->store_result();
			$existe = $stmt->num_rows;
			$stmt->fetch();
			
			$stmt->free_result();
			
			if($existe > 0) {

				return array($etnia_id, $ddd_id, $bairro_id, $ocupacao_id,
							 $acs_id, $nome, $prontuario, $sexo, $nascimento, $email,
							 $telefone, $logradouro, $cep, $cartaosus, $cpf, $mae,
							 $pai, $unidadeDeSaude_id, $acamado, $vacinavel);
			}
		
			if($existe == 0) {
				
				$this->AdicionarMensagemDeErro('Não foi possível selecionar a
					unidade de saúde. A identificação parece não existir.');
			}
			if($existe < 0) {
				
				$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar
					a unidade de saúde.');
			}
		}
		
		if($existe == 0) {
			
			$this->AdicionarMensagemDeErro('Não foi possível selecionar os dados
				deste indivíduo. A identificação do mesmo parece não existir.');
		}
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao selecionar os
				dados deste indivíduo.');
		}
		return false;
	}
	//--------------------------------------------------------------------------
	private function SelecionarACS($acs_id = false)
	{

		// Administrador não master, exibe só o editar e os ACS da unidade dele:
		if( Sessao::Permissao('ACS_LISTAR') == 2 ) {
			
			$sql = "SELECT id, nome	FROM `acs` WHERE acs.UnidadeDeSaude_id =
				{$_SESSION['unidadeDeSaude_id']} AND ativo ORDER BY nome";
		}
		
		// Administrador nível master:
		elseif( Sessao::Permissao('ACS_LISTAR') == 1 ) {
			
			$sql = "SELECT acs.id, acs.nome FROM `acs`, `unidadedesaude` WHERE
				acs.UnidadeDeSaude_id = unidadedesaude.id AND acs.ativo
				AND unidadedesaude.ativo ORDER BY nome";
			
		}
		
		$resultado = $this->conexao->prepare($sql)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$resultado->bind_result($id, $nome);
		
		$resultado->execute();
		


		while( $resultado->fetch() ) {

			$selecionado = '';
			if($acs_id == $id) {
				$selecionado = 'selected="true"';
			}
			echo "<option value='$id' $selecionado>".Html::FormatarMaiusculasMinusculas($nome)."</option>";
		}
		$resultado->free_result();
	}
	//--------------------------------------------------------------------------

	protected function SelecionarOcupacao($profissao_id = false)
	{
		$resultado = $this->conexao->prepare('SELECT id, descricao FROM
			`ocupacao_cbo` ORDER BY descricao')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->execute();

		$resultado->bind_result($id, $descricao);

		while( $resultado->fetch() ) {

			$selecionado = '';
			if($profissao_id == $id) {
				$selecionado = 'selected="true"';
			}
			echo "<option value='$id' $selecionado>$descricao</option>";
		}

		$resultado->free_result();
	}
	//--------------------------------------------------------------------------
	protected function SelecionarEstado($estado_id = false)
	{
		$resultado = $this->conexao->prepare('SELECT id, nome FROM `estado`')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_result($id, $nome);

		$resultado->execute();

		while( $resultado->fetch() ) {

			$selecionado = '';
			if($estado_id == $id) {
				$selecionado = 'selected="true"';
			}
			echo "<option value='$id' $selecionado>$nome</option>";
		}

		$resultado->free_result();
	}
	//--------------------------------------------------------------------------
	public function SelecionarDdd($ddd = false)
	{
		$resultado = $this->conexao->prepare('SELECT id FROM `ddd`')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->execute();

		$resultado->bind_result($id);

		while($resultado->fetch()) {

			$selecionado = '';
			if($ddd == $id) {
				$selecionado = 'selected="true"';
			}
			echo "<option value='$id' $selecionado>$id</option>";
		}

		$resultado->free_result();
	}
	//--------------------------------------------------------------------------
	protected function SelecionarEtnia($etnia = false)
	{
		$resultado = $this->conexao->prepare('SELECT id, nome FROM `etnia` ORDER BY nome')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->execute();

		$resultado->bind_result($id, $nome);

		while($resultado->fetch()) {

			$selecionado = '';
				if($etnia == $id) {
					$selecionado = 'selected="true"';
				}
			echo "<option value='$id' $selecionado>$nome</option>";

		}
		$resultado->free_result();
	}

	//--------------------------------------------------------------------------
	public function SelecionarEstadoCidade($usuario_id = false) {
		///////////////////// ?????? ta usando????
		$resultado = $this->conexao->prepare('SELECT estado.id, bairro.Cidade_id
			FROM `estado` , `bairro`, `cidade`, `usuario` WHERE
			
			usuario.Bairro_id = bairro.id AND  bairro.Cidade_id = cidade.id
			AND cidade.Estado_id = estado.id AND usuario.id = ?
			AND bairro.ativo AND usuario.ativo')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_param('i', $usuario_id);
		$resultado->bind_result($estado_id, $cidade_id);
		$resultado->execute();
		$resultado->fetch();
		$resultado->free_result();

		return array ($estado_id, $cidade_id);
	}
	//--------------------------------------------------------------------------
	///////////////////////////////// VERIFICAR ////////////////////////////////

	private function VerificarSeBairroExiste()
	{
			$resultado = $this->conexao->prepare('SELECT id FROM `bairro`
				WHERE nome = ? AND Cidade_id = ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$bairro = $this->Bairro();
			$cidade = $this->Cidade();

			$resultado->bind_param('ss', $bairro, $cidade);
			$resultado->bind_result($id);

			$resultado->execute();
			
			$resultado->store_result();
	
			$existe = $resultado->num_rows;
			
			if($existe > 0) {
				
				$resultado->fetch();

				$resultado->free_result();
	
				if($id){
					return $id;
				}
			}
			if($existe == 0) {

				return false; // Bairro não existe
			}
			if($existe < 0) {

				$this->AdicionarMensagemDeErro("Algum erro ocorreu ao verificar
					se o bairro $bairro existe.");
					
				return false;
			}
	}
	//--------------------------------------------------------------------------
	public function VerificarSePessoaFoiVacinada($usuario_id)
	{
		$sql = "SELECT COUNT(id) FROM usuariovacinado WHERE Usuario_id = ?";
		$stmt = $this->conexao->prepare($sql);
		$stmt->bind_param('i', $usuario_id);
		$stmt->bind_result($jaVacinado);
		$stmt->execute();
		$stmt->fetch();
		$stmt->free_result();
		return $jaVacinado;
		
	}
	//--------------------------------------------------------------------------
	public function VerificarNaoDuplicidadeDePessoa($id = false)
	{
		$data = new Data(); // Para inverter a data
		
		$nome = $this->Nome();
		$mae = $this->Mae();
		$nasc = $data->InverterData($this->nascimento);
		
		// Busca não restrita por causa de CPF nulos ou "0":
		$cpf  = '%' . Preparacao::RemoverSimbolos($this->Cpf()) . '%';

		if($id) {
			$stmt = $this->conexao->prepare('SELECT id FROM `usuario`
				WHERE nome = ? AND nascimento = ? AND cpf LIKE ?
				AND mae = ? AND id <> ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$stmt->bind_param('ssssi', $nome, $nasc, $cpf, $mae, $id);
		}
		else {
			$stmt = $this->conexao->prepare('SELECT id FROM `usuario`
				WHERE nome = ? AND nascimento = ? AND cpf LIKE ? AND mae = ? AND ativo')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$stmt->bind_param('ssss', $nome, $nasc, $cpf, $mae);
		}

		$stmt->execute();
		$stmt->store_result();

		$registroJaExiste = $stmt->num_rows;

		$stmt->free_result();

		// Para exibir o CPF na mensagem de erro para o usuário do sistema:
		if( strlen($this->Cpf()) > 5 ) $cpf = ", CPF: {$this->Cpf()}";
		else                           $cpf = " (CPF não preenchido)";
		
		if($registroJaExiste > 0) {
			
			$this->AdicionarMensagemDeErro("Já existe o registro de $nome,
				nascido em {$this->nascimento} $cpf.");
			
			return false;
			
		}
		if($registroJaExiste == 0) return true;
		if($registroJaExiste < 0) {
			
			$this->AdicionarMensagemDeErro("Algum erro ocorreu ao verificar a
				não duplicidade de indivíduo($nome,
				nascido em {$this->nascimento} $cpf.)");
			
			return false;
		}

		return true;
	}
	//--------------------------------------------------------------------------
	public function SelecionarEnderecoBairroId($usuario_id)
	{

		$resultado = $this->conexao->prepare("SELECT logradouro, Bairro_id FROM
		`usuario` WHERE id = ? AND ativo")
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_param('i', $usuario_id);
		$resultado->bind_result($logradouro, $bairro_id);
		$resultado->execute();
		$resultado->fetch();
		$resultado->free_result();

		return array($logradouro, $bairro_id);
	}
	//--------------------------------------------------------------------------
	public function SelecionarBairro($usuario_id)
	{
		$resultado = $this->conexao->prepare("SELECT bairro.nome FROM `bairro`,
			`usuario` WHERE usuario.id = ? AND usuario.Bairro_id = bairro.id
			 AND bairro.ativo  AND usuario.ativo")
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_param('i', $usuario_id);
		$resultado->bind_result($bairro);
		$resultado->execute();
		$resultado->fetch();
		$resultado->free_result();

		return $bairro;
	}
	//--------------------------------------------------------------------------
	public function SelecionarCep($usuario_id)
	{
		$resultado = $this->conexao->prepare("SELECT cep FROM `usuario`
			WHERE id = ? AND ativo")
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_param('i', $usuario_id);
		$resultado->bind_result($cep);
		$resultado->execute();
		$resultado->fetch();
		$resultado->free_result();

		return $cep;
	}

	//--------------------------------------------------------------------------
	///////////////////////////////// LISTAR //////////////////////////////////
	/**
	 * Lista determinadas pessoas
	 *
	 */
	public function ListarPessoa($pesquisa, $mae, $cpf, $cidade_id, $nasc,
								 $pagina_atual = false)
	{

		$html = new Html;
		$nomeDaSessao = 'paginacao_listarPessoa';
		$pagina_atual = $html->TratarPaginaAtual($pagina_atual, $nomeDaSessao);

		$aPartirDe = ($pagina_atual - 1) * Html::LIMITE;

        // Para guardar o array de resultados na sessao-------------------------
       if( isset($_SESSION['listarPessoa']['arr']) &&
            count($_SESSION['listarPessoa']['arr']) && count($_POST) == 0) {

            $totalDeRegistros = $_SESSION['listarPessoa']['totalDeRegistros'];
            $arr = $_SESSION['listarPessoa']['arr'];

			Html::CriarTabelaDeArray($arr);
            
			$html->ControleDePaginacao($totalDeRegistros, $nomeDaSessao,
						'PessoaVacinavel',
						"ListarPessoa($pesquisa, $mae, $cpf, $cidade_id, $nasc, [paginaAtual])");


            $html->ExibirInformacoesDeRegistrosEncontrados($totalDeRegistros);

            return count($arr);
        }
        // fim da guarda de array de resultados da sessao-----------------------
		/*
		if( !$pagina_atual && isset( $_SESSION['paginaAtual_listarPessoa']) )
				$pagina_atual = $_SESSION['paginaAtual_listarPessoa'];
		
		if( !$pagina_atual && !isset( $_SESSION['paginaAtual_listarPessoa']) )
				$pagina_atual = 1;
		
		if( !isset( $_SESSION['paginaAtual_listarPessoa']) )
				$_SESSION['paginaAtual_listarPessoa'] = 1;
		
		else $_SESSION['paginaAtual_listarPessoa'] = $pagina_atual;
	 */
		
		$nome = $this->conexao->real_escape_string(trim($pesquisa));
		$mae = $this->conexao->real_escape_string(trim($mae));

		$explodeCaracteres = explode(' ',$nome);
		$implodeCaracteres = implode('%',$explodeCaracteres);

		$nome = "$implodeCaracteres";

		$explodeCaracteres = explode(' ',$mae);
		$implodeCaracteres = implode('%',$explodeCaracteres);

		$mae = "$implodeCaracteres";
		
		$sqlMae = false;
		if($mae != 'vazio') $sqlMae = "AND (usuario.mae LIKE '%$mae%' OR '$mae' = '%vazio%')";
		
		$sqlNome = false;
		/*if( strlen($nome) > 2)*/ $sqlNome = " usuario.nome LIKE '$nome%' AND";
		
		////////////////////////////////////////////////////////////////////////
		
		$sql = "SELECT COUNT(usuario.id)
			FROM `usuario`, `cidade`, `bairro`
			WHERE (usuario.cpf = ? OR '$cpf' = 'vazio')
			AND (usuario.nascimento = ? OR '$nasc' = 'vazio')
			$sqlMae
			AND usuario.Bairro_id = bairro.id
			AND bairro.Cidade_id = cidade.id
			AND cidade.id = ?
			AND $sqlNome usuario.ativo AND bairro.ativo";
		
		$stmt = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));


	    $stmt->bind_param('ssi', $cpf, $nasc, $cidade_id);
	    
		$totalDeRegistros = 0;
		$stmt->bind_result($totalDeRegistros);

		$stmt->execute();

	 	$stmt->fetch();
		
		$stmt->free_result();

		if($totalDeRegistros == 0) return 0;
		
		////////////////////////////////////////////////////////////////////////

		$data = new Data();
		
		/*echo '<pre>';
		print_r($_POST);
		print_r($_SESSION);
		echo '</pre>';*/
	
		////////////////////////////////////////////////////////////////////////
		//$nomeDaSessao = 'paginaAtual_listarPessoa';
		
		/*
		if( !$pagina_atual && isset( $_SESSION[$nomeDaSessao]) )
				$pagina_atual = $_SESSION[$nomeDaSessao];
		
		if( !$pagina_atual && !isset( $_SESSION[$nomeDaSessao]) )
				$pagina_atual = 1;
		
		if( !isset( $_SESSION[$nomeDaSessao]) )
				$_SESSION[$nomeDaSessao] = 1;
		
		else $_SESSION[$nomeDaSessao] = $pagina_atual;
		*/
		
		
		///////////////////////////////////////////////////////////////////////
		
		$limite = Html::LIMITE;
		
		$sql = "SELECT usuario.id, usuario.nome, usuario.mae,
			usuario.nascimento, usuario.vacinavel
			FROM `usuario`, `cidade`, `bairro`
			WHERE (usuario.cpf = ? OR '$cpf' = 'vazio')
			AND (usuario.nascimento = ? OR '$nasc' = 'vazio')
			$sqlMae
			AND usuario.Bairro_id = bairro.id
			AND bairro.Cidade_id = cidade.id
			AND cidade.id = ?
			AND $sqlNome usuario.ativo AND bairro.ativo
			ORDER BY usuario.nome
		    LIMIT $aPartirDe, ?";
		
		$resultado = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

	    $resultado->bind_param('ssii', $cpf, $nasc, $cidade_id, $limite);
	    
		$resultado->bind_result($id, $nome, $mae, $nascimento, $vacinavel);

		$resultado->execute();

		$resultado->store_result();

		$linhas = $resultado->num_rows;

		if ($linhas > 0) {

            $estilo = $fimEstilo = '';
            
		    ////////////////////////
			$html->ControleDePaginacao($totalDeRegistros, $nomeDaSessao,
						'PessoaVacinavel',
						"ListarPessoa($pesquisa, $mae, $cpf, $cidade_id, $nasc, [paginaAtual])");
			///////////////////////
			
			$arr = array();

			$crip = new Criptografia();
			
			if( Sessao::Permissao('INDIVIDUOS_EXCLUIR') && Sessao::Permissao('INDIVIDUOS_EDITAR') )

			while( $resultado->fetch() ) {
								
				$end = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id");

				$acaoExcluir = '';
				$acaoEditar = '';
			
				$queryStringEditar = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id"); 
				$queryStringExcluir = $crip->Cifrar("pagina=Adm/excluirPessoa&id=$id");
				//$queryStringExcluir = $crip->Cifrar("pagina=Adm/inativarPessoa&usuarioId=$id");
	
				$acaoEditar = "<a href='?$queryStringEditar'>"
					. "<img src='$this->arquivoGerarIcone?imagem=editar' border='0'
					border='0' alt='Alterar este Indivíduo' title='Alterar este Indivíduo' /></a>";
				
				
				$acaoExcluir = "<a href='?$queryStringExcluir'>"
					. "<img src='$this->arquivoGerarIcone?imagem=excluir' border='0'
					border='0' alt='Excluir este Indivíduo' title='Excluir este Indivíduo' /></a>";
                /*
				if($this->VerificarSePessoaFoiVacinada($id)) 
				$acaoExcluir = "<img src='$this->arquivoGerarIcone?imagem=excluir_desab'
					border='0' alt='Indivíduo não pode ser excluido, pois já foi vacinado' 
					title='Indivíduo não pode ser excluido, pois já foi vacinado' />";
				*/
                
				if(!$mae) $mae = "<em><span style='color: #CCC'>Não Informada</span></em>";

                if(!$vacinavel)
                {
                    $estilo = "<span style='color: #AAA' "
                            . "title='Indivíduo não pode ser excluido ou "
                            . "inabilitado, pois já foi desativado no sistema'>";
                            
                    $fimEstilo = "</span>";

                    $acaoExcluir = "<img src='$this->arquivoGerarIcone?imagem=excluir_desab' "
                                 . "border='0' alt='Indivíduo não pode ser excluido ou "
                                 . "inabilitado, pois já foi desativado no sistema' "
                                 . "title='Indivíduo não pode ser excluido ou "
                                 . "inabilitado, pois já foi desativado no sistema' />";
                }
                else $estilo = $fimEstilo = '';

				$arr[] = array('id'    	=> $id,
					       'nome'  		=> "<a href='?$end'>{$estilo}{$nome}{$fimEstilo}</a>",
					       'mãe'   		=> "{$estilo}{$mae}{$fimEstilo}",
					       'nascimento' => $estilo . $data->InverterData($nascimento) . $fimEstilo,
					       'ações' 		=> "$acaoEditar $acaoExcluir");
			}
			
			elseif( !Sessao::Permissao('INDIVIDUOS_EXCLUIR') && Sessao::Permissao('INDIVIDUOS_EDITAR') )

			while( $resultado->fetch() ) {
				
				$end = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id");
		
				$acaoEditar = '';
			
				$queryStringEditar = $crip->Cifrar("pagina=Adm/editarPessoa&id=$id"); 
	
				if( Sessao::Permissao('INDIVIDUOS_EDITAR') ) {
	
				$acaoEditar = "<a href='?$queryStringEditar'>"
						. "<img src='$this->arquivoGerarIcone?imagem=editar' border='0'
						border='0' alt='Alterar este Indivíduo' title='Alterar este Indivíduo' /></a>";
				}
				
				if(!$mae) $mae = "<em><span style='color: #CCC'>Não Informada</span></em>";
				$arr[] = array('id'    	=> $id,
					       'nome'  		=> "<a href='?$end'>{$estilo}{$nome}{$fimEstilo}</a>",
					       'mãe'   		=> "{$estilo}{$mae}{$fimEstilo}",
					       'nascimento'	=> $estilo . $data->InverterData($nascimento) . $fimEstilo,
					       'ações' 		=> "$acaoEditar");
			}
			
			elseif( Sessao::Permissao('INDIVIDUOS_EXCLUIR') && !Sessao::Permissao('INDIVIDUOS_EDITAR') )
			while( $resultado->fetch() ) {
				
				$acaoExcluir = '';
			
				$queryStringExcluir = $crip->Cifrar("pagina=Adm/excluirPessoa&id=$id"); 
	
				$acaoExcluir = "<a href='?$queryStringExcluir'>"
					. "<img src='$this->arquivoGerarIcone?imagem=excluir' border='0'
					border='0' alt='Excluir este Indivíduo' title='Excluir este Indivíduo' /></a>";
				
				
				if(!$mae) $mae = "<em><span style='color: #CCC'>Não Informada</span></em>";
				$arr[] = array('id'    	=> $id,
					       'nome'  		=> "{$estilo}{$nome}{$fimEstilo}",
					       'mãe'   		=> "{$estilo}{$mae}{$fimEstilo}",
					       'nascimento' => $estilo . $data->InverterData($nascimento) . $fimEstilo,
					       'ações' 		=> "$acaoExcluir");
			}
			
			elseif( !Sessao::Permissao('INDIVIDUOS_EXCLUIR') && !Sessao::Permissao('INDIVIDUOS_EDITAR') ) 
			while( $resultado->fetch() ) {
				
				if(!$mae) $mae = "<em><span style='color: #CCC'>Não Informada</span></em>";
				$arr[] = array('id'    	=> $id,
					       'nome'  		=> "{$estilo}{$nome}{$fimEstilo}",
					       'mãe'   		=> "{$estilo}{$mae}{$fimEstilo}",
					       'nascimento' => $estilo . $data->InverterData($nascimento) . $fimEstilo);
			}
            $_SESSION['listarPessoa']['arr'] = $arr;
            $_SESSION['listarPessoa']['totalDeRegistros'] = $totalDeRegistros;

			Html::CriarTabelaDeArray($arr);	
			
			$resultado->free_result();
			
			return $totalDeRegistros;
		}
				
		$resultado->free_result();
		
		if($linhas == 0) {
			
			return $totalDeRegistros;
		}
		
		if($linhas < 0) {
			
			$this->AdicionarMensagemDeErro("Algum erro ocorreu ao tentar buscar
				dados com o critério $pesquisa!");
				
			return false;
		}
	}
	///////////////////////////////// DIVERSOS ////////////////////////////////

	/**
	 * Conexão com a Base de Dados
	 *
	 */
	public function UsarBaseDeDados()
	{

		$this->conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
			$_SESSION['senha']);


		$this->conexao->select_db($_SESSION['banco']);
	}
	//--------------------------------------------------------------------------
	public function ValidarFormulario($nomeDoFormulario)
	{
		switch($nomeDoFormulario) {

			case 'inserirPessoa':
			case 'editarPessoa':

				$nomeValido = $this->ValidarNomeDaPessoa( $_POST['nome'] );
				$sexoValido = $this->ValidarSexoDaPessoa( $_POST['sexo'] );
				$acamadoValido = $this->ValidarAcamadoDaPessoa( $_POST['acamado'] );
				$etniaValida = $this->ValidarEtniaDaPessoa( $_POST['etnia'] );
				$nascValido = $this->ValidarDataDaPessoa( $_POST['datadenasc'] );
				$dddValido = $this->ValidarDddDaPessoa( $_POST['ddd'] );
				$telefoneValido = $this->ValidarTelefoneDaPessoa( $_POST['telefone'] );
				$cpfValido = $this->ValidarCpfDaPessoa( $_POST['cpf'] );
				$estadoValido = $this->ValidarEstadoDaPessoa( $_POST['estadouf'] );
				$cidadeValida = $this->ValidarCidadeDaPessoa( $_POST['cidade'] );
				$logradouroValido = $this->ValidarLogradouroDaPessoa( $_POST['endereco'] );
				$bairroValido = $this->ValidarBairroDaPessoa( $_POST['bairro'] );
				$cepValido = $this->ValidarCepDaPessoa( $_POST['cep'] );
				$cartaoSusValido = $this->ValidarCartaoSusDaPessoa( $_POST['cartaosus'] );
				$prontuarioValido = $this->ValidarProntuarioDaPessoa( $_POST['prontuario'] );
				$profissaoValida = $this->ValidarProfissaoDaPessoa( $_POST['profissao'] );
				$maeValida = $this->ValidarMaeDaPessoa( $_POST['nomedamae'] );
				$paiValido = $this->ValidarPaiDaPessoa( $_POST['nomedopai'] );
				$emailValido = $this->ValidarEmail($_POST['email'], true /*opcional*/);
				$acsValido = $this->validarAgenteComunitarioDaPessoa( $_POST['acs'] );
				
				if( $nomeValido && $sexoValido && $etniaValida && $nascValido
					 && $dddValido && $telefoneValido && $cpfValido
				     && $estadoValido && $cidadeValida && $logradouroValido
				     && $bairroValido && $cepValido && $cartaoSusValido
				     && $prontuarioValido && $profissaoValida && $maeValida
				     && $paiValido && $emailValido && $acsValido && $acamadoValido )
				     
				     return true;
				break;

			case 'pesquisarPessoa':
			case 'vacinarPessoa':
				
				if(strlen($_POST['pesquisa']) > 2) 
				$pesquisaValida = $this->ValidarPesquisaDePessoa( $_POST['pesquisa'] );
				else $pesquisaValida = true;
				
				$cpfValido 		= $this->ValidarCpfDaPessoa( $_POST['cpf'] );
				$nascValido		= $this->ValidarDataDaPessoa( $_POST['datadenasc'], true );
				
				if ( $pesquisaValida && $cpfValido && $nascValido  ) {
					return true;
				}
				break;

			case 'intercorrencia':
				if ( $this->ValidarPesquisaDePessoa( $_POST['nome'] ) ) {
					return true;
				}
				break;

            case 'inativarPessoa':
                if ( $this->ValidarInativacaoDePessoa( $_POST['motivo'], 5 ) ) {
					return true;
				}
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
	public function ValidarNomeDaPessoa($nome)
	{
		$permitidos = " 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
						çÇáéíóúàèìòùâêîôûäëïöüãõÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÃÕ.-";
		if( strlen($nome) > 2  &&
		    strlen($nome) == strspn($nome, $permitidos) &&
		    !ctype_digit($nome[0])) {
			return true;
		}
		$this->AdicionarMensagemDeErro("Nome $nome é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarMaeDaPessoa($nome)
	{
		$permitidos = " 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
						çÇáéíóúàèìòùâêîôûäëïöüãõÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÃÕ.-";
		if(( strlen($nome) > 2  &&
			strlen($nome) == strspn($nome, $permitidos) ) ||
			strlen($nome) == 0 ) {
			return true;
		}
		$this->AdicionarMensagemDeErro("Nome $nome é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarPaiDaPessoa($nome)
	{
		$permitidos = " 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
						çÇáéíóúàèìòùâêîôûäëïöüãõÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÃÕ.-";
		if(( strlen($nome) > 2  &&
			strlen($nome) == strspn($nome, $permitidos) ) ||
			strlen($nome) == 0 ) {
			return true;
		}
		$this->AdicionarMensagemDeErro("Nome $nome é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarDataDaPessoa($data, $opcional = true)
	{
		if($opcional && strlen($data) == 0) return true;
		
		$ponteiro = 0;
		$permitidos = "0123456789/";
		if ( strlen($data) == 10 &&
		    strspn($data, $permitidos) == 10 ) {
			for ($ponteiro = 0;$ponteiro < 10;$ponteiro++ ) {
				if ( $ponteiro != 2 && $ponteiro != 5 ) {
					if ( !ctype_digit( $data[$ponteiro] ) ) {
						$this->AdicionarMensagemDeErro("Data $data é inválida");
						return false;
					}
				} else {
					if ( $data[$ponteiro] != '/' ) {
						$this->AdicionarMensagemDeErro("Data $data é inválida");
						return false;
					}
				}
			}
			list($dia, $mes, $ano) = preg_split('@[^0-9]{1}@', $data);
			if( checkdate( $mes, $dia, $ano ) ) {
				return true;
			}
		}
		$this->AdicionarMensagemDeErro("Data $data é inválida");
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarBairroDaPessoa($bairro)
	{
		$permitidos = " 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
		  			  çÇáéíóúàèìòùâêîôûäëïöüãõÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÃÕ.-,º";
		if( strlen($bairro) > 2  &&
			strlen($bairro) == strspn($bairro, $permitidos) ) {
			return true;
		}
		$this->AdicionarMensagemDeErro("Bairro $bairro é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarLogradouroDaPessoa($logradouro)
	{
		$permitidos = " 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
				 	  çÇáéíóúàèìòùâêîôûäëïöüãõÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÃÕ,.-º():;/\\";
		if( strlen($logradouro) > 2  &&
			strlen($logradouro) == strspn($logradouro, $permitidos) ) {
			return true;
		}
		$this->AdicionarMensagemDeErro("Endereço $logradouro é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarDddDaPessoa($ddd) {
		if ( ( strlen($ddd) == 2 && ctype_digit($ddd) ) || strlen($ddd) == 0 ) {
			return true;
		}
		$this->AdicionarMensagemDeErro('Selecione o DDD corretamente');
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarTelefoneDaPessoa($telefone)
	{
		if ( strlen( $telefone ) == 0 ) {
			return true;
		}
		$permitidos = " 0123456789-";
		if(
			strlen($telefone) == 9 &&
			strlen($telefone) == strspn($telefone, $permitidos) &&
			$telefone[4] == '-'
		  ) {
			return true;
		}
		$this->AdicionarMensagemDeErro("Telefone $telefone é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarCepDaPessoa($cep)
	{
		$permitidos = "0123456789-";
		if( (( strlen($cep) == 9 ) &&
		  ( strlen($cep) == strspn($cep, $permitidos) ) &&
		  ( $cep[5] == '-' )) || strlen($cep) == 0 ) {
			return true;
		}
		$this->AdicionarMensagemDeErro("CEP $cep é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	public function FormatoDeEmailValido($email)
	{
		$er1 = '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])'
			 . '+([a-zA-Z0-9\._-]+)+$/';
	
		// Abaixo verifica também os dominios válidos existentes, tipo .com, .nl:
		$er2 = '/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*'
		     . '[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|'
		     . 'arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|'
		     . 'bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|'
		     . 'co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|'
		     . 'ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|'
		     . 'gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|'
		     . 'id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|'
		     . 'km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|'
		     . 'mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|'
		     . 'mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|'
		     . 'om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|'
		     . 'ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|'
		     . 'sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|'
		     . 'us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)|(([0-9]'
		     . '[0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9]'
		     . '[0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i';
	
		if ( preg_match($er1, $email) &&  preg_match($er2, $email)) return true;
	
		return false;
	}
	//------------------------------------------------------------------------------
	public function DominioDeEmailExiste($email)
	{
		$dominio = explode('@', $email);
	
		if( checkdnsrr($dominio[1], 'MX')) return 1;
		if( checkdnsrr($dominio[1], 'A')) return 2;
		if( checkdnsrr($dominio[1], 'CNAME') ) return 3;
		if( gethostbyname($dominio[1]) != $dominio[1] ) return 4;
	
		return false;
	}
	//------------------------------------------------------------------------------
	public function  ValidarEmail($email, $opcional = false)
	{
		if( $opcional && strlen($email) == 0 ) return true;
		
		if( strlen($email) > 5 ) {
				
			if( $this->FormatoDeEmailValido($email)
				&& $this->DominioDeEmailExiste($email) // Descomentar ao usar online! //????
			) {
				return true;
			}
		}
		
		$this->AdicionarMensagemDeErro("Email $email é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarCpfDaPessoa($cpf)
	{
	if ( strlen( $cpf ) == 0 ) {
		return true;
	}
	$simbolos = array ('.','-');
	$cpf = str_replace($simbolos,'',$cpf);
	if ( ctype_digit($cpf) ) {
			if( ($cpf == '11111111111') || ($cpf == '22222222222') ||
   			($cpf == '33333333333') || ($cpf == '44444444444') ||
   			($cpf == '55555555555') || ($cpf == '66666666666') ||
   			($cpf == '77777777777') || ($cpf == '88888888888') ||
   			($cpf == '99999999999') || ($cpf == '00000000000') ) {
   				$this->AdicionarMensagemDeErro("CPF $cpf é inválido");
   				return false;
   			}
		$dv_informado = substr($cpf, 9,2);
	    for($i=0; $i<=8; $i++) {
    	$digito[$i] = substr($cpf, $i,1);
   		}

   		$posicao = 10;
   		$soma = 0;

   		for($i=0; $i<=8; $i++) {
    		$soma = $soma + $digito[$i] * $posicao;
    		$posicao = $posicao - 1;
   		}
	    $digito[9] = $soma % 11;

   		if($digito[9] < 2) {
    		$digito[9] = 0;
   		} else {
    		$digito[9] = 11 - $digito[9];
   		}

   		$posicao = 11;
   		$soma = 0;

   		for ($i=0; $i<=9; $i++) {
    		$soma = $soma + $digito[$i] * $posicao;
    		$posicao = $posicao - 1;
   		}

   		$digito[10] = $soma % 11;

   		if ($digito[10] < 2) {
    		$digito[10] = 0;
   		} else {
    		$digito[10] = 11 - $digito[10];
   		}

    	$dv = $digito[9] * 10 + $digito[10];
  		if ($dv == $dv_informado) {
   			return true;
  		}

   		$this->AdicionarMensagemDeErro("CPF $cpf é inválido");
		return false;
		}
	}
	//--------------------------------------------------------------------------
	public function ValidarCartaoSusDaPessoa($cartaosus)
	{
		if ( strlen($cartaosus) == 0 ) return true;
		if ( strlen($cartaosus) == 15 && ctype_digit($cartaosus) ) {
			return true;
		}
		$this->AdicionarMensagemDeErro("Número do Cartão SUS $cartaosus é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarProntuarioDaPessoa($prontuario)
	{
		if ( strlen($prontuario) == 0 ) return true;
		if ( strlen($prontuario) > 0 && ctype_digit($prontuario) ) {
			return true;
		}
		$this->AdicionarMensagemDeErro("Prontuário $prontuario é inválido");
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe uma mensagem
	 *
	 * @param String $mensagem
	 */
	public function ValidarPesquisaDePessoa($nome)
	{
		if ($nome != null && strlen($nome)> 2 ) {
			return true;
		}
		$this->AdicionarMensagemDeErro("Nome $nome é inválido!Mínimo de três caracteres");
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Valida o motivo de desligamento digitado pelo usuário.
	 *
	 * @param String $motivo
	 */
	public function ValidarInativacaoDePessoa($motivo, $tamMinimo)
	{
        $motivoValido = $dataNascimentoValida
                      = $dataHojeValida
                      = $dataUltimaVacinacao = false;

		if ($motivo != null && strlen($motivo) >= $tamMinimo )
        {
			$motivoValido = true;
		}
        else
        {
            $this->AdicionarMensagemDeErro("Motivo $motivo é inválido! "
                                     . "Digite ao menos $tamMinimo caracteres");
        }
        
        $crip = new Criptografia();

        parse_str( $crip->Decifrar($_SERVER['QUERY_STRING']) );

        if( isset($usuarioId) )
        {
            $nascimento = $this->Nascimento($usuarioId);

            $ultimaVacinacao = $this->DataHoraUltimaVacinacao($usuarioId);
            $ultimaVacina    = $this->UltimaVacinaTomada($usuarioId, true);

            $data = new Data();

            if($ultimaVacinacao)
            {
                $ultimaDataVacinacao = $data->InverterData($ultimaVacinacao);
            }


            $dataDesligamento = $data->InverterData($_POST['dataDesligamento']);

            if( $data->CompararData($dataDesligamento, '>=', $nascimento) )
            {
                $dataNascimentoValida = true;
            }
            else
            {
                $this->AdicionarMensagemDeErro("A data é inválida! "
                                         . "A mesma não deve ser anterior ao "
                                         . "nascimento.");
            }

            if( $data->CompararData($dataDesligamento, '<=') )
            {
                $dataHojeValida = true;
            }
            else
            {
                $this->AdicionarMensagemDeErro("A data é inválida! "
                                         . "A mesma não deve ser posterior à "
                                         . "data de hoje.");
            }

            if( $ultimaVacinacao &&
                $data->CompararData($dataDesligamento . date(' H:i:s'),
                                                  '>=', $ultimaVacinacao) )
            {
                $dataUltimaVacinacao = true;
            }
            else
            {
                $this->AdicionarMensagemDeErro("A data é inválida! "
                                         . "A mesma não deve ser anterior à "
                                         . "data da ultima vacinação "
                                         . $ultimaDataVacinacao . ' ('
                                         . $ultimaVacina . ')');
            }
		}

        if( $motivoValido
            && $dataNascimentoValida
            && $dataHojeValida
            && $dataUltimaVacinacao) return true;

        return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarSexoDaPessoa( $sexo )
	{
		if ( $sexo == 'F' || $sexo == 'M' ) {
			return true;
		}
		$this->AdicionarMensagemDeErro('Defina o sexo do indivíduo');
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarAcamadoDaPessoa( $acamado )
	{
		if ( $acamado == 1 || $acamado == 0 ) {
			return true;
		}
		$this->AdicionarMensagemDeErro('Defina se o indivíduo está acamado');
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarEtniaDaPessoa( $etnia )
	{
		if ( $etnia != '' ) {
			return true;
		}
		$this->AdicionarMensagemDeErro('Selecione o tipo de etnia');
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarEstadoDaPessoa( $estado )
	{
		if ( $estado != '' ) {
			return true;
		}
		$this->AdicionarMensagemDeErro('Selecione o estado de origem');
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarCidadeDaPessoa( $cidade )
	{
		if ( $cidade != '' ) {
			return true;
		}
		$this->AdicionarMensagemDeErro('Selecione a cidade de origem');
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarProfissaoDaPessoa( $profissao )
	{
		if ( $profissao != '' ) {
			return true;
		}
		$this->AdicionarMensagemDeErro('Selecione uma profissão');
		return false;
	}
	//--------------------------------------------------------------------------
	public function validarAgenteComunitarioDaPessoa( $agente )
	{
		if ( $agente != '' ) {
			return true;
		}
		$this->AdicionarMensagemDeErro('Selecione um Agente Comunitário de Saúde');
		return false;
	}
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
	//--------------------------------------------------------------------------
	public function RetornarCampoNome($tabela, $id)
	{
		$sql = "SELECT nome FROM `$tabela` WHERE id = $id";
		
		$b = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$b->bind_result($nome);
		
		$b->execute();
		
		$b->fetch();
		
		$b->free_result();

		return Html::FormatarMaiusculasMinusculas($nome);
	}
	//--------------------------------------------------------------------------
	public function SelectCidades($cidade_id = false)
	{
		$estado_id = $_SESSION['estado_id'];
		if($cidade_id == false) $cidade_id = $_SESSION['cidade_id'];
		
		$sql = 'SELECT id, nome
				    FROM `cidade`
						WHERE Estado_id = ?';
						
		$stmt = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_param('s', $estado_id);
		$id = $nome = false;
		$stmt->bind_result($id, $nome);
		$stmt->execute();
		$stmt->store_result();
		$qtdCidades = $stmt->num_rows;
		
		if( $qtdCidades ) {
		
		    echo '<option value="0">- selecione -</option>';
			
		    while( $stmt->fetch() ) {
				
				$selecionada = false;
				if($cidade_id == $id) $selecionada = "selected='true'";
				
		        echo "<option value='$id' $selecionada>",
				     Html::FormatarMaiusculasMinusculas($nome),
					 '</option>';
			}
		}
		
		else {
		    $this->AdicionarMensagemDeErro('Cidades não puderam ser recuperadas');
			return false;
		}
		
		$stmt->free_result();
		return true;
	}
	//--------------------------------------------------------------------------
	public function SelectsUnidades($unidade = false)
	{
		$cidade_id = $_SESSION['cidade_id'];
		$parametro = null;
		
		if( Sessao::Permissao('INDIVIDUOS_CADASTRAR') ) {
			
			if( Sessao::Permissao('INDIVIDUOS_CADASTRAR') == 1 ) {
				$sql = 'SELECT unidadedesaude.id, unidadedesaude.nome, tipodaunidade.nome
					FROM unidadedesaude, bairro, tipodaunidade
					WHERE unidadedesaude.Bairro_id = bairro.id
						AND unidadedesaude.TipoDaUnidade_id = tipodaunidade.id
						AND bairro.Cidade_id = ?
						AND unidadedesaude.ativo
						AND bairro.ativo ORDER BY unidadedesaude.nome';
						
				$parametro = $cidade_id;
			
			} elseif( Sessao::Permissao('INDIVIDUOS_CADASTRAR') == 2) {
			
				$sql = 'SELECT unidadedesaude.id, unidadedesaude.nome, tipodaunidade.nome
					FROM unidadedesaude, tipodaunidade
					WHERE unidadedesaude.TipoDaUnidade_id = tipodaunidade.id
						AND unidadedesaude.id = ?';
			
				$parametro = $_SESSION['unidadeDeSaude_id'];
			
			}
			
		} else return false;
		
		$selectUnidade = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$selectUnidade->bind_param('i', $parametro);
		$selectUnidade->bind_result($id, $nome, $tipo);

		$selectUnidade->execute();
			
		if ($unidade) {
				
			while($selectUnidade->fetch()) {
				
				if($unidade == $id){
					echo '<option value="'
						. $id
						. '" selected="true">'
						. Html::FormatarMaiusculasMinusculas($nome) . " ($tipo)"
						. "</option>";
				}
				else {
					echo '<option value="'
						. $id 
						. '">'
						. Html::FormatarMaiusculasMinusculas($nome). " ($tipo)"
						. "</option>";
				}
			}
		}
		else {
			while($selectUnidade->fetch()) {

				echo '<option value="'
					. $id
					. '">'
					. Html::FormatarMaiusculasMinusculas($nome) . " ($tipo)"
					. "</option>";
			}
		}
		$selectUnidade->free_result();
	}
    //--------------------------------------------------------------------------
    /**
     * Inativa a pessoa para busca de usuário vacinável.
     *
     * @param int $pessoaId
     * @param String $motivo
     * @param String $dataDesligamento
     */
    public function InativarPessoa($pessoaId, $motivo, $dataDesligamento)
    {
        $login = $_SESSION['login'];
        
        $data = new Data();
        $dataDesligamento = $data->InverterData($dataDesligamento);

        $sql = 'INSERT INTO `usuarioinativado` '
             .      'VALUES(NULL, ?, ?, ?, ?)';

		$stmt = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_param('isss', $pessoaId, $motivo, $dataDesligamento, $login);

		$stmt->execute();

        $stmt->close();
        
        $sql = 'UPDATE `usuario` '
             .      'SET vacinavel = 0 '
             .      'WHERE id = ?';

		$stmt = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_param('i', $pessoaId);

		$stmt->execute();

        $stmt->close();

        // Apaga os dados da sessão, para que atualize a busca quando voltar
        unset($_SESSION['listarPessoa']['arr']);
    }
    //--------------------------------------------------------------------------
    /**
     * Retorna a data e hora da última vacinação, seja ela de qualquer vacina.
     *
     * @param int $usuario_id
     * @return String Data e hora da vacinação
     */
	public function DataHoraUltimaVacinacao($usuario_id)
	{
        // Inicializando a variável. Caso dê erro, retorna false;
        $datahoravacinacao = false;
        
        $sql = 'SELECT MAX(datahoravacinacao) '
			 . 'FROM `usuariovacinado` '
			 . 'WHERE usuario_id = ?';
             
		$dataReal = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$dataReal->bind_param('i', $usuario_id);
		$dataReal->bind_result($datahoravacinacao);
		$dataReal->execute();

		$dataReal->fetch();
		$dataReal->free_result();

		return $datahoravacinacao;
	}
    //--------------------------------------------------------------------------
    /**
     * Retorna o nome da última vacina tomada pela pessoa.
     *
     * @param int $usuario_id
     * @return String Nome da vacina
     */
	public function UltimaVacinaTomada($usuario_id, $retornarDose = false)
	{
        // Inicializando a variável. Caso dê erro, retorna false;
        $vacinaNome = false;

        $sql = 'SELECT vacina.nome, usuariovacinado.numerodadose '
             . 'FROM `usuariovacinado`, `vacina` '
             . 'WHERE usuariovacinado.Usuario_id = ? '
             .     'AND usuariovacinado.Vacina_id = vacina.id '
             .     'ORDER BY usuariovacinado.id DESC '
             .     'LIMIT 1';
             
		$dataReal = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$dataReal->bind_param('i', $usuario_id);
		$dataReal->bind_result($vacinaNome, $numerodadose);
		$dataReal->execute();

		$dataReal->fetch();
		$dataReal->free_result();

        if($retornarDose && $vacinaNome) $vacinaNome .= ", dose $numerodadose";

		return $vacinaNome;
	}
    //--------------------------------------------------------------------------
}