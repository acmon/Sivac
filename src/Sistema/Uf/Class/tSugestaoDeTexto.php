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
 * Esta classe � usada para evitar duplicidades no sistema, sugerindo textos
 * parecidos com o que foi digitado. Inicialmente criada pensando-se na
 * inser��o de bairro, ela pode ser usada para buscar pessoas com nomes
 * parecidos, para que o administrador possa resolver duplicidades.
 *
 * @package Sivac/Class
 *
 * @author Douglas, v 1.0, 2009-09-15 10:28
 *
 * @copyright 2009 
 */
class SugestaoDeTexto
{
    //--------------------------------------------------------------------------
    /**
     * Atributos
     */
    protected $conexao; // mysqli
    //--------------------------------------------------------------------------
    /**
     * Construtor
     */
    public function __construct()
	{
	}
    //--------------------------------------------------------------------------
    /**
     * Destrutor
     */
	public function __destruct()
	{
		if( isset($this->conexao) ) $this->conexao->close();
	}
	//--------------------------------------------------------------------------
	/**
	 * Conex�o com a Base de Dados
	 */
	public function UsarBaseDeDados()
	{
		$this->conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
			$_SESSION['senha']);


		$this->conexao->select_db($_SESSION['banco']);
	}
    //--------------------------------------------------------------------------
    public function ArrayDeDadosBaseadoEmSql($sql)
    {
        $rs = $this->conexao->query($sql) or die('falha');

        $array = array();

        if($rs->num_rows > 0) {
            
            while( $linha = $rs->fetch_assoc() ) $array[] = $linha;
            $rs->free_result();
        }
        
        if( count($array) ) return $array;
    }
    //--------------------------------------------------------------------------
    /**
     * @param String $palavraDigitada
     * @param Array $arrayDePalavrasCertas
     * @param int $relevancia
     * @param int|bool $maxSugestoes
     * @return Array
     * 
     * M�todo que sugere uma lista com diversas palavras (consideradas certas),
     * vindas de uma fonte de dados (lista, array, banco de dados, etc), que
     * sejam parecidas com o que o usu�rio digitou. O n�mero m�ximo de sugest�es
     * pode ser limitado pelo desenvolvedor. No caso da relev�ncia, quando mais
     * pr�xima de 0, mais parecida com a palavra certa � o que o usu�rio
     * digitou. Foi acrescentada a possibilidade de trabalhar com o conjunto de
     * dados m�ltiplo, como tratar o nome, mas considerar tamb�m a m�e, a data
     * de nascimento, etc.
     */
    public function Sugestoes($palavraDigitada, Array
                              $arrayDePalavrasCertas,
                              $relevancia = 7,
                              $maxSugestoes = false )
    {
        $sugestoes = array();

        $contador = 0;

        // Se vai tratar linha a linha um array multidimensional, ent�o
        // o array tem mais de uma posi��o:
        $considerarSubArray = false;
        if( count($arrayDePalavrasCertas[0]) > 1 ) {

            $considerarSubArray = true;
        }

        foreach( $arrayDePalavrasCertas as $palavra ) {

            // Se o array tem mais de uma posicao, considerar a chave "nome"
            // como a palavra certa do array:
            if( $considerarSubArray ) $palavraCerta = $palavra['nome'];
            else                      $palavraCerta = $palavra;

            // Se for a mesma palavra, p�ra e retorna true:
            //if( strcmp(strtolower($palavraCerta),
            //           strtolower($palavraDigitada)) === 0) return true;

            // Verifica o tamanho da palavra digitada:
            if( strlen($palavraDigitada) < 3 ) return false;

            $relevanciaAtual = levenshtein(strtolower($palavraDigitada),
                                           strtolower($palavraCerta) );

            // Se a palavra digitada � maior que 2, ent�o a primeira letra n�o
            // precisa necessariamente ser igual:
            if( strlen($palavraDigitada) > 2 ) $primeiraLetra = true;

            else {

                $primeiraLetraPalavraCerta = strtolower(substr($palavraCerta, 0, 1));
                $primeiraLetraPalavraDigitada = strtolower(substr($palavraDigitada, 0, 1) );

                if( $primeiraLetraPalavraCerta === $primeiraLetraPalavraDigitada) {

                    $primeiraLetra = true;
                }
                else {

                    $primeiraLetra = false;
                }
            }

            // Verifica se a relev�ncia da sugest�o viabiliza a palavra:
            if(  $relevanciaAtual < $relevancia && $primeiraLetra) {

                $sugestoes[$contador]['palavra'] = $palavraCerta;
                $sugestoes[$contador]['relevancia'] = $relevanciaAtual;

                if( $considerarSubArray ) {

                    // Se o array passado � multidimensional, repetir os campos
                    // informados para que possa retornar todos os outros dados
                    // (como nascimento, m�e, etc):
                    foreach( $palavra as $campo => $valor) {

                        $sugestoes[$contador][$campo] = $valor;
                    }
                }

                $contador++;
            }
        }

        // Ordena o array de forma que a sugest�o de maior relev�ncia fique primeiro
        // usando uma fun��o personalizada de ordena��o:
        usort($sugestoes, array($this, 'Ordenacao')   );

        // Somente podemos remover elemento repetido se o array passado for
        // simples (n�o multidimensional), pois podem existir nomes iguais, mas
        // com datas de nasc. ou m�e diferentes, p.ex.:
       if( !$considerarSubArray ) {
           $sugestoes = $this->RemoverElementoRepetido($sugestoes);
       }

        // Retorna somente at� a quantidade m�xima de sugest�es, se for definida:
        if( $maxSugestoes ) return array_slice($sugestoes, 0, $maxSugestoes);

        return $sugestoes;
    }
    //--------------------------------------------------------------------------
    /**
     *
     * @param int $valor1
     * @param int $valor2
     * @return int
     *
     * M�todo usado para ordenar o array pela chave "relevancia", de cada
     * sub-array dentro dos registros.
     */
    private function Ordenacao($valor1, $valor2)
    {
        if ($valor1['relevancia'] == $valor2['relevancia']) return 0;

        return ($valor1['relevancia'] < $valor2['relevancia']) ? -1 : 1;
    }
    //--------------------------------------------------------------------------
    /**
     * @param Array $array
     * @return Array
     *
     * M�todo que recebe um array e remove a(s) palavra(s) duplicadas de cada
     * chave "palavra" dentro dos registros.
     */
    private function RemoverElementoRepetido(Array $array)
    {
        $palavras = array();
        $relevancias = array();

        // Separando em 2 arrays:
        foreach ($array as $elemento) {

            $palavras[] = $elemento['palavra'];
            $relevancias[] = $elemento['relevancia'];
        }

        $palavrasUnicas = array_unique($palavras);

        $novoArray = array();
        $contador = 0;
        
        // Removendo o elemento repetido (se houver):
        foreach($palavrasUnicas as $chave => $valor) {

            $novoArray[$contador]['palavra'] = $palavrasUnicas[$chave];
            $novoArray[$contador]['relevancia'] = $relevancias[$chave];

            $contador++;
        }

        // Retornando o novo array sem elementos repetidos:
        return $novoArray;

    }
    //--------------------------------------------------------------------------
}
?>
<!-- DESCOMENTE PARA TESTAR O ARQUIVO DIRETAMENTE E VER COMO A CLASSE FUNCIONA:
<form method="post">

    <label>Cidade: <input type="text" name="cidade"/></label>
    <button>Testar</button>

