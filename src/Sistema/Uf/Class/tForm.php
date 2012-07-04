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


class Form
{
    
	protected $arquivoGerarIcone; // String com o nome do arquivo que gera �cones
	
	public function __construct()
	{
		$this->LocalizarArquivoGeradorDeIcone();
	}
	//--------------------------------------------------------------------------
	private function LocalizarArquivoGeradorDeIcone()
	{
		if(file_exists("{$_SERVER['DOCUMENT_ROOT']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php")) {
			
			$this->arquivoGerarIcone =
				"http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php";
        }
	}
	//--------------------------------------------------------------------------
	public function BotaoVoltarHistorico($texto = 'Voltar', $imagem = 'listar',
		$corDoTexto ='#14E')
	{
		if( !isset($_SESSION) ) {
			
			die('O bot�o voltar s� pode ser usado com sess�es');
		}
		
		$crip = new Criptografia();
		
		parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
		
		$pag = $pagina;
		
		if( isset($_SERVER['HTTP_REFERER']) ) {
			
			list($url, $querystringDoReferer) = explode('?', $_SERVER['HTTP_REFERER']);
			
			parse_str($crip->Decifrar($querystringDoReferer) );
			
			if( $pagina != $pag ) {
				
				$queryDecifrada = strtolower($crip->Decifrar($querystringDoReferer));
				
				if( strpos( strtolower($queryDecifrada), 'pagina=adm' ) === 0 ) {
					
					//echo "<script>alert('{$queryDecifrada}')</script>";
					$_SESSION['paginaAnterior'] = $_SERVER['HTTP_REFERER'];
					
				}
				
			}
		}
		
		if( isset($_SESSION['paginaAnterior']) ) {
			
			$paginaAnterior = $_SESSION['paginaAnterior']; 
		
		
	
			echo '<div align="center" style="clear:both">';
			echo "<button name='listar' type='button'
				  style='color: $corDoTexto; width: 130px; margin:10px;' 
				  onclick=\"window.location = '$paginaAnterior'\">";
			echo "<img src='{$this->arquivoGerarIcone}?imagem=$imagem' alt='listar'
				  style='vertical-align: middle' />";
			echo "$texto";
			echo '</button>';
			echo '</div>';
		
		}
	}
	//--------------------------------------------------------------------------
	public function BotaoFechar($texto = 'Fechar', $imagem = 'excluir',
		$corDoTexto ='#14E')
	{

		echo '<div align="center" style="clear:both">';
		echo "<button name='listar' type='button'
			  style='color: $corDoTexto; width: 130px; margin:10px;' 
		      onclick=\"window.close()\">";
		echo "<img src='{$this->arquivoGerarIcone}?imagem=$imagem' alt='listar'
			  style='vertical-align: middle' />";
		echo "$texto";
		echo '</button>';
		echo '</div>';
	}
	
}
?>