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
 * Simac_Bd_Preparacao: Prepara os dados para serem inseridos sem símbolos no BD
 *
 * Esta classe prepara os dados, tanto para serem inseridos no banco de dados,
 * removendo símbolos como pontos, traços, barras, etc. mantendo somente os
 * dados relevantes, como prepara os dados recuperados do banco de dados para
 * serem exibidos para o usuário em um formato "legível" - com pontos e traços.
 *
 * @package Sivac/Class
 *
 * @author Douglas, v 1.0, 2008-07-23 10:36
 *
 * @copyright 2008 
 */
//-----------------------------------------------------------------------------
class Preparacao
{
	/**
	 * Símbolos que serão removidos quando um CPF, CNPJ, Tel, etc. forem
	 * inseridos no banco de dados. Os traços, pontos etc. são exibidos na tela
	 * com auxílio do javascript, porém no banco de dados só são armazenadas as
	 * informações essenciais. No caso de um número de IP, os pontos serão
	 * armazenados, pois não teríamos como diferenciar p.ex. 200.122.23.122 de
	 * 200.12.223.122 ou outras variações.
	 *
	 * @staticvar array $simbolos alguns caracteres que serão removidos antes da
	 * inserção no banco de dados.
	 */
	private static $simbolos = array('-', '.', '/', '(', ')', ',', ':');
	//--------------------------------------------------------------------------
	/**
	 * Gera um array limpo, baseado no input do usuário - que vem do array
	 * $_POST, que captura as variáveis do formulário. Este método é necessário
	 * para certificar que não há seqüências maliciosas de código fornecidas
	 * pelo usuário. Caso tenha, o método devolverá um array "limpo" (sem essas
	 * seqüências ou com essas seqüências "escapadas" para não produzirem efeito
	 * malicioso ao serem interpretadas).
	 *
	 * @param array $post Array ainda não tratado nem filtrado
	 * @return array|null Um array "limpo", escapado, sem espaços em branco, sem os
	 * símbolos usados na formatação e "seguro" de acordo com o
	 * mysqli->real_scape_string()
	 */
	public static function GerarArrayLimpo($post, $conexao = false)
	{

        // o "new mysqli" foi adicionado aqui prq ele não estava conseguindo
        // se conectar e tava dando erro. Inicialmente isso funcionava sem o "new mysqli",
        // mas foi feita alguma modificação desconhecia que acabou afetando isso.
        $conexao = new mysqli($_SERVER['HOST_SIVAC'],
                      $_SERVER['USER_SIVAC'],
                      $_SERVER['PASS_SIVAC'],
                      $_SERVER['BD_SIVAC'],
                      $_SERVER['PORT_SIVAC']);
        
		if( !$conexao ) $conexao = mysqli_connect();

        if(  !count($post)  ) return null; // se o post estiver vazio, retorna

		$clean = array();

		foreach ($post as $chave => $valor)
		{
			$clean[$chave] = $conexao->real_escape_string(trim($valor));
		}
		return $clean;
	}
	//--------------------------------------------------------------------------
	/**
	 * Método que insere os símbolos para a melhor visualização do usuário nos
	 * campos como telefone, cpf, cnpj, etc. Os símbolos (pontos, traços, barras,
	 * etc.) são reinseridos, pois no banco de dados só são inseridas as
	 * informações estitamente necessárias. Os símbolos, além de ocupar
	 * espaço desnecessário poderá atrapalhar em alguns casos. Logo, um telefone
	 * é armazenado como "45895555" ao invés de "4589-5555" e um CPF como
	 * "65445428737" ao invés de "654.454.287-37".
	 *
	 * @param string $campo O campo "crú" - sem símbololos, recuperado do banco
	 * @param string $tipo Telefone, CPF, CNPJ, etc.
	 * @return string $campoFormatado Campo formatado com símbolos
	 */
	public static function InserirSimbolos($campo, $tipoDeCampo)
	{
		$campoFormatado = '';

		$tipo = strtoupper($tipoDeCampo);

		switch ($tipo) {
			case 'CPF':
				$campoFormatado = substr($campo, 0, 3)
								. '.' . substr($campo, 3, 3)
								. '.' . substr($campo, 6, 3)
								. '-' . substr($campo, 9);
				break;

			case 'CNPJ':
				$campoFormatado = substr($campo, 0, 2)
								. '.' . substr($campo, 2, 3)
								. '.' . substr($campo, 5, 3)
								. '/' . substr($campo, 8, 4)
								. '-' . substr($campo, 12);
				break;

			case 'TEL':
				$campoFormatado = substr($campo, 0, 4)
								. '-' . substr($campo, 4);
				break;
			case 'INSC':
				$campoFormatado = substr($campo, 0, 2)
								. '.' . substr($campo, 2, 3)
								. '.' . substr($campo, 5);
				break;
			case 'CEP':
				if( strlen($campo) >= 8 )
				$campoFormatado = substr($campo, 0, 5)
								. '-' . substr($campo, 5);
				else $campoFormatado = '';
				break;

			default:
				$campoFormatado = $campo;
		}

		return $campoFormatado;
	}

	//--------------------------------------------------------------------------
	/**
	 * Método que remove os símbolos (pontos, traços, barras, etc.)para a
	 * inserção no banco de dados.
	 *
	 * @param String $campo
	 * @return String $campo
	 */
	public static function RemoverSimbolos($campo) {

		return str_replace(self::$simbolos, '', $campo);
	}
}
/*
Para teste:

$cpf = '07582493737';
$cnpj = '60659463000191';
$tel = '25662489';
$insc = '52658965';
$cep = '28660000';
$outro = '123567890';

echo '<pre>',
	Simac_Bd_Preparacao::InserirSimblos($cpf, 'cpf'), PHP_EOL,
	Simac_Bd_Preparacao::InserirSimblos($cnpj, 'CNpj'), PHP_EOL,
	Simac_Bd_Preparacao::InserirSimblos($tel, 'tEL'), PHP_EOL,
	Simac_Bd_Preparacao::InserirSimblos($insc, 'insc'), PHP_EOL,
	Simac_Bd_Preparacao::InserirSimblos($cep, 'cep'), PHP_EOL,
	Simac_Bd_Preparacao::InserirSimblos($outro, 'algo'), PHP_EOL;
*/