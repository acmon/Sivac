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

$navegacao = Navegacao::ListarPessoasVacinaveis();

//$retroativo veio da querystring
if (isset($retroativo)) {
	echo '<h3><center>Atualizar Caderneta</center></h3>';
	
}
else echo '<h3><center>Vacinar</center></h3>';

$pessoa = new PessoaVacinavel();

$pessoa->UsarBaseDeDados();

$vacina = new Vacina();

$vacina->UsarBaseDeDados();

$pessoa->VerificarDoseAtrasada(1,1);

$msgBuscaSemResultado = '';

if ( (count($_POST) && $pessoa->ValidarFormulario('pesquisarPessoa'))
	|| $navegacao) {
		
		$exibiuLista = false;
		
		$unidade_id = $_SESSION['unidadeDeSaude_id'];
		
		if( isset($navegacao['cidade_id']) && $navegacao['cidade_id'] > 0) {
			
			$cidade_id = $navegacao['cidade_id'];	
		}
		else {
			$cidade_id  = $_SESSION['cidade_id'];
		}
		
		if( count($_POST) ) {
							
			$campanha_id = $_POST['campanha'];	
			
			$pesquisa = $_POST['pesquisa'];
			$navegacao['mae'] = $_POST['mae'];
			
			
			if (isset($navegacao['mae'])) {
				$mae = $navegacao['mae'];	

				if(isset($_POST['mae']) && strlen($_POST['mae']) < 3) $mae = 'vazio';
			}
			
			else $mae = 'vazio';
			
			//$mae = 'vazio';
			//if(isset($_POST['mae']) && strlen($_POST['mae']) >= 3) $mae = $_POST['mae'];
					
			if(isset($navegacao['cpf'])){
				$cpf = $navegacao['cpf'];
				
				//Se o POST existir e for vazio atribui vazio ao cpf
				if(isset($_POST['cpf']) && strlen($_POST['cpf']) == 0) $cpf = 'vazio';
				
			}
			else $cpf = 'vazio';
			
			if(isset($_POST['emAtraso']) && $_POST['emAtraso'] == 'on') $emAtraso = $_POST['emAtraso'];
			else $emAtraso = '';
							
			if(isset($navegacao['datadenasc'])){
				
				$nasc = $navegacao['datadenasc'];
				
				//Se o POST existir e for vazio atribui vazio ao nascimento
				if(isset($_POST['datadenasc']) && strlen($_POST['datadenasc']) == 0) $nasc = 'vazio';
			
								
			}
			else $nasc = 'vazio';			
			
			// Se estiver fornecido a data de nascimento corretamente, atribui a $nasc:
			if(isset($_POST['datadenasc']) && strlen($_POST['datadenasc']) == 10){				
							
				$data = new Data();
				$nasc = $data->InverterData($_POST['datadenasc']);
							
			}
			
			// Se estiver fornecido o CPF corretamente, então atribui o a $cpf
			if(isset($_POST['cpf']) && strlen($_POST['cpf']) == 14){
	
				$cpf = Preparacao::RemoverSimbolos($_POST['cpf']);
			}
			
			if( isset($_POST['vacina']) ) $vacina_id = $_POST['vacina'];
			else                          $vacina_id = 0;

			if( isset($_POST['vacinaFilha']) ) $vacinaFilha_id = $_POST['vacinaFilha'];
			else                               $vacinaFilha_id = 0;
			
            if( isset($campanhaDoIcone) ) $campanha_id = $campanhaDoIcone;
            
			Navegacao::GravarDadosListarPessoasVacinaveis($unidade_id,
				$cidade_id, $campanha_id, $vacina_id, $vacinaFilha_id, $pesquisa, $mae, $cpf, $nasc, $emAtraso);
		}
		else {
			
			echo '<p>Busca realizada anteriormente:</p>';
			
			$campanha_id = $navegacao['campanha'];
            if( isset($campanhaDoIcone) ) $campanha_id = $campanhaDoIcone;
			
			$vacina_id = $navegacao['vacina'];
			$pesquisa = $navegacao['pesquisa'];
			$mae = $navegacao['mae'];
			$cpf = $navegacao['cpf'];
			$nasc = $navegacao['datadenasc'];
            $vacinaFilha_id = $navegacao['VacinaFilha_id'];


		}
	
	// Impede que a saída seja exibida (temporariamente) - pois ela ficaria
	// acima do form, pqp!
	ob_start();

    if(isset($vacinaFilha_id) && $vacinaFilha_id > 0) $vacina_id = $vacinaFilha_id;

	$paginaVacinar = 'vacinar';
	echo '<div id="listaDePessoas">';
	
	// $retroativo vem da querystring
	if(isset($retroativo)) $paginaVacinar = 'vacinarRetroativo';
	
	if( $vacina_id > 0 && $campanha_id == 0 ) {


        if( isset($retroativo) || !$vacina->VacinaPertenceAoGrupo($vacina_id, 'Descontinuadas') )
		$exibiuLista = $pessoa->ExibirListaDePessoasVacinaveisSemCampanha($vacina_id,
										 $cidade_id,
										 $pesquisa,
										 $mae,
										 $cpf,
										 $nasc,
										 $paginaVacinar);

	}
	elseif( $campanha_id > 0 && !isset($retroativo) ) {
		
		$exibiuLista =  $pessoa->ExibirListaDePessoasVacinaveisPorVacina($campanha_id,
										$cidade_id,
										$pesquisa,
										$mae,
										$cpf,
										$nasc);

	}
	
	echo '</div>';
	
	// Pega a lista bufferizada
	$listaToda = ob_get_contents();
	
	// Limpa o buffer
	ob_clean();	


	if(!$exibiuLista) {
		
		if( isset($_POST['pesquisa']) ) $nomePesquisado = $_POST['pesquisa'];
		elseif( isset($navegacao['pesquisa']) ) $nomePesquisado = $navegacao['pesquisa'];
		
		if( isset($_POST['mae']) ) $maePesquisada = $_POST['mae'];
		elseif( isset($navegacao['mae']) ) $maePesquisada = $navegacao['mae'];
		if( $maePesquisada == 'vazio') $maePesquisada = '';
		
		if( isset($_POST['datadenasc']) ) $datadenascPesquisada = $_POST['datadenasc'];
		elseif( isset($navegacao['datadenasc']) ) $datadenascPesquisada = $navegacao['datadenasc'];
		if( $datadenascPesquisada == 'vazio') $datadenascPesquisada = '';
		
		if( isset($_POST['cpf']) ) $cpfPesquisado = $_POST['cpf'];
		elseif( isset($navegacao['cpf']) ) $cpfPesquisado = $navegacao['cpf'];
		if( $cpfPesquisado == 'vazio') $cpfPesquisado = '';
		
		$irPara = 'vacinar';
		if( isset($retroativo)) $irPara = 'vacinarRetroativo';
		
		$campanhaEscolhida = 0;
		
		if( (int)$campanha_id && !isset($retroativo) ) $campanhaEscolhida = $campanha_id;
		
		$vacinaEscolhida = 0;
		if( (int)$vacina_id ) $vacinaEscolhida = $vacina_id;
		
		$dadosParaConfirm = '<br /><br />Nome não informado';
		if( strlen($nomePesquisado) > 2) $dadosParaConfirm = '<br /><br />Nome: ' . $nomePesquisado;
		if( strlen($datadenascPesquisada) > 2) $dadosParaConfirm .= '<br />Nascimento: ' . $datadenascPesquisada;
		if( strlen($maePesquisada) > 2) $dadosParaConfirm .= ';<br />Mãe: ' . $maePesquisada;
		if( strlen($cpfPesquisado) > 2) $dadosParaConfirm .= ';<br />CPF: ' . $cpfPesquisado;
		
		if( isset($_POST['estado']) )$estado_id = $_POST['estado'];
		else                         $estado_id = $_SESSION['estado_id'];
		
		if( isset($_POST['cidade']) ) {
			
			$cidade_id = $_POST['cidade'];
			$cidadePesquisada = $pessoa->RetornarCampoNome('cidade', $_POST['cidade']);
		}
		else {
			
			$cidade_id = $_SESSION['cidade_id'];
			$cidadePesquisada = $_SESSION['cidade_nome'];
		}
		
		$dadosParaConfirm .= ';<br />Morador de ' . "$cidadePesquisada/$estado_id";
		$dadosParaConfirm .= '<br /><br />';
				
		
		// Pega as IDs da cidade e do estado:
				
		$msgBuscaSemResultado = "<p>A sua busca não obteve resultado. Verifique:<ol>
			<li>Se existe estoque de vacinas na sua unidade;</li>
			<li>Se o nome está digitado corretamente (tente usar apenas parte do nome);</li>
			<li>Se sua unidade possui estoque das vacinas presentes na campanha escolhida.</li>
            <li>Se as características da campanha escolhida conferem com as
                características do indivíduo procurado</li>
			</ol>
			<blockquote><strong>Nota:</strong> <var>Se a sua busca anterior foi
			feita em uma cidade diferente de
			{$_SESSION['cidade_nome']}/{$_SESSION['estado_id']} a busca anterior
			não será recuperada automaticamente.</blockquote></p>";

		$pagina = $crip->Cifrar("pagina=Adm/inserirPessoa&dados[]="
				. "$nomePesquisado&dados[]=$maePesquisada&dados[]="
				. "$datadenascPesquisada&dados[]=$cpfPesquisado&irPara=$irPara"
				. "&campanha_id=$campanhaEscolhida&vacina_id=$vacinaEscolhida"
				. "&estado_id=$estado_id&cidade_id=$cidade_id");			
		
        //echo '<h1>'. $pesquisa, $mae, $cpf, $nasc, $cidade_id;/*
        Depurador::Pre("$campanha_id - $vacina_id");

        if( ($campanha_id == 'semCampanha' &&
             !$pessoa->VerificarEstoqueDaUnidadeParaRotina($vacina_id) ) ||
            ($campanha_id > 0 && !$pessoa->VerificarEstoqueDaUnidadeParaACampanha($campanha_id)) ) {
            
                $pessoa->ExibirMensagem('Não há vacinas com estoque nesta unidade.');
        }
        elseif( $pessoa->VerificarSePessoaExiste($pesquisa, $mae, $cpf, $nasc, $cidade_id, $vacinavel = true)  == 0 ) {

                // Verifica qual era a página anterior - se foi a página de cadastro, não
                // induz ao usuário ir para o cadastro novamente:
                list(, $querystringPagAnterior) = explode('?', $_SERVER['HTTP_REFERER']);
                
                if( strpos($crip->Decifrar($querystringPagAnterior), 'pagina=Adm/inserirPessoa') === false) {
                    
                    if($campanha_id == 0 || !isset($retroativo) ){
                        
                        if( isset($retroativo) || !$vacina->VacinaPertenceAoGrupo($vacinaEscolhida,'Descontinuadas') )
                        {
                        
                            $mensagemNovoCadastro = "Os dados $dadosParaConfirm ainda não
                                existem no sistema.<br /><br />Deseja fazer o cadastro do indivíduo?";
                            
                            $pessoa->ExibirConfirmacaoParaFazerNovoCadastro($mensagemNovoCadastro, $pagina);
                        
                        }
                        
                    }
                 
            } elseif ($pessoa->VerificarEstoqueDaUnidadeParaRotina($vacina_id)
            && $campanha_id == 'semCampanha' ) {
                //echo 'rotina sem resultado';
                
                // Verifica qual era a página anterior - se foi a página de cadastro, não
                // induz ao usuário ir para o cadastro novamente:
                list(, $querystringPagAnterior) = explode('?', $_SERVER['HTTP_REFERER']);
                
                if( strpos($crip->Decifrar($querystringPagAnterior), 'pagina=Adm/inserirPessoa') === false) {
                    
                    if($campanha_id == 0 || !isset($retroativo) ){
                        
                        if( isset($retroativo) || !$vacina->VacinaPertenceAoGrupo($vacinaEscolhida,'Descontinuadas') )
                        {
                        
                            $mensagemNovoCadastro = "Os dados $dadosParaConfirm ainda não
                                existem no sistema.<br /><br />Deseja fazer o cadastro do indivíduo?";
                            
                            $pessoa->ExibirConfirmacaoParaFazerNovoCadastro($mensagemNovoCadastro, $pagina);
                        
                        }
                        
                    }
                
                }
                
            }
        }
        else {

            $pessoa->ExibirMensagem('O indivíduo procurado está cadastrado no sistema,
                   porém não se enquadra nas características desta campanha');
        }
	}
	else {

		$icones[] = array('ok', 'Indivíduo com doses em dia');
		$icones[] = array('ok_vermelho', 'Indivíduo com doses atrasadas');
		$icones[] = array('#0B0', 'Indivíduo vacinado com todas as doses');
		$icones[] = array('#00F', 'Indivíduo vacinado, mas com ciclo incompleto');
		$icones[] = array('#F00', 'Indivíduo que não tomou nenhuma dose');
		$icones[] = array('olive', 'Indivíduo que tomou todas as doses, mas não reforço(s)');
                if(isset($campanha_id) && $campanha_id > 0 ) $icones[] = array('purple', 'Indivíduo participou da campanha selecionada');

                $legenda = new Legenda($icones);
		/*if(isset($_SESSION['dosesTotaisDaCampanhaNaUnidade']))
		unset($_SESSION['dosesTotaisDaCampanhaNaUnidade']);*/
	}
}

// $exibirDescontinuadas, $retroativo
if (isset($retroativo)) $pessoa->ExibirFormularioListarCampanhasBuscarPessoa(1, 1);
else                    $pessoa->ExibirFormularioListarCampanhasBuscarPessoa();


// Exibe o que foi pego do buffer (ob_start, acima):
if(isset($listaToda)) 	{
    
    Html::ExibirInformacoesDeRegistrosEncontrados($exibiuLista);
    echo '<div id="listagem">', $listaToda, '</div>';
}

echo $msgBuscaSemResultado;

if( isset($legenda) ) $legenda->ExibirLegenda();

$pessoa->ExibirMensagensDeErro();

if( !isset($id) && !isset($pesquisa)) {

	echo '<div id="Dicas">Dica:<br />Para realizar a vacinação de um indivíduo
		digite o primeiro nome, nome completo, sobrenome, ou apenas uma parte do
		nome (contendo no minimo 3 caracteres).<br />Não se esqueça de verificar
		a data de nascimento antes da vacinação.<br />Em caso de dúvida consulte
		nosso manual ou ou envie um email para contato@rgweb.com.br.
		</div>';
}

//echo strlen($listaToda);



