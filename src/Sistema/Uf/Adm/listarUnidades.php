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
	 <blockquote>As unidades que possuem indiv�duos ativos, vacinas em estoque e
     agentes de sa�de ligados � mesma n�o podem ser exclu�das. Caso a unidade de
     sa�de tenha sido desativada em seu munic�pio, ent�o no sistema todos os ACS,
     indiv�duos e vacinas em estoque precisam ser removidas da mesma, para que a
     unidade possa tamb�m ser removida.</blockquote>';

$icones[] = array('editar', 'Alterar unidade de sa�de');
$icones[] = array('excluir', 'Excluir unidade de sa�de');

$legenda = new Legenda($icones);
$legenda->ExibirLegenda();
 