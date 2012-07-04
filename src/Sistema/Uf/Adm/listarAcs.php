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


Sessao::Singleton()->ChecarAcesso();

$acs = new Acs();

$acs->UsarBaseDeDados();

echo '<h3 align="center">ACS cadastrados</h3>';

$exibiuLista = $acs->ExibirListaDeAcs();

$icones[] = array('editar', 'Alterar cadastro de ACS');
/*if( Sessao::Permissao('ACS_EXCLUIR') )*/ $icones[] = array('excluir', 'Excluir ACS do sistema');

echo '<p><strong>Nota:</strong>
	 <blockquote>Os agentes comunitários de saúde que possuem indivíduos ligados
	 ao mesmo não podem ser excluídos.</blockquote>';

$acs->ExibirMensagensDeErro();

if($exibiuLista) {
	$legenda = new Legenda($icones);
	$legenda->ExibirLegenda();
}

