

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

function ativaOptionsDisabled()
{ 
    var sels = document.getElementsByTagName('select');
    
    for(var i=0; i < sels.length; i++){

        // Colocar aqui as IDs dos selects que suportarão o
        // esquema do desabilitado:
       if( sels[i].id == 'campanha') {
           
           sels[i].onchange= function(){ //pra se mudar pro desabilitado

                if(this.options[this.selectedIndex].disabled){
                    if(this.options.length<=1){
                        this.selectedIndex = -1;
                    }else if(this.selectedIndex < this.options.length - 1){
                        this.selectedIndex++;
                    }else{
                        this.selectedIndex--;
                    }
                }

           }
       }
        
        if(sels[i].options[sels[i].selectedIndex].disabled){
            //se o selecionado atual desabilitado chamo o onchange
            sels[i].onchange();
        }    
        for(var j=0; j < sels[i].options.length; j++){ //colocando o estilo
            if(sels[i].options[j].disabled){
                sels[i].options[j].style.color = '#CCC';
            }
        }
        
    }
    
    
}
window.attachEvent("onload", ativaOptionsDisabled);
