
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

var arquivoAjax = 'http://' + window.location.host + '/ippes/Sistema/Uf/ajax.php';
var textoCifrado = '';
var textoDecifrado = '';
var intercorrencia_id = 0;
//------------------------------------------------------------------------------
function IncluirDoseEspecifica(checked, vacina_id, idDaDiv)
{


    var conteudo=document.getElementById(idDaDiv);

    if(checked) {
        
        conteudo.innerHTML = '';
    }
    else {

        if(vacina_id < 1) {

            alert('Escolha uma vacina para listar as doses da mesma.');
            return false;
        }

        http.open("GET", arquivoAjax + "?ajax=listarDosesDaVacina&codigodaVacina="
                + vacina_id, true);

        http.onreadystatechange = function ()
        {

            if (http.readyState == 4) {


                var texto=http.responseText;

                //Desfaz o urlencode
                texto=texto.replace("/\+/g", " ");
                texto=unescape(texto);
                conteudo.innerHTML=texto;

            }
        }
        http.send(null);
    }
}
//------------------------------------------------------------------------------
/**
sql: a consulta;
bind_param_types: os tipos do bind_param no mysql (string como 'siis')
bind_param_vars: vari‡veis do bind_param separadas por |
bind_result: vari‡veis de resultado que devem ser exibidas
limite_inicio: limite inicial da pagina?‹o
limite_fim: limite final da pagina?‹o
idDaDiv: div na qual o resultado dever‡ ser apresentado
*/
function PaginarVelho(sql, bind_param_types, bind_param_vars, bind_result,
				limite_inicio, limite_fim, idDaDiv)
{
	alert ('sql='+ sql + '; bind_param_types=' + bind_param_types + '; bind_param_vars='
		   + bind_param_vars + '; bind_result=' + bind_result + '; limite_inicio='
		   + limite_inicio + '; limite_fim=' + limite_fim + '; idDaDiv=' + idDaDiv);
	  
	http.open("GET", arquivoAjax + '?ajax=paginarVelho&'
			  + '&sql='+ sql
			  + '&bind_param_types=' + bind_param_types
			  + '&bind_param_vars=' + bind_param_vars
			  + '&bind_result=' + bind_result
			  + '&limite_inicio=' + limite_inicio
			  + '&limite_fim=' + limite_fim
			  , true);

  	http.onreadystatechange = function()
	{
		//Exibe o texto no div idDaDiv
		var conteudo=document.getElementById(idDaDiv);

		conteudo.innerHTML = '<em>Aguarde...</em>';

		if (http.readyState == 4) {

			var texto=http.responseText;

			//Desfaz o urlencode
			texto=texto.replace("/\+/g", " ");
			texto=unescape(texto);
			conteudo.innerHTML=texto;
		}
	}
	http.send(null);  
}
//------------------------------------------------------------------------------
function Paginar(classe, metodo, idDaDiv)
{
	//alert(classe + metodo + idDaDiv);
	
	http.open("GET", arquivoAjax + '?ajax=paginar&'
			  + '&classe='+ classe
			  + '&metodo=' + metodo
			  , true);

  	http.onreadystatechange = function()
	{
		//Exibe o texto no div idDaDiv
		var conteudo=document.getElementById(idDaDiv);

		conteudo.innerHTML = '<em>Aguarde...</em>';

		if (http.readyState == 4) {

			var texto=http.responseText;

			//Desfaz o urlencode
			texto=texto.replace("/\+/g", " ");
			texto=unescape(texto);
			conteudo.innerHTML=texto;
		}
	}
	http.send(null);  
}
//------------------------------------------------------------------------------
function ValidarSenhaDoAdministrador(idDaDiv, login, senha)
{
	if(senha.length < 5) return false;
	
    valores = "login=" + login + "&senha=" + senha;
    
	http.open("POST", arquivoAjax + '?ajax=validarSenhaDoAdministrador',
			true);
        
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.setRequestHeader("Content-length", valores.length);
    http.setRequestHeader("Connection", "close");
    
	http.onreadystatechange = function()
	{
		var conteudo = document.getElementById(idDaDiv);
	
		if (http.readyState == 4) {
        
            texto = unescape(http.responseText.replace("/\+/g", " "));

            if(texto.length > 10) {
            	
            	// Desabilita digitação, aparece ok:
        	    document.getElementById('senhaValidacao').disabled = true;
        	    document.getElementById('ok').style.visibility = 'visible';
        	    
        	    conteudo.innerHTML = ' ';
        	    
        	    if( idDaDiv == 'formAtivo') {
        	    
        	    	// Faz aparecer o form, desenhado pelo PHP:
        	    	conteudo.innerHTML += texto;
        	    }
        	    
        	    // Cria um input do tipo hidden só pra dizer que a senha ta ok:
	            conteudo.innerHTML += '<input type="hidden" name="senhaOk" value="ok" />';

                // e coloca foco no nome:
        	    document.getElementById('nome').focus();
            }
		}
	}

    http.send(valores);
}