</form>
-->
<?php
//------------------------------------------------------------------------------
/*
$_POST['cidade'] = 'joao';

if( isset($_POST['cidade']) ) {

    $s = new SugestaoDeTexto();
    $s->UsarBaseDeDados();
    
    //$rs = $conexao->query('SELECT * FROM cidade')->fetchAll();
    $rs = $s->ArrayDeDadosBaseadoEmSql('SELECT nome, sexo, nascimento, mae FROM usuario');

    //foreach( $rs as $linha ) $cidades[] = $linha['nome'];

   $cidades = $rs;
    
    $sugestoes = $s->Sugestoes($_POST['cidade'], $cidades, 7, 5);
    //$sugestoes = SugestaoSonora($_POST['cidade'], $cidades);

    if( $sugestoes === true ) echo 'Palavra correta!';

    elseif( $sugestoes === false ) echo 'Palavra inv�lida!';

    elseif( count($sugestoes) ) {

        echo "<p>Voc� digitou <span style='color: blue'>[{$_POST['cidade']}]</span></p>";

        echo '<hr /><p>Sugest�es:<ul>';

        foreach( $sugestoes as $sugestao ) {

            echo "<li><big>{$sugestao['palavra']}&nbsp;&nbsp;&nbsp;</big><span style='color: gray'><small><em>relev�ncia: {$sugestao['relevancia']}</em></small></span></li>";
            echo $sugestao['nome'], $sugestao['sexo'], $sugestao[ 'nascimento'], $sugestao['mae'];
        }
        echo '</ul></p>';
    }

    else echo 'Sem sugest�es para o que voc� digitou!';
}
*/