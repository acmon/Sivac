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
 class RelatorioIndividuosInativados extends Relatorio
{
     /**
     * Atributos
     */
     protected static $titulo = ' Indiv�duos Inativos';

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

        // Para criar a vari�vel $tipo e $apagarDaQuery
        parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));

        $end = $crip->Cifrar("pagina=exibirRelatorioPop&tipo=$tipo");

        echo "<h3 align='center'>{$this->Titulo()}</h3>";

        $validarOnClick =	$this->MontarValidacaoDeSubmissaoDoFormulario();

       // return false para n�o submeter com ENTER:
       // (o formul�rio ser� usado com um evento ONCLICK em um bot�o)
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
            <div class="CadastroEsq">M�e: </div>
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
            <div class="CadastroEsq">Motivo de Inativa��o:</div>
            <div class="CadastroDir">
                <select name="motivoInativacao" id="motivoInativacao">
                    <option>- selecione -</option>
                    <option>Mudan�a de Cidade</option>
                    <option>�bito</option>
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
                Listar apenas indiv�duos n�o residentes neste munic�pio</label>
        </div>
        </p>

                <?php

        //==================================================================

        ?>
        <p><div class="CadastroEsq" >Unidade de Sa�de:</div>
            <div class='CadastroDir'>

            <select name="unidade" id="unidade" style="width:305px;
				margin-left:2px;"
				onblur="ValidarCampoSelect(this, 'unidade de sa�de', false)"
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
                <option value="month">M�s(s)</option>
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
                <option value="month">M�s(s)</option>
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
    * Monta a valida��o para o "if()" do clique do bot�o no formul�rio desta
    * classe.
    *
    * @return String
    */
    private function MontarValidacaoDeSubmissaoDoFormulario()
    {
        // Esta valida��o � para um bot�o "n�o submit", logo, n�o h� return:
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
}