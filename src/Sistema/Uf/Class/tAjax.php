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



/**
 * Ajax: Classe que em conjunto com o javascript, monta o ajax.
 *
 * Esta classe prepara a saída para a exibição de um conjunto de dados que será
 * exibido de forma dinâmica, de acordo com a chamada no javascript. O método
 * específico é chamado de acordo com o arquivo ajax.php, que deverá instanciar
 * um objeto desta classe, de acordo com o que foi solicitado via GET, no
 * arquivo ajax.js.
 *
 * @package Sivac/Class
 *
 * @author Douglas, v 1.0, 2008-10-27 16:58
 *
 * @copyright 2008 
 *
 */
/*
require_once('./tVacina.php');
require_once('./tUnidadeDeSaude.php');
require_once('./tAjax.php');
*/
class Ajax
{
	//--------------------------------------------------------------------------
	public function __construct()
	{
		header('Content-Type: text/html;  charset=ISO-8859-1', true);
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe vacina e chama o método adicionar doses
	 *
	 */
	public function AdicionarDoses()
	{
		$vacina = new Vacina();
		$vacina->AdicionarDoses();
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o método
	 * listar etnias
	 *
	 */
	public function ListarEtnias()
	{
		$vacina = new Vacina();
		$vacina->UsarBaseDeDados();
		$vacina->ListarEtnias();
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o método
	 * listar estados
	 *
	 */
	public function ListarEstados()
	{
		$vacina = new Vacina();
		$vacina->UsarBaseDeDados();
		$vacina->ListarEstados();
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o método
	 * listar vacinas
	 *
	 */
	public function ListarVacinas($listarDescontinuadas, $retroativo)
	{
		$vacina = new Vacina();
		$vacina->UsarBaseDeDados();

		echo '<div class="CadastroEsq">Vacina: </div><div class="CadastroDir">';

		$vacina->ListarVacinas($listarDescontinuadas, $retroativo, true, false);

		echo '</div>';

      //  echo "<script> CarregarVacinasFilhas('vacinasFilhas'); </script>";
	}
	//--------------------------------------------------------------------------
	/**
	 *
	 */
	public function CarregarVacinasFilhas($vacina_id)
	{
        $vacina = new Vacina();
        $vacina->UsarBaseDeDados();
        
        $consulta = $vacina->CarregarVacinasFilhas($vacina_id);
        if($consulta) {
        echo '<div class="CadastroEsq">Tipo de Aplicação: </div><div class="CadastroDir">';
           echo $consulta;
        echo '</div>';
        }
            
        
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o método
	 * listar vacinas
	 *
	 */
	public function ListarVacinasDaCampanha($campanha_id)
	{
		$intercorrencia = new Intercorrencia();
		$intercorrencia->UsarBaseDeDados();
		
		$intercorrencia->SelectVacinasDaCampanha($campanha_id);
	}
	//--------------------------------------------------------------------------
	/**
	 * Instancia a classe Administrador e exibe o formulário se a senha digitada
	 * estiver correta
	 *
	 * @param string $login
	 * @param string $senha
	 */
	public function ValidarSenhaDoAdministrador($login, $senha)
	{
		$adm = new Administrador();
		$adm->UsarBaseDeDados();
		$administradorId = $adm->IdDoAdministradorAtual();
		
		if(  $adm->AutenticarAdministrador($login, $senha)  ) {
			
			$adm->ExibirFormularioEditarAdministrador($administradorId);	
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe relatorio, conecta na base de dados e chama o método
	 * listar pessoa
	 *
	 */
	public function ListarPessoa($pesquisa = false, $mae = false, $tipoRelatorio = false,
		$datai = false, $dataf = false, $cidade = false, $unidade = false,
		$acs = false, $cpf = false)
	{
		if(($pesquisa || $mae) && $tipoRelatorio) {
		
			$relatorio = new Relatorio();
			$relatorio->UsarBaseDeDados();
			$relatorio->ListarPessoa($pesquisa, $mae, $tipoRelatorio, $datai, $dataf,
										$cidade, $unidade, $acs, $cpf);
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o método
	 * pesquisar cidades do estado escolhido
	 *
	 */
	public function PesquisarCidades()
	{
		$us = new UnidadeDeSaude();
		$us->UsarBaseDeDados();
		$us->PesquisarCidades();
		
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe pesspa, conecta na base de dados e chama o método
	 * pesquisar unidades da cidade escolhida
	 *
	 */
	public function PesquisarAcs()
	{
		$us = new Vacina();
		$us->UsarBaseDeDados();
		$us->PesquisarAcs();
	}
	//--------------------------------------------------------------------------
	/**
	 * Esta função foi necessária pois não foi possível usar sequencialmente
	 * PesquisarCidades(); PesquisarAcs();, por causa do Ajax ser assíncrono.
	 * Assim, tivemos de retornar o resultado todo ao mesmo tempo e depois dividir
	 * ainda mais.
	 */
	public function PesquisarCidadesEAcs()
	{
		$us = new Vacina();
		$us->UsarBaseDeDados();
		$us->PesquisarCidadesEAcs();
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o método
	 * pesquisar intercorrencia da vacina escolhida.
	 *
	 */
	public function PesquisarIntercorrencia()
	{
		$vacina = new Vacina();
		$vacina->UsarBaseDeDados();
		$vacina->PesquisarIntercorrencia();
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe vacina e chama o método exibir idade
	 *
	 */
	public function ExibirIdade()
	{
		$vacina = new Vacina();
		$vacina->ExibirIdade();
	}
	//--------------------------------------------------------------------------
	public function ExibirSexo()
	{
		$vacina = new Vacina();
		$vacina->ExibirSexo();
	}
	//--------------------------------------------------------------------------
	public function ExibirBuscaPorEstadoCidade()
	{
		$pessoa = new PessoaVacinavel();
		$pessoa->UsarBaseDeDados();
		$pessoa->ExibirBuscaPorEstadoCidade();
	}
	//--------------------------------------------------------------------------
	public function PesquisarAjuda($pesquisa, $tipo)
	{
		$ajuda = new Ajuda();
		$ajuda->UsarBaseDeDados();
		$ajuda->PesquisarAjuda($pesquisa, $tipo);
	}
	//--------------------------------------------------------------------------
	public function ListarNotaDaVacina( $valor ,$leitura, $exibirLegenda)
	{
		$nota = new Nota();
		$nota->UsarBaseDeDados();
		$nota->ListarNotaDaVacina( $valor ,$leitura, $exibirLegenda );
	}
	//--------------------------------------------------------------------------
	public function ExibirCampoSenhaAdm()
	{
		$adm = new Administrador();
		$adm->ExibirConfirmarSenha('hidden');
	}
	//--------------------------------------------------------------------------
	public function GravarDataHoraConexao()
	{
		Sessao::Singleton()->GravarDataHoraConexao();
	}
	//--------------------------------------------------------------------------
	public function PesquisarUnidades($cidade_id, $tipoUnidade)
	{
		$unidade = new UnidadeDeSaude();
		$unidade->UsarBaseDeDados();
		$unidade->PesquisarUnidades($cidade_id, $tipoUnidade);

	}
	//--------------------------------------------------------------------------
	public function PesquisarUnidadesSemTipo($cidade_id)
	{
		$unidade = new UnidadeDeSaude();
		$unidade->UsarBaseDeDados();
		$unidade->PesquisarUnidadesSemTipo($cidade_id);
	}
	//--------------------------------------------------------------------------
	public function PaginarVelho($sql, $bind_param_types, $bind_param_vars,
					$bind_param_vars, $bind_result, $limite_inicio, $limite_fim)
	{
		$html = new Html();
		$html->UsarBaseDeDados();
		$html->PaginarVelho($sql, $bind_param_types, $bind_param_vars,
					$bind_param_vars, $bind_result, $limite_inicio, $limite_fim);
	}
	//--------------------------------------------------------------------------
	public function Paginar($classe, $metodo)
	{
		$html = new Html();
		$html->Paginar($classe, $metodo);
	}
    //--------------------------------------------------------------------------
    public function ListarDosesDaVacina($codigodaVacina)
    {
		$vacina = new Vacina();
		$vacina->UsarBaseDeDados();
		$vacina->ListarDosesDaVacina($codigodaVacina);
    }
    //--------------------------------------------------------------------------
    public function Cifrar($texto)
    {
		$crip = new Criptografia();
        echo $crip->Cifrar($texto);
    }
    //--------------------------------------------------------------------------
    public function Decifrar($texto)
    {
		$crip = new Criptografia();
        echo $crip->Decifrar($texto);
    }
    //--------------------------------------------------------------------------
    public function GravarIntercorrenciaSelecionada($intercorrencia_id)
    {
		echo $_SESSION['detalhesIntercorrencia_id'] = $intercorrencia_id;

    }
}