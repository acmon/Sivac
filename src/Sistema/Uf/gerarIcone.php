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