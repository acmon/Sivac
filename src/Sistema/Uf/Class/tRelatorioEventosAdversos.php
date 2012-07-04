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
class RelatorioEventosAdversos extends Relatorio
{
//------------------------------------------------------------------------------
    /**
     * Atributos
     */
     protected static $titulo = 'Indiv�duos com eventos adversos ocorridos';

     protected static $nota = 'Este relat�rio tem por finalidade listar os indiv�duos que
                               tomaram determinada vacina e que apresentaram eventos adversos
                               ocorridos p�s-vacina��o. ';

//------------------------------------------------------------------------------

    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->url = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA;
        Depurador::Pre('Classe instanciada: ' . __CLASS__);
    }

//------------------------------------------------------------------------------

    /**
     * Retorna o t�tulo do relat�rio. Necess�rio pois na classe m�e (Relatorio)
     * � usado $this->Titulo() genericamente para retornar o t�tulo de cada
     * filha, o que n�o funcionaria se chamasse self::$titulo na classe m�e.
     *
     * @return String
     */
    protected function Titulo()
    {
        return self::$titulo;
    }

//------------------------------------------------------------------------------

    /**
     * Retorna a nota do relat�rio. Necess�rio pois na classe m�e (Relatorio)
     * � usado $this->Nota() genericamente para retornar o t�tulo de cada
     * filha, o que n�o funcionaria se chamasse self::$nota na classe m�e.
     *
     * @return String
     */
    protected function Nota()
    {
        return self::$nota;
    }

