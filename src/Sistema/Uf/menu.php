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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ai ai ai</title>
<style type="text/css">
<!-- 


	#inicial  {
		position:absolute;
	}	
	#principal  {
		position:absolute;
	}	
	#principal2  {
		position:absolute;
		
	}	
	#nivel1  {
		position:absolute;
	}	
	#nivel2  {
		position:absolute;
	}	

	#menu ul {

        font-family: Helvetica, Geneva, Arial;
        font-size:small;
		display:table;
		margin:0px;
		padding:0px;
		padding-top:3px;
		height:22px;
		cursor:pointer;
		/* background: url("./Imagens/tic.png") no-repeat right; */
        /* margin-right: 25px; */
		
	}
	#menu ul:hover {
        border-left: 3px double white;
        border-bottom:1px dashed white;
        font-weight: bold;
	}

	
-->
</style>
</head>

<body>

<?php


    $arrMenu = Array('Início|?'.$crip->Cifrar('pagina=inicio'),
                     'Login|?' .$crip->Cifrar('pagina=login'));
   // if ( Sessao::Permissao('UNIDADES_') ) {

       if( Sessao::Permissao('UNIDADES_INSERIR')) $arrMenu['Administração']['Unidades'][] = 'Inserir|?'.$crip->Cifrar('pagina=Adm/inserirUnidadeDeSaude');
       if( Sessao::Permissao('UNIDADES_LISTAR') ) $arrMenu['Administração']['Unidades'][] = 'Listar|?' .$crip->Cifrar('pagina=Adm/listarUnidades');
       if( Sessao::Permissao('UNIDADES_ESTOQUE')) $arrMenu['Administração']['Unidades'][] = 'Estoque|?'.$crip->Cifrar('pagina=Adm/estoqueDaUnidade');

    //}
   // if( Sessao::Permissao('ACS_') ) {

       if( Sessao::Permissao('ACS_INSERIR')) $arrMenu['Administração']['Acs'][] = 'Inserir|?'.$crip->Cifrar('pagina=Adm/inserirAcs');
       if( Sessao::Permissao('ACS_LISTAR'))  $arrMenu['Administração']['Acs'][] = 'Listar|?' .$crip->Cifrar('pagina=Adm/listarAcs');

   // }
   // if( Sessao::Permissao('CAMPANHAS_') ) {

       if( Sessao::Permissao('CAMPANHAS_INSERIR')) $arrMenu['Administração']['Campanhas'][] = 'Inserir|?'.$crip->Cifrar('pagina=Adm/inserirCampanha');
       if( Sessao::Permissao('CAMPANHAS_LISTAR'))  $arrMenu['Administração']['Campanhas'][] = 'Listar|?' .$crip->Cifrar('pagina=Adm/listarCampanhas');

    //}
    //if( Sessao::Permissao('CONFIGURAR_') ) {

       if( Sessao::Permissao('CONFIGURAR_TEXTOINICIAL')) $arrMenu['Administração']['Configurar'][] = 'Texto Inicial|?'.$crip->Cifrar('pagina=Adm/editarInicio');
       if( Sessao::Permissao('CONFIGURAR_NOTA'))         $arrMenu['Administração']['Configurar'][] = 'Nota|?'         .$crip->Cifrar('pagina=Adm/nota');

   // }

    if ( Sessao::Permissao('USUARIOS_ACESSOS') ) $arrMenu['Administração']['Visualizar'][] = 'Usuarios|?'.$crip->Cifrar('pagina=Adm/acessos');

    if( Sessao::Permissao('CAMPANHAS_')) $arrMenu['Administração']['Visualizar'][] = 'Vacinas|?'.$crip->Cifrar('pagina=Adm/vacinas');

    //if( Sessao::Permissao('INDIVIDUOS_') ) {

       if( Sessao::Permissao('INDIVIDUOS_BUSCAR'))         $arrMenu['Indivíduo'][] = 'Buscar|?'        .$crip->Cifrar('pagina=Adm/listarPessoa');
       if( Sessao::Permissao('INDIVIDUOS_CADASTRAR'))      $arrMenu['Indivíduo'][] = 'Cadastrar|?'     .$crip->Cifrar('pagina=Adm/inserirPessoa');
       if( Sessao::Permissao('INDIVIDUOS_VACINAR'))        $arrMenu['Indivíduo'][] = 'Vacinar|?'       .$crip->Cifrar('pagina=Adm/listarPessoasVacinaveis');
       if( Sessao::Permissao('INDIVIDUOS_CADERNETA'))      $arrMenu['Indivíduo'][] = 'Caderneta|?'     .$crip->Cifrar('pagina=Adm/listarPessoasVacinaveis&retroativo');
       if( Sessao::Permissao('INDIVIDUOS_INTERCORRENCIA')) $arrMenu['Indivíduo'][] = 'Evento Adverso|?'.$crip->Cifrar('pagina=Adm/intercorrencias');

   // }
    
    //if( Sessao::Permissao('RELATORIOS_') ) {

       $relatorios = 'Relatórios|?'.$crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=geral');

       if( Sessao::Permissao('RELATORIOS_INDIVIDUO'))      $arrMenu[$relatorios][] = 'Indivíduo|?'     .$crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=individuo');
       if( Sessao::Permissao('RELATORIOS_ROTINA'))         $arrMenu[$relatorios][] = 'Rotina|?'        .$crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=rotina');
       if( Sessao::Permissao('RELATORIOS_CAMPANHA'))       $arrMenu[$relatorios][] = 'Campanha|?'      .$crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=campanha');
       if( Sessao::Permissao('RELATORIOS_INTERCORRENCIA')) $arrMenu[$relatorios][] = 'Vacinação|?'.$crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=geral');
       if( Sessao::Permissao('RELATORIOS_INTERCORRENCIA')) $arrMenu[$relatorios][] = 'Inativados|?'.$crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=geral');
       if( Sessao::Permissao('RELATORIOS_INTERCORRENCIA')) $arrMenu[$relatorios][] = 'Estoque|?'.$crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=geral');
       if( Sessao::Permissao('RELATORIOS_INTERCORRENCIA')) $arrMenu[$relatorios][] = 'PNI|?'.$crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=geral');

   // }

    $arrMenu[] = 'Contato|?' .$crip->Cifrar('pagina=contato');
	
	$dadosMenu = " [ '";
	$nivel3 = '';
	
	foreach( $arrMenu as $chave => $valor)
	{
							
		if(!is_array($valor)) { 
			
			if(substr_count($chave, '|a'))
			{
				list($texto,$link) = explode('|', $chave);
				
				if(substr_count($valor, '|a')) {
					
					// link ---------------
					list($textoValor,$linkValor) = explode('|', $valor);
					$dadosMenu .=  "<a href=\'$link\'>$texto</a> -> <a href=\'$linkValor\'>$textoValor </a> ";
				
				}else $dadosMenu .=  "<a href=\'$link\'>$texto</a> -> $valor";
			}
			else{

                if(is_int($chave)) $dadosMenu .=  "$valor"; // primeiro nivel com chave
                else $dadosMenu .=  " $chave -> $valor"; // primeiro nivel sem chave (chave inteira Ex.: 0,1,2,3)

            }
		}
		else {  
		
			if(substr_count($chave, '|a')) {
				list($textoValor,$linkValor) = explode('|', $chave);
				$chave =  " <a href=\'$linkValor\'>$textoValor</a> ";
			}
			$dadosMenu .=  " $chave";
		
		}
		
		if(is_array($valor) && count($valor) > 0) {
			
			foreach($valor as $chave2 => $valor2) 
			{
				if(!is_int($chave2)) { 
					
					if(is_array($valor2)) {
						
						foreach($valor2 as $valor3) 
						{
							if(substr_count($valor3, '|a')) {
								list($textoValor,$linkValor) = explode('|', $valor3);
								$nivel3 .=  " => <a href=\'$linkValor\'>$textoValor</a>";
							}
							else $nivel3 .=  " => $valor3 ";
						}
					}
					
					// Transformar em link... separar tudo depois em metodos
						
						if(substr_count($chave2, '|a')) {
								list($textoValor,$linkValor) = explode('|', $chave2);
								$chave2 =  " <a href=\'$linkValor\'>$textoValor</a> ";
						} 
						
					//---
						
					if(strlen($nivel3) > 0){					
						$dadosMenu .=  " -> $chave2 $nivel3";
					}
					else {
													
						if(substr_count($valor2, '|a')) {
								list($textoValor,$linkValor) = explode('|', $valor2);
								$dadosMenu .=  " -> $chave2 => <a href=\'$linkValor\'>$textoValor</a> $nivel3";
						} 
						else $dadosMenu .=  " -> $chave2 => $valor2  $nivel3";
					}
					$nivel3 = '';
				}
				else { 
				
					if(substr_count($valor2, '|a')) {
							list($textoValor,$linkValor) = explode('|', $valor2);
							$valor2 =  " <a href=\'$linkValor\'>$textoValor</a> ";
					} 
						
					$dadosMenu .=  " -> $valor2 ";
				}
			}
		}
		$dadosMenu .= ', ';
	}
	$dadosMenu = trim($dadosMenu,', ');
	$dadosMenu .= "' ]";
	
	echo '<script>';
	if(substr_count($_SERVER['HTTP_USER_AGENT'],'Firefox') || substr_count($_SERVER['HTTP_USER_AGENT'],'NET')) echo ' var descontoAlturaDoMenu = 3; ';
	else echo ' var descontoAlturaDoMenu = 0; '; 

	if(substr_count($_SERVER['HTTP_USER_AGENT'],'Firefox') || substr_count($_SERVER['HTTP_USER_AGENT'],'NET')) echo ' var descontoAlturaDoMenuNivel2 = 0; ';
	else echo ' var descontoAlturaDoMenuNivel2 = 3; '; 
	
	if(substr_count($_SERVER['HTTP_USER_AGENT'],'Firefox') || substr_count($_SERVER['HTTP_USER_AGENT'],'NET')) echo ' var descontoMarginDoMenuNivel2 = 0; ';
	else echo ' var descontoMarginDoMenuNivel2 = 25; '; 
	

	if(substr_count($_SERVER['HTTP_USER_AGENT'],'Firefox') || substr_count($_SERVER['HTTP_USER_AGENT'],'NET')) echo ' var descontoLarguraDoMenu = 20; ';
	else echo ' var descontoLarguraDoMenu = 0; ';

	//if(substr_count($_SERVER['HTTP_USER_AGENT'],'Safari') && !substr_count($_SERVER['HTTP_USER_AGENT'],'Chrome')) echo " var background = ' background-color: #82adb6;'; ";
	/*else */echo " var background = 'background: url(\'./Imagens/fundoMenuSubmenu.png\');'; ";
	
	//echo 'alert(background);';
	
	echo '</script>';
