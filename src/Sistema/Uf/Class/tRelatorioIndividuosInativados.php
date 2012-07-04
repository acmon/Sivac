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
 class RelatorioIndividuosInativados extends Relatorio
{
     /**
     * Atributos
     */
     protected static $titulo = ' Indivíduos Inativos';

     protected static $nota = ' Elaborar! ';

	//------------------------------------------------------------------------------

    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();

        Depurador::Pre('Classe instanaciada: ' . __CLASS__);
    }
//------------------------------------------------------------------------------

   /**
    * Exibe o formulario para os relatorios de rotina.
    *
    */
   public function ExibirFormulario()
   {
        $crip = new Criptografia();

        // Para criar a variável $tipo e $apagarDaQuery
        parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));

        $end = $crip->Cifrar("pagina=exibirRelatorioPop&tipo=$tipo");

        echo "<h3 align='center'>{$this->Titulo()}</h3>";

        $validarOnClick =	$this->MontarValidacaoDeSubmissaoDoFormulario();

       // return false para não submeter com ENTER:
       // (o formulário será usado com um evento ONCLICK em um botão)
       echo "<form name='relatorioCaderneta' id='relatorioCaderneta' onsubmit='return false'>";

       

        //==================================================================

        ?>
        <p>
            <div class="CadastroEsq">Nome:</div>
            <div class='CadastroDir'><input type="text" name="nome" id="nome" style="width:300px"
                 onkeypress="FormatarNome(this, event)"
                 onkeyup="FormatarNome(this, event)"
                 onkeydown="Mascara('NOME', this, event)"
                 onblur="LimparString(this); ValidarPesquisa(this, 3, true);
                        FormatarNome(this, event)" />
            </div>
        </p>

        <?php

        //==================================================================

        ?>
		<p>
            <div class="CadastroEsq">Mãe: </div>
			<div class='CadastroDir'><input type="text"  name="mae" id="mae" style="width:300px"
                onkeypress="FormatarNome(this, event)"
				onkeyup="FormatarNome(this, event)"
				onkeydown="Mascara('NOME', this, event)"
				onblur="LimparString(this); ValidarPesquisa(this, 3, true);
				FormatarNome(this, event)" />
			</div>
        </p>
        <?php

        //==================================================================

        ?>
        <p>
            <div class="CadastroEsq">CPF:</div>
            <div class="CadastroDir">
			<input type="text" name="cpf" id="cpf" maxlength="14"
                onkeypress="return Digitos(event, this);"
				onkeydown="Mascara('CPF',this,event);"
				onblur="ValidarCpf(this, true);"/>
				<span id="TextoExemplos"><?php
                    echo " Ex.: 474.876.345-07" ?></span>
			</div>
		</p>

        <?php
       
       //==================================================================

        ?>
        <p>
            <div class="CadastroEsq">Motivo de Inativação:</div>
            <div class="CadastroDir">
                <select name="motivoInativacao" id="motivoInativacao">
                    <option>- selecione -</option>
                    <option>Mudança de Cidade</option>
                    <option>Óbito</option>
                    <option>Duplicidade</option>
                </select>
        	</div>
		</p>
        <div class="CadastroEsq" style="width: 125px"></div>
        <div class='CadastroDir'><hr style="width: 520px;  border: 1px #ccc solid; "/></div>
        <?php
       
       //==================================================================

        ?>
        <p>
        <div class="CadastroEsq">Cidade:</div>
        <div class='CadastroDir'><?php $this->SelectCidades(); ?><br />
            <label id="labelNaoResidentes"><input type="checkbox" name="naoResidentes"
                id="naoResidentes">
                Listar apenas indivíduos não residentes neste município</label>
        </div>
        </p>

                <?php

        //==================================================================

        ?>
        <p><div class="CadastroEsq" >Unidade de Saúde:</div>
            <div class='CadastroDir'>

            <select name="unidade" id="unidade" style="width:305px;
				margin-left:2px;"
				onblur="ValidarCampoSelect(this, 'unidade de saúde', false)"
				onchange="PesquisarAcs(this.value)">
                    <?php $this->SelectUnidades(false); ?>
			 	</select>

            </div>

        </p>

        <?php

        //==================================================================

        ?>
       <p><div class="CadastroEsq">ACS: </div>
				<div class='CadastroDir'><select name="acs" id="acs"
                                 style="width:305px;" 
				onblur="ValidarCampoSelect(this, 'ACS')"
                >
				<option value="0">- selecione -</option></select></div>

        </p>
        <?php

        //==================================================================

        ?>
        <p>
            <div class="CadastroEsq">
                Sexo:
            </div>

            <div class="CadastroDir" >
                <label><input type="radio" name="sexo" value="Ambos" checked="true" style="vertical-align: bottom;" />Ambos</label>
                <label><input type="radio" name="sexo" value="F" style="vertical-align: bottom;" />F</label>
                <label><input type="radio" name="sexo" value="M" style="vertical-align: bottom;" />M</label>
            </div>
        </p>


        <?php

        //==================================================================

        ?>
            <p>
                <div class="CadastroEsq">Idade entre:</div>
                <div class='CadastroDir'>
                <input type="text" name="faixaInicio"  id="faixaInicio" style="width:50px" maxlength="7"
                onkeypress="return Digitos(event, this);"
                onblur="ValidarFaixaEtaria(document.escolherRelatorio.faixaInicio,
                document.escolherRelatorio.unidadeInicio,this,document.escolherRelatorio.unidadeFim,true)"/>
                <select name="unidadeInicio" id="unidadeInicio">
                <option value="day">Dia(s)</option>
                <option value="week">Semana(s)</option>
                <option value="month">Mês(s)</option>
                <option value="year">Ano(s)</option>
                </select>

                e

                <input type="text" name="faixaFim"  id="faixaFim" style="width:50px" maxlength="7"
                onkeypress="return Digitos(event, this);"
                onblur="ValidarFaixaEtaria(document.escolherRelatorio.faixaInicio,
                document.escolherRelatorio.unidadeInicio,this,document.escolherRelatorio.unidadeFim,true)"/>
                <select name="unidadeFim" id="unidadeFim">
                <option value="day">Dia(s)</option>
                <option value="week">Semana(s)</option>
                <option value="month">Mês(s)</option>
                <option value="year">Ano(s)</option>
                </select>
                </div>
            </p>

        <?php

        //==================================================================
        
        $botao = new Vacina();
        $botao->ExibirBotoesDoFormulario('Confirmar');

        echo "</form>";

        echo "<p><strong>Nota:</strong><blockquote>";
           echo $this->Nota();
        echo "</blockquote></p>";


    }
