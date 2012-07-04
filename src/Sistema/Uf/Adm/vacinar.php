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



$vacinar = new PessoaVacinavel();

$vacinar->UsarBaseDeDados();

$crip = new Criptografia();
     
$decifrado = $crip->Decifrar($_SERVER['QUERY_STRING']);

parse_str($decifrado);

//-----------------------------------------------------------------------------

if( isset($numerodadose, $usuario_id, $vacina_id)) {
	
	if(!isset($foi_vacinado)){

		$qs = $crip->Cifrar("$decifrado&foi_vacinado");

			if( isset($_POST['obs']) )
				$obs = $_POST['obs'];
			else
				$obs = '';
			if($vacinar->VerificarEstoqueDaUnidadeParaACampanha($campanha_id) || $vacinar->VerificarEstoqueDaUnidadeParaRotina($vacina_id))
            {

                if ($vacinar->Vacinar($vacina_id, $numerodadose, $usuario_id, $obs, $campanha_id, $ciclo)) {
                    //print_r($_SESSION);
                    //echo "<script>window.location = '?$qs'</script>";
                }
                else {
                     $vacinar->ExibirMensagem('Nao foi possível vacinar o indivíduo!');
                }
            }
            else $vacinar->ExibirMensagem('Não há  estoque para esta vacina.');

	}
}

$vacinar->ExibirMensagensDeErro(); 


//-------------------------- LISTAR DOSES ------------------------------------

if (isset($usuario_id, $vacina_id) ) {

				
    $vacinar->ListarDosesIdeais($usuario_id, $vacina_id, $campanha_id);

    if( $campanha_id == 0 )
    echo '<p><strong>Nota:</strong><blockquote>Antes de vacinar, confira as
	datas (datas desatualizadas se apresentam em vermelho).
	</blockquote></p>';

			
	$icones[] = array('vacinar', 'Aplicar dose no indivíduo');
	
	$legenda = new Legenda($icones);
	$legenda->ExibirLegenda();
	
	$botao = new Form();
	$botao->BotaoVoltarHistorico();
}


?>
 
