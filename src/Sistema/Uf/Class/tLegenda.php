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

class Legenda
{
    private $_icones; // Array
    protected $arquivoGerarIcone; // String
    private $_larguraDaLegenda; // Int

    
    //--------------------------------------------------------------------------
    public function __construct(array $icones = array())
    {
        $this->_icones = $icones;
        $this->LocalizarArquivoGeradorDeIcone();
    }
    //--------------------------------------------------------------------------
    private function CalcularMelhorLargura()
    {
    	// A melhor largura se baseia no tamanho do maior texto da legenda:
    	
    	$maiorTexto = 0;
    	
    	foreach ($this->_icones as $texto) {
    		
    		$tamTexto = strlen($texto[1]);
    		
    		if($tamTexto > $maiorTexto) $maiorTexto = $tamTexto; 
    	}
    	
    	return $maiorTexto;
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
    /**
     * Aceita também os nomes de cores, porém só os nomes padrão W3C: aqua,
     * black, blue, fuchsia, gray, green, lime, maroon, navy, olive, purple,
     * red, silver, teal, white e yellow. (continuar esse comentário)
     * @param <type> $titulo
     * @param <type> $largura
     */
	public function ExibirLegenda($titulo = 'Legenda', $largura = false)
    {
        if( count($this->_icones) ) {

        	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false
        		|| $largura) {

        		// Para Internet Explorer 6, somente:
        		if(!$largura) $largura = 70 + $this->CalcularMelhorLargura() * 7;
        		
        	}
        	
        	if($largura) $especificarLargura = "width: {$largura}px; ";
        	else $especificarLargura = ''; 
        	
        	

        	
        	echo '<p>';
            echo "<div style='background-image: url({$this->arquivoGerarIcone}?imagem=fundoLegenda);
            		float: right; $especificarLargura margin: 10px;'>";
            echo "<fieldset><legend>$titulo</legend>";
            
            $linha = 0;
            
            foreach( $this->_icones as $icone ) {
            
                //if( $linha ) echo '<br />';
                
                if(count($this->_icones) == 1) echo '<br />';
                
                echo '<div style="height: 22px; margin-top: 5px">';
                
                if($icone[0][0] == '#'
                    || (substr_count('aqua, black, blue, fuchsia, gray, green,
                                      lime, maroon, navy, olive, purple, red,
                                      silver, teal, white, yellow', $icone[0]) )
                  ) { // É uma cor e não uma imagem
                    
                	
                	echo $this->MontarQuadrinhoDeCor($icone[0]);
                }
                else {
	                echo "<img src='{$this->arquivoGerarIcone}?imagem={$icone[0]}'
	                    alt='{$icone[1]}' style='vertical-align: middle' />";
                }
                
                echo "&nbsp;&nbsp;<label><var>{$icone[1]}</var></label>";
                
                echo '</div>';
                
                if(count($this->_icones) == 1) echo '<br />';
                
                $linha++;
            }
            
            echo '</fieldset>';
            echo '</div>';
            echo '</p>';
        }
        else {
            
            echo 'Erro: Passar um array bidimensional (0 => nome, 1 => legenda)
                como parâmetro do construtor<br />';
        }
    }
    //--------------------------------------------------------------------------
    private function MontarQuadrinhoDeCor($cor)
    {
    	return "<div style='background-color: $cor; width: 15px; height: 15px;
    			float: left; margin-top: 1px; margin-left: 2px;
    			font-size: 1px;'></div>&nbsp;";
    }
    //--------------------------------------------------------------------------
    public function AdicionarItem($nome, $descricao)
    {
        // Se já existir um item de legenda idêntico, não adiciona:
        if( !in_array( array($nome, $descricao), $this->_icones ) ) {
        
            $this->_icones[] = array($nome, $descricao);
        }
    }
    //--------------------------------------------------------------------------
}
/*

Ajuda para usar a classe:

Se na hora de exibir a legenda você não passar a largura da mesma, ela então se
adaptará ao maior texto inserido.

Observe que deve-se usar os nomes dos ícones ou alguma cor (no caso de ser uma
cor, sempre começar pelo caracter "#"):

$icones[] = array('editar', 'Edita');
$icones[] = array('excluir', 'Exclui');
$icones[] = array('#CCC', 'Cinza');
$icones[] = array('#F00', 'Vermelho'); 

$leg = new Legenda($icones);

$leg->ExibirLegenda();
*/

