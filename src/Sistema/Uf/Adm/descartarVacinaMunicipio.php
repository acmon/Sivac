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

$unidade->ExibirOpcaoOperacaoDeEstoque(7);

$data = new Data();


if(count($_POST)) {

        Depurador::Print_r($_POST);
	// Quando o admin for nivel 1000 ele pode escolher a unidade
	//$unidade_id = $_SESSION['unidadeDeSaude_id'];
	//if($_SESSION['nivel'] == 1000) $unidade_id = $_POST['unidade'];
		
	$vacina_id = $_POST['vacina'];
	$quantidade = $_POST['quantidade'];
	$unidade_id = $_POST['unidadeCentral'];
	$motivo_id = $_POST['motivo'];
	$lote = $_POST['lote'];
	$obs = $_POST['obs'];
	$login = $_SESSION['login_adm'];

    $codigoDeBarras = $_POST['codigoDeBarras'];
	$produto = $_POST['produto'];


	
   // Verifica se os dados estão setados... 
	if( isset($_POST['vacina'], $_POST['quantidade']) && empty($_POST['estornar'])) {
		
		if ($unidade->VerificarEstoque($quantidade, $vacina_id, $unidade_id)) {
			
			$idIncluido = $unidade->InserirDescarte($motivo_id,
                                                    $vacina_id,
                                                    $unidade_id,
                                                    $login,
                                                    $quantidade,
                                                    $lote,
                                                    $obs,
                                                    $codigoDeBarras,
                                                    $produto);
			                                   
			if ($idIncluido && $unidade->EstornarEstoque($unidade_id,
                                                         $vacina_id,
                                                         $quantidade)
                           ) {
				
				//buscar o nome da Unidade de Saúde
				$nome_unidade = $unidade->RetornarNomeUnidade($unidade_id);

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
				$unidade->ExibirMensagem("Serão descartadas $quantidade dose(s) desta vacina $nomeVacina 
                                    da unidade de saúde " . Html::FormatarMaiusculasMinusculas($nome_unidade) .
                                    ". Deseja mesmo efetuar essa operação? <br />
                                    $botaoConfirmar $botaoEstorno", 'Atenção', 'onclick');
			}
		}
		
		else {
                    $unidade->ExibirMensagem('Quantidade para descarte maior que
                                                           o estoque atual!');
		}
	}
	
	
	// Se clicou no botão estornar:
	elseif( isset($_POST['estornar']) ) {

            if ($unidade->VerificarEstoque($quantidade, $vacina_id, $unidade_id)) {

        	if($unidade->ExcluirDescarte($idIncluidoExtornar)
		    
		   //ação contrária do inserir transporte.será devolvido os valores iniciais.
		   && $unidade->SalvarEstoque($unidade_id, $vacina_id, $quantidade, $codigoDeBarras, $produto)) {

 			$msg_estorno = "Foi cancelado o descarte de $quantidade doses nesta "
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

$unidade->ExibirFormularioDescartarVacinaMunicipio();

$unidade->ExibirMensagensDeErro();
