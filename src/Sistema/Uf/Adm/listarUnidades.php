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



//require_once './autoload.php';
Sessao::Singleton()->ChecarAcesso();
/*require_once('./tHtml.php');
require_once('./tUnidadeDeSaude.php');*/

$UnidadeSaude = new UnidadeDeSaude();

$UnidadeSaude->UsarBaseDeDados();

echo '<h3 align="center">Unidades cadastradas</h3>';

$UnidadeSaude->ExibirListaDeUnidades();

$UnidadeSaude->ExibirMensagensDeErro();

echo '<p><strong>Nota:</strong>
	 <blockquote>As unidades que possuem indivíduos ativos, vacinas em estoque e
     agentes de saúde ligados à mesma não podem ser excluídas. Caso a unidade de
     saúde tenha sido desativada em seu município, então no sistema todos os ACS,
     indivíduos e vacinas em estoque precisam ser removidas da mesma, para que a
     unidade possa também ser removida.</blockquote>';

$icones[] = array('editar', 'Alterar unidade de saúde');
$icones[] = array('excluir', 'Excluir unidade de saúde');

$legenda = new Legenda($icones);
$legenda->ExibirLegenda();
 