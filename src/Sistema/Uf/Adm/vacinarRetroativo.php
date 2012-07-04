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

$pessoa = new PessoaVacinavel();

$pessoa->UsarBaseDeDados();

$crip = new Criptografia();

$decifrado = $crip->Decifrar($_SERVER['QUERY_STRING']);

parse_str($decifrado);

if (isset($_POST['dataRetroativa'])) {

$ciclo = 1;

		if(!isset($foi_vacinado)){
			
			
			$decrementarEstoque = false;
			
			if (isset($_POST['checkRetroativo']) && $_POST['checkRetroativo'] == 'on' ) {

				$decrementarEstoque = 1;
				
			}
			$data = new Data();
			
			$dataDaAplicacao = $data->InverterData($_POST['dataRetroativa'][0]).date(' H:i:s');



            $totalDeDoses       = $pessoa->TotalDeDoses($vacina_id);
            list($proximaDose, $idadeAno, $idadeMes, $idadeDia ) =
                $pessoa->IncrementarProximaDoseParaVacinar($usuario_id,
                                                         $vacina_id,
                                                         $numerodadose-1,
                                                         $totalDeDoses,
                                                         $dataDaAplicacao);
            
			if($pessoa->ValidarDataRetroativa($dataDaAplicacao, $usuario_id, $vacina_id)
			
				&& $pessoa->VacinarRetroativo($vacina_id, $numerodadose,
					$usuario_id, "Vacinação da dose $numerodadose retroativa", 
					$dataDaAplicacao, $campanha_id, $decrementarEstoque, $ciclo,
                    $idadeAno, $idadeMes, $idadeDia, $proximaDose) ) {

				$arrDependencias = $pessoa->GerarDependencia($vacina_id);
				$nomeDaVacina = $pessoa->ExibirNomeDaVacina($vacina_id);
					
				foreach( $arrDependencias as $vacinaDependente ){
				
					$obsDependente = "Vacinação da dose $numerodadose retroativa<br />Vacinado por $nomeDaVacina"; 
				
					$pessoa->VacinarRetroativo($vacinaDependente, $numerodadose,
					$usuario_id,$obsDependente, 
					$dataDaAplicacao, $campanha_id, $decrementarEstoque,
                    $ciclo, $idadeAno, $idadeMes, $idadeDia, $proximaDose);
				
				}		
					
				$qs = $crip->Cifrar("$decifrado&foi_vacinado");
					
				//echo "<script>window.location = '?$qs'</script>";
				
			}
			else {
				echo '<center><h3>Nao foi possível vacinar o indivíduo!</h3></center>';
			}
		}
		
		$_POST = array();
}

$pessoa->GerarTabelaComDosesVacinarRetroativo($usuario_id, $vacina_id);

$icones[] = array('decrementar', 'Descontar dose aplicada do estoque');

$legenda = new Legenda($icones);

$legenda->ExibirLegenda();

$pessoa->ExibirMensagensDeErro();