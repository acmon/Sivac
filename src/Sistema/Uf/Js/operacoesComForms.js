

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
//Usar onkeypress ao invés do onkeydown (na tag form) por causa do Opera:
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
    
        // Se o elemento for uma div, verifica se ela é uma aba:
        if(divs[i].nodeName.toLowerCase() == 'div') {

            // Se a id da div for a mesma que passou por parâmetro (acima)
            if(divs[i].getAttribute('id').indexOf(id) == 0) {
                // Mostra a div:

                divs[i].style.visibility = 'visible';
            }
            
            // Senão, verifica se contém a palavra "aba" no início da id,
            // escondendo assim, todas as outras "abas"
            else if(divs[i].getAttribute('id').indexOf('aba') == 0) {

                // Esconde a div:
                divs[i].style.visibility = 'hidden';
            }
            
        }
    }
	
	// Coloca o valor do input hidden, para dizer qual a aba o cara estava
	// antes de clicar no bot‹o que submete, dando post.
	document.getElementById(ultimaAba).value = id;
}
//------------------------------------------------------------------------------