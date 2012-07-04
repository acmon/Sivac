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

$unidade = new UnidadeDeSaude();

$unidade->UsarBaseDeDados();

$unidade->ExibirOpcaoOperacaoDeEstoque(1);

$transporte = new Transporte();

$transporte->UsarBaseDeDados();

$data = new Data();

$unidade->ExibirFormularioAlimentarCentral();

if(count($_POST)) {
	
	// Quando o admin for nivel 1000 ele pode escolher a unidade
	//$unidade_id = $_SESSION['unidadeDeSaude_id'];
	//if($_SESSION['nivel'] == 1000) $unidade_id = $_POST['unidade'];
		
	$vacina_id = $_POST['vacina'];
	$quantidade = $_POST['quantidade'];
	$unidadeDeSaudeOrigem_id  = 0;
	$unidadeDeSaudeDestino_id = $_POST['unidadeCentral'];
	$lote = $_POST['lote'];
	$obs = $_POST['obs'];
	$validadeDoLote = $data->InverterData($_POST['validade']);
	$dataDeTransporte = $data->InverterData($_POST['datadeenvio']);  
    
	$codigoDeBarras = $_POST['codigoDeBarras'];
	$produto = $_POST['produto'];
	
   // Verifica se os dados estão setados... 
	if( isset($_POST['vacina'], $_POST['quantidade']) && empty($_POST['estornar'])) { 
		
		//... e se conseguiu inserir um transporte e salvar o estoque
		$idIncluido = $transporte->InserirTransporte($unidadeDeSaudeDestino_id, $unidadeDeSaudeOrigem_id, 
		                                   $vacina_id, $quantidade, $dataDeTransporte, $lote, 
		                                   $validadeDoLote, $obs, $codigoDeBarras, $produto);
		                                   
		if ($idIncluido && $unidade->SalvarEstoque($unidadeDeSaudeDestino_id, $vacina_id, $quantidade, $codigoDeBarras, $produto)) {
			
			//buscar o nome da Unidade de Saúde
			$nome_unidade = $unidade->RetornarNomeUnidade($unidadeDeSaudeDestino_id);

                        // Captura o que o método exibe para a variável $botaoEstorno:
                        ob_start();
                        $unidade->ExibirBotaoEstornarEstoque($idIncluido);
                        $botaoEstorno = ob_get_contents(); 
                        ob_clean();

                        $botaoConfirmar = "<button name='estornar' type='submit' value='estornar'
                                                          style='color: #14E; width: 130px; margin:10px'
                                                          onclick=\"document.getElementById('containerDeMensagem').style.visibility = 'hidden';\">
                                                          <img src='{$unidade->LocalizarArquivoGeradorDeIcone()}?imagem=ok' alt='Sim'
                                                           style='vertical-align: middle' />Sim</button>";

			// exibe msg de confirmação
            $nomeVacina = $unidade->RetornarNome($vacina_id, 'vacina');
			$unidade->ExibirMensagem("Serão acrescentadas $quantidade dose(s) da vacina $nomeVacina para
				a unidade de saúde ". Html::FormatarMaiusculasMinusculas($nome_unidade) . 
				". Deseja mesmo efetuar essa operação? <br />".
                       $botaoConfirmar.$botaoEstorno, 'Atenção', 'onclick'); 
		}
	}
	
	
	// Se clicou no botão estornar:
	elseif( isset($_POST['estornar']) ) {
		
                // Verifica se o estoque recém acrescentado já não foi usado para vacinar.
                // Só estorna quando o estoque recém inserido não foi usado:
                if($transporte->RetornarEstoque($unidadeDeSaudeDestino_id, $vacina_id) >= $quantidade) {

                    if($transporte->ExcluirTransporte($idIncluidoExtornar)

                       //ação contrária do inserir transporte.será devolvido os valores iniciais.
                       && $unidade->SalvarEstoque($unidadeDeSaudeOrigem_id, $vacina_id, $quantidade, $codigoDeBarras, $produto)

                       && $unidade->EstornarEstoque($unidadeDeSaudeDestino_id, $vacina_id, $quantidade)) {

                            $msg_estorno = "Foram removidas $quantidade doses desta "
                                                     . "unidade de saúde.";

                            $_POST = array();

                            // Recarrega a página para que evite o F5 do usuário (que poderia
                            // "re-estornar" o estoque, gerando um número inteiro extremamente
                            // grande no banco de dados:
                            echo "<script>
                                            alert('$msg_estorno');
                                            window.location = '{$_SERVER['REQUEST_URI']}';
                                      </script>";
                    }
                    else {

                         $unidade->ExibirMensagem('Estoque não pode ser estornado!');
                    }
                }
		else {

                    $unidade->ExibirMensagem('Estoque já foi usado para vacinação e não poderá ser estornado!');
		}
	}
	else {
		
		$unidade->ExibirMensagem('Estoque não foi atualizado.');
	}

}

$unidade->ExibirMensagensDeErro();
$transporte->ExibirMensagensDeErro();