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
$navegacao = Navegacao::ListarPessoa();
$pessoa = new PessoaVacinavel();

$pessoa->UsarBaseDeDados();

$crip = new Criptografia();

parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));

echo '<h3 align="center">Buscar</h3>';

//$existeAlguem = false; // para a busca
$listaToda = false; // para a busca

if ( ($pessoa->VerificarSeEmitiuFormulario()) //&&
	//$pessoa->ValidarFormulario('pesquisarPessoa'))
	|| $navegacao) {
			
	if ($pessoa->VerificarSeEmitiuFormulario()) {
		
		$pesquisa = $_POST['pesquisa'];
		
		if (isset($_POST['estado'], $_POST['cidade']) && ($_POST['cidade'] > 0)) {
			$cidade_id = $_POST['cidade'];
		}
		
		else {
			$cidade_id = $_SESSION['cidade_id'];
		}

		if(isset($navegacao['cpf'])){
			$cpf = $navegacao['cpf'];

			//Se o POST existir e for vazio atribui vazio ao cpf
			if(isset($_POST['cpf']) && strlen($_POST['cpf']) == 0) $cpf = 'vazio';

		}
		else $cpf = 'vazio';
		
		if(isset($navegacao['ultimaAba'])){

            $ultimaAba = $navegacao['ultimaAba'];
		}
		else $ultimaAba = 'aba1';
		
		if(isset($navegacao['datadenasc'])){
			
			$nasc = $navegacao['datadenasc'];
			
			//Se o POST existir e for vazio atribui vazio ao nascimento
			if(isset($_POST['datadenasc']) && strlen($_POST['datadenasc']) == 0) $nasc = 'vazio';
		
							
		}
		
		else $nasc = 'vazio';
		
		if(isset($navegacao['mae'])){
			
			$mae = $navegacao['mae'];
			
			//Se o POST existir e for vazio atribui vazio a mae
			if(isset($_POST['mae']) && strlen($_POST['mae']) < 3) $mae = '';				
		}
		else $mae = '';
		
		// Se estiver fornecido a data de nascimento corretamente, atribui a $nasc:
		if(isset($_POST['datadenasc']) && strlen($_POST['datadenasc']) == 10){				
						
			$data = new Data();
			$nasc = $data->InverterData($_POST['datadenasc']);
						
		}

		// Se estiver fornecido o CPF corretamente, ent�o atribui o a $cpf
		if(isset($_POST['cpf']) && strlen($_POST['cpf']) == 14){

			$cpf = Preparacao::RemoverSimbolos($_POST['cpf']);
		}

		// Grava qual a �ltima aba que o usu�rio estava e ent�o inicia nela:
		if( isset($_POST['ultimaAba']) ){

			$ultimaAba = $_POST['ultimaAba'];
		}
        else {
            $ultimaAba = 'aba1';
        }
		
		// Se estiver fornecido o nome da m�e corretamente, ent�o atribui o a $mae
		if(isset($_POST['mae']) /*&& strlen($_POST['mae']) > 2*/ ){

			$mae = $_POST['mae'];
		}
		
		Navegacao::GravarDadosListarPessoa($cidade_id, $pesquisa, $mae,
														$cpf, $ultimaAba, $nasc);
		
	}
	else {
		
		$pesquisa  = $navegacao['pesquisa'];
		$mae	   = $navegacao['mae'];
		$cpf	   = $navegacao['cpf'];
		$ultimaAba = $navegacao['ultimaAba'];
		$cidade_id = $navegacao['cidade_id'];
		$nasc	   = $navegacao['datadenasc'];
		
	}
	ob_start();
	// retorna true ou false pra exibir ou nao a legenda
	   // ???????? provisorio, s� feito para depois trocar pelo array:
   if( !isset($_POST['ultimaAba'])  && isset($navegacao['ultimaAba']) && $navegacao['ultimaAba'] == 'aba2') {

        Depurador::Pre('a �ltima visita foi a aba 2! n�o guardar a consulta!');
   }
   else {

	 if((isset($_POST['ultimaAba']) && $_POST['ultimaAba']  == 'aba1')
	    || ( isset($navegacao) && count($navegacao) && (isset($_POST['ultimaAba'])
			&& $_POST['ultimaAba']  != 'aba2') || !isset($_POST['ultimaAba']) ))
	 $existeAlguem = $pessoa->ListarPessoa($pesquisa, $mae, $cpf, $cidade_id, $nasc);

   // Depurador::Print_r($navegacao);
  //  Depurador::Print_r($_POST);
    //--------------------------------------------------------------------------
   // print_r($_POST);



    if(isset($_POST['ultimaAba'])   && $_POST['ultimaAba']  != 'aba1') {
        
        if(   ( isset($_POST['cidade_avancado'])         && $_POST['cidade_avancado']         ) 
           || ( isset($_POST['unidade_avancado'])        && $_POST['unidade_avancado']        )
           || ( isset($_POST['acs_avancado'])            && $_POST['acs_avancado']            ) 
		   || ( isset($_POST['faixaInicio_avancado'])    && $_POST['faixaInicio_avancado']    )
		   || ( isset($_POST['unidadeInicio_avancado'])  && $_POST['unidadeInicio_avancado']  )  
		   || ( isset($_POST['faixaFim_avancado'])       && $_POST['faixaFim_avancado']       )
		   || ( isset($_POST['unidadeFim_avancado'])     && $_POST['unidadeFim_avancado']     )
		   || ( isset($_POST['diaAniversario_avancado']) && $_POST['diaAniversario_avancado'] )
		   || ( isset($_POST['mesAniversario_avancado']) && $_POST['mesAniversario_avancado'] )
		   || ( isset($_POST['nomeParecido_avancado'])   && $_POST['nomeParecido_avancado'] )
		   || ( isset($_POST['semelhanca_avancado'])     && $_POST['semelhanca_avancado'] )
		  ) {
            
            $cidade_id               = $unidade_id        = $acs_id             = $faixaInicio_avacado     =
			$uniadeInicio_avancado   = $faixaFim_avancado = $uniadeFim_avancado = $diaAniversario_avancado =
			$mesAniversario_avancado = $sexo_avancado     = $nomeParecido_avancado = $semelhanca_avancado  = false;
            
            if( isset($_POST['cidade_avancado'])         && $_POST['cidade_avancado']    != 0 ) $cidade_id               = $_POST['cidade_avancado']; 
            if( isset($_POST['unidade_avancado'])        && $_POST['unidade_avancado']   != 0 ) $unidade_id              = $_POST['unidade_avancado'];
            if( isset($_POST['acs_avancado'])            && $_POST['acs_avancado']       != 0 ) $acs_id                  = $_POST['acs_avancado'];
			if( isset($_POST['faixaInicio_avancado'])    && $_POST['faixaInicio_avancado']    ) $faixaInicio_avacado     = $_POST['faixaInicio_avancado'];
		    if( isset($_POST['unidadeInicio_avancado'])  && $_POST['unidadeInicio_avancado']  ) $uniadeInicio_avancado   = $_POST['unidadeInicio_avancado'];
		    if( isset($_POST['faixaFim_avancado'])       && $_POST['faixaFim_avancado']       ) $faixaFim_avancado       = $_POST['faixaFim_avancado'];
		    if( isset($_POST['unidadeFim_avancado'])     && $_POST['unidadeFim_avancado']     ) $uniadeFim_avancado      = $_POST['unidadeFim_avancado'];
		    if( isset($_POST['diaAniversario_avancado']) && $_POST['diaAniversario_avancado'] ) $diaAniversario_avancado = $_POST['diaAniversario_avancado'];
		    if( isset($_POST['mesAniversario_avancado']) && $_POST['mesAniversario_avancado'] ) $mesAniversario_avancado = $_POST['mesAniversario_avancado'];
		    if( isset($_POST['nomeParecido_avancado'])   && $_POST['nomeParecido_avancado']   ) $nomeParecido_avancado   = $_POST['nomeParecido_avancado'];
		    if( isset($_POST['semelhanca_avancado'])     && $_POST['semelhanca_avancado'] != 0) $semelhanca_avancado     = $_POST['semelhanca_avancado'];

            $sexo_avancado = $_POST['sexo_avancado'];
            
          $existeAlguem =  $pessoa->BuscaAvancada($cidade_id,
								   $unidade_id,
								   $acs_id,
								   $faixaInicio_avacado,
								   $uniadeInicio_avancado,
								   $faixaFim_avancado,
								   $uniadeFim_avancado,
								   $diaAniversario_avancado,
								   $mesAniversario_avancado,
                                   $sexo_avancado,
                                   $nomeParecido_avancado,
                                   $semelhanca_avancado);
            
           }
           
    }
   }
    //--------------------------------------------------------------------------
    
	$listaToda = ob_get_contents();
	ob_clean();
	
	$icones[] = array('#AAA',    'Indiv�duo inabilitado para vacina��o');
	$icones[] = array('editar',  'Alterar cadastro do indiv�duo');
	if(Sessao::Permissao('INDIVIDUOS_EXCLUIR')) $icones[] = array('excluir', 'Excluir indiv�duo do sistema');
	
	$legenda = new Legenda($icones);

}

