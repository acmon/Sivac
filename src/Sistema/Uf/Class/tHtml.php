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


//require_once('tCriptografia.php');


/**
 * Html: Classe para uso da interface HTML com o usu�rio.
 *
 * Esta classe serve para facilitar a programa��o de diversas utlidades no
 * que se refere � intera��o entre o usu�rio e o sistema. Verificar o IP do
 * usu�rio, retornar o nome do navegador, criar uma tabela baseada em um array
 * passado por par�metro, etc. s�o algumas de suas funcionalidades. Os m�todos
 * s�o est�ticos, pois n�o tem objetivo de se criar uma inst�ncia para a
 * manipula��o desses dados.
 *
 * @package Sivac/Class
 *
 * @author  Douglas, v 1.0, 2008-06-02 09:53
 *
 * @version Douglas, v 1.1, 2008-06-09 12:32
 * @version Douglas, v 1.2, 2008-06-14 17:15
 * @version Douglas, v 1.3, 2008-06-25 19:50
 *
 * @copyright 2008
 *
 */
class Html
{
	protected $conexao;
	const LIMITE = 50;

    
	//--------------------------------------------------------------------------
	/**
	 * M�todo est�tico que retorna o nome do navegador que o usu�rio est� usando.
	 * Os tipos 100% compat�veis com o sistema s�o: Internet Explorer, Firefox e
	 * Safari. O navegador Opera n�o se mostrou totalmente compat�vel com
	 * algumas valida��es em Javascript, por isso n�o � recomendado. Outros
	 * navegadores n�o foram testados.
	 *
	 * @return string Nome do navegador usado, ou, caso o navegador n�o esteja
	 * na lista "Navegador n�o compat�vel"
	 */
	public static function Navegador()
	{
		$ambiente = $_SERVER['HTTP_USER_AGENT'];

		// Importante usar !== ao inv�s de !=, pois no caso de !=,
		// false equivale a null ou a 0, e a string pode estar presente na
		// posi��o 0.

		if (stripos($ambiente, 'msie') !== false) {
			return 'Internet Explorer';
		}

		if (stripos($ambiente, 'firefox') !== false ) {
			return 'Firefox';
		}

		if (stripos($ambiente, 'Opera') !== false ) {
			return 'Opera';
		}

		if (stripos($ambiente, 'Chrome') !== false ) {
			return 'Chrome';
		}

		if (stripos($ambiente, 'safari') !== false ) {
			return 'Safari';
		}

		return 'Navegador n�o compat�vel';
	}
	//--------------------------------------------------------------------------
	/**
	 * Conex�o com a Base de Dados
	 *
	 */
	public function UsarBaseDeDados()
	{
		$this->conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
			$_SESSION['senha']);


		$this->conexao->select_db($_SESSION['banco']);

