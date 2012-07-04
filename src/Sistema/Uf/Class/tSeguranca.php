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


class Seguranca {
	 
	public static function VerificarJavaScript($nomeDaDiv) 
	{
		?>
		<script type="text/javascript">
		ExibirConteudo('<?php echo $nomeDaDiv?>')
		</script>
		
		<noscript>
		<div style="position: absolute;
					top: 40%;
					width: 98%;
					z-index: 10;"
					align="center">
						
		<h3 style=" color: white" align="center">Javascript desabilitado</h3>

		<div style='position: relative;
					color: white;
					width: 280px;
					text-align: justify;
					margin: auto;'>
			O sistema só poderá funcionar com javascript habilitado.
			Habilite nas opções do seu navegador e recarregue esta página.</div>
		</div>
		</noscript>
		
		<?php
	}
	//--------------------------------------------------------------------------
	/**
	 * Método que recebe um ponteiro de um valor de um array e a sua chave,
	 * para que o array_walk_recursive limpe todo o post, gerando um texto sem
	 * caracteres e palavras inválidas. Caso o usuário entre com algum texto
	 * da lista negra, então será apresentado no input a informação que o texto
	 * é inválido.
	 * 
	 * @param string $strString Dado a ser verificado.
	 * @param string $chave Chave do valor a ser verificado
     */		
	public static function MapeamentoDaLimpezaDoPost(&$strString, $chave)
	{
		$strBlackList = '/(select|insert|delete|drop table|database|table|';
		$strBlackList.= 'where|update|show tables|or 1=1|\*|--|)/';
		
		$strString = trim($strString);
		$strString = mysql_escape_string($strString);
		
		$str = preg_replace( sql_regcase($strBlackList), "", $strString);
		
		if($str !== $strString) $strString = '(TEXTO INVÁLIDO)';
		else {
			
			$strString = $str;
			$strString = Seguranca::CorrigirEscapamentos($strString);
			//$strString = nl2br($strString);
			
		}
	}

	//--------------------------------------------------------------------------
	/**
	 * Método usado para limpar o array superglobal $_POST, de injeção de SQL.
	 * O método acima - MapeamentoDaLimpezaDoPost() - é utilizado pela função
	 * array_walk_recursive(), que atua nos valores do array passado. Foi
	 * necessário usar ponteiro, para atuar diretamente no valor (sem retornar).
	 * Foi preciso usar recursividade, pois o $_POST pode conter arrays
	 * aninhados.
	 */
	public static function LimparPost()
	{
		if(!isset($_POST)) return null;

		$post = $_POST;
		array_walk_recursive($post, 'Seguranca::MapeamentoDaLimpezaDoPost');

		// echo '<pre>', print_r($post); echo '</pre>'; die;

		return $post;
	}
	
	public static function CorrigirEscapamentos($valor)
	{
		$retirar   = array('\r\n', '\n', '\r');
		$substituir = '<br />';
		
		$valor = str_replace($retirar, PHP_EOL, $valor);

		return $valor;
		
	}
}