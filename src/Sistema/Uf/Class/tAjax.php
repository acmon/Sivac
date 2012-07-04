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



/**
 * Ajax: Classe que em conjunto com o javascript, monta o ajax.
 *
 * Esta classe prepara a sa�da para a exibi��o de um conjunto de dados que ser�
 * exibido de forma din�mica, de acordo com a chamada no javascript. O m�todo
 * espec�fico � chamado de acordo com o arquivo ajax.php, que dever� instanciar
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
	 * Cria uma instancia da classe vacina e chama o m�todo adicionar doses
	 *
	 */
	public function AdicionarDoses()
	{
		$vacina = new Vacina();
		$vacina->AdicionarDoses();
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o m�todo
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
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o m�todo
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
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o m�todo
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
        echo '<div class="CadastroEsq">Tipo de Aplica��o: </div><div class="CadastroDir">';
           echo $consulta;
        echo '</div>';
        }
            
        
	}
	//--------------------------------------------------------------------------
	/**
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o m�todo
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
	 * Instancia a classe Administrador e exibe o formul�rio se a senha digitada
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
	 * Cria uma instancia da classe relatorio, conecta na base de dados e chama o m�todo
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
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o m�todo
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
	 * Cria uma instancia da classe pesspa, conecta na base de dados e chama o m�todo
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
	 * Esta fun��o foi necess�ria pois n�o foi poss�vel usar sequencialmente
	 * PesquisarCidades(); PesquisarAcs();, por causa do Ajax ser ass�ncrono.
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
	 * Cria uma instancia da classe vacina, conecta na base de dados e chama o m�todo
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
	 * Cria uma instancia da classe vacina e chama o m�todo exibir idade
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