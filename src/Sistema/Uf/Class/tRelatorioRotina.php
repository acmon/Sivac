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

class RelatorioRotina extends Relatorio
{
//------------------------------------------------------------------------------

    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        
        Depurador::Pre('Classe instanciada: ' . __CLASS__);
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

        $validarSubmissao =	$this->MontarValidacaoDeSubmissaoDoFormulario();

        echo "<form name='relatorioRotina' id='relatorioRotina' method='post'
        action='./Rel/?$end' target='_blank' onsubmit=\"$validarSubmissao\">";

        //==================================================================
        ?>
        <p><div class="CadastroEsq">Vacina:  </div>
            <div class='CadastroDir' id="vacinasSelecionadas"><?php $this->SelectVacinas(true, false); ?> </div>
            <?php


             if( true /*????  VERIRIFCAR SE É RELATORIO DE QUNATIDADE  ????*/ ) {

                 echo '<div id="incluirDoseEspecifica"></div>';

                 echo "<p><div class='CadastroEsq'></div>
                    <div class='CadastroDir'><label><input type='checkbox'
                    checked='true' id='todas_doses' name='todas_doses'
                    onclick='IncluirDoseEspecifica(this.checked,
                    document.getElementById(\"vacina_id\").value,
                    \"incluirDoseEspecifica\")' />
                    Incluir todas as doses da vacina</label></div></p>";
             }

            ?>

        </p>
        <?php

        //==================================================================

        ?>
        <br />
        <div style="background-color: #eaeff3; display: table; clear: both; margin-left: 123px; width: 520px; padding-bottom: 10px">
            

            <div style="background-color: #fff; display: table; width: 140px; float: left; margin: 8px; padding: 3px">
                <label><input type="radio" name="tipoDeConsulta" value="na"
                    checked="true" style="vertical-align: bottom;"
                    onclick="
                    var arr = new Array('naoResidentes', 'labelNaoResidentes');
                    TratarHabilitacaoAoSelecionar(!this.checked, arr);
                    arr = new Array('acs');
                    TratarHabilitacaoAoSelecionar(this.checked, arr);
                    document.getElementById('labelNaoResidentes').style.color = '#000'"/>Vacinados
                    na</label>
            </div>
            <div style="display: table; float: left; margin-top: 8px; padding-top: 3px">|</div>
            <div style="background-color: #fff; display: table; width: 140px; float: left; margin: 8px; padding: 3px">
            <label><input type="radio" name="tipoDeConsulta" value="da"
                    onclick="document.getElementById('naoResidentes').checked = false;
                    var arr = new Array('naoResidentes', 'labelNaoResidentes');
                    TratarHabilitacaoAoSelecionar(this.checked, arr);
                    arr = new Array('unidade');
                    TratarHabilitacaoAoSelecionar(!this.checked, arr);
                    arr = new Array('acs');
                    TratarHabilitacaoAoSelecionar(!this.checked, arr);
                    document.getElementById('labelNaoResidentes').style.color = '#CCC'"
                style="vertical-align: bottom;" />Com vínculo de</label>
            </div>
            <div style="clear: both"><hr /></div>

        <p>
        <div class="CadastroEsq" style="width: 125px;">Cidade:</div>
        <div class='CadastroDir' ><?php $this->SelectCidades(); ?></div><br />
        <div class='CadastroDir' >
            <label id="labelNaoResidentes"><input type="checkbox" name="naoResidentes"
                id="naoResidentes">
                Listar apenas indivíduos não residentes neste município</label>
        </div>
        </p>
        
        <?php 

        //==================================================================

        ?>
        <p><div class="CadastroEsq" style="width: 125px">Unidade de Saúde:</div>
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
       <p><div class="CadastroEsq" style="width: 125px">ACS: </div>
				<div class='CadastroDir'><select name="acs" id="acs"
                                 style="width:305px;" disabled="true"
				onblur="ValidarCampoSelect(this, 'ACS')"
                >
				<option value="0">- selecione -</option></select></div>

        </p>

        </div>
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
        //------------------------------------------------------------------
            ?>
            <p>
                <div class="CadastroEsq">
                    Tipo de Exibição:
                </div>

                <div class="CadastroDir" >
                    <label><input type="radio" name="tipoExibicao" value="lista" checked="true" style="vertical-align: bottom;" />Lista</label>
                    <label><input type="radio" name="tipoExibicao" value="grafico" style="vertical-align: bottom;" />Gráfico</label>
                </div>
            </p>
            <p>
                <div class="CadastroEsq"></div>

                <div class="CadastroDir" >
                    <label>
                    <input type="checkbox" name="acamados" style="vertical-align: bottom;" />
                    Exibir apenas indivíduos acamados
                    </label>
                </div>
            </p>
         <?php

    $botao = new Vacina();
    $botao->ExibirBotoesDoFormulario('Confirmar');

    echo "</form>";
    
    echo "<p><strong>Nota:</strong><blockquote>";
       echo $this->Nota();
    echo "</blockquote></p>";
       
   }

   //---------------------------------------------------------------------------

   /**
    * Monta a validação para o "onsubmit" do formulário desta classe e filhas
    * de relatório de rotina.
    * 
    * @return String
    */
   protected function MontarValidacaoDeSubmissaoDoFormulario()
	{
        $validacao = 'return ( '
                   . ' ValidarCampoSelect(this.vacina_id, \'Vacina\')'
                   . ' && ValidarCampoSelect(this.cidade_id, \'Cidade\')'
                   . ' && ValidarData(this.data_inicio, true)'
                   . ' && ValidarData(this.data_fim, true)'
                   . ' )';

        return $validacao;
	}
}