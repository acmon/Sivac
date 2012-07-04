


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
		
	SetarTextoDeMensagem("mensagemDeErro", "O dia é inválido.");
	MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
	return false;
}
//------------------------------------------------------------------------------
function ValidarVacinaFilha(vacina, vacinaFilha)
{
	if(vacina.valeu != 'undefined' && vacina.valeu != 0 && vacina.valeu != false)
    {
        if(vacinaFilha.value == 0) {
            SetarTextoDeMensagem("mensagemDeErro", "O tipo de aplicação é inválido.");
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
		SetarTextoDeMensagem("mensagemDeErro", "O mês se encontra vazio.");
		MudarPropriedade(objMes, 'backgroundColor', '#f4e5e0');
		return false;
	}
	
	if(parseInt(objMes.value, 10) > 0 && parseInt(objMes.value) < 13)	{
	
		if( ( (parseInt(objMes.value, 10) == 2) && (parseInt(objDia.value) > 29) )
			|| ( (parseInt(objMes.value, 10) == 4) && (parseInt(objDia.value) > 30) )
			|| ( (parseInt(objMes.value, 10) == 6) && (parseInt(objDia.value) > 30) )
			|| ( (parseInt(objMes.value, 10) == 9) && (parseInt(objDia.value) > 30) )
			|| ( (parseInt(objMes.value, 10) == 11) && (parseInt(objDia.value) > 30) ) ) {
			
			SetarTextoDeMensagem("mensagemDeErro", "O mês não é compatível com o dia digitado.");
			MudarPropriedade(objMes, 'backgroundColor', '#f4e5e0');
			return false;
		}
	
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(objMes, 'backgroundColor', 'white');
		return true;
	}
		
	SetarTextoDeMensagem("mensagemDeErro", "O mês é inválido.");
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
// Verifica se a String é um email válido:
function EmailValido(endereco)
{
	var reTipo = /^[\w-]+(\.[\w-]+)*@(([A-Za-z\d][A-Za-z\d-]{0,61}[A-Za-z\d]\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/;
	return reTipo.test(endereco);
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverArrobaArroba(st)
{
	var s = st.replace('@@','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverPontoPonto(st)
{
	var s = st.replace('..','.');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverTracoTraco(st)
{
	var s = st.replace('--','-');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverSublinhadoSublinhado(st)
{
	var s = st.replace('__','_');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverArrobaSublinhado(st)
{
	var s = st.replace('@_','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverSublinhadoArroba(st)
{
	var s = st.replace('_@','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverArrobaTraco(st)
{
	var s = st.replace('@-','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverTracoArroba(st)
{
	var s = st.replace('-@','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverPontoArroba(st)
{
	var s = st.replace('.@','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverArrobaPonto(st)
{
	var s = st.replace('@.','@');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverTracoPonto(st)
{
	var s = st.replace('-.','-');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverPontoTraco(st)
{
	var s = st.replace('.-','.');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverPontoSublinhado(st)
{
	var s = st.replace('._','.');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverSublinhadoPonto(st)
{
	var s = st.replace('_.','_');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverSublinhadoTraco(st)
{
	var s = st.replace('_-','_');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverTracoSublinhado(st)
{
	var s = st.replace('-_','-');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverPontoPrecedidoDeEspaco(st)
{
	var s = st.replace(' .','.');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverBarraInvertida(st)
{
	var s = st.replace(/\\/, '');
	return s;
}
//------------------------------------------------------------------------------
// Remove conjunto inválido de caracteres:
function RemoverDoisEspacos(st)
{
	// Remove dois espaços:
	var s = st.replace('  ',' ');
	
	// Remove espaço em branco no início da string:
	if(s.length > 2 && s.charAt(0) == ' ') s = s.substr(1);
	
	return s;
}
//------------------------------------------------------------------------------
// Remove qualquer ocorrência de barras invertidas:
function RemoverBarrasInvertidas(obj)
{
	obj.value = obj.value.replace(/\\/g, '');
}
//------------------------------------------------------------------------------
// Trim que remove espaços em branco do início e fim da string 
function Trim(obj)
{
	obj.value =  obj.value.replace(/^\s+|\s+$/, '');
	obj.value =  obj.value.replace(/^\s+|\s+$/, '');
}
//------------------------------------------------------------------------------
// Remove pontos, traços, etc do início e fim da string
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
			
			if (  isNaN(inteiro) ) { // Só trata o que for diferente de número:
			
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
// Remove conjunto inválido de caracteres:
function RemoverDoisEnters(st)
{
	var output = "";
	// Remove dois enters seguidos:
	
	// Remove enters no início:
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
	
	// Remove zeros do início da string:
	
	if(s.charAt(0) == '0') s = obj.value.substr(1);
	if(s.substr(0, 2) == '00') s = obj.value.substr(2);
	if(s.substr(0, 3) == '000') s = obj.value.substr(3);
	if(s.substr(0, 4) == '0000') s = obj.value.substr(4);
	if(s.substr(0, 5) == '00000') s = obj.value.substr(5);
		
	obj.value = s;
}
//------------------------------------------------------------------------------

// Validação de descrições, observações, etc.
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
                "O texto está pequeno demais. Digite ao menos "
               + tamMinimo + " caracteres.");
           
            MudarPropriedade(obj, 'backgroundColor', 'red');
            return false;
    }

    var digitouEspaco = false;

    for(var i=0; i<obj.value.length; i++) {

            if(obj.value.charAt(i) == ' ') digitouEspaco = true;
    }

    if( !digitouEspaco && permitirSemEspaco != true) {
            SetarTextoDeMensagem("mensagemDeErro", "O texto é inválido.");
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
		SetarTextoDeMensagem("mensagemDeErro", "A palavra está pequena demais.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;	
}
//------------------------------------------------------------------------------
// Colocar mais validações adequadas (Pesquisar)
function ValidarMatricula(obj)
{
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "A matrícula se encontra vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}
	if(obj.value.length < 3) {
		SetarTextoDeMensagem("mensagemDeErro", "A matrícula está pequena demais.");
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
		SetarTextoDeMensagem("mensagemDeErro", "A senha está pequena demais.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	for(var i=0; i<obj.value.length; i++) {
		if(obj.value.charAt(i) == ' ') {
			SetarTextoDeMensagem("mensagemDeErro", "A senha não pode conter espaço em branco.");
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
		SetarTextoDeMensagem("mensagemDeErro", "A contra-senha está pequena demais.");
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
// Verifica se é um email válido:
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

	SetarTextoDeMensagem("mensagemDeErro", "email inválido.");
	MudarPropriedade(obj, 'backgroundColor', 'red');
	return false;
}
//------------------------------------------------------------------------------
// Verifica se a busca é válida:
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
		
		if(obj.value.charAt(i) == '¨' || obj.value.charAt(i) == '_' 
			|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
			|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
			|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
			|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
			|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
			|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
			|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
			|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
			|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '§'
			|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
			|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
			|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
			|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '´'
			|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == ',') {
		
			SetarTextoDeMensagem("mensagemDeErro", "Pesquisa inválida. Remova os símbolos.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}
	
	if(obj.value.length < tamanhoMinimo) {
		SetarTextoDeMensagem("mensagemDeErro", "A busca está pequena. Digite ao menos " + tamanhoMinimo + " letras.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;		
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
// Verifica se a busca de nome parecido é válida:
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

		if(obj.value.charAt(i) == '¨' || obj.value.charAt(i) == '_'
			|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
			|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
			|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
			|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
			|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
			|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
			|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
			|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
			|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '§'
			|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
			|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
			|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
			|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '´'
			|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == ',') {

			SetarTextoDeMensagem("mensagemDeErro", "Pesquisa inválida. Remova os símbolos.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}

	if(obj.value.length < tamanhoMinimo) {
		SetarTextoDeMensagem("mensagemDeErro", "A busca está pequena. Digite ao menos " + tamanhoMinimo 
                                                + " letras para fazer uma busca significativa.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}

	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
// Verifica se é um login válido:
function ValidarLogin(obj)
{
	
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O login se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}
	
	if (obj.value.length < 4) {
	   SetarTextoDeMensagem("mensagemDeErro", "Seu login está pequeno demais.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}
	
	if (obj.value.length > 25) {
	   SetarTextoDeMensagem("mensagemDeErro", "Seu login está grande demais.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}
	
	for(var i=0; i<obj.value.length; i++) {
		if(obj.value.charAt(i) == '¨'
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
			|| obj.value.charAt(i) == 'á' || obj.value.charAt(i) == 'ã'
			|| obj.value.charAt(i) == 'à' || obj.value.charAt(i) == 'â'
			|| obj.value.charAt(i) == 'ä' || obj.value.charAt(i) == 'é'
			|| obj.value.charAt(i) == 'è' || obj.value.charAt(i) == 'ê'
			|| obj.value.charAt(i) == 'ë' || obj.value.charAt(i) == 'í'
			|| obj.value.charAt(i) == 'ì' || obj.value.charAt(i) == 'î'
			|| obj.value.charAt(i) == 'ï' || obj.value.charAt(i) == 'ó'
			|| obj.value.charAt(i) == 'ò' || obj.value.charAt(i) == 'ô'
			|| obj.value.charAt(i) == 'ö' || obj.value.charAt(i) == 'ú'
			|| obj.value.charAt(i) == 'ù' || obj.value.charAt(i) == 'û'
			|| obj.value.charAt(i) == 'ü' || obj.value.charAt(i) == 'ª'
			|| obj.value.charAt(i) == 'º' || obj.value.charAt(i) == 'Á'
			|| obj.value.charAt(i) == 'À' || obj.value.charAt(i) == 'Â'
			|| obj.value.charAt(i) == 'Ä' || obj.value.charAt(i) == 'É'
			|| obj.value.charAt(i) == 'È' || obj.value.charAt(i) == 'Ê'
			|| obj.value.charAt(i) == 'Ë' || obj.value.charAt(i) == 'Í'
			|| obj.value.charAt(i) == 'Ì' || obj.value.charAt(i) == 'Î'
			|| obj.value.charAt(i) == 'Ï' || obj.value.charAt(i) == 'Ó'
			|| obj.value.charAt(i) == 'Ò' || obj.value.charAt(i) == 'Ô'
			|| obj.value.charAt(i) == 'Ö' || obj.value.charAt(i) == 'Ú'
			|| obj.value.charAt(i) == 'Ù' || obj.value.charAt(i) == 'Û'
			|| obj.value.charAt(i) == 'Ü' || obj.value.charAt(i) == 'Ã'			
			|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
			|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '´'
			|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == '~') {
			
				SetarTextoDeMensagem("mensagemDeErro", "Login inválido.");
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}
			
	}
	if (obj.value.charAt(0) == '.' || obj.value.charAt(0) == '-' ||
		obj.value.charAt(0) == '_') {
		SetarTextoDeMensagem("mensagemDeErro", "Login inválido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	for(var i=0; i < 10; i++) {
		if ((obj.value.charAt(0) == i.toString() )) {
			SetarTextoDeMensagem("mensagemDeErro", "Login inválido.");

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

	SetarTextoDeMensagem("mensagemDeErro", "O DDD é inválido.");
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

	SetarTextoDeMensagem("mensagemDeErro", "Telefone inválido. Preencha corretamente.");
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
		
		SetarTextoDeMensagem("mensagemDeErro", "Telefone inválido. Preencha corretamente.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		
		return false;	
	}

	var primeirosDigitos = obj.value.substr(5, 4);
	var traco = obj.value.charAt(9);
	var ultimosDigitos = obj.value.substr(10);
			
	if ( !(primeirosDigitos.length == 4 && ultimosDigitos.length == 4
		&& !isNaN(primeirosDigitos) && !isNaN(ultimosDigitos)
		&& traco == '-') ) {
		
		SetarTextoDeMensagem("mensagemDeErro", "Telefone inválido. Preencha corretamente.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;			
	}

	return true;

}
//------------------------------------------------------------------------------
function ValidarCartaoSus(obj, opcional)
{
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == false) {
		SetarTextoDeMensagem("mensagemDeErro", "O número do cartão se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');		
		return true;
	}

	if(obj.value.length < 15) {
		SetarTextoDeMensagem("mensagemDeErro", "Prencha os 15 digitos do cartão SUS ou deixe em branco.");
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
		SetarTextoDeMensagem("mensagemDeErro", "O número do prontuário se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	if(((obj.value.length == 0) || (obj.value == 'undefined')) && opcional == true) {
		SetarTextoDeMensagem("mensagemDeErro", "");
		MudarPropriedade(obj, 'backgroundColor', 'white');		
		return true;
	}

	if(obj.value.length < 2) {
		SetarTextoDeMensagem("mensagemDeErro", "Prencha o número do prontuário corretamente ou deixe em branco.");
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
					+ nomeDoCampo + " está muito pequeno. Digite corretamente ou deixe em branco.");
		
		else SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
				+ nomeDoCampo + " está muito pequeno.");
		
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	if(obj.name == 'logradouro') {
		
		for(var i=0; i<obj.value.length; i++) {
			
			if(obj.value.charAt(i) == ' '
				&&	i < (obj.value.length - 2) ) sobrenomePreenchido = true;
			
			if(obj.value.charAt(i) == '¨' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '§'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '´'
				|| obj.value.charAt(i) == '`') {
			
				SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
						+ nomeDoCampo + " ficou inválido.");
				
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}
		}
	}
		
	if(obj.name == 'nome' || obj.name == 'nomedamae' || obj.name == 'nomedopai') {
		
		// Não aceitar nomes com menos que 5 caracteres e pelo menos um espaço.
		// Aceitar pontos para abreviar quando se precede uma única letra.
		if (obj.value.length < 5) {
			if(opcional == true)
				SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
						+ nomeDoCampo + " está muito pequeno. Digite corretamente ou deixe em branco.");
			
			else SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
					+ nomeDoCampo + " está muito pequeno.");
			
			MudarPropriedade(obj, 'backgroundColor', 'red');
		   	return false;
		}
		
		var sobrenomePreenchido = false;
		
		for(var i=0; i<obj.value.length; i++) {
			
			if(obj.value.charAt(i) == ' '
				&&	i < (obj.value.length - 2) ) sobrenomePreenchido = true;
			
			if(obj.value.charAt(i) == '¨' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '§'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '´'
				|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == ',') {
			
				SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de "
						+ nomeDoCampo + " ficou inválido.");
				
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}

			// Aceitar pontos para abreviar quando se precede uma única letra.
			if( obj.value.indexOf('Dr.') == -1
				&&  obj.value.indexOf('Dra.') == -1
				&&  obj.value.indexOf('Drª.') == -1
				&&  obj.value.charAt(i) == '.'){
					
				if(obj.value.charAt(i+1) != ' ' || obj.value.charAt(i-2) != ' '){
					
					SetarTextoDeMensagem("mensagemDeErro", "Use a abreviação corretamente para "
							+ nomeDoCampo + ".");
					
					MudarPropriedade(obj, 'backgroundColor', 'red');
					return false;					
				}
			}
		}
		if( sobrenomePreenchido == false ) {
			
			SetarTextoDeMensagem("mensagemDeErro", "Preencha também o segundo nome.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;	
		}
	}
		
	if(obj.name == 'logradouro' || obj.name == 'endereco') {
		
		// Aceitar pontos e traços, barras, vírgulas, parênteses
		for(var i=0; i<obj.value.length; i++) {
			if(    obj.value.charAt(i) == '¨' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '§'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '´'
				|| obj.value.charAt(i) == '`') {
				
				SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo
						+ " ficou inválido.");
				
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}
		}

		if (obj.value.length < 5) {
		   SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo +
				   " está pequeno demais");
		   
		   MudarPropriedade(obj, 'backgroundColor', 'red');
		   return false;
		}			
	}	
	if(obj.name == 'bairro' ) {
		
		// Aceitar pontos e traços, barras, vírgulas, parênteses
		for(var i=0; i<obj.value.length; i++) {
			if(    obj.value.charAt(i) == '¨' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '§'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '´'
				|| obj.value.charAt(i) == '`') {
				
				SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo
						+ " ficou inválido.");
				
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}
		}

		if (obj.value.length < 3) {
		   SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo +
				   " ficou inválido.");
		   
		   MudarPropriedade(obj, 'backgroundColor', 'red');
		   return false;
		}			
	}
	
	// Veifica ponto, traço e sublinhado no início:
	if (obj.value.charAt(0) == '.' || obj.value.charAt(0) == '-' ||
		obj.value.charAt(0) == '_') {
		
		SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo +
		   " ficou inválido.");
		
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	// Verifica números no início:
	for(var i=0; i < 10; i++) {
		if ((obj.value.charAt(0) == i.toString() )) {
			SetarTextoDeMensagem("mensagemDeErro", "O preenchimento de " + nomeDoCampo +
			   " ficou inválido.");

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
	
		// Não aceitar nomes com menos que 5 caracteres e pelo menos um espaço.
		// Aceitar pontos para abreviar quando se precede uma única letra.
		if (obj.value.length < 5) {
			if(opcional == true)
				SetarTextoDeMensagem("mensagemDeErro", "Nome da unidade muito pequeno. Preencha corretamente ou deixe em branco.");
			
			else SetarTextoDeMensagem("mensagemDeErro", "O nome da unidade está pequeno demais. Não abrevie.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
		   	return false;
		}
		
		for(var i=0; i<obj.value.length; i++) {
			
			if(obj.value.charAt(i) == '¨' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '§'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '´'
				|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == ',') {
			
				SetarTextoDeMensagem("mensagemDeErro", "Nome de unidade inválido.");
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}

		}
				
	// Veifica ponto, traço e sublinhado no início:
	if (obj.value.charAt(0) == '.' || obj.value.charAt(0) == '-' ||
		obj.value.charAt(0) == '_') {
		SetarTextoDeMensagem("mensagemDeErro", "Nome de unidade inválido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	// Verifica números no início:
	for(var i=0; i < 10; i++) {
		if ((obj.value.charAt(0) == i.toString() )) {
			SetarTextoDeMensagem("mensagemDeErro", "Nome de unidade inválido.");

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
        
		// Não aceitar nomes com menos que 5 caracteres e pelo menos um espaço.
		// Aceitar pontos para abreviar quando se precede uma única letra.
		if (obj.value.length < 5) {
			if(opcional == true)
				SetarTextoDeMensagem("mensagemDeErro",
					"Nome da campanha muito pequeno. Preencha corretamente ou deixe em branco.");
			
			else SetarTextoDeMensagem("mensagemDeErro", "O nome da campanha está pequeno demais. Não abrevie.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
		   	return false;
		}
		
		for(var i=0; i<obj.value.length; i++) {
			
			if(obj.value.charAt(i) == '¨' || obj.value.charAt(i) == '_' 
				|| obj.value.charAt(i) == '(' || obj.value.charAt(i) == ')'
				|| obj.value.charAt(i) == '"' || obj.value.charAt(i) == '!'
				|| obj.value.charAt(i) == '@' || obj.value.charAt(i) == '#'
				|| obj.value.charAt(i) == '$' || obj.value.charAt(i) == '%'
				|| obj.value.charAt(i) == '*' || obj.value.charAt(i) == '~'
				|| obj.value.charAt(i) == '+' || obj.value.charAt(i) == '='
				|| obj.value.charAt(i) == '|' || obj.value.charAt(i) == '/'
				|| obj.value.charAt(i) == '?' || obj.value.charAt(i) == ':'
				|| obj.value.charAt(i) == ';' || obj.value.charAt(i) == '§'
				|| obj.value.charAt(i) == '<' || obj.value.charAt(i) == '>'
				|| obj.value.charAt(i) == '[' || obj.value.charAt(i) == ']'
				|| obj.value.charAt(i) == '{' || obj.value.charAt(i) == '}'
				|| obj.value.charAt(i) == '^' || obj.value.charAt(i) == '´'
				|| obj.value.charAt(i) == '`' || obj.value.charAt(i) == ',') {
			
				SetarTextoDeMensagem("mensagemDeErro", "Nome de campanha inválido.");
				MudarPropriedade(obj, 'backgroundColor', 'red');
				return false;
			}

		}
				
	// Veifica ponto, traço e sublinhado no início:
	if (obj.value.charAt(0) == '.' || obj.value.charAt(0) == '-' ||
		obj.value.charAt(0) == '_') {
		SetarTextoDeMensagem("mensagemDeErro", "Nome de campanha inválido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	// Verifica números no início:
	for(var i=0; i < 10; i++) {
		if ((obj.value.charAt(0) == i.toString() )) {
			SetarTextoDeMensagem("mensagemDeErro", "Nome de campanha inválido.");

			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
// Validar Número do Computador:
function ValidarNumeroDeComputador(obj)
{
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "O número do computador ficou vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}

	if (obj.value.length < 3) {
	   SetarTextoDeMensagem("mensagemDeErro", "O número do computador está pequeno demais.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}

	if (parseInt(obj.value) < 1) {
	   SetarTextoDeMensagem("mensagemDeErro", "O número do computador é inválido.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
}
//------------------------------------------------------------------------------
// Validar Número do Computador:
function ValidarIdade(obj)
{
	if((obj.value.length == 0) || (obj.value == 'undefined')) {
		SetarTextoDeMensagem("mensagemDeErro", "A idade ficou vazia.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');
		return false;
	}

	if (parseInt(obj.value) < 1) {
	   SetarTextoDeMensagem("mensagemDeErro", "A idade está pequena demais.");
	   MudarPropriedade(obj, 'backgroundColor', 'red');
	   return false;
	}

	if (parseInt(obj.value) > 180) {
	   SetarTextoDeMensagem("mensagemDeErro", "A idade do computador está grande demais.");
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
		SetarTextoDeMensagem("mensagemDeErro", "A escolha de " + nomeDoCampo + " não foi feita.");
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
	
	SetarTextoDeMensagem("mensagemDeErro", "O telefone é inválido.");
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
	
	SetarTextoDeMensagem("mensagemDeErro", "O Cnes é inválido.");
	MudarPropriedade(obj, 'backgroundColor', 'red');		
	return false;		
}
//------------------------------------------------------------------------------
function ValidarInscricaoEstadual(obj)
{
	if (((obj.value.length == 0) || (obj.value == 'undefined')) && document.formulario.isento.checked == false) {
		SetarTextoDeMensagem("mensagemDeErro", "A Inscrição estadual se encontra vazia.");
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
		SetarTextoDeMensagem("mensagemDeErro", "A Inscrição Estadual é inválida.");
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

	// parseInt tem um bug com os números 08 e 09 ;), por isso tem-se que
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

			// Verificar se o ano é bisexto:
			if( obj.value.substr(0, 2) == '29' &&
				obj.value.substr(3, 2) == '02' ) {
				
				ano = obj.value.substr(6, 4);
				
				if(ano.length == 4) {

					if(!AnoBisexto(ano)) {
						
						SetarTextoDeMensagem("mensagemDeErro", "Este não é um ano bisexto.");
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

	SetarTextoDeMensagem("mensagemDeErro", "A data é inválida.");
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
			SetarTextoDeMensagem("mensagemDeErro", "O CEP é inválido.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
	}
		
	if(partes[0].length != 5 || partes[1].length != 3 ) {
		SetarTextoDeMensagem("mensagemDeErro", "O CEP é inválido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;		
	}
		
	for(var i=0; i<5; i++) {
		if( !(obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") ) {
			SetarTextoDeMensagem("mensagemDeErro", "O CEP é inválido.");
			MudarPropriedade(obj, 'backgroundColor', 'red');
			return false;
		}
	}
	if (obj.value.charAt(5) != '-') {
		SetarTextoDeMensagem("mensagemDeErro", "O CEP é inválido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;		
	}
	
	for(var i=6;  i < obj.value.length; i++) {
		if( !(obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") ) {
			SetarTextoDeMensagem("mensagemDeErro", "O CEP é inválido.");
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

	SetarTextoDeMensagem("mensagemDeErro", "A hora é inválida.");
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

            SetarTextoDeMensagem("mensagemDeErro", "A hora é maior que a atual.");
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
// em maiúsculas.
function Ucwords(str)
{
	var novoTexto = str.replace(/^(.)|\s(.)/g, function ( $1 ) {
		return $1.toUpperCase ( );
	} );
	
	return novoTexto;
}
//------------------------------------------------------------------------------
// Equivalente ao PHP strtolower para transformar strings em minúsculos:
function Strtolower(str)
{
	var novoTexto = str.toLowerCase();
	return novoTexto;	
}
//------------------------------------------------------------------------------
// Prepara o nome para ser inserido no banco somente com a inicial maiúscula,
// mantendo somente as conjunções como de, da, etc.
function FormatarNome(obj, teclaPress)
{
    if (window.event) {
        var tecla = teclaPress.keyCode;
    } else {
        tecla = teclaPress.which;
    }

    var s = new String(obj.value);
    // Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
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
// OBS. NÃO FUNCIONA NO OPERA INICIALMENTE.
//--->Função para a formatação dos campos...<---
function Mascara(tipo, campo, teclaPress) {
    if (window.event)
    {
        var tecla = teclaPress.keyCode;
    } else {
        tecla = teclaPress.which;
    }

    if(tecla == 13 || tecla == 8) return true;
    
    var s = new String(campo.value);
    // Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
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
		
		// Remove ponto precedido de espaço de todos os tipos
		campo.value = RemoverPontoPrecedidoDeEspaco(campo.value);
		
		// Remove barra invertida de todos os tipos
		campo.value = RemoverBarraInvertida(campo.value);
		
        switch (tipo) {
		case 'NOME' :
			campo.value = RemoverDoisEspacos(campo.value);
			campo.value = RemoverCaracteresRepetidos(campo.value);
			
			var naoValidoNoInicio = " 1234567890@-_./-()!@#%¨&*+=_;<>,?:|~^][{}`´";
			
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
			
			var naoValidoNoInicio = " 1234567890@-_./-()!@#%¨&*+=_;<>,?:|~^][{}`´";
			
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

				// Vare e remove o caracter não permitido:
				for( var i = 0; i < tam; i++) {
					if ( strValidos.indexOf(campo.value.charAt(i)) == -1 ) {
						var s = campo.value.substr(0, i) + campo.value.substr(i+1);
						campo.value = s;
					}
				}
				
				// Remove conjunto inválido de caracteres:
				
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
        	
        	// Na posição 0, não permitir números maiores que 3:
        	if(((tecla > 51 && tecla < 58) || tecla > 99 ) && tam < 2){
        		return false;
        	} 

        	// Na posição 1, se o número anterior for 3, então não permitir
        	// número maiores que 1:
        	if(((tecla > 49 && tecla < 58) || tecla > 97 ) && tam == 2
        			&& campo.value.charAt(0) == '3'){
        		return false;
        	}
        	
        	// Na posição 1, se o número anterior for 0, então não permitir
        	// outro zero (dia 00)
        	if((tecla == 48 || tecla == 96 ) && tam == 2
        			&& campo.value.charAt(0) == '0'){
        		return false;
        	}

        	// Na posição 4, se o número anterior for 0, então não permitir
        	// outro zero (mes 00)
        	if((tecla == 48 || tecla == 96 ) && tam == 4
        			&& campo.value.charAt(3) == '0'){
        		return false;
        	}
			
        	// Na posição 3, não permitir números maiores que 1 (mês até 12)
        	if(((tecla > 49 && tecla < 58) || tecla > 97 ) && tam == 3){
        		return false;
        	}
        	
        	// Na posição 4, não permitir números maiores que 2 (mês até 12) se
        	// o número anterior for 1
        	if(((tecla > 50 && tecla < 58) || tecla > 98 ) && tam == 4
        			&& campo.value.charAt(3) == '1'){
        		return false;
        	}
        	
        	// Não permitir 30 ou 31 de fevereiro:
        	if((tecla == 50 || tecla == 98 ) && tam == 4
        			&& campo.value.charAt(3) == '0'
        			&& campo.value.charAt(0) == '3' ) {
        		return false;
        	}
        	
        	// Não permitir anos que comecem com 3000:
        	if(((tecla > 50 && tecla < 58 ) || tecla > 98 ) && tam == 5 ) {
        		return false;
        	}
			
        	// Não permitir anos que comecem com 0:
        	if((tecla == 48 || tecla == 96 ) && tam == 5 ) {
        		return false;
        	}
			
        	// Não permitir anos cheguem a 2100:
        	if(((tecla > 48 && tecla < 58) || tecla > 96 ) && tam == 6
				&& campo.value.charAt(6) == '2') {
        		return false;
        	}
			
        	// Não permitir anos sejam menores que 1900:
        	if((tecla != 105 && tecla != 57 ) && tam == 6
				&& campo.value.charAt(6) == '1') {
        		return false;
        	}
						
			if(campo.value.substr(0, 2) == '31' && campo.value.length < 6) {
				//alert(campo.value.substr(0, 2));
				

				// Não permite os meses 02, 04, 06 e 09
				if(campo.value.charAt(3) == '0'
					// coloquei "&& campo.value.length < 5" pois não aceitava
					// anos que se iniciassem com 2000.
					&& campo.value.length < 5
					&& ( tecla == 50 || tecla == 52 ||
						 tecla == 54 || tecla == 57 ||
						 tecla == 98 || tecla == 100 ||
						 tecla == 102 || tecla == 105) ) {
						 
						 return false;

				} 
				// Não permite o mes 11
				if(campo.value.charAt(3) == '1'
					&& ( tecla == 49 || tecla == 97 )) {
						 
						 return false;
				}
			}
			
			// Verificar se o ano é bisexto:
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
		//na posição 0 somente número de 0 a 2 são permitidos
		if(((tecla > 51 && tecla < 58) || tecla > 98 ) && tam < 2){
        		return false;
        	}
		//na posição 1 somente numero, mas se a posiçao anterior for
		//2 somente sera permitido de 0 a 3
		if(((tecla > 51 && tecla < 58) || tecla > 99 ) && tam == 2
        			&& campo.value.charAt(0) == '2'){
        		return false;
        	}
		//na posicao 3 só será permitidos número de 0 a 5
		if(((tecla > 53 && tecla < 58) || tecla > 101 ) && tam == 4){
        		return false;
        	}
            if (tam > 2 && tam < 4)
                campo.value = s.substr(0,2) + ':' + s.substr(2, tam);
				
			// Na posição 0, não permitir números maiores que 2:
        	/*
			if(( !(tecla > 47 && tecla < 51) ) && tam < 2){
        		return false;
        	}
																						   */

        	// Na posição 1, se o número anterior for 3, então não permitir
        	// número maiores que 1:
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

				// Vare e remove o caracter não permitido:
				for( var i = 0; i < tam; i++) {
					if ( strValidos.indexOf(campo.value.charAt(i)) == -1 ) {
						var s = campo.value.substr(0, i) + campo.value.substr(i+1);
						campo.value = s;
					}
				}
				
				// Remove conjunto inválido de caracteres:
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
            
            key = String.fromCharCode(tecla); // Valor para o código da Chave
            if (strCheck.indexOf(key) == -1) return false; // Chave inválida
            
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
//--->Função para verificar se o valor digitado é número...<---
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
 * PROTÓTIPOS:
 * método String.lpad(int pSize, char pCharPad)
 * método String.trim()
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
 * Adiciona método lpad() à classe String.
 * Preenche a String à esquerda com o caractere fornecido,
 * até que ela atinja o tamanho especificado.
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
 * Adiciona método trim() à classe String.
 * Elimina brancos no início e fim da String.
 */
String.prototype.trim = function()
{
	return this.replace(/^\s*/, "").replace(/\s*$/, "");
} //String.trim


/**
 * Elimina caracteres de formatação e zeros à esquerda da string
 * de número fornecida.
 * @param String pNum
 * 	String de número fornecida para ser desformatada.
 * @return String de número desformatada.
 */
function unformatNumber(pNum)
{
	return String(pNum).replace(/\D/g, "").replace(/^0+/, "");
} //unformatNumber


/**
 * Formata a string fornecida como CNPJ ou CPF, adicionando zeros
 * à esquerda se necessário e caracteres separadores, conforme solicitado.
 * @param String pCpfCnpj
 * 	String fornecida para ser formatada.
 * @param boolean pUseSepar
 * 	Indica se devem ser usados caracteres separadores (. - /).
 * @param boolean pIsCnpj
 * 	Indica se a string fornecida é um CNPJ.
 * 	Caso contrário, é CPF. Default = false (CPF).
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
 * Calcula os 2 dígitos verificadores para o número-efetivo pEfetivo de
 * CNPJ (12 dígitos) ou CPF (9 dígitos) fornecido. pIsCnpj é booleano e
 * informa se o número-efetivo fornecido é CNPJ (default = false).
 * @param String pEfetivo
 * 	String do número-efetivo (SEM dígitos verificadores) de CNPJ ou CPF.
 * @param boolean pIsCnpj
 * 	Indica se a string fornecida é de um CNPJ.
 * 	Caso contrário, é CPF. Default = false (CPF).
 * @return String com os dois dígitos verificadores.
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
 * Testa se a String pCpf fornecida é um CPF válido.
 * Qualquer formatação que não seja algarismos é desconsiderada.
 * @param String pCpf
 * 	String fornecida para ser testada.
 * @return <code>true</code> se a String fornecida for um CPF válido.
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
		SetarTextoDeMensagem("mensagemDeErro", "CPF inválido. Preencha corretamente ou deixe em branco.");
		MudarPropriedade(obj, 'backgroundColor', 'red');		
		return false;
	}
	
	if(pCpf.length == 0) {
		SetarTextoDeMensagem("mensagemDeErro", "O CPF se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	// Esses dois for removem pontos e traços.
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

	// Valida dígitos verificadores
	if (numero != base + digitos) {
		SetarTextoDeMensagem("mensagemDeErro", "CPF Inválido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}

	/* Não serão considerados válidos os seguintes CPF:
	 * 000.000.000-00, 111.111.111-11, 222.222.222-22, 333.333.333-33, 444.444.444-44,
	 * 555.555.555-55, 666.666.666-66, 777.777.777-77, 888.888.888-88, 999.999.999-99.
	 */
	algUnico = true;
	for (i=1; algUnico && i<NUM_DIGITOS_CPF; i++)
	{
		algUnico = (numero.charAt(i-1) == numero.charAt(i));
	}
	
	if(algUnico) {
		SetarTextoDeMensagem("mensagemDeErro", "CPF Inválido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	return true;
	
} //ValidarCpf


/**
 * Testa se a String pCnpj fornecida é um CNPJ válido.
 * Qualquer formatação que não seja algarismos é desconsiderada.
 * @param String pCnpj
 * 	String fornecida para ser testada.
 * @return <code>true</code> se a String fornecida for um CNPJ válido.
 */
function ValidarCnpj(obj)
{
	var pCnpj = obj.value;
	
	if(pCnpj.length == 0) {
		SetarTextoDeMensagem("mensagemDeErro", "O CNPJ se encontra vazio.");
		MudarPropriedade(obj, 'backgroundColor', '#f4e5e0');		
		return false;
	}
	
	// Esses três for removem pontos, traços e barras, etc.
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

	// Valida dígitos verificadores
	if (numero != base + ordem + digitos) {
		SetarTextoDeMensagem("mensagemDeErro", "CNPJ Inválido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}

	/* Não serão considerados válidos os CNPJ com os seguintes números BÁSICOS:
	 * 11.111.111, 22.222.222, 33.333.333, 44.444.444, 55.555.555,
	 * 66.666.666, 77.777.777, 88.888.888, 99.999.999.
	 */
	algUnico = numero.charAt(0) != '0';
	for (i=1; algUnico && i<NUM_DGT_CNPJ_BASE; i++)
	{
		algUnico = (numero.charAt(i-1) == numero.charAt(i));
	}
	if (algUnico) {
		SetarTextoDeMensagem("mensagemDeErro", "CNPJ Inválido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');	
		return false;
	}

	/* Não será considerado válido CNPJ com número de ORDEM igual a 0000.
	 * Não será considerado válido CNPJ com número de ORDEM maior do que 0300
	 * e com as três primeiras posições do número BÁSICO com 000 (zeros).
	 * Esta crítica não será feita quando o no BÁSICO do CNPJ for igual a 00.000.000.
	 */
	if (ordem == "0000") {
		SetarTextoDeMensagem("mensagemDeErro", "CNPJ Inválido.");
		MudarPropriedade(obj, 'backgroundColor', 'red');
		return false;
	}
	
	SetarTextoDeMensagem("mensagemDeErro", "");
	MudarPropriedade(obj, 'backgroundColor', 'white');
	
	return (base == "00000000"
		|| parseInt(ordem, 10) <= 300 || base.substring(0, 3) != "000");
} //ValidarCnpj


/**
 * Testa se a String pCpfCnpj fornecida é um CPF ou CNPJ válido.
 * Se a String tiver uma quantidade de dígitos igual ou inferior
 * a 11, valida como CPF. Se for maior que 11, valida como CNPJ.
 * Qualquer formatação que não seja algarismos é desconsiderada.
 * @param String pCpfCnpj
 * 	String fornecida para ser testada.
 * @return <code>true</code> se a String fornecida for um CPF ou CNPJ válido.
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
    // Data no formato brasileiro, que será exibida na mensagem de confirmação:
    var dataParaMensagem = dataIdeal;
    
    // Recebe dd/mm/aaaa e inverte para aaaa/mm/dd:
    dataDigitada = InverterData(dataDigitada);
    dataIdeal    = InverterData(dataIdeal);

    // Substitui qualquer caractere que não seja um dígito pela barra (aceita
    // qualquer caractere como separador de ano-mes-dia:
    dataIdeal    = dataIdeal.replace(/[^0-9]{1}/g, '/');
    dataDigitada = dataDigitada.replace(/[^0-9]{1}/g, '/');

    // Cria um array ano/mes/dia:
    var arrDataIdeal    = dataIdeal.split('/');
    var arrDataDigitada = dataDigitada.split('/');

    // Macete: monta um número inteiro com as strings do ano + mes + dia, ex.:
    // 20001230 para 2000/12/30 (ano/mes/dia)
    var intDataIdeal = parseInt( arrDataIdeal[0].toString()
                     + arrDataIdeal[1].toString()
                     + arrDataIdeal[2].toString(), 10 );

    var intDataDigitada = parseInt( arrDataDigitada[0].toString()
                        + arrDataDigitada[1].toString()
                        + arrDataDigitada[2].toString(), 10 );

    if(intDataIdeal > intDataDigitada)
    {
        return confirm("A data digitada é menor que a ideal ("
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
	

	SetarTextoDeMensagem("mensagemDeErro", "Número do lote Inválido.");
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
	

	SetarTextoDeMensagem("mensagemDeErro", "Quantidade Inválida.");
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
	
	SetarTextoDeMensagem("mensagemDeErro", "A data inicial deve ser menor ou igual a data final e ambas devem ser válidas.");
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
        
        SetarTextoDeMensagem("mensagemDeErro", "A faixa etária deve estar preenchida.");
		MudarPropriedade(valori, 'backgroundColor', '#f4e5e0');
		MudarPropriedade(valorf, 'backgroundColor', '#f4e5e0');
        return false;
        
    }
    
    if( isNaN(valori.value) || isNaN(valorf.value) ) {
        
        SetarTextoDeMensagem("mensagemDeErro", "Digite apenas números na faixa de idades.");
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
    
    SetarTextoDeMensagem("mensagemDeErro", "A faixa etária inicial deve ser menor ou igual a faixa etária final e ambas devem ser válidas.");
	MudarPropriedade(valori, 'backgroundColor', 'red');
	MudarPropriedade(valorf, 'backgroundColor', 'red');
	
	return false;
    
}
/*
Como calcular anos bissextos

 
Para saber se um ano será bissexto na regra gregoriana que usamos até hoje faz-se a seguinte conta:

Tente dividir o ano por 4. Se o resto for diferente de 0, ou seja, se for indivisível por 4, ele não é bissexto. Se for divisível por 4, é preciso verificar se o ano acaba em 00 (zero duplo). Em caso negativo, o ano é bissexto. Se terminar em 00, é preciso verificar se é divisível por 400. Se sim, é bissexto; se não, é um ano normal. 

Achou confuso? Vejamos na prática como funciona a regra. Tomemos 2008 como exemplo. 2008 é um número divisível por 4 (o resultado é 502) e que não acaba em 00. Logo, esse ano é bissexto. Já o ano 1900 não foi bissexto: é divisível por 4, termina em 00, mas não é divisível por 400. O ano 2000, por sua vez, foi bissexto: é divisível por 4, termina em 00 e é divisível por 400.

A regra de Gregório 13, apesar de ser a mais exata das que existiram, também não resolve totalmente o problema. A cada 3.300 anos seguindo essa regra, o calendário gregoriano terá uma defasagem de 1 dia. Assim, no ano 4.882, o nosso calendário vai estar um dia adiantado com relação ao início da primavera. Ainda não há uma solução planejada para tal ano, já que os astrônomos de hoje em dia resolveram deixar a preocupação para os seus colegas do futuro.

Porque o dia a mais é em fevereiro?

Comecemos explicando por que fevereiro é mais curto que os outros meses.

O primeiro calendário romano foi supostamente bolado por Rômulo (mitologicamente, um dos fundadores de Roma), por volta de 753 a.C. Ele só tinha 10 meses, de 30 ou 31 dias, e o ano durava 304 dias ao todo, segundo o professor de astronomia Roberto Boczko, da USP.

Essa folhinha não estava em sincronia com as estações do ano e, quando o sucessor Numa tomou o poder, instituiu um novo calendário, baseado nas fases da lua com 12 meses de 29 ou 31 dias. Nenhum deles tinha 30 dias porque, nessa época, acreditava-se que os números pares desagradavam aos deuses e, por isso, eram sinal de azar. A soma dos dias do ano, porém, era par (356 dias). Para que o ano inteiro não fosse considerado azarado por ser par, decidiu-se que um mês teria de ser “sacrificado” e ter um número par de dias para que o ano somasse 355 dias.

O escolhido foi fevereiro, considerado na época um mês ruim (“o nome 'fevereiro' foi dado justamente por aquele ser o mês das febres, das cobranças e das execuções judiciárias”, conta Boczko). Arredondar para 28 dias, em vez de 30, foi uma escolha estratégica: o mês azarado deveria acabar o mais rápido possível. Quando surgiu a necessidade de acrescentar um dia ao ano, nada melhor que colocá-lo no mês mais curto.



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

Todos os anos que sejam múltiplos de 4 mas que não sejam múltiplos de 100, com exceção daqueles que são múltiplos de 400, são bissextos

function AnoBisexto(ano)
{
	if(((parseInt(ano, 10) % 4 == 0) && (parseInt(ano, 10) % 100 != 0)) 
		|| parseInt(ano, 10) % 400 == 0) {
		
		return true;
	}
	return false;
}

*/