		//echo mysqli_connect_error(); die;
	}
	//--------------------------------------------------------------------------
	/**
	 * M�todo est�tico que retorna o IP usado pelo usu�rio.
	 *
	 * @return string Representa o endere�o IP do usu�rio.
	 */
	public static function IpDoUsuario()
	{
		$possibilib = array('REMOTE_ADDR',
	                      'HTTP_X_FORWARDED_FOR',
	                      'HTTP_X_FORWARDED',
	                      'HTTP_FORWARDED_FOR',
	                      'HTTP_FORWARDED',
	                      'HTTP_X_COMING_FROM',
	                      'HTTP_COMING_FROM',
	                      'HTTP_CLIENT_IP');

		foreach ($possibilib as $ip)
			if (isset($_SERVER[$ip])) return $_SERVER[$ip];

		return '0.0.0.0';
	}
	//--------------------------------------------------------------------------
	/**
	 * Inverte uma data. Serve tanto para inverter o formato 00/00/0000 quanto
	 * para inverter o formato 0000/00/00, dependendo do argumento passado
	 *
	 * @param string $data Data informada
	 * @return string Data invertida
	 */
	public static function InverterData($data)
	{
		list($p1, $p2, $p3) = preg_split('@[^0-9]{1}@', $data);

		if(  isset($p1, $p2, $p3)  ) return "$p3/$p2/$p1";
		return $data;
	}
	//--------------------------------------------------------------------------
	/**
	 * Espera receber um array bidimensional e, baseado neste array, cria uma
	 * tabela HTML num formato visual leg�vel e compat�vel com os os navegadores
	 * padr�o. Os atributos obrigat�rios s�o o array $arrayDaTabela e $tabela,
	 * que � o seu nome no banco de dados. O par�mtro $editarexcluir serve para
	 * exibir os bot�es que servem para editar ou para excluir o registro no
	 * banco de dados, e o par�metro $atributos serve para modificar os
	 * atributos que ser�o usados para formatar visualmente a tabela. O nome da
	 * tabela � para montar o link de que tabela ser� editada ou exclu�da.
	 *
	 * Se colocado no primeiro caracter da chave o valor "?", ent�o esta coluna
	 * n�o ser� exibida
	 *
	 * @param array $arrayDaTabela Array bidimensional para montar a tabela
	 * @param string|bool $editarexcluir Editar, Excluir ou FALSE
	 * @param string $atributos Atributos HTML da tabela
	 */
	public static function CriarTabelaDeArray($arrayDaTabela, $editar = false,
	$excluir = false, $visualizar = false, $largura = 645, $corTh = '#b8c8d9', $regua = '',
	$atributos = 'border="1" bgcolor="#ffffff" align="center"
				 cellspacing="0" frame="box"') {

		// Localiza aonde est� o arquivo gerarIcone.php, de acordo com o script
		// que usa este m�todo:
		
		if(file_exists('./gerarIcone.php')) {
        	$arquivo = './gerarIcone.php';
        }
        elseif (file_exists('../gerarIcone.php')) {
        	$arquivo = '../gerarIcone.php';
        }
        elseif (file_exists('../../gerarIcone.php')) {
        	$arquivo = '../../gerarIcone.php';
        }
        else {
        	$arquivo = 'gerarIcone.php';
        }
	
		echo "<table width=$largura rules=$regua $atributos>";

		$titulo = true;
		$par = true;

		foreach($arrayDaTabela as $chave => $linha) {

			if (!$par) { // Imprime uma linha cinza-azulado...
				echo "<tr bgcolor='#eaeff3'>";
				$par = true;
			}
			else {      // ... e outra branca.
				echo "<tr>";
				$par = false;
			}

			if($titulo) { // Imprime os t�tulos da tabela:
				foreach($linha as $chave => $naousado) {
					
				if(strtolower($chave) == 'data de aplica��o') {
					$chave = 'aplica��o';
					$larguraColuna = "style='width: 200px'";
				}
				elseif(strtolower($chave) == 'nova data ideal'){
					$chave = 'nova data';
				}
				elseif(strtolower($chave) == 'nascimento') {
					$larguraColuna = "style='width: 85px'";
				}
				elseif(strtolower($chave) == 'dose') {
					$larguraColuna = "style='width: 40px'";
				}
				elseif(strtolower($chave) == 'a��es') {
					$larguraColuna = "style='width: 65px'";
				}
				else $larguraColuna = '';
			
					// Pula se o titulo for ID:
					if( trim(strtolower($chave)) == 'id' || $chave[0] == '?' ) continue;
					echo "<th bgcolor='$corTh' $larguraColuna>$chave</th>";
					//echo "<th bgcolor='#c7d4e1'>$chave</th>";
				}
				$titulo = false;

				if ( ( $editar == true ) && ( $excluir == true ) && ($visualizar == true) ) {
					echo "<th bgcolor='$corTh' align='center'
						 width='90px'>a&ccedil;&otilde;es</th>";

				} else {

					if ( $editar == true ) {
						echo "<th bgcolor='$corTh' align='center'
							 width='20px'>a&ccedil;&otilde;es</th>";
					}
					if ( $excluir == true ) {
						echo "<th bgcolor='$corTh' align='center'
						     width='20px'>a&ccedil;&otilde;es</th>";
					}
					if ( $visualizar == true ) {
						echo "<th bgcolor='$corTh' align='center'
						     width='20px'>a&ccedil;&otilde;es</th>";
					}
				}
				echo '</tr><tr>';
			}

			foreach($linha as $chave => $dado) {

				if( trim(strtolower($chave)) == 'id' || $chave[0] == '?' ) continue;

				if(strpos(trim(strtolower($chave)), 'nome') !== false
					|| strpos(trim(strtolower($chave)), 'm�e') !== false)
					 $alinhamento = 'left';

				else $alinhamento = 'center';

				if( trim(strtolower($chave)) == 'data' )
					$dado = self::InverterData($dado);

				if(trim(strtolower($chave)) == 'data de aplica��o') $dado = str_ireplace('Vacinado Em', '', $dado);
					
				$naoInformada = str_replace('�', 'a', strtolower($dado));
				
				if(substr_count($naoInformada, 'nao inform') && strtolower($chave) =='m�e')
				$dado = '<em><span style="color: #CCC">N�o informada</span></em>';
				elseif(substr_count($naoInformada, 'nao inform'))
				$dado = '<em><span style="color: #CCC">N�o informado</span></em>';
				elseif(substr_count($naoInformada, 'nt') && strlen($naoInformada) < 5)
				$dado = '<em><span style="color: #CCC">N�o informado</span></em>';
					
				echo "<td align='$alinhamento'>"
					. self::FormatarMaiusculasMinusculas($dado)
					. "</td>"; // imprime os valores da tabela
			}

			if ( ( $editar == true ) && ( $excluir == true ) && ( $visualizar == true ) ) {

				$crip = new Criptografia();

				$uriEditar = $crip->Cifrar("$editar&id={$linha['id']}");
				$uriExcluir = $crip->Cifrar("$excluir&id={$linha['id']}");
				$uriVisualizar = $crip->Cifrar("$visualizar&id={$linha['id']}");

				echo "<td align='center'><a href='?$uriEditar'>
					<img src='$arquivo?imagem=editar' alt='editar' border='0' /></a>
					  <a href='?$uriExcluir'>
					<img src='$arquivo?imagem=excluir' alt='excluir' border='0' /></a>
					 <a href='?$uriVisualizar'>
					<img src='$arquivo?imagem=listar' alt='Visualizar' border='0' /></a>
					</td>";
			} else {

			if ( $editar ) {

				$crip = new Criptografia();

				$uriEditar = $crip->Cifrar("$editar&id={$linha['id']}");

				echo "<td align='center'><a href='?$uriEditar'>
					<img src='$arquivo?imagem=editar' alt='editar' border='0' /></a>
					</td>";
			}
			if ( $excluir ) {

				$crip = new Criptografia();

				$uriExcluir = $crip->Cifrar("$excluir&id={$linha['id']}");

				echo "<td align='center'><a href='?$uriExcluir'>
					<img src='$arquivo?imagem=excluir' alt='excluir' border='0' /></a>
					</td>";
			}
			if ( $visualizar ) {

				$crip = new Criptografia();

				$uriVisualizar = $crip->Cifrar("$visualizar&id={$linha['id']}");

				echo "<td align='center'><a href='?$uriVisualizar'>
					<img src='$arquivo?imagem=listar' alt='Visualizar' border='0' /></a>
					</td>";
			}
			}
			echo '</tr>';

		}
		echo '</table>';
		
	}
	//--------------------------------------------------------------------------
	public static function FormatarMaiusculasMinusculas($nomeAntigo)
	{
		$excecoes = array('ACS', 'CNES', ' de ', ' da ', ' do ', ' das ',
				' dos ', ' com ', ' para ', ' por ', ' e ', 'BCG',
				'(DT)', ' tipo ', '(VOP)', ' B/C', 'DTP', ' oral ', ' em ',
				' AC ', ' AL ', ' AM ', ' AP ', ' BA ', ' CE ', ' DF ', ' ES ',
				' GO ', ' MA ', ' MG ', ' MS ', ' MT ', ' PA ', ' PB ', ' PE ',
				' PI ', ' PR ', ' RN ', ' RO ', ' RJ ', ' RR ', ' RS ', ' SC ',
				' SE ', ' SP ', ' TO ', ' no ', ' � ', ' � ');
		
		
		//--------
		
		$pre = $pos = false;
		
		if(!substr_count($nomeAntigo,'<img ') && !substr_count($nomeAntigo,'input ')) {
			
		$limpo = strip_tags($nomeAntigo, '<b>'); // strip_tags retorna o texto sem tags HTML
		
	    if($limpo != '' && $limpo != $nomeAntigo) {
			
	        $pre = substr($nomeAntigo, 0, strpos($nomeAntigo, $limpo));
	        $pos = substr($nomeAntigo, strlen($limpo) + strlen($pre) );
	        
	        $nomeAntigo = $limpo;
	    }
	    		
		//--------
		
	    //setlocale(LC_CTYPE, 'pt_BR');
	    setlocale(LC_ALL, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese');
	   
		$nomeMin = trim( strtolower($nomeAntigo) );
		$primeiras = ucwords($nomeMin);

		$formatado = str_ireplace($excecoes, $excecoes, $primeiras);
		
		}
		else $formatado = $nomeAntigo;
		
		return $pre.$formatado.$pos;
		
	}
	//--------------------------------------------------------------------------
	/**
	 * M�todo chamado pela classe Ajax, que instancia a classe passada por
	 * por par�metdo e usa o m�todo adequado, tamb�m passado por par�metro
	 *
	 * @param $classe string Nome da classe
	 * @param $metodo string Nome do m�todo
	 */
	public function Paginar($classe, $metodo)
	{
        // Transforma, p.ex: "ListarPessoa(maria, 1, 2, 3...)" em "listarPessoa"
        $nomeDaSessaoComArray = strtolower($metodo[0])
                              . substr($metodo, 1, strpos($metodo, '(') - 1 );

        // Excessao para a busca avancada, que � guardada no mesmo
        // array de listar pessoa:
        if($nomeDaSessaoComArray == 'buscaAvancada')
            $nomeDaSessaoComArray = 'listarPessoa';

        // Limpa o array com os registros da sessao:
        $_SESSION[$nomeDaSessaoComArray]['arr'] = array();

		// Instanciando a classe:
		$classeInstanciada = new $classe();
				
		// Para paginar, � necess�rio fazer consultas ao banco de dados, ent�o,
		// a classe instanciada dever� ter o m�todo que faz usar a base de dados
		// e s� continua suas opera��es se esse m�todo existir:
		if(method_exists($classeInstanciada, 'UsarBaseDeDados' )) {
			
			// O m�todo est� mandando os par�metros sem aspas, como em Faz(ta ta)
			// ao inv�s de Faz('ta ta'). Essa sequ�ncia resolve o problema em
			// todos os casos:
			$metodo = str_replace(array( ', ' , ' ,' ), ',', $metodo);
			
			$metodo = str_replace(array( '('  ,  ','  , ')' ),
								  array( '("' , '","' , '")' ), $metodo);	
			
			// Conectando a base de dados:
			$classeInstanciada->UsarBaseDeDados();
			
			// Debug que exibe qual a classe e qual o m�todo usado
			// Comentar online ?????????????????????
			//echo "M�todo: $classe->$metodo<br />";
			//var_dump($classeInstanciada);
			
			// Usando finalmente o m�todo para paginar. Exemplo do que � montado:
			// $pessoa->ListarPessoa("ALA","MAR","vazio","3613","vazio","2")
			eval( "\$classeInstanciada->{$metodo};" );
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * M�todo que desenha o navegador de pagina��o genericamente
	 *
	 * @param $totalDeRegistros int Total de linhas recuperadas da consulta
	 * @param $paginaEmSessao string Nome da p�gina da sess�o
	 * @param $classe string Nome da classe que deve ser instanciada
	 * @param $metodo Nome do m�todo que deve ser usado para montar a lista de
	 * 					resultados, com um limite que far� a pagina��o
	 */
	public function ControleDePaginacao($totalDeRegistros, $paginaEmSessao,
										$classe, $metodo, $altura = false)
	{
		// S� exibe o controle de pagina��o se precisar, ou seja: se a qtd de
		// registros for maior do que o limite
		if( $totalDeRegistros > Html::LIMITE) {
			
			// Setando valores adequados para cada controlador de navega��o:
			$pagina_atual = $_SESSION[$paginaEmSessao];
			$pagina_anterior = (($pagina_atual - 1 ) > 1) ? ($pagina_atual - 1) : 1;
            
			$ultima_pagina = ceil($totalDeRegistros / Html::LIMITE);
			$proxima_pagina = (($pagina_atual + 1) < $ultima_pagina) ? ($pagina_atual + 1 ) : $ultima_pagina;
			
			// Montando o nome e os valores dos par�metdos do m�todo recebido.
			// No caso, o �ltimo par�metro vai mudar de acordo com o n�mero da
			// p�gina calculada anteriormente
			$metodoPrimeiraPagina = str_replace('[paginaAtual]', 1, $metodo);
			$metodoPaginaAnterior = str_replace('[paginaAtual]', $pagina_anterior, $metodo);
			$metodoProximaPagina = str_replace('[paginaAtual]', $proxima_pagina, $metodo);
			$metodoUltimaPagina = str_replace('[paginaAtual]', $ultima_pagina, $metodo);
			$metodoInput = str_replace(array('[paginaAtual])', '[paginaAtual] )'), ' ', $metodo);
			
			// Para debug (comentar online) ???????????????????
			//echo "$pagina_anterior << <b>$pagina_atual</b> >> $proxima_pagina<br />de $ultima_pagina";
			
		    // Local das imagens para o precarregamento:
		    
		    // ?????????????????? TIRAR O 1000 depois! - o caminho absoluto �
		    // necess�rio em alguns casos por causa do ajax.
		    if(file_exists("{$_SERVER['DOCUMENT_ROOT']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoBranco_PrimeiraPag.png") ) {
		    
                $primeiraPagBranco = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoBranco_PrimeiraPag.png";
                $pagAnteriorBranco = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoBranco_PagAnterior.png";
                $proximaPagBranco = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoBranco_ProximaPag.png";
                $ultimaPagBranco = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoBranco_UltimaPag.png";
                
                $primeiraPagAmarelo = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoAmarelo_PrimeiraPag.png";
                $pagAnteriorAmarelo = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoAmarelo_PagAnterior.png";
                $proximaPagAmarelo = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoAmarelo_ProximaPag.png";
                $ultimaPagAmarelo = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoAmarelo_UltimaPag.png";
                
                $primeiraPagCinza = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoCinza_PrimeiraPag.png";
                $pagAnteriorCinza = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoCinza_PagAnterior.png";
                $proximaPagCinza = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoCinza_ProximaPag.png";
                $ultimaPagCinza = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/Imagens/Paginacao/botaoCinza_UltimaPag.png";
            }
            else {
            
                $primeiraPagBranco = $pagAnteriorBranco  = $proximaPagBranco
                                   = $ultimaPagBranco    = $primeiraPagAmarelo
                                   = $pagAnteriorAmarelo = $proximaPagAmarelo
                                   = $ultimaPagAmarelo   = $primeiraPagCinza
                                   = $pagAnteriorCinza   = $proximaPagCinza
                                   = $ultimaPagCinza     = false; 
            }

            self::PrecarregarImagens(array($primeiraPagBranco, $pagAnteriorBranco,
                $proximaPagBranco, $ultimaPagBranco, $primeiraPagAmarelo,
                $pagAnteriorAmarelo, $proximaPagAmarelo, $ultimaPagAmarelo,
                $primeiraPagCinza, $pagAnteriorCinza, $proximaPagCinza,
                $ultimaPagCinza) );
				
			// Desenhando o controlador de pagina��o:
			if( $altura == false ) echo "<div id='paginas' class='paginacao'>";
			else echo "<div id='paginas' class='paginacao' style='top: $altura'>";
			
			echo '<div style="margin: 7px">P�ginas</div>';

            if($pagina_atual == 1) {
				
				echo "<img src='$primeiraPagCinza'
				        border='0' alt='primeira p�gina' />";
			
			    echo "<img src='$pagAnteriorCinza'
				        border='0' alt='p�gina anterior' />";
			}
			
			else {
			
			    echo "<a href='javascript: Paginar(\"$classe\",
				    \"$metodoPrimeiraPagina\", \"listagem\")'
				    title='primeira p�gina'><img src='$primeiraPagBranco'
				        onmouseover='this.src=\"$primeiraPagAmarelo\"'
				        onmouseout='this.src=\"$primeiraPagBranco\"'
				        border='0' /></a>";
			
			    echo "<a href='javascript: Paginar(\"$classe\",
				    \"$metodoPaginaAnterior\", \"listagem\")'
				    title='p�gina anterior'><img src='$pagAnteriorBranco'
				        onmouseover='this.src=\"$pagAnteriorAmarelo\"'
				        onmouseout='this.src=\"$pagAnteriorBranco\"'
				        border='0' /></a>";
			}
			echo "<input type='text' name='atual' id='atual'
				value='$pagina_atual' maxlength='4'
				onkeypress='return Digitos(event, this)'
				title='Digite o n�mero da p�gina a qual deseja listar e pressione ENTER'
				
				onkeyup='if(this.value > 0 && this.value <= $ultima_pagina)
						IrParaPaginaComEnter(event, \"listagem\", \"$classe\",
						\"$metodoInput\" + this.value + \")\");
						else if (this.value.length > 1)
						alert(\"Digite um n�mero de p�gina v�lido.\");'
						
				style='width:24px; text-align:center; font-size:8px;
				font-family: verdana, arial, helvetica, sans;
				font-weight: bold'>";
					
			if($pagina_atual == $ultima_pagina) {
				
				echo "<img src='$proximaPagCinza'
				        border='0' alt='pr�xima p�gina' />";
			
			    echo "<img src='$ultimaPagCinza'
				        border='0' alt='�ltima p�gina' />";
			}
			
			else {		
			  
			    echo "<a href='javascript: Paginar(\"$classe\",
				    \"$metodoProximaPagina\", \"listagem\")'
				    title='pr�xima p�gina'><img src='$proximaPagBranco'
				        onmouseover='this.src=\"$proximaPagAmarelo\"'
				        onmouseout='this.src=\"$proximaPagBranco\"'
				        border='0' alt='pr�xima p�gina' title='pr�xima p�gina' /></a>";
			
			    echo "<a href='javascript: Paginar(\"$classe\",
				    \"$metodoUltimaPagina\", \"listagem\")'
				    title='�ltima p�gina'><img src='$ultimaPagBranco'
				        onmouseover='this.src=\"$ultimaPagAmarelo\"'
				        onmouseout='this.src=\"$ultimaPagBranco\"'
				        border='0' alt='�ltima p�gina' title='�ltima p�gina' /></a>";
			}
			
			echo "</div>"; // Finalizando a div do desenho do controlador
		}
	}
	//--------------------------------------------------------------------------
	public static function PrecarregarImagens(array $imagens)
    {
        foreach($imagens as $imagem) {
        
            echo "<div style='display: none'><img src='$imagem' alt='precarregando'/></div>";
        }
    }
	//--------------------------------------------------------------------------
	/**
	 * Lista as informa��es da quantidade de registros encontrados
	 */
	public static function ExibirInformacoesDeRegistrosEncontrados($totalDeRegistros)
	{
		$emPaginas = '';
		if( $totalDeRegistros > Html::LIMITE)
			$emPaginas = ', '
					   . ceil($totalDeRegistros / Html::LIMITE)
					   . ' p�gina(s) com m�ximo de '
					   . Html::LIMITE . ' registros cada';
	
		echo "<div id='totalDeRegistrosEncontrados'>Total: $totalDeRegistros registro(s) $emPaginas</div>";
	}
	//--------------------------------------------------------------------------
	/**
	 * M�todo que ve se a sess�o da p�gina atual j� existe (para ir para a p�gina
	 * certa, mesmo que o cara volte). Verifica antes se a sess�o para a
	 * pagina��o da p�gina j� existe. Se n�o existir, coloca 1.
	 *
	 * @param $pagina_atual int|false P�gina atual
	 * @param $nomeDaSessao string Nome que damos para tratar a p�gina atual da
	 *                          sess�o
	 *
	 */
	public function TratarPaginaAtual($pagina_atual, $nomeDaSessao)
	{
		// Se a pessoa muda o crit�rio da pesquisa, ent�o a p�gina atual tem
		// obrigatoriamente que ser a primeira:
		if( isset($_POST) && count($_POST) ) {
			$_SESSION[$nomeDaSessao] = 1;
			return 1;
		}
		
		if( !$pagina_atual && isset( $_SESSION[$nomeDaSessao]) )
				$pagina_atual = $_SESSION[$nomeDaSessao];
		
		if( !$pagina_atual && !isset( $_SESSION[$nomeDaSessao]) )
				$pagina_atual = 1;
		
		if( !isset( $_SESSION[$nomeDaSessao]) )
				$_SESSION[$nomeDaSessao] = 1;
		
		else $_SESSION[$nomeDaSessao] = $pagina_atual;
		
		return $pagina_atual;
		
	}
	//--------------------------------------------------------------------------
	public function PaginarVelho($sql, $bind_param_types, $bind_param_vars,
					$bind_param_vars, $bind_result, $limite_inicio, $limite_fim)
	{
		echo "<pre><br />\$sql=$sql";
		
		echo "<br />\$bind_param_types=$bind_param_types";
		
		echo "<br />\$bind_param_vars=$bind_param_vars"; print_r(explode('|', $bind_param_vars));
		
		// Criando variaveis e dando os valores certos para o bind_param:
		
		$i = 0;
		
		// Criando e atribuindo os valores para as vari�veis do bind_param:
		foreach(explode('|', $bind_param_vars) as $valor) {
		
			$i++;
			
			eval("\$var{$i} = '$valor';");
		}
		
		$stmt = $this->conexao->prepare($sql) or print $sql;
		
		$bindString = "\$stmt->bind_param('$bind_param_types', ";
		
		$i = 0;
		
		foreach(explode('|', $bind_param_vars) as $valor) {
		
			$i++;
			if($i == 1) $bindString .= "\$var{$i}";
			else        $bindString .= ", \$var{$i}";
		}
		
		$bindString .= ');';
		
		echo '<h4>', $bindString, '</h4>';
		
		// Cria o bind_param com quantos par�metros, de quantos tipos precisar
		eval($bindString);
		
		
		// Criando e zerando as vari�veis com os nomes passados (bind_result):
		eval ('$' . str_replace('|', ' = 0; $', $bind_result) . ' = 0;');	
		
		// Criando agora o bind_result com quantas vari�veis precisar
		$bindString = "\$stmt->bind_result(";
		
		$i = 0;
		foreach(explode('|', $bind_result) as $valor) {
			
			$i++;
			if($i == 1) $bindString .= "\${$valor}";
			else        $bindString .= ", \${$valor}";
		}
		
		$bindString .= ');';
		
		echo '<h4>', $bindString, '</h4>';
		
		eval($bindString);
		
		$stmt->execute();
		$stmt->store_result();
		
		$linhas = $stmt->num_rows;
		
		echo '<h1>', $linhas, '</h1>';
		
		if($linhas > 0) {
		
			$arr = array();
			while( $stmt->fetch() ) {
				
				eval ('echo $' . str_replace('|', ', $', $bind_result) . ', "<br />";');
				
			}
		}
		
		echo "<br />\$bind_result=$bind_result"; print_r(explode('|', $bind_result));
		
		echo '$' . str_replace('|', ' = 0; $', $bind_result) . ' = 0;';
				
		echo "<br />\$limite_inicio=$limite_inicio";
		
		echo "<br />\$limite_fim=$limite_fim";
		
		//**********************************************************************
		
		
		
	}
	//--------------------------------------------------------------------------
	public static function SortearImagem($caminho, $qtdImagens) 
	 {
		$num = mt_rand(1, $qtdImagens);

		echo "{$caminho}{$num}.jpg";
	 }
 	//--------------------------------------------------------------------------
}
