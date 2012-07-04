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
 * Esta classe ser� reaproveitada para outros sistemas, por�m acrescida de
 * opera��es de Banco de Dados utilizando PDO. No momento ela s� ser� usada
 * para logar os erros de consultas e conex�es em um arquivo, que estar� em
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
	 * que apresentar� uma mensagem adequada ao usu�rio enquanto logar� o erro
	 * em um arquivo que poder� ser mais tarde acessado. Neste arquivo ter� a
	 * unidade, cidade, estado, data e hora, al�m do nome do usu�rio que estava
	 * utilizando o sistema no momento do erro e a pr�pria mensagem de erro
	 * que aconteceu.
	 *
	 * @param String $mensagemDeErro Mensagem de erro que ser� logada.
	 */
	public static function TratarErroSql($mensagemDeErro,
							$arquivo = 'arquivo n�o informado', $linha = 0)
	{

        $arquivoDeLog = "{$_SERVER['DOCUMENT_ROOT']}/".Constantes::PASTA_SISTEMA."/errosSivac.log";
		
		date_default_timezone_set('America/Sao_Paulo');
		
		$unidade = 'Unidade indefinida';
		$cidade  = 'Cidade indefinida';
		$estado  = 'UF indefinido';
		$usuario = 'Usu�rio indefinido';
		
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
			O sistema se tornou inst�vel, provavelmente por causa de problemas
			com a sua conex�o. Recarregue a p�gina, e se n�o obtiver sucesso,
			tente novamente mais tarde.</label></p>';
		
		// ????????????
		//echo "<p>COMENTAR ISSO E TAMB�M ABAIXO AO COLOCAR NO AR!<hr />$mensagemCompleta</p>";
		
		echo '</div>';
						  
		
		
		
		error_log($mensagemCompleta, 3, $arquivoDeLog);
	}
}
