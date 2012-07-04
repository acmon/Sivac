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
			O sistema s� poder� funcionar com javascript habilitado.
			Habilite nas op��es do seu navegador e recarregue esta p�gina.</div>
		</div>
		</noscript>
		
		<?php
	}
	//--------------------------------------------------------------------------
	/**
	 * M�todo que recebe um ponteiro de um valor de um array e a sua chave,
	 * para que o array_walk_recursive limpe todo o post, gerando um texto sem
	 * caracteres e palavras inv�lidas. Caso o usu�rio entre com algum texto
	 * da lista negra, ent�o ser� apresentado no input a informa��o que o texto
	 * � inv�lido.
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
		
		if($str !== $strString) $strString = '(TEXTO INV�LIDO)';
		else {
			
			$strString = $str;
			$strString = Seguranca::CorrigirEscapamentos($strString);
			//$strString = nl2br($strString);
			
		}
	}

	//--------------------------------------------------------------------------
	/**
	 * M�todo usado para limpar o array superglobal $_POST, de inje��o de SQL.
	 * O m�todo acima - MapeamentoDaLimpezaDoPost() - � utilizado pela fun��o
	 * array_walk_recursive(), que atua nos valores do array passado. Foi
	 * necess�rio usar ponteiro, para atuar diretamente no valor (sem retornar).
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