//------------------------------------------------------------------------------

    /**
     * Exibe o relat�rio desta classe.
     *
     * @param Boolean|int $pagina_atual (opcional) P�gina usada quando o
     *                    o  relat�rio est� paginado
     * @return Boolean
     */
    public function ExibirRelatorio($pagina_atual = false)
    {
        $html = new Html;
        $nomeDaSessao = 'paginacao_relatorio';
        $pagina_atual = $html->TratarPaginaAtual($pagina_atual, $nomeDaSessao);

        $aPartirDe = ($pagina_atual - 1) * Html::LIMITE;

        ////////////////////////////////////////////////////////////////////////

        $cidade_id    = $_SESSION['cidade_id'];

        $post = $this->UltimoPostParaAjax();
 
        if( isset($post['vacina_id']) )      $vacina_id             = $post['vacina_id'];
        if( isset($post['cidade_id']) )      $cidade_id             = $post['cidade_id'];
        if( isset($post['campanha_id']) )    $campanha_id           = $post['campanha_id'];

        if( isset($post['naoResidentes'])  && (Boolean)$post['naoResidentes'] == true) $naoResidentes     = $post['naoResidentes'];
        else $naoResidentes = '';

        $nomeVacina = $this->RetornarCampoNome('vacina', $vacina_id);

        ///////////////////////////////////////////////////////////////////////

        $sqlCidade = "AND usuario.Bairro_id = bairro.id
                      AND bairro.Cidade_id = cidade.id
                      AND cidade.id = $cidade_id ";

        if($naoResidentes != '') $sqlCidade = "AND usuario.Bairro_id NOT IN
                 (SELECT id FROM bairro WHERE Cidade_id = $cidade_id) ";

        if ($campanha_id != 0 && $campanha_id != 'semCampanha') {

            /*
             * SELECT DISTINCT usuario.id, usuario.nome FROM `usuariovacinadocampanha`,
             *  usuario WHERE `Campanha_id` = 9  AND `Vacina_id` = 31
             *  AND usuario.id = usuariovacinadocampanha.Usuario_id limit 100
             *
             */

             /*
            $sql = "SELECT COUNT( DISTINCT usuario.id)
                        FROM usuario , usuariointercorrenciacampanha, usuariovacinadocampanha, bairro , cidade
                            WHERE usuariovacinadocampanha.id  = usuariointercorrenciacampanha.UsuarioVacinadoCampanha_id
                                AND usuariovacinadocampanha.Usuario_id = usuario.id
                                AND usuario.Bairro_id = bairro.id
                                AND bairro.Cidade_id = cidade.id
                                AND cidade.id = ?
                                AND usuario.ativo
                                AND usuariovacinadocampanha.Vacina_id = ?
                                AND usuariovacinadocampanha.Campanha_id = ?";*/

             $sql = "SELECT COUNT( DISTINCT usuario.id)
                        FROM `usuariovacinadocampanha`, usuario, bairro, cidade, usuariointercorrenciacampanha
                            WHERE `Campanha_id` = $campanha_id
                                AND `Vacina_id` = $vacina_id
                                AND usuario.id = usuariovacinadocampanha.Usuario_id 
                                $sqlCidade
                                AND usuariointercorrenciacampanha.UsuarioVacinadoCampanha_id = usuariovacinadocampanha.id
                                AND usuario.ativo";

            $stmt = $this->conexao->prepare($sql);
           // $stmt->bind_param('iii', $cidade_id, $vacina_id, $campanha_id);
        }
        else 
        {
             /*$sql = "SELECT COUNT( DISTINCT usuario.id)
                        FROM usuario , usuariointercorrencia, usuariovacinado, bairro , cidade
                            WHERE usuariovacinado.id  = usuariointercorrencia.UsuarioVacinado_id
                                AND usuariovacinado.Usuario_id = usuario.id
                                AND usuario.Bairro_id = bairro.id
                                AND bairro.Cidade_id = cidade.id
                                AND cidade.id = ?
                                AND usuario.ativo
                                AND usuariovacinado.Vacina_id = ?";*/

            $sql = "SELECT COUNT( DISTINCT usuario.id)
                    FROM `usuariovacinado`, usuario, bairro, cidade, usuariointercorrencia
                        WHERE `Vacina_id` = $vacina_id
                        AND usuariovacinado.Usuario_id = usuario.id
                        $sqlCidade
                        AND usuariointercorrencia.UsuarioVacinado_id = usuariovacinado.id
                        AND usuario.ativo";

            $stmt = $this->conexao->prepare($sql);
            //$stmt->bind_param('ii', $cidade_id, $vacina_id); 

        }
        
        $stmt->bind_result($totalDeRegistros);
        $stmt->execute();
        $stmt->fetch();
        $stmt->free_result();

        ///////////////////////////////////////////////////////////////////////

        $limite = Html::LIMITE;
        /*
        $sql = "SELECT usuario.id, usuario.nome, usuario.mae, usuario.nascimento
                    FROM usuario , usuariointercorrencia, usuariovacinado, bairro , cidade
                        WHERE usuariovacinado.id  = usuariointercorrencia.UsuarioVacinado_id
                            AND usuariovacinado.Usuario_id = usuario.id
                            AND usuario.Bairro_id = bairro.id
                            AND bairro.Cidade_id = cidade.id
                            AND cidade.id = ?
                            AND usuario.ativo
                            AND bairro.ativo
                            AND usuariovacinado.Vacina_id = ?
                                GROUP BY usuario.id
                                    LIMIT $aPartirDe, $limite";*/


        if ($campanha_id != 0 && $campanha_id != 'semCampanha') {

            /*$sql = "SELECT DISTINCT usuario.id, usuario.nome, usuario.mae, usuario.nascimento
                        FROM usuario , usuariointercorrenciacampanha, usuariovacinadocampanha, bairro , cidade
                            WHERE usuariovacinadocampanha.id  = usuariointercorrenciacampanha.UsuarioVacinadoCampanha_id
                                AND usuariovacinadocampanha.Usuario_id = usuario.id
                                AND usuario.Bairro_id = bairro.id
                                AND bairro.Cidade_id = cidade.id
                                AND cidade.id = ?
                                AND usuario.ativo
                                AND usuariovacinadocampanha.Vacina_id = ?
                                AND usuariovacinadocampanha.Campanha_id = ?
                                ORDER BY usuario.nome
                                    LIMIT $aPartirDe, $limite";*/


            $sql = "SELECT DISTINCT usuario.id, usuario.nome, usuario.mae, usuario.nascimento
                        FROM `usuariovacinadocampanha`, usuario, bairro, cidade, usuariointercorrenciacampanha
                            WHERE `Campanha_id` = $campanha_id
                                AND `Vacina_id` = $vacina_id
                                AND usuario.id = usuariovacinadocampanha.Usuario_id
                                $sqlCidade
                                AND usuario.ativo
                                AND usuariointercorrenciacampanha.UsuarioVacinadoCampanha_id = usuariovacinadocampanha.id
                                ORDER BY usuario.nome
                                    LIMIT $aPartirDe, $limite";

            $stmt = $this->conexao->prepare($sql);
            //$stmt->bind_param('iii', $cidade_id, $vacina_id, $campanha_id);
        }
        else
        {
            /*
             $sql = "SELECT DISTINCT usuario.id, usuario.nome, usuario.mae, usuario.nascimento
                        FROM usuario , usuariointercorrencia, usuariovacinado, bairro , cidade
                            WHERE usuariovacinado.id  = usuariointercorrencia.UsuarioVacinado_id
                                AND usuariovacinado.Usuario_id = usuario.id
                                AND usuario.Bairro_id = bairro.id
                                AND bairro.Cidade_id = cidade.id
                                AND cidade.id = ?
                                AND usuario.ativo
                                AND usuariovacinado.Vacina_id = ?
                                GROUP BY usuario.nome
                                    LIMIT $aPartirDe, $limite";*/

            $sql = "SELECT DISTINCT usuario.id, usuario.nome, usuario.mae, usuario.nascimento
                    FROM `usuariovacinado`, usuario, bairro, cidade, usuariointercorrencia
                        WHERE `Vacina_id` = $vacina_id
                        AND usuariovacinado.Usuario_id = usuario.id
                        $sqlCidade
                        AND usuario.ativo
                        AND usuariointercorrencia.UsuarioVacinado_id = usuariovacinado.id
                        ORDER BY usuario.nome
                                    LIMIT $aPartirDe, $limite";


            $stmt = $this->conexao->prepare($sql);
           // $stmt->bind_param('ii', $cidade_id, $vacina_id);

        }


       ################
       // TERMINANDO //
       ################

       // $stmt = $this->conexao->prepare($sql);
       // $stmt->bind_param('ii', $cidade_id, $vacina_id);
        $stmt->bind_result($usuario_id, $nome, $mae, $nascimento);
        $stmt->execute();
        $stmt->store_result();
        $qtd = $stmt->num_rows;
  
        // Imprime o cabe�alho com os dados espec�ficos escolhidos pelo usu�rio
        $this->ImprimirCabecalho( $this->CamposParaCabecalhoRelatorio($post, self::$titulo) );

        if($qtd > 0) {
            $data = new Data();
            $crip = new Criptografia();
            while($stmt->fetch()){

                $end = $crip->Cifrar("pagina=exibirDetalhesIntercorrenciaRelatorio&"
                                   . "tipo=RelatorioEventosAdversos&vacina_id=$vacina_id"
                                   . "&usuario_id=$usuario_id&campanha_id=$campanha_id");
                $arr[] = array('id' => $usuario_id,
                               'nome' => "<a href='#' onclick='AbrirJanela(\"$this->url/Uf/Pop?$end\")' >$nome</a>",
                               'm�e' => $mae,
                               'nascimento' => $data->InverterData($nascimento));
            }

            Html::ExibirInformacoesDeRegistrosEncontrados($totalDeRegistros);
            Html::CriarTabelaDeArray($arr);
        }

        elseif( $qtd == 0 ) $this->AdicionarMensagem('Nenhum registro encontrado.');
        else $this->AdicionarMensagemDeErro('Algum erro ocorreu durante a pesquisa.');
        $stmt->free_result();
    }