//------------------------------------------------------------------------------
function PesquisarCidades( valor, cidade_id, idDoSelectDeDestino )
{

	if(typeof(idDoSelectDeDestino)  == "undefined" || idDoSelectDeDestino == null) {
		
		idDoSelectDeDestino = 'cidade';
	}
	
	if (valor == 0) {
	
		document.getElementById("cidade").options.length = 0;
        return false;

	}
 
	http.open("GET", arquivoAjax + "?ajax=pesquisarCidades&codigodoestado="
  			+ valor + "&codigocidade=" + cidade_id, true);


  
  	http.onreadystatechange = function ()
	{
		campo_select = document.getElementById(idDoSelectDeDestino);
		
		if (http.readyState == 4 && http.status == 200) {

		  	campo_select.options.length = 0;
			
			texto_completo = http.responseText;
			
		  	
		    results = texto_completo.split(",");
			
		    for( i = 0; i < results.length; i++ ) {
		    	
				string = results[i].split( "|" );
				
				if(string[0] != undefined) {
	
				campo_select.options[i] = new Option( string[0], string[1] );
	
					if(  string[2] == 1 ) {
						campo_select.options[i].selected=true;
					}
				}
			}
		    
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
function PesquisarUnidades( valor, tipoUnidade,idSelect )
{
 http.open("GET", arquivoAjax + "?ajax=pesquisarUnidades&cidade_id=" + valor +'&tipoUnidade='+tipoUnidade, true);

  
  	http.onreadystatechange = function ()
	{
		campo_select = document.getElementById(idSelect);
		
		if (http.readyState == 4) {

		  	campo_select.options.length = 0;
			
			texto_completo = http.responseText;
			
			//alert(texto_completo);
		  	
		    results = texto_completo.split(",");
			
		    for( i = 0; i < results.length; i++ ) {
		    	
				string = results[i].split( "|" );
				
				if(string[0] != undefined) {
	
				campo_select.options[i] = new Option( string[0], string[1] );
	
					if(  string[2] == 1 ) {
						campo_select.options[i].selected=true;
					} 
				}
			}
		    
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
function PesquisarUnidadesSemTipo( valor, idSelect)
{
 http.open("GET", arquivoAjax + "?ajax=pesquisarUnidadesSemTipo&cidade_id=" + valor, true);

  
  
  	http.onreadystatechange = function ()
	{
		campo_select = document.getElementById(idSelect);
		
		if (http.readyState == 4) {

		  	campo_select.options.length = 0;
			
			texto_completo = http.responseText;
			
			//alert(texto_completo);
		  	
		    results = texto_completo.split(",");
			
		    for( i = 0; i < results.length; i++ ) {
		    	
				string = results[i].split( "|" );
				
				if(string[0] != undefined) {
	
				campo_select.options[i] = new Option( string[0], string[1] );
	
					if(  string[2] == 1 ) {
						campo_select.options[i].selected=true;
					}
				}
			}
		    
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
function PesquisarAcs( valor, acs_id, idDoSelectDeDestino)
{

	if(typeof(idDoSelectDeDestino)  == "undefined" || idDoSelectDeDestino == null) {
		
		idDoSelectDeDestino = 'acs';
	}

	http.open("GET", arquivoAjax + "?ajax=pesquisarAcs&codigodaunidade="
  			+ valor + "&codigoacs=" + acs_id, true);
  
  	http.onreadystatechange = function ()
	{
		campo_select = document.getElementById(idDoSelectDeDestino);
		
		if (http.readyState == 4) {

		  	campo_select.options.length = 0;
			
			texto_completo = http.responseText;
		  				
		    results = texto_completo.split(",");
			
		    for( i = 0; i < results.length; i++ ) {
		    	
				string = results[i].split( "|" );
				
				if(string[0] != undefined) {
	
				campo_select.options[i] = new Option( string[0], string[1] );

                    if(  string[2] == 1 ) {
						campo_select.options[i].selected=true;
					}
				}
			}
		}
	}

	http.send(null);

}
//------------------------------------------------------------------------------
// Esta função foi necessária pois não foi possível usar sequencialmente
// PesquisarCidades(); PesquisarAcs();, por causa do Ajax ser assíncrono.
// Assim, tivemos de retornar o resultado todo ao mesmo tempo e depois dividir
// ainda mais.
function PesquisarCidadesEAcs(valor_estado, cidade_id, valor_unidade, acs_id)
{
	http.open("GET", arquivoAjax + "?ajax=pesquisarCidadesEAcs&codigodoestado="
  			+ valor_estado + "&codigocidade=" + cidade_id + "&codigodaunidade="
  			+ valor_unidade + "&codigoacs=" + acs_id, true);
  
  	http.onreadystatechange = function ()
	{
  		// Input da cidade:
		campo_selectCidade = document.getElementById("cidade");
		
		// Input do Acs
		campo_selectAcs = document.getElementById("acs");
		
		if (http.readyState == 4) {

			campo_selectCidade.options.length = campo_selectAcs.options.length = 0;
			
			texto_completoDosDois = http.responseText;
			
			// Divide: Antes do ';', retorna as cidades (posicao 0), e depois do
			// ';' retorna os Acs (posição 1)
			texto_completo = texto_completoDosDois.split(';');
		  	
			// resultados de cidades:
		    results = texto_completo[0].split(",");
			
		    // Fazendo o FOR para retornar as cidades
		    for( i = 0; i < results.length; i++ ) {
		    	
				string = results[i].split( "|" );
				
				if(string[0] != undefined) {
	
				campo_selectCidade.options[i] = new Option( string[0], string[1] );
	
					if(  string[2] == 1 ) {
						campo_selectCidade.options[i].selected=true;
					}
				}
			}
		    
		    // Resultados de Acs:
		    results = texto_completo[1].split(",");
		    
		    // Fazendo o FOR para os Acs
		    for( i = 0; i < results.length; i++ ) {
		    	
		    	string = results[i].split( "|" );
		    	
		    	if(string[0] != undefined) {
		    		
		    		campo_selectAcs.options[i] = new Option( string[0], string[1] );
		    		
		    		if(  string[2] == 1 ) {
		    			campo_selectAcs.options[i].selected=true;
		    		}
		    	}
		    }
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
function PesquisarIntercorrencia( valor )
{
  http.open("GET", arquivoAjax + "?ajax=pesquisarIntercorrencia&codigodaVacina="
  			+ valor, true);

  	http.onreadystatechange = function ()
	{
		campo_select = document.forms[1].opcoesIntercorrencias;

		if (http.readyState == 4) {

		  	campo_select.options.length = 0;
		    results = http.responseText.split(",");

		    for( i = 0; i < results.length; i++ ) {
				string = results[i].split( "|" );

				campo_select.options[i] = new Option( string[0], string[1] );

				if(  string[2] == 1 ) {
					campo_select.options[i].selected=true;
				}
			}
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
function ListarNotaDaVacina( valor, objeto, exibirLegenda )
{

 	http.open("GET", arquivoAjax + "?ajax=listarNotaDaVacina&exibirLegenda="+exibirLegenda+"&codigoDaVacina="
  			+ valor, true);

  	http.onreadystatechange = function ()
	{
		
		var conteudo = document.getElementById(objeto);
		
		if (http.readyState == 4) {

       	    var texto=http.responseText;

			texto=texto.replace("/\+/g", " ");

			texto=unescape(texto);
			   
		    conteudo.innerHTML = texto;
            
		}
		
	}
	http.send(null);
    
}
//------------------------------------------------------------------------------
function AdicionarDoses(qtd)
{
	insercoes = parseInt(qtd);

	if( isNaN(insercoes) ) return false;

	http.open("GET", arquivoAjax + "?ajax=adicionarDoses&qtd=" + insercoes, true);
	http.onreadystatechange = function ()
	{
		if (http.readyState == 4) {

			//Lê o texto
			var texto=http.responseText;

			//Desfaz o urlencode
			texto=texto.replace("/\+/g", " ");
			texto=unescape(texto);

			//Exibe o texto no div "cadaDose"
			var conteudo=document.getElementById("cadaDose");
			conteudo.innerHTML=texto;
		}
	}
	http.send(null);

    window.location.reload();
}
//------------------------------------------------------------------------------
function ListarEtnias(idDaDiv, checked)
{
	http.open("GET", arquivoAjax + "?ajax=listarEtnias", true);

	http.onreadystatechange = function()
	{
		//Exibe o texto no div "listarEtnias"
		var conteudo=document.getElementById(idDaDiv);

		if (http.readyState == 4) {

			if(checked){
				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
				conteudo.innerHTML=texto;
			}
			else {
				conteudo.innerHTML='';
			}
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
function ListarEstados(idDaDiv, checked)
{
	http.open("GET", arquivoAjax + "?ajax=listarEstados", true);

	http.onreadystatechange = function()
	{
		//Exibe o texto no div "listarEstados"
		var conteudo=document.getElementById(idDaDiv);

		if (http.readyState == 4) {

			if(checked){
				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
				conteudo.innerHTML=texto;
			}
			else {
				conteudo.innerHTML='';
			}
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
function ListarVacinas(idDaDiv, valor, listarDescontinuadas, retroativo)
{

	http.open("GET", arquivoAjax + "?ajax=listarVacinas&listarDescontinuadas="
				+ listarDescontinuadas + "&retroativo=" + retroativo, true);

	http.onreadystatechange = function()
	{
		//Exibe o texto no div "listarVacinas"
		var conteudo=document.getElementById(idDaDiv);

		if (http.readyState == 4) {

			if(valor == 'semCampanha'){
				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
				conteudo.innerHTML=texto;
			}
			else {
				conteudo.innerHTML='';
                document.getElementById('vacinasFilhas').innerHTML = '';
			}
		}
	}

	http.send(null);
    
}
//------------------------------------------------------------------------------
function CarregarVacinasFilhas(idDaDiv, vacina_id)
{

   
	http.open("GET", arquivoAjax + "?ajax=CarregarVacinasFilhas&vacina_id="+ vacina_id, true);

	http.onreadystatechange = function()
	{
		//Exibe o texto no div "listarVacinas"
		var conteudo=document.getElementById(idDaDiv);

		if (http.readyState == 4) {

				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
				conteudo.innerHTML=texto;
	
		}
	}
	http.send(null);
    
}
//------------------------------------------------------------------------------
function ListarVacinasDaCampanha(idDaDiv, campanhaId)
{
			
	http.open("GET", arquivoAjax + "?ajax=listarVacinasDaCampanha&" +
            "campanha_id=" + campanhaId, true);
	
	http.onreadystatechange = function()
	{
		//Exibe o texto no div "idDaDiv"
		var conteudo=document.getElementById(idDaDiv);
		
		if (http.readyState == 4) {
			
                    //Lê o texto
                    var texto=http.responseText;

                    //Desfaz o urlencode
                    texto=texto.replace("/\+/g", " ");
                    texto=unescape(texto);
                    conteudo.innerHTML=texto;
			
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
// Usada para remover os acentos da palavra que vai para a querystring:
function RemoverAcentos (text)
{                                                                   
	  text = text.replace(new RegExp('[ÁÀÂÃ]','gi'), 'A');
	  text = text.replace(new RegExp('[ÉÈÊ]','gi'), 'E');
	  text = text.replace(new RegExp('[ÍÌÎ]','gi'), 'I');
	  text = text.replace(new RegExp('[ÓÒÔÕ]','gi'), 'O');
	  text = text.replace(new RegExp('[ÚÙÛÜ]','gi'), 'U');
	  text = text.replace(new RegExp('[Ç]','gi'), 'C');
	  
	  return text;                 
}
//------------------------------------------------------------------------------
function ListarPessoa(idDaDiv, valor, mae, tipoRelatorio, data_inicio, data_fim,
						cidade, unidade, acs, cpf)
{

	if (( valor.length < 3 && valor.length != 0) || (mae.length < 3 && mae.length != 0 ) ) return false; 

	valor = RemoverAcentos(valor);
	mae = RemoverAcentos(mae);
	
	if(typeof(data_inicio)  == "undefined" || data_inicio == null  || data_inicio.length < 10) data_inicio = '0';
	if(typeof(data_fim)  == "undefined" || data_fim == null || data_fim.length < 10) data_fim = '0';
	if(typeof(cidade)  == "undefined" || cidade == null || cidade.length == 0) cidade = '0';
	if(typeof(unidade)  == "undefined" || unidade == null || unidade.length == 0) unidade = '0';
	if(typeof(acs)  == "undefined" || acs == null || acs.length == 0) acs = '0';
	if(typeof(cpf)  == "undefined" || cpf == null || cpf.length == 0) cpf = '0';
	
	//-------------------------
	//if(typeof(valor)  == "undefined" || valor == null || valor.length == 0) valor = '%';
	//if(typeof(mae)  == "undefined" || mae == null || mae.length == 0) mae = '%';
	//-------------------------

	cpf = cpf.replace(/\./g, '');
	cpf = cpf.replace(/\-/g, '');
	
	/*
	alert('idDaDiv: ' + idDaDiv
			+ ', valor: ' + valor
			+ ', mae: ' + mae
			+ ', tipoRelatorio: ' + tipoRelatorio
			+ ', data_inicio: ' + data_inicio
			+ ', data_fim: ' + data_fim
			+ ', cidade: ' + cidade
			+ ', unidade: ' + unidade
			+ ', acs: ' + acs
			+ ', cpf: ' + cpf); return false;
	*/
	 
	
	// Codifica p/ não ter barras na querystring. Decodificar c/ urldecode no PHP
	
	if( data_inicio != 0 && data_fim != 0) {
		
		data_inicio = data_inicio.replace(/\//g, '%2F');
		data_fim = data_fim.replace(/\//g, '%2F');
	}
	valor = valor.replace(/ /g, '+');
	
	mae = mae.replace(/ /g, '+');
	
	//alert(data_inicio + ' ' + data_fim + ' ' + valor);
	//alert('oi');
	
	var end = arquivoAjax + "?ajax=listarPessoa&pesquisa=" + valor + "&mae=" + mae + "&tipo=" 
	+ tipoRelatorio + "&datai=" + data_inicio + "&dataf=" + data_fim
	+ "&cidade=" + cidade + "&unidade=" + unidade + "&acs=" + acs + "&cpf=" + cpf;
	
	//document.write(end);
	
	//alert(tipoRelatorio);
	//alert(end);
	
	http.open("GET", end,  true);
	
	http.onreadystatechange = function()
	{
		//Exibe o texto no div "listarVacinas"
		var conteudo=document.getElementById(idDaDiv);
		
		if (http.readyState == 4) {

				//Lê o texto
				var texto=http.responseText;
				
				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
				conteudo.innerHTML=texto;
		}
	}
	//conteudo.innerHTML = 'asdfasdfasdf';
	//alert(texto);
	http.send(null);
}

//------------------------------------------------------------------------------
function ExibirIdade(idDaDiv, checked)
{
	http.open("GET", arquivoAjax + "?ajax=exibirIdade", true);

	http.onreadystatechange = function()
	{
		//Exibe o texto no div idDaDiv
		var conteudo=document.getElementById(idDaDiv);

		if (http.readyState == 4) {

			if(checked){
				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
				conteudo.innerHTML=texto;
			}
			else {
				conteudo.innerHTML='';
			}
		}
	}
	http.send(null);
}

//------------------------------------------------------------------------------
function PesquisarAjuda(idDaDiv, conteudoPesquisa, tipo)
{
	
	if(conteudoPesquisa.length < 4) return false;
	
    valores = "pesquisa=" + conteudoPesquisa + "&tipo=" + tipo;
	
	http.open("POST", arquivoAjax + '?ajax=pesquisarAjuda', true);
      
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    http.setRequestHeader("Content-length", valores.length);
    http.setRequestHeader("Connection", "close");
    
	http.onreadystatechange = function()
	{
		var conteudo = document.getElementById(idDaDiv);
	
		conteudo.innerHTML = '<em>Aguarde...</em>';
		
		if (http.readyState == 4) {
        
            texto = unescape(http.responseText.replace("/\+/g", " "));

            if(texto.length > 10) {
            	
                conteudo.innerHTML = texto;
            }
		}
	}
    http.send(valores);
}
//------------------------------------------------------------------------------
function PesquisarComEnter(teclaPress, idDaDiv, conteudoPesquisa, tipo)
{
	if (window.event) {
        var tecla = teclaPress.keyCode;
    } else {
        tecla = teclaPress.which;
    }
	
	if(tecla == 13) PesquisarAjuda(idDaDiv, conteudoPesquisa, tipo);
}
//------------------------------------------------------------------------------
function IrParaPaginaComEnter(teclaPress, idDaDiv, classe, metodo)
{
	if (window.event) {
        var tecla = teclaPress.keyCode;
    } else {
        tecla = teclaPress.which;
    }
	
	if(tecla == 13) Paginar(classe, metodo, idDaDiv);
}
//------------------------------------------------------------------------------
function ExibirSexo(idDaDiv, checked)
{
	http.open("GET", arquivoAjax + "?ajax=exibirSexo", true);

	http.onreadystatechange = function()
	{
		//Exibe o texto no div "listarSexo"
		var conteudo=document.getElementById(idDaDiv);

		if (http.readyState == 4) {

			if(checked){
				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
				conteudo.innerHTML=texto;
			}
			else {
				conteudo.innerHTML='';
			}
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
function ExibirCampoSenhaAdm(idDaDiv, checked)
{
	http.open("GET", arquivoAjax + "?ajax=exibirCampoSenhaAdm", true);

	http.onreadystatechange = function()
	{
		//Exibe o texto no div "confirmarSenhaRetroativo"
		var conteudo=document.getElementById(idDaDiv);

		if (http.readyState == 4) {

			if(checked){
				
				var texto=http.responseText;

				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
				conteudo.innerHTML=texto;
			}
			else {
				conteudo.innerHTML='';
			}
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
function ExibirBuscaPorEstadoCidade(idDaDiv, checked)
{
	http.open("GET", arquivoAjax + "?ajax=exibirBuscaPorEstadoCidade", true);

	http.onreadystatechange = function()
	{
		//Exibe o texto no div passada por parâmetro:
		var conteudo=document.getElementById(idDaDiv);

		if (http.readyState == 4) {

			if(checked){
				conteudo.innerHTML='';
			}
			else {
				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
				conteudo.innerHTML=texto;
			}
		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------
function GravarDataHoraConexao()
{
	http.open("GET", arquivoAjax + "?ajax=gravarDataHoraConexao", true);
	http.send(null);
}
//------------------------------------------------------------------------------
function Intervalo()
{
	// 300.000 milisegundos equivalem a cinco minutos:
	window.setInterval("GravarDataHoraConexao()", 300000); 
}
//------------------------------------------------------------------------------
// Verifica qual é o navegador e retorna um objeto do tipo adequado:
function getHTTPObject() {
  var xmlhttp;
  /*@cc_on
  @if (@_jscript_version >= 5)
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (e) {
        xmlhttp = false;
      }
    }
  @else
  xmlhttp = false;
  @end @*/
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    try {
      xmlhttp = new XMLHttpRequest();
    } catch (e) {
      xmlhttp = false;
    }
  }
  return xmlhttp;
}
//------------------------------------------------------------------------------
function Cifrar(qs)
{
    qs = escape(qs);
	http.open("GET", arquivoAjax + "?ajax=Cifrar&texto="+qs, true);

    var conteudo = '';

    textoCifrado = '';

    http.onreadystatechange = function()
	{

		if (http.readyState == 4) {

				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
                textoCifrado = texto;
                
		}
	}

	http.send(null);
    
    textoCifrado = texto;
}
//------------------------------------------------------------------------------
function Decifrar(qs)
{
    qs = escape(qs);
	http.open("GET", arquivoAjax + "?ajax=Decifrar&texto="+qs, true);

    //var conteudo = textoDecifrado = '';

    http.onreadystatechange = function()
	{

		if (http.readyState == 4) {

				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
                textoDecifrado = texto;
                
		}
	}
	http.send(null);

}
//------------------------------------------------------------------------------
function GravarNaSessao(sessao, dado)
{
    sessao = escape(sessao);
    dado = "'"+dado+"'";

	http.open("GET", arquivoAjax + "?ajax=GravarNaSessao&sessao="+sessao+"&dado="+dado, true);

    http.onreadystatechange = function()
	{
		if (http.readyState == 4) {

				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
                textoDecifrado = texto;

		}
	}
	http.send(null);
}
//------------------------------------------------------------------------------

function ExibirDetalhesIntercorrencia(url)
{
    var qs = 'pagina=exibirDetalhesIntercorrencia&intercorrencia_id=' + intercorrencia_id;
    qs = escape(qs);

    http.open("GET", arquivoAjax + "?ajax=Cifrar&texto="+qs, true);

    //----------
    http.onreadystatechange = function()
    {

        if (http.readyState == 4) {

                //Lê o texto
                var texto=http.responseText;

                //Desfaz o urlencode
                texto=texto.replace("/\+/g", " ");
                texto=unescape(texto);

                AbrirJanela(url + '/Uf/Pop?' + texto, 200, 200, 700, 460);

        }
    }
    http.send(null);
    //-----------
  
}

//------------------------------------------------------------------------------
function GravarIntercorrenciaSelecionada(id) // usado para o exibir detalhes
{
    
    http.open("GET", arquivoAjax + "?ajax=GravarIntercorrenciaSelecionada&intercorrencia_id="+id, true);   

    http.onreadystatechange = function()
	{

		if (http.readyState == 4) {

				//Lê o texto
				var texto=http.responseText;

				//Desfaz o urlencode
				texto=texto.replace("/\+/g", " ");
				texto=unescape(texto);
                intercorrencia_id = texto;
                
		}
	}

    http.send(null);
}
//------------------------------------------------------------------------------
// Cria um objeto http para Ajax do tipo adequado ao navegador usado:
var http = getHTTPObject();
//------------------------------------------------------------------------------