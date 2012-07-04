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

/*
 * @package Sivac/Class
 *
 * @author Maykon Monnerat (maykon_ttd@hotmail.com), v 1.0, 2008-08-20
 *
 * @copyright 2008 
 */


class Ajuda
{
	//--------------------------------------------------------------------------
	public function UsarBaseDeDados()
	{

		$this->conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
			$_SESSION['senha']);


		$this->conexao->select_db($_SESSION['banco']);
	}
	//--------------------------------------------------------------------------
	public function SelecionarIndice($indice)
	{
		$ajuda = $this->conexao->prepare('SELECT titulo, conteudo, permissao
			FROM `ajuda` WHERE indice = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$ajuda->bind_param('s', $indice);
		$ajuda->bind_result($titulo, $conteudo, $permissao);
		$ajuda->execute();
		$ajuda->fetch();
		$ajuda->free_result();
		
		if( Sessao::Permissao($permissao) ) {
				
				echo "<br />$titulo";
				echo "<br />$conteudo";		
				return true;
				
		}
		
		return false;
		
	}
	//--------------------------------------------------------------------------
	public function PesquisarAjuda($pesquisa, $tipo)
	{
		if( $tipo == 'indice') {
			
			$sql = "SELECT titulo, conteudo, permissao
					FROM `ajuda`
					WHERE indice = '$pesquisa'
                          OR indice LIKE '%$pesquisa/%'
                          OR indice LIKE '%/$pesquisa%'

						ORDER BY indice";
		}
		elseif( $tipo == 'mapeamento') {
			
			$sql = "SELECT titulo, conteudo, permissao FROM `ajuda`
					WHERE mapeamento LIKE '%$pesquisa%'
						ORDER BY indice";
		}
		elseif( $tipo == 'conteudo') {
			
			$sql = "SELECT titulo, conteudo, permissao
					FROM `ajuda`
					WHERE MATCH (conteudo, titulo)
						AGAINST ('$pesquisa' IN BOOLEAN MODE)
						ORDER BY indice";
		}
		
		// Para grifar as palavras da pesquisa:
		$palavras = explode(' ', $pesquisa);
		
		$palavrasGrifadas = array();
		
		foreach ($palavras as $palavra) {
			
			$palavrasGrifadas[] = "<span
				style='background-color: #cae1f5'>$palavra</span>";
		}
		
		//echo ($sql);
		
		$ajuda = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
					
		$ajuda->bind_result($titulo, $conteudo, $permissao);
		$ajuda->execute();
		$ajuda->store_result();
		$existe = $ajuda->num_rows;
		
		
		
		if($existe > 0) {
			
			$arrResultado = array();
			
			while( $ajuda->fetch() ) {
				
				if( Sessao::Permissao($permissao) ) {
				
						$arrResultado[] = array('titulo' => $titulo,
												'conteudo' => $conteudo,
												'permissao' =>$permissao);		
						
				}
				
			}
			
			if( count($arrResultado) > 0 ) {
				
				$primeiroResultado = true;
				
				foreach( $arrResultado as $resultado )
				{
				
						// Entre um resultado e outro, coloca uma linha delimitadora:
						if(!$primeiroResultado) echo '<hr />';		
						
						echo "<h3 align='center'>{$resultado['titulo']}</h3>";
				
						if($tipo == 'conteudo') echo str_replace($palavras, $palavrasGrifadas, $resultado['conteudo']);
						else	echo $resultado['conteudo'];
				
						echo '<br />';
						$primeiroResultado = false;
				}
				
			}
			
		}
		else {
			
			if($tipo == 'conteudo') echo "A sua busca n�o retornou resultado.<br />
											Voc� procurou por [$pesquisa].";
		}
		
		$ajuda->free_result();
	}
	
	//--------------------------------------------------------------------------
	public function GerarMenu()
	{
		$ajuda = $this->conexao->prepare('SELECT indice, permissao
			FROM `ajuda` ORDER BY indice')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$ajuda->bind_result($indice, $permissao);
		$ajuda->execute();
		
		
		$arr = array();
		$arrNovo = array();
		
		while ($ajuda->fetch()){
			
			if( Sessao::Permissao($permissao) ) {
				
				$arr = explode('/', $indice);
				
				$nivel1 = $nivel2 = false;
				
				if(isset($arr[1])) {
					$nivel1 = $arr[1];
	
					if(isset($arr[2])) { 
						$nivel2 = $arr[2];
						$arrNovo[$arr[0]][$nivel1][] =  $nivel2;
					}
					else $arrNovo[$arr[0]][$nivel1] =  $nivel1;
	
				}
				else {
					$arrNovo[$arr[0]] =  $nivel1;
				}
			
			}
			
		}
		
		//echo '<pre>'; print_r($arrNovo); echo '</pre>';

        

		echo '<ul class="ajuda">';
		
		$crip = new Criptografia();
		
		foreach ($arrNovo as $nivel0 => $valor0) {
			
			$qs = $crip->Cifrar("indice=$nivel0");
			echo "<li><a href='?$qs'>$nivel0</a></li>";
			
			if(is_array($valor0)) {

				echo "<ul>";
				foreach ($valor0 as $nivel1 => $valor1){

					$qs = $crip->Cifrar("indice=$nivel0/$nivel1");
					echo "<li><a href='?$qs'>$nivel1</a></li>";
					
					if(is_array($valor1)) {
						echo "<ul>";
						foreach ($valor1 as $nivel2 => $valor2){

                            $qs = $crip->Cifrar("indice=$nivel0/$nivel1/$valor2");
							echo "<li><a href='?$qs'>$valor2</a></li>";
							
						}
						echo "</ul>";
					}
				}			
				echo "</ul>";
				
			}
		}
		echo "</ul>";
		
		$ajuda->free_result();
		//echo '<pre>'; print_r($arrNovo); echo '</pre>';

	}	
	//--------------------------------------------------------------------------
	
	// Arrumar um lugar melhor
	public function CaracteresEspeciais ()
	{
			$arrTotal = file('mysql.txt'); /// TEM QUE CRIAR ESSE ARQUIVO !!!!!!!!!
		
			foreach ($arrTotal as $valor) {
		
				$arrAntigo = array( '&aacute;','&Aacute;','&atilde;','&Atilde;',
									'&acirc;', '&Acirc;' ,'&agrave;','&Agrave;',
									'&eacute;','&Eacute;','&ecirc;' ,'&Ecirc;',
									'&iacute;','&Iacute;','&oacute;','&Oacute;',
									'&otilde;','&Otilde;','&ocirc;' ,'&Oacute;',
									'&uacute;','&Uacute;','&uuml;'  ,'&Uuml;',
									'&ccedil;','&Ccedil;');

				$arrNovo = array('�', '�', '�', '�', 
								 '�', '�', '�', '�', 
								 '�', '�', '�', '�', 
								 '�', '�', '�', '�', 
								 '�', '�', '�', '�', 
								 '�', '�', '�', '�', 
								 '�', '�');
				
				$linha = str_replace(array('\n', '\r'), '', $valor);
				
				echo str_replace($arrAntigo, $arrNovo, $linha);
			}
			
			/*
			�	&aacute;	�	&Aacute;	�	&atilde;	�	&Atilde;
			�	&acirc;		�	&Acirc;		�	&agrave;	�	&Agrave;
			�	&eacute;	�	&Eacute;	�	&ecirc;		�	&Ecirc;
			�	&iacute;	�	&Iacute;	�	&oacute;	�	&Oacute;
			�	&otilde;	�	&Otilde;	�	&ocirc;		�	&Ocirc;
			�	&uacute;	�	&Uacute;	�	&uuml;		�	&Uuml;
			�	&ccedil;	�	&Ccedil;
			*/ 
			
	}
}
?>