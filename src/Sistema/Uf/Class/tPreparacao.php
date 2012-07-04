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


/**
 * Simac_Bd_Preparacao: Prepara os dados para serem inseridos sem s�mbolos no BD
 *
 * Esta classe prepara os dados, tanto para serem inseridos no banco de dados,
 * removendo s�mbolos como pontos, tra�os, barras, etc. mantendo somente os
 * dados relevantes, como prepara os dados recuperados do banco de dados para
 * serem exibidos para o usu�rio em um formato "leg�vel" - com pontos e tra�os.
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
	 * S�mbolos que ser�o removidos quando um CPF, CNPJ, Tel, etc. forem
	 * inseridos no banco de dados. Os tra�os, pontos etc. s�o exibidos na tela
	 * com aux�lio do javascript, por�m no banco de dados s� s�o armazenadas as
	 * informa��es essenciais. No caso de um n�mero de IP, os pontos ser�o
	 * armazenados, pois n�o ter�amos como diferenciar p.ex. 200.122.23.122 de
	 * 200.12.223.122 ou outras varia��es.
	 *
	 * @staticvar array $simbolos alguns caracteres que ser�o removidos antes da
	 * inser��o no banco de dados.
	 */
	private static $simbolos = array('-', '.', '/', '(', ')', ',', ':');
	//--------------------------------------------------------------------------
	/**
	 * Gera um array limpo, baseado no input do usu�rio - que vem do array
	 * $_POST, que captura as vari�veis do formul�rio. Este m�todo � necess�rio
	 * para certificar que n�o h� seq��ncias maliciosas de c�digo fornecidas
	 * pelo usu�rio. Caso tenha, o m�todo devolver� um array "limpo" (sem essas
	 * seq��ncias ou com essas seq��ncias "escapadas" para n�o produzirem efeito
	 * malicioso ao serem interpretadas).
	 *
	 * @param array $post Array ainda n�o tratado nem filtrado
	 * @return array|null Um array "limpo", escapado, sem espa�os em branco, sem os
	 * s�mbolos usados na formata��o e "seguro" de acordo com o
	 * mysqli->real_scape_string()
	 */
	public static function GerarArrayLimpo($post, $conexao = false)
	{

        // o "new mysqli" foi adicionado aqui prq ele n�o estava conseguindo
        // se conectar e tava dando erro. Inicialmente isso funcionava sem o "new mysqli",
        // mas foi feita alguma modifica��o desconhecia que acabou afetando isso.
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
	 * M�todo que insere os s�mbolos para a melhor visualiza��o do usu�rio nos
	 * campos como telefone, cpf, cnpj, etc. Os s�mbolos (pontos, tra�os, barras,
	 * etc.) s�o reinseridos, pois no banco de dados s� s�o inseridas as
	 * informa��es estitamente necess�rias. Os s�mbolos, al�m de ocupar
	 * espa�o desnecess�rio poder� atrapalhar em alguns casos. Logo, um telefone
	 * � armazenado como "45895555" ao inv�s de "4589-5555" e um CPF como
	 * "65445428737" ao inv�s de "654.454.287-37".
	 *
	 * @param string $campo O campo "cr�" - sem s�mbololos, recuperado do banco
	 * @param string $tipo Telefone, CPF, CNPJ, etc.
	 * @return string $campoFormatado Campo formatado com s�mbolos
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
	 * M�todo que remove os s�mbolos (pontos, tra�os, barras, etc.)para a
	 * inser��o no banco de dados.
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