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


class Intercorrencia
{

    

	//----------------------------------------------------------------------
	public function __construct()
	{
		$this->msgDeErro = array();
        
		// ????? Tirar o 1000 depois.
		$this->url = "http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA;
		
		$this->LocalizarArquivoGeradorDeIcone();
	}
	//--------------------------------------------------------------------------
	public function __destruct()
	{
		if( isset($this->conexao) ) $this->conexao->close();
	}
	//--------------------------------------------------------------------------
	private function LocalizarArquivoGeradorDeIcone()
	{
		if(file_exists("{$_SERVER['DOCUMENT_ROOT']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php")) {
			
			$this->arquivoGerarIcone =
				"http://{$_SERVER['HTTP_HOST']}/".Constantes::PASTA_SISTEMA."/Uf/gerarIcone.php";
		}
	}
	//----------------------------------------------------------------------
	/**
	 * Conexão com a Base de Dados
	 *
	 */
	public function UsarBaseDeDados()
	{
		$this->conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
			$_SESSION['senha']);


		$this->conexao->select_db($_SESSION['banco']);

		//echo mysqli_connect_error(); die;
	}
	//--------------------------------------------------------------------------
	/**
	 * Antes de adicionar a mensagem, verifica se a mesma já existe:
	 *
	 * @param String $mensagem
	 */
	public function AdicionarMensagemDeErro($mensagem)
	{
		if( !in_array($mensagem, $this->msgDeErro) ) {
			$this->msgDeErro[] = $mensagem;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibir uma ou várias mensagens de erro
	 *
	 */
	public function ExibirMensagensDeErro(
                                       $tituloDaJanela = 'Erro',
                                       $eventoDeVisibilidade = 'onmousemove')
	{
		if( count($this->msgDeErro) ) {

                        // Container para a barra de título e o corpo da mensagem
			echo '<div class="msgErro" id="containerDeMensagem"
                                style="visibility: visible">';

                        // Barra de título:
                        echo '<div class="barraDeTituloMsgErro" id="tituloMsgErro"
                              title="Fechar" '
                        . $eventoDeVisibilidade
                        . '="document.getElementById(\'containerDeMensagem\').style.visibility = \'hidden\';'
                        . 'document.getElementById(\'tituloMsgErro\').style.visibility = \'hidden\';'
                        . 'document.getElementById(\'mensagemDeErro\').style.visibility = \'hidden\';">'
                        . '&nbsp;'
                        . $tituloDaJanela
                        . '</div>';

                        // Corpo da mensagem:
                        echo '<div class="corpoMsgErro" id="mensagemDeErro">';
			echo 'Corrija o(s) erro(s) abaixo:';

                        // Exibindo a lista de erros:
			echo '<ul>';
			foreach ($this->msgDeErro as $mensagem) {
				echo "<li>$mensagem</li>";
			}
			echo '</ul>';

			echo '</div></div>';
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Exibe uma mensagem 
	 *
	 * @param String $mensagem
	 */
	public function ExibirMensagem($mensagem,
                                       $tituloDaJanela = 'Informação',
                                       $eventoDeVisibilidade = 'onmousemove'
                                       )
	{
                // Container para a barra de título e o corpo da mensagem
		echo '<div class="msgErro" id="containerDeMensagem"
                        style="visibility: visible">';

                // Barra de título:
		echo '<div class="barraDeTituloMsgErro" id="tituloMsgErro" title="Fechar" '
                        . $eventoDeVisibilidade
                        . '="document.getElementById(\'containerDeMensagem\').style.visibility = \'hidden\';'
                        . 'document.getElementById(\'tituloMsgErro\').style.visibility = \'hidden\';'
                        . 'document.getElementById(\'mensagemDeErro\').style.visibility = \'hidden\';">'
                        . '&nbsp;'
                        . $tituloDaJanela
                        . '</div>';

                // Corpo da mensagem:
		echo '<div class="corpoMsgErro" id="mensagemDeErro">', $mensagem, '</div>';

                // Fechando o container:
		echo '</div>';
	}

	//--------------------------------------------------------------------------
	public function SelectIntercorrencias($intercorrencia_id = false,
		$nomeDoSelect = 'intercorrencia_id')
	{
		$id = $intercorrencia_id;

		$sql = 'SELECT id, nome FROM `intercorrencia`
			WHERE ativo';

		/*if($intercorrencia_id) {
			$sql .= " AND id = $intercorrencia_id";
		}*/

		$stmt = $this->conexao->prepare($sql) or
		die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($intercorrencia_id, $intercorrencia_nome);

		$stmt->execute();
		$stmt->store_result();
		$qtdLinhas = $stmt->num_rows;

		if($qtdLinhas > 0) {

			echo "<select name='$nomeDoSelect' id='$nomeDoSelect'
				style='width: 300px' 
                onchange=\"GravarIntercorrenciaSelecionada(this.value);\"
                onblur=\"ValidarCampoSelect(this, ' evento adverso ')\">";
			echo '<option value="0">- selecione -</option>';

			while( $stmt->fetch() ) {
				$selected = false;
				if($intercorrencia_id == $id) $selected = "selected='true'";
				echo "<option value='$intercorrencia_id' $selected>$intercorrencia_nome</option>";
			}

			echo '</select>';

			$stmt->free_result();

			return true;
		}

		$stmt->free_result();

		if($qtdLinhas == 0) {
			$this->AdicionarMensagemDeErro('Não há eventos adversos no banco de dados');
			return true;
		}

		if($qtdLinhas < 0) {
			$this->AdicionarMensagemDeErro('Ocorreu algum erro ao recuperar o evento adverso');
			return false;
		}
	}
//--------------------------------------------------------------------------
	public function ExibirDetalhesIntercorrencias($intercorrencia_id, $datahoraintercorrencia = false, $obs = false)
	{

		$sql = "SELECT id, nome, descricao, tempo, frequencia FROM `intercorrencia`
                WHERE ativo AND id = $intercorrencia_id";

		$stmt = $this->conexao->prepare($sql) or
		die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_result($id, $nome, $descricao, $tempo, $frequencia);

		$stmt->execute();
		$stmt->store_result();
		$qtdLinhas = $stmt->num_rows;

		if($qtdLinhas > 0) {

			$stmt->fetch();

            echo "<fieldset style='margin-left:50px; margin-right:50px; padding:10px; margin-top:25px'><legend><b>Nome</b></legend>$nome</fieldset>";

            if(strlen($datahoraintercorrencia) && $datahoraintercorrencia) {
                $datahoraintercorrencia = explode('-',str_replace(array(':', ' '), '-',$datahoraintercorrencia));
                $data = $datahoraintercorrencia[2].'-'.$datahoraintercorrencia[1].'-'.$datahoraintercorrencia[0];
                $hora = $datahoraintercorrencia[3].':'.$datahoraintercorrencia[4];
                echo "<fieldset style='margin-left:50px; margin-right:50px; padding:10px; '><legend><b>Data e Hora</b></legend>$data $hora</fieldset>";
            }
            echo "<fieldset style='margin-left:50px; margin-right:50px; padding:10px; '><legend><b>Descrição</b></legend>$descricao</fieldset>";
            echo "<fieldset style='margin-left:50px; margin-right:50px; padding:10px; '><legend><b>Tempo</b></legend>$tempo</fieldset>";
            echo "<fieldset style='margin-left:50px; margin-right:50px; padding:10px; '><legend><b>Frequência</b></legend>$frequencia</fieldset>";
            if(strlen($obs) && $obs)
                echo "<fieldset style='margin-left:50px; margin-right:50px; padding:10px; '><legend><b>Obs</b></legend>$obs</fieldset>";

            echo "<br /><br />";
			$stmt->free_result();
			return true;
		}

		$stmt->free_result();

		if($qtdLinhas == 0) {
			$this->AdicionarMensagemDeErro('Não há intercorrências com essas referências no banco de dados');
			return true;
		}

		if($qtdLinhas < 0) {
			$this->AdicionarMensagemDeErro('Ocorreu algum erro ao recuperar intercorrências');
			return false;
		}
	}
	//----------------------------------------------------------------------
	public function RetornarDadosDoUsuario($usuario_id)
	 {
		$stmt = $this->conexao->prepare('SELECT nome, mae, nascimento FROM `usuario`
			WHERE id = ? AND ativo') 
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_param('i', $usuario_id);
		$stmt->bind_result($nome, $mae, $nascimento);
		$stmt->execute();
		$stmt->fetch();
		$stmt->free_result();
		
		return array($nome, $mae, $nascimento);
	 }
	//----------------------------------------------------------------------
	public function ExibirTodasIntercorrencias($usuario_id, $vacina_id, $campanha_id = 0)
	 {
        echo "<h3 style='font-size:15px'><center>Detalhes eventos adversos<center></h3>";

		$arr = $this->RetornarIntercorrenciasDoUsuario($usuario_id, $vacina_id, $campanha_id);
        
        if($arr) foreach ($arr as $arrIntercorrencias){

            list($intercorrencia_id, $datahoraintercorrencia, $obs) = $arrIntercorrencias;

            $this->ExibirDetalhesIntercorrencias($intercorrencia_id, $datahoraintercorrencia, $obs);
        }
	 }
	//----------------------------------------------------------------------
	public function RetornarIntercorrenciasDoUsuario($usuario_id, $vacina_id, $campanha_id = 0)
	 {

        $stmt = $this->conexao->prepare("SELECT Intercorrencia_id, datahoraintercorrencia, usuariointercorrencia.obs
                                                FROM `usuariointercorrencia`, usuariovacinado
                                                    WHERE usuariovacinado.Vacina_id = $vacina_id
                                                    AND usuariovacinado.Usuario_id = $usuario_id
                                                    AND usuariovacinado.id = usuariointercorrencia.UsuarioVacinado_id")
                                        or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        if($campanha_id != 0 && $campanha_id != 'semCampanha')
        $stmt = $this->conexao->prepare("SELECT Intercorrencia_id, datahoraintercorrencia, usuariointercorrenciacampanha.obs
                                                FROM `usuariointercorrenciacampanha`, `usuariovacinadocampanha`
                                                    WHERE usuariovacinadocampanha.Vacina_id = $vacina_id
                                                    AND usuariovacinadocampanha.Campanha_id = $campanha_id
                                                    AND usuariovacinadocampanha.Usuario_id = $usuario_id
                                                    AND usuariointercorrenciacampanha.Usuariovacinadocampanha_id = usuariovacinadocampanha.id")
                                        or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        $arr = Array();
		$stmt->bind_result($intercorrencia_id, $datahoraintercorrencia, $obs);
		$stmt->execute();
		while($stmt->fetch()) $arr[] =  array($intercorrencia_id, $datahoraintercorrencia, $obs);
		$stmt->free_result();

		return  count ($arr) > 0 ? $arr : false;
	 }
	//----------------------------------------------------------------------
	public function SelectVacinas($vacina_id = 0)
	{

	    echo '<select name="vacina_id" id="vacina_id"
	    style="width:305px;"
		onblur="ValidarCampoSelect(this, \'Vacina\')">';

	    $consulta = 'SELECT id, Grupo_id, nome, pertence
			  		FROM `vacina`m
			  		WHERE ativo
			  		ORDER BY Grupo_id DESC,
			  		nome ASC';
			  	
		$sql = $this->conexao->prepare($consulta)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			  	
		$grupo_id_anterior = 'nenhum';
		$sql->bind_result($id, $grupo_id, $nome, $pertence);
		$sql->execute();

		echo "<option value='0'>- selecione -</option>";



		while ($sql->fetch()) {

           

           // $vacina = new Vacina;
           // $vacina->UsarBaseDeDados();

            if($pertence) continue;
			  		
			if($grupo_id_anterior != $grupo_id) {

				echo "<optgroup label='$grupo_id'>";
				$grupo_id_anterior = $grupo_id;
				
			}
			$selected = false;
			if($vacina_id == $id) $selected = " selected='true' ";
			echo "\n<option value='$id' $selected>$nome</option>";
			  		
			if($grupo_id_anterior != $grupo_id) echo '</optgroup>';

		}
		echo '</select>';
		
	}

    //----------------------------------------------------------------------
	public function SelectCampanhas($campanha_id)
	{

	    echo '<select name="campanha_id" id="campanha_id"
	    style="width:305px;"
		onblur="ValidarCampoSelect(this, \'Campanha\')"
        onchange="ListarVacinasDaCampanha(\'vacinasSelecionadas\', this.value)">';

	    $consulta = 'SELECT id, nome
			  		FROM `campanha`
			  		WHERE ativo
			  		ORDER BY datainicio';

		$sql = $this->conexao->prepare($consulta)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$sql->bind_result($id,  $nome);
		$sql->execute();

		echo "<option value='semCampanha'>Rotina</option>";

		while ($sql->fetch()) {

			
			$selected = false;
			if($campanha_id == $id) $selected = " selected='true' ";
			echo "\n<option value='$id' $selected>"
            . Html::FormatarMaiusculasMinusculas($nome) . "</option>";

		}
		echo '</select>';

	}
    //----------------------------------------------------------------------
	public function SelectVacinasDaCampanha($campanha_id)
	{
        if($campanha_id == 'semCampanha') {
            $this->SelectVacinas();
            return;
        }

	    echo '<select name="vacina_id" id="vacina_id"
	    style="width:305px;"
		onblur="ValidarCampoSelect(this, \'Vacina da Campanha\')">';

	    $consulta = "SELECT vacinadacampanha.Vacina_id, vacina.nome
			  		FROM `vacinadacampanha` , vacina
			  		WHERE vacina.id = vacinadacampanha.Vacina_id
                    AND vacinadacampanha.Campanha_id = '$campanha_id'
                    AND ativo
			  		ORDER BY nome";

		$sql = $this->conexao->prepare($consulta)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$sql->bind_result($id,  $nome);
		$sql->execute();
        
        $sql->store_result();
		$qtdVacinas = $sql->num_rows;

		echo "<option value='0'>- selecione -</option>";

		while ($sql->fetch()) {
            
            if($qtdVacinas == 1) $campanha_id = $id;

			$selected = false;
			if($campanha_id == $id) $selected = " selected='true' ";
			echo "\n<option value='$id' $selected>$nome</option>";

		}
		echo '</select>';

	}
	//----------------------------------------------------------------------
	
	public function ExibirFormularioBuscarPessoaAdicionarIntercorrencia(
		$cidade_id = false, $pesquisa = false, $mae = false, $cpf = false,
		$nasc = false, $vacina_id = false, $intercorrencia_id = false, $campanha_id = false)
	{
	
		$data = new Data();
		if($pesquisa == 'vazio')  $pesquisa = '';
		if($mae == 'vazio') 	  $mae = '';
		if($cpf == 'vazio') 	  $cpf = '';
		if($nasc == 'vazio') 	  $nasc = '';
		else $nasc = $data->inverterData($nasc);
		if($cidade_id == 'vazio') $cidade_id = '';

		if($campanha_id == 'vazio') $campanha_id = '';

		if($intercorrencia_id == 'vazio' || $intercorrencia_id == 0) $intercorrencia_id = '';

        $this->ExibirFormBuscarUsuarioIntercorrencia(
		$cidade_id, $pesquisa, $mae, $cpf, $nasc, $vacina_id, $intercorrencia_id, $campanha_id);

        return;
		
	?>
	<h3 align="center">Eventos Adversos</h3>
	<form id="formulario2" name="formulario2" method="post"
	action="<?php echo $_SERVER['REQUEST_URI']?>"
	onsubmit="return ( ((ValidarPesquisa(this.pesquisa, 3 , true) && ValidarPesquisa(this.mae, 3, false)) ||
			 (ValidarPesquisa(this.pesquisa, 3 , false) && ValidarPesquisa(this.mae, 3, true)))
			 && ValidarCpf(this.cpf, true)
			 && ValidarData(this.datadenasc, true)
			 && ValidarCampoSelect(this.intercorrencia_id, 'evento adverso')
             && ValidarCampoSelect(this.vacina_id, 'vacina')
			 && ValidarCampoSelect(this.campanha_id, 'campanha', true))">

		<p>
		<div class="CadastroEsq">
			Nome:
		</div> 
		<div class='CadastroDir'>
		<input type="text" name="pesquisa" id="pesquisa"
			value="<?php echo $pesquisa?>"
			style="width:300px;" onkeypress="FormatarNome(this, event)"
			onkeyup="FormatarNome(this, event)"
			onkeydown="Mascara('NOME', this, event)"
			onblur="LimparString(this); ValidarPesquisa(this, 3, true);
					FormatarNome(this, event)"
			value="<?php echo $pesquisa?>"/>
		</div>
		</p>

		<p>
		<div class="CadastroEsq">
			Mãe:
		</div> 
		<div class='CadastroDir'>
		<input type="text" name="mae" id="mae"
			value="<?php echo $mae?>"
			style="width:300px;" onkeypress="FormatarNome(this, event)"
			onkeyup="FormatarNome(this, event)"
			onkeydown="Mascara('NOME', this, event)"
			onblur="LimparString(this); ValidarPesquisa(this);
					FormatarNome(this, event)"
			value="<?php $mae?>"/>
		</div>
		</p>

        <p>
            <div class="CadastroEsq">
                Campanha:
            </div>
            <div class='CadastroDir'>
                <?php $this->SelectCampanhas($campanha_id)?>
            </div>
		</p>

		<p>
		<div class="CadastroEsq">
			Vacina:
		</div>
		<div class='CadastroDir' id="vacinasSelecionadas">
            <?php $this->SelectVacinas($vacina_id); ?>
		</div>
		</p>

		<!-- ============================================================= -->
		<p>
		<div class="CadastroEsq">
			Evento Adverso:
		</div>
		<div class='CadastroDir'> 
		<?php $this->SelectIntercorrencias($intercorrencia_id); ?>
		</div>
		</p>
		<!-- ============================================================= -->
		<p>
		<div class="CadastroEsq">
			Nascimento:
		</div>
		
		<div class='CadastroDir'>
			<input type="text" name="datadenasc" maxlength="10"
				onkeypress="return Digitos(event, this);"
			onkeydown="return Mascara('DATA', this, event);"
			    onkeyup="return Mascara('DATA', this, event);"
				onblur="ValidarData(this,true)"
				value="<?php echo $nasc;?>" />
			<span id="TextoExemplos">
				<?php echo " Ex.: 01/01/1980 " ?>
			</span>
		</div>
		</p>		
		
		<p>
		<div class="CadastroEsq">
			CPF:
		</div>
		
		<div class="CadastroDir">
			<input type="text" name="cpf" maxlength="14"
			onkeypress="return Digitos(event, this);"
			onkeydown="Mascara('CPF',this,event);"
			onblur="ValidarCpf(this, true);"
			value="<?php echo $cpf;?>" />
				<span id="TextoExemplos">
					<?php echo " Ex.: 474.876.345-07" ?>
				</span>
		</div>
		</p>	
		<p>
		<div class="CadastroEsq"></div>
		
		<div class="CadastroDir">
			<label>
				<input type="checkbox" name="nestaCidade" id="nestaCidade" checked="checked" 
				onclick="ExibirBuscaPorEstadoCidade('buscarPorEstadoCidade', this.checked)"/>
				Habitantes de <?php echo "{$_SESSION['cidade_nome']}/{$_SESSION['estado_id']}" ?>
			</label>
		</div>
		</p>
		<div id="buscarPorEstadoCidade"></div>
		<?php

		$botao = new Vacina();

		$botao->ExibirBotaoBuscar('buscar');

		?>
				
		</form>
		
		<br />
		<br />

        <!-- ################################################################# -->
        
		<hr />
            
		<hr />
		<?php
        
        //////////////////////// SQL PARA O RELATORIO ///////////////////////
        
        /*$sql = "SELECT usuario.id, usuario.nome, usuario.mae, usuario.nascimento
                    FROM usuario , usuariointercorrencia, usuariovacinado
                        WHERE usuariovacinado.id  = usuariointercorrencia.UsuarioVacinado_id
                        AND usuariovacinado.Usuario_id = usuario.id
                            GROUP BY usuario.id
                                LIMIT 200";
                        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_result($usuario_id, $nome, $mae, $nascimento);
        $stmt->execute();
        
        while($stmt->fetch()) $arr[] = array('id' => $usuario_id, 'nome' => $nome, 'mãe' => $mae, 'nascimento' => $nascimento);
        echo "<pre>";
        print_r($arr);*/
	}
	//----------------------------------------------------------------------
	public function ExibirFormBuscarUsuarioIntercorrencia(
		$cidade_id = false, $pesquisa = false, $mae = false, $cpf = false,
		$nasc = false, $vacina_id = false, $intercorrencia_id = false, $campanha_id = false)
	{
        ?>
            <h3 align="center">Buscar indivíduos para cadastrar evento adverso</h3>
            <form id="formulario2" name="formulario2" method="post" 
                  action="<?php echo $_SERVER['REQUEST_URI']?>"
                  onsubmit="return ( ((ValidarPesquisa(this.pesquisa, 3 , true) && ValidarPesquisa(this.mae, 3, false)) ||
                                     (ValidarPesquisa(this.pesquisa, 3 , false) && ValidarPesquisa(this.mae, 3, true)))
                                     && ValidarCpf(this.cpf, true)
                                     && ValidarData(this.datadenasc, true)
                                     && ValidarCampoSelect(this.vacina_id, 'vacina')
                                     && ValidarCampoSelect(this.campanha_id, 'campanha', true))">
                  <fieldset>
                    <legend>
                        Indivíduo
                    </legend>

                    <div class="CadastroEsq">
                        Nome:
                    </div>
                    <div class='CadastroDir'>
                        <input type="text" name="pesquisa" id="pesquisa"
                            value="<?php echo $pesquisa?>"
                            style="width:300px;" onkeypress="FormatarNome(this, event)"
                            onkeyup="FormatarNome(this, event)"
                            onkeydown="Mascara('NOME', this, event)"
                            onblur="LimparString(this); ValidarPesquisa(this, 3, true);
                                    FormatarNome(this, event)"
                        />
                    </div>


                    <div class="CadastroEsq">
                        Mãe:
                    </div>
                    <div class='CadastroDir'>
                        <input type="text" name="mae" id="mae"
                            value="<?php echo $mae?>"
                            style="width:300px;" onkeypress="FormatarNome(this, event)"
                            onkeyup="FormatarNome(this, event)"
                            onkeydown="Mascara('NOME', this, event)"
                            onblur="LimparString(this); ValidarPesquisa(this, 3, true);
                                    FormatarNome(this, event)"
                        />
                    </div>


                    <div class="CadastroEsq">
                        Nascimento:
                    </div>

                    <div class='CadastroDir'>
                        <input type="text" name="datadenasc" maxlength="10"
                            onkeypress="return Digitos(event, this);"
                        onkeydown="return Mascara('DATA', this, event);"
                            onkeyup="return Mascara('DATA', this, event);"
                            onblur="ValidarData(this,true)"
                            value="<?php echo $nasc;?>" />
                        <span id="TextoExemplos">
                            <?php echo " Ex.: 01/01/1980 " ?>
                        </span>
                    </div>

                    <div class="CadastroEsq">
                        CPF:
                    </div>

                    <div class="CadastroDir">
                        <input type="text" name="cpf" maxlength="14"
                        onkeypress="return Digitos(event, this);"
                        onkeydown="Mascara('CPF',this,event);"
                        onblur="ValidarCpf(this, true);"
                        value="<?php echo $cpf;?>" />
                            <span id="TextoExemplos">
                                <?php echo " Ex.: 474.876.345-07" ?>
                            </span>
                    </div>

                    <div class="CadastroEsq"></div>

                    <div class="CadastroDir">
                        <label>
                            <input type="checkbox" name="nestaCidade" id="nestaCidade" checked="checked"
                            onclick="ExibirBuscaPorEstadoCidade('buscarPorEstadoCidade', this.checked)"/>
                            Habitantes de <?php echo "{$_SESSION['cidade_nome']}/{$_SESSION['estado_id']}" ?>
                        </label>
                    </div>
                    <div id="buscarPorEstadoCidade"></div>
                  </fieldset>

                  <!-- |||||||||||||||||||||||||||||||||||||||||||||||||||| -->

                  <fieldset>
                    <legend>
                        Vacinado pela
                    </legend>

                    <div class="CadastroEsq">
                            Campanha:
                        </div>
                        <div class='CadastroDir'>
                            <?php $this->SelectCampanhas($campanha_id)?>
                        </div>
                    <div class="CadastroEsq">
                        Vacina:
                    </div>
                    <div class='CadastroDir' id="vacinasSelecionadas">
                        <?php $this->SelectVacinas($vacina_id); ?>
                    </div>
                  </fieldset>

                  <?php
                      $botao = new Vacina();
                      $botao->ExibirBotaoBuscar('buscar');
                  ?>

            </form>
        <?php
	}
	//----------------------------------------------------------------------
	public function VerificarSeEmitiuFormulario()
	{
		if( count($_POST) ) return true;

		return false;
	}
	//----------------------------------------------------------------------
	public function ListarPessoa($pesquisa = 'vazio', $mae = 'vazio', $cpf,
				     $cidade_id, $nasc, $intercorrencia_id, $vacina_id, $campanha_id = false )
	{

        $nome = $this->conexao->real_escape_string(trim($pesquisa));

		$explodeCaracteres = explode(' ',$nome);
		$implodeCaracteres = implode('%',$explodeCaracteres);

		$nome = "%$implodeCaracteres%";

		$explodeCaracteres = explode(' ',$mae);
		$implodeCaracteres = implode('%',$explodeCaracteres);

		$mae = "%$implodeCaracteres%";
		
		if(!$cidade_id) $cidade_id = $_SESSION['cidade_id'];
		
		$data = new Data();
				
		$sqlMae = false;
		if($mae != '%vazio%') $sqlMae = "AND (usuario.mae LIKE '$mae' OR '$mae' = '%vazio%')";
		
		$sqlNome = false;
		if( strlen($nome) > 4) $sqlNome = " usuario.nome LIKE '$nome' AND";

        $tabelaUsuarioVacinado = 'usuariovacinado'; // por vacina ou campanha
        if($campanha_id) $tabelaUsuarioVacinado = 'usuariovacinadocampanha'; 
        else $campanha_id = '0';
        // MAX em usuariovacinado para registrar a intercorrencia para a última
        // dose aplicada no cara:
		$sql = "SELECT MAX($tabelaUsuarioVacinado.id), usuario.id, usuario.nome, usuario.mae,
			usuario.nascimento
			FROM `usuario`, `cidade`, `bairro`, `$tabelaUsuarioVacinado`
			WHERE (usuario.cpf = ? OR '$cpf' = 'vazio')
			AND (usuario.nascimento = ? OR '$nasc' = 'vazio')
			$sqlMae
			AND usuario.Bairro_id = bairro.id
			AND bairro.Cidade_id = cidade.id
			AND cidade.id = ?
            AND $tabelaUsuarioVacinado.Vacina_id = ?
            AND $tabelaUsuarioVacinado.Campanha_id = $campanha_id
            AND usuario.id = $tabelaUsuarioVacinado.Usuario_id
			AND $sqlNome usuario.ativo AND bairro.ativo
            GROUP BY $tabelaUsuarioVacinado.Usuario_id
			ORDER BY usuario.nome
            LIMIT 200";

		$resultado = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado->bind_param('ssii', $cpf, $nasc, $cidade_id, $vacina_id);
	    
		$resultado->bind_result($usuariovacinado_id, $id, $nome, $mae, $nascimento);

		$resultado->execute();

		$resultado->store_result();

		$linhas = $resultado->num_rows;
		
		if ($linhas > 0) {

			$arr = array();

			$crip = new Criptografia();			
			
			while( $resultado->fetch() ) {
				
				$end = $crip->Cifrar("pagina=Adm/adicionarIntercorrencia".
						     "&id=$id&intercorrencia_id=$intercorrencia_id".
						     "&vacina_id=$vacina_id&usuariovacinado_id=$usuariovacinado_id&campanha_id=$campanha_id");
		
				if(!$mae) $mae = "<em><span style='color: #CCC'>Não Informada</span></em>";
				$arr[] = array('id'    			=> $id,
						'nome'  		=> "<a href='?$end'>$nome</a>",
						'mãe'   		=> $mae,
						'nascimento'		=> $data->InverterData($nascimento));
			}

			Html::CriarTabelaDeArray($arr);	
			
			$resultado->free_result();
			
			return $linhas;
		}
				
		$resultado->free_result();
		
		if($linhas == 0) {
            
			$this->ExibirMensagem('Nenhum dado foi encontrado');
            return $linhas;
        
		}
		
		if($linhas < 0) {
			
			$this->AdicionarMensagemDeErro("Algum erro ocorreu ao tentar buscar
				dados com o critério $pesquisa!");
				
			return false;
		}
	}
	//--------------------------------------------------------------------------
	
	public function RetornarCampoNome($tabela, $id)
	{
				
		$sql = "SELECT nome FROM `$tabela` WHERE id = $id";
		$b = $this->conexao->query($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$campo = $b->fetch_assoc(); $nome = $campo['nome'];
		
		$b->free_result();

		return Html::FormatarMaiusculasMinusculas($nome);
	}
	
	//----------------------------------------------------------------------
	
	public function ExibirFormularioConfirmarIntercorrencia($usuario_id , $vacina_id, $campanha_id = 0)
	{
        
        if( isset($_POST['campanha_id']) )
            $campanha_id = $_POST['campanha_id'];
        else if($campanha_id == 0 ) $campanha_id = 'semCampanha';

        
        
        if( isset($_POST['data_inicio']) )
            $data_inicio = $_POST['data_inicio'];
        
        else $data_inicio = '';
        
        if( isset($_POST['hora_inicio']) )
            $hora_inicio = $_POST['hora_inicio'];
        
        else $hora_inicio = '';
        
        if( isset($_POST['obs'])  && strlen($_POST['obs']) > 5)
            $obs = $_POST['obs'];
        
        else $obs = '';

        if( isset($_POST['intercorrencia_id'])  && strlen($_POST['intercorrencia_id']) > 5)
            $intercorrencia_id = $_POST['intercorrencia_id'];

        else $intercorrencia_id = '';
        		
        list( $nome, $mae, $nascimento) = $this->RetornarDadosDoUsuario($usuario_id);
		
        $vacina = $this->RetornarCampoNome('vacina', $vacina_id);
        
		?>
        <!-- =============================================================== -->
        <h3 align="center">Confirmar ocorrência de evento adverso</h3>
        <!-- =============================================================== --> 

        <fieldset>
            <legend>Indivíduo </legend>

    <div class="CadastroEsq">
			Nome:
		</div> 
		<div class='CadastroDir'>
            <input type="text" disabled="true" style="color: #000; width: 300px"
            value="<?php echo Html::FormatarMaiusculasMinusculas($nome) ?>" />
		</div>
		</p>
        <!-- =============================================================== --> 
        <p>
		<div class="CadastroEsq">
			Mãe:
		</div> 
		<div class='CadastroDir'>
            <input type="text" disabled="true" style="color: #000; width: 300px"
            value="<?php echo Html::FormatarMaiusculasMinusculas($mae) ?>" />
		</div>
		</p>
        <!-- =============================================================== --> 
        <p>
		<div class="CadastroEsq">
			Nascimento:
		</div> 
		<div class='CadastroDir'>
            <input type="text" disabled="true" style="color: #000; width: 100px"
            value="<?php
            $data = new Data; echo $data->InverterData($nascimento) ?>" /> 
		</div>
		</p>

        </fieldset>
        <fieldset>
            <legend>Dados</legend>
        <!-- =============================================================== --> 
		<div class="CadastroEsq">
			Campanha:
		</div> 
		<div class='CadastroDir'>
            <input type="text" disabled="true" style="color: #000; width: 300px"
            value="<?php if($campanha_id != 'semCampanha') echo $this->RetornarCampoNome('campanha', $campanha_id); else echo 'Rotina'; ?>" />
		</div>
        <!-- =============================================================== -->
		<div class="CadastroEsq">
			Vacina:
		</div>
		<div class='CadastroDir'>
            <input type="text" disabled="true" style="color: #000; width: 300px"
            value="<?php echo $vacina ?>" />
		</div>
        <!-- =============================================================== -->

        <!-- onclick=' alert(document.getElementById("intercorrencia_id").value );'>
            AbrirJanela("####/Uf/Pop?$querystring_exibir_detalhe_vacina",200, 200, 700, 460);

                     onclick='AbrirJanela("<?php //echo $this->url?>/Uf/Pop?pagina=exibirDetalhesIntercorrencia&intercorrencia_id="+document.getElementById("intercorrencia_id").value, 200, 200, 700, 460);'

            =============================================================== -->
        
        <form id='confirmarIntercorrencia' name='confirmarIntercorrencia'
			method='post' action='<?php echo $_SERVER['REQUEST_URI'] ?>'
            onsubmit="return (ValidarData(this.data_inicio, 'Data')
                      && VerificarSeDataMaiorQueHoje(this.data_inicio, this.data_inicio.value, <?php echo date("'d/m/Y'"); ?>)
                      && ValidarHora(this.hora_inicio, 'hora')
                      && ValidarHoraFutura(this.hora_inicio, this.hora_inicio.value, <?php echo date("'d/m/Y'"); ?>, data_inicio.value )
                      && ValidarCampoSelect(this.intercorrencia_id, 'evento adverso'))" >
        <!-- =============================================================== -->
        <div class="CadastroEsq">
			Eventos Adversos:
		</div>
		<div class='CadastroDir'>
		<?php
            if(isset($_SESSION['detalhesIntercorrencia_id'])) {
                $intercorrencia_id = $_SESSION['detalhesIntercorrencia_id'];
                echo "<script>GravarIntercorrenciaSelecionada($intercorrencia_id);</script>";
            }
            $this->SelectIntercorrencias($intercorrencia_id);

              $crip = new Criptografia;
              $qs = $crip->Cifrar('pagina=exibirDetalhesIntercorrencia');
        ?>

        <!-- onmousemove='IntercorrenciaSelecionada(document.getElementById("intercorrencia_id").value), Cifrar("pagina=exibirDetalhesIntercorrencia&intercorrencia_id="+document.getElementById("intercorrencia_id").value);'
            onmouseup="ExibirDetalhesIntercorrencia('<?php //echo $this->url?>/Uf/Pop?');" -->
            
        <button style="background-color:#e6e3e3; height:22px;" alt='detalhes' type="button"
             onclick="if(document.getElementById('intercorrencia_id').value > 0){ ExibirDetalhesIntercorrencia('<?php echo $this->url ?>');}" >
            <img  src="<?php echo $this->arquivoGerarIcone?>?imagem=detalhes" border='0' />
        </button>
		</div>
        <!-- =============================================================== -->
        <p>
		<div class="CadastroEsq">
			Data inicial do evento ocorrido:
		</div> 
		<div class='CadastroDir'>
		<input type="text" name="data_inicio" id="data_inicio" maxlength="10"
			value="<?php echo $data_inicio ?>" style="width:100px;"
            onblur="ValidarData(this); VerificarSeDataMaiorQueHoje(this, this.value, <?php echo date("'d/m/Y'"); ?>);"
            onkeypress="return Digitos(event, this);"
			onkeydown="return Mascara('DATA', this, event);"
			onkeyup="return Mascara('DATA', this, event);"
			 />
        
            &nbsp;&nbsp;&nbsp;&nbsp;Hora: <input type="text" name="hora_inicio" id="hora_inicio"
                maxlength="5" value="<?php echo $hora_inicio ?>"
                style="width:50px;"
                onblur="ValidarHora(this); ValidarHoraFutura(this, this.value, <?php echo date("'d/m/Y'"); ?>, data_inicio.value );"
                onkeypress="return Digitos(event, this);"
                onkeydown="return Mascara('HORA', this, event);"
                onkeyup="return Mascara('HORA', this, event);"
                />
		</div>
        
		</p>
        <!-- =============================================================== -->
		<p>
		<div class="CadastroEsq">
			Observação:
		</div> 
		<div class='CadastroDir'>
		<textarea name="obs" id="obs" style="width:450px;" cols="50" rows="5" ><?php
            echo $obs ?></textarea>
		</div>
		</p>

        </fieldset>
        <!-- =============================================================== -->
			
        <?php
					
		$botao = new Vacina;
		$botao->ExibirBotoesDoFormulario('Confirmar');
		$form = new Form;
		$form->BotaoVoltarHistorico();
		
        ?> 
        
		</form>        
            
        <?php    
	}
    //--------------------------------------------------------------------------
    public function VerificarNaoDuplicidadeDeIntercorrencia($usuarioVacinado_id,
            $intercorrencia_id, $dataInicio, $hora_inicio, $campanha_id = false)
    {
        $data = new Data();
               
        $dataInicio = $data->InverterData($dataInicio). " $hora_inicio";
        
        $usuarioIntercorrencia = 'usuariointercorrencia';
        $usuarioVacinado = 'UsuarioVacinado_id';
        if($campanha_id) {
            $usuarioIntercorrencia = 'usuariointercorrenciacampanha';
            $usuarioVacinado = 'UsuarioVacinadoCampanha_id';
        }

        $sql = "SELECT $usuarioVacinado, Intercorrencia_id, datahoraintercorrencia
                    FROM `$usuarioIntercorrencia`
                        WHERE $usuarioVacinado = ?
                        AND intercorrencia_id = ?
                        AND  datahoraintercorrencia = ?";
        
        $stmt = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
        
        $stmt->bind_param('iis', $usuarioVacinado_id, $intercorrencia_id, $dataInicio);
        
        $stmt->execute();
        $stmt->store_result();
        $existe = $stmt->num_rows;
        
        $stmt->free_result(); 
                
        if($existe > 0) {
            
            $this->AdicionarMensagemDeErro('Evento Adverso já cadastrado anteriormente');
            
            return false; // Já tem uma intercorrência registrada.
        }
        if($existe < 0) {
            $this->AdicionarMensagemDeErro('Algum problema ocorreu ao verificar
                a não duplicidade de registro de evento adverso para o indivíduo');
            
            return false;
        }
        
        return true; // Ainda não existe; pode registrar...
    }
    //--------------------------------------------------------------------------
    public function RegistrarIntercorrenciaOcorrida($usuario_id,
                                                    $intercorrencia_id,
                                                    $dataInicio,
                                                    $horaInicio,
                                                    $obs,
                                                    $campanha_id = false)
    {
       
        $data = new Data();
               
        $dataInicio = $data->InverterData($dataInicio). " $horaInicio:00";

        $tabelaUsuarioIntercorrencia = 'usuariointercorrencia'; // vacina ou campanha
        if($campanha_id) $tabelaUsuarioIntercorrencia = 'usuariointercorrenciacampanha';
        
        $sql = "INSERT INTO `$tabelaUsuarioIntercorrencia`
            VALUES(NULL, $usuario_id, $intercorrencia_id, '$dataInicio', NULL, '$obs')";
        
        $stmt = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
            
       // $stmt->bind_param('iiss', $usuario_id, $intercorrencia_id, $dataInicio, $obs);
        
        $stmt->execute();
		$inseriu = $stmt->affected_rows;
        
		$stmt->close();

        if($inseriu > 0) return $inseriu;

        return false;

    }

}