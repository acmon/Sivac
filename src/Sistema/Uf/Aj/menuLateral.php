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