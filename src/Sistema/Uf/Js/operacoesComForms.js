

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

//------------------------------------------------------------------------------
function TrocarDataInicioPara1900(checked)
{
    if(checked) {
        document.getElementById('data_inicio').disabled = true;
        document.getElementById('data_inicio_hidden').disabled = false;
        document.getElementById('data_inicio_hidden').value = '01/01/1900';
    }
    else {
        document.getElementById('data_inicio').disabled = false;
        document.getElementById('data_inicio_hidden').disabled = true;

    }
}
//------------------------------------------------------------------------------
function TratarHabilitacaoAoSelecionar(checked, elementos)
{
    if( checked )
    {
        for(i = 0; i < elementos.length; i++)
        {
            document.getElementById(elementos[i]).disabled = true;
        }
    }
    else
    {
        for(i = 0; i < elementos.length; i++)
        {
            document.getElementById(elementos[i]).disabled = false;
        }
    }
}
//------------------------------------------------------------------------------
function InverterSelecao(form, nomeDoCheck)
{
    for(i = 0; i < form.elements.length; i++) {

        if(form.elements[i].name == nomeDoCheck) {
            form.elements[i].checked = form.elements[i].checked ? false : true;
        }
     }
}
//------------------------------------------------------------------------------
function MarcarTodas(form, nomeDoCheck)
{
    for(i = 0; i < form.elements.length; i++) {

        if(form.elements[i].name == nomeDoCheck) {
            form.elements[i].checked = true;
        }
     }
}
//------------------------------------------------------------------------------
function DesmarcarTodas(form, nomeDoCheck)
{
    for(i = 0; i < form.elements.length; i++) {

        if(form.elements[i].name == nomeDoCheck) {
            form.elements[i].checked = false;
        }
     }
}
//------------------------------------------------------------------------------
//Usar onkeypress ao inv�s do onkeydown (na tag form) por causa do Opera:
function ImpedirSubmitComEnter(teclaPress)
{
    if (window.event) {
        var tecla = teclaPress.keyCode;
    } else {
        tecla = teclaPress.which;
    }
	
	if(teclaPress.srcElement) {
		tipoDoElemento = teclaPress.srcElement.type.toString().toLowerCase();
	}
	
	if(teclaPress.target) {
		tipoDoElemento = teclaPress.target.toString().toLowerCase();
	}
	
	if(tecla == 13 && (tipoDoElemento.indexOf('textarea') == -1) ) return false;
}
//------------------------------------------------------------------------------
function DesabilitarCampo(nomeDoCampo, checked)
{
	campo = document.getElementById(nomeDoCampo);

	campo.style.background = 'white';
	
	if(checked == true){
		campo.disabled = false;
		campo.value = ''; 
		campo.focus();
	}
	else {
		campo.value = 'data';
		campo.disabled = true;
	}
}
//------------------------------------------------------------------------------
// Abas

function ExibirAba(id, container, ultimaAba)
{

    // Pega todos os filhos de "container"
    var divs = document.getElementById(container).childNodes;

    // Pega a quantidade de elementos que tem dentro de "container"
    var qtdAbas = divs.length;

    // Percorre todos os elementos
    for(var i=0; i<qtdAbas; i++) {
    
        // Se o elemento for uma div, verifica se ela � uma aba:
        if(divs[i].nodeName.toLowerCase() == 'div') {

            // Se a id da div for a mesma que passou por par�metro (acima)
            if(divs[i].getAttribute('id').indexOf(id) == 0) {
                // Mostra a div:

                divs[i].style.visibility = 'visible';
            }
            
            // Sen�o, verifica se cont�m a palavra "aba" no in�cio da id,
            // escondendo assim, todas as outras "abas"
            else if(divs[i].getAttribute('id').indexOf('aba') == 0) {

                // Esconde a div:
                divs[i].style.visibility = 'hidden';
            }
            
        }
    }
	
	// Coloca o valor do input hidden, para dizer qual a aba o cara estava
	// antes de clicar no bot�o que submete, dando post.
	document.getElementById(ultimaAba).value = id;
}
//------------------------------------------------------------------------------