?>


<script>

var styleUl = ' style="padding-left:10px; width:'+(130-descontoLarguraDoMenu)+'px; padding-right:10px; " ';

function criarMenuTresNiveis(itens)
{
	var html = '';
	var niveis1 = '';
	var menu3 = '';
	var menu2 = '';
	var menu1 = '';
	var nivel2e3 = '';
	var arrMenuNivel2 = new Array();
	var arrMenuNivel3 = new Array();
	var conteudoUl = '';
	var linkUl = '';
    var indicadorLinkMenu = '';
	
	itens = itens.toString();
	niveis1 = itens.split(',');
	
	for ( var itemMenu in niveis1) 
	{	
		
		menu1 +=  niveis1[itemMenu].split('->',1);
		
		niveis2 = niveis1[itemMenu].toString();
		niveis2 = niveis2.split('->');
		
		
		if(niveis2.length > 1) { 
			var i = 0;
			arrMenuNivel3 = new Array();
			for ( var itemMenu2 in niveis2 )
			{

				if(itemMenu2 > 0) menu2 +=  niveis2[itemMenu2].split('=>',1) ;
		   			
				 niveis3 = niveis2[itemMenu2].toString();
				 niveis3 = niveis3.split('=>');	
					
				 menu3 = '';
				 
				 
		   		 for ( var itemMenu3 in niveis3)
				 {
					 //if(itemMenu3 > 0) menu3 +=  niveis3[itemMenu3] ;
					 
					 arrMenuNivel3[i] =  itemMenu2 +'!'+ niveis3[itemMenu3] ;
					 i++;
				 }
			
				/*nivel2e3 += '<b>' + menu2 + '<b>' +menu3+ '</b></b>' ;*/
				//menu3 = '<div id=\\\'nivel2\\\' style=\\\'margin-left:120px; width:120px;  background-color:#abc; margin-top:'+itemMenu3*20+'px; \\\'><ul>abcd</ul></div>'; 
				nivel2e3 += '<i>' + menu2 +  '</i>' ;
				
				arrMenuNivel2[itemMenu2] = menu2;
				
				menu2 = '';
			}
			
			
		}
	
		conteudoUl = menu1.split('|')[0];


		if(menu1.indexOf('|') >= 0) linkUl = 'onclick="window.location=\''+menu1.split('|')[1]+'\'"';
		else linkUl = '';

        if(arrMenuNivel2.length) indicadorLinkMenu = ' background: url(\'./Imagens/indicadorLinkMenu.png\') no-repeat right; margin-right:55px; ';
        else indicadorLinkMenu = '';
		/* html += '<ul id=\'teste:'+itemMenu+'\' onmouseover="Posicao(this);" >' + menu1 + '</ul>' +nivel2e3 ; */
		html += '<ul '+styleUl.replace(' "', indicadorLinkMenu+' "')+linkUl+' id=\'teste:'+
		itemMenu+'\' onDblClick="document.getElementById(\'principal\').innerHTML=\'\'" onmouseover="Posicao(this, \''
																						+arrMenuNivel2+'\', \''+arrMenuNivel3+'\');" >' + conteudoUl + '</ul>';
		
		arrMenuNivel2 = new Array();
		menu3 = '';
		menu2 = '';
		menu1 = '';
		nivel2e3 = '';
	}
	
	document.getElementById('inicial').innerHTML = html;
}


