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
					$usuario_id, "Vacina��o da dose $numerodadose retroativa", 
					$dataDaAplicacao, $campanha_id, $decrementarEstoque, $ciclo,
                    $idadeAno, $idadeMes, $idadeDia, $proximaDose) ) {

				$arrDependencias = $pessoa->GerarDependencia($vacina_id);
				$nomeDaVacina = $pessoa->ExibirNomeDaVacina($vacina_id);
					
				foreach( $arrDependencias as $vacinaDependente ){
				
					$obsDependente = "Vacina��o da dose $numerodadose retroativa<br />Vacinado por $nomeDaVacina"; 
				
					$pessoa->VacinarRetroativo($vacinaDependente, $numerodadose,
					$usuario_id,$obsDependente, 
					$dataDaAplicacao, $campanha_id, $decrementarEstoque,
                    $ciclo, $idadeAno, $idadeMes, $idadeDia, $proximaDose);
				
				}		
					
				$qs = $crip->Cifrar("$decifrado&foi_vacinado");
					
				//echo "<script>window.location = '?$qs'</script>";
				
			}
			else {
				echo '<center><h3>Nao foi poss�vel vacinar o indiv�duo!</h3></center>';
			}
		}
		
		$_POST = array();
}

$pessoa->GerarTabelaComDosesVacinarRetroativo($usuario_id, $vacina_id);

$icones[] = array('decrementar', 'Descontar dose aplicada do estoque');

$legenda = new Legenda($icones);

$legenda->ExibirLegenda();

$pessoa->ExibirMensagensDeErro();