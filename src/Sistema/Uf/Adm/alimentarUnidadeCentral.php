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
	
   // Verifica se os dados est�o setados... 
	if( isset($_POST['vacina'], $_POST['quantidade']) && empty($_POST['estornar'])) { 
		
		//... e se conseguiu inserir um transporte e salvar o estoque
		$idIncluido = $transporte->InserirTransporte($unidadeDeSaudeDestino_id, $unidadeDeSaudeOrigem_id, 
		                                   $vacina_id, $quantidade, $dataDeTransporte, $lote, 
		                                   $validadeDoLote, $obs, $codigoDeBarras, $produto);
		                                   
		if ($idIncluido && $unidade->SalvarEstoque($unidadeDeSaudeDestino_id, $vacina_id, $quantidade, $codigoDeBarras, $produto)) {
			
			//buscar o nome da Unidade de Sa�de
			$nome_unidade = $unidade->RetornarNomeUnidade($unidadeDeSaudeDestino_id);

                        // Captura o que o m�todo exibe para a vari�vel $botaoEstorno:
                        ob_start();
                        $unidade->ExibirBotaoEstornarEstoque($idIncluido);
                        $botaoEstorno = ob_get_contents(); 
                        ob_clean();

                        $botaoConfirmar = "<button name='estornar' type='submit' value='estornar'
                                                          style='color: #14E; width: 130px; margin:10px'
                                                          onclick=\"document.getElementById('containerDeMensagem').style.visibility = 'hidden';\">
                                                          <img src='{$unidade->LocalizarArquivoGeradorDeIcone()}?imagem=ok' alt='Sim'
                                                           style='vertical-align: middle' />Sim</button>";

			// exibe msg de confirma��o
            $nomeVacina = $unidade->RetornarNome($vacina_id, 'vacina');
			$unidade->ExibirMensagem("Ser�o acrescentadas $quantidade dose(s) da vacina $nomeVacina para
				a unidade de sa�de ". Html::FormatarMaiusculasMinusculas($nome_unidade) . 
				". Deseja mesmo efetuar essa opera��o? <br />".
                       $botaoConfirmar.$botaoEstorno, 'Aten��o', 'onclick'); 
		}
	}
	
	
	// Se clicou no bot�o estornar:
	elseif( isset($_POST['estornar']) ) {
		
                // Verifica se o estoque rec�m acrescentado j� n�o foi usado para vacinar.
                // S� estorna quando o estoque rec�m inserido n�o foi usado:
                if($transporte->RetornarEstoque($unidadeDeSaudeDestino_id, $vacina_id) >= $quantidade) {

                    if($transporte->ExcluirTransporte($idIncluidoExtornar)

                       //a��o contr�ria do inserir transporte.ser� devolvido os valores iniciais.
                       && $unidade->SalvarEstoque($unidadeDeSaudeOrigem_id, $vacina_id, $quantidade, $codigoDeBarras, $produto)

                       && $unidade->EstornarEstoque($unidadeDeSaudeDestino_id, $vacina_id, $quantidade)) {

                            $msg_estorno = "Foram removidas $quantidade doses desta "
                                                     . "unidade de sa�de.";

                            $_POST = array();

                            // Recarrega a p�gina para que evite o F5 do usu�rio (que poderia
                            // "re-estornar" o estoque, gerando um n�mero inteiro extremamente
                            // grande no banco de dados:
                            echo "<script>
                                            alert('$msg_estorno');
                                            window.location = '{$_SERVER['REQUEST_URI']}';
                                      </script>";
                    }
                    else {

                         $unidade->ExibirMensagem('Estoque n�o pode ser estornado!');
                    }
                }
		else {

                    $unidade->ExibirMensagem('Estoque j� foi usado para vacina��o e n�o poder� ser estornado!');
		}
	}
	else {
		
		$unidade->ExibirMensagem('Estoque n�o foi atualizado.');
	}

}

$unidade->ExibirMensagensDeErro();
$transporte->ExibirMensagensDeErro();