//------------------------------------------------------------------------------

    /**
     * Retorna o título do relatório. Necessário pois na classe mãe (Relatorio)
     * é usado $this->Titulo() genericamente para retornar o título de cada
     * filha, o que não funcionaria se chamasse self::$titulo na classe mãe.
     *
     * @return String
     */
    protected function Titulo()
    {
        return self::$titulo;
    }
//------------------------------------------------------------------------------

   /**
    * Monta a validação para o "if()" do clique do botão no formulário desta
    * classe.
    *
    * @return String
    */
    private function MontarValidacaoDeSubmissaoDoFormulario()
    {
        // Esta validação é para um botão "não submit", logo, não há return:
        $validacao = 'if( '
                   . 'ValidarCampoSelect(document.relatorioCaderneta.estado, "estado") '
                   . '&& ValidarCampoSelect(document.relatorioCaderneta.cidade, "cidade") '
                   . '&& ( '
                   .     '(ValidarPesquisa(document.relatorioCaderneta.mae, 3, true) '
                   .     '&& ValidarPesquisa(document.relatorioCaderneta.nome)) '
                   .     '|| (ValidarPesquisa(document.relatorioCaderneta.nome, 3, true) '
                   .     '&& ValidarPesquisa(document.relatorioCaderneta.mae))'
                   . ' ) '
                   . '&& ValidarCpf(document.relatorioCaderneta.cpf, true)'
                   . ' ) ';

        return $validacao;
	}
//------------------------------------------------------------------------------

    /**
     * Retorna a nota do relatório. Necessário pois na classe mãe (Relatorio)
     * é usado $this->Nota() genericamente para retornar o título de cada
     * filha, o que não funcionaria se chamasse self::$nota na classe mãe.
     *
     * @return String
     */
    protected function Nota()
    {
        return self::$nota;
    }
}