<?php


/*
 *  Sivac - Sistema Online de Vacinaзгo     
 *  Copyright (C) 2012  IPPES  - Institituto de Pesquisa, Planejamento e Promoзгo da Educaзгo e Saъde   
 *  www.sivac.com.br                     
 *  ippesaude@uol.com.br                   
 *                                                                    
 *  Este programa e software livre; vocк pode redistribui-lo e/ou     
 *  modificб-lo sob os termos da Licenзa Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a versгo 2 da      
 *  Licenзa como (a seu critйrio) qualquer versгo mais nova.          
 *                                                                    
 *  Este programa й distribuнdo na expectativa de ser ъtil, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia implнcita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licenзa Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Vocк deve ter recebido uma copia da Licenзa Publica Geral GNU     
 *  junto com este programa; se nгo, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licenзa no diretуrio Sistema/licenca_en.txt 
 *                                Sistema/licenca_pt.txt 
 */


//Sessao::Singleton()->ChecarAcesso();

$vacina = new Vacina();

$vacina->UsarBaseDeDados();

$vacina->ExibirFormularioVisualizarVacinas();
 
if(isset($_POST['vacina']) && count($_POST) && $_POST['vacina'] != 0 ) $vacina->VisualizarVacinas($_POST['vacina']);

?>