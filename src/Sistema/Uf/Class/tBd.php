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
 * Esta classe será reaproveitada para outros sistemas, porém acrescida de
 * operações de Banco de Dados utilizando PDO. No momento ela só será usada
 * para logar os erros de consultas e conexões em um arquivo, que estará em
 * uma pasta raiz do sistema.
 * 
 * @package Sivac/Class
 *
 * @author Maykon Monnerat (maykon_ttd@hotmail.com), v 1.0, 2008
 *
 * @copyright 2008 
 * 
 */

class Bd
{
    
	/**
	 * Para cada consulta, colocar "or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__)),
	 * que apresentará uma mensagem adequada ao usuário enquanto logará o erro
	 * em um arquivo que poderá ser mais tarde acessado. Neste arquivo terá a
	 * unidade, cidade, estado, data e hora, além do nome do usuário que estava
	 * utilizando o sistema no momento do erro e a própria mensagem de erro
	 * que aconteceu.
	 *
	 * @param String $mensagemDeErro Mensagem de erro que será logada.
	 */
	public static function TratarErroSql($mensagemDeErro,
							$arquivo = 'arquivo não informado', $linha = 0)
	{

        $arquivoDeLog = "{$_SERVER['DOCUMENT_ROOT']}/".Constantes::PASTA_SISTEMA."/errosSivac.log";
		
		date_default_timezone_set('America/Sao_Paulo');
		
		$unidade = 'Unidade indefinida';
		$cidade  = 'Cidade indefinida';
		$estado  = 'UF indefinido';
		$usuario = 'Usuário indefinido';
		
		$ip = Html::IpDoUsuario();
		
		if(strlen($mensagemDeErro) < 3) $mensagemDeErro = 'Sem mensagem de erro';
		
		if( isset($_SESSION['unidade_nome'])) $unidade = $_SESSION['unidade_nome']; 
		if( isset($_SESSION['cidade_nome']))  $cidade  = $_SESSION['cidade_nome'];
		if( isset($_SESSION['estado_id']))    $estado  = $_SESSION['estado_id'];
		if( isset($_SESSION['nome']))         $usuario = $_SESSION['nome'];
		
		$mensagemCompleta = '[' . date('Y/m/d h:i:s') . "] $arquivo (linha $linha)\n"
						  . "$ip - $unidade ($cidade/$estado) - $usuario\n"
						  . "$mensagemDeErro\n\n";
		
		echo '<div class="msgErro" style="visibility: visible"
			onclick="this.style.visibility=\'hidden\'">';
		
		echo '<p><label>Algum problema ocorreu ao tentar recuperar os dados.
			O sistema se tornou instável, provavelmente por causa de problemas
			com a sua conexão. Recarregue a página, e se não obtiver sucesso,
			tente novamente mais tarde.</label></p>';
		
		// ????????????
		//echo "<p>COMENTAR ISSO E TAMBÉM ABAIXO AO COLOCAR NO AR!<hr />$mensagemCompleta</p>";
		
		echo '</div>';
						  
		
		
		
		error_log($mensagemCompleta, 3, $arquivoDeLog);
	}
}
