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


?><div style="padding-left: 10px">Pesquisar</div>

<div id="pesquisaAjuda"><div style="float: left"><input type="text"
	name="pesquisa" id="pesquisa" style="width: 210px; background-color: #cae1f5;
	height: 18px; border: 1px solid #000"
	onblur="ValidarPesquisa(this, 4)"
	onkeyup="PesquisarComEnter(event, 'textoAjuda', this.value, 'conteudo')"/></div><div><input type="image"
	src="../Imagens/vazio.gif" border="0" width="15px" height="15px"
	alt="Pesquisar" title="Pesquisar"
	onclick="PesquisarAjuda('textoAjuda', document.getElementById('pesquisa').value, 'conteudo')" /></div>
</div>
<?php 

$ajuda = new Ajuda();

$ajuda->UsarBaseDeDados();

$ajuda->GerarMenu();