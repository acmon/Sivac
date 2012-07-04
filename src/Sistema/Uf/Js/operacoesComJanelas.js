

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

//Exibe o conte�do se tiver javascript habilitado:
function ExibirConteudo(nomeDaDiv)
{
	obj = document.getElementById(nomeDaDiv);
    obj.style.visibility= 'visible';
}
//------------------------------------------------------------------------------
function SetarFocoPrimeiroInput()
{
    if( typeof(document.forms[0]) == "undefined" || document.forms[0] == null )
		return true;

	for(var i=0; i<document.forms[0].elements.length; i++) {
		
		//alert(document.forms[0].elements[i].type);
		
		if(typeof(document.forms[0].elements[i]) == "undefined"
			|| document.forms[0].elements[i] == null) return false;
		
		if(document.forms[0].elements[i].type == 'text'
			|| document.forms[0].elements[i].type == 'textarea') {
			document.forms[0].elements[i].focus();
			return true;
		}
	}
}
//------------------------------------------------------------------------------
// Usar tamanho m�ximo de janela para 776 x 460
function AbrirJanela(url, top, left, width, height)
{
	if(typeof(top)  == "undefined" || top == null)  top = 100;
	if(typeof(left) == "undefined" || left == null) left = 100;
	if(typeof(width)  == "undefined" || width == null)  width = 776;
	if(typeof(height)  == "undefined" || height == null)  height = 460;

	window.open(url, '_blank', 'toolbar=no, location=no, status=no, menubar=no, '
	+ 'scrollbars=yes, resizable=no, width=' + width +', height=' + height
	+ ', top=' + top + ', left=' + left);
}
//------------------------------------------------------------------------------
function AtualizarOutraJanela(url) {
	window.opener.location.href = url;
}
//------------------------------------------------------------------------------
function CarregarPagina(pagina)
{
	//alert(pagina);
	window.location.href = '?' + pagina + '';
}
