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

$navegacao = Navegacao::AdicionarIntercorrencia();

$intercorrencia = new Intercorrencia();

$intercorrencia->UsarBaseDeDados();

$pesquisa = $mae = $cpf = $nasc = $intercorrencia_id = $vacina_id = $listaToda = $campanha_id = false;
$cidade_id = $_SESSION['cidade_id'];


if ( $intercorrencia->VerificarSeEmitiuFormulario() || $navegacao) {
			
	if ($intercorrencia->VerificarSeEmitiuFormulario()) {
		
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
		
		// Se estiver fornecido o CPF corretamente, então atribui o a $cpf
		if(isset($_POST['cpf']) && strlen($_POST['cpf']) == 14){

			$cpf = Preparacao::RemoverSimbolos($_POST['cpf']);
		}
		
		// Se estiver fornecido o nome da mãe corretamente, então atribui o a $mae
		if(isset($_POST['mae']) && strlen($_POST['mae']) > 2 ){

			$mae = $_POST['mae'];
		}
                
                if(isset($_POST['intercorrencia_id']) && $_POST['intercorrencia_id'] != 0) $intercorrencia_id = $_POST['intercorrencia_id'];
                
                if(isset($_POST['vacina_id']) && $_POST['vacina_id'] != 0) $vacina_id = $_POST['vacina_id'];

                if(isset($_POST['campanha_id']) && $_POST['campanha_id'] != 0) $campanha_id = $_POST['campanha_id'];
		
                Navegacao::GravarDadosAdicionarIntercorrencia($cidade_id, $pesquisa, $mae, $cpf, $nasc, $intercorrencia_id, $vacina_id, $campanha_id);
		
	}
	else {
		
		$pesquisa           = $navegacao['pesquisa'];
		$mae	            = $navegacao['mae'];
		$cpf	            = $navegacao['cpf'];
		$cidade_id          = $navegacao['cidade_id'];
		$nasc	            = $navegacao['datadenasc'];
        $intercorrencia_id  = $navegacao['intercorrencia'];
        $vacina_id          = $navegacao['vacina_id'];
        $campanha_id        = $navegacao['campanha_id'];

		
	}
	ob_start();
	// retorna true ou false pra exibir ou nao a legenda
	
	$existeAlguem = $intercorrencia->ListarPessoa($pesquisa, $mae, $cpf, $cidade_id, $nasc, $intercorrencia_id, $vacina_id, $campanha_id);
	$listaToda = ob_get_contents();
	ob_clean();
}


$intercorrencia->ExibirFormularioBuscarPessoaAdicionarIntercorrencia($cidade_id,
                     $pesquisa, $mae, $cpf, $nasc, $vacina_id, $intercorrencia_id, $campanha_id);

if( $navegacao && !isset($_POST['intercorrencia_id']) && $existeAlguem) echo '<p>Busca realizada anteriormente:</p>';

echo $listaToda;

//$qs = $crip->cifrar('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioEventosAdversos');

//echo "<h4 align='center'><a href='?$qs'>Listar usuários com eventos adversos ocorridos</a></h4>";

$intercorrencia->ExibirMensagensDeErro();

