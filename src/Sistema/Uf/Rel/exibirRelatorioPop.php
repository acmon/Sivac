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

$crip = new Criptografia();

Sessao::Singleton()->ChecarAcesso();

$relatorio = new $tipo();

$relatorio->usarBaseDeDados();

////////////////////////////////////////////////////////////////////////////////
if( isset($_POST['faixaInicio']) )   $postFaixaInicio   = $_POST['faixaInicio'];
if( isset($_POST['unidadeInicio']) ) $postUnidadeInicio = $_POST['unidadeInicio'];
if( isset($_POST['faixaFim']) )      $postFaixaFim      = $_POST['faixaFim'];
if( isset($_POST['unidadeFim']) )    $postUnidadeFim    = $_POST['unidadeFim'];
if( isset($_POST['cidade_id']) )     $postCidadeId      = $_POST['cidade_id'];

//print_r($crip->Decifrar($_SERVER['QUERY_STRING']));
//usuario_id pode vir por POST ou parse_str
if( (isset($usuario_id) && count($_POST)) || count($_POST)) {
	
	echo '<div id="listagem">';

        $vacinasIds = false;
        if(isset($_POST['vacina'])) $vacinasIds = $_POST['vacina'];

        if( $tipo == 'RelatorioCadernetaDeVacinacao' )
            $relatorio->Caderneta($usuario_id, $vacinasIds);
           
        else
        {
            // Verifica se o tipo de gráfico é uma listagem ou um gráfico:
            if( isset($_POST['tipoExibicao']) && $_POST['tipoExibicao'] == 'grafico')
            {
                $relatorio->ExibirGrafico();
            }
            else
            {
                $relatorio->ExibirRelatorio($tipo);
                
            }
        }

	echo '</div>';
	
	$relatorio->ExibirMensagensDeErro();
}
else {
    switch ($tipo) {
        
        case 'RelatorioEstoqueUnidadesMunicipio' :
        case 'RelatorioEstoqueDaUnidade' :
                $relatorio->ExibirRelatorio();

        default: return false;
    }
}

$qs = $crip->Cifrar("pagina=Rel/exibirFormularioEscolherRelatorio&tipo=$tipo");

list($end, $qsAnterior) = explode('?',$_SERVER['HTTP_REFERER']);

$qsAnterior = $crip->Decifrar($qsAnterior);

if(!count($_POST) && substr_count($qsAnterior, 'pagina=Adm/vacinar')) header("Location: ../?$qs");