//------------------------------------------------------------------------------

    /**
     * Exibe o formulario para os relatorios de eventos adversos.
     *
     */
   public function ExibirFormulario($porCampanhaOuVacina = 'porVacina')
   {
   
        $crip = new Criptografia();

        // Para criar a vari�vel $tipo e $apagarDaQuery
        parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));

        $end = $crip->Cifrar("pagina=exibirRelatorioPop&tipo=$tipo");

        echo "<h3 align='center'>{$this->Titulo()}</h3>";

        $validarSubmissao =	$this->MontarValidacaoDeSubmissaoDoFormulario($porCampanhaOuVacina);

        echo "<form name='relatorioEventosAdversos' id='relatorioEventosAdversos' method='post'
        action='./Rel/?$end' target='_blank' onsubmit=\"$validarSubmissao\">";

			//==================================================================

			?>
			<p>
                <div class="CadastroEsq">Campanha ou rotina:</div>
				<div class='CadastroDir'><?php $this->SelectCampanhaComRotina(); ?></div>
			</p>
            <p>
			<p>
                <div class="CadastroEsq">Vacina:</div>
				<div class='CadastroDir' id="vacinasSelecionadas"><?php $this->SelectVacinas(true, false); ?> </div>
			</p>
            </p>
            <p>
                <div class="CadastroEsq">Cidade:</div>
				<div class='CadastroDir'>

                    <?php $this->SelectCidades(); ?>
                    <br />
                    <label id="labelNaoResidentes">
                        <input type="checkbox" name="naoResidentes" id="naoResidentes">
                        Listar apenas indiv�duos n�o residentes neste munic�pio
                    </label>

                </div>

			</p>

            <p><div class="CadastroEsq">Vacinados entre:</div>
				<div class='CadastroDir'>
					<input type="text" name="data_inicio" id="data_inicio"
					size="10" maxlength="10"
					onblur="ValidarData(this, true)"
					onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					onkeyup="return Mascara('DATA', this, event);"
					value="<?php if (isset($_POST['data_inicio']))
					echo $_POST['data_inicio'] ?>" />
				e
					<input type="text" name="data_fim" id="data_fim"
					size="10" maxlength="10"
					onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					onkeyup="return Mascara('DATA', this, event);"
					onblur="ValidarData(this, true)"
					value="<?php if (isset($_POST['data_fim']))
					echo $_POST['data_fim'] ?>" />
				</div>

			</p>

			<?php
			//==================================================================
            /*
            }
            elseif( $porCampanhaOuVacina == 'porVacina' ) {
			?>
			<p><div class="CadastroEsq">Vacina:  </div>
				<div class='CadastroDir' id="vacinasSelecionadas"><?php $this->SelectVacinas(); ?> </div>
			</p>

			<p><div class="CadastroEsq">Vacinados entre:</div>
				<div class='CadastroDir'>
					<input type="text" name="data_inicio" id="data_inicio"
					size="10" maxlength="10"
					onblur="ValidarData(this, true)"
					onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					onkeyup="return Mascara('DATA', this, event);"
					value="<?php if (isset($_POST['data_inicio']))
					echo $_POST['data_inicio'] ?>" />
				e
					<input type="text" name="data_fim" id="data_fim"
					size="10" maxlength="10"
					onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara('DATA', this, event);"
					onkeyup="return Mascara('DATA', this, event);"
					onblur="ValidarData(this, true)"
					value="<?php if (isset($_POST['data_fim']))
					echo $_POST['data_fim'] ?>" />
				</div>

			</p>
	
            <?php
             
             */
          //  }

            $botao = new Vacina();
            $botao->ExibirBotoesDoFormulario('Confirmar');

            echo "</form>";

            echo "<p><strong>Nota:</strong><blockquote>";
               echo $this->Nota();
            echo "</blockquote></p>";

   }

   //---------------------------------------------------------------------------

   /**
    * Monta a valida��o para o "onsubmit" do formul�rio desta classe e filhas
    * de relat�rio de rotina.
    * 
    * @return String
    */
   private function MontarValidacaoDeSubmissaoDoFormulario($porCampanhaOuVacina)
	{
        if( $porCampanhaOuVacina == 'porVacina')
        {
            $validacao = 'return ( '
                       . ' ValidarCampoSelect(this.vacina_id, \'Vacina\')'
                       . ' && ValidarCampoSelect(this.cidade_id, \'Cidade\')'
                       . ' && ValidarData(this.data_inicio, true)'
                       . ' && ValidarData(this.data_fim, true)'
                       . ' )';
        }
        if( $porCampanhaOuVacina == 'porCampanha')
        {
            $validacao = 'return ( '
                       . ' ValidarCampoSelect(this.campanha_id, \'Campanha\')'
                       . ' && ValidarCampoSelect(this.cidade_id, \'Cidade\')'
                       . ' && ValidarData(this.data_inicio, true)'
                       . ' && ValidarData(this.data_fim, true)'
                       . ' )';
        }

        return $validacao;
	}
}