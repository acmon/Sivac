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



require_once './autoload.php';

parse_str($_SERVER['QUERY_STRING']);

if( isset($imagem) ) {
    
    $icone = new Icone;
    
    switch($imagem) {
    
        case 'adicionar':
            $icone->Adicionar();
            break;
            
        case 'adicionar_desab':
            $icone->Adicionar_desab();
            break;
            
        case 'decrementar':
            $icone->Decrementar();
            break;
            
        case 'decrementar_desab':
            $icone->Decrementar_desab();
            break;
            
        case 'desmarcar':
            $icone->Desmarcar();
            break;
            
        case 'detalhes':
            $icone->Detalhes();
            break;
            
        case 'detalhes_desab':
            $icone->Detalhes_desab();
            break;
            
        case 'editar':
            $icone->Editar();
            break;
    
        case 'excluir':
            $icone->Excluir();
            break;
            
        case 'excluir_desab':
        	$icone->Excluir_desab();
        	break;
        
        case 'fundoLegenda':
        	$icone->FundoLegenda();
        	break;
        	
        case 'listar':
            $icone->Listar();
            break;
            
        case 'listar_desab':
            $icone->Listar_desab();
            break;
            
        case 'ok':
            $icone->Ok();
            break;
            
        case 'ok_vermelho':
            $icone->Ok_vermelho();
            break;
            
        case 'pesquisar':
            $icone->Pesquisar();
            break;
            
        case 'vacinar':
            $icone->Vacinar();
            break;
            
        case 'vacinar_desab':
            $icone->Vacinar_desab();
            break;
            
        default:
        	break;
  
    }
}
//------------------------------------------------------------------------------