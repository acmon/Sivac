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



require_once './autoload.php';
Sessao::Singleton()->ChecarAcessoBanco();

parse_str($_SERVER['QUERY_STRING']); // Cria a variável $ajax

//------------------------------------------------------------------------------
if( isset ($ajax) ) {

	$aj = new Ajax();

	switch ($ajax) {
		case 'adicionarDoses':
			$aj->AdicionarDoses();
			break;

		case 'pesquisarCidades':
			$aj->PesquisarCidades();
			break;
		
		case 'pesquisarAcs':
			$aj->PesquisarAcs();
			break;
			
		// Esta função foi necessária pois não foi possível usar sequencialmente
		// PesquisarCidades(); PesquisarAcs();, por causa do Ajax ser assíncrono.
		// Assim, tivemos de retornar o resultado todo ao mesmo tempo e depois dividir
		// ainda mais. 	
		case 'pesquisarCidadesEAcs':
			$aj->PesquisarCidadesEAcs();
			break;
			
		case 'listarEtnias':
			$aj->ListarEtnias();
			break;

		case 'listarEstados':
			$aj->ListarEstados();
			break;

		case 'listarVacinasDaCampanha':
			$aj->ListarVacinasDaCampanha($campanha_id);
			break;

		case 'exibirIdade':
			$aj->ExibirIdade();
			break;

		case 'exibirSexo':
			$aj->ExibirSexo();
			break;
		
		case 'pesquisarIntercorrencia':
			$aj->PesquisarIntercorrencia();
			break;

		case 'listarVacinas':
			$aj->ListarVacinas($listarDescontinuadas, $retroativo);
			break;

		case 'CarregarVacinasFilhas':
			$aj->CarregarVacinasFilhas($vacina_id);
			break;

		case 'gravarDataHoraConexao':
			$aj->GravarDataHoraConexao();
			break;
			
		case 'listarPessoa':
			
			$pesquisa = urldecode($pesquisa);
			$mae = urldecode($mae);
			$datai = urldecode($datai);
			$dataf = urldecode($dataf);
			
			$aj->ListarPessoa($pesquisa, $mae, $tipo, $datai, $dataf, $cidade, $unidade, $acs, $cpf);
			
			break;
				
		case 'exibirBuscaPorEstadoCidade':
			$aj->ExibirBuscaPorEstadoCidade();
			break;
				
		case 'pesquisarAjuda':
			$aj->PesquisarAjuda(utf8_decode($_POST['pesquisa']), $_POST['tipo']);
			break;
				
		case 'validarSenhaDoAdministrador':
			
			$clean = Preparacao::GerarArrayLimpo($_POST);
			
			$aj->ValidarSenhaDoAdministrador($_SESSION['login_adm'], $clean['senha']);
			
			break;
			
		case 'exibirCampoSenhaAdm':
			$aj->ExibirCampoSenhaAdm();
			break;
		
		case 'listarNotaDaVacina' :
			$aj->ListarNotaDaVacina($codigoDaVacina,false, $exibirLegenda);

			break;
			
		case 'pesquisarUnidades' :
			$aj->PesquisarUnidades($cidade_id, $tipoUnidade);
			break;
			
		case 'pesquisarUnidadesSemTipo' :
			$aj->PesquisarUnidadesSemTipo($cidade_id);
			break;

		case 'paginarVelho':
			$aj->PaginarVelho($sql, $bind_param_types, $bind_param_vars,
					$bind_param_vars, $bind_result, $limite_inicio, $limite_fim);
			break;
		
		case 'paginar':
			$aj->Paginar($classe, $metodo);
			break;

        case 'listarDosesDaVacina':
            $aj->ListarDosesDaVacina($codigodaVacina);
            break;

        case 'Cifrar':
            $aj->Cifrar($texto);
            break;

        case 'Decifrar':
            $aj->Decifrar($texto);
            break;

        case 'GravarNaSessao':
            
            eval('$_SESSION["'.$sessao.'"] = '.$dado.';');

            break;

        case 'GravarIntercorrenciaSelecionada':
            $aj->GravarIntercorrenciaSelecionada($intercorrencia_id);
            break;

		default:
			break;
	}
}

