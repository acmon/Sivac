


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

////------------------------------------------------------------------------------
// Seta o texto ao objeto:
function SetarTexto(objId, novoTexto)
{
	if(novoTexto.length > 1) {
		document.getElementById(objId).style.visibility = 'visible';
	}
	else {
		document.getElementById(objId).style.visibility = 'hidden';
	}

	with (document) if (getElementById && ((obj=getElementById(objId))!=null))
    with (obj) innerHTML = '<div class="barraDeTituloMsgErro">' +
	unescape(novoTexto) + '</div>';
}
//------------------------------------------------------------------------------
// Seta o texto ao objeto:
function SetarTextoDeMensagem(objId, novoTexto)
{	
	if(novoTexto.length > 1) {
		document.getElementById('containerDeMensagem').style.visibility = 'visible';
		document.getElementById('tituloMsgErro').style.visibility = 'visible';
		document.getElementById('mensagemDeErro').style.visibility = 'visible';
	}
	else {
		document.getElementById('containerDeMensagem').style.visibility = 'hidden';
		document.getElementById('tituloMsgErro').style.visibility = 'hidden';
		document.getElementById('mensagemDeErro').style.visibility = 'hidden';
	}
        
        document.getElementById(objId).innerHTML = unescape(novoTexto);
}
//------------------------------------------------------------------------------
// Valida um dia digitado
function ValidarDia(obj, opcional)
{
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	
	if(obj.value.length == 0) {
		SetarTextoDeMensagem("mensagemDeErro", "O dia se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}
	
	if(parseInt(obj.value, 10) > 0 && parseInt(obj.value, 10) < 32)	{
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
		
	SetarTextoDeMensagem("mensagemDeErro", "O dia � inv�lido.");
	MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
	return false;
}
//------------------------------------------------------------------------------
function ValidarVacinaFilha(vacina, vacinaFilha)
{
	if(vacina.valeu != 'undefined' && vacina.valeu != 0 && vacina.valeu != false)
    {
        if(vacinaFilha.value == 0) {
            SetarTextoDeMensagem("mensagemDeErro", "O tipo de aplica��o � inv�lido.");
            MudarPropriedade(vacinaFilha, 'backgroundColor', '#f4e5e0');

            return false;
        }
    }

    return true;
}
//------------------------------------------------------------------------------
// Valida um dia digitado

function ValidarMesBaseadoNoDia(objMes, objDia, opcional)
{
	if(((objMes.value.length == 0) || (objMes.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(objMes, 'backgroundColor', 'white');
		return true;
	}
	
	if(objMes.value.length == 0) {
		SetarTextoDeMensagem("mensagemDeErro", "O m�s se encontra vazio.");
		MudarPropriedade(objMes, 'backgroundColor', '#f4e5e0');
		return false;
	}
	
	if(parseInt(objMes.value, 10) > 0 && parseInt(objMes.value) < 13)	{
	
		if( ( (parseInt(objMes.value, 10) == 2) && (parseInt(objDia.value) > 29) )
			|| ( (parseInt(objMes.value, 10) == 4) && (parseInt(objDia.value) > 30) )
			|| ( (parseInt(objMes.value, 10) == 6) && (parseInt(objDia.value) > 30) )
			|| ( (parseInt(objMes.value, 10) == 9) && (parseInt(objDia.value) > 30) )
			|| ( (parseInt(objMes.value, 10) == 11) && (parseInt(objDia.value) > 30) ) ) {
			
			SetarTextoDeMensagem("mensagemDeErro", "O m�s n�o � compat�vel com o dia digitado.");
			MudarPropriedade(objMes, 'backgroundColor', '#f4e5e0');
			return false;
		}
	
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(objMes, 'backgroundColor', 'white');
		return true;
	}
		
	SetarTextoDeMensagem("mensagemDeErro", "O m�s � inv�lido.");
	MudarPropriedade(objMes, 'backgroundColor', '#f4e5e0');
	return false;
}
//------------------------------------------------------------------------------
// Muda uma propriedade do objeto:
function MudarPropriedade(obj, propriedade, valor)
{
    obj.style[propriedade] = valor; return true;

    //eval("obj.style."+propriedade+"='"+valor+"'");
    /*
	if (valor == true || valor == false)
		eval("obj.style."+propriedade+"="+valor);
	else eval("obj.style."+propriedade+"='"+valor+"'");
    */
}
//------------------------------------------------------------------------------
// Verifica se a String � um email v�lido:
function EmailValido(endereco)
{
	var reTipo = /^[\w-]+(\.[\w-]+)*@(([A-Za-z\d][A-Za-z\d-]{0,61}[A-Za-z\d]\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/;
	return reTipo.test(endereco);
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverArrobaArroba(st)
{
	var s = st.replace('@@','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverPontoPonto(st)
{
	var s = st.replace('..','.');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverTracoTraco(st)
{
	var s = st.replace('--','-');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverSublinhadoSublinhado(st)
{
	var s = st.replace('__','_');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverArrobaSublinhado(st)
{
	var s = st.replace('@_','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverSublinhadoArroba(st)
{
	var s = st.replace('_@','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverArrobaTraco(st)
{
	var s = st.replace('@-','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverTracoArroba(st)
{
	var s = st.replace('-@','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverPontoArroba(st)
{
	var s = st.replace('.@','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverArrobaPonto(st)
{
	var s = st.replace('@.','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverTracoPonto(st)
{
	var s = st.replace('-.','-');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverPontoTraco(st)
{
	var s = st.replace('.-','.');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverPontoSublinhado(st)
{
	var s = st.replace('._','.');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverSublinhadoPonto(st)
{
	var s = st.replace('_.','_');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverSublinhadoTraco(st)
{
	var s = st.replace('_-','_');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverTracoSublinhado(st)
{
	var s = st.replace('-_','-');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverPontoPrecedidoDeEspaco(st)
{
	var s = st.replace(' .','.');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverBarraInvertida(st)
{
	var s = st.replace(/\\/, '');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverDoisEspacos(st)
{
	// Remove dois espa�os:
	var s = st.replace('  ',' ');
	
	// Remove espa�o em branco no in�cio da string:
	if(s.length > 2 && s.charAt(0) == ' ') s = s.substr(1);
	
	return s;
}
//------------------------------------------------------------------------------
// Remove qualquer ocorr�ncia de barras invertidas:
function RemoverBarrasInvertidas(obj)
{
	obj.value = obj.value.replace(/\\/g, '');
}
//------------------------------------------------------------------------------
// Trim que remove espa�os em branco do in�cio e fim da string 
function Trim(obj)
{
	obj.value =  obj.value.replace(/^\s+|\s+$/, '');
	obj.value =  obj.value.replace(/^\s+|\s+$/, '');
}
//------------------------------------------------------------------------------
// Remove pontos, tra�os, etc do in�cio e fim da string
function RemoverSimbolosDeExtremidades(obj)
{
	obj.value =  obj.value.replace(/^\.+|\.+$/, '');
	obj.value =  obj.value.replace(/^\!+|\!+$/, '');
	obj.value =  obj.value.replace(/^\-+|\-+$/, '');
	obj.value =  obj.value.replace(/^\?+|\?+$/, '');
	
	obj.value =  obj.value.replace(/^\.+|\.+$/, '');
	obj.value =  obj.value.replace(/^\!+|\!+$/, '');
	obj.value =  obj.value.replace(/^\-+|\-+$/, '');
	obj.value =  obj.value.replace(/^\?+|\?+$/, '');
}
//------------------------------------------------------------------------------
function LimparString(obj)
{
	Trim(obj);
	RemoverSimbolosDeExtremidades(obj);
	RemoverBarrasInvertidas(obj);
}
//------------------------------------------------------------------------------
// Remove caracteres repetidos mais que 2 vezes, tipo aaaaaa...
function RemoverCaracteresRepetidos(st)
{
	var tam = st.length;
	var s = st;
	
	if(tam > 2) {
		for(var i=0; i<(tam - 2); i++) {
			
			var inteiro = parseInt(st.charAt(i)+st.charAt(i+1)+st.charAt(i+2));
			
			if (  isNaN(inteiro) ) { // S� trata o que for diferente de n�mero:
			
				if( st.charAt(i).toLowerCase() == st.charAt(i+1).toLowerCase()
					&& st.charAt(i).toLowerCase() == st.charAt(i+2).toLowerCase()){
						
					s = st.replace( (st.charAt(i) + st.charAt(i+1) + st.charAt(i+2)),
									(st.charAt(i) + st.charAt(i+i)) );
				}
			}
		}
	}
	
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inv�lido de caracteres:
function RemoverDoisEnters(st)
{
	var output = "";
	// Remove dois enters seguidos:
	
	// Remove enters no in�cio:
	// Para Windows:
	if ((st.charCodeAt(0) == 13) && (st.charCodeAt(1) == 10)) {
		output = st.substr(2);
	}
	
	// Para Unix:
	else if ((st.charCodeAt(0) == 13)){
		output = st.substr(1);
	}
	
	// Para Macintosh:
	else if ((st.charCodeAt(0) == 10)){
		output = st.substr(1);
	}
	
	// Remove dois enters:
	
	// Para Windows:
	for (var i = 0; i < st.length; i++) {
		if ((st.charCodeAt(i) == 13) && (st.charCodeAt(i + 1) == 10)
			&& (st.charCodeAt(i + 2) == 13) && (st.charCodeAt(i + 3) == 10)) {
			i+=3;
			output += '';
		}
		else if ((st.charCodeAt(i) == 13) && (st.charCodeAt(i + 1) == 13)) {
			i++;
			output += '';			
		}
		else if ((st.charCodeAt(i) == 10) && (st.charCodeAt(i + 1) == 10)) {
			i++;
			output += '';
		}
		
		else {
			output += st.charAt(i);
		}		
	}

	return output;
}
//------------------------------------------------------------------------------
function RemoverEspaco(st)
{
	// Remove espaco digitado:
	var s = st.replace(' ','');

	return s;
}
//------------------------------------------------------------------------------
function RemoverZerosNoInicio(obj)
{
	var s = obj.value;
	
	// Remove zeros do in�cio da string:
	
	if(s.charAt(0) == '0') s = obj.value.substr(1);
	if(s.substr(0, 2) == '00') s = obj.value.substr(2);
	if(s.substr(0, 3) == '000') s = obj.value.substr(3);
	if(s.substr(0, 4) == '0000') s = obj.value.substr(4);
	if(s.substr(0, 5) == '00000') s = obj.value.substr(5);
		
	obj.value = s;
}
//------------------------------------------------------------------------------

// Valida��o de descri��es, observa��es, etc.
function ValidarTextoLongo(obj, opcional, tamMinimo, permitirSemEspaco)
{
    if( parseInt(tamMinimo, 10) > 0 )
    {
        tamMinimo = parseInt(tamMinimo, 10);
    }
    else
    {
        tamMinimo = 10;
    }
    
    if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
            SetarTextoDeMensagem("mensagemDeErro", "");
            MudarPropriedade(obj, 'backgroundColor', 'white');
            return true;
    }

    if(obj.value.length == 0) {
            SetarTextoDeMensagem("mensagemDeErro", "O texto se encontra vazio.");
            MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
            return false;
    }

    if(obj.value.length < tamMinimo) {
            SetarTextoDeMensagem("mensagemDeErro",
                "O texto est� pequeno demais. Digite ao menos "
               + tamMinimo + " caracteres.");
           
            MudarPropriedade(obj, 'backgroundColor', 'red');
            return false;
    }

    var digitouEspaco = false;

    for(var i=0; i<obj.value.length; i++) {

            if(obj.value.charAt(i) == ' ') digitouEspaco = true;
    }

    if( !digitouEspaco && permitirSemEspaco != true) {
            SetarTextoDeMensagem("mensagemDeErro", "O texto � inv�lido.");
            MudarPropriedade(obj, 'backgroundColor', 'red');
            return false;
    }

    SetarTextoDeMensagem("mensagemDeErro", "");
    MudarPropriedade(obj, 'backgroundColor', 'white');
    return true;
}
//------------------------------------------------------------------------------
function ValidarAntibot(obj)
{
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "A palavra se encontra vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}
	if(obj.value.length < 4) {
		SetarTextoDeMensagem("mensagemDeErro", "A palavra est� pequena demais.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;	
}
//------------------------------------------------------------------------------
// Colocar mais valida��es adequadas (Pesquisar)
function ValidarMatricula(obj)
{
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "A matr�cula se encontra vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}
	if(obj.value.length < 3) {
		SetarTextoDeMensagem("mensagemDeErro", "A matr�cula est� pequena demais.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;	
}
//------------------------------------------------------------------------------
function ValidarSenha(obj)
{
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "A senha se encontra vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}
	if(obj.value.length < 4) {
		SetarTextoDeMensagem("mensagemDeErro", "A senha est� pequena demais.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	for(var i=0; i<obj.value.length; i++) {
		if(obj.value.charAt(i) == ' ') {
			SetarTextoDeMensagem("mensagemDeErro", "A senha n�o pode conter espa�o em branco.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;		
		}
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
function ValidarContraSenha(obj, confirmarSenha)
{
  with (document)
  	if ( getElementById && ((anterior=getElementById(confirmarSenha))!=null))
	
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "A contra-senha se encontra vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}
	
	if(obj.value.length < 4) {
		SetarTextoDeMensagem("mensagemDeErro", "A contra-senha est� pequena demais.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}	

	if(obj.value != anterior.value) {	
		SetarTextoDeMensagem("mensagemDeErro", "A senha foi confirmada incorretamente");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		MudarPropriedade(document.formulario.senha, 'backgroundColor', 'red');
		return false;		
	}

	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	MudarPropriedade(document.formulario.senha, 'backgroundColor', 'white');		
	return true;
}
//------------------------------------------------------------------------------
// Verifica se � um email v�lido:
function ValidarEmail(obj, opcional)
{
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O email se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}
	
	var valido = EmailValido(obj.value);
	
	if(valido) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}

	SetarTextoDeMensagem("mensagemDeErro", "email inv�lido.");
	MudarPropriedade(obj, 'backgroundColor', 'red');
	return false;
}
//------------------------------------------------------------------------------
// Verifica se a busca � v�lida:
function ValidarPesquisa(obj, tamanhoMinimo, opcional)
{
	
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	
	if(typeof(tamanhoMinimo)  == "undefined" || tamanhoMinimo == null) {
		
		tamanhoMinimo = 3;
	}
	
	if((obj.value == 'undefined') || (obj.value.length == 0)) {
		SetarTextoDeMensagem("mensagemDeErro", "A busca se encontra vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}

	for(var i=0; i<obj.value.length; i++) {
		
		if(obj.value.charAt(i) == '�' || obj.value.charAt(i) == '_' 
			|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
			|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
			|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
			|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
			|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
			|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
			|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
			|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
			|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
			|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
			|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
			|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == ',') {
		
			SetarTextoDeMensagem("mensagemDeErro", "Pesquisa inv�lida. Remova os s�mbolos.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}
	
	if(obj.value.length < tamanhoMinimo) {
		SetarTextoDeMensagem("mensagemDeErro", "A busca est� pequena. Digite ao menos " + tamanhoMinimo + " letras.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;		
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
// Verifica se a busca de nome parecido � v�lida:
function ValidarPesquisaNomeParecido(obj, tamanhoMinimo, opcional)
{

	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}

	if(typeof(tamanhoMinimo)  == "undefined" || tamanhoMinimo == null) {

		tamanhoMinimo = 10;
	}

	if((obj.value == 'undefined') || (obj.value.length == 0)) {
		SetarTextoDeMensagem("mensagemDeErro", "A busca se encontra vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}

	for(var i=0; i<obj.value.length; i++) {

		if(obj.value.charAt(i) == '�' || obj.value.charAt(i) == '_'
			|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
			|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
			|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
			|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
			|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
			|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
			|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
			|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
			|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
			|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
			|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
			|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == ',') {

			SetarTextoDeMensagem("mensagemDeErro", "Pesquisa inv�lida. Remova os s�mbolos.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}

	if(obj.value.length < tamanhoMinimo) {
		SetarTextoDeMensagem("mensagemDeErro", "A busca est� pequena. Digite ao menos " + tamanhoMinimo 
                                                + " letras para fazer uma busca significativa.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}

	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
// Verifica se � um login v�lido:
function ValidarLogin(obj)
{
	
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O login se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}
	
	if (obj.value.length < 4) {
	   SetarTextoDeMensagem("mensagemDeErro", "Seu login est� pequeno demais.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}
	
	if (obj.value.length > 25) {
	   SetarTextoDeMensagem("mensagemDeErro", "Seu login est� grande demais.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}
	
	for(var i=0; i<obj.value.length; i++) {
		if(obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
			|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
			|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
			|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
			|| obj.value.charAt(i) == '&' || obj.value.charAt(i) == '*'
			|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
			|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
			|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
			|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == ','
			|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
			|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '�' || obj.value.charAt(i) == '�'			
			|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
			|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '�'
			|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == '~') {
			
				SetarTextoDeMensagem("mensagemDeErro", "Login inv�lido.");
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}
			
	}
	if (obj.value.charAt(0) == '.' || obj.value.charAt(0) == '-' ||
		obj.value.charAt(0) == '_') {
		SetarTextoDeMensagem("mensagemDeErro", "Login inv�lido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	for(var i=0; i < 10; i++) {
		if ((obj.value.charAt(0) == i.toString() )) {
			SetarTextoDeMensagem("mensagemDeErro", "Login inv�lido.");

			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;	

}
//------------------------------------------------------------------------------
function ValidarTelDdd(obj)
{
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O DDD se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}

	var dddnum = parseInt(obj.value);
	
	if (dddnum > 9 && dddnum < 100) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;			
	}

	SetarTextoDeMensagem("mensagemDeErro", "O DDD � inv�lido.");
	MudarPropriedade(obj, 'backgroundColor', 'red');
	return false;	
}
//------------------------------------------------------------------------------
function ValidarTelLocal(obj, opcional)
{
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == false) {
		SetarTextoDeMensagem("mensagemDeErro", "O Telefone se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');		
		return true;
	}

	var primeirosDigitos = obj.value.substr(0, 4);
	var traco = obj.value.charAt(4);
	var ultimosDigitos = obj.value.substr(5);
			
	if ( primeirosDigitos.length == 4 && ultimosDigitos.length == 4
		&& !isNaN(primeirosDigitos) && !isNaN(ultimosDigitos)
		&& traco == '-') {
		
		if(document.formulario.ddd.value == undefined
			|| document.formulario.ddd.value == '') {
			
			SetarTextoDeMensagem("mensagemDeErro", "Selecione o DDD.");
			MudarPropriedade(document.formulario.ddd, 'backgroundColor', 'red');
			MudarPropriedade(obj, 'backgroundColor', 'white');
			return false;	
					
		}
		
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;			
	}

	SetarTextoDeMensagem("mensagemDeErro", "Telefone inv�lido. Preencha corretamente.");
	MudarPropriedade(obj, 'backgroundColor', 'red');
	return false;	
}
//------------------------------------------------------------------------------
function ValidarTelComum(obj, opcional)
{
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == false) {
		SetarTextoDeMensagem("mensagemDeErro", "O Telefone se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
	
		return true;
	
	}
	
	var digitosDdd = obj.value.substr(1, 2);
	var dddnum = parseInt(digitosDdd);
		
	if (dddnum < 10 && dddnum > 99) {
		
		SetarTextoDeMensagem("mensagemDeErro", "Telefone inv�lido. Preencha corretamente.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		
		return false;	
	}

	var primeirosDigitos = obj.value.substr(5, 4);
	var traco = obj.value.charAt(9);
	var ultimosDigitos = obj.value.substr(10);
			
	if ( !(primeirosDigitos.length == 4 && ultimosDigitos.length == 4
		&& !isNaN(primeirosDigitos) && !isNaN(ultimosDigitos)
		&& traco == '-') ) {
		
		SetarTextoDeMensagem("mensagemDeErro", "Telefone inv�lido. Preencha corretamente.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;			
	}

	return true;

}
//------------------------------------------------------------------------------
function ValidarCartaoSus(obj, opcional)
{
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == false) {
		SetarTextoDeMensagem("mensagemDeErro", "O n�mero do cart�o se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');		
		return true;
	}

	if(obj.value.length < 15) {
		SetarTextoDeMensagem("mensagemDeErro", "Prencha os 15 digitos do cart�o SUS ou deixe em branco.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;			
}
//------------------------------------------------------------------------------
function ValidarProntuario(obj, opcional)
{
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == false) {
		SetarTextoDeMensagem("mensagemDeErro", "O n�mero do prontu�rio se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');		
		return true;
	}

	if(obj.value.length < 2) {
		SetarTextoDeMensagem("mensagemDeErro", "Prencha o n�mero do prontu�rio corretamente ou deixe em branco.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;			
}
//------------------------------------------------------------------------------
// Validar nome:
function ValidarNome(obj, nomeDoCampo, opcional)
{
	if(opcional == true && ((obj.value.length == 0) || (obj.value == 'undefined'))) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;		
	}
		
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo + " ficou vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	if (obj.value.length < 3) {
		
		if(opcional == true)
			SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
					+ nomeDoCampo + " est� muito pequeno. Digite corretamente ou deixe em branco.");
		
		else SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
				+ nomeDoCampo + " est� muito pequeno.");
		
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	if(obj.name == 'logradouro') {
		
		for(var i=0; i<obj.value.length; i++) {
			
			if(obj.value.charAt(i) == ' '
				&&	i < (obj.value.length - 2) ) sobrenomePreenchido = true;
			
			if(obj.value.charAt(i) == '�' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '`') {
			
				SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
						+ nomeDoCampo + " ficou inv�lido.");
				
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}
		}
	}
		
	if(obj.name == 'nome' || obj.name == 'nomedamae' || obj.name == 'nomedopai') {
		
		// N�o aceitar nomes com menos que 5 caracteres e pelo menos um espa�o.
		// Aceitar pontos para abreviar quando se precede uma �nica letra.
		if (obj.value.length < 5) {
			if(opcional == true)
				SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
						+ nomeDoCampo + " est� muito pequeno. Digite corretamente ou deixe em branco.");
			
			else SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
					+ nomeDoCampo + " est� muito pequeno.");
			
			MudarPropriedade(obj, 'backgroundColor', 'red');
		   	return false;
		}
		
		var sobrenomePreenchido = false;
		
		for(var i=0; i<obj.value.length; i++) {
			
			if(obj.value.charAt(i) == ' '
				&&	i < (obj.value.length - 2) ) sobrenomePreenchido = true;
			
			if(obj.value.charAt(i) == '�' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == ',') {
			
				SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
						+ nomeDoCampo + " ficou inv�lido.");
				
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}

			// Aceitar pontos para abreviar quando se precede uma �nica letra.
			if( obj.value.indexOf('Dr.') == -1
				&&  obj.value.indexOf('Dra.') == -1
				&&  obj.value.indexOf('Dr�.') == -1
				&&  obj.value.charAt(i) == '.'){
					
				if(obj.value.charAt(i+1) != ' ' || obj.value.charAt(i-2) != ' '){
					
					SetarTextoDeMensagem("mensagemDeErro", "Use a abrevia��o corretamente para "
							+ nomeDoCampo + ".");
					
					MudarPropriedade(obj, 'backgroundColor', 'red');
					return false;					
				}
			}
		}
		if( sobrenomePreenchido == false ) {
			
			SetarTextoDeMensagem("mensagemDeErro", "Preencha tamb�m o segundo nome.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;	
		}
	}
		
	if(obj.name == 'logradouro' || obj.name == 'endereco') {
		
		// Aceitar pontos e tra�os, barras, v�rgulas, par�nteses
		for(var i=0; i<obj.value.length; i++) {
			if(    obj.value.charAt(i) == '�' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '`') {
				
				SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo
						+ " ficou inv�lido.");
				
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}
		}

		if (obj.value.length < 5) {
		   SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo +
				   " est� pequeno demais");
		   
		   MudarPropriedade(obj, 'backgroundColor', 'red');
		   return false;
		}			
	}	
	if(obj.name == 'bairro' ) {
		
		// Aceitar pontos e tra�os, barras, v�rgulas, par�nteses
		for(var i=0; i<obj.value.length; i++) {
			if(    obj.value.charAt(i) == '�' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '`') {
				
				SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo
						+ " ficou inv�lido.");
				
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}
		}

		if (obj.value.length < 3) {
		   SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo +
				   " ficou inv�lido.");
		   
		   MudarPropriedade(obj, 'backgroundColor', 'red');
		   return false;
		}			
	}
	
	// Veifica ponto, tra�o e sublinhado no in�cio:
	if (obj.value.charAt(0) == '.' || obj.value.charAt(0) == '-' ||
		obj.value.charAt(0) == '_') {
		
		SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo +
		   " ficou inv�lido.");
		
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	// Verifica n�meros no in�cio:
	for(var i=0; i < 10; i++) {
		if ((obj.value.charAt(0) == i.toString() )) {
			SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo +
			   " ficou inv�lido.");

			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
// Validar unidade de saude:
function ValidarUnidadeDeSaude(obj, opcional)
{	
	if(opcional == true && ((obj.value.length == 0) || (obj.value == 'undefined'))) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;		
	}
	
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O nome da unidade ficou vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
		// N�o aceitar nomes com menos que 5 caracteres e pelo menos um espa�o.
		// Aceitar pontos para abreviar quando se precede uma �nica letra.
		if (obj.value.length < 5) {
			if(opcional == true)
				SetarTextoDeMensagem("mensagemDeErro", "Nome da unidade muito pequeno. Preencha corretamente ou deixe em branco.");
			
			else SetarTextoDeMensagem("mensagemDeErro", "O nome da unidade est� pequeno demais. N�o abrevie.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
		   	return false;
		}
		
		for(var i=0; i<obj.value.length; i++) {
			
			if(obj.value.charAt(i) == '�' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == ',') {
			
				SetarTextoDeMensagem("mensagemDeErro", "Nome de unidade inv�lido.");
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}

		}
				
	// Veifica ponto, tra�o e sublinhado no in�cio:
	if (obj.value.charAt(0) == '.' || obj.value.charAt(0) == '-' ||
		obj.value.charAt(0) == '_') {
		SetarTextoDeMensagem("mensagemDeErro", "Nome de unidade inv�lido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	// Verifica n�meros no in�cio:
	for(var i=0; i < 10; i++) {
		if ((obj.value.charAt(0) == i.toString() )) {
			SetarTextoDeMensagem("mensagemDeErro", "Nome de unidade inv�lido.");

			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
// Validar nome da campanha:
function ValidarNomeCampanha(obj, opcional)
{


	if(opcional == true && ((obj.value.length == 0) || (obj.value == 'undefined'))) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;		
	}
	
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O nome da campanha ficou vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');

		return false;
	}
        
		// N�o aceitar nomes com menos que 5 caracteres e pelo menos um espa�o.
		// Aceitar pontos para abreviar quando se precede uma �nica letra.
		if (obj.value.length < 5) {
			if(opcional == true)
				SetarTextoDeMensagem("mensagemDeErro",
					"Nome da campanha muito pequeno. Preencha corretamente ou deixe em branco.");
			
			else SetarTextoDeMensagem("mensagemDeErro", "O nome da campanha est� pequeno demais. N�o abrevie.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
		   	return false;
		}
		
		for(var i=0; i<obj.value.length; i++) {
			
			if(obj.value.charAt(i) == '�' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '�'
				|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == ',') {
			
				SetarTextoDeMensagem("mensagemDeErro", "Nome de campanha inv�lido.");
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}

		}
				
	// Veifica ponto, tra�o e sublinhado no in�cio:
	if (obj.value.charAt(0) == '.' || obj.value.charAt(0) == '-' ||
		obj.value.charAt(0) == '_') {
		SetarTextoDeMensagem("mensagemDeErro", "Nome de campanha inv�lido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	// Verifica n�meros no in�cio:
	for(var i=0; i < 10; i++) {
		if ((obj.value.charAt(0) == i.toString() )) {
			SetarTextoDeMensagem("mensagemDeErro", "Nome de campanha inv�lido.");

			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
// Validar N�mero do Computador:
function ValidarNumeroDeComputador(obj)
{
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O n�mero do computador ficou vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}

	if (obj.value.length < 3) {
	   SetarTextoDeMensagem("mensagemDeErro", "O n�mero do computador est� pequeno demais.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}

	if (parseInt(obj.value) < 1) {
	   SetarTextoDeMensagem("mensagemDeErro", "O n�mero do computador � inv�lido.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
// Validar N�mero do Computador:
function ValidarIdade(obj)
{
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "A idade ficou vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}

	if (parseInt(obj.value) < 1) {
	   SetarTextoDeMensagem("mensagemDeErro", "A idade est� pequena demais.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}

	if (parseInt(obj.value) > 180) {
	   SetarTextoDeMensagem("mensagemDeErro", "A idade do computador est� grande demais.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
function ValidarCampoSelect(obj, nomeDoCampo, opcional)
{
	if(typeof(opcional)  == "undefined" || opcional == null)  opcional = false;
	
	if(opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');	
		return true;
	}
	if( obj.value == 'undefined' ) return false;
	
	if((obj.value.length == 0) || (obj.value == 'undefined') || (obj.value == '0')) {
		SetarTextoDeMensagem("mensagemDeErro", "A escolha de " + nomeDoCampo + " n�o foi feita.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');		
	return true;	
}
//------------------------------------------------------------------------------
function ValidarTelefone(obj, opcional)
{
	if(opcional == true && ((obj.value.length == 0) || (obj.value == 'undefined'))) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');	
		return true;
	}
	
	if ((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O telefone se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	var parenteseAbre = (obj.value.charAt(0) == '(');
	var parenteseFecha = (obj.value.charAt(3) == ')');
	var ddd = !(isNaN(parseInt(obj.value.substr(1, 2))));
	var espaco = (obj.value.charAt(4) == ' ');
	var primeirosDigitos = !(isNaN(parseInt(obj.value.substr(5, 4))));
	var traco = (obj.value.charAt(9) == '-');
	var ultimosDigitos = !(isNaN(parseInt(obj.value.substr(10, 4))));
	var ultimoDigito = !(isNaN(parseInt(obj.value.charAt(13))));
	
	if ( parenteseAbre && parenteseFecha && ddd && espaco
		&& primeirosDigitos && traco && ultimosDigitos && ultimoDigito) {
			
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "O telefone � inv�lido.");
	MudarPropriedade(obj, 'backgroundColor', 'red');		
	return false;		
}
//------------------------------------------------------------------------------
function ValidarCnes(obj, opcional)
{
	if(opcional == true && ((obj.value.length == 0) || (obj.value == 'undefined'))) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');	
		return true;
	}
	
	if ((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O Cnes se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	if ( obj.value.length == 7 ) {
			
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "O Cnes � inv�lido.");
	MudarPropriedade(obj, 'backgroundColor', 'red');		
	return false;		
}
//------------------------------------------------------------------------------
function ValidarInscricaoEstadual(obj)
{
	if (((obj.value.length == 0) || (obj.value == 'undefined')) && document.formulario.isento.checked == false) {
		SetarTextoDeMensagem("mensagemDeErro", "A Inscri��o estadual se encontra vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	var primeiraSequencia = parseInt(obj.value.substr(0, 2));
	var ponto1 = obj.value.charAt(2);
	var segundaSequencia = parseInt(obj.value.substr(3, 3));
	var ponto2 = obj.value.charAt(6);
	var terceiraSequencia = parseInt(obj.value.substr(7));

	if (  (primeiraSequencia > 9 && primeiraSequencia < 100)
	   && (segundaSequencia > 99 && segundaSequencia < 1000)
	   && (terceiraSequencia > 99 && terceiraSequencia < 1000)
	   && (ponto1 = ponto2 = '.'
	   && document.formulario.isento.checked == false) ) {
		
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	
	if (document.formulario.isento.checked == false) {		
		SetarTextoDeMensagem("mensagemDeErro", "A Inscri��o Estadual � inv�lida.");
		MudarPropriedade(obj, 'backgroundColor', 'red');		
		return false;
	}
	
	if (document.formulario.isento.checked) {		
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'silver');		
		return true;
	}
}
//------------------------------------------------------------------------------
function VerificarInscricaoEstadualIsento(obj)
{
	if(obj.checked) {
		MudarPropriedade(document.formulario.inscricaoEstadual, 'backgroundColor', 'silver');
		MudarPropriedade(document.formulario.inscricaoEstadual, 'color', 'gray');
	}
	else {
		MudarPropriedade(document.formulario.inscricaoEstadual, 'backgroundColor', 'white');
		MudarPropriedade(document.formulario.inscricaoEstadual, 'color', 'black');
	}
	
	ValidarInscricaoEstadual(document.formulario.inscricaoEstadual);
}
//------------------------------------------------------------------------------
function ValidarMoeda(obj)
{
    if ((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O valor se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}

    SetarTextoDeMensagem("mensagemDeErro", "");
    MudarPropriedade(obj, 'backgroundColor', 'white');
    return true;
}
//------------------------------------------------------------------------------
function ValidarData(obj, opcional)
{
   if ( typeof(opcional) == "undefined" || opcional == null ) opcional = false;

        if(opcional == true && ((obj.value.length == 0) || (obj.value == 'undefined'))) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	if ((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "A data se encontra vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}

	// parseInt tem um bug com os n�meros 08 e 09 ;), por isso tem-se que
	// especificar a base decimal para o casting:
	var dia = parseInt(obj.value.substr(0, 2), 10);
	var barra1 = obj.value.substr(2, 1);
	var mes = parseInt(obj.value.substr(3, 2), 10);
	var barra2 = obj.value.substr(5, 1);
	var ano = parseInt(obj.value.substr(6, 4), 10);

	
	if (  (dia > 0 && dia < 32)
	   && (mes > 0 && mes < 13)
	   && (barra1 = barra2 = '/')
	   && (ano > 1850 && ano < 2099) ) {
		
		if ( dia > 29 && mes == 2 ) {
		
		}
		else {

			// Verificar se o ano � bisexto:
			if( obj.value.substr(0, 2) == '29' &&
				obj.value.substr(3, 2) == '02' ) {
				
				ano = obj.value.substr(6, 4);
				
				if(ano.length == 4) {

					if(!AnoBisexto(ano)) {
						
						SetarTextoDeMensagem("mensagemDeErro", "Este n�o � um ano bisexto.");
						MudarPropriedade(obj, 'backgroundColor', 'red');
						return false;
					}
				}
			}
			
			SetarTextoDeMensagem("mensagemDeErro", "");
			MudarPropriedade(obj, 'backgroundColor', 'white');
			return true;
		}
	}	

	SetarTextoDeMensagem("mensagemDeErro", "A data � inv�lida.");
	MudarPropriedade(obj, 'backgroundColor', 'red');		
	return false;		
}

//------------------------------------------------------------------------------
function ValidarCep(obj, opcional)
{
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	
	if ((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O CEP se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	var partes = obj.value.split('-'); // Equivale ao PHP Explode ;-)
	
	if(typeof(partes[0]) == 'undefined' || typeof(partes[1]) == 'undefined') {
			SetarTextoDeMensagem("mensagemDeErro", "O CEP � inv�lido.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
	}
		
	if(partes[0].length != 5 || partes[1].length != 3 ) {
		SetarTextoDeMensagem("mensagemDeErro", "O CEP � inv�lido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;		
	}
		
	for(var i=0; i<5; i++) {
		if( !(obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") ) {
			SetarTextoDeMensagem("mensagemDeErro", "O CEP � inv�lido.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}
	if (obj.value.charAt(5) != '-') {
		SetarTextoDeMensagem("mensagemDeErro", "O CEP � inv�lido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;		
	}
	
	for(var i=6;  i < obj.value.length; i++) {
		if( !(obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") ) {
			SetarTextoDeMensagem("mensagemDeErro", "O CEP � inv�lido.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
function ValidarSexo(obj)
{
	if (document.formulario.sexo[0].checked
		|| document.formulario.sexo[1].checked) {		
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(document.formulario.sexo[0], 'backgroundColor', 'white');	
		MudarPropriedade(document.formulario.sexo[1], 'backgroundColor', 'white');	
		return true;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "Escolha o sexo.");
	MudarPropriedade(document.formulario.sexo[0], 'backgroundColor', 'red');	
	MudarPropriedade(document.formulario.sexo[1], 'backgroundColor', 'red');		
	return false;
}
//------------------------------------------------------------------------------
function ValidarAcamado(obj)
{
	if (document.formulario.acamado[0].checked
		|| document.formulario.acamado[1].checked) {		
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(document.formulario.acamado[0], 'backgroundColor', 'white');	
		MudarPropriedade(document.formulario.acamado[1], 'backgroundColor', 'white');	
		return true;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "Escolha o sexo.");
	MudarPropriedade(document.formulario.acamado[0], 'backgroundColor', 'red');	
	MudarPropriedade(document.formulario.acamado[1], 'backgroundColor', 'red');		
	return false;
}
//------------------------------------------------------------------------------
function ValidarHora(obj)
{
	if ((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "A hora se encontra vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}

	var horas = parseInt( obj.value.substr(0, 2) );
	var doisPontos = obj.value.substr(2, 1);
	var minutos = parseInt( obj.value.substr(3) );

	if ( obj.value.substr(3).length < 2 ) {
		SetarTextoDeMensagem("mensagemDeErro", "Complete os minutos.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}

	if (horas < 24
		&& minutos < 60
		&& doisPontos == ':') {
			SetarTextoDeMensagem("mensagemDeErro", "");
			MudarPropriedade(obj, 'backgroundColor', 'white');
			return true;
	}

	SetarTextoDeMensagem("mensagemDeErro", "A hora � inv�lida.");
	MudarPropriedade(obj, 'backgroundColor', 'red');
	return false;

}//------------------------------------------------------------------------------
function ValidarHoraFutura(obj,horaPassada, dataHoje, dataPassada)
{
    
   dataHoje = dataHoje.toString();

    var dia = parseInt(dataHoje.substr(0, 2));
	var mes = parseInt(dataHoje.substr(3, 2));
	var ano = parseInt(dataHoje.substr(6, 4));

    dataHoje = ano+''+mes+''+dia;

	dia = parseInt(dataPassada.substr(0, 2));
	mes = parseInt(dataPassada.substr(3, 2));
	ano = parseInt(dataPassada.substr(6, 4));
    
    dataPassada = ano+''+mes+''+dia;

    if(dataHoje <= dataPassada) {
        
        var momentoAtual = new Date()

        var hora = momentoAtual.getHours();
        var minuto = momentoAtual.getMinutes();

        var intHora = horaPassada[0]+horaPassada[1]+horaPassada[3]+horaPassada[4];

        if( (intHora) > (hora+''+minuto)) {

            SetarTextoDeMensagem("mensagemDeErro", "A hora � maior que a atual.");
            MudarPropriedade(obj, 'backgroundColor', 'red');
            return false;

        }
    

    SetarTextoDeMensagem("mensagemDeErro", "");
    MudarPropriedade(obj, 'backgroundColor', 'white');

    }
    return true;

}
//------------------------------------------------------------------------------
// Equivalente ao PHP para transformar todos as primeiras letras das palavras
// em mai�sculas.
function Ucwords(str)
{
	var novoTexto = str.replace(/^(.)|\s(.)/g, function ( $1 ) {
		return $1.toUpperCase ( );
	} );
	
	return novoTexto;
}
//------------------------------------------------------------------------------
// Equivalente ao PHP strtolower para transformar strings em min�sculos:
function Strtolower(str)
{
	var novoTexto = str.toLowerCase();
	return novoTexto;	
}
//------------------------------------------------------------------------------
// Prepara o nome para ser inserido no banco somente com a inicial mai�scula,
// mantendo somente as conjun��es como de, da, etc.
function FormatarNome(obj, teclaPress)
{
    if (window.event) {
        var tecla = teclaPress.keyCode;
    } else {
        tecla = teclaPress.which;
    }

    var s = new String(obj.value);
    // Remove todos os caracteres � seguir: ( ) / - . e espa�o, para tratar a string denovo.
    s = s.replace(/(\.|\(|\)|\/|\-| )+/g,'');

    if ( tecla != 9 && tecla != 8 && tecla != 16 && tecla != 27 && tecla != 33
		&& tecla != 34 && tecla != 35 && tecla != 36 && tecla != 37 && tecla != 38
		&& tecla != 39 && tecla != 40 && tecla != 45 && tecla != 46
		&& tecla != 17 && tecla != 18 && tecla != 20 && tecla != 19
		&& tecla != 144  && tecla != 145  && tecla != 112  && tecla != 113
		&& tecla != 114  && tecla != 115  && tecla != 116  && tecla != 117
		&& tecla != 118  && tecla != 119  && tecla != 120  && tecla != 121
		&& tecla != 122  && tecla != 123) {
		
			obj.value = obj.value.toUpperCase();
	}
}
//------------------------------------------------------------------------------
// OBS. N�O FUNCIONA NO OPERA INICIALMENTE.
//--->Fun��o para a formata��o dos campos...<---
function Mascara(tipo, campo, teclaPress) {
    if (window.event)
    {
        var tecla = teclaPress.keyCode;
    } else {
        tecla = teclaPress.which;
    }

    if(tecla == 13 || tecla == 8) return true;
    
    var s = new String(campo.value);
    // Remove todos os caracteres � seguir: ( ) / - . e espa�o, para tratar a string denovo.
    s = s.replace(/(\.|\(|\)|\/|\-| )+/g,'');

    tam = s.length + 1;

    if ( tecla != 9 && tecla != 8 && tecla != 16 && tecla != 27 && tecla != 33
		&& tecla != 34 && tecla != 35 && tecla != 36 && tecla != 37 && tecla != 38
		&& tecla != 39 && tecla != 40 && tecla != 45 && tecla != 46
		&& tecla != 17 && tecla != 18 && tecla != 20 && tecla != 19
		&& tecla != 144  && tecla != 145  && tecla != 112  && tecla != 113
		&& tecla != 114  && tecla != 115  && tecla != 116  && tecla != 117
		&& tecla != 118  && tecla != 119  && tecla != 120  && tecla != 121
		&& tecla != 122  && tecla != 123) {
		
		// Remove ponto precedido de espa�o de todos os tipos
		campo.value = RemoverPontoPrecedidoDeEspaco(campo.value);
		
		// Remove barra invertida de todos os tipos
		campo.value = RemoverBarraInvertida(campo.value);
		
        switch (tipo) {
		case 'NOME' :
			campo.value = RemoverDoisEspacos(campo.value);
			campo.value = RemoverCaracteresRepetidos(campo.value);
			
			var naoValidoNoInicio = " 1234567890@-_./-()!@#%�&*+=_;<>,?:|~^][{}`�";
			
			// Remove traco, ponto, etc. do inicio da String:
			if ( naoValidoNoInicio.indexOf(campo.value.charAt(0)) > -1 ) {
				var s = campo.value.substr(1);
				campo.value = s;
			}
			
			break;
			
		case 'TEXTO' :
			campo.value = RemoverDoisEspacos(campo.value);
			campo.value = RemoverDoisEnters(campo.value);
			campo.value = RemoverCaracteresRepetidos(campo.value);
			
			var naoValidoNoInicio = " 1234567890@-_./-()!@#%�&*+=_;<>,?:|~^][{}`�";
			
			// Remove traco, ponto, etc. do inicio da String:
			if ( naoValidoNoInicio.indexOf(campo.value.charAt(0)) > -1 ) {
				var s = campo.value.substr(1);
				campo.value = s;
			}			
			
			break;
			
		case 'LOGIN' :
				var strValidos = "abcdefghijklmnopqrstuvwxyz1234567890-_.";
				var naoValidoNoInicio = "1234567890@-_.";
				var tam = campo.value.length;

				// Vare e remove o caracter n�o permitido:
				for( var i = 0; i < tam; i++) {
					if ( strValidos.indexOf(campo.value.charAt(i)) == -1 ) {
						var s = campo.value.substr(0, i) + campo.value.substr(i+1);
						campo.value = s;
					}
				}
				
				// Remove conjunto inv�lido de caracteres:
				
				campo.value = RemoverEspaco(campo.value);
				campo.value = RemoverTracoPonto(campo.value);
				campo.value = RemoverPontoTraco(campo.value);
				campo.value = RemoverPontoSublinhado(campo.value);
				campo.value = RemoverSublinhadoPonto(campo.value);
				campo.value = RemoverSublinhadoTraco(campo.value);
				campo.value = RemoverTracoSublinhado(campo.value);
				campo.value = RemoverPontoPonto(campo.value);
				campo.value = RemoverTracoTraco(campo.value);
				campo.value = RemoverSublinhadoSublinhado(campo.value);
				campo.value = RemoverArrobaSublinhado(campo.value);
				campo.value = RemoverSublinhadoArroba(campo.value);
				campo.value = RemoverArrobaTraco(campo.value);
				campo.value = RemoverTracoArroba(campo.value);
				campo.value = RemoverPontoArroba(campo.value);
				campo.value = RemoverArrobaPonto(campo.value);
				
				// Remove traco, ponto, etc. do inicio da String:
				if ( naoValidoNoInicio.indexOf(campo.value.charAt(0)) > -1 ) {
					var s = campo.value.substr(1);
					campo.value = s;
				}
				break;
		
        case 'SENHA' :
				campo.value = RemoverEspaco(campo.value);
			break;
			
        case 'CPF' :
            if (tam > 3 && tam < 7)
                campo.value = s.substr(0,3) + '.' + s.substr(3, tam);
            if (tam >= 7 && tam < 10)
                campo.value = s.substr(0,3) + '.' + s.substr(3,3) + '.' + s.substr(6,tam-6);
            if (tam >= 10 && tam < 12)
                campo.value = s.substr(0,3) + '.' + s.substr(3,3) + '.' + s.substr(6,3) + '-' + s.substr(9,tam-9);
			break;

        case 'CNPJ' :

            if (tam > 2 && tam < 6)
                campo.value = s.substr(0,2) + '.' + s.substr(2, tam);
            if (tam >= 6 && tam < 9)
                campo.value = s.substr(0,2) + '.' + s.substr(2,3) + '.' + s.substr(5,tam-5);
            if (tam >= 9 && tam < 13)
                campo.value = s.substr(0,2) + '.' + s.substr(2,3) + '.' + s.substr(5,3) + '/' + s.substr(8,tam-8);
            if (tam >= 13 && tam < 15)
                campo.value = s.substr(0,2) + '.' + s.substr(2,3) + '.' + s.substr(5,3) + '/' + s.substr(8,4)+ '-' + s.substr(12,tam-12);
			break;
			
        case 'INSC' :

            if (tam > 2 && tam < 6)
                campo.value = s.substr(0,2) + '.' + s.substr(2, tam);
            if (tam >= 6 && tam < 9)
                campo.value = s.substr(0,2) + '.' + s.substr(2,3) + '.' + s.substr(5,tam-5);
            if (tam >= 9 && tam < 13)
                campo.value = s.substr(0,2) + '.' + s.substr(2,3) + '.' + s.substr(5);
				
			
			MudarPropriedade(campo, 'backgroundColor', 'white');
			MudarPropriedade(campo, 'color', 'black');
			document.formulario.isento.checked = false;
			
			break;

        case 'TEL' :
            if (tam > 2 && tam < 4)
                campo.value = '(' + s.substr(0,2) + ') ' + s.substr(2,tam);
            if (tam >= 7 && tam < 11)
                campo.value = '(' + s.substr(0,2) + ') ' + s.substr(2,4) + '-' + s.substr(6,tam-6);
			break;
			
        case 'TELLOCAL' :
            if (tam >= 5)
                campo.value = s.substr(0,4) + '-' + s.substr(4);
			break;

        case 'DATA' :
        	
        	// Na posi��o 0, n�o permitir n�meros maiores que 3:
        	if(((tecla > 51 && tecla < 58) || tecla > 99 ) && tam < 2){
        		return false;
        	} 

        	// Na posi��o 1, se o n�mero anterior for 3, ent�o n�o permitir
        	// n�mero maiores que 1:
        	if(((tecla > 49 && tecla < 58) || tecla > 97 ) && tam == 2
        			&& campo.value.charAt(0) == '3'){
        		return false;
        	}
        	
        	// Na posi��o 1, se o n�mero anterior for 0, ent�o n�o permitir
        	// outro zero (dia 00)
        	if((tecla == 48 || tecla == 96 ) && tam == 2
        			&& campo.value.charAt(0) == '0'){
        		return false;
        	}

        	// Na posi��o 4, se o n�mero anterior for 0, ent�o n�o permitir
        	// outro zero (mes 00)
        	if((tecla == 48 || tecla == 96 ) && tam == 4
        			&& campo.value.charAt(3) == '0'){
        		return false;
        	}
			
        	// Na posi��o 3, n�o permitir n�meros maiores que 1 (m�s at� 12)
        	if(((tecla > 49 && tecla < 58) || tecla > 97 ) && tam == 3){
        		return false;
        	}
        	
        	// Na posi��o 4, n�o permitir n�meros maiores que 2 (m�s at� 12) se
        	// o n�mero anterior for 1
        	if(((tecla > 50 && tecla < 58) || tecla > 98 ) && tam == 4
        			&& campo.value.charAt(3) == '1'){
        		return false;
        	}
        	
        	// N�o permitir 30 ou 31 de fevereiro:
        	if((tecla == 50 || tecla == 98 ) && tam == 4
        			&& campo.value.charAt(3) == '0'
        			&& campo.value.charAt(0) == '3' ) {
        		return false;
        	}
        	
        	// N�o permitir anos que comecem com 3000:
        	if(((tecla > 50 && tecla < 58 ) || tecla > 98 ) && tam == 5 ) {
        		return false;
        	}
			
        	// N�o permitir anos que comecem com 0:
        	if((tecla == 48 || tecla == 96 ) && tam == 5 ) {
        		return false;
        	}
			
        	// N�o permitir anos cheguem a 2100:
        	if(((tecla > 48 && tecla < 58) || tecla > 96 ) && tam == 6
				&& campo.value.charAt(6) == '2') {
        		return false;
        	}
			
        	// N�o permitir anos sejam menores que 1900:
        	if((tecla != 105 && tecla != 57 ) && tam == 6
				&& campo.value.charAt(6) == '1') {
        		return false;
        	}
						
			if(campo.value.substr(0, 2) == '31' && campo.value.length < 6) {
				//alert(campo.value.substr(0, 2));
				

				// N�o permite os meses 02, 04, 06 e 09
				if(campo.value.charAt(3) == '0'
					// coloquei "&& campo.value.length < 5" pois n�o aceitava
					// anos que se iniciassem com 2000.
					&& campo.value.length < 5
					&& ( tecla == 50 || tecla == 52 ||
						 tecla == 54 || tecla == 57 ||
						 tecla == 98 || tecla == 100 ||
						 tecla == 102 || tecla == 105) ) {
						 
						 return false;

				} 
				// N�o permite o mes 11
				if(campo.value.charAt(3) == '1'
					&& ( tecla == 49 || tecla == 97 )) {
						 
						 return false;
				}
			}
			
			// Verificar se o ano � bisexto:
			if( campo.value.substr(0, 2) == '29' &&
				campo.value.substr(3, 2) == '02' ) {
				
				ano = campo.value.substr(6, 4);
				
				if(ano.length == 4) {
	
					if(!AnoBisexto(ano)) {
				
						campo.value = campo.value.substr(0, 9);
						//alert('Digite um ano bisexto!');
						return false;
					}
				}
				
			}
        	
            if (tam > 2 && tam < 4)
                campo.value = s.substr(0,2) + '/' + s.substr(2, tam);
            if (tam > 4 && tam < 11)
                campo.value = s.substr(0,2) + '/' + s.substr(2,2) + '/' + s.substr(4,tam-4);
			break;
        
        case 'CEP' :
            if (tam > 5 && tam < 7)
                campo.value = s.substr(0,5) + '-' + s.substr(5, tam);
        break;
	case 'HORA' :
		//na posi��o 0 somente n�mero de 0 a 2 s�o permitidos
		if(((tecla > 51 && tecla < 58) || tecla > 98 ) && tam < 2){
        		return false;
        	}
		//na posi��o 1 somente numero, mas se a posi�ao anterior for
		//2 somente sera permitido de 0 a 3
		if(((tecla > 51 && tecla < 58) || tecla > 99 ) && tam == 2
        			&& campo.value.charAt(0) == '2'){
        		return false;
        	}
		//na posicao 3 s� ser� permitidos n�mero de 0 a 5
		if(((tecla > 53 && tecla < 58) || tecla > 101 ) && tam == 4){
        		return false;
        	}
            if (tam > 2 && tam < 4)
                campo.value = s.substr(0,2) + ':' + s.substr(2, tam);
				
			// Na posi��o 0, n�o permitir n�meros maiores que 2:
        	/*
			if(( !(tecla > 47 && tecla < 51) ) && tam < 2){
        		return false;
        	}
																						   */

        	// Na posi��o 1, se o n�mero anterior for 3, ent�o n�o permitir
        	// n�mero maiores que 1:
			/*
        	if(((tecla > 49 && tecla < 58) || tecla > 97 ) && tam == 2
        			&& campo.value.charAt(0) == '3'){
        		return false;
        	} */
			
 			break;
			
		case 'EMAIL' :
				var strValidos = "abcdefghijklmnopqrstuvwxyz1234567890@-_.";
				var naoValidoNoInicio = "@-_.";
				var tam = campo.value.length;

				// Vare e remove o caracter n�o permitido:
				for( var i = 0; i < tam; i++) {
					if ( strValidos.indexOf(campo.value.charAt(i)) == -1 ) {
						var s = campo.value.substr(0, i) + campo.value.substr(i+1);
						campo.value = s;
					}
				}
				
				// Remove conjunto inv�lido de caracteres:
				campo.value = RemoverEspaco(campo.value);
				campo.value = RemoverTracoPonto(campo.value);
				campo.value = RemoverPontoTraco(campo.value);
				campo.value = RemoverPontoSublinhado(campo.value);
				campo.value = RemoverSublinhadoPonto(campo.value);
				campo.value = RemoverSublinhadoTraco(campo.value);
				campo.value = RemoverTracoSublinhado(campo.value);				
				campo.value = RemoverArrobaArroba(campo.value);
				campo.value = RemoverPontoPonto(campo.value);
				campo.value = RemoverTracoTraco(campo.value);
				campo.value = RemoverSublinhadoSublinhado(campo.value);
				campo.value = RemoverArrobaSublinhado(campo.value);
				campo.value = RemoverSublinhadoArroba(campo.value);
				campo.value = RemoverArrobaTraco(campo.value);
				campo.value = RemoverTracoArroba(campo.value);
				campo.value = RemoverPontoArroba(campo.value);
				campo.value = RemoverArrobaPonto(campo.value);
				
				// Remove traco, ponto, etc. do inicio da String:
				if ( naoValidoNoInicio.indexOf(campo.value.charAt(0)) > -1 ) {
					var s = campo.value.substr(1);
					campo.value = s;
				}
			
			break;
            
        case 'MOEDA' :
        
            var SeparadorMilesimo = '.';
            var SeparadorDecimal = ',';
            var sep = 0;
            var key = '';
            var i = j = 0;
            var len = len2 = 0;
            var strCheck = '0123456789';
            var aux = aux2 = '';
            
            //var whichCode = (window.Event) ? teclaPress.which : teclaPress.keyCode;
            
            //if (whichCode == 13) return true; // Enter - envia o form
            //if (whichCode == 8) return true;  // Backspace - permite voltar caracteres
            
            key = String.fromCharCode(tecla); // Valor para o c�digo da Chave
            if (strCheck.indexOf(key) == -1) return false; // Chave inv�lida
            
            len = campo.value.length;
            
            for(i = 0; i < len; i++)
                if ((campo.value.charAt(i) != '0')
                && (campo.value.charAt(i) != SeparadorDecimal))
                    
                    break;
            
            aux = '';
            
            for(; i < len; i++)
                if (strCheck.indexOf(campo.value.charAt(i))!=-1)
                    aux += campo.value.charAt(i);
                    
            aux += key;
            len = aux.length;
            
            if (len == 0) campo.value = '';
            if (len == 1) campo.value = '0'+ SeparadorDecimal + '0' + aux;
            if (len == 2) campo.value = '0'+ SeparadorDecimal + aux;
            
            if (len > 2) {
                aux2 = '';
                for (j = 0, i = len - 3; i >= 0; i--) {
                    if (j == 3) {
                        aux2 += SeparadorMilesimo;
                        j = 0;
                    }
                    aux2 += aux.charAt(i);
                    j++;
                }
                campo.value = '';
                len2 = aux2.length;
                
                for (i = len2 - 1; i >= 0; i--)
                campo.value += aux2.charAt(i);
                campo.value += SeparadorDecimal + aux.substr(len - 2, len);
            }
            
            return false;
            break;
        }
    }
}
//------------------------------------------------------------------------------
//--->Fun��o para verificar se o valor digitado � n�mero...<---
function Digitos(event){
    if (window.event) {
        // IE
        key = event.keyCode;
    } else if ( event.which ) {
        // netscape
        key = event.which;
    }
    if ( key != 8 || key != 13 || key < 48 || key > 57 )
        return ( ( ( key > 47 ) && ( key < 58 ) ) || ( key == 8 ) || ( key == 13 ) );
    return true;
}
//------------------------------------------------------------------------------
function AnoBisexto(ano)
{
	if(((parseInt(ano, 10) % 4 == 0) && (parseInt(ano, 10) % 100 != 0)) 
		|| parseInt(ano, 10) % 400 == 0) {
		
		return true;
	}
	return false;
}
//------------------------------------------------------------------------------
/**
 * CPF - CNPJ:
 * PROT�TIPOS:
 * m�todo String.lpad(int pSize, char pCharPad)
 * m�todo String.trim()
 *
 * String unformatNumber(String pNum)
 * String formatCpfCnpj(String pCpfCnpj, boolean pUseSepar, boolean pIsCnpj)
 * String dvCpfCnpj(String pEfetivo, boolean pIsCnpj)
 * boolean ValidarCpf(String pCpf)
 * boolean ValidarCnpj(String pCnpj)
 * boolean ValidarCpfCnpj(String pCpfCnpj)
 */


NUM_DIGITOS_CPF  = 11;
NUM_DIGITOS_CNPJ = 14;
NUM_DGT_CNPJ_BASE = 8;


/**
 * Adiciona m�todo lpad() � classe String.
 * Preenche a String � esquerda com o caractere fornecido,
 * at� que ela atinja o tamanho especificado.
 */
String.prototype.lpad = function(pSize, pCharPad)
{
	var str = this;
	var dif = pSize - str.length;
	var ch = String(pCharPad).charAt(0);
	for (; dif>0; dif--) str = ch + str;
	return (str);
} //String.lpad


/**
 * Adiciona m�todo trim() � classe String.
 * Elimina brancos no in�cio e fim da String.
 */
String.prototype.trim = function()
{
	return this.replace(/^\s*/, "").replace(/\s*$/, "");
} //String.trim


/**
 * Elimina caracteres de formata��o e zeros � esquerda da string
 * de n�mero fornecida.
 * @param String pNum
 * 	String de n�mero fornecida para ser desformatada.
 * @return String de n�mero desformatada.
 */
function unformatNumber(pNum)
{
	return String(pNum).replace(/\D/g, "").replace(/^0+/, "");
} //unformatNumber


/**
 * Formata a string fornecida como CNPJ ou CPF, adicionando zeros
 * � esquerda se necess�rio e caracteres separadores, conforme solicitado.
 * @param String pCpfCnpj
 * 	String fornecida para ser formatada.
 * @param boolean pUseSepar
 * 	Indica se devem ser usados caracteres separadores (. - /).
 * @param boolean pIsCnpj
 * 	Indica se a string fornecida � um CNPJ.
 * 	Caso contr�rio, � CPF. Default = false (CPF).
 * @return String de CPF ou CNPJ devidamente formatada.
 */
function formatCpfCnpj(pCpfCnpj, pUseSepar, pIsCnpj)
{
	if (pIsCnpj==null) pIsCnpj = false;
	if (pUseSepar==null) pUseSepar = true;
	var maxDigitos = pIsCnpj? NUM_DIGITOS_CNPJ: NUM_DIGITOS_CPF;
	var numero = unformatNumber(pCpfCnpj);

	numero = numero.lpad(maxDigitos, '0');
	if (!pUseSepar) return numero;

	if (pIsCnpj)
	{
		reCnpj = /(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/;
		numero = numero.replace(reCnpj, "$1.$2.$3/$4-$5");
	}
	else
	{
		reCpf  = /(\d{3})(\d{3})(\d{3})(\d{2})$/;
		numero = numero.replace(reCpf, "$1.$2.$3-$4");
	}
	return numero;
} //formatCpfCnpj


/**
 * Calcula os 2 d�gitos verificadores para o n�mero-efetivo pEfetivo de
 * CNPJ (12 d�gitos) ou CPF (9 d�gitos) fornecido. pIsCnpj � booleano e
 * informa se o n�mero-efetivo fornecido � CNPJ (default = false).
 * @param String pEfetivo
 * 	String do n�mero-efetivo (SEM d�gitos verificadores) de CNPJ ou CPF.
 * @param boolean pIsCnpj
 * 	Indica se a string fornecida � de um CNPJ.
 * 	Caso contr�rio, � CPF. Default = false (CPF).
 * @return String com os dois d�gitos verificadores.
 */
function dvCpfCnpj(pEfetivo, pIsCnpj)
{
	if (pIsCnpj==null) pIsCnpj = false;
	var i, j, k, soma, dv;
	var cicloPeso = pIsCnpj? NUM_DGT_CNPJ_BASE: NUM_DIGITOS_CPF;
	var maxDigitos = pIsCnpj? NUM_DIGITOS_CNPJ: NUM_DIGITOS_CPF;
	var calculado = formatCpfCnpj(pEfetivo, false, pIsCnpj);
	calculado = calculado.substring(2, maxDigitos);
	var result = "";

	for (j = 1; j <= 2; j++)
	{
		k = 2;
		soma = 0;
		for (i = calculado.length-1; i >= 0; i--)
		{
			soma += (calculado.charAt(i) - '0') * k;
			k = (k-1) % cicloPeso + 2;
		}
		dv = 11 - soma % 11;
		if (dv > 9) dv = 0;
		calculado += dv;
		result += dv
	}

	return result;
} //dvCpfCnpj


/**
 * Testa se a String pCpf fornecida � um CPF v�lido.
 * Qualquer formata��o que n�o seja algarismos � desconsiderada.
 * @param String pCpf
 * 	String fornecida para ser testada.
 * @return <code>true</code> se a String fornecida for um CPF v�lido.
 */
function ValidarCpf(obj, opcional)
{
	var pCpf = obj.value;
	
	if(opcional == true && pCpf.length == 0) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
		
	if(opcional == true && pCpf.length > 0 &&  pCpf.length < 14 ) {
		SetarTextoDeMensagem("mensagemDeErro", "CPF inv�lido. Preencha corretamente ou deixe em branco.");
		MudarPropriedade(obj, 'backgroundColor', 'red');		
		return false;
	}
	
	if(pCpf.length == 0) {
		SetarTextoDeMensagem("mensagemDeErro", "O CPF se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	// Esses dois for removem pontos e tra�os.
    var r = "";
    for(i = 0; i < pCpf.length; i++){
      if(pCpf.charAt(i) != '.')
        r += pCpf.charAt(i);
	}
	var rv = "";
	
	for(i = 0; i < r.length; i++){
      if(r.charAt(i) != '-')
        rv += r.charAt(i);
	}
	//alert(rv);

	var numero = formatCpfCnpj(rv, false, false);
	
	var base = numero.substring(0, numero.length - 2);
	var digitos = dvCpfCnpj(base, false);
	var algUnico, i;

	// Valida d�gitos verificadores
	if (numero != base + digitos) {
		SetarTextoDeMensagem("mensagemDeErro", "CPF Inv�lido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}

	/* N�o ser�o considerados v�lidos os seguintes CPF:
	 * 000.000.000-00, 111.111.111-11, 222.222.222-22, 333.333.333-33, 444.444.444-44,
	 * 555.555.555-55, 666.666.666-66, 777.777.777-77, 888.888.888-88, 999.999.999-99.
	 */
	algUnico = true;
	for (i=1; algUnico && i<NUM_DIGITOS_CPF; i++)
	{
		algUnico = (numero.charAt(i-1) == numero.charAt(i));
	}
	
	if(algUnico) {
		SetarTextoDeMensagem("mensagemDeErro", "CPF Inv�lido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
	
} //ValidarCpf


/**
 * Testa se a String pCnpj fornecida � um CNPJ v�lido.
 * Qualquer formata��o que n�o seja algarismos � desconsiderada.
 * @param String pCnpj
 * 	String fornecida para ser testada.
 * @return <code>true</code> se a String fornecida for um CNPJ v�lido.
 */
function ValidarCnpj(obj)
{
	var pCnpj = obj.value;
	
	if(pCnpj.length == 0) {
		SetarTextoDeMensagem("mensagemDeErro", "O CNPJ se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	// Esses tr�s for removem pontos, tra�os e barras, etc.
    var r = "";
    for(i = 0; i < pCnpj.length; i++){
      if(pCnpj.charAt(i) != '.')
        r += pCnpj.charAt(i);
	}
	var rv = "";
	
	for(i = 0; i < r.length; i++){
      if(r.charAt(i) != '-')
        rv += r.charAt(i);
	}
	var rvc = "";
	
	for(i = 0; i < rv.length; i++){
      if(rv.charAt(i) != '/')
        rvc += rv.charAt(i);
	}
	//alert(rv);

	var numero = formatCpfCnpj(rvc, false, true);

	var base = numero.substring(0, NUM_DGT_CNPJ_BASE);
	var ordem = numero.substring(NUM_DGT_CNPJ_BASE, 12);
	var digitos = dvCpfCnpj(base + ordem, true);
	var algUnico;

	// Valida d�gitos verificadores
	if (numero != base + ordem + digitos) {
		SetarTextoDeMensagem("mensagemDeErro", "CNPJ Inv�lido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}

	/* N�o ser�o considerados v�lidos os CNPJ com os seguintes n�meros B�SICOS:
	 * 11.111.111, 22.222.222, 33.333.333, 44.444.444, 55.555.555,
	 * 66.666.666, 77.777.777, 88.888.888, 99.999.999.
	 */
	algUnico = numero.charAt(0) != '0';
	for (i=1; algUnico && i<NUM_DGT_CNPJ_BASE; i++)
	{
		algUnico = (numero.charAt(i-1) == numero.charAt(i));
	}
	if (algUnico) {
		SetarTextoDeMensagem("mensagemDeErro", "CNPJ Inv�lido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');	
		return false;
	}

	/* N�o ser� considerado v�lido CNPJ com n�mero de ORDEM igual a 0000.
	 * N�o ser� considerado v�lido CNPJ com n�mero de ORDEM maior do que 0300
	 * e com as tr�s primeiras posi��es do n�mero B�SICO com 000 (zeros).
	 * Esta cr�tica n�o ser� feita quando o no B�SICO do CNPJ for igual a 00.000.000.
	 */
	if (ordem == "0000") {
		SetarTextoDeMensagem("mensagemDeErro", "CNPJ Inv�lido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	
	return (base == "00000000"
		|| parseInt(ordem, 10) <= 300 || base.substring(0, 3) != "000");
} //ValidarCnpj


/**
 * Testa se a String pCpfCnpj fornecida � um CPF ou CNPJ v�lido.
 * Se a String tiver uma quantidade de d�gitos igual ou inferior
 * a 11, valida como CPF. Se for maior que 11, valida como CNPJ.
 * Qualquer formata��o que n�o seja algarismos � desconsiderada.
 * @param String pCpfCnpj
 * 	String fornecida para ser testada.
 * @return <code>true</code> se a String fornecida for um CPF ou CNPJ v�lido.
 */
function ValidarCpfCnpj(pCpfCnpj)
{
	var numero = pCpfCnpj.replace(/\D/g, "");
	if (numero.length > NUM_DIGITOS_CPF)
		return ValidarCnpj(pCpfCnpj)
	else
		return ValidarCpf(pCpfCnpj);
} //ValidarCpfCnpj
//------------------------------------------------------------------------------
function ValidarVacinarRetroativo(dataIdeal, dataDigitada)
{
    // Data no formato brasileiro, que ser� exibida na mensagem de confirma��o:
    var dataParaMensagem = dataIdeal;
    
    // Recebe dd/mm/aaaa e inverte para aaaa/mm/dd:
    dataDigitada = InverterData(dataDigitada);
    dataIdeal    = InverterData(dataIdeal);

    // Substitui qualquer caractere que n�o seja um d�gito pela barra (aceita
    // qualquer caractere como separador de ano-mes-dia:
    dataIdeal    = dataIdeal.replace(/[^0-9]{1}/g, '/');
    dataDigitada = dataDigitada.replace(/[^0-9]{1}/g, '/');

    // Cria um array ano/mes/dia:
    var arrDataIdeal    = dataIdeal.split('/');
    var arrDataDigitada = dataDigitada.split('/');

    // Macete: monta um n�mero inteiro com as strings do ano + mes + dia, ex.:
    // 20001230 para 2000/12/30 (ano/mes/dia)
    var intDataIdeal = parseInt( arrDataIdeal[0].toString()
                     + arrDataIdeal[1].toString()
                     + arrDataIdeal[2].toString(), 10 );

    var intDataDigitada = parseInt( arrDataDigitada[0].toString()
                        + arrDataDigitada[1].toString()
                        + arrDataDigitada[2].toString(), 10 );

    if(intDataIdeal > intDataDigitada)
    {
        return confirm("A data digitada � menor que a ideal ("
                     + dataParaMensagem
                     + ")\nVacinar mesmo assim?");
    }
    
    return true;
}
//------------------------------------------------------------------------------
function ValidarLote(obj, opcional)
{
	
	if(opcional == true && obj.value.length == 0) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	
	
	if(obj.value.length > 3) 
	{
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	

	SetarTextoDeMensagem("mensagemDeErro", "N�mero do lote Inv�lido.");
	MudarPropriedade(obj, 'backgroundColor', 'red');
	
	return false;
}
//------------------------------------------------------------------------------
function ValidarQuantidade(obj)
{
	if(obj.value > 0) 
	{
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');
		return true;
	}
	

	SetarTextoDeMensagem("mensagemDeErro", "Quantidade Inv�lida.");
	MudarPropriedade(obj, 'backgroundColor', 'red');
	
	return false;

}
//------------------------------------------------------------------------------
function InverterData(data)
{
	dia = data.substr(0, 2);
	mes = data.substr(3, 2);
	ano = data.substr(6, 4);
	
	invertida = ano + '/' + mes + '/' + dia;
	
	return invertida;
}
//------------------------------------------------------------------------------
function ValidarFaixaDeDatas(obji, objf, opcional)
{
	if ( ( typeof(obji) == "undefined" || obji == null ) ||
	     ( typeof(objf) == "undefined" || objf == null ) && opcional
	   ) {
		
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obji, 'backgroundColor', 'white');
		MudarPropriedade(objf, 'backgroundColor', 'white');
		return true;
	}
	
	if( ( obji.value == null || obji.value == '' ) ||
	    (objf == null || objf.value == '' ) && opcional
	  ) {
		
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obji, 'backgroundColor', 'white');
		MudarPropriedade(objf, 'backgroundColor', 'white');
		return true;
		
	}
    
    if( ( obji.value == null || obji.value == '' ) ||
	    (objf == null || objf.value == '' ) && !opcional
	  ) {
		
		SetarTextoDeMensagem("mensagemDeErro", "A data inicial e a data final devem estar preenchidas");
		MudarPropriedade(obji, 'backgroundColor', '#f4e5e0');
		MudarPropriedade(objf, 'backgroundColor', '#f4e5e0');
		return true;
		
	}
	
	if ( CompararDatas(obji, objf, '<', '=') ) {
		
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obji, 'backgroundColor', 'white');
		MudarPropriedade(objf, 'backgroundColor', 'white');
		return true;
	
	} 		
	
	SetarTextoDeMensagem("mensagemDeErro", "A data inicial deve ser menor ou igual a data final e ambas devem ser v�lidas.");
	MudarPropriedade(obji, 'backgroundColor', 'red');
	MudarPropriedade(objf, 'backgroundColor', 'red');
	
	return false;

	
}
//------------------------------------------------------------------------------
function CompararDatas(obji, objf, comparador1, comparador2)
{
	if ( !( ValidarData(obji, false) && ValidarData(objf, false) ) )
		return false;
	
	if( comparador1 == comparador2 ) return false;
	
	if( (comparador1 == '<' && comparador2 == '>') ||
	    (comparador1 == '>' && comparador2 == '<')
	  ) return false;

	datai = obji.value;
	dataf = objf.value;
	
	if( comparador2 == '' || comparador2 == null ) {
	
		switch(comparador1) {
			
			case '<':
				return (DataMenorQue(datai, dataf));
				break;
			
			case '=':
				return (DataIgual(datai, dataf));
				break;
			
			case '>':
				return (DataMaiorQue(datai, dataf));
				break;
			
			default:
				return false;
		
		}	
		
	} else {
		
		switch(comparador1) {
			
			case '<':
				if( comparador2 == '=' ) {
					
					return (DataMenorQue(datai, dataf) || DataIgual(datai, dataf));
					
				} else return false;
				
				break;
			
			case '=':
				if( comparador2 == '<' ) {
				
					return (DataMenorQue(datai, dataf) || DataIgual(datai, dataf));
					
				} else if( comparador2 == '>' ) {
					
					return (DataMaiorQue(datai, dataf) || DataIgual(datai, dataf));
					
					} else return false;
					
				break;
			
			case '>':
				if( comparador2 == '=' ) {
					
					return (DataMaiorQue(datai, dataf) || DataIgual(datai, dataf));
					
				} else return false;
				
				break;
			
			default:
				return false;
		
		}
		
	}
	
	return false;

}
//------------------------------------------------------------------------------
function DataMenorQue(datai, dataf)
{

	diai = parseInt(datai.substr(0, 2));
	mesi = parseInt(datai.substr(3, 2));
	anoi = parseInt(datai.substr(6, 4));
	
	diaf = parseInt(dataf.substr(0, 2));
	mesf = parseInt(dataf.substr(3, 2));
	anof = parseInt(dataf.substr(6, 4));
	
	diadif = diai - diaf;
	mesdif = mesi - mesf;
	anodif = anoi - anof;
	
	if( anodif < 0 ) return true;
	else {
		
		if( anodif == 0 ) {
			
			if( mesdif < 0 ) return true;
			else {
				
				if( mesdif == 0 ) {
					
					if( diadif < 0 ) return true;
					
				}
				
			}
			
		}
		
	}
	return false;

}
//------------------------------------------------------------------------------
function DataIgual(datai, dataf)
{
	
	if(datai == dataf) return true;
	
	return false;
	
}
//------------------------------------------------------------------------------
function DataMaiorQue(datai, dataf)
{
	
	diai = parseInt(datai.substr(0, 2));
	mesi = parseInt(datai.substr(3, 2));
	anoi = parseInt(datai.substr(6, 4));
	
	diaf = parseInt(dataf.substr(0, 2));
	mesf = parseInt(dataf.substr(3, 2));
	anof = parseInt(dataf.substr(6, 4));
	
	diadif = diaf - diai;
	mesdif = mesf - mesi;
	anodif = anof - anoi;
	
	if( anodif < 0 ) return true;
	else {
		
		if( anodif == 0 ) {
			
			if( mesdif < 0 ) return true;
			else {
				
				if( mesdif == 0 ) {
					
					if( diadif < 0 ) return true;
					
				}
				
			}
			
		}
		
	}
	return false;
	
}
//------------------------------------------------------------------------------
function VerificarSeDataMaiorQueHoje(obj, dataInput, dataHoje)
{

	diai = parseInt(dataInput.substr(0, 2));
	mesi = parseInt(dataInput.substr(3, 2));
	anoi = parseInt(dataInput.substr(6, 4));

	diaf = parseInt(dataHoje.substr(0, 2));
	mesf = parseInt(dataHoje.substr(3, 2));
	anof = parseInt(dataHoje.substr(6, 4));
 
	diadif = diaf - diai;
	mesdif = mesf - mesi;
	anodif = anof - anoi;

    
    var dataMaior = false;
	if( anodif < 0 ) dataMaior = true;
	else {

		if( anodif == 0 ) {

			if( mesdif < 0 ) dataMaior = true;
			else {

				if( mesdif == 0 ) {

					if( diadif < 0 ) dataMaior = true;

				}

			}

		}

	}
    if(dataMaior){
        SetarTextoDeMensagem("mensagemDeErro", "A data deve ser menor ou igual a de hoje.");
        MudarPropriedade(obj, 'backgroundColor', 'red');
        return false;
    }

	return true;

}
//------------------------------------------------------------------------------
function ValidarFaixaEtaria( valori, unidadei, valorf, unidadef, opcional )
{

    if( ( valori.value == '' || valori.value == null ) &&
        ( valorf.value == '' || valorf.value == null ) &&
        opcional == true
    ) {
     
        SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(valori, 'backgroundColor', 'white');
		MudarPropriedade(valorf, 'backgroundColor', 'white');
        return true;
        
    }
    
    if( ( valori.value == '' || valori.value == null ) &&
        ( valorf.value == '' || valorf.value == null ) &&
        opcional == false
    ) {
        
        SetarTextoDeMensagem("mensagemDeErro", "A faixa et�ria deve estar preenchida.");
		MudarPropriedade(valori, 'backgroundColor', '#f4e5e0');
		MudarPropriedade(valorf, 'backgroundColor', '#f4e5e0');
        return false;
        
    }
    
    if( isNaN(valori.value) || isNaN(valorf.value) ) {
        
        SetarTextoDeMensagem("mensagemDeErro", "Digite apenas n�meros na faixa de idades.");
		MudarPropriedade(valori, 'backgroundColor', 'red');
		MudarPropriedade(valorf, 'backgroundColor', 'red');
        return false;
        
    }
    
    contagemi = 1;
    contagemf = 1;
    
    if( unidadei.value == 'day' ) contagemi = contagemi * valori.value;
    if( unidadei.value == 'week' ) contagemi = contagemi * valori.value * 7;
    if( unidadei.value == 'month' ) contagemi = contagemi * valori.value * 30;
    if( unidadei.value == 'year' ) contagemi = contagemi * valori.value * 365;
    
    if( unidadef.value == 'day' ) contagemf = contagemf * valorf.value;
    if( unidadef.value == 'week' ) contagemf = contagemf * valorf.value * 7;
    if( unidadef.value == 'month' ) contagemf = contagemf * valorf.value * 30;
    if( unidadef.value == 'year' ) contagemf = contagemf * valorf.value * 365;
    
    if( contagemi <= contagemf ) {
        
        SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(valori, 'backgroundColor', 'white');
		MudarPropriedade(valorf, 'backgroundColor', 'white');
        return true; 
        
    }
    
    SetarTextoDeMensagem("mensagemDeErro", "A faixa et�ria inicial deve ser menor ou igual a faixa et�ria final e ambas devem ser v�lidas.");
	MudarPropriedade(valori, 'backgroundColor', 'red');
	MudarPropriedade(valorf, 'backgroundColor', 'red');
	
	return false;
    
}
/*
Como calcular anos bissextos

 
Para saber se um ano ser� bissexto na regra gregoriana que usamos at� hoje faz-se a seguinte conta:

Tente dividir o ano por 4. Se o resto for diferente de 0, ou seja, se for indivis�vel por 4, ele n�o � bissexto. Se for divis�vel por 4, � preciso verificar se o ano acaba em 00 (zero duplo). Em caso negativo, o ano � bissexto. Se terminar em 00, � preciso verificar se � divis�vel por 400. Se sim, � bissexto; se n�o, � um ano normal. 

Achou confuso? Vejamos na pr�tica como funciona a regra. Tomemos 2008 como exemplo. 2008 � um n�mero divis�vel por 4 (o resultado � 502) e que n�o acaba em 00. Logo, esse ano � bissexto. J� o ano 1900 n�o foi bissexto: � divis�vel por 4, termina em 00, mas n�o � divis�vel por 400. O ano 2000, por sua vez, foi bissexto: � divis�vel por 4, termina em 00 e � divis�vel por 400.

A regra de Greg�rio 13, apesar de ser a mais exata das que existiram, tamb�m n�o resolve totalmente o problema. A cada 3.300 anos seguindo essa regra, o calend�rio gregoriano ter� uma defasagem de 1 dia. Assim, no ano 4.882, o nosso calend�rio vai estar um dia adiantado com rela��o ao in�cio da primavera. Ainda n�o h� uma solu��o planejada para tal ano, j� que os astr�nomos de hoje em dia resolveram deixar a preocupa��o para os seus colegas do futuro.

Porque o dia a mais � em fevereiro?

Comecemos explicando por que fevereiro � mais curto que os outros meses.

O primeiro calend�rio romano foi supostamente bolado por R�mulo (mitologicamente, um dos fundadores de Roma), por volta de 753 a.C. Ele s� tinha 10 meses, de 30 ou 31 dias, e o ano durava 304 dias ao todo, segundo o professor de astronomia Roberto Boczko, da USP.

Essa folhinha n�o estava em sincronia com as esta��es do ano e, quando o sucessor Numa tomou o poder, instituiu um novo calend�rio, baseado nas fases da lua com 12 meses de 29 ou 31 dias. Nenhum deles tinha 30 dias porque, nessa �poca, acreditava-se que os n�meros pares desagradavam aos deuses e, por isso, eram sinal de azar. A soma dos dias do ano, por�m, era par (356 dias). Para que o ano inteiro n�o fosse considerado azarado por ser par, decidiu-se que um m�s teria de ser �sacrificado� e ter um n�mero par de dias para que o ano somasse 355 dias.

O escolhido foi fevereiro, considerado na �poca um m�s ruim (�o nome 'fevereiro' foi dado justamente por aquele ser o m�s das febres, das cobran�as e das execu��es judici�rias�, conta Boczko). Arredondar para 28 dias, em vez de 30, foi uma escolha estrat�gica: o m�s azarado deveria acabar o mais r�pido poss�vel. Quando surgiu a necessidade de acrescentar um dia ao ano, nada melhor que coloc�-lo no m�s mais curto.



function AnoBisexto(ano)
{
	if(parseInt(ano, 10)) % 4 != 0) {
		return false;
	}
	else {
		if(ano.substr(2, 2) == '00') {
			return false;
		}
		else {
			return true;
		}
	}
}


---------------------

Todos os anos que sejam m�ltiplos de 4 mas que n�o sejam m�ltiplos de 100, com exce��o daqueles que s�o m�ltiplos de 400, s�o bissextos

function AnoBisexto(ano)
{
	if(((parseInt(ano, 10) % 4 == 0) && (parseInt(ano, 10) % 100 != 0)) 
		|| parseInt(ano, 10) % 400 == 0) {
		
		return true;
	}
	return false;
}

*/
