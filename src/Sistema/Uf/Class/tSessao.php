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

require_once('tCriptografia.php');//SETAR AUTOLOAD ?????
/**
 * Sessao: Classe para uso de sess�o autenticada no sistema
 *
 * Classe para a manipula��o da sess�o autenticada.
 * Ap�s ter um login v�lido, o usu�rio se conecta ao sistema,
 * e de acordo com o login pessoal enviado para checagem.
 * Foi usado o padr�o Singleton, para que se tenha certeza de que a mesma sess�o
 * ser� usada com acesso global em todas as partes do aplicativo.
 *
 * @package Sivac/Class/
 *
 * @author Douglas, v 1.0, 2008-10-30 15:29
 *
 * @copyright 2008 
 *
 */
class Sessao
{
	private $url; // String com host montado no construtor
	
	const TAMANHO_MAX_LOG_ACESSOS = 500;
	private static $_singleton = NULL;
	private static $listaPermissao = NULL; //array
	private static $listaTelas = NULL;

    
	//-------------------------------------------------------------------------
	/**
	 * Construtor privado vazio: impede que uma inst�ncia dessa classe seja criada
	 * fora da mesma com o "new".
	 */
	private function __construct()
	{
		$this->url = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA;
		$permissao = array();
	}
	//-------------------------------------------------------------------------
	/**
	 * Cria uma instancia �nica de sess�o usando o Singleton.
	 *
	 * @return Instancia �nica da classe.
	 */
	public static function Singleton()
	{


        self::ChecarValidadeLicensa();

		if (self::$_singleton == NULL) {
			self::$_singleton = new self();
		}
		return self::$_singleton;
	}
    /**
     *
     * Verifica a validade da criptografia gerada
     *
     */
	public static function ChecarValidadeLicensa()
	{
        if (file_exists('../../licval.php')) include  '../../licval.php';
        else include '../licval.php';;
	}
	//-------------------------------------------------------------------------
	/**
	 * Fun��o que gera um array com o endere�o das telas do sistema se n�o instanciado
	 * ainda
	 *
	 * @param null
	 * @return array
	 *
	 */
	public static function ResgatarTelas()
	{
		if( self::$listaTelas != NULL ) return self::$listaTelas;
		
		self::$listaTelas = array();
		
		self::$listaTelas[] = array('pagina=Adm/inserirUnidadeDeSaude' , 'UNIDADES_INSERIR');
		self::$listaTelas[] = array('pagina=Adm/listarUnidades' , 'UNIDADES_LISTAR');
		self::$listaTelas[] = array('pagina=Adm/estoqueDaUnidade' , 'UNIDADES_ESTOQUE');
		self::$listaTelas[] = array('pagina=Adm/alimentarUnidadeCentral' , 'UNIDADES_ESTOQUE_ALIMENTAR');
		self::$listaTelas[] = array('pagina=Adm/dispensarParaUnidadeSatelite' , 'UNIDADES_ESTOQUE_DISPENSAR');
		self::$listaTelas[] = array('pagina=Adm/retornarEstoqueParaCentral' , 'UNIDADES_ESTOQUE_RETORNAR');
		self::$listaTelas[] = array('pagina=Adm/visualizarEstoqueUnidadesEstado' , 'UNIDADES_ESTOQUE_VISUALIZAR_ESTADO');
		self::$listaTelas[] = array('pagina=Adm/visualizarEstoqueUnidadesMunicipio' , 'UNIDADES_ESTOQUE_VISUALIZAR_MUNICIPIO');
		self::$listaTelas[] = array('pagina=Adm/visualizarEstoqueUnidadesUnidade' , 'UNIDADES_ESTOQUE_VISUALIZAR_UNIDADE');
		self::$listaTelas[] = array('pagina=Adm/descartarVacinaMunicipio' , 'UNIDADES_ESTOQUE_DESCARTAR_MUNICIPIO');
		self::$listaTelas[] = array('pagina=Adm/descartarVacinaUnidade' , 'UNIDADES_ESTOQUE_DESCARTAR_UNIDADE');
		self::$listaTelas[] = array('pagina=Adm/inserirAcs' , 'ACS_INSERIR');
		self::$listaTelas[] = array('pagina=Adm/listarAcs' , 'ACS_LISTAR');
		self::$listaTelas[] = array('pagina=Adm/editarAcs' , 'ACS_EDITAR');
		self::$listaTelas[] = array('pagina=Adm/excluirAcs' , 'ACS_EXCLUIR');
		self::$listaTelas[] = array('pagina=Adm/inserirCampanha' , 'CAMPANHAS_INSERIR');
		self::$listaTelas[] = array('pagina=Adm/inserirVacinasNaCampanha' , 'CAMPANHAS_INSERIR');
		self::$listaTelas[] = array('pagina=inserirVacinasNaCampanha_listarCaracteristicaDaVacina' , 'CAMPANHAS_INSERIR');
		self::$listaTelas[] = array('pagina=Adm/editarCampanha' , 'CAMPANHAS_EDITAR');
		self::$listaTelas[] = array('pagina=editarCaracteristicaDaVacina' , 'CAMPANHAS_EDITAR');
		self::$listaTelas[] = array('pagina=editarVacinasDaCampanha_inserirCaracteristicaDaVacina' , 'CAMPANHAS_EDITAR');
		self::$listaTelas[] = array('pagina=editarVacinasDaCampanha_listarCaracteristicaDaVacin' , 'CAMPANHAS_EDITAR');
		self::$listaTelas[] = array('pagina=excluirCaracteristicaDaVacina' , 'CAMPANHAS_EXCLUIR');
		self::$listaTelas[] = array('pagina=Adm/listarCampanhas', 'CAMPANHAS_LISTAR');
		self::$listaTelas[] = array('pagina=Adm/editarInicio' , 'CONFIGURAR_TEXTOINICIAL');
		self::$listaTelas[] = array('pagina=Adm/nota' , 'CONFIGURAR_NOTA');
		self::$listaTelas[] = array('pagina=Adm/editarNota' , 'NOTAS_EDITAR');
		self::$listaTelas[] = array('pagina=Adm/excluirNota' , 'NOTAS_EXCLUIR');
		self::$listaTelas[] = array('pagina=Adm/acessos' , 'USUARIOS_ACESSOS');
		self::$listaTelas[] = array('pagina=Adm/listarPessoa' , 'INDIVIDUOS_BUSCAR');
		self::$listaTelas[] = array('pagina=Adm/editarPessoa' , 'INDIVIDUOS_ALTERAR');
		self::$listaTelas[] = array('pagina=Adm/excluirPessoa' , 'INDIVIDUOS_EXCLUIR');
		self::$listaTelas[] = array('pagina=Adm/inserirPessoa' , 'INDIVIDUOS_CADASTRAR');
		self::$listaTelas[] = array('pagina=Adm/inativarPessoa' , 'INDIVIDUOS_INATIVAR');
		self::$listaTelas[] = array('pagina=Adm/listarPessoasVacinaveis' , 'INDIVIDUOS_VACINAR');
		self::$listaTelas[] = array('pagina=Adm/vacinar' , 'INDIVIDUOS_VACINAR');
/*		self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=acs' , 'RELATORIOS_ACS');
		self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=cidade' , 'RELATORIOS_CIDADE');
		self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=individuo' , 'RELATORIOS_INDIVIDUO');
		self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=unidade' , 'RELATORIOS_UNIDADE');
		self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=campanha' , 'RELATORIOS_CAMPANHA');
		self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=intercorrencia' , 'RELATORIOS_INTERCORRENCIA');
		self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=geral' , 'RELATORIOS_CIDADE');
	*/	self::$listaTelas[] = array('pagina=Adm/adicionarIntercorrencia' , 'INDIVIDUOS_INTERCORRENCIA');
		self::$listaTelas[] = array('pagina=Adm/intercorrencias' , 'INDIVIDUOS_INTERCORRENCIA');
	/*	self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ListarUsuariosVacinadosPorAgente' , 'RELATORIOS_ACS');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=CriarCadernetaDeVacinacao' , 'RELATORIOS_INDIVIDUO');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ListarVacinasPorUsuario' , 'RELATORIOS_INDIVIDUO');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ListarUsuariosVacinadosPorCidade' , 'RELATORIOS_CIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ComVacinaPorCidade' , 'RELATORIOS_CIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ListarUsuariosNaoVacinadosPorCidade' , 'RELATORIOS_CIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=IndividuosAcamadosPorCidade' , 'RELATORIOS_CIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ListarUsuariosVacinadosPorUnidade' , 'RELATORIOS_UNIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ListarUsuariosVacinadosPorFaixaEtariaVacinaEUnidade' , 'RELATORIOS_UNIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ComVacinaPorUnidade' , 'RELATORIOS_UNIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ListarUsuariosNaoVacinadosPorUnidade' , 'RELATORIOS_UNIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=QuantidadeDeUsuariosVacinadosPorPeriodo' , 'RELATORIOS_UNIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=PessoasASeremVacinadasPorPeriodoEUnidade' , 'RELATORIOS_UNIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=PessoasASeremVacinadasPorPeriodoEUnidade' , 'RELATORIOS_UNIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=IndividuosAcamadosPorUnidade' , 'RELATORIOS_UNIDADE');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ComVacinaPorAcs' , 'RELATORIOS_ACS');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=ListarUsuariosNaoVacinadosPorAgente' , 'RELATORIOS_ACS');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=PessoasASeremVacinadasPorPeriodoEAcs' , 'RELATORIOS_ACS');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=IndividuosAcamadosPorAcs' , 'RELATORIOS_ACS');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=PessoasVacinadasPorCampanhaEUnidade' , 'RELATORIOS_CAMPANHA');
		self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=PessoasComIntercorrenciaPorCidadeEVacina' , 'RELATORIOS_INTERCORRENCIA');
*/		self::$listaTelas[] = array('pagina=listarVacinasParaCaderneta' , 'RELATORIOS_INDIVIDUO');
		self::$listaTelas[] = array('pagina=exibirRelatorioPop' , 'RELATORIOS_INDIVIDUO');
		self::$listaTelas[] = array('pagina=Adm/editarUnidadeDeSaude' , 'UNIDADES_ALTERAR');
		self::$listaTelas[] = array('pagina=detalhesVacina' , 'VACINAS_LISTAR');
		self::$listaTelas[] = array('pagina=editarVacinasDaCampanha' , 'CAMPANHAS_EDITAR');
		self::$listaTelas[] = array('pagina=Adm/excluirUnidadeDeSaude' , 'UNIDADES_EXCLUIR');

        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioRotinaVacinados'      , 'RELATORIOS_ROTINA');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioRotinaASeremVacinados', 'RELATORIOS_ROTINA');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioCampanhaVacinados'    , 'RELATORIOS_CAMPANHA');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioCampanhaNaoVacinados' , 'RELATORIOS_CAMPANHA');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioEventosAdversos'      , 'RELATORIOS_INTERCORRENCIA');
        self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=intercorrencia'                             , 'RELATORIOS_INTERCORRENCIA');
        self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=individuo'                                   , 'RELATORIOS_INTERCORRENCIA');
        self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=rotina'                                      , 'RELATORIOS_INTERCORRENCIA');
        self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=campanha'                                      , 'RELATORIOS_INTERCORRENCIA');
        self::$listaTelas[] = array('pagina=Rel/listarRelatorios&subtipo=gera'                                        , 'RELATORIOS_INTERCORRENCIA');
        self::$listaTelas[] = array('pagina=exibirObs'                                        , 'RELATORIOS_INTERCORRENCIA');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=RelatorioCadernetaDeVacinacao' , 'RELATORIOS_INDIVIDUO');