var visivel = '';

/* ----------------------------------------------------- */ 

function Posicao( margin, conteudoNivel2, conteudoNivel3)
{
	var conteudo = '';
	
	var conteudoUl = '';
	var linkUl = '';
	var posicaoNoMenu = margin.id.split(':')[1];
	var novaPosicao = parseFloat(posicaoNoMenu * 22)+(posicaoNoMenu*descontoAlturaDoMenu); /* + parseFloat(posicaoNoMenu * 2) ;*/
	//document.getElementById('nivel1').style.marginTop = posicaoNoMenu * 20;
	
	itens = conteudoNivel2.toString();
	conteudoNivel2 = itens.split(',');

    //---------------------- indicadorLinkMenu
    var item = '';
    var teste = '';
    var itens = '';
    itens = conteudoNivel3.toString();
	conteudoNivel3 = itens.split(',');

    for ( var item in conteudoNivel3 )
    {
        teste += conteudoNivel3[item].split('!')[0];
    }
    var qtdDeCaracter = (teste.length)-1;
    
    if(teste[teste.length-1] != qtdDeCaracter) indicadorLinkMenu = ' background: url(\'./Imagens/indicadorLinkMenu.png\') no-repeat right; ';
    else indicadorLinkMenu = '';

    // -------------------------------
	for ( var itemMenu2 in conteudoNivel2 )
	{
		conteudoUl = conteudoNivel2[itemMenu2].split('|')[0];
		if(conteudoNivel2[itemMenu2].indexOf('|') >= 0) linkUl = ' onclick="window.location=\''+conteudoNivel2[itemMenu2].split('|')[1]+'\'" ';
		if(conteudoNivel2[itemMenu2].length > 0) conteudo += ' <ul '+styleUl.replace(' "', indicadorLinkMenu+' "')+'  id=\'testeB:'+itemMenu2+'\'  onmouseover="PosicaoNivel2(this,'+
														   novaPosicao+', \''+conteudoNivel3+'\');" '+linkUl+'> '+ conteudoUl +'</ul>';
		
	}
	
	document.getElementById('principal').innerHTML = '' + 
													 ' <div id="nivel1" style="margin-left:120px;  ' +background+ 
													 ' margin-top: '+
													   novaPosicao + 'px">' +
											 
													 ' <div id=\'principal2\' style=""></div>' + conteudo +
													
												/*	 ' <ul id=\'testeB:'+novaPosicao+'\' onmouseover="PosicaoNivel2(this,'+novaPosicao+', \''+conteudoNivel3+'\');" > '+ conteudoNivel2 +' - '+ novaPosicao +'</ul>' + */
																										 
													 ' </div>'; 

	

}

