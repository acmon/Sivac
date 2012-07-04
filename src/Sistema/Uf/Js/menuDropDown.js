

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

function DOMgetElementsByClassName($node,$className){
  var $node, $atual, $className, $retorno = new Array(), $novos = new Array();
  $retorno = new Array();
  for (var $i=0;$i<$node.childNodes.length;$i++){
   $atual = $node.childNodes[$i];
   if($atual.nodeType==1){
      $classeAtual = $atual.className;                              
      if(new RegExp("\\b"+$className+"\\b").test($classeAtual)){
      $retorno[$retorno.length] = $atual;
      }
      if($atual.childNodes.length>0){
      $novos = DOMgetElementsByClassName($atual,$className);
      if($novos.length>0){
     $retorno = $retorno.concat($novos);
      }
      }
   }
  }
  return $retorno;
}
function addEvent(obj, evType, fn){
  if (obj.addEventListener){
   obj.addEventListener(evType, fn, true)}
  if (obj.attachEvent){
   obj.attachEvent("on"+evType, fn)}
}
function ativaHover(classe) {
  var pais = DOMgetElementsByClassName(document.body,classe);
  for (var j=0; j<pais.length; j++) {
   var sfEls = pais[j].getElementsByTagName("li");
   for (var i=0; i<sfEls.length; i++) {
      sfEls[i].onmouseover=function() {
       this.className+=" over";
      }
      sfEls[i].onmouseout=function() {
       this.className=this.className.replace(new RegExp(" over\\b"), "");
      }
   }
  }
}
  
addEvent(window,"load",function () { ativaHover("menuVertical"); });