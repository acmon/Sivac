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



?>
<div id="intercorrenciasConteudo">
	<div align="center">
	<strong>Eventos adversos adicionadas anteriormente</strong>
	</div>
	<p>
	
	<?php

	//__________________________________________________________________________
		
		//$conexao = mysqli_connect();
		//$conexao->select_db($_SERVER['BD']);
		
		$conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
			$_SESSION['senha']);


		$conexao->select_db($_SESSION['banco']);
		
		#######################################################################
		
		$crip = new Criptografia();
		
		parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
		
		@$testeID = $id;
						
		
	//__________________________________________________________________________
		
	$rs = $conexao->prepare('SELECT DISTINCT usuario.nome, usuario.id
		FROM `usuariovacinado`, `usuariointercorrencia`, `usuario`
		WHERE usuario.ativo AND usuariointercorrencia.UsuarioVacinado_id = usuariovacinado.id 
		AND usuariovacinado.Usuario_id = usuario.id')
		or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
	
	$rs->bind_result($nome, $testeID);
	$rs->execute();
	
	while( $rs->fetch() ) {
		
		$end = $crip->Cifrar("pagina=Adm/intercorrenciasAdicionadas&id=$testeID"); 
		
		echo "<a href='?$end'>
		      <span class=Links>$nome</span></a><br />";
	}
	
	$rs->free_result();
	
	if( isset($id) && $id ) {
		
		$id = $conexao->real_escape_string($testeID);
		
		echo '<hr /><br />';
		echo '<strong>Usu�rio</strong><br /><br /><font color="#8B8B8B">';
		
		$sql ="SELECT usuario.nome as `usuario`, usuario.nascimento, 
		usuario.cartaosus, usuario.prontuario, DAY(usuario.nascimento) AS `dia`,
		MONTH(usuario.nascimento) AS `mes`,  YEAR(usuario.nascimento) AS `ano`, 
		usuario.cpf, usuario.sexo, estado.nome as `estado`,
		estado.id as `idestado`  FROM `usuario`, `cidade`, 
		`estado`, `endereco`, `bairro` 
		WHERE usuario.ativo AND bairro.ativo AND usuario.Endereco_id = endereco.id 
		AND endereco.Bairro_id = bairro.id 
		AND bairro.Cidade_id = cidade.id 
		AND cidade.Estado_id = estado.id 
		AND usuario.id= ?";
				
		$rs = $conexao->prepare($sql) or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
		
		$rs->bind_param('i', $id);
		
		$rs->bind_result($usuario, $nascimento, $cartaosus,
						 $prontuario, $dia, $mes, $ano, $cpf,
						 $sexo, $estado, $estado_id);
		$rs->execute();
		
		$dataClass = new Data();
		
		while( $rs->fetch() ) {
		
			
			$dataFormatoBR = $dataClass->InverterData($nascimento);
			
			echo '<strong>Nome:</strong> '.$usuario            . '<br /><br />';
			echo "<strong>Data de nascimento:</strong> $dataFormatoBR<br /><br />";
			
			echo '<strong>Cpf:</strong> '        . $cpf        . '<br /><br />';
			echo '<strong>Sexo:</strong> '       . $sexo       . '<br /><br />';
			echo '<strong>Estado:</strong> '     . $estado     . '<br /><br />';
			echo '<strong>Cart�o SUS:</strong> ' . $cartaosus  . '<br /><br />';
			echo '<strong>Prontuario:</strong> ' . $prontuario . '<br /><br />';
			
		}
		$rs->free_result();
		
		$sql = 'SELECT usuariovacinado.id AS `usuario_id`, 
			intercorrencia.eventoadverso, 
			intercorrencia.descricao, intercorrencia.tempo, 
			intercorrencia.frequencia, intercorrencia.id AS `intercorrencia_id`
			FROM `usuariovacinado`, `intercorrencia`, `usuariointercorrencia` 
			WHERE intercorrencia.ativo
			ANDusuariointercorrencia.UsuarioVacinado_id = usuariovacinado.id 
			AND intercorrencia.id = usuariointercorrencia.Intercorrencia_id 
			AND usuariovacinado.Usuario_id= ?';	
		
		$rs = $conexao->prepare($sql) or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
		
		$rs->bind_param('i', $id);
		
		$rs->bind_result($usuario_id, $eventoAdverso, $descricao,
						 $tempo, $frequencia, $intercorrencia_id);
		$rs->execute();

		echo '</font>
			<strong>Evento adverso</strong><br /><br /><font color="#8B8B8B">';
		
		while( $rs->fetch() ) {
		
			echo '<strong>Evento adverso: </strong>'. $eventoAdverso . '<br /><br />';
			echo '<strong>Descri��o: </strong>'     . $descricao . '<br /><br />';
			echo '<strong>Tempo: </strong>'         . $tempo . '<br /><br />';
			echo '<strong>Frenqu�ncia: </strong>'   . $frequencia . '<br /><br /><hr />';
		}
		
		$rs->free_result();
		 
		echo '</font>';
	}
	?>
</div>