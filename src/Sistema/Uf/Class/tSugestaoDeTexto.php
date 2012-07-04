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
 * Esta classe é usada para evitar duplicidades no sistema, sugerindo textos
 * parecidos com o que foi digitado. Inicialmente criada pensando-se na
 * inserção de bairro, ela pode ser usada para buscar pessoas com nomes
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
	 * Conexão com a Base de Dados
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
     * Método que sugere uma lista com diversas palavras (consideradas certas),
     * vindas de uma fonte de dados (lista, array, banco de dados, etc), que
     * sejam parecidas com o que o usuário digitou. O número máximo de sugestões
     * pode ser limitado pelo desenvolvedor. No caso da relevância, quando mais
     * próxima de 0, mais parecida com a palavra certa é o que o usuário
     * digitou. Foi acrescentada a possibilidade de trabalhar com o conjunto de
     * dados múltiplo, como tratar o nome, mas considerar também a mãe, a data
     * de nascimento, etc.
     */
    public function Sugestoes($palavraDigitada, Array
                              $arrayDePalavrasCertas,
                              $relevancia = 7,
                              $maxSugestoes = false )
    {
        $sugestoes = array();

        $contador = 0;

        // Se vai tratar linha a linha um array multidimensional, então
        // o array tem mais de uma posição:
        $considerarSubArray = false;
        if( count($arrayDePalavrasCertas[0]) > 1 ) {

            $considerarSubArray = true;
        }

        foreach( $arrayDePalavrasCertas as $palavra ) {

            // Se o array tem mais de uma posicao, considerar a chave "nome"
            // como a palavra certa do array:
            if( $considerarSubArray ) $palavraCerta = $palavra['nome'];
            else                      $palavraCerta = $palavra;

            // Se for a mesma palavra, pára e retorna true:
            //if( strcmp(strtolower($palavraCerta),
            //           strtolower($palavraDigitada)) === 0) return true;

            // Verifica o tamanho da palavra digitada:
            if( strlen($palavraDigitada) < 3 ) return false;

            $relevanciaAtual = levenshtein(strtolower($palavraDigitada),
                                           strtolower($palavraCerta) );

            // Se a palavra digitada é maior que 2, então a primeira letra não
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

            // Verifica se a relevância da sugestão viabiliza a palavra:
            if(  $relevanciaAtual < $relevancia && $primeiraLetra) {

                $sugestoes[$contador]['palavra'] = $palavraCerta;
                $sugestoes[$contador]['relevancia'] = $relevanciaAtual;

                if( $considerarSubArray ) {

                    // Se o array passado é multidimensional, repetir os campos
                    // informados para que possa retornar todos os outros dados
                    // (como nascimento, mãe, etc):
                    foreach( $palavra as $campo => $valor) {

                        $sugestoes[$contador][$campo] = $valor;
                    }
                }

                $contador++;
            }
        }

        // Ordena o array de forma que a sugestão de maior relevância fique primeiro
        // usando uma função personalizada de ordenação:
        usort($sugestoes, array($this, 'Ordenacao')   );

        // Somente podemos remover elemento repetido se o array passado for
        // simples (não multidimensional), pois podem existir nomes iguais, mas
        // com datas de nasc. ou mãe diferentes, p.ex.:
       if( !$considerarSubArray ) {
           $sugestoes = $this->RemoverElementoRepetido($sugestoes);
       }

        // Retorna somente até a quantidade máxima de sugestões, se for definida:
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
     * Método usado para ordenar o array pela chave "relevancia", de cada
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
     * Método que recebe um array e remove a(s) palavra(s) duplicadas de cada
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

    elseif( $sugestoes === false ) echo 'Palavra inválida!';

    elseif( count($sugestoes) ) {

        echo "<p>Você digitou <span style='color: blue'>[{$_POST['cidade']}]</span></p>";

        echo '<hr /><p>Sugestões:<ul>';

        foreach( $sugestoes as $sugestao ) {

            echo "<li><big>{$sugestao['palavra']}&nbsp;&nbsp;&nbsp;</big><span style='color: gray'><small><em>relevância: {$sugestao['relevancia']}</em></small></span></li>";
            echo $sugestao['nome'], $sugestao['sexo'], $sugestao[ 'nascimento'], $sugestao['mae'];
        }
        echo '</ul></p>';
    }

    else echo 'Sem sugestões para o que você digitou!';
}
*/