$pessoa->ExibirFormularioListarPessoa();

if(isset($legenda) && $legenda instanceof Legenda
	&& strlen($listaToda) > 0) {
    
    //Html::ExibirInformacoesDeRegistrosEncontrados($existeAlguem);
    
	if(!isset($_POST)) echo '<p>Busca realizada anteriormente:</p>';
	echo '<div id="listagem">', $listaToda, '</div>';
      
    $legenda->ExibirLegenda();  
}

if( strlen($listaToda) == 0 && (isset($_POST['pesquisa']) || isset($navegacao['pesquisa']))) {


    //=======

    if($nasc == 'vazio') $nasc = '';
    if($cpf  == 'vazio') $cpf = '';
    if($mae  == 'vazio') $mae = '';

    $pagina = $crip->Cifrar("pagina=Adm/inserirPessoa&dados[]="
				. "$pesquisa&dados[]=$mae&dados[]="
				. "$nasc&dados[]=$cpf&irPara=listarPessoa");

        $dadosParaConfirm = '<br /><br />Nome n�o informado';
            if( strlen($pesquisa) > 2) $dadosParaConfirm = '<br /><br />Nome: ' . $pesquisa;
            if( strlen($nasc) > 2) $dadosParaConfirm .= '<br />Nascimento: ' . $nasc;
            if( strlen($mae) > 2) $dadosParaConfirm .= ';<br />M�e: ' . $mae;
            if( strlen($cpf) > 2) $dadosParaConfirm .= ';<br />CPF: ' . $cpf;

        $mensagemNovoCadastro = "Os dados $dadosParaConfirm ainda n�o
                                existem no sistema.<br /><br />Deseja fazer o cadastro do indiv�duo?";

    $novoCadastro = new PessoaVacinavel;
    $novoCadastro->ExibirConfirmacaoParaFazerNovoCadastro($mensagemNovoCadastro, $pagina);
    
    //=======


	echo "<p>A sua busca n�o obteve resultado. Verifique:<ol>
		<li>Se o nome est� digitado corretamente (tente usar apenas parte do nome);</li>
		<li>Se a data de nascimento e o CPF correspondem � sua busca;</li>
		</ol>
		<blockquote><strong>Nota:</strong> <var>Se a sua busca anterior foi
		feita em uma cidade diferente de
		{$_SESSION['cidade_nome']}/{$_SESSION['estado_id']} a busca anterior
		n�o ser� recuperada automaticamente.</blockquote></p>";
}

$pessoa->ExibirMensagensDeErro();

if(!isset($pesquisa) && !isset($id)) {
	
	echo '<div id="Dicas">Dica:<br />Voc� pode realizar suas buscas digitando o
		  primeiro nome, nome completo, sobrenome, ou apenas uma parte do nome
		  (contendo no minimo 3 caracteres). <br />Em caso de d�vida consulte
		  nosso manual ou envie um email para contato@rgweb.com.br.
		  </div>';
}
/*
		echo '<pre>';
		print_r($_POST);
		print_r($_SESSION);
		echo '</pre>';*/