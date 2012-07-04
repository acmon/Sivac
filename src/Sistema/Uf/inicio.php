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


$host = $_SESSION['local'];
$bd = $_SESSION['banco'];
$usuario = $_SESSION['login'];
$senha = $_SESSION['senha'];

$conexao = new mysqli($host, $usuario, $senha, $bd);

$resultado = $conexao->prepare("SELECT titulo, texto FROM `textoinicio`")
	or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));

$resultado->execute();

$resultado->bind_result($titulo, $texto);

$resultado->fetch();

echo '<p><strong>';

$titulo = str_replace(array('\\r\\n','\\r','\\n'),'<br />', $titulo);
echo stripslashes($titulo);
	
echo '</strong></p><br />';	

echo '<div id="textoInicial">';

$texto = str_replace(array('\\r\\n','\\r','\\n'),'<br />', $texto);
echo stripslashes($texto);


$resultado->free_result();
$conexao->close();
?>