/* -------------------------------------------------- */

function PosicaoNivel2( margin, ultimaposicao,conteudoNivel3)
{
	var itens = '';
	var conteudo = '';
	
	
	//alert('margin' + margin.id + '\nultima' + ultimaposicao  + '\nconteudo' + conteudoNivel3);
	var posicaoNoMenu = margin.id.split(':')[1];
	var novaPosicao = parseFloat(posicaoNoMenu * 23)+(posicaoNoMenu*descontoAlturaDoMenu)-parseInt(posicaoNoMenu);/* + parseFloat(posicaoNoMenu * 2) ;*/
	
	novaPosicao = novaPosicao-25+parseInt(descontoAlturaDoMenuNivel2);
														   
	itens = conteudoNivel3.toString();
	conteudoNivel3 = itens.split(',');
	
	
	var item3 = 0;
    var i = 0;
	var conteudoUl = '';
	var linkUl = '';
	for ( var itemMenu3 in conteudoNivel3 )
	{	
		
		item3 = conteudoNivel3[itemMenu3].split('!', 1);
		//alert(item3 +'=='+ itemMenu3);
		if(item3==posicaoNoMenu) i++;
		if(i > 1 && item3==posicaoNoMenu){ 
		
			
			conteudoUl = conteudoNivel3[itemMenu3].split('|')[0];
			conteudoUl = conteudoUl.split('!')[1];
			if(conteudoNivel3[itemMenu3].indexOf('|') >= 0) linkUl = ' onclick=\'window.location=\"'+conteudoNivel3[itemMenu3].split('|')[1]+'\"\' ';
			
			conteudo += ' <ul '+styleUl+linkUl+' > '+ conteudoUl.split('|')[0] +'</ul>';
		}
		//alert(conteudoNivel3[itemMenu3].split('|')[1]);
		
	}
	
	document.getElementById('principal2').innerHTML =  ' <div id=\'nivel2\'  style="margin-left:'+(145-descontoMarginDoMenuNivel2-descontoLarguraDoMenu)+'px; margin-top:'+novaPosicao+'px; background: url(\'./Imagens/fundoMenuSubmenu.png\'); ">' +
														conteudo +
													   '</div>';

	

}

</script>


<div  style="position:absolute" id="menu">


    <div id="inicial"  style="position:absolute; ">    
    
    </div>
    
    <div id='principal' >
    
    <div id="nivel1" >
        <div id='principal2'  ></div>
    </div> 
    
       
    </div> 
    
</div>

<?php echo "<script>criarMenuTresNiveis($dadosMenu)</script>"; ?>
</body>
</html>