/*
 *      self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=PessoasASeremVacinadasPorPeriodoECidade', 'RELATORIOS_CIDADE');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=VacinadosPorCampanhaEUnidade' , 'RELATORIOS_UNIDADE');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=QuantidadeDeVacinadosPorCampanhaEUnidade' , 'RELATORIOS_UNIDADE');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=VacinadosPorCampanhaECidade' , 'RELATORIOS_CIDADE');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=QuantidadeDeVacinadosPorCampanhaEAcs' , 'RELATORIOS_ACS');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=VacinadosPorCampanhaEAcs' , 'RELATORIOS_ACS');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=QuantidadeDeVacinadosPorCampanhaECidade' , 'RELATORIOS_CIDADE');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=VacinadosPorCampanhaFaixaEtariaECidade' , 'RELATORIOS_CIDADE');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=QuantidadeDeVacinadosPorCampanhaFaixaEtariaECidade' , 'RELATORIOS_CIDADE');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=individuosNaoVacinadosPorCampanhaCidadeEFaixa' , 'RELATORIOS_CIDADE');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=individuosNaoVacinadosPorCampanhaEAcs' , 'RELATORIOS_ACS');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=individuosNaoVacinadosPorCampanhaEUnidade' , 'RELATORIOS_UNIDADE');
        self::$listaTelas[] = array('pagina=Rel/exibirFormularioEscolherRelatorio&tipo=individuosNaoVacinadosPorCampanhaECidade' , 'RELATORIOS_CIDADE');
 */     //self::$listaTelas[] = array('' , '');
		
		return self::$listaTelas;
		
	}
	//-------------------------------------------------------------------------
	/**
	 * Fun��o que defini a profundidade de acesso do usu�rio
	 * 0 significa Sem Permiss�o
	 * 1 significa Permiss�o Total
	 * 2 significa Permiss�o Parcial
	 * .
	 * .
	 * .
	 *
	 * por exemplo o usu�rio de n�vel 1 somente poder� cadastrar ACS
	 * mas somente na sua pr�pria unidade portanto ter� acesso n�mero 2
	 * quanto maior o n�mero mais restrito � o acesso.
	 *
	 * LOCALIDADE NO PLURAL + _ + OPERA��O NO SINGULAR
	 * ex.: ADMINISTRADORES_EDITAR
	 *
	 * se n�o for uma localidade, uma exibi��o, ou qualquer outra coisa
	 * utilizar o mesmo padr�o, duas palavras, a primeira no plural e a
	 * segunda no singular
	 *
	 * ATEN��O N�O � ERRO DEFINI��ES COMO "ACS_", este significa que todos ter�o
	 * pelo menos acesso m�nimo a tela ou menu.
	 * 
	 * @param null
	 * @return array
	 */
	public static function ResgatarRestricoes()
	{
		if( self::$listaPermissao != NULL ) return self::$listaPermissao;
		
		self::$listaPermissao[] = array('chave' => 'ACESSOS_MASTER',
					   '1' => '0',
					   '10' => '0',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'ACS_',
					   '1' => '1',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'ACS_EDITAR',
					   '1' => '2',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'ACS_EXCLUIR',
					   '1' => '2',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'ACS_INSERIR',
					   '1' => '2',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'ACS_LISTAR',
					   '1' => '3',
					   '10' => '2',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'ADMINISTRADORES_EDITAR',
					   '1' => '0',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'CAMPANHAS_',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'CAMPANHAS_EDITAR',
					   '1' => '0',
					   '10' => '0',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'CAMPANHAS_EXCLUIR',
					   '1' => '0',
					   '10' => '0',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'CAMPANHAS_INSERIR',
					   '1' => '0',
					   '10' => '0',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'CAMPANHAS_LISTAR',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'CONFIGURAR_',
					   '1' => '0',
					   '10' => '0',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'CONFIGURAR_NOTA',
					   '1' => '0',
					   '10' => '0',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'CONFIGURAR_TEXTOINICIAL',
					   '1' => '0',
					   '10' => '0',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'INDIVIDUOS_',
					   '1' => '1',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'INDIVIDUOS_BUSCAR',
					   '1' => '1',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'INDIVIDUOS_CADASTRAR',
					   '1' => '1',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'INDIVIDUOS_CADERNETA',
					   '1' => '1',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'INDIVIDUOS_EDITAR',
					   '1' => '1',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'INDIVIDUOS_EXCLUIR',
					   '1' => '0',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'INDIVIDUOS_INTERCORRENCIA',
					   '1' => '1',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'INDIVIDUOS_VACINAR',
					   '1' => '1',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'INDIVIDUOS_INATIVAR',
					   '1' => '0',
					   '10' => '0',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'LIBERADO',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'NOTAS_CADASTRAR',
					   '1' => '0',
					   '10' => '0',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'NOTAS_EDITAR',
					   '1' => '0',
					   '10' => '0',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'NOTAS_EXCLUIR',
					   '1' => '0',
					   '10' => '0',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'RELATORIOS_',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'RELATORIOS_ACS',
					   '1' => '2',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'RELATORIOS_ROTINA',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'RELATORIOS_CAMPANHA',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		/*self::$listaPermissao[] = array('chave' => 'RELATORIOS_CIDADE',
					   '1' => '0',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');*/
		self::$listaPermissao[] = array('chave' => 'RELATORIOS_INDIVIDUO',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'RELATORIOS_INTERCORRENCIA',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'RELATORIOS_UNIDADE',
					   '1' => '3',
					   '10' => '2',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_ALTERAR',
					   '1' => '2',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_ESTOQUE',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_ESTOQUE_ALIMENTAR',
					   '1' => '0',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_ESTOQUE_DESCARTAR_MUNICIPIO',
					   '1' => '0',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_ESTOQUE_DESCARTAR_UNIDADE',
					   '1' => '1',
					   '10' => '0',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_ESTOQUE_DISPENSAR',
					   '1' => '0',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_ESTOQUE_RETORNAR',
					   '1' => '0',
				       '10' => '1',
					   '100' => '0',
					   '1000' => '1'); 
		self::$listaPermissao[] = array('chave' => 'UNIDADES_ESTOQUE_VISUALIZAR_ESTADO',
					   '1' => '0',
					   '10' => '0',
					   '100' => '1',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_ESTOQUE_VISUALIZAR_MUNICIPIO',
					   '1' => '0',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_ESTOQUE_VISUALIZAR_UNIDADE',
					   '1' => '1',
					   '10' => '0',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_EXCLUIR',
					   '1' => '0',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_INSERIR',
					   '1' => '0',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'UNIDADES_LISTAR',
					   '1' => '0',
					   '10' => '1',
					   '100' => '0',
					   '1000' => '2');
		self::$listaPermissao[] = array('chave' => 'USUARIOS_ACESSOS',
					   '1' => '0',
					   '10' => '0',
					   '100' => '0',
					   '1000' => '1');
		self::$listaPermissao[] = array('chave' => 'VACINAS_LISTAR',
					   '1' => '1',
					   '10' => '1',
					   '100' => '1',
					   '1000' => '1');
		
		return self::$listaPermissao;
		
	}
	//-------------------------------------------------------------------------
	/**
	 * Inicia a sess�o �nica que ser� usada para toda a aplica��o. Para evitar
	 * os "avisos" de sess�o j� iniciada, o session_start() s� � chamado se uma
	 * sess�o ainda n�o existir.
	 *
	 * @return void
	 */
	private function IniciarSessao()
	{
		// Inicia a sess�o somente se a sess�o j� n�o estiver iniciada:
		if( empty( $_SESSION ) || count( $_SESSION ) == 0 ) {

			// ini_set configura as op��es do php.ini para dar maior seguran�a;
			// isso ent�o deve ser setado antes de iniciar a sess�o. Como medida
			// auxiliar de seguran�a, ap�s session_start() h� tamb�m o m�todo
			// MedidasDeSeguranca() que previne determinados ataques.

			ini_set('session.use_cookies', true);

			// Faz a sess�o usar somente cookies para sua identifica��o, e n�o
			// permite que seja usada URL para se digitar a id de sess�o. Sendo
			// habilitada, esta configura��o previne ataques envolvendo passagem
			// de ids de sess�o nas URLs:
			ini_set('session.use_only_cookies', true);

			// Faz a sess�o expirar em X minutos:
			ini_set('session.cache_expire', 60 * 8);

			// Marca o cookie com a ID de sess�o para ser acess�vel apenas atr�ves
			// do protocolo HTTP. Isto significa que o cookie n�o ser� acess�vel
			// por linguagens de script, como o JavaScript. Esta defini��o pode
			// efetivamente reduzir o roubo de identidade atr�ves de ataques XSS
			// (mesmo n�o sendo suportado por todos os navegadores):
			ini_set('session.cookie_httponly', true);

			// Seta uma substring para cada referenciador (Referer) de HTTP. Se o
			// referenciador foi enviado pelo cliente e a sustring n�o foi
			// encontrada, a id de sess�o embutida ser� marcada como inv�lida.
			//ini_set('session.referer_check', $this->url);?????
			//N�O FUNCIONANDO COM DIRET�RIOS DIFERENTES ???????
			//USAR UF OU N�O

			session_start();
		}

	}
	//--------------------------------------------------------------------------
	/**
	 * Cria medidas de seguran�a para que o sistema fique mais dif�cil de ser
	 * atacado por hackers.
	 *
	 * @param bool $pararNavegacao Muito usado em popups para n�o redirecionar o
	 * usu�rio, mas bloque�-lo e encerrar a navega��o.
	 *
	 * @return void
	 */
	private function MedidasDeSeguranca($pararNavegacao = false)
	{
		// As medidas de seguran�a devem ser chamadas a cada checagem de login.

		// Protege contra os ataques de fixa��o de sess�o (session fixation)
		// A cada mudan�a de p�gina, o cookie aonde a id de sess�o � armazenada
		// muda de valor:
		if( !headers_sent() ) session_regenerate_id();

		// Gera um chave aleat�rio para navega��o segura entre as p�ginas:

		/*if ( empty($_SESSION['chave']) ) {
			$_SESSION['chave']  = uniqid();
		}*/


		// echo $_SESSION['chave']; // Chave �nica da sess�o: varia de tamanho.

		// Verifica se o atacante tentou acessar a sess�o digitando a id
		// na url tentando set�-la ao phpsessid. Se foi, a sess�o � destru�da:

		if(strpos(strtolower($_SERVER['REQUEST_URI']), 'phpsessid') !== false) {

			$_SESSION = Array();
			session_destroy();

			if($pararNavegacao) $this->MensagemEBloqueio();

			else $this->Direcionar();
		}


		// Protege contra os ataques de seq�estro de sess�o (session hijacking)
		// Obriga o atacante a usar n�o apenas uma identifica��o de sess�o
		// v�lida, mas tamb�m o user-agent correto. Isto complica um pouco a
		// vida do atacante e deixa a sess�o um pouco mais segura. Al�m disso,
		// se o atacante descobrir o HTTP_USER_AGENT, ele estar� cifrado:

		if (    empty($_SESSION['user_agent'])   ||
			($_SESSION['user_agent']
			!== hash('md5', $_SERVER['HTTP_USER_AGENT']))  ) {

			$_SESSION = Array();
			session_destroy();

			if($pararNavegacao) $this->MensagemEBloqueio();

			else $this->Direcionar();
		}

		//print_r($_COOKIE); // Para ver a id de sess�o armazenada no cookie
	}
	//--------------------------------------------------------------------------
	/**
	 * Se a identifica��o e senha fornecidas pelo usu�rio
	 * existir no banco de dados, o usuario se conectar� ao sistema. Esse � o
	 * n�vel mais baixo de autentica��o e ser� preciso se conectar ao sistema
	 * com uma identifica��o v�lida de usu�rio. Caso o usuario tente se logar
	 * mais de 3 vezes o mesmo � impedido de continuar tentando e � direcionado
	 * para a p�gina "Proibido".
	 *
	 * @return void
	 */
	public function Logar()
	{
		$this->IniciarSessao();
		
		//print_r($_SESSION);

		if (empty($_SESSION['tentativas']))
			$_SESSION['tentativas'] = 0;

		if(isset($_POST['login'], $_SESSION['estado_id'])
			&& $_SESSION['tentativas'] < 4) {

			$conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'], $_SESSION['senha']);
			$conexao->select_Db($_SESSION['banco']);
			
			//echo '<pre>'; print_r($_SESSION); echo '</pre>'; die;
						
			$login = $conexao->real_escape_string( trim($_POST['login']) );
			$senha = $conexao->real_escape_string( trim($_POST['senha']) );
			$estado_id = $_SESSION['estado_id'];

			$crip = new Criptografia();
			$senha = $crip->Senha($senha);

			$reg = $conexao->prepare("SELECT administrador.UnidadeDeSaude_id,
				administrador.nome, administrador.login, administrador.id, administrador.nivel,
				bairro.Cidade_id, unidadedesaude.nome AS `unidade`,
				cidade.nome AS `cidade`
				
				FROM `administrador`, `unidadedesaude`, `bairro`, `cidade`, `estado`
				
				WHERE administrador.UnidadeDeSaude_id = unidadedesaude.id
					
					AND unidadedesaude.Bairro_id = bairro.id
					AND (
						(bairro.Cidade_id = cidade.id AND cidade.Estado_id = ?)
						OR (administrador.nivel = 1000)
						)
					AND login = ?
					AND senha = ?
					AND unidadedesaude.ativo
					AND bairro.ativo
					AND administrador.ativo")
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$reg->bind_param('sss', $estado_id, $login, $senha);
			
			$reg->bind_result($unidadeDeSaude_id, $nome, $login_adm, $adm_id, $nivel,
				$cidade_id, $unidade_nome, $cidade_nome);
				
			$reg->execute();
			$reg->fetch();
			$reg->free_result();
			
			
			if($nivel == 1000) {
				
				$reg = $conexao->prepare('SELECT cidade.id, cidade.nome, 
					cidade.Estado_id
					FROM `cidade`, `unidadedesaude`, `bairro`
					WHERE unidadedesaude.Bairro_id = bairro.id
					AND bairro.Cidade_id = cidade.id
					AND unidadedesaude.ativo
					AND bairro.ativo
					AND unidadedesaude.id = ?')
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
				
				$reg->bind_param('i', $unidadeDeSaude_id);
				$reg->bind_result($cidade_id, $cidade_nome, $estado_id);
				$reg->execute();
				$reg->fetch();
				$reg->free_result();
				
				$_SESSION['estado_id'] = $estado_id;
			}
			
		}

		if ((isset($unidadeDeSaude_id) && (int)($unidadeDeSaude_id > 0)) ) {
			
			$_SESSION['user_agent']			= hash('md5', $_SERVER['HTTP_USER_AGENT']);
			$_SESSION['unidadeDeSaude_id']	= $unidadeDeSaude_id;
			$_SESSION['unidade_nome']		= $unidade_nome;
			$_SESSION['cidade_id']			= $cidade_id;
			$_SESSION['cidade_nome']		= $cidade_nome;
			$_SESSION['nome']				= $nome;
			$_SESSION['login_adm']			= $login_adm;
			$_SESSION['adm_id']			    = $adm_id;
			$_SESSION['nivel']				= $nivel;

			// Para inserir no log de conex�o:			
			$logUnidade = "[$unidadeDeSaude_id] $unidade_nome"; 
			$logCidade = "[$cidade_id] $cidade_nome/$estado_id";
			$ip = Html::IpDoUsuario();
			$navegador = Html::Navegador();
			
			$sql = "SELECT id
						FROM `usuarioconectado`
						WHERE login = '{$_SESSION['login_adm']}'
						AND conectado = 1
						ORDER BY id DESC";
								
			$reg = $conexao->prepare($sql)
			or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
			
			$idAcessoAnterior = false;
			$reg->bind_result($idAcessoAnterior);
			
			$reg->execute();
			
			$reg->store_result();
			
			while($reg->fetch()) {
				
				// Atualiza para "desconectado" o cara se a diferen�a da data do �ltimo
				// acesso � grande:
				if($idAcessoAnterior) {
					
					$sql = "SELECT MINUTE(TIMEDIFF(NOW(), datahoradesconexao)) AS `diff`
							FROM `usuarioconectado`
							WHERE id = $idAcessoAnterior
								AND conectado = 1";
								
					$linhas = $conexao->query($sql)
					or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
					
					$diff = false;
					
					$linha = $linhas->fetch_assoc();
					
					$diff = $linha['diff'];
					
					$linhas->free_result();
					
					// Se passou de 5 minutos do acesso anterior (que pode ter sido
					// deixado "conectado", atualizar o anterior para "desconectado")
					// Fazer isso antes de pegar uma nova id de conex�o.
					if($diff > 2) {
						
						static $exibiuAlert = false;
						
						if(!$exibiuAlert) {
							
							echo '<script>alert("No acesso anterior, voc� esqueceu de " +
								"desconectar do Sivac antes de fechar o " +
								"navegador.\n\nPara maior seguran�a, utilize " +
								"sempre o bot�o \"Desconectar\" para sair do " +
								"sistema")</script>';
							
							$exibiuAlert = true;
						}
						
						$sql = "UPDATE `usuarioconectado`
								SET conectado = 0
									WHERE id = $idAcessoAnterior";
									
						$conexao->query($sql)
						or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
						
					}
				}
			}
			
			$reg->free_result();
			
			$sql = 'INSERT INTO `usuarioconectado`
					VALUES(NULL, ?, ?, ?, ?, NULL, NULL, ?, ?, 1)';
								
			$reg = $conexao->prepare($sql)
			or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
			
			$reg->bind_param('sissss',
				$login_adm, $nivel, $logCidade, $logUnidade, $navegador, $ip);
			
			$reg->execute();
			
			$_SESSION['usuarioConectado_id'] = $reg->insert_id;
			
			$reg->close();
			
			// Registra quando ocorreu o primeiro acesso do usu�rio:
			$this->RegistrarPrimeiroAcesso();
			
			// Remove no log de acesso, a quantidade excedente de acessos do
			// usu�rio (setado em self::TAMANHO_MAX_LOG_ACESSOS)
			$this->LimparRegistrosExcedentes();
			
			// Se o usu�rio antes de logar tentou acessar um link, quando ele se
			// logar, ent�o vai para esse link que ele tentou anteriormente:
			if( isset($_SESSION['linkAnterior']) ) {
				
				echo "<script>
						window.location = '{$_SESSION['linkAnterior']}';
					</script>"; 
			}

			// Se ele n�o tentou, ent�o vai para a p�gina de login mesmo:
			else {
				$this->Direcionar('Uf/', 'pagina=login');
			}
		}

		if (isset($_POST['login'])) {
			$_SESSION['tentativas']++;
		}

		if($_SESSION['tentativas'] > 4) $this->Direcionar();
	}
	//--------------------------------------------------------------------------
	public function LimparRegistrosExcedentes()
	{
		$conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'], $_SESSION['senha']);
		$conexao->select_Db($_SESSION['banco']);
		
		// Verificando se o usu�rio tem mais de 500 registros no log de acessos:
		$sql = "SELECT COUNT(id) as `qtd` FROM `usuarioconectado`
			WHERE login = '{$_SESSION['login_adm']}' HAVING qtd > "
			. self::TAMANHO_MAX_LOG_ACESSOS;
			
		$stmt = $conexao->prepare($sql)
			or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
		
		$stmt->execute();
		
		$stmt->store_result();
		
		$qtd = $stmt->num_rows;
		
		$stmt->free_result();
		
		if($qtd == 1) {
			
			$sql = "DELETE FROM `usuarioconectado`
					WHERE login = '{$_SESSION['login_adm']}'
						LIMIT 1";
			
			$stmt = $conexao->prepare($sql)
				or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
				
			$stmt->execute();
		}
	}
	//--------------------------------------------------------------------------
	public function RegistrarPrimeiroAcesso()
	{
		$conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'], $_SESSION['senha']);
		$conexao->select_Db($_SESSION['banco']);
		
		// Registrar o primeiro acesso do usu�rio:
		$sql = "UPDATE `administrador` SET primeiroacesso = NOW()
				WHERE login = '{$_SESSION['login_adm']}'
					AND primeiroacesso IS NULL";
			
		$stmt = $conexao->prepare($sql)
			or die(Bd::TratarErroSql($conexao->error, __FILE__, __LINE__));
		
		$stmt->execute();
	}
	//--------------------------------------------------------------------------
	/**
	 * ChecarAcesso � um m�todo colocado no in�cio de cada script em que o
	 * usuario precisa estar logado e ter permiss�es necess�rias. Caso o mesmo n�o esteja
	 * logado ou n�o possua permiss�o, ent�o o acesso
	 * fica bloqueado para aquela p�gina e o usu�rio � redirecionado para a tela
	 * de login.
	 *
	 * OBS: se a p�gina acessado for considerada de acesso restrito e ainda n�o
	 * tiver sido citada no m�todo ResgatarTelas est� apresentar� um alerta
	 * com o endere�o a ser colocado, lembrando que s� dever� ser colocada
	 * a parte da string antes do "&".
	 *
	 * @return void
	 */
	public function ChecarAcesso()
	{
		$this->IniciarSessao();
		$this->MedidasDeSeguranca();

		if ( empty($_SESSION['unidadeDeSaude_id']) ) {

			// Para memorizar aonde o usu�rio queria ir antes de se logar:
			$_SESSION['linkAnterior'] = $_SERVER['REQUEST_URI'];
			
			// Envia o usu�rio n�o logado para a tela de login:
			$this->Direcionar('Uf/','pagina=login');
		}
		else {
			/* retirar o IF para iniciaro procedimento,
			 * colocado somente para n�o interferir no trabalho dos
			 * outros
			 */ 
				
			$cripto = new Criptografia();
			$arrTelas = self::ResgatarTelas();
			$queryString = $cripto->Decifrar($_SERVER['QUERY_STRING']);
			$contador = 0;
			
            //$uri = $_SERVER['REQUEST_URI'];
            //echo "<script>alert(\"$uri\")</script>";
            
			foreach($arrTelas as $tela)
			{
				$posicao = strpos(strtolower($queryString),strtolower($tela[0]));
				if ( $posicao !== false && $posicao == 0 ) {
					
					$contador++;
					
					if ( !Sessao::Permissao($tela[1]) ) {
						
                        $_SESSION['linkAnterior'] = $_SERVER['REQUEST_URI'];
						$this->Direcionar('Uf/','pagina=login');
						
					}
					
				}
				
			}
				
			//if($contador == 0) echo "<script>alert('$queryString - n�o citada')</script>";
		
			$this->GravarDataHoraConexao();
		}
	}
	//--------------------------------------------------------------------------
	/**
	 */
	public static function GravarDadosDeNavegacao()
	{

        if( !isset($_SESSION['usuarioConectado_id']) ) return;

        $dadosEnviadosPorPost = $dadosDaSessao = $dadosDoServer = '';

        $crip = new Criptografia();

        $paginaNavegada = str_replace('pagina=','',$crip->Decifrar($_SERVER['QUERY_STRING']));


        foreach($_SERVER as $chave => $valor)
            $dadosDoServer .= " [$chave] => (| $valor |) , ";

        foreach($_SESSION as $chave => $valor)
            if(!is_array($valor)) $dadosDaSessao .= " [$chave] => (| $valor |) , ";

        foreach($_POST as $chave => $valor)
            $dadosEnviadosPorPost .= " [$chave] => (| $valor |) , ";

        $administrador_id    = $_SESSION['adm_id'];
        $administrador_nome  = $_SESSION['nome'];

        $conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'], $_SESSION['senha']);
		$conexao->select_Db($_SESSION['banco']);

        $stmt = $conexao->prepare('INSERT INTO `dadosdenavegacao` (id, administrador_id, 
            administrador_nome, dataHora, pagina, dadosPost, dadosSession, dadosSever) VALUES
			(NULL, ?, ?, NOW(), ?, ?, ?, ?)');

			$stmt->bind_param('isssss', $administrador_id,
                                        $administrador_nome,
                                        $paginaNavegada,
                                        $dadosEnviadosPorPost,
                                        $dadosDaSessao,
                                        $dadosDoServer)

            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));


			$stmt->execute();
			$stmt->close();

	}
    //--------------------------------------------------------------------------

	public function GravarDataHoraConexao()
	{
		if( isset($_SESSION['usuarioConectado_id']) ) {
			
			// Sempre atualizar a hora de "desconex�o", pois o usu�rio poder�
			// fechar sem desconectar:
			$conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'], $_SESSION['senha']);
			$conexao->select_Db($_SESSION['banco']);
			
			$sql = "UPDATE `usuarioconectado`
					SET datahoradesconexao = NOW()
						WHERE id = {$_SESSION['usuarioConectado_id']}";
			
			$stmt = $conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
			$stmt->execute();
			$stmt->close();
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Desconectar significa desconectar tudo, pois � o n�vel mais baixo
	 * de acesso; ent�o a sess�o pode ser encerrada e destru�da. Para isso, �
	 * atribu�do um array vazio para $_SESSION (acabando com toda a sess�o via
	 * cliente) e � chamado em seguida o session_destroy() (acabando com a
	 * sess�o no servidor). Logo em seguida o usu�rio � redirecionado para a
	 * p�gina de login.
	 *
	 * @return void
	 */
	public function Desconectar()
	{
		$this->IniciarSessao();
		
        if( isset($_SESSION['usuarioConectado_id']) ) {
        
            // Sempre atualizar a hora de "desconex�o", pois o usu�rio poder�
            // fechar sem desconectar:
            $conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'], $_SESSION['senha']);
            $conexao->select_Db($_SESSION['banco']);
            
        
            $sql = "UPDATE `usuarioconectado`
                    SET conectado = 0, datahoradesconexao = NOW()
                        WHERE id = {$_SESSION['usuarioConectado_id']}";
            
            $stmt = $conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
            
            $stmt->execute();
            $stmt->close();		
            
            $_SESSION = Array();
            session_destroy();
            
        }
        
		$this->Direcionar();
	}
	//--------------------------------------------------------------------------
	/**
	 * Mensagem e bloqueio � usado quando o m�todo ChecarUsuario recebe true no
	 * seu segundo par�metro ($pararNavegacao). Quando $pararNavegacao �
	 * verdadeiro, ao inv�s do usu�rio que n�o tem pemiss�o para estar naquele
	 * local ser redirecionado para a p�gina de login, o acesso � bloqueado e
	 * aparece uma mensagem informando o usu�rio que o acesso n�o � permitido
	 * para aquele local. Se o javascript estiver habilitado (maioria das vezes)
	 * aparece um alerta e em seguida a janela � fechada. Caso contr�rio aparece
	 * uma mensagem na tela escrita pelo PHP que o usu�rio n�o tem acesso para
	 * aquele conte�do.
	 *
	 * @return void
	 */
	private function MensagemEBloqueio()
	{
		// Usa o javascript para alertar o usu�rio que ele n�o tem acesso:
		echo '<script>alert("Voc� n�o tem acesso para esse conte�do");
				window.close();</script>';

		// Se o usu�rio desabilitar javascript, o PHP bloqueia a navega��o,
		// encerrando o script:
		die("Voc� n�o tem acesso para este conte�do");
	}
	//--------------------------------------------------------------------------
	/**
	 * Direcionar � usado para enviar o usu�rio para a p�gina adequada ao caso.
	 * A p�gina adequada � parte da navega��o e do acesso ao qual o seu n�vel
	 * permite. Se o usu�rio n�o tem permiss�o para determinada �rea, ent�o ele
	 * � direcionado para o local
	 *
	 * @param string $local Valores v�lidos s�o Proibido ou "raiz" (valor padr�o
	 * - quando o par�metro n�o for informado).
	 * @param string|null $querystring String que vem depois do "?" para informar
	 * a consulta para a p�gina. Todas as querystrings usadas no Simac ser�o
	 * cifradas com um mecanismo pr�prio de criptografia, criado pela empresa.
	 */
	private function Direcionar($local = 'raiz', $querystring = null)
	{
		if ($local === 'raiz') {
			header('Location: ' . $this->url);
		}
		elseif ($querystring) {
			
			$crip = new Criptografia();

			$qs = "/$local?{$crip->Cifrar($querystring)}";
		}
		else {
			$qs = $local;
		}
		echo '<script>window.location = "'. $this->url . $qs .'";</script>';
		//header('Location: ' . $this->url . $uri); 
		//MUDAN�A REALIZADA POR CAUSA DE ALGUM ECHO ANTERIOR
	}
	//--------------------------------------------------------------------------
	/**
	 * 
	 */	
	public function MostrarUsuario() 
	{
		if ( isset($_SESSION['unidadeDeSaude_id']) &&
		    $_SESSION['nome'] != null) {
		    
		    $nome = Html::FormatarMaiusculasMinusculas(strtok($_SESSION['nome'],' '));
			//URL = # para esconder o verdadeiro endere�o
			// e o onclick apenas com um link de um script de desconexao
			// pr� executado antes do redirecionamento
			echo "<div id='desconectar' align='right'>$nome
			- <a href='#' onclick='window.location=\"deslogar.php\"'>
			<span class='spanBranco'>Desconectar</span></a></div>";
			
			// ?????????????? - apagar depois...
			//echo "<br />cidade_id do administrador: {$_SESSION['cidade_id']}";
			//echo "<br />estado selecionado: {$_SESSION['estado_id']}";
			//echo "<br />n�vel de acesso: {$_SESSION['nivel']}<br />";
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * 
	 */	
	public function MostrarUnidade() 
	{
		if ( isset($_SESSION['unidade_nome'], $_SESSION['cidade_nome'],
			$_SESSION['estado_id']) ) {
				
			$unidade =  Html::FormatarMaiusculasMinusculas($_SESSION['unidade_nome']);
		    
			echo "<h3>$unidade - {$_SESSION['cidade_nome']}/{$_SESSION['estado_id']}";
				
			echo '</h3><hr />';
		}
	}
	//--------------------------------------------------------------------------
	public function GravarSessaoBanco($local, $banco, $login, $senha,
		$estado_id, $estado_nome)
	{
		$this->IniciarSessao();
		
		// Antes de logar a pessoa, destruir a sess�o, pois ele pode estar
		// logado em um outro estado, num outro banco e aproveitar a sess�o.
		// Destruindo a sess�o antes evita esse problema:
		$_SESSION = Array();
		session_destroy();
		
		$this->IniciarSessao();
		
		$_SESSION['local'] = $local;
		$_SESSION['banco'] = $banco;
		$_SESSION['login'] = $login;
		$_SESSION['senha'] = $senha;
		$_SESSION['estado_id'] = $estado_id;
		$_SESSION['estado_banco'] = $estado_id;
		$_SESSION['estado_banco_nome'] = $estado_nome;
		$_SESSION['tentativas'] = 0;
		
		//echo "<br />Na classe Sessao: \$local=$local, \$banco=$banco, \$login=$login, \$senha=$senha, \$estado_id=$estado_id";
		
		$_SESSION['user_agent'] = hash('md5', $_SERVER['HTTP_USER_AGENT'] );                      
		
	}
	//--------------------------------------------------------------------------
	public function ChecarAcessoBanco()
	{
		$this->IniciarSessao();
		$this->MedidasDeSeguranca();
        $this->GravarDadosDeNavegacao();

		if ( empty($_SESSION['local']) || empty($_SESSION['banco']) ) {
			
			$this->Direcionar('../');
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Verifica quantas tentativas o usu�rio j� fez. S� podem existir 3 tentativas 
	 * caso contrario o usu�rio s� poder� tentar acessar o sistema novamente se 
	 * fechar e abrir o navegador
	 *
	 */
	public function VerificaTentativas()
	{	
		if(isset($_SESSION['tentativas'])) {
			
			$vezes = $_SESSION['tentativas']; // Tentativas 
		
			echo '<div align="center">';
			
			switch ($vezes) {
				
				case 1: echo '<div class="mensagens">Voc� tem mais 3 tentativas 
							  para entrar!</div>'; break; //Uma tentativa 
					
				case 2: echo '<div class="mensagens">Voc� tem mais 2 tentativas 
							  para entrar!</div>'; break; //Duas tentativas
					
				case 3: echo '<div class="mensagens">Essa � a sua �ltima tentativa 
							  para se conectar!</div>'; break; //Tr�s tentivas
					
				default: echo '<div class="mensagens">Voc� n�o pode mais se conectar!
							   </div>'; break; // Mais de tr�s tentativas
			}
			echo '</div>';
			
			if($vezes > 1) {
				
				echo '<div style="width: 500px; margin: auto; text-align: justify">';
				
				echo "<fieldset><legend><em><strong>Nota:</strong></em></legend>
					<blockquote>O estado escolhido foi <strong>{$_SESSION['estado_banco_nome']}</strong>.
                   Se este n�o � o seu estado, clique <a href='../'>aqui para escolher
                   novamente</a>.</blockquote></fieldset>";
					
				echo '</div>';
			}
		}
	}
	//--------------------------------------------------------------------------
	public function ExibirInformacoesDeAcesso()
	{
		$crip = new Criptografia();
		$querystring = $crip->Cifrar("pagina=Adm/editarAdministrador&login={$_SESSION['login_adm']}");
		
		echo '<div id="informacoesDoAdministrador">';
		echo "[<a href='?$querystring'
			title='Editar administrador'>{$_SESSION['login_adm']}</a>]<br />";
		
		if($_SESSION['nivel'] == 1000) {

			echo "acesso master";
		}
		
		echo '<div style="margin-top: 5px; padding-top: 5px; border-top: 1px dotted #dde">';
		
		echo "Acessando a base: {$_SESSION['estado_banco_nome']}";
		
		echo '</div>';
		
		echo '</div>';
	}
	
	public static function Permissao($parte)
	{
		if( !isset($parte) || $parte == '' ) return false;
		$arrPermissoes = self::ResgatarRestricoes();
		foreach( $arrPermissoes as $permissoes )
		{

			if( $permissoes['chave'] == $parte ){
				
				if( isset($_SESSION['nivel']) ) {
					$nivel = $_SESSION['nivel'];
					return $permissoes[$nivel];
				} else return 0;
				
			}
			
			
		}
		
	}
}