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


//require('tPessoa.php');
//require('tData.php');
//require('tVacina.php');

/**
 * PessoaVacinavel: Classe que representa uma pessoa vacinada.
 *
 * Esta classe abstrai pessoas vacinadas no sistema. Cada pessoa pode ter sido
 * vacinada por uma ou mais vacinas. Cada vacina poderá possuir intercorrências,
 * que são eventos adversos causados nas pessoas vacinadas por aquela vacina. As
 * intercorrências serão acessadas via $_vacina['bcg'][0], (para o 1o. evento
 * adverso da vacina BCG). Se nenhuma intercorrência existir para a vacina xyz,
 * então count($_vacina['xyz']) retorna zero.
 *
 *
 * @package Sivac/Class
 *
 * @author Douglas, v 1.0, 2008-10-27 16:58
 *
 * @copyright 2008 
 *
 */
class PessoaVacinavel extends Pessoa
{
	public $_vacinarHabilitado;       // Boolean
	public $_campoEstoqueHabilitado;  // Boolean
    
	private $_vacinas;              // array
	private $_prontuario;		// string
	private $_cartaoSus;		// string
	private $_dataHoraVacinacao;		// string
	private $_dependencia; 		// array
	private static $exibiuMensagemVerificarEstoque = false;
	private static $numeroDoReforco  = 1;

	//--------------------------------------------------------------------------
	public function ValidarDataRetroativa($dataRetroativa, $usuario_id, $vacina_id)
	{
		$data = new Data();
		//die($dataRetroativa);
		
		if($data->CompararData($dataRetroativa, '>=')) {
			

			
			$this->AdicionarMensagemDeErro('A data retroativa não pode ser posterior à data de hoje.');
						
			//die("entrou: $dataRetroativa");
			return false;
		}
		
		$stmt = $this->conexao->prepare('SELECT nascimento FROM `usuario` WHERE id = ? AND ativo');

		$stmt->bind_param('i', $usuario_id);
		$stmt->bind_result($nascimento);
		$stmt->execute();
		$stmt->fetch();
		$stmt->free_result();
		
		if($data->CompararData($dataRetroativa, '<', $nascimento)) {

			$this->AdicionarMensagemDeErro('A data retroativa não pode ser anterior à data de nascimento.');
			return false;
		}
		
		$stmt = $this->conexao->prepare('SELECT DATE(datahoravacinacao) AS `data`
										 FROM `usuariovacinado` 
										 WHERE Usuario_id = ?
										 AND Vacina_id = ? 
										 ORDER BY numerodadose DESC LIMIT 1');

		$stmt->bind_param('ii', $usuario_id, $vacina_id);
		$stmt->bind_result($dataVacinacao);
		$stmt->execute();
		$stmt->store_result();
		$existe = $stmt->num_rows;

		if( $existe > 0 ) {
			
			$stmt->fetch();
			$stmt->free_result();
			
			if($data->CompararData($dataRetroativa, '<', $dataVacinacao)) {
				
				$this->AdicionarMensagemDeErro('A data retroativa não pode ser anterior à data da última dose aplicada.');
				return false;
			}
		}
		$stmt->free_result();
		
		return true;
		
	}
	//--------------------------------------------------------------------------
	public function DataHoraVacinacao($usuario_id, $vacina_id, $numerodadose, $numerodociclo = 1)
	{
		$dataReal = $this->conexao->prepare('SELECT datahoravacinacao
												FROM `usuariovacinado`
													WHERE usuario_id = ?
													AND vacina_id = ? 
													AND numerodadose = ? 
													AND numerodociclo = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$dataReal->bind_param('iiii', $usuario_id, $vacina_id, $numerodadose, $numerodociclo);
		$dataReal->bind_result($datahoravacinacao);
		$dataReal->execute();

		$dataReal->fetch();
		$dataReal->free_result();

		if( isset($datahoravacinacao) && strlen($datahoravacinacao > 3)) {

			return $datahoravacinacao;
		}
		return false;
	}
	//--------------------------------------------------------------------------
	public function ExibirBuscaPorEstadoCidade()
	{
		?>
		
		<p>
		 	<div class='CadastroEsq'>Estado:</div>
		 	<div class='CadastroDir'>
		 	<select name="estado" id="estado" style="width:150px;
		  		margin-left:2px;"
		  		onchange="PesquisarCidades(this.value);"
		  		onblur="ValidarCampoSelect(this, 'estado')">
		 		<option value="0">- selecione -</option>
			<?php
		
			$resultado = $this->conexao->prepare('SELECT id, nome FROM `estado`')
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
			$resultado->execute();
		
			$resultado->bind_result($id, $nome);
		
			while( $resultado->fetch() ) {
		
				// Procedimentos para não perder a seleção que o usuário fez
				// antes (existe post)
				$selecionado = '';
				if( isset($_POST['estado']) ) {
					
					if ( isset( $_POST['cidade'] ) && $_POST['cidade'] != '' ) {
						echo "<script>PesquisarCidades( document.getElementById('estado').value, $_POST[cidade] )</script>";
					} else {
						echo "<script>PesquisarCidades( document.getElementById('estado').value )</script>";
					}
		
					if($_POST['estado'] == $id) {
						$selecionado = 'selected="true"';
					}
				}
				echo "<option value=\"{$id}\" {$selecionado}>" . $nome . "</option>";
			}
			$resultado->free_result();
		?>
		</select>
		 	</div>
		 </p>
		 <p>
		 	<div class='CadastroEsq'>Cidade:</div>
		 	<div class='CadastroDir'>
		 	<select name="cidade" id="cidade" style="width:150px;
		  		margin-left:2px;"
		  		onblur="ValidarCampoSelect(this, 'cidade')"></select>
		 	</div>
		 </p>
		<?php
	}
	
	//--------------------------------------------------------------------------
	public function ExibirNomeDaVacina($vacina_id = false)
	{
		//Maykon 2008-11-28
		
		if(!$vacina_id) {
			
			$crip = new Criptografia();
			
			parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
			
		}
		
		$vacina  = $this->conexao->prepare('SELECT nome FROM `vacina`
		WHERE id = ? AND ativo') 
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$vacina->bind_param('i', $vacina_id);
		$vacina->bind_result($nomeDaVacina);
		$vacina->execute();
		$vacina->fetch();
		$vacina->free_result();
		
		return $nomeDaVacina;
	}
	//--------------------------------------------------------------------------
	private function ExibirEsquemaDaVacina($vacina_id = false)
	{
		//Maykon 2008-11-28
		
		if(!$vacina_id) {
			
			$crip = new Criptografia();
			
			parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
			
		}
		
		$esquema  = $this->conexao->prepare('SELECT esquemadedosagem FROM `vacina`
			WHERE id = ? AND ativo') 
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$esquema->bind_param('i', $vacina_id);
		$esquema->bind_result($EsquemaDaVacina);
		$esquema->execute();
		$esquema->fetch();
		$esquema->free_result();
		
		return $EsquemaDaVacina;
	}
	//--------------------------------------------------------------------------
	public function ExibirDadosDeVacinacaoDoUsuario($usuario_id)
	{
		//Maykon 2008-11-28
		$nomeDaVacina = $this->ExibirNomeDaVacina();
		//////
		//Maykon 2008-12-02
		$esquemaDaVacina = $this->ExibirEsquemaDaVacina();
		//////
		
		$listar = $this->conexao->prepare('SELECT nome, mae, nascimento FROM `usuario`
			WHERE id = ? AND ativo') 
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$listar->bind_param('i', $usuario_id);
		$listar->bind_result($nome, $mae, $nascimento);
		$listar->execute();
		$listar->fetch();
		$listar->free_result();
		
		$data = new Data();

		$idade = $data->IdadeExata($nascimento);

		list($ano, $mes, $dia) = explode('/', $idade);

		$anoliteral = $ano ? "$ano ano(s)," : '';
		$mesliteral = $mes ? "$mes mes(es)," : '';
		$dialiteral = $dia ? "$dia dia(s)" : '';

		$nascimentoFormatoBR = $data->InverterData($nascimento);
		
		$crip = new Criptografia();
		
		parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));
		
		$endVacina = $crip->Cifrar("pagina=detalhesVacina&id=$vacina_id");
		
		//vacina id e campanha id serao criados a partir do parse_str
		$endPessoa = $crip->Cifrar("pagina=editarPessoa&id=$usuario_id&vacina_id=$vacina_id&campanha_id=$campanha_id&opener=$pagina");
		
		$url = str_replace('index.php', '', $_SERVER['PHP_SELF']);
		
		echo "<h3><a href='javascript: AbrirJanela(\"$url/Pop/?$endPessoa\",200, 200, 800, 700)'
			title='Conferir dados do indivíduo'>"
			.Html::FormatarMaiusculasMinusculas($nome) . "</a></h3>
			  <h4>Nascimento: $nascimentoFormatoBR - idade: 
			  $anoliteral $mesliteral $dialiteral</h4>";
		
		if (strlen($mae) <= 3) echo "<h4>Mãe: Não Informada<h4>";

		else echo "<h4>Mãe: " . Html::FormatarMaiusculasMinusculas($mae) . "</h4>";	
			
		
		echo "<h3><a href='javascript: AbrirJanela(\"$url/Pop/?$endVacina\",200, 200, 700, 460)'
			title='Exibir detalhes da vacina'>$nomeDaVacina</a>
			<span style='font-size: 12px; font-weight: normal'>
			(estoque: {$this->DosesDaUnidade($_SESSION['unidadeDeSaude_id'],
			$vacina_id)})</span></h3>";
		
		
		$dia = $data->ConverterIdadeExataParaDias($idade);

		$dados = array($nascimento, $ano);

		return $dados;
	}
	//--------------------------------------------------------------------------
	private function TestarSeDoseFoiAplicada($numeroDaDose, $usuario_id, $vacina_id, $numerodociclo)
	{
		$d = $this->conexao->query("SELECT id FROM `usuariovacinado`
			WHERE usuario_id = $usuario_id AND numerodadose = $numeroDaDose
			AND Vacina_id = $vacina_id AND numerodociclo = $numerodociclo")
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$resultado = $d->num_rows;
		
		$d->free_result();

		if($resultado > 0) return $resultado;
		
		if($resultado == 0) return false;

		if($resultado < 0) {
			
			$this->AdicionarMensagemDeErro("Algum erro ocorreu ao verificar se
				a dose $numeroDaDose foi aplicada.");
				
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function CriarArrayDeConfiguracoesDeDoses($vacina_id)
	{
        $sql = 'SELECT diaidealparavacinar, numerodadose, atrasomaximo, nome, dosebase '

             .     'FROM `intervalodadose`, `vacina` '

             .     'WHERE intervalodadose.Vacina_id = vacina.id '
		     .         'AND vacina.id = ? '
             .         'AND vacina.ativo ORDER BY numerodadose';

		$listar = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$listar->bind_param('i', $vacina_id);
        
		$listar->bind_result($diaidealparavacinar,
                             $numerodadose,
                             $atrasomaximo,
                             $nomeDaVacina,
                             $doseBase);

        $listar->execute();

		$arrDeIntervaloDeDoses = array();

		while($listar->fetch()) {

			$arrDeIntervaloDeDoses[] = array($diaidealparavacinar,
											 $numerodadose,
											 $atrasomaximo,
											 $nomeDaVacina,
                                             $doseBase);
		}

        $listar->free_result();

		return $arrDeIntervaloDeDoses;
	}
	//--------------------------------------------------------------------------
	public function ValidadeIndeterminadaDaDose($dataDaDose, $validade)
	{
		$maximoDeAnos = AgenteImunizador::IDADE_MAXIMA / 2;
		 
		$dataDaDose = str_replace("<font color='#BC1343'>", '', $dataDaDose);
		$dataDaDose = str_replace("</font>", '', $dataDaDose);
		
		$data = new Data();
		
		$dataDaDose = $data->InverterData($dataDaDose);

		$dataDaDose = $data->IncrementarData($dataDaDose, $maximoDeAnos, 'year');
		
		return $data->CompararData($dataDaDose ,'<=', $validade);
	}
	//--------------------------------------------------------------------------
	public function GerarArrayParaTabelaDeDoses($numerodadose, $usuario_id,
				$vacina_id, $textoParaDoseIdeal, $atraso,
				$textoParaNovaDoseIdeal, $novoAtraso, $campanha_id)
	{
		$novoAtrasoFormatoBR = '';

		static $linkVacinarHabilitado = true;
		
		$data = new Data();
		
		//Maykon 2008-11-28
		$atrasoFormatoBR = $data->InverterData($atraso);
		//

		if( isset($novoAtraso) && $novoAtraso != '-') {
			
			//Maykon 2008-11-28
			$novoAtrasoFormatoBR = $data->InverterData($novoAtraso);
			//

			$podeVacinar = $data->CompararData($novoAtraso, '>=');
		}
		elseif ($novoAtraso == '-') {
			$podeVacinar = true;
		}
		else $podeVacinar = false;
		
		
		///////////////////////////// Rafael - Maykon 03-12-2008
		
		$anoAtual = date('Y');
				
		if ($novoAtraso != '-' && $textoParaNovaDoseIdeal != '-') {
			
			if($this->ValidadeIndeterminadaDaDose($textoParaNovaDoseIdeal, $novoAtraso)) {
				$novoAtrasoFormatoBR = 'Indeterminado';
				
			}

		}
		
		
		//==================================================================
				
		if ($atraso != '-' && $textoParaDoseIdeal != '-') {
						
			if($this->ValidadeIndeterminadaDaDose($textoParaDoseIdeal, $atraso)) {
				$atrasoFormatoBR = 'Indeterminado';
			}

		}
		///////////
		
		$tomouVacina = $this->PessoaTomouVacina($usuario_id, $vacina_id);
		
		if(isset($atraso) && !$tomouVacina) {

			$podeVacinar = $data->CompararData($atraso, '>=');	
			
			if(!$podeVacinar && $linkVacinarHabilitado) {
				
				
				
				echo "<script>alert('{$this->BuscarNomeDaPessoa($usuario_id)} não deve tomar a '
					+ \"{$numerodadose}ª dose.\\nEsta deveria ter sido \"
					+ 'aplicada em {$data->InverterData($atraso)}.')</script>";
			}
		}
		
		$datahoravacinacao = $this->DataHoraVacinacao($usuario_id,
									$vacina_id, $numerodadose);

		$ultimaDose = false;

		$ultimaDose = $this->VerificarUltimaDose($usuario_id, $vacina_id);

		//=======
		$cicloAtual = $this->TotalDeCiclosCompletos($usuario_id, $vacina_id) + 1; //////???????????????
				//echo "<h1>terminar essa parada!!!";
				//echo "===>>",$cicloAtual;
		//=======
		
		if($this->TestarSeDoseFoiAplicada($numerodadose, $usuario_id, $vacina_id, $cicloAtual)) {
			
			//Maykon 2008-11-28

			list($dataDaVacinacao, $horaDaVacinacao) = explode(' ', $datahoravacinacao);
			
			$dataDaVacinacao = $data->InverterData($dataDaVacinacao);
			
			//$campoVacinar = "Vacinado em $dataDaVacinacao $horaDaVacinacao";
			
		
			$campoVacinar = "Vacinado em $dataDaVacinacao";
            $this->_campoEstoqueHabilitado = false;
			//
			
			//Alterado por Luiz 2008-11-17
			//$novoAtraso = '-';
			$textoParaNovaDoseIdeal = '-';
			//Fim alteracao
			
			// Maykon 2008-11-28
			$novoAtrasoFormatoBR = '-';
			//
			
		}
		elseif ($linkVacinarHabilitado) {	

			$novoAtrasoFormatoBR = '-'; //???????? coloquei agora teste!!!!
	
			if($this->DosesDaUnidade($_SESSION['unidadeDeSaude_id'], $vacina_id)) {
				
				$crip = new Criptografia();
	
				$qs = $crip->Cifrar("pagina=Adm/vacinar&numerodadose=$numerodadose"
								.	"&usuario_id=$usuario_id&vacina_id=$vacina_id"
								.	"&campanha_id=$campanha_id&ciclo=$cicloAtual");
	
				/*$campoVacinar = "<a href='{$_SERVER['PHP_SELF']}?$qs' 
								onclick=\"return confirm('Confirme a aplicação da {$numerodadose}ª dose.'
								+ '\\nUma dose será decrementada do estoque de sua unidade.')\">
								<img src='{$this->arquivoGerarIcone}?imagem=vacinar' 
								border='0' alt='Vacinar' /></a>";*/
				
				
				if($this->PermiteVacinar($usuario_id, $vacina_id, $numerodadose, $cicloAtual)) {
								
				$campoVacinar = "<input type='image'
								src='{$this->arquivoGerarIcone}?imagem=vacinar'
							 	name='aplicar'
							 	onclick=\"return confirm('Confirme a aplicação da {$numerodadose}ª dose.')\"
							 	/>";
							 
				}
				else {
					$campoVacinar = "<img src='{$this->arquivoGerarIcone}?imagem=vacinar_desab'
					border='0' alt='Vacinar (desabilitado)'
					title='Não pode ser aplicada a vacina por ter sido aplicada uma versão mais recente da mesma' />";
				}
				
				$_SESSION['query'] = $qs;				
				
				$linkVacinarHabilitado = false;
			}
			else {
								
				$campoVacinar = "<img src='{$this->arquivoGerarIcone}?imagem=vacinar_desab'
					border='0' alt='Vacinar (desabilitado)' />";
				
				if(!self::$exibiuMensagemVerificarEstoque) {
					self::$exibiuMensagemVerificarEstoque = true;
					echo '<script>alert("Verifique o estoque desta vacina.")</script>';
				}
			}
			
			
			/* Modificado por rafael e maykon em 10-03-2009
			
			if(!$podeVacinar) {
				
				$campoVacinar = "<img src='{$this->arquivoGerarIcone}?imagem=vacinar_desab'
					border='0' alt='Vacinar (desabilitado)' />";
				
				
				$qs = $crip->Cifrar("pagina=Adm/excluirUsuarioVacinado&usuario_id=$usuario_id&vacina_id=$vacina_id&campanha_id=$campanha_id");				
				
				?><script>

				
				var ok = confirm("A pessoa passou do prazo para esta vacina.\n"
						+ "A dose deveria ter sido aplicada em "
						+ "<?php echo $data->InverterData($novoAtraso)?>"
						+ "\n\nDeseja reiniciar o ciclo de vacinação?");
				
						
				if(ok) {
					window.location = '?<?php echo $qs?>';
				}
	
				</script>
				
				<?php

			}
			*/

		}
	
		else $campoVacinar = "<src='{$this->arquivoGerarIcone}?imagem=vacinar_desab'
					border='0' alt='Vacinar (desabilitado)' />";

		// Se for somente uma dose para esta vacina e a mesma foi aplicada, não
		// tem necessidade de aparecer "nova dose ideal"
		if( $this->VerificarRigidezDeAplicacao($vacina_id) == true && ($ultimaDose +1 == $numerodadose) ){

			$dataIdealPura = strip_tags($textoParaDoseIdeal);
			$dataIdealPura = $data->InverterData($dataIdealPura);

			$atrasoPuro = strip_tags($atraso);
			
			if ( $data->CompararData($dataIdealPura, '>') ||  ( !($atrasoPuro == '-') && ($data->CompararData($atrasoPuro, '<') ) ) ){
				
				$campoVacinar = "<img src='{$this->arquivoGerarIcone}?imagem=vacinar_desab'
					border='0' alt='Vacinar (desabilitado)' title='Período de aplicação expirado' />";

		
			}
						
		}		

        $textoNumeroDaDose = "{$numerodadose}ª";

        if( $this->VerificarTipoDaDose($vacina_id, $numerodadose) == 2) {

            static $numeroDoReforco = 1;

            $titulo = "A {$numerodadose}ª dose é o {$numeroDoReforco}º reforço";

            $textoNumeroDaDose      = $this->AplicarEstiloDoseDeReforco('R' . $numeroDoReforco++, $titulo);
            $textoParaDoseIdeal     = $this->AplicarEstiloDoseDeReforco($textoParaDoseIdeal, $titulo);
            $textoParaNovaDoseIdeal = $this->AplicarEstiloDoseDeReforco($textoParaNovaDoseIdeal, $titulo);
            $novoAtrasoFormatoBR    = $this->AplicarEstiloDoseDeReforco($novoAtrasoFormatoBR, $titulo);
            $atrasoFormatoBR        = $this->AplicarEstiloDoseDeReforco($atrasoFormatoBR, $titulo);
            $campoVacinar           = $this->AplicarEstiloDoseDeReforco($campoVacinar, $titulo);
        }

        elseif( $this->VerificarTipoDaDose($vacina_id, $numerodadose) == 3) {

            static $doseEspecial = 1;
            
            $titulo = "A {$numerodadose}ª dose é especial e só deve ser aplicada em casos específicos";
            
            $textoNumeroDaDose      = $this->AplicarEstiloDoseEspecial('E' . $doseEspecial++, $titulo);
            $textoParaDoseIdeal     = $this->AplicarEstiloDoseEspecial('(especial)', $titulo);
            $textoParaNovaDoseIdeal = $this->AplicarEstiloDoseEspecial('', $titulo);
            $novoAtrasoFormatoBR    = $this->AplicarEstiloDoseEspecial($novoAtrasoFormatoBR, $titulo);
            $atrasoFormatoBR        = $this->AplicarEstiloDoseEspecial($atrasoFormatoBR, $titulo);
            $campoVacinar           = $this->AplicarEstiloDoseEspecial($campoVacinar, $titulo);
        }

		if ($ultimaDose > 0) {
			

			$linhaDaTabelaDeDoses = array('dose'	=> $textoNumeroDaDose,
						   	'ideal para vacinar'	=> $textoParaDoseIdeal,
						  //'validade da dose'		=> $atraso,
						   	'nova ideal'	 		=> $textoParaNovaDoseIdeal,
						   	'nova validade'			=> $novoAtrasoFormatoBR,
						   	'aplicar'				=> $campoVacinar);

		}
		else {
			$linhaDaTabelaDeDoses = array('dose'	=> $textoNumeroDaDose,
						   	'ideal para vacinar'	=> $textoParaDoseIdeal,
						   	'validade da dose'		=> $atrasoFormatoBR,
						   	'aplicar'				=> $campoVacinar);
			
			

		}
		return $linhaDaTabelaDeDoses;
	}
    //--------------------------------------------------------------------------
    public function VerificarTipoDaDose($vacina_id, $numerodadose)
    {
        $sql = 'SELECT TipoDaDose_id
                    FROM `intervalodadose`
                        WHERE Vacina_id = ?
                        AND numerodadose = ?';

		$res = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$res->bind_param('ii', $vacina_id, $numerodadose);

        $tipoDaDose_id = false;
		$res->bind_result($tipoDaDose_id);
		$res->execute();

		$res->fetch();

		$res->free_result();

        return $tipoDaDose_id;
    }
	//--------------------------------------------------------------------------
	public function CalcularNovaDataIdeal($datahoravacinacao, $vacina_id, $numerodadose)
	{
		if( strlen($datahoravacinacao) > 5)  {

			$arr = $this->DiasIdeaisParaVacinar( $vacina_id, $numerodadose);

			if(substr_count($datahoravacinacao, ' ')) {
				//list($data, $hora) = explode(' ', $datahoravacinacao);
				$data = strtok($datahoravacinacao, ' /+.');
			}
			else {
				$data = $datahoravacinacao;
			}
			if(isset($data)) {

				list($diaidealparavacinar, $atrasomaximo) = $arr;

                $calcular = new Data();

                $novadata = $calcular->IncrementarData($data, $diaidealparavacinar);

				$atraso = $calcular->IncrementarData($novadata, $atrasomaximo);

				return array($novadata, $atraso);
			}
		}
		return false;
	}
	//--------------------------------------------------------------------------
	private function VerificarDoseBase($vacina_id, $numerodadose)
	{
        $doseBase = false;
        
        $sql = 'SELECT IF( dosebase > 0, dosebase, numerodadose ) as dosebase '
             .     'FROM `intervalodadose` '
             .     'WHERE Vacina_id = ? '
             .         'AND numerodadose = ?';

		$res = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$res->bind_param('ii', $vacina_id, $numerodadose);
		$res->bind_result($doseBase);
		$res->execute();

		$res->fetch();

        return $doseBase;
    }
	//--------------------------------------------------------------------------
	private function DiasIdeaisParaVacinar($vacina_id, $numerodadose)
	{
		$res = $this->conexao->prepare('SELECT diaidealparavacinar,	atrasomaximo
			FROM `intervalodadose`
			WHERE vacina_id = ?
			AND numerodadose = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$res->bind_param('ii', $vacina_id, $numerodadose);
		$res->bind_result($diaidealparavacinar, $atrasomaximo);
		$res->execute();

		$res->fetch();

		$arr = array($diaidealparavacinar, $atrasomaximo);

		$res->free_result();

		return $arr;
	}
	//--------------------------------------------------------------------------
	public function VerificarUltimaDose ($usuario_id, $vacina_id) {

        // Verifica qual foi a última dose aplicada:
		$res = $this->conexao->prepare('SELECT numerodadose FROM `usuariovacinado`
		WHERE usuario_id = ? AND vacina_id = ? ORDER BY id DESC LIMIT 1')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$res->bind_param('ii', $usuario_id, $vacina_id);
		$res->bind_result($numerodadose);
		$res->execute();
		$res->fetch();
		$res->free_result();

		if (isset ($numerodadose)) {

			return $numerodadose;
		}
		return 0;
	}
	//--------------------------------------------------------------------------
	public function VerificarUltimoCiclo($usuario_id, $vacina_id) {
		
		$res = $this->conexao->prepare('SELECT MAX(numerodociclo)  FROM `usuariovacinado`
		WHERE usuario_id = ? AND vacina_id = ? LIMIT 1')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$res->bind_param('ii', $usuario_id, $vacina_id);
		$res->bind_result($numerodociclo);
		$res->execute();
		$res->fetch();
		$res->free_result();

		if (isset ($numerodociclo)) {
			
			return $numerodociclo;
		}
		return 0;
		
	}
        //----------------------------------------------------------------------
        private function ExibirVacinarSimples($usuario_id, $vacina_id, $campanha_id)
        {
            // Se a pessoa já tomou as doses da vacina, não continua e TRUE faz
            // exibir a mensagem esclarecendo que todas as doses foram aplicadas
            
            $pessoaFoiImunizada =
                $this->PessoaFoiImunizada($usuario_id, $vacina_id, true, true);

            /*$sql = "SELECT MAX(numerodadose)
                        FROM `usuariovacinado`
                            WHERE numerodociclo =
                            (
                                SELECT MAX(numerodociclo)
                                    FROM `usuariovacinado`
                                        WHERE Vacina_id = $vacina_id
                                        AND Usuario_id = $usuario_id
                            )
                            AND Vacina_id = $vacina_id
                            AND Usuario_id = $usuario_id";

            Depurador::Pre($sql);

            $stmt = $this->conexao->prepare($sql);

            $numerodadose = 0;
            $stmt->bind_result($numerodadose);
            $stmt->execute();
            $stmt->fetch();
            $stmt->free_result();*/

            $ciclosPermitidos 		= $this->CiclosPermitidos($vacina_id);
            $totalDeDosesAplicadas	= $this->TotalDeDosesAplicadas($usuario_id, $vacina_id);
            $totalDeDoses           = $this->TotalDeDoses($vacina_id);
            $ciclosCompletos 		= $this->TotalDeCiclosCompletos($usuario_id, $vacina_id);

            //if($ciclosCompletos >= $ciclosPermitidos) return;

            $numerodadose = ($totalDeDosesAplicadas % $totalDeDoses)+1;

            $cicloAtual = $this->VerificarUltimoCiclo($usuario_id, $vacina_id);

            if(!$cicloAtual) $cicloAtual ++;
 
            if( $this->CicloFechado($usuario_id, $vacina_id, $cicloAtual)) $cicloAtual ++;

            $crip = new Criptografia();

            $queryStringSemRotina = "pagina=Adm/vacinar&numerodadose=$numerodadose"
                            .	"&usuario_id=$usuario_id&vacina_id=$vacina_id"
                            .	"&campanha_id=$campanha_id&ciclo=$cicloAtual";

            $queryStringComRotina = "pagina=Adm/vacinar&numerodadose=$numerodadose"
                            .	"&usuario_id=$usuario_id&vacina_id=$vacina_id"
                            .	"&campanha_id=$campanha_id&ciclo=$cicloAtual"
                            .   "&acrescentarEmRotina";

            parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));

            if( isset($acrescentarEmRotina) ) {

                $mensagemDeConfirmacao = '<h3><u>'
                                   . $this->RetornarCampoNome('campanha', $campanha_id)
                                   . '</u></h3>'
                                   . $this->RetornarCampoNome('usuario', $usuario_id)
                                   . ' vacinado(a) com sucesso em '
                                   . date('d/m/Y - H:i')
                                   . '<br /><div style="text-align: justify;'
                                   . 'border: 1px dashed #f8b080; padding: 8px;'
                                   . 'margin: 8px;">'
                                   . '<strong>Obs</strong>:<ul><li>'
                                   . '<em>Dose registrada também na vacina de '
                                   . 'rotina de '
                                   . $this->RetornarCampoNome('usuario', $usuario_id)
                                   . '. Este registro irá computar na caderneta '
                                   . 'de vacinação do indivíduo.</em></li></ul></div>';
            }
            else {

                $mensagemDeConfirmacao = '<h3><u>'
                                   . $this->RetornarCampoNome('campanha', $campanha_id)
                                   . '</u></h3>'
                                   . $this->RetornarCampoNome('usuario', $usuario_id)
                                   . ' vacinado(a) com sucesso em '
                                   . date('d/m/Y - H:i');
            }
            
            if(isset($foi_vacinado)) $this->ExibirMensagem($mensagemDeConfirmacao,
                                                       'Confirmação', 'onclick');

            $querySemRotina = $crip->Cifrar($queryStringSemRotina);
            $queryComRotina = $crip->Cifrar($queryStringComRotina);
                                        
            if( $pessoaFoiImunizada ) {
                $checkParaRotina = "<label><input name='incluirEmRotina'
                    id='incluirEmRotina' type='checkbox' disabled='true' />
                    <span style='color: #ccc'>Registrar também em rotina</span></label>";
            }
            else {
                $checkParaRotina = "<label><input name='incluirEmRotina'
                    id='incluirEmRotina' type='checkbox' />
                    Registrar também em rotina</label>";
            }


            $inputDataRetroativa = "<tr><td align='center' valign='middle'
                width='250' height='40'style='vertical-align: middle; 
                border: 1px solid #dae6ef' ><input type='text' value='digite a data'
                name='dataRetroativa' id='dataRetroativa' size='12' 
                onclick='if(this.value == \"digite a data\")this.value=\"\"'
                onkeydown='if(this.value == \"digite a data\")this.value=\"\"'
                style=' border:#7a7a7a solid 1px; '

                maxlength='10'
                onkeypress='return Digitos(event, this);'
                onkeydown='return Mascara(\"DATA\", this, event);'
                onkeyup='Mascara(\"DATA\", this, event);
                if(this.value.length == 0) this.value = \"digite a data\"
                if(this.value.length == 10) document.getElementById(\"codigoDaImagem\").focus();'
                onblur='if(this.value == \"\")this.value=\"digite a data\"; if(this.value != \"digite a data\") return ValidarData(this, true);'

                /> Data Retroativa</td></tr>";


            $campoAplicar = "<table cellpadding='0' cellspacing='0' align='center'>
                <tr><td onclick=\"this.style.backgroundColor='#b7cada';
                GravarNaSessao('dataRetroativa', document.getElementById('dataRetroativa').value);
                if( ((document.getElementById('dataRetroativa').value != 'digite a data'
                     &&  ValidarData(document.getElementById('dataRetroativa'), true))
                     || document.getElementById('dataRetroativa').value == 'digite a data')
                     && confirm('Confirme a aplicação da dose.')) {

                    if( document.getElementById('incluirEmRotina').checked ) {

                       window.location='?$queryComRotina';
                    }
                    else {

                       window.location='?$querySemRotina';
                    }
                }
                \"
                onmouseover=\"this.style.backgroundColor='#eaeff3'\"
                onmouseout=\"this.style.backgroundColor='#dae6ef'\"
                align='center' valign='middle' width='250' height='40'
                name='aplicar' style='vertical-align: middle; cursor:pointer;
                border: 1px solid #b8c8d9; background-color: #dae6ef'>
                <img alt=\"aplicar\" src='{$this->arquivoGerarIcone}?imagem=vacinar'/>
                Aplicar vacina da campanha</td></tr><tr>
                <td align='center' valign='middle' width='250' height='40'
                style='vertical-align: middle; border: 1px solid #dae6ef'>
                $checkParaRotina</td></tr>$inputDataRetroativa</table>";

           

            /*$arr[] = array('Dose' =>  $numerodadose . 'ª',
                            "Aplicar (ciclo $cicloAtual)" => $campoAplicar);
            */
            //$arr[] = array('  ' => '<b> Aplicar</b>', ' '=> $campoAplicar);
            
            /*$query = $crip->Cifrar("pagina=Adm/vacinar&numerodadose=$numerodadose"
                                            .	"&usuario_id=$usuario_id&vacina_id=$vacina_id"
                                            .	"&campanha_id=$campanha_id&ciclo=$cicloAtual");
            */
           // Depurador::Pre('Query sem rotina: ' . $crip->Decifrar($querySemRotina));
           // Depurador::Pre('Query com rotina: ' . $crip->Decifrar($queryComRotina));
            
            // Precisa do FORM? echo "<form name='form' method='post' action='?$query'>";
                
                echo $campoAplicar; //Html::CriarTabelaDeArray($arr, false, false, false, 300);

                echo '<div style="padding-left:90px; padding-top:20px;">';

			//$this->ListarObs($usuario_id, $vacina_id);
                        //$nomeDaCampanha = $this->RetornarCampoNome('campanha', $campanha_id);
                        //echo "<input name='obs' type='hidden' value='$nomeDaCampanha '>";

                        $this->ListarObsCampanha($usuario_id, $campanha_id, $vacina_id);
                    
                echo '</div>';
	    // Precisa do FORM? echo '</form>';

        unset($_SESSION['dataRetroativa']);

        }

	//--------------------------------------------------------------------------
	public function ListarDosesIdeais($usuario_id, $vacina_id, $campanha_id)
	{
        // Verifica se o tipo de formulário a ser exibido é um simples,
        // com uma única dose, sem tratar que dose é, nem datas
        // anteriores e subsequentes:
       /* ddd if( $campanha_id && $this->VerificarSeDoseUnicaEmCampanha($vacina_id) ) {

            $this->ExibirVacinarSimples($usuario_id, $vacina_id, $campanha_id);
            return; // Pára aqui e não continua esse metodãozão
        }

		$cicloAtual = $this->VerificarUltimoCiclo($usuario_id, $vacina_id);
		if( $this->CicloFechado($usuario_id, $vacina_id, $cicloAtual) )
		$cicloAtual ++;
		
		list($nascimento, $anos) = $this->ExibirDadosDeVacinacaoDoUsuario($usuario_id);

		$arrDeIntervaloDeDoses = $this->CriarArrayDeConfiguracoesDeDoses($vacina_id);

		$this->PessoaFoiImunizada($usuario_id, $vacina_id, true);
		
		if( isset($campanha_id) && $campanha_id <> '' )
				$this->VacinadoPelaCampanha($usuario_id, $vacina_id, $campanha_id);
		
		$dataDaDose = $nascimento;

		$data = new Data();

		$arrParaTabelaDeDoses = array();
		$dataAnterior = false;
		$novaData = false;
		$novaDataIdeal = false;
		//alterado por Luiz

		$atrasoAcumulado = 0;

		//fim da alteracao

		//alterado por rafael

		$novaIdadeIdeal = false;

		//fim da alteracao


		foreach ($arrDeIntervaloDeDoses as $intervaloDaDose) {

			/**
			 * Na primeira iteração, sem vacinar, as únicas variáveis que existem
			 * são $nascimento, $anos e $dataDaDose. (1a. linha da tabela)
			 * -----------------------------------------------------------------
			 * Já na segunda iteração (2a. linha), sem vacinar, não estão setadas
			 * as variáveis $dataAnterior, $novoAtraso, $textoParaNovaDoseIdeal,
			 * $datahoravacinacao, $novaDataIdeal (que gera $novoAnoIdeal, $novoMesIdeal
			 * e $novoDiaIdeal)
			 * -----------------------------------------------------------------
			 * $dataDaDose inicialmente recebe $nascimento, e no final de cada
			 * iteração, recebe a $novaData para que faça novos cálculos. Os
			 * valores recebidos são do tipo "1997/11/11"
			 * -----------------------------------------------------------------
			 * $novaData recebe a $dataDaDose incrementada com a quantidade de
			 * dias ($diaidealparavacinar) para formar a data ideal para a pessoa
			 * ser vacinada.
			 * -----------------------------------------------------------------
			 * $textoParaDoseIdeal e $textoParaNovaDoseIdeal são a combinação de
			 * uma data e a idade ideal para vacinar, sendo que se não for
			 * compatível com a idade da pessoa, se apresenta em vermelho.
			 * -----------------------------------------------------------------
			 * $atraso armazena sempre a data de atraso da iteração
			 * anterior (existe vacinando ou não), enquanto que $novoAtraso só
			 * existirá quando o usuário for vacinado. $novoAtraso é baseado na
			 * $datahoravacinacao somado com o atraso em dias.
			 * -----------------------------------------------------------------
			 * Quando a pessoa é vacinada por alguma dose, a coluna que exibe os
			 * atrasos com base na dose ideal some, e a variável responsável por
			 * exibir esses atrasos ($atraso) não é usada nesta iteração (só na
			 * próxima).
			 * -----------------------------------------------------------------
			 *
			 */
/*
			echo "<hr />";
			echo "<h2>Iteração ". $i++.'</h2>';
			echo "\$dataAnterior: $dataAnterior<br />"; // se foi vacinado é a $datahoravacinacao e senão é a $novaDataIdeal
			echo "\$nascimento: $nascimento<br />"; // Data de nascimento do camarada
			echo "\$anos: $anos<br />"; // A idade do camarada
			echo "\$dataDaDose: $dataDaDose<br />"; // Data Ideal para vacinar baseada na ideal anterior
			echo "\$diaidealparavacinar: $diaidealparavacinar<br />"; // Numero de dias buscado no banco de dados na tabela intervalo da dose
			echo "\$numerodadose: $numerodadose<br />"; // Numero da dose buscado no banco de dados na tabela intervalo da dose
			echo "\$atrasomaximo: $atrasomaximo<br />"; // buscado no banco de dados na tabela intervalo da dose (em dias)
			echo "\$novaData: $novaData<br />"; // É a dataDaDose somada com o numero de dias de determinada dose
			echo "\$idadeIdeal: $idadeIdeal<br />"; // Idade exata da pessoa no formato anos/meses/dias
			echo "\$diaIdeal: $diaIdeal<br />"; // Não ta sendo usado
			echo "\$mesIdeal: $mesIdeal<br />"; // Não ta sendo usado
			echo "\$anoIdeal: $anoIdeal<br />"; // Sendo usado para apresentar a idade aproximada em anos para tomar determinada dose
			echo "\$atraso: $atraso<br />"; // Usado só qunado o camarada num ta vacinado. Mostra o atraso maximo baseado na dose ideal
			echo "\$novoAtraso: $novoAtraso<br />"; // É o atraso maximo baseado na nova dose ideal (calculado a partir do momento de vacinação)
			echo "\$ultimaDose: $ultimaDose<br />"; // Numero da ultima dose que a pessoa tomou
			echo "\$textoParaNovaDoseIdeal: $textoParaNovaDoseIdeal<br />"; // É o texto que mostra a data e "aprox. aos X anos" da nova dose ideal
			echo "\$textoParaDoseIdeal: $textoParaDoseIdeal<br />"; // É o texto que mostra a data e "aprox. aos X anos" da dose ideal fixa
			echo "\$datahoravacinacao: $datahoravacinacao<br />"; // Data e hora da aplicação da ultima dose
			echo "\$novaDataIdeal: $novaDataIdeal<br />";// É a nova data ideal de aplicação
			echo "\$novoAnoIdeal: $novoAnoIdeal<br />"; // Serve para apresentar a idade em anos da pessoa, Novo mesIdeal e novoDiaIdeal não estão sendo usados
*/
			/* ddd
			
			list($diaidealparavacinar, $numerodadose,
				$atrasomaximo, $nomeDaVacina) = $intervaloDaDose;

			$novaData = $data->IncrementarData($dataDaDose, $diaidealparavacinar);

			$idadeIdeal = $data->IdadeExata($nascimento, $novaData);

			$anoIdeal = strtok($idadeIdeal, ' /+.');

			/*
			//alterado por Luiz 2008-11-17
			$atrasoAcumulado += $atrasomaximo;

			//$atraso = $data->IncrementarData($novaData, $atrasomaximo);
			$atraso = $data->IncrementarData($novaData, $atrasoAcumulado);
			//fim da alteracao de Luiz
			*/
			
			//alterado por maykon 2008-11-26 tentando fazer funcionar
			/*ddd$atrasoAcumulado = $atrasomaximo;

			$atraso = $data->IncrementarData($novaData, $atrasoAcumulado);
			//fim da alteracao de maykon
			

			$textoParaDoseIdeal = $this->TextoParaDoseIdeal($novaData, $anoIdeal,
				$anos);
				
			

			$ultimaDose = $this->VerificarUltimaDose($usuario_id, $vacina_id);

			$textoParaNovaDoseIdeal =  $novoAtraso = '-';

			$datahoravacinacao = $this->DataHoraVacinacao($usuario_id,
			$vacina_id, $numerodadose, $cicloAtual);

			if( $dataAnterior &&  $atraso ) {

				list($novaDataIdeal, $novoAtraso) =
					$this->CalcularNovaDataIdeal($dataAnterior, $vacina_id, $numerodadose);

				$novaIdadeIdeal = $data->IdadeExata($nascimento, $dataAnterior);

				$novoAnoIdeal = strtok($novaIdadeIdeal, ' /+.');

				$textoParaNovaDoseIdeal = $this->TextoParaDoseIdeal($novaDataIdeal, $novoAnoIdeal,
				$anos);
			}

			$arrParaTabelaDeDoses[] = $this->GerarArrayParaTabelaDeDoses($numerodadose,
					$usuario_id, $vacina_id, $textoParaDoseIdeal, $atraso,
					$textoParaNovaDoseIdeal, $novoAtraso, $campanha_id);


			if($datahoravacinacao) {
				$dataAnterior = $datahoravacinacao;
				$novoAnoIdeal = strtok($novaIdadeIdeal, ' /+.');

			}
			else {
				$dataAnterior = $novaDataIdeal;
				$dataDaDose = $novaDataIdeal;
			}

			$dataDaDose = $novaData;
		}

		
		// Esse foreach é para contar se o número de "Indeterminado" é igual ao
		// número de linhas da tabela (para mais tarde remover a coluna):
		foreach ($arrParaTabelaDeDoses as $linha) {

			static $qtd_sem_validade = 0;
			
			// Macete terrível para não dar warning de variável não setada:
			if(!isset($linha['validade da dose'])) $linha['validade da dose'] = 1;
			if(!isset($linha['nova validade'])) $linha['nova validade'] = 1;
			
			if($linha['validade da dose'] == 'Indeterminado'
				|| $linha['nova validade'] == 'Indeterminado') {
					
					$qtd_sem_validade++;
			}
		}
		
	
		// Verifica se a coluna de validade deve ser removida ou não:
		if( isset($qtd_sem_validade) &&
			$qtd_sem_validade == count($arrParaTabelaDeDoses) ) {
			
			foreach (array_keys($arrParaTabelaDeDoses) as $chave) {
			
				if( isset($arrParaTabelaDeDoses[$chave]['validade da dose']) ) {
					unset($arrParaTabelaDeDoses[$chave]['validade da dose']);
				}
				
				if( isset($arrParaTabelaDeDoses[$chave]['nova validade']) ) {
					unset($arrParaTabelaDeDoses[$chave]['nova validade']);
				}
			
			}
		}
				
		// Debug para verificar a qtd de "Indeterminado" que existe no array:
		// echo "<p>\$qtd_sem_validade=$qtd_sem_validade; "
		//	 . count($arrParaTabelaDeDoses) . "</p>";
		
		if( isset($novoAtraso) && $novoAtraso != '-') {
			
			$podeVacinar = $data->CompararData($novoAtraso, '>=');
		}
		else {
			
			$podeVacinar = false;
		}
		
				
		///#######################
		//====================================
		
		$ciclosPermitidos 		= $this->CiclosPermitidos($vacina_id);
		$totalDeDosesAplicadas	= $this->TotalDeDosesAplicadas($usuario_id, $vacina_id);
		$totalDeDoses 			= $this->TotalDeDoses($vacina_id);
		$ciclosCompletos 		= $this->TotalDeCiclosCompletos($usuario_id, $vacina_id);

        /*
		echo "<h3>Doses permitidas ",    $totalDeDoses,
		 	 "<br /> Total de doses " ,  $totalDeDosesAplicadas, 
			 "<br />Ciclos permitidos ", $ciclosPermitidos, 
			 "<br />Ciclos completos ",  $ciclosCompletos,
			 "<br />Última dose ",		 $ultimaDose,
			 "</h3>";*/
		
		/*$numeroDaNovaDose = $totalDeDosesAplicadas - ($totalDeDoses * $ciclosCompletos);
		if(!$numeroDaNovaDose) $numeroDaNovaDose = 1;*/
		
		//$numeroDaNovaDose = $this->VerificarUltimaDose($usuario_id, $vacina_id) +1;
		/*ddd
		$obsDose =  ($totalDeDosesAplicadas % $totalDeDoses)+1;
		
		$CampoAplicardeDeNovo = "<input type='image' src='{$this->arquivoGerarIcone}?imagem=vacinar' 
		 name='aplicar' onclick=\"return confirm('Confirme a aplicação da {$obsDose}ª dose.')\" />";
		
		 
		//====================================
		///#######################
		$contadorNovaValidade = 0;
		foreach (array_keys($arrParaTabelaDeDoses) as $chave)
		{

			if( $chave['nova validade'] == '-' || $chave['nova validade'] == '' ){
				$contadorNovaValidade ++;
			}
			
			if( isset($campanha_id) &&
		   ctype_digit($campanha_id) &&
		   $campanha_id > 0 ) {
					
					unset($arrParaTabelaDeDoses[$chave]['ideal para vacinar']);
					unset($arrParaTabelaDeDoses[$chave]['validade da dose']);
						
			}
			
		}
		
		//Se todas as posições do array na casa "nova validade" forem "-" ou vazio
		//esta coluna será removida do array
		if( $contadorNovaValidade == count($arrParaTabelaDeDoses)){
			foreach (array_keys($arrParaTabelaDeDoses) as $chave)
				unset($arrParaTabelaDeDoses[$chave]['nova validade']);
		}
		
		// Se o paciente já tomou todas as doses, remover as colunas "nova ideal"
		// e "nova validade", que estarão somente com traços "-":
		if ( $this->PessoaFoiImunizada($usuario_id, $vacina_id) ) {
			
			foreach (array_keys($arrParaTabelaDeDoses) as $chave) {
				
				//// [1] é igual ao numero da dose
				
				//print_r($arrDeIntervaloDeDoses[$chave]);
				
				if($obsDose > $arrDeIntervaloDeDoses[$chave][1]) {
					$horavacinacao = $this->DataHoraVacinacao($usuario_id,
											$vacina_id, $numerodadose-1, $ciclosCompletos);
					
					$horavacinacao = $data->InverterData($horavacinacao);						
					$aplicardeDeNovo = "Vacinado em ".strtok($horavacinacao, ' ');
					//echo 'ja foi' ;
				}
				if($obsDose == $arrDeIntervaloDeDoses[$chave][1]) {
					$aplicardeDeNovo = $CampoAplicardeDeNovo;
					//echo 'aplicar' ;
				}
				if($obsDose < $arrDeIntervaloDeDoses[$chave][1]) {
					
					$aplicardeDeNovo = "<img src='{$this->arquivoGerarIcone}?imagem=vacinar_desab'
					border='0' alt='Vacinar (desabilitado)' />";
				
					//echo 'ainda vai' ;
				}
				

				unset($arrParaTabelaDeDoses[$chave]['nova ideal']);
				unset($arrParaTabelaDeDoses[$chave]['nova validade']);
				
				//$arrParaTabelaDeDoses[$chave]['Aplicação'] = $arrParaTabelaDeDoses[$chave]['aplicar'];
				if($ciclosCompletos >= $ciclosPermitidos) {
					unset($arrParaTabelaDeDoses[$chave]['aplicar']);
					
					$datahoravacinacao = $this->DataHoraVacinacao($usuario_id,
					$vacina_id, $arrDeIntervaloDeDoses[$chave][1], $ciclosCompletos); // pega a data da vacinacao

                    $arrParaTabelaDeDoses[$chave]['aplicação'] = "Vacinado em ". $data->InverterData(strtok($datahoravacinacao, ' '));

                    $numeroDaDoseAtual = $chave + 1;
                    if( $this->VerificarTipoDaDose($vacina_id, $numeroDaDoseAtual) == 2) {


                        $stringMarronzinha = $this->AplicarEstiloDoseDeReforco('Vacinado em '
                                 . $data->InverterData(strtok($datahoravacinacao, ' ')));

                        $arrParaTabelaDeDoses[$chave]['aplicação'] = $stringMarronzinha;
                    }
					
				}else {
					//$arrParaTabelaDeDoses[$chave]['aplicar'] = $arrParaTabelaDeDoses[$chave]['aplicar'];
					unset($arrParaTabelaDeDoses[$chave]['aplicar']);
					$arrParaTabelaDeDoses[$chave]["aplicar (ciclo {$cicloAtual})"] = $aplicardeDeNovo;
				}
				
			}
			
			//echo '<pre>'; print_r($arrParaTabelaDeDoses); echo '</pre>';
		}

		$crip = new Criptografia();
		
		$query = false;
		if(isset($_SESSION['query'])) $query = $_SESSION['query'];
		//echo $crip->Decifrar($query);
		echo "<form name='form' method='post' action='?$query' 
			 onsubmit='if(this.checkRetroativo.checked == true)
			 		return (ValidarData(this.dataRetroativa))'>";
		
		
		echo '<div style="width:645px;  margin:auto">';
		
		//print_r($arrParaTabelaDeDoses);
		
		if( isset($campanha_id) && ctype_digit($campanha_id) && $campanha_id > 0 ) {
		
			Html::CriarTabelaDeArray($arrParaTabelaDeDoses, false, false, false, 300 );
		    echo '<hr style="width:350px" />';
                    print_r($arrParaTabelaDeDoses);
		
		} else {
		
		    Html::CriarTabelaDeArray($arrParaTabelaDeDoses);
			echo '<hr />';
		
		}
		
		echo '</div>';
		
		//echo '<pre>'; print_r($arrParaTabelaDeDoses); echo '</pre>';
		
		/////---------- ??????? Ta usando pra que?
		$crip = new Criptografia();

		$qs = $crip->Cifrar("usuario_id=$usuario_id&vacina_id=$vacina_id");
		////-----------

		$pessoaImunizada = $this->PessoaFoiImunizada($usuario_id, $vacina_id);
		
		//=========================
		
		
		echo '<div style="padding-left:90px; padding-top:20px;">';

                
		$this->ListarObs($usuario_id, $vacina_id);
		
		if(($ciclosCompletos < $ciclosPermitidos)) {
		//if($ultimaDose != $numerodadose){
			//$obsDose = $ultimaDose+1;
			$cicloAtual = $ciclosCompletos + 1;
			echo "<fieldset style='width:625px;'><legend>Observação para a {$obsDose}ª dose do ciclo {$cicloAtual}</legend>";
			echo '<textarea name="obs" id="textarea" rows="2" style="width:620px; border: 0px;"></textarea></fieldset></div>';
		}
		echo '</form>';
*/ 
        $this->ListarDoses($usuario_id, $vacina_id, $campanha_id);
	}

//------------------------------------------------------------------------------

    /**
     * Retorna o número do ciclo atual
     *
     * @param int $vacina_id
     */
    public function CicloAtual($usuario_id, $vacina_id)
    {
        $cicloAtual = $this->VerificarUltimoCiclo($usuario_id, $vacina_id);

        if(!$cicloAtual) $cicloAtual ++;

        if( $this->CicloFechado($usuario_id, $vacina_id, $cicloAtual)) $cicloAtual++;

        return $cicloAtual;
    }
//------------------------------------------------------------------------------

    /**
     * Se todos os intervalos de dose são indeterminados, então não é necessário
     * listar a coluna "Validade da dose" na tabela para vacinar.
     *
     * @param int $vacina_id
     */
    public function BotaoAplicarVacina($dose, $vacina_id)
    {
        //echo '<pre>';
        $arr = $this->CriarArrayDeConfiguracoesDeDoses($vacina_id);
        list($diaIdealPraVacinar,,$validadeDaDose) = $arr[$dose-1];
        
        $data = new Data;
        
        $this->_dataHoraVacinacao = $data->IncrementarData($this->_dataHoraVacinacao, $diaIdealPraVacinar);
        
        
       //  print_r($arr[$dose-1]);
       // if(  $data->CompararData( $data->IncrementarData($this->_dataHoraVacinacao, $validadeDaDose), '>')) echo $data->CompararData( $data->IncrementarData($this->_dataHoraVacinacao, $validadeDaDose), '>');

       // $this->_dataHoraVacinacao;

       // echo "$diaIdealPraVacinar,,$validadeDaDose";

       // echo "<script>alert('Antes ------>{$this->_vacinarHabilitado}')</script>";

        if($this->VerificarRigidezDeAplicacao($vacina_id) && !$data->CompararData( $data->IncrementarData($this->_dataHoraVacinacao, $validadeDaDose), '>')){
            $this->_vacinarHabilitado = false;
            $title = 'Deveria ter sido aplicada';
        }

       //echo "<script>alert(' depois ------> {$this->_vacinarHabilitado}')</script>";

        $icone = 'vacinar';
        if(!$this->_vacinarHabilitado) $icone = 'vacinar_desab';

        $title = 'Aplicar vacina';
        
        $botao = "<input type='image' "
               . "src='{$this->arquivoGerarIcone}?imagem=$icone' "
		       . "name='Aplicar' "
		       . "id='Aplicar' "
		       . "title='$title' "
               . "onclick=\"return confirm('Confirme a aplicação da {$dose}ª dose.')\" "
               . ($this->_vacinarHabilitado ? '' : 'disabled="true" style="cursor:default" ')
               . "/>";

        $this->_vacinarHabilitado = false;

        return $botao;

    }
//------------------------------------------------------------------------------

    /**
     * Se todos os intervalos de dose são indeterminados, então não é necessário
     * listar a coluna "Validade da dose" na tabela para vacinar.
     *
     * @param int $vacina_id
     */
    public function ListarValidadeDaDose($vacina_id)
    {
        $arrConfiguracoes = $this->CriarArrayDeConfiguracoesDeDoses($vacina_id);

        foreach($arrConfiguracoes as $configuracao)
        {
            // Só é necessário o terceiro elemento do Array:
            list(, , $diasAtrasoMax) = $configuracao;

            // 43800 indica validade indeterminada, no banco:
            if( $diasAtrasoMax < 43800 )
            {
                return true;
            }
        }

        return false;
    }

//------------------------------------------------------------------------------

    /**
     * Método remodelado para listar doses ideais para vacinar, baseadas em
     * campos não calculados, já gravados no banco.
     *
     * @param int $usuario_id
     * @param int $vacina_id
     * @param int $campanha_id
     */
    public function ListarDoses($usuario_id, $vacina_id, $campanha_id)
    {
        

        // Array que recebe dose, data ideal, validade da dose...
        $arrDoses = array();

        // Retorna array bidimensional com os seguintes dados:
        // - $diasIdeal
        // - $dose
        // - $diasAtrasoMax
        // - $nomeVacina
        $arrConfiguracoes = $this->CriarArrayDeConfiguracoesDeDoses($vacina_id);

        // Recupera o nascimento do usuario
        list($nascimento) = $this->ExibirDadosDeVacinacaoDoUsuario($usuario_id);
        $this->_dataHoraVacinacao = $dataUltimaDose = $nascimento;

       $data = new Data();

        // Verifica se precisa listar a coluna "Validade da dose" para o usuário
        $listarValidade = $this->ListarValidadeDaDose($vacina_id);

        $this->_vacinarHabilitado = true;
 
        // Guarda as doses ideais baseadas no nascimento. Útil quando a data ideal
        // para vacinar não se baseia na anterior, mas na dose base:
        $datasDosesIdeais = array();

        $novaDataDoseIdeal = 0;

        $i = 0; // Contador para aplicar estilo à linha inteira:
        foreach($arrConfiguracoes as $configuracao)
        {
            list($diasIdeal, $dose, $diasAtrasoMax, $nomeVacina, $doseBase) = $configuracao;

            $tipoDaDose = $this->VerificarTipoDaDose($vacina_id, $dose);
            
            if($diasAtrasoMax == '43800') $diasAtrasoMax = 'Indeterminado';
            //if($diasAtrasoMax == '43800') $dataAtrasoMax = 'Indeterminado';

            // Cria o botão de aplicar vacina:
            if($dose > $this->VerificarUltimaDose($usuario_id, $vacina_id))
            {
                $botaoAplicar = $this->BotaoAplicarVacina($dose, $vacina_id);
            }
            else
            {
                // Pega data/hora e aproveita só a data, invertendo em seguida:
                list($dataVacinacao) = explode(' ',
                      $this->DataHoraVacinacao($usuario_id, $vacina_id, $dose));

                $this->_dataHoraVacinacao = $dataVacinacao; 
                $dataVacinacao = $data->InverterData($dataVacinacao);

                // Então o botão "aplicar" dá lugar a data de vacinação:
                $botaoAplicar = "Vacinado em $dataVacinacao";
                $this->_campoEstoqueHabilitado = false;
            }



            $dataAtrasoMax = $diasAtrasoMax;
            if( $diasAtrasoMax != 'Indeterminado' ) $dataAtrasoMax = $data->IncrementarData($dataUltimaDose, $diasIdeal+$diasAtrasoMax);

            // Verifica se a doseBase é a padrão, ou seja: a DOSE ANTERIOR
            if( $doseBase == 0 )
            {
                $datasDosesIdeais[$dose] = $data->IncrementarData($dataUltimaDose, $diasIdeal);
            }
            
            // Se não for a anterior, incrementa os dias baseando-se na data da
            // dose base (no caso de hepatite B, a 3a. dose é 180 dias após a 1a)
            else
            {
                $datasDosesIdeais[$dose] = $data->IncrementarData($datasDosesIdeais[$doseBase], $diasIdeal);
            }
            
            $dataDoseIdeal = $data->InverterData($datasDosesIdeais[$dose]);
            if( $dataAtrasoMax != 'Indeterminado' ) $dataAtraso = $data->InverterData($dataAtrasoMax);
            else $dataAtraso = $dataAtrasoMax;

            $arrProximaDose = $this->ProximaDose($usuario_id, $vacina_id);

            if( $arrProximaDose )
            {
                list($proximaDose, $ultimaDoseAplicada) = $arrProximaDose;
            
                $proximaDosePosterior = $ultimaDoseAplicada + 2;

                if($ultimaDoseAplicada+1 == $dose) $novaDataDoseIdeal = $proximaDose;
                elseif($proximaDosePosterior <= $dose) $novaDataDoseIdeal = $data->IncrementarData($proximaDose, $diasIdeal);
                else $novaDataDoseIdeal =  ' * ';

                //-------
                if($doseBase > 0){
                    list($novaDataDoseIdeal) = explode(' ',
                        $this->DataHoraVacinacao($usuario_id, $vacina_id, $doseBase));

                   /*

                    Codigo antigo:
                    $novaDataDoseIdeal = $data->IncrementarData($novaDataDoseIdeal, $diasIdeal);

                    Adicionei esse if abaixo prq tava dando erro em tdata 140... a "$novaDataDoseIdeal"
                    as vezes ia vazia e dava erro!

                    Maykon | 25-11-2010

                    */

                   // ###########
                   if(strlen($novaDataDoseIdeal) > 0)
                        $novaDataDoseIdeal = $data->IncrementarData($novaDataDoseIdeal, $diasIdeal);
                   else $novaDataDoseIdeal = '';
                   // ###########

                   if( $data->CompararData($novaDataDoseIdeal, '<=', $dataUltimaVacinacao )
                        && $dataUltimaVacinacao > 0)
                    {
                        // Incrementa 60 dias a última dose aplicada (segundo Nádia)
                       $novaDataDoseIdeal = $data->IncrementarData($dataUltimaVacinacao, 60);
                    }
                }
                // data de aplicacao da dose
                list($dataUltimaVacinacao) = explode(' ', $this->DataHoraVacinacao($usuario_id, $vacina_id, $dose));

                //-------
                // Se for uma data, inverte para o formato brasileiro:
                if( strlen($novaDataDoseIdeal) == 10 )
                {
                    $novaDataDoseIdeal = $data->InverterData($novaDataDoseIdeal);
                }
            }

            // Caso não exista estoque da vacina na unidade
            if(!$this->VerificarEstoqueDaUnidadeParaRotina($vacina_id) && !substr_count($botaoAplicar, 'vacinar_desab')) $botaoAplicar = 'Vacina sem estoque';

            if( $listarValidade )
            {
                // Listando a coluna "Validade da dose":
                $arrDoses[$dose] = array('dose'          => $dose,
                                 //   'ideal para vacinar' => $diasIdeal,
                                    'data para vacinar'  => $dataDoseIdeal, //  baseado no nascimento
                                    'data validade'      => $dataAtraso,
                                  //  'validade da dose'   => $diasAtrasoMax,
                                    'nova data'          => $novaDataDoseIdeal,
                                    'aplicar'            => $botaoAplicar);
            }
            else
            {
                $arrDoses[$dose] = array('dose'          => $dose,
                                  //  'ideal para vacinar' => $diasIdeal,
                                    'data para vacinar'  => $dataDoseIdeal, //  baseado no nascimento
                                    'nova data'          => $novaDataDoseIdeal,
                                    'aplicar'            => $botaoAplicar);
            }

            if( $arrProximaDose ) unset($arrDoses[$dose]['data validade']);
            else                  unset($arrDoses[$dose]['nova data']);

            
            // Formatação da linha inteira para os textos e estilos de acordo
            // com o tipo de dose. ATENÇÃO: O método usa um ponteiro!
            $this->AplicarEstiloParaLinha($arrDoses[$dose], $tipoDaDose);

            $dataUltimaDose = $datasDosesIdeais[$dose];
        }

        if(substr_count(strtolower($nomeVacina), 'soro')){

            unset($arrDoses[$dose]['data para vacinar']);
            unset($arrDoses[$dose]['nova data']);
        }


       // echo '<pre>';
       // print_r($arrDoses);

        $this->ExibirFormVacinacao($arrDoses, $campanha_id, $usuario_id, $vacina_id);

    }
    
//------------------------------------------------------------------------------
	public function ProximaDose($usuario_id, $vacina_id) {

         $sql = "SELECT proximadose, numerodadose  FROM `usuariovacinado`
                    WHERE usuario_id = ?
                          AND vacina_id = ?
                        ORDER BY numerodadose DESC
                            LIMIT 1";
        
		$stmt = $this->conexao->prepare($sql)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        $proximadose = $numerodadose = false;
        
		$stmt->bind_param('ii', $usuario_id, $vacina_id);
		$stmt->bind_result($proximadose, $numerodadose);
		$stmt->execute();
		$stmt->fetch();
		$stmt->free_result();

        if( $proximadose && $numerodadose) return Array($proximadose, $numerodadose);

        return false;
	}
//------------------------------------------------------------------------------

    /**
     * Inicia novamente o número do reforço
     *
     */
    public static function ReiniciarReforco()
    {
        self::$numeroDoReforco = 1;
    }
//------------------------------------------------------------------------------

    /**
     * Aplica o estilo para a linha da dose, de acordo com o seu tipo:
     *
     * @param Array $linha
     * @param int $tipoDaDose
     */
    public function AplicarEstiloParaLinha(&$linha, $tipoDaDose)
    {
        switch( $tipoDaDose )
        {
            case 2: // "Reforço"

                $titulo = "A {$linha['dose']}ª dose é o " . self::$numeroDoReforco . "º reforço";

                foreach( $linha as $i => $campo )
                {
                    if( $i == 'dose' )
                    {
                        $linha[$i] = 
                            $this->AplicarEstiloDoseDeReforco('R' . self::$numeroDoReforco++,
                                $titulo);
                    }
                    else $linha[$i] = $this->AplicarEstiloDoseDeReforco($campo, $titulo);
                }
                
                break;

            case 3: // "Especial"
                
                static $doseEspecial = 1;

                $titulo = "A {$linha['dose']}ª dose é especial e só deve ser aplicada em casos específicos";

                foreach( $linha as $i => $campo )
                {
                    if( $i == 'dose' )
                    {
                        $linha[$i] =
                            $this->AplicarEstiloDoseEspecial('E' . $doseEspecial++,
                                $titulo);
                    }
                    else if( $i == 'data para vacinar' )
                            $linha['data para vacinar'] = $this->AplicarEstiloDoseEspecial('(especial)', $titulo);
                    
                    else if( $i == 'nova data' ) $linha['nova data'] = '';
                            
                    else $linha[$i] = $this->AplicarEstiloDoseEspecial($campo, $titulo);
                }

                break;

            default:
                $data = new Data();
                $linha['dose']  = "{$linha['dose']}ª";

                $linha['data para vacinar'] = 
                      ($data->CompararData($data->InverterData($linha['data para vacinar']), '<' )) ?
                        "<font color='#BC1343'>{$linha['data para vacinar']}</font>"
                      : $linha['data para vacinar'];
                      
                break;
        }
    }
    
//------------------------------------------------------------------------------

    /**
     * Remove a coluna especificada de cada linha da tabela de vacinação
     * 
     * @param Array $arrDoses
     */
    public function RemoverColunaDoArray(&$arrDoses, $colunaNome)
    {
        foreach( $arrDoses as $linha => $valor )
        {
            if( isset($arrDoses[$linha][$colunaNome]) )
                unset($arrDoses[$linha][$colunaNome]);
        }
    }
//------------------------------------------------------------------------------

    /**
     * Exibe o form com a tabela de doses para a vacinação
     * 
     * @param Array $arrDoses
     */
    public function ExibirFormVacinacao($arrDoses, $campanha_id, $usuario_id, $vacina_id)
    {
        $ultimaDoseAplicada = $this->VerificarUltimaDose($usuario_id, $vacina_id);
        $numerodadose       = $ultimaDoseAplicada + 1;
		$cicloAtual         = $this->CicloAtual($usuario_id, $vacina_id);

		$crip = new Criptografia();

        $query = $crip->Cifrar("pagina=Adm/vacinar&numerodadose=$numerodadose"
                            .	"&usuario_id=$usuario_id&vacina_id=$vacina_id"
                            .	"&campanha_id=$campanha_id&ciclo=$cicloAtual");

		echo "<form name='form' method='post' action='?$query'
			 onsubmit='if(this.checkRetroativo.checked == true)
			 		return (ValidarData(this.dataRetroativa))'>";

		echo '<div style="width:645px;  margin:auto">';

		if( isset($campanha_id) && ctype_digit($campanha_id) && $campanha_id > 0 )
        {
/*
            $this->RemoverColunaDoArray($arrDoses, 'data para vacinar');

            Html::CriarTabelaDeArray($arrDoses, false, false, false, 300 );
		    echo '<hr style="width:350px" />';
                    print_r($arrDoses);
*/
            $this->ExibirVacinarSimples($usuario_id, $vacina_id, $campanha_id);
		}
        else
        {
		    Html::CriarTabelaDeArray($arrDoses);
			echo '<hr />';
		}

		echo '</div>';


		$pessoaImunizada = $this->PessoaFoiImunizada($usuario_id, $vacina_id);

		echo '<div style="padding-left:90px; padding-top:20px;">';


		$this->ListarObs($usuario_id, $vacina_id);

		$ciclosPermitidos = $this->CiclosPermitidos($vacina_id);
		$ciclosCompletos  = $this->TotalDeCiclosCompletos($usuario_id, $vacina_id);

		if($ciclosCompletos < $ciclosPermitidos)
        {
            $doseAtual  = $ultimaDoseAplicada + 1;
			$cicloAtual = $ciclosCompletos    + 1;
            
			echo "<fieldset style='width:625px;'><legend>Observação para a {$doseAtual}ª dose do ciclo {$cicloAtual}</legend>";
			echo '<textarea name="obs" id="textarea" rows="2" style="width:620px; border: 0px;"></textarea></fieldset>';
		}
		echo '</div></form>';
    }
//------------------------------------------------------------------------------

    /**
     *
     * @param <type> $campo
     * @param <type> $titulo
     * @return <type>
     */
     public function AplicarEstiloDoseDeReforco($campo, $titulo = 'Reforço aplicado')
     {
        $estilo = 'color: olive; cursor: n-resize';

        $novaString = "<span style='$estilo' title='$titulo'>"
                    . str_ireplace('#BC1343', 'olive', $campo)
                    . '</span>';

        return $novaString;
     }
     //--------------------------------------------------------------------------
     public function AplicarEstiloDoseEspecial($campo, $titulo = 'Dose especial aplicada')
     {
        $estilo = 'color: teal; cursor: n-resize; font-style:italic;'.
            'font-family: Georgia, "Times New Roman", Times, serif;';

        $novaString = "<span style='$estilo' title='$titulo'>"
                    . str_ireplace('#BC1343', 'teal', $campo)
                    . '</span>';

        return $novaString;
     }
	 //--------------------------------------------------------------------------

    public function TextoParaDoseIdeal($novaData, $anoIdeal, $anos)
	{
		$data = new Data();
		
		$novaData = $data->InverterPosicaoData($novaData);
		
		//$textoParaDoseIdeal = "$novaData<br />aprox. aos $anoIdeal ano(s)";
		$textoParaDoseIdeal = $novaData;

		/* Ta funcionando, mas Paulo pediu pra tirar a idade!
		 if($anoIdeal != $anos) {
			$textoParaDoseIdeal = "<font color='#BC1343'>$novaData
						<br />aprox. aos $anoIdeal ano(s)</font>";
		}*/
		
	
		if($anoIdeal != $anos) {
			$textoParaDoseIdeal = "<font color='#BC1343'>$novaData</font>";
		}
		return $textoParaDoseIdeal;
	}
	//--------------------------------------------------------------------------
	private function ListarObs($usuario_id, $vacina_id)
	{
		/*$obs = $this->conexao->prepare('SELECT obs, numerodadose, DATE(datahoravacinacao) FROM  `usuariovacinado`
			WHERE Usuario_id = ? AND Vacina_id = ? AND LENGTH(obs) > 3')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));*/

		$obs = $this->conexao->prepare('SELECT obs, numerodadose,
										DATE(datahoravacinacao),
										numerodociclo
											FROM  `usuariovacinado`
												WHERE Usuario_id = ?
												AND Vacina_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$obs->bind_param('ii', $usuario_id, $vacina_id);
		$obs->bind_result($observacao, $numerodadose, $dataVacinacao, $numerodociclo);

		$obs->execute();

		$obs->store_result();

		$existe = $obs->num_rows;
		$existeObs = false;

		$data = new Data();

		if($existe > 0) {
			echo '<fieldset style="width:625px;"><legend>Obs.:</legend>
				  <div style="padding-left:10px;"><ul>';

			while ($obs->fetch()){
				$dataVacinacao = $data->InverterData($dataVacinacao);

				if(strlen($observacao) > 3) {

                    $stringObs = "<li style='margin-top: 10px'>
					 	  <strong>Ciclo: $numerodociclo - Dose $numerodadose</strong>:
						  $observacao  ($dataVacinacao) </li>";

                    if( $this->VerificarTipoDaDose($vacina_id, $numerodadose) == 2) {

                        static $numeroDoReforco = 1;

                        $stringObs = "<li style='margin-top: 10px'>
					 	  <strong>Ciclo: $numerodociclo - Dose $numerodadose ("
                          . $numeroDoReforco++
                          . "º reforço)</strong>:
						  $observacao  ($dataVacinacao) </li>";
                    }

                    elseif( $this->VerificarTipoDaDose($vacina_id, $numerodadose) == 3) {

                        static $doseEspecial = 1;

                        $stringObs = "<li style='margin-top: 10px'>
					 	  <strong>Ciclo: $numerodociclo - Dose $numerodadose ("
                          . $doseEspecial++
                          . "º dose especial)</strong>:
						  $observacao  ($dataVacinacao) </li>";
                    }
                    echo $stringObs;
                }

		else {

                    if( $this->CiclosPermitidos($vacina_id) > 1) {

                        $stringObs = "<li style='margin-top: 10px'><strong>Ciclo: $numerodociclo - Dose $numerodadose aplicada em: </strong>
                                    $observacao  $dataVacinacao </li>";

                        if( $this->VerificarTipoDaDose($vacina_id, $numerodadose) == 2) {

                            static $numeroDoReforco = 1;

                            $stringObs = "<li style='margin-top: 10px'><strong>
                                Ciclo: $numerodociclo - Dose $numerodadose("
                              . $numeroDoReforco++
                              . "º reforço) aplicada em: </strong>
                                    $observacao $dataVacinacao </li>";

                        }

                        if( $this->VerificarTipoDaDose($vacina_id, $numerodadose) == 3) {

                            static $doseEspecial = 1;

                            $stringObs = "<li style='margin-top: 10px'><strong>
                                Ciclo: $numerodociclo - Dose $numerodadose("
                              . $doseEspecial++
                              . "º reforço) aplicada em: </strong>
                                    $observacao $dataVacinacao </li>";

                        }

                        echo $stringObs;
                    }
                }
			}

			echo '</ul></div>
				  </fieldset>';
			$obs->free_result();
		}
	}//--------------------------------------------------------------------------
	private function ListarObsCampanha($usuario_id, $campanha_id, $vacina_id)
	{

		$obs = $this->conexao->prepare('SELECT obs, DATE(datahoravacinacao), TIME(datahoravacinacao)
                                                        FROM  `usuariovacinadocampanha`
                                                                WHERE Usuario_id = ?
                                                                AND Campanha_id = ?
                                                                AND Vacina_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$obs->bind_param('iii', $usuario_id, $campanha_id, $vacina_id);
		$obs->bind_result($observacao, $dataVacinacao, $hora);

		$obs->execute();

		$obs->store_result();

		$existe = $obs->num_rows;
		$existeObs = false;

		$data = new Data();

		if($existe > 0) {
			echo '<fieldset style="width:625px;"><legend>Obs.:</legend>
				  <div style="padding-left:10px;"><ul>';

                    while ($obs->fetch()){
                            $dataVacinacao = $data->InverterData($dataVacinacao);

                        if(strlen($observacao) > 3) {

                            $stringObs = "<li style='margin-top: 10px'>
                                                  <strong>Vacina já aplicada: </strong>
                                                  $observacao  ($dataVacinacao - $hora) </li>";


                            echo $stringObs;
                        }

                        else {

                            $stringObs = "<li style='margin-top: 10px'><strong>Vacina já aplicada: </strong>
                                        $observacao  $dataVacinacao $hora</li>";
                            echo $stringObs;

                        }

                        }

                        echo '</ul></div>
                      </fieldset>';
                        $obs->free_result();
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Aqui se vacina uma pessoa de acordo com as configurações de cada vacina.
	 *
	 * @param int $vacina_id id da vacina que deve ser aplicada.
	 */
	public function Vacinar($vacina_id, $numerodadose, $usuario_id, $obs,
		$campanha_id, $ciclo = 1)
	{
		$arrDependencias = $this->GerarDependencia($vacina_id);
		$nomeDaVacina = $this->ExibirNomeDaVacina($vacina_id);
		
		//die('<h1>Vacinar</h1>');
		$login_adm = $_SESSION['login_adm'];

        $totalDeDoses       = $this->TotalDeDoses($vacina_id);
		$ultimadoseaplicada = $this->VerificarUltimaDose($usuario_id, $vacina_id);
		
		$ciclosPermitidos 		= $this->CiclosPermitidos($vacina_id);
		$ciclosCompletos 		= $this->TotalDeCiclosCompletos($usuario_id, $vacina_id);

		//if ($numerodadose == $ultimadoseaplicada + 1) {
		if($ciclosCompletos <= $ciclosPermitidos) {

			//$ciclo++;
			
			/*die("unidade_id, $vacina_id, $usuario_id,
						$campanha_id, $login_adm, $numerodadose, $obs, $ciclo");*/
			
			$unidade_id = $_SESSION['unidadeDeSaude_id'];
			
			// NULL aqui é para gravar a data do sistema (TIMESTAMP), que no caso
			// de vacinar retroativo, vai ser diferente
			/*$vacinar = $this->conexao->prepare ('INSERT INTO `usuariovacinado`
				VALUES (NULL, ?, ?, ?, ?, ?, NOW(), NULL, ?, ?, ?, 1) ')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$vacinar->bind_param('iiiisisi',$unidade_id, $vacina_id, $usuario_id,
						$campanha_id, $login_adm, $numerodadose, $obs, $ciclo);
			$vacinar->execute();
			$vacinou = $vacinar->affected_rows;
			$vacinar->close();*/

            $crip = new Criptografia();

			parse_str($crip->Decifrar($_SERVER['QUERY_STRING']));

            if($campanha_id) $obs = 'Campanha '. str_ireplace('campanha', '', $this->RetornarCampoNome('campanha', $campanha_id));
            if(isset($acrescentarEmRotina)) $obs .= ' e rotina ';


            //########------------


            list($dataIncrementada, $ano, $mes, $dia ) =
                $this->IncrementarProximaDoseParaVacinar($usuario_id,
                                                         $vacina_id, 
                                                         $ultimadoseaplicada,
                                                         $totalDeDoses);


/*
            list($nascimento) = $this->ExibirDadosDeVacinacaoDoUsuario($usuario_id);

            $data = new Data();
            $idade = $data->IdadeExata($nascimento);

            list($ano, $mes, $dia) = explode('/', $idade);

            $doseAtual = ($ultimadoseaplicada + 1) < $totalDeDoses ?
                         ($ultimadoseaplicada + 1)                 :
                         1;
                         
            $proximaDose = $doseAtual + 1;
            $doseBase = $this->VerificarDoseBase($vacina_id, $proximaDose);

            $dataParaIncremento = date('Y/m/d H:i:s');

            if($doseBase != $proximaDose) {

                $dataParaIncremento = $this->DataHoraVacinacao($usuario_id, $vacina_id, $doseBase);
            }

            list($qtdDias) = $this->DiasIdeaisParaVacinar($vacina_id, $proximaDose);

            $dataIncrementada = $data->IncrementarData($dataParaIncremento, $qtdDias);

            // Verificando se a data incrementada é menor do que a data da
            // última dose aplicada (testar)
            $dataUltimaVacinacao = $this->DataHoraVacinacao($usuario_id, $vacina_id, $ultimadoseaplicada);
            if( $data->CompararData($dataIncrementada, '<', $dataUltimaVacinacao ) )
            {
                // Incrementa 60 dias a última dose aplicada (segundo Nádia)
                $dataIncrementada = $data->IncrementarData($dataUltimaVacinacao, 60);
            }

            // Se a quantidade de dias retornar zero, então não há próxima dose.
            // neste caso, colocar que $dataIncrementada é zero.
            if(1 + $ultimadoseaplicada == $totalDeDoses) $dataIncrementada = 0;

            // Informando os detalhes (comentar - MAS NÃO APAGAR - depois):
            echo "<script>alert('Dose atual: $doseAtual; "
               . "\\nCiclo: $ciclo; "
               . "\\nTotal de doses desta vacina: $totalDeDoses; "
               . "\\nProxima dose: $proximaDose; "
               . "\\nDose base para a próxima dose: $doseBase; "
               . "\\nData para incremento: $dataParaIncremento; "
               . "\\nDias para incrementar na data: $qtdDias; "
               . "\\nData incrementada: $dataIncrementada;"
               . "\\nÚltima dose aplicada: $ultimadoseaplicada')</script>";

            // Até aqui tudo certo quando se trata do primeiro ciclo. Quando o
            // ciclo é maior que 1, fazer:
*/
            //########------------

            if ( ($campanha_id > 0 && isset($acrescentarEmRotina)) || (!$campanha_id)) {

                    $vacinou = $this->VacinarComRotina( $unidade_id, $vacina_id, $usuario_id,
                       0, $login_adm, $numerodadose,
                       $obs, $ciclo, $ano, $mes, $dia, $dataIncrementada);

                    if($vacinou) {
                        $vacina = new Vacina;
                        $vacina->UsarBaseDeDados();
                        $vacinaMae_id = $vacina->VacinaPertence($vacina_id);
                        
                        if($vacinaMae_id)
                        $vacinou = $this->VacinarComRotina( $unidade_id, $vacinaMae_id, $usuario_id,
                           0, $login_adm, $numerodadose,
                           $obs, $ciclo, $ano, $mes, $dia, $dataIncrementada);
                    }
                   
            }
                               //
            if($campanha_id > 0)
                    $vacinou = $this->VacinarComCampanha( $unidade_id, $vacina_id, $usuario_id,
                       $campanha_id, $login_adm, $obs, $ano, $mes, $dia);
                        						
			foreach( $arrDependencias as $vacinaDependente )
            {
				//Travessão
				$obsDependente = $obs . "Vacinado &#8212; $nomeDaVacina"; 

                if( $campanha_id == 0 )
                {
                     $sql = "INSERT INTO `usuariovacinado`
                             VALUES (NULL,               -- id
                                      $unidade_id,       -- UnidadeDeSaude_id
                                      $vacinaDependente, -- Vacina_id
                                      $usuario_id,       -- Usuario_id
                                      $campanha_id,      -- Campanha_id
                                     '$login_adm',       -- loginadm
                                      NOW(),             -- datahoravacinacao
                                      NULL,              -- datahorasistema
                                      $numerodadose,     -- numerodadose
                                     '$obsDependente',   -- obs
                                      $ciclo,            -- numerodociclo
                                      0,                 -- decrementarestoque
                                      $ano,              -- idadeano
                                      $mes,              -- idademes
                                      $dia,              -- idadedia
                                     '$dataIncrementada')-- proximadose";

                    $vacinar = $this->conexao->prepare ($sql)
                    or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                    $vacinar->execute();
                    $vacinar->close();
                }
			}

			if ($vacinou) {
				
				$this->DecrementarDoseDaUnidade($unidade_id, $vacina_id);
				return true;
			}
		}
		return false;
	}
	//--------------------------------------------------------------------------
    public function IncrementarProximaDoseParaVacinar($usuario_id,
        $vacina_id, $ultimadoseaplicada, $totalDeDoses, $dataVacinacao = false)
    {

        $nascimento = $this->Nascimento($usuario_id);

        $data = new Data();
        $idade = $data->IdadeExata($nascimento);

        list($ano, $mes, $dia) = explode('/', $idade);

        $doseAtual = ($ultimadoseaplicada + 1) < $totalDeDoses ?
                 ($ultimadoseaplicada + 1)                 :
                 1;

        $proximaDose = $doseAtual + 1;
        $doseBase = $this->VerificarDoseBase($vacina_id, $proximaDose);


        $dataParaIncremento = date('Y/m/d H:i:s');


        if($dataVacinacao)  $dataParaIncremento = $dataVacinacao;


        $crip = new Criptografia();

        $decifrado = $crip->Decifrar($_SERVER['QUERY_STRING']);

        if($doseBase != $proximaDose && $doseBase > 0 && 
            ((!$dataVacinacao && substr_count($decifrado, 'Retroativo'))
                ||
             ($dataVacinacao && !substr_count($decifrado, 'Retroativo')))
            ) { // ADICIONEI  ( && $dataVacinacao ) pra tava dando erro na linha 140 no tData 24-11-2010
                
            $dataParaIncremento = $this->DataHoraVacinacao($usuario_id, $vacina_id, $doseBase);
        }
 
  
        list($qtdDias) = $this->DiasIdeaisParaVacinar($vacina_id, $proximaDose);

        
        $dataIncrementada = $data->IncrementarData($dataParaIncremento, $qtdDias);


        // Verificando se a data incrementada é menor do que a data da
        // última dose aplicada (testar)
        $dataUltimaVacinacao = $this->DataHoraVacinacao($usuario_id, $vacina_id, $ultimadoseaplicada);



       // echo "<li>ultima aplicada $ultimadoseaplicada if( $dataIncrementada < $dataUltimaVacinacao) merda tudo</li>";

        if( $data->CompararData($dataIncrementada, '<', $dataUltimaVacinacao )
            && $dataUltimaVacinacao > 0)
        {
            // Incrementa 60 dias a última dose aplicada (segundo Nádia)
           $dataIncrementada = $data->IncrementarData($dataUltimaVacinacao, 60);
        }

        // Se a quantidade de dias retornar zero, então não há próxima dose.
        // neste caso, colocar que $dataIncrementada é zero.
        if((1 + $ultimadoseaplicada) == $totalDeDoses) $dataIncrementada = 0;

       /* echo "<h1>if($ultimadoseaplicada+1 == $totalDeDoses) fica 0 ==== $dataIncrementada----> $dataParaIncremento, ======> $qtdDias #####> $proximaDose</h1>";

        // Informando os detalhes (comentar - MAS NÃO APAGAR - depois):
        echo "<script>alert('Dose atual: $doseAtual; "
        //. "\\nCiclo: $ciclo; "
        . "\\nTotal de doses desta vacina: $totalDeDoses; "
        . "\\nProxima dose: $proximaDose; "
        . "\\nDose base para a próxima dose: $doseBase; "
        . "\\nData para incremento: $dataParaIncremento; "
        . "\\nDias para incrementar na data: $qtdDias; "
        . "\\nData incrementada: $dataIncrementada;"
        . "\\nÚltima dose aplicada: $ultimadoseaplicada')</script>";
        */
        // Até aqui tudo certo quando se trata do primeiro ciclo. Quando o
        // ciclo é maior que 1, fazer:

        return Array($dataIncrementada, $ano, $mes, $dia);

        //########------------

    }
	//--------------------------------------------------------------------------
        public function VacinarComCampanha($unidade_id, $vacina_id, $usuario_id,
                                           $campanha_id, $login_adm, $obs, $ano,
                                           $mes, $dia)
        {

           if(isset($_SESSION['dataRetroativa'])
           && $_SESSION['dataRetroativa'] != 'digite a data'
           && $_SESSION['dataRetroativa'] != '' ){

            $dataDeVacinacao = $_SESSION['dataRetroativa'];
            list($diaVac, $mesVac , $anoVac) = explode('/',$dataDeVacinacao);

            $dataDeVacinacao = "$anoVac/$mesVac/$diaVac 00:00:00";

           }
           else $dataDeVacinacao = date('Y/m/d H:i:s');

            $sql = 'INSERT INTO `usuariovacinadocampanha`
                     VALUES (NULL,     -- id
                                ?,     -- UnidadeDeSaude_id
                                ?,     -- Vacina_id
                                ?,     -- Usuario_id
                                ?,     -- Campanha_id
                                ?,     -- loginadm
                                ?,     -- datahoravacinacao
                                NULL,  -- datahorasistema
                                ?,     -- obs
                                1,     -- decrementardoestoque
                                ?,     -- idadeano
                                ?,     -- idademes
                                ?)     -- idadedia';

            $vacinar = $this->conexao->prepare ($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $vacinar->bind_param('iiiisssiii',$unidade_id, $vacina_id, $usuario_id,
                                    $campanha_id, $login_adm,$dataDeVacinacao, $obs, $ano, $mes, $dia);
            $vacinar->execute();
            $vacinou = $vacinar->affected_rows;
            $vacinar->close();
            return $vacinou;

        }
	//--------------------------------------------------------------------------
        public function VacinarComRotina( $unidade_id, $vacina_id, $usuario_id,
                                          $campanha_id, $login_adm, $numerodadose,
                                          $obs, $ciclo, $ano, $mes, $dia,
                                          $dataIncrementada)
        {
            $dataDeVacinacao = 'NOW()';
            if(isset($_SESSION['dataRetroativa'])
                  && $_SESSION['dataRetroativa'] != ''
                  && $_SESSION['dataRetroativa'] != 'digite a data' )
                  {
                      list($dia,$mes,$ano) = explode('/',$_SESSION['dataRetroativa']);
                      $dataDeVacinacao = "'$ano/$mes/$dia'";
                      echo "<script>alert('".$dataDeVacinacao."');</script>";
                  }


             $sql = 'INSERT INTO `usuariovacinado`
                     VALUES (NULL,     -- id
                                ?,     -- UnidadeDeSaude_id
                                ?,     -- Vacina_id
                                ?,     -- Usuario_id
                                ?,     -- Campanha_id
                                ?,     -- loginadm
                                '.$dataDeVacinacao.', -- datahoravacinacao
                                NULL,  -- datahorasistema
                                ?,     -- numerodadose
                                ?,     -- obs
                                ?,     -- numerodociclo
                                1,     -- decrementarestoque
                                ?,     -- idadeano
                                ?,     -- idademes
                                ?,     -- idadedia
                                ?)     -- proximadose';

            $vacinar = $this->conexao->prepare ($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $vacinar->bind_param('iiiisisiiiis', $unidade_id, $vacina_id,
                $usuario_id, $campanha_id, $login_adm, $numerodadose, $obs,
                $ciclo, $ano, $mes, $dia, $dataIncrementada);
            
            $vacinar->execute();
            $vacinou = $vacinar->affected_rows;
            $vacinar->close();

            return $vacinou;
        }

    //--------------------------------------------------------------------------
	public function VacinarRetroativo($vacina_id, $numerodadose, $usuario_id, $obs,
					$data, $campanha_id, $decrementarEstoque, $ciclo,
                    $idadeAno, $idadeMes, $idadeDia, $proximaDose)
	{
		$login_adm = $_SESSION['login_adm'];

		$ultimadoseaplicada = $this->VerificarUltimaDose($usuario_id, $vacina_id);

		if ($numerodadose == $ultimadoseaplicada + 1) {

			$unidade_id = $_SESSION['unidadeDeSaude_id'];


            $sql = 'INSERT INTO `usuariovacinado`
                    VALUES (NULL,     -- id
                               ?,     -- UnidadeDeSaude_id
                               ?,     -- Vacina_id
                               ?,     -- Usuario_id
                               ?,     -- Campanha_id
                               ?,     -- loginadm
                               ?,     -- datahoravacinacao
                               NULL,  -- datahorasistema
                               ?,     -- numerodadose
                               ?,     -- obs
                               ?,     -- numerodociclo
                               ?,     -- decrementarestoque
                               ?,     -- idadeano
                               ?,     -- idademes
                               ?,     -- idadedia
                               ?)     -- proximadose';

			$vacinar = $this->conexao->prepare ($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$vacinar->bind_param('iiiissisiiiiis',$unidade_id, $vacina_id, $usuario_id,
				$campanha_id, $login_adm, $data, $numerodadose, $obs, $ciclo,
                $decrementarEstoque, $idadeAno, $idadeMes, $idadeDia, $proximaDose);
				
			$vacinar->execute();
			$vacinou = $vacinar->affected_rows;
			$vacinar->close();

            if($vacinou) {

                // Conta a vacinaçao para a vacina principal (vacina mae)

                $vacina = new Vacina;
                $vacina->UsarBaseDeDados();
                $vacinaMae_id = $vacina->VacinaPertence($vacina_id);

                $vacinar = $this->conexao->prepare ($sql)
                or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                $vacinar->bind_param('iiiissisiiiiis',$unidade_id, $vacinaMae_id, $usuario_id,
				$campanha_id, $login_adm, $data, $numerodadose, $obs, $ciclo,
                $decrementarEstoque, $idadeAno, $idadeMes, $idadeDia, $proximaDose);

                $vacinar->execute();
                $vacinou = $vacinar->affected_rows;
                $vacinar->close();

            }
            
            if ($vacinou){
				if ($decrementarEstoque) $this->DecrementarDoseDaUnidade($unidade_id, $vacina_id);
				return true;
			}

		}
		return false;
	}
	//--------------------------------------------------------------------------
	public function DosesDaUnidade($unidade_id, $vacina_id)
	{
        $vacina = new Vacina;
        $vacina->UsarBaseDeDados();

        $vacinaQuePertence = $vacina->VacinaPertence($vacina_id);
        if($vacinaQuePertence) $vacina_id = $vacinaQuePertence;

		$quantidade = $this->conexao->prepare('SELECT quantidade 
			FROM vacinadaunidade
			WHERE UnidadeDeSaude_id = ? 
			AND Vacina_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$quantidade->bind_param('ii', $unidade_id, $vacina_id);
		$quantidade->bind_result($qtdDeDose);
		$quantidade->execute();
		
		$quantidade->fetch();
		
		$quantidade->free_result();
		
		return $qtdDeDose;

	}
	//--------------------------------------------------------------------------
	private function DecrementarDoseDaUnidade($unidade_id, $vacina_id)
	{

        $vacina = new Vacina;
        $vacina->UsarBaseDeDados();

        $vacinaQuePertence = $vacina->VacinaPertence($vacina_id);
        if($vacinaQuePertence) $vacina_id = $vacinaQuePertence;

		$decrementar = $this->conexao->prepare ('UPDATE `vacinadaunidade` 
			SET quantidade = (quantidade -1)
			WHERE UnidadeDeSaude_id = ? 
			AND Vacina_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$decrementar->bind_param('ii', $unidade_id, $vacina_id);
		$decrementar->execute();
		$decrementar->close();
	}
	//--------------------------------------------------------------------------
	public function MontarListaDeCaracteristicas ($vacinaDaCampanha_id) {

		$caracteristicas = $this->conexao->prepare('SELECT idadeInicio, idadeFinal,
		sexo, etnias, estados FROM `configuracaodavacina` WHERE vacinaDaCampanha_id = ?')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$caracteristicas->bind_param('i', $vacinaDaCampanha_id);
		$caracteristicas->bind_result($idadeInicio, $idadeFinal, $sexo, $etnias, $estados);
		$caracteristicas->execute();
		$caracteristicas->store_result();

		$existe = $caracteristicas->num_rows;


		if ($existe > 0) {

			$arr_carac = array();

			while ($caracteristicas->fetch()) {

				$arr_carac[] = array('idadeInicio'=> $idadeInicio,
                             // Necessario para X anos, 11 meses e 29 dias
                             // (considerando que um ano bisexto tem 366 dias e
                             // é preciso ainda mais um dia, usar 367:
				             'idadeFinal' => $idadeFinal + 367,
				             'sexo' => $sexo,
				             'etnias' => $etnias,
				             'estados' => $estados );

			}
			$caracteristicas->free_result();
					
			return $arr_carac;
		}
		
		$caracteristicas->free_result();
		
		if ($existe == 0) {
			
			$this->AdicionarMensagemDeErro('Não existem configurações para esta
				vacina.');
			
			return false;
		}
			
		if ($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar
				selecionar configurações para a vacina.');
			
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function ExibirListaDePessoasVacinaveis($vacinaDaCampanha_id,
		$cidade_id, $vacina_id, $pesquisa, $mae, $cpf, $nasc, $campanha_id,
        $aPartirDe = 1) {

		$estadoSigla = $this->BuscarEstadoDaCidade($cidade_id);

		$arr_carac = $this->MontarListaDeCaracteristicas($vacinaDaCampanha_id);
		
		
		// Ex Pesquisa: Diego Da Silva no sql vai ficar %Diego%Da%Silva%
		
		$explodeCaracteres = explode(' ',$pesquisa);
		$implodeCaracteres = implode('%',$explodeCaracteres);

		$pesquisa = "$implodeCaracteres";
		
		
		$explodeCaracteresMae = explode(' ', $mae);
		$implodeCaracteresMae = implode('%', $explodeCaracteresMae);

		$mae = "$implodeCaracteresMae";
		
		if( count($arr_carac) ) {

			$nomeDaTabelaTemporaria = uniqid('cid');

			$this->conexao->query("CREATE TEMPORARY TABLE IF NOT EXISTS
				`$nomeDaTabelaTemporaria` (
					`id` INT NOT NULL ,
					`nome` VARCHAR( 100 ) NOT NULL ,
					`mae` VARCHAR( 100 ) NOT NULL ,
					`nasc` DATE NULL ,

					PRIMARY KEY ( `id` ),
					UNIQUE INDEX unico( `nome` , `mae`, `nasc` )
					) ENGINE = MyIsam")
					
					or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			//echo '<pre>'; print_r($arr_carac); echo '</pre>';
			
			//---------------		
				$sqlMae = false;
				if($mae != 'vazio') $sqlMae = "AND usuario.mae LIKE '%$mae%' ";
				
				$sqlNome = false;
				if( strlen($pesquisa) > 2) $sqlNome = "AND usuario.nome LIKE '%$pesquisa%'";
			//---------------		
					
			foreach ($arr_carac as $carac) {

				$idadeInicio = $carac['idadeInicio'];
				$idadeFinal = $carac['idadeFinal'];
				$sexo = $carac['sexo'];
				$etnias = $carac['etnias'];
				$estados = $carac['estados'];

				if( strpos($estados, $estadoSigla) !== false || $estados == 'todos') {

					$data = new Data();
					$periodo = $data->IntervaloDeDatasEmDias($carac['idadeInicio'], $carac['idadeFinal']);

					list($dataFinal, $dataInicio) = explode(',', $periodo);


                    Depurador::Pre("Usar faixas\n início: {$carac['idadeInicio']};\n final: {$carac['idadeFinal']}\n\nData início: $dataInicio;\nData final: $dataFinal");

					if( $sexo == 'ambos' && $etnias == 'todas') {

						$sqlCount = "SELECT COUNT(usuario.id)
                            FROM `usuario`, `bairro`, `cidade`
							WHERE (usuario.cpf = '$cpf' OR '$cpf' = 'vazio')
							AND  (usuario.nascimento = '$nasc' OR '$nasc' = 'vazio')
							AND usuario.bairro_id = bairro.id
							AND bairro.cidade_id = cidade.id
							AND cidade.id = $cidade_id
							$sqlNome
							$sqlMae
							AND usuario.ativo
                            AND usuario.vacinavel
							AND bairro.ativo
							AND nascimento
							BETWEEN '$dataInicio' AND '$dataFinal'";
                            
						$sql = "SELECT usuario.id, usuario.nome, usuario.mae,
							usuario.nascimento FROM
							`usuario`, `bairro`, `cidade`
							WHERE (usuario.cpf = '$cpf' OR '$cpf' = 'vazio')
							AND  (usuario.nascimento = '$nasc' OR '$nasc' = 'vazio')
							AND usuario.bairro_id = bairro.id
							AND bairro.cidade_id = cidade.id
							AND cidade.id = $cidade_id
							$sqlNome
							$sqlMae
							AND usuario.ativo
                            AND usuario.vacinavel
							AND bairro.ativo
							AND nascimento
							BETWEEN '$dataInicio' AND '$dataFinal'
							ORDER BY usuario.nome
                            LIMIT $aPartirDe, ". Html::LIMITE;
					}
                    
					elseif( $sexo == 'ambos' && $etnias != 'todas') {

						$consultaEtnias = '"' . str_replace(', ', '" OR etnia.nome = "', $etnias) . '"';

                        $sqlCount = "SELECT COUNT(usuario.id)
                            FROM `usuario`, `bairro`, `cidade`, `etnia`
							WHERE  (usuario.cpf = '$cpf' OR '$cpf' = 'vazio')
							AND  (usuario.nascimento = '$nasc' OR '$nasc' = 'vazio')
							AND usuario.bairro_id = bairro.id
							AND bairro.cidade_id = cidade.id
							AND cidade.id = $cidade_id
							$sqlNome
							$sqlMae
							AND usuario.etnia_id = etnia.id
							AND (etnia.nome = $consultaEtnias)
							AND usuario.ativo
                            AND usuario.vacinavel
							AND bairro.ativo
							AND nascimento
							BETWEEN '$dataInicio' AND '$dataFinal'";
                        
						$sql = "SELECT usuario.id, usuario.nome, usuario.mae,
							usuario.nascimento FROM
							`usuario`, `bairro`, `cidade`, `etnia`
							WHERE  (usuario.cpf = '$cpf' OR '$cpf' = 'vazio')
							AND  (usuario.nascimento = '$nasc' OR '$nasc' = 'vazio')
							AND usuario.bairro_id = bairro.id
							AND bairro.cidade_id = cidade.id
							AND cidade.id = $cidade_id
							$sqlNome
							$sqlMae
							AND usuario.etnia_id = etnia.id
							AND (etnia.nome = $consultaEtnias)
							AND usuario.ativo
                            AND usuario.vacinavel
							AND bairro.ativo
							AND nascimento
							BETWEEN '$dataInicio' AND '$dataFinal'
							ORDER BY usuario.nome
                            LIMIT $aPartirDe, ". Html::LIMITE;
					}
                    
					elseif( $sexo != 'ambos' && $etnias == 'todas') {
						
                        $sqlCount = "SELECT COUNT(usuario.id)
                            FROM `usuario`, `bairro`, `cidade`
							WHERE (usuario.cpf = '$cpf' OR '$cpf' = 'vazio')
							AND  (usuario.nascimento = '$nasc' OR '$nasc' = 'vazio')
							AND usuario.bairro_id = bairro.id
							AND bairro.cidade_id = cidade.id
							AND cidade.id = $cidade_id 
							$sqlNome
							$sqlMae
							AND sexo = '$sexo'
							AND usuario.ativo
                            AND usuario.vacinavel
							AND bairro.ativo
							AND nascimento
							BETWEEN '$dataInicio' AND '$dataFinal'";
                        
						$sql = "SELECT usuario.id, usuario.nome, usuario.mae,
							usuario.nascimento FROM
							`usuario`, `bairro`, `cidade`
							WHERE (usuario.cpf = '$cpf' OR '$cpf' = 'vazio')
							AND  (usuario.nascimento = '$nasc' OR '$nasc' = 'vazio')
							AND usuario.bairro_id = bairro.id
							AND bairro.cidade_id = cidade.id
							AND cidade.id = $cidade_id 
							$sqlNome
							$sqlMae
							AND sexo = '$sexo'
							AND usuario.ativo
                            AND usuario.vacinavel
							AND bairro.ativo
							AND nascimento
							BETWEEN '$dataInicio' AND '$dataFinal'
							ORDER BY usuario.nome
                            LIMIT $aPartirDe, ". Html::LIMITE;						
					}
                    
					else {
						$consultaEtnias = '"' . str_replace(', ', '" OR etnia.nome = "', $etnias) . '"';
						
                        $sqlCount = "SELECT COUNT(usuario.id)
                            FROM `usuario`, `bairro`, `cidade`, `etnia`
							WHERE (usuario.cpf = '$cpf' OR '$cpf' = 'vazio')
							AND  (usuario.nascimento = '$nasc' OR '$nasc' = 'vazio')
							AND usuario.bairro_id = bairro.id
							AND bairro.cidade_id = cidade.id
							AND cidade.id = $cidade_id 						
							$sqlNome
							$sqlMae
							AND sexo = '$sexo'
							AND usuario.etnia_id = etnia.id
							AND (etnia.nome = $consultaEtnias)
							AND bairro.ativo
							AND usuario.ativo
                            AND usuario.vacinavel
							AND nascimento
							BETWEEN '$dataInicio' AND '$dataFinal'";
                        
						$sql = "SELECT usuario.id, usuario.nome, usuario.mae,
							usuario.nascimento FROM
							`usuario`, `bairro`, `cidade`, `etnia`
							WHERE (usuario.cpf = '$cpf' OR '$cpf' = 'vazio')
							AND  (usuario.nascimento = '$nasc' OR '$nasc' = 'vazio')
							AND usuario.bairro_id = bairro.id
							AND bairro.cidade_id = cidade.id
							AND cidade.id = $cidade_id 						
							$sqlNome
							$sqlMae
							AND sexo = '$sexo'
							AND usuario.etnia_id = etnia.id
							AND (etnia.nome = $consultaEtnias)
							AND bairro.ativo
							AND usuario.ativo
                            AND usuario.vacinavel
							AND nascimento
							BETWEEN '$dataInicio' AND '$dataFinal'
							ORDER BY usuario.nome
                            LIMIT $aPartirDe, ". Html::LIMITE;
					}

					//echo "<br />$nasc - $sexo -  $dataInicio - $dataFinal - $cidade_id - $nome<br />";
					//echo "<p>$sql</p>";
					
                    $stmt = $this->conexao->prepare($sqlCount)
						or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
                    
                    $totalDeRegistros = false;
					$stmt->bind_result($totalDeRegistros);
					$stmt->execute();
                    $stmt->fetch();
                    $stmt->free_result();

                    Depurador::Pre($sql);
                    
					$pessoas = $this->conexao->prepare($sql)
						or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
                    
					$pessoas->bind_result($id_usuario, $nome, $mae, $nasceu);
					$pessoas->execute();
					$pessoas->store_result();

					$existePessoas = $pessoas->num_rows;

					if ($existePessoas > 0) {

						$arr_pessoas = array();

						while ($pessoas->fetch()) {

							$this->conexao->query("INSERT INTO `$nomeDaTabelaTemporaria`
								(id, nome, mae, nasc) VALUES($id_usuario, '$nome', '$mae', '$nasceu')");
								
								//Nesse caso é desejavel a retirada do or die devido ao erro de duplicidade
								//no cadastro. Varias caracteristicas podem apontar para um mesmo individuo,
								//porém o mesmo só pode ser inserido apenas 1 vez na tabela temporária.
								//or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

						}
					}
					
					$pessoas->free_result();
					
					if($existePessoas == 0) {
						
						// Se retornar, irá sair do foreach. Caso haja mais de uma condição na campanha
						// só será executada a primeira sql.
						//return false;
					}
					if($existePessoas < 0) {
						
						$this->AdicionarMensagemDeErro('Algum erro ocorreu ao
							tentar verificar se existem indivíduos com as
							características das vacinas nesta campanha.');
						
						return false;
					}
				}
			}

			$linhas = $this->conexao->query("SELECT id, nome, mae, nasc FROM
				`$nomeDaTabelaTemporaria`")
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

			$arr = array();
			$crip = new Criptografia();
		
			$data = new Data();
		
			//echo '<h1>O filtro da mãe não está funcionando qdo se usa tb o nome do cara!</h1>'; ???????
			//die("raticum ");
			while ($linha = $linhas->fetch_assoc()) {

				$status = $this->VerificarStatusDeVacinacao($linha['id'], $vacina_id, $campanha_id);
				
				if($status == 'vacinacaoCompleta')
					$tituloDoLink = 'Vacinação completa';
				
				elseif($status == 'vacinacaoIncompleta')
					$tituloDoLink = 'Vacinação iniciada, mas não completada';
				
				elseif($status == 'naoVacinado')
					$tituloDoLink = 'Nenhuma dose aplicada neste indivíduo';

                                elseif($status == 'faltaReforco')
                                        $tituloDoLink = 'Todas as doses aplicadas, faltando somente reforço(s).';

                                elseif($status == 'participouDaCampanha')
                    
                                        $tituloDoLink = 'Indivíduo participou da campanha selecionada';

                                $qs = $crip->Cifrar("pagina=Adm/vacinar&usuario_id={$linha['id']}&vacina_id=$vacina_id&campanha_id=$campanha_id");
				
				///???HA
				if( $this->VerificarDoseAtrasada($linha['id'],$vacina_id) ){
					
						$linha['vacinar'] = "<a href='?$qs'><img src='{$this->arquivoGerarIcone}?imagem=ok_vermelho' border='0' alt='Listar'/></a>";
				}else 
						$linha['vacinar'] = "<a href='?$qs'><img src='{$this->arquivoGerarIcone}?imagem=ok' border='0' alt='Listar' /></a>";
				
				$nascimentoInvertido = $data->InverterData($linha['nasc']);
				
				
				if( strlen($linha['mae']) <= 2) 
					$linha['mae'] = '<em><span style="color: #CCC">Não informado</span></em>';
				//========================
				if( $this->VerificarDoseAtrasada($linha['id'],$vacina_id)) {
					
					$arr[] = array(' ' => $linha['vacinar'],
						       'id' => $linha['id'], 'nome' => "<a  title='$tituloDoLink' 
							href='?$qs'><span class='$status'>{$linha['nome']}</span></a>",
							'mãe' => $linha['mae'],
							'nascimento' => $nascimentoInvertido);
				}
						
				elseif (isset($_SESSION['listarPessoasVacinaveis']['emAtraso'])
					&& $_SESSION['listarPessoasVacinaveis']['emAtraso'] != 'on') {
					
					$arr[] = array(' ' => $linha['vacinar'],
					       'id' => $linha['id'], 'nome' => "<a  title='$tituloDoLink' 
						href='?$qs'><span class='$status'>{$linha['nome']}</span></a>",
						'mãe' => $linha['mae'],
						'nascimento' => $nascimentoInvertido);
				}
				//========================
				
				/*$arr[] = array(' ' => $linha['vacinar'],
					       'id' => $linha['id'], 'nome' => "<a  title='$tituloDoLink' 
						href='?$qs'><span class='$status'>{$linha['nome']}</span></a>",
						'mãe' => $linha['mae'],
						'nascimento' => $nascimentoInvertido);*/
						
						
			}

			$tamanho = count($arr);
			//echo "<hr>$sqlCount<hr>";
			if($tamanho) {
				
				$dosesDaVacinaNaUnidade = $this->DosesDaUnidade($_SESSION['unidadeDeSaude_id'], 
				$vacina_id);
				
				echo "<h2 align='center'>{$this->ExibirNomeDaVacina($vacina_id)}";
				echo "<span style='font-size: 12px; font-weight: normal'>
				(doses restantes: $dosesDaVacinaNaUnidade)</span></h2>";
				
				Html::CriarTabelaDeArray($arr);
				//echo $totalDeRegistros;
				return $totalDeRegistros;
			}
		}
		return false;
	}
    //--------------------------------------------------------------------------
    public function QuantidadeDeReforco($vacinaId)
    {
        $sql = 'SELECT
					COUNT(intervalodadose.id)
					FROM `intervalodadose` 
					WHERE intervalodadose.Vacina_id = ?
                    AND TipoDaDose_id = 2';

		$stmt = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        $qtdReforco = false;

		$stmt->bind_param('i', $vacinaId);
		$stmt->bind_result($qtdReforco);
		$stmt->execute();
		$stmt->fetch();
		$stmt->free_result();

        return $qtdReforco;
    }
	//--------------------------------------------------------------------------
	/**
	 * Verifica o status de vacinação
	 * Método usado para colorir o nome do indivíduo, dependendo do seu status.
	 * Os possíveis são: vacinacaoCompleta, vacinacaoIncompleta e naoVacinado
	 * (classes do css)
	 *
	 * @param int $pessoaId Id da pessoa que foi buscada
	 * @param int $vacinaId Id da vacina que foi buscada
	 */
	private function VerificarStatusDeVacinacao($pessoaId, $vacinaId, $campanhaId = false)
	{        
		/*
		 $stmt = $this->conexao->prepare('SELECT COUNT(usuariovacinado.usuario_id)
			AS `qtdtomadas`, aplicacoesporpessoa
			FROM `usuario` , `usuariovacinado` , `vacina` 
			WHERE usuario.id = usuariovacinado.Usuario_id
			AND vacina.id = usuariovacinado.Vacina_id
			AND usuario.id = ?
			AND vacina.id = ?
			AND usuario.ativo
			AND vacina.ativo
			GROUP BY usuario.id')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		*/

                if($campanhaId) {

                    $sql = 'SELECT
                                COUNT(usuariovacinadocampanha.usuario_id) AS `qtdtomadas`, 1

                                FROM `usuario` , `usuariovacinadocampanha` , `vacina`

                                WHERE usuario.id = usuariovacinadocampanha.Usuario_id

                                        AND vacina.id = usuariovacinadocampanha.Vacina_id
                                        AND usuario.id = ?
                                        AND vacina.id = ?
                                        AND usuariovacinadocampanha.Campanha_id = ?
                                        AND usuario.ativo
                                        AND vacina.ativo

                                                GROUP BY usuario.id';


                    $stmt = $this->conexao->prepare($sql)
                            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
                            
                    $stmt->bind_param('iii', $pessoaId, $vacinaId, $campanhaId);
                }

                else {
                    $qtdReforco = $this->QuantidadeDeReforco($vacinaId);

                    $sql = 'SELECT
                                            COUNT(usuariovacinado.usuario_id) AS `qtdtomadas`,
                                            aplicacoesporpessoa

                                            FROM `usuario` , `usuariovacinado` , `vacina`

                                            WHERE usuario.id = usuariovacinado.Usuario_id

                                                    AND vacina.id = usuariovacinado.Vacina_id
                                                    AND usuario.id = ?
                                                    AND vacina.id = ?
                                                    AND usuario.ativo
                                                    AND vacina.ativo

                                                            GROUP BY usuario.id';
 

                    $stmt = $this->conexao->prepare($sql)
                            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                    $stmt->bind_param('ii', $pessoaId, $vacinaId);
                }


		$stmt->bind_result($qtdtomadas, $aplicacoesporpessoa);
		$stmt->execute();
		$stmt->store_result();
		
		$existe = $stmt->num_rows;
		
		$stmt->fetch();
		$stmt->free_result();
		
		if($existe > 0) {
			
			//if($qtdtomadas == 0) "return 'vacinacaoCompleta';
			//if($qtdtomadas > 0) return 'vacinacaoIncompleta';
                        
                    $qtdDosesEspeciais = $this->SelecionarDosesEspeciais($vacinaId);
                    $qtdDosesEspeciais = count($qtdDosesEspeciais);
                    if($qtdDosesEspeciais){
                        if(($qtdtomadas)>= ($aplicacoesporpessoa-$qtdDosesEspeciais)) return 'vacinacaoCompleta';
                    }
                    
                    if($qtdtomadas > 0 && $campanhaId)                  return 'participouDaCampanha';
                    if($qtdtomadas >= $aplicacoesporpessoa)             return 'vacinacaoCompleta';
                    if($qtdtomadas >= $aplicacoesporpessoa-$qtdReforco) return 'faltaReforco';
                    if($qtdtomadas <  $aplicacoesporpessoa-$qtdReforco) return 'vacinacaoIncompleta';

          

		}
		
		if($existe == 0) return 'naoVacinado';
		
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro('Algum problema ocorreu ao tentar
				verificar o status de vacinação do indivíduo');
			
			return false;
		}
	}
	//--------------------------------------------------------------------------
        public function SelecionarDosesEspeciais($vacinaId)
        {

                $sql = 'SELECT numerodadose
                            FROM intervalodadose
                                WHERE tipoDaDose_id = 3
                                      AND Vacina_id = ?';


                $stmt = $this->conexao->prepare($sql)
                        or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                $stmt->bind_param('i', $vacinaId);
		$stmt->bind_result($numerodadose);
		$stmt->execute();
		$stmt->store_result();

		$existe = $stmt->num_rows;

		if ($existe > 0) while ($stmt->fetch()) $arr[] = $numerodadose;

		$stmt->free_result();

                if($existe > 0) return $arr;
                else return array();
        }
	//--------------------------------------------------------------------------
	public function ExibirListaDePessoasVacinaveisSemCampanha($vacina_id,
		$cidade_id, $nomeCriterio, $nomeMae, $cpf, $nasc, $paginaVacinar = 'vacinar', $pagina_atual = false)
	{
        
		// Ex Pesquisa: Diego Da Silva no sql vai ficar %Diego%Da%Silva%
		$explodeCaracteres = explode(' ',$nomeCriterio);
		$implodeCaracteres = implode('%',$explodeCaracteres);
		
		$nomeCriterio = "$implodeCaracteres";
		
		$explodeCaracteresMae = explode(' ', $nomeMae);
		$implodeCaracteresMae = implode('%', $explodeCaracteresMae);

		$nomeMae = "$implodeCaracteresMae";
			
		$vacina = $this->conexao->query("SELECT nome, faixaetariainicio,
			faixaetariafim FROM `vacina` WHERE id = $vacina_id AND ativo")
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		//die("$vacina_id, $cidade_id, $nomeCriterio");
		
		$linha = $vacina->fetch_assoc();
		
		$vacina->free_result();
		
		$vacina_nome = $linha['nome'];
		$faixaetariainicio = $linha['faixaetariainicio'];
		$faixaetariafim = $linha['faixaetariafim'];
		
		$data = new Data();
		
		$dataFaixaEtariaInicio = $data->DecrementarData(date('Y/m/d'), $faixaetariainicio);
		$dataFaixaEtariaFim = $data->DecrementarData(date('Y/m/d'), $faixaetariafim);
			
		// Se estiver marcado checkbox "nestaCidade" pega a cidade_id do SESSION:
		if( count($_POST) == 0 || (isset($_POST['nestaCidade']) && $_POST['nestaCidade'] == 'on' )) {
			
			$cidade = $_SESSION['cidade_id'];
		}
		
		// Senão estiver marcado, pega a cidade_id do select fornecido pelo ajax:
		elseif (isset($_POST['estado'], $_POST['cidade']) && $_POST['cidade'] != '0') {
			
			$cidade = $_POST['cidade'];
		}
		
		$sqlMae = false;
		if($nomeMae != 'vazio') $sqlMae = "AND usuario.mae LIKE '%$nomeMae%' ";
		
		$sqlNome = false;
		if( strlen($nomeCriterio) > 2) $sqlNome = "AND usuario.nome LIKE '%$nomeCriterio%'";
		
        
        ////////////////////////////////////////////////////////////////////////
        $sql = "SELECT COUNT(usuario.id)
					FROM  `usuario`, `bairro` , `cidade`
					WHERE (usuario.cpf = ? OR '$cpf' = 'vazio')
					AND (usuario.nascimento = ? OR '$nasc' = 'vazio')
					AND usuario.Bairro_id = bairro.id
					AND bairro.Cidade_id = cidade.id 
					AND cidade.id = ?
					AND usuario.ativo
                    AND usuario.vacinavel
					AND bairro.ativo
					$sqlNome
					$sqlMae";

		$pessoa = $this->conexao->prepare($sql)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$pessoa->bind_param('ssi',$cpf, $nasc, $cidade);
		
		$pessoa->bind_result($totalDeRegistros);
		
		$pessoa->execute();

        $pessoa->fetch();
        
        $pessoa->free_result();
        
        $html = new Html;
        $nomeDaSessao = 'paginacao_listarPessoasVacinaves';
        $pagina_atual = $html->TratarPaginaAtual($pagina_atual, $nomeDaSessao);
        ////////////////////////////////////////////////////////////////////////

		$limite = Html::LIMITE;
        
        $aPartirDe = ($pagina_atual - 1) * Html::LIMITE;
		
		$sql = "SELECT usuario.id, usuario.nome, usuario.mae, usuario.nascimento
					FROM  `usuario`, `bairro` , `cidade`
					WHERE (usuario.cpf = ? OR '$cpf' = 'vazio')
					AND (usuario.nascimento = ? OR '$nasc' = 'vazio')
					AND usuario.Bairro_id = bairro.id
					AND bairro.Cidade_id = cidade.id 
					AND cidade.id = ?
					AND usuario.ativo
                    AND usuario.vacinavel
					AND bairro.ativo
					$sqlNome
					$sqlMae
					ORDER BY usuario.nome
					LIMIT $aPartirDe, ?";

		$pessoa = $this->conexao->prepare($sql)
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$pessoa->bind_param('ssii',$cpf, $nasc, $cidade, $limite);
		
		$pessoa->bind_result($id, $nome, $mae, $nascimento);
		
		$pessoa->execute();
		
		$pessoa->store_result();
		
		$existe = $pessoa->num_rows;
		
		$arr = array();
		
		$crip = new Criptografia();

		if ($existe > 0) {
            
            ////////////////////////
            
			$html->ControleDePaginacao($totalDeRegistros, $nomeDaSessao,
						'PessoaVacinavel',
						"ExibirListaDePessoasVacinaveisSemCampanha($vacina_id, $cidade_id, $nomeCriterio, $nomeMae, $cpf, $nasc, $paginaVacinar, [paginaAtual])");
            
			///////////////////////

			$data = new Data();

			while ($pessoa->fetch()) {
				
				$linha = array();
				
				$status = $this->VerificarStatusDeVacinacao($id, $vacina_id);
				
				if($status == 'vacinacaoCompleta')
					$tituloDoLink = 'Vacinação completa';
				
				elseif($status == 'vacinacaoIncompleta')
					$tituloDoLink = 'Vacinação iniciada, mas não completada';
				
				elseif($status == 'naoVacinado')
					$tituloDoLink = 'Nenhuma dose aplicada neste indivíduo';
				
                elseif($status == 'faltaReforco')
					$tituloDoLink = 'Todas as doses aplicadas, faltando somente reforço(s).';



				$qs = $crip->Cifrar("pagina=Adm/$paginaVacinar&usuario_id={$id}&vacina_id=$vacina_id&campanha_id=0");

				if( $this->VerificarDoseAtrasada($id,$vacina_id) != false ){
				
					$linha[' '] = "<a href='?$qs'><img src='{$this->arquivoGerarIcone}?imagem=ok_vermelho' border='0' alt='Listar' /></a>";
				}
				else 
					$linha[' '] = "<a href='?$qs'><img src='{$this->arquivoGerarIcone}?imagem=ok' border='0' alt='Listar' /></a>";
						
				$linha['nome'] = "<a title='$tituloDoLink'
					href='?$qs'><span class='$status'>$nome</span></a>";
				
				if( strlen($mae) > 2) $linha['mãe'] = $mae;
				else				  $linha['mãe'] = '<em><span style="color: #CCC">Não informado</span></em>';
					
				$linha['nascimento'] = $data->InverterData($nascimento);
				
				if( $this->VerificarDoseAtrasada($id,$vacina_id)) $arr[] = $linha;
				elseif (isset($_SESSION['listarPessoasVacinaveis']['emAtraso'])
					&& $_SESSION['listarPessoasVacinaveis']['emAtraso'] != 'on') $arr[] = $linha;
			
			}
		}
		
		$pessoa->free_result();
		
		if($existe == 0) {
			
			// Não existe resultado com esse critério:
			return false;
		}
		if($existe < 0) {
			
			$this->AdicionarMensagemDeErro("Algum erro ocorreu ao tentar buscar
				$nomeCriterio na base de dados.");
				
			return false;
			
		}
		

		if( count($arr) ) {
			echo "<h3>$vacina_nome
			<span style='font-size: 12px; font-weight: normal'>
			(doses restantes: {$this->DosesDaUnidade($_SESSION['unidadeDeSaude_id'],
				$vacina_id)})</span></h3>";
		}
		
		Html::CriarTabelaDeArray($arr);
		
		if( count($arr) ) return $totalDeRegistros;
		
		return false;
	}
	//----------------------------------------------------------------------
	public function VerificarEstoqueDaUnidadeParaACampanha($campanha_id)
	{
		$sql = 'SELECT MAX(vacinadaunidade.quantidade)
				FROM `vacinadacampanha`, `vacinadaunidade`
					WHERE vacinadacampanha.Campanha_id = ?
					AND vacinadaunidade.UnidadeDeSaude_id = ?
					AND vacinadaunidade.Vacina_id = vacinadacampanha.Vacina_id';
		
		$vacina = $this->conexao->prepare ($sql)
						  or die($this->conexao->error);
		
		$unidade_id = $_SESSION['unidadeDeSaude_id'];
		$qtd = 0;
		$vacina->bind_param('ii', $campanha_id,  $unidade_id);
		$vacina->bind_result( $qtd );
		$vacina->execute();
		$vacina->fetch();
		
		return $qtd;
		
	}	
	//----------------------------------------------------------------------
	public function VerificarEstoqueDaUnidadeParaRotina($vacina_id)
	{
        $vacina = new Vacina;
        $vacina->UsarBaseDeDados();
        
        $vacinaQuePertence = $vacina->VacinaPertence($vacina_id);
        if($vacinaQuePertence) $vacina_id = $vacinaQuePertence;

		$sql = 'SELECT MAX(vacinadaunidade.quantidade)
				FROM `vacinadaunidade`
					WHERE vacinadaunidade.UnidadeDeSaude_id = ?
					AND vacinadaunidade.Vacina_id = ?';
		
		$vacina = $this->conexao->prepare ($sql)
						  or die($this->conexao->error);
		
		$unidade_id = $_SESSION['unidadeDeSaude_id'];
		$qtd = 0;
		$vacina->bind_param('ii', $unidade_id, $vacina_id);
		$vacina->bind_result( $qtd );
		$vacina->execute();
		$vacina->fetch();
		
		return $qtd;
		
	}
	//----------------------------------------------------------------------
	public function ExibirListaDePessoasVacinaveisPorVacina($campanha_id,
		$cidade_id, $nome, $mae, $cpf, $nasc, $pagina_atual = false)
	{
        $html = new Html;
		$nomeDaSessao = 'paginacao_listarPessoaCampanha';
		$pagina_atual = $html->TratarPaginaAtual($pagina_atual, $nomeDaSessao);
		
		$aPartirDe = ($pagina_atual - 1) * Html::LIMITE;
        
        ////////////////////////////////////////////////////////////////////////
		
		if (isset($_POST['cidade']) && $_POST['cidade'] > 0) $cidade_id = $_POST['cidade'];
		
		$limite = Html::LIMITE;
		
		$vacina = $this->conexao->prepare ('SELECT vacinadacampanha.id,
					vacinadaunidade.vacina_id FROM
					`vacinadacampanha`, `vacina`, `vacinadaunidade` WHERE
					vacinadacampanha.Vacina_id = vacina.id
					AND vacinadaunidade.Vacina_id = vacina.id
					AND quantidade > 0 
					AND vacinadaunidade.UnidadeDeSaude_id = ?
					AND campanha_id = ? AND vacina.ativo') or die($this->conexao->error);
		
		$unidade_id = $_SESSION['unidadeDeSaude_id'];
		
		$vacina->bind_param('ii', $unidade_id, $campanha_id);
		$vacina->bind_result($vacinadacampanha_id, $vacina_id );
		$vacina->execute();
		$vacina->store_result();
		
		$resultado = $vacina->num_rows;

		$arrIdVacinaDaCampanha = array();
		
		$listou = false;
		
		if($resultado > 0) {
					
			while ($vacina->fetch()) {			
				
				$arrIdVacinaDaCampanha[] = array($vacinadacampanha_id, $vacina_id);
	
			}
	
			
			foreach ($arrIdVacinaDaCampanha as $valor) {
	
				list($vacinadacampanha_id , $vacina_id) =  $valor;
						
                        
				$listou = $this->ExibirListaDePessoasVacinaveis($vacinadacampanha_id,
					$cidade_id, $vacina_id, $nome, $mae, $cpf, $nasc, $campanha_id, $aPartirDe);
                
                //echo "<h2>$listou</h2>";
                        
			}
            
                ////////////////////////
                $html->ControleDePaginacao($listou, $nomeDaSessao,
                            'PessoaVacinavel',
                            "ExibirListaDePessoasVacinaveisPorVacina($campanha_id, $cidade_id, $nome, $mae, $cpf, $nasc, [paginaAtual])");
                ///////////////////////
                
		}
		
		$vacina->free_result();
		
		if($resultado == 0) {
			
			// Não há dados para serem exibidos:
			return false;
		}
		if($resultado < 0) {
			
			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar exibir
				a lista de indivíduos.');
			
			return false;
		}
		
		return $listou;
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioPessoasVacinaveis()
	{
		?>
		<form name="form1" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>"
			onsubmit="return (ValidarCampoSelect(this.campanha, 'campanha') && ValidarCampoSelect(this.cidade, 'campanha'))">
			
		<select name="campanha" id="campanha"
			onblur="ValidarCampoSelect(this, 'campanha')">
		<?php
		$camp = $this->conexao->prepare('SELECT id, nome FROM `campanha` WHERE ativo ORDER BY nome')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$camp->bind_result($campanha_id, $campanha_nome);
		$camp->execute();
		while ($camp->fetch()) {
			echo "<option value='$campanha_id'>$campanha_nome</option>";
		}
		$camp->free_result();
		?>
		</select>
		<select name="cidade" id="cidade" onblur="ValidarCampoSelect(this, 'cidade')">
		<?php
		$camp = $this->conexao->prepare('SELECT id, nome FROM `cidade`')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
			
		$camp->bind_result($cidade_id, $cidade_nome);
		$camp->execute();
		while ($camp->fetch()) {
			echo "<option value='$cidade_id'>$cidade_nome</option>";
		}
		$camp->free_result();
		?>
		</select>
		<input type="submit" name="Enviar" value="Enviar" />
		</form>
		<?php

	}
	//--------------------------------------------------------------------------
	public function TotalDeDoses($vacina_id)
	{
		$sql = "SELECT numerodadose 
				FROM intervalodadose 
					WHERE Vacina_id = ?
                    -- AND TipoDaDose_id = 1
						ORDER BY numerodadose DESC LIMIT 1";
		
		$doses = $this->conexao->prepare($sql);
		$doses->bind_param('i', $vacina_id);
		$doses->bind_result($numeroDeDoses);
		$doses->execute();
		$doses->fetch();
		$doses->free_result();
		
		return  $numeroDeDoses;
		
	}	
	
	//--------------------------------------------------------------------------
	private function TotalDeCiclosCompletos($usuario_id, $vacina_id)
	{
			
		$ciclosPermitidos = $this->CiclosPermitidos($vacina_id);
		
		$totalDeDosesAplicadas = $this->TotalDeDosesAplicadas($usuario_id, $vacina_id);
		
		$totalDeDoses = $this->TotalDeDoses($vacina_id);
		
		$ciclosCompletos = 0;
					
		$resto =  $totalDeDosesAplicadas % $totalDeDoses;
		
		//echo "<h1> --->$totalDeDosesAplicadas>";
		
		$ciclosCompletos = ($totalDeDosesAplicadas-$resto) / $totalDeDoses;
		
		return $ciclosCompletos;
		
	}
	//--------------------------------------------------------------------------
	private function TotalDeDosesAplicadas($usuario_id, $vacina_id)
	{
		$sql = "SELECT COUNT(usuariovacinado.id)
				FROM usuariovacinado -- , intervalodadose
					WHERE usuariovacinado.Vacina_id = ?
					AND usuariovacinado.Usuario_id = ?
                   -- AND intervalodadose.numerodadose = usuariovacinado.numerodadose
                   -- AND intervalodadose.TipoDaDose_id = 1";

		$doses = $this->conexao->prepare($sql);
		$doses->bind_param('ii', $vacina_id, $usuario_id);
		$doses->bind_result($numeroDeDosesAplicadas);
		$doses->execute();
		$doses->fetch();
		$doses->free_result();
		
		return  $numeroDeDosesAplicadas;
		
	}	
	//--------------------------------------------------------------------------
	private function CiclosPermitidos($vacina_id)
	{
		$sql = "SELECT ciclos
				FROM vacina
					WHERE id = ?
					AND ativo";
		 
		$doses = $this->conexao->prepare($sql);
		$doses->bind_param('i', $vacina_id);
		$doses->bind_result($ciclosPermitidos);
		$doses->execute();
		$doses->fetch();
		$doses->free_result();
		
		return  $ciclosPermitidos;
		
	}
		
	//--------------------------------------------------------------------------
	public function PessoaFoiImunizada($usuario_id, $vacina_id,
            $exibirMensagem = false, $considerarCiclos = false,
            $considerarDoseEspecial = false)
	{
            if( $considerarCiclos ) {
                 $sql = "SELECT id
                           FROM `usuariovacinado`
                               WHERE Usuario_id = ?
                                   AND Vacina_id = ?
                                   AND numerodociclo =
                                   (
                                       SELECT MAX(numerodociclo)
                                           FROM `usuariovacinado`
                                               WHERE Usuario_id = $usuario_id
                                                   AND Vacina_id = $vacina_id
                                   )";
          
            }
            else {
                $sql = 'SELECT id
                            FROM `usuariovacinado`
                                WHERE Usuario_id = ?
                                AND Vacina_id = ?';
            }
            
            $usuario = $this->conexao->prepare($sql)
                    or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $usuario->bind_param('ii', $usuario_id, $vacina_id);
            $usuario->execute();
            $usuario->store_result();
            $qtd_usuario = $usuario->num_rows;
            $usuario->free_result();

            $sql = 'SELECT id
                        FROM `intervalodadose`
                        WHERE Vacina_id = ?
                       -- AND TipoDaDose_id = 1';

		$vacina = $this->conexao->prepare($sql)
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$vacina->bind_param('i', $vacina_id);
		$vacina->execute();
		$vacina->store_result();
		$qtd_vacina = $vacina->num_rows;
		$vacina->free_result();
		
		if($qtd_usuario == 0 || $qtd_vacina == 0) {

			//$this->AdicionarMensagemDeErro('Não existem doses ou pessoa vacinada
			//	para verificar se todas as doses foram aplicadas.');
			
			return false;
		}
		
		
		//??????? precisa de mensagem de erro???
		
		if($qtd_usuario < 0 || $qtd_vacina < 0) {

			//$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar
			//	verificar se a pessoa tomou todas as doses.');
			
			return false;
		}

                $doses = $considerarDoseEspecial ?
                         $qtd_vacina             :
                         $qtd_vacina - count($this->SelecionarDosesEspeciais($vacina_id));
                
		if($qtd_usuario >= $doses) { ////?????? troquei == por >=

			if($exibirMensagem) {

                            echo "<p style='color: green;'>
				Todas as doses necessárias para esta vacina em rotina já foram aplicadas.
				</p>";

                            if($qtd_vacina > $doses) echo "<span style='color: #BC1343;'>
				<ul><li><strong>Obs</strong>.: Doses especiais não são consideradas.
				</li></ul></span>";
                        }
		}

                if($qtd_usuario >= $qtd_vacina) return true;
                
		return false;

	}
	//--------------------------------------------------------------------------
	public function PessoaTomouVacina($usuario_id, $vacina_id)
	{
		
		$usuario = $this->conexao->prepare('SELECT id FROM `usuariovacinado` WHERE
			Usuario_id = ? AND Vacina_id = ?')
			or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$usuario->bind_param('ii', $usuario_id, $vacina_id);
		$usuario->execute();
		$usuario->store_result();
		$tomou = $usuario->num_rows;
		$usuario->free_result();
		
		if($tomou >= 0) return $tomou;
		
		if($tomou < 0) {

			$this->AdicionarMensagemDeErro('Algum erro ocorreu ao tentar checar
				se o indivíduo tomou esta vacina.');
			
			return false;
		}
	}
	//--------------------------------------------------------------------------
	public function GerarTabelaComDosesVacinarRetroativo($usuario_id, $vacina_id)
	{
        $this->ExibirTabelaVacinarRetroativo($usuario_id, $vacina_id);
        /*
		$this->ExibirDadosDeVacinacaoDoUsuario($usuario_id);

		$ultimadose =  $this->VerificarUltimaDose($usuario_id, $vacina_id);

		$vacina = $this->conexao->prepare('SELECT numerodadose, diaidealparavacinar, usuario.nascimento
			FROM `intervalodadose`, `usuario` WHERE
			Vacina_id = ?
			AND usuario.id = ?')

		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$vacina->bind_param('ii', $vacina_id,$usuario_id );
		$vacina->bind_result($numerodadose, $diaidealparavacinar, $nascimento);
		$vacina->execute();

		while ($vacina->fetch())  $arr[] = array($numerodadose, $diaidealparavacinar, $nascimento);

		$vacina->free_result();

		$data = new Data();

		$diasAcumulados = $aplicacao = 0;

		$ultimaData = $nascimento;

		$imagemDescontarEstoque = '<img src="'
			. $this->arquivoGerarIcone . '?imagem=decrementar" border="0"
			alt="Decrementar dose do estoque"  style="vertical-align: top"/>';

		$descontarDoEstoque = '<label><input type="checkbox" name="checkRetroativo[]"
			id="checkRetroativo[]" style="height: 15px;" /></label>';

        $descontarDoEstoqueDesable = '<label><input type="checkbox" name="checkRetroativo[]"
			id="checkRetroativo[]" disabled="true" style="height: 15px;"/></label>';

		$campos = '<input type="text" name="dataRetroativa[]"  id="dataRetroativa[]"
				  		style="width: 100%; border: 0px none; background: none;
				  		text-align: center"
				  		maxlength="10"
				  		onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara(\'DATA\', this, event);"
				        onkeyup="Mascara(\'DATA\', this, event);
				        if(this.value.length == 10)
				        document.getElementById(\'codigoDaImagem\').focus();"
				        onblur="return ValidarData(this);"/>';


		$camposDesable = '<input type="text" name="dataRetroativa[]"  id="dataRetroativa[]"
				  		style="width: 100%; border: 0px none; background: none;
				  		text-align: center" disabled="true"
				  		maxlength="10" value="data"
				  		onkeypress="return Digitos(event, this);"
				        onkeydown="return Mascara(\'DATA\', this, event);"
				        onkeyup="return Mascara(\'DATA\', this, event);"
				        onblur="return ValidarData(this);"/>';

		$dataIdealDaDose = $dataIdealAplicada = false;

		// Usado também lá embaixo, para inserção ou não do botão "Confirmar"
		$pessoaImunizada = $this->PessoaFoiImunizada($usuario_id, $vacina_id, true);

		$i = 0;
		foreach ($arr as $valor) {

			list($numerodadose, $diaidealparavacinar, $nascimento) = $valor;

			if($this->DataHoraVacinacao($usuario_id, $vacina_id, $numerodadose)) $i++;
		}
		$ultimadose = $i;

		foreach ($arr as $valor) {

			list($numerodadose, $diaidealparavacinar, $nascimento) = $valor;

			$Estoque = $descontarDoEstoqueDesable;
            if($this->DosesDaUnidade($_SESSION['unidadeDeSaude_id'], $vacina_id)) $Estoque = $descontarDoEstoque;

			$aplicacao = false;
			$aplicacao = $this->DataHoraVacinacao($usuario_id, $vacina_id, $numerodadose);

			$dataIdeal =  $data->IncrementarData($ultimaData, $diaidealparavacinar);
			$ultimaData = $dataIdeal;

			if($aplicacao){
				list($ultimaData) = explode(' ', $aplicacao);
				$Estoque = $descontarDoEstoqueDesable;
			}
			elseif($numerodadose+1 >= $i) $ultimaData = $dataIdeal;

			if($numerodadose > $i+1) $aplicacao = $camposDesable;

			if(!$aplicacao) $aplicacao = $campos;

			if(!substr_count($aplicacao, '<')) {
				list($aplicacao) = explode(' ', $aplicacao);
				$aplicadoEm = "Vacinado em ".$data->InverterData($aplicacao);
			}
			else $aplicadoEm = $aplicacao;

            $textoDataIdeal = $data->InverterData($dataIdeal);
            $textoNumeroDaDose = "{$numerodadose}ª";

            if($this->VerificarTipoDaDose($vacina_id, $numerodadose) == 2) {

                static $numeroDoReforco = 1;

                $titulo = "A {$numerodadose}ª dose é o {$numeroDoReforco}º reforço";

                $textoNumeroDaDose = $this->AplicarEstiloDoseDeReforco('R'. $numeroDoReforco++, $titulo);
                $textoDataIdeal    = $this->AplicarEstiloDoseDeReforco($textoDataIdeal, $titulo);
                $aplicadoEm        = $this->AplicarEstiloDoseDeReforco($aplicadoEm);
            }

			$arrDoses [] = array('dose' =>					$textoNumeroDaDose,
								 'data ideal' =>			$textoDataIdeal,
								 'aplicação' =>				$aplicadoEm,
								 $imagemDescontarEstoque =>	$Estoque);

		}

			$crip = new Criptografia();

			$numerodadose = $ultimadose + 1;

			$qs = $crip->Cifrar("pagina=Adm/vacinarRetroativo&numerodadose=$numerodadose&usuario_id=$usuario_id&vacina_id=$vacina_id&campanha_id=0");

			?>
		 <form method='post' name='formVacinarRetroativo' id='formVacinarRetroativo'
		 	action='?<?php echo $qs ?>'
			onsubmit="return (
				ValidarData(document.getElementsByName('dataRetroativa[]')[0])
				&& ValidarVacinarRetroativo('<?php echo $dataIdealDaDose;?>',
					document.getElementsByName('dataRetroativa[]')[0].value))">

		<?php

		Html::CriarTabelaDeArray($arrDoses);

		if( !$pessoaImunizada ) {

			$botao = new Vacina();
			$botao->ExibirBotoesDoFormulario('Confirmar');
		}

		echo '</form>';

        echo '<div style="padding-left:90px; padding-top:20px;">',
                $this->ListarObs($usuario_id, $vacina_id),
                '</div>';


		$voltar = new Form();
		$voltar->BotaoVoltarHistorico();
        */
        //$this->TabelaVacinarRetroativo($usuario_id, $vacina_id);
	}
//------------------------------------------------------------------------------

    /**
     * Método remodelado para listar doses ideais para vacinar, baseadas em
     * campos não calculados, já gravados no banco.
     *
     * @param int $usuario_id
     * @param int $vacina_id
     * @param int $campanha_id
     */
    public function ExibirTabelaVacinarRetroativo($usuario_id, $vacina_id)
    {
        // Array que recebe dose, data ideal, validade da dose...
        $arrDoses = array();


        // Retorna array bidimensional com os seguintes dados:
        // - $diasIdeal
        // - $dose
        // - $diasAtrasoMax
        // - $nomeVacina
        // - $doseBase


        $arrConfiguracoes = $this->CriarArrayDeConfiguracoesDeDoses($vacina_id);

        // Recupera o nascimento do usuario
        list($nascimento) = $this->ExibirDadosDeVacinacaoDoUsuario($usuario_id);
        $dataUltimaDose = $nascimento;

       $data = new Data();

        // Verifica se precisa listar a coluna "Validade da dose" para o usuário
        $listarValidade = $this->ListarValidadeDaDose($vacina_id);

        $this->_vacinarHabilitado = true;
        $this->_campoEstoqueHabilitado = true;

        // Guarda as doses ideais baseadas no nascimento. Útil quando a data ideal
        // para vacinar não se baseia na anterior, mas na dose base:
        $datasDosesIdeais = array();

        $novaDataDoseIdeal = 0;
        $i = 0; // Contador para aplicar estilo à linha inteira:
        foreach($arrConfiguracoes as $configuracao)
        {
            list($diasIdeal, $dose, $diasAtrasoMax, $nomeVacina, $doseBase) = $configuracao;

            $tipoDaDose = $this->VerificarTipoDaDose($vacina_id, $dose);

            if($diasAtrasoMax == '43800') $diasAtrasoMax = 'Indeterminado';
            //if($diasAtrasoMax == '43800') $dataAtrasoMax = 'Indeterminado';

            // Cria o botão de aplicar vacina:
            if($dose > $this->VerificarUltimaDose($usuario_id, $vacina_id))
            {
                $botaoAplicar = $this->CampoDataRetroativa();
            }
            else
            {
                // Pega data/hora e aproveita só a data, invertendo em seguida:
                list($dataVacinacao) = explode(' ',
                      $this->DataHoraVacinacao($usuario_id, $vacina_id, $dose));

                $dataVacinacao = $data->InverterData($dataVacinacao);

                // Então o botão "aplicar" dá lugar a data de vacinação:
                $botaoAplicar = "Vacinado em $dataVacinacao";
                $this->_campoEstoqueHabilitado = false;
            }

            $dataAtrasoMax = $diasAtrasoMax;
            if( $diasAtrasoMax != 'Indeterminado' ) $dataAtrasoMax = $data->IncrementarData($dataUltimaDose, $diasIdeal+$diasAtrasoMax);

            // Verifica se a doseBase é a padrão, ou seja: a DOSE ANTERIOR
            if( $doseBase == 0 )
            {
                $datasDosesIdeais[$dose] = $data->IncrementarData($dataUltimaDose, $diasIdeal);
            }

            // Se não for a anterior, incrementa os dias baseando-se na data da
            // dose base (no caso de hepatite B, a 3a. dose é 180 dias após a 1a)
            else
            {
                /*
                echo '________________________________________________________________<br><br>';
                echo 'Dose Base : '. $doseBase . '<br>';
                echo 'Data da dose base : '. $datasDosesIdeais[$doseBase] . '<br>';
                echo 'Dias Ideais : '. $diasIdeal . '<br>';
                echo 'Data Incrementada : '. $data->IncrementarData($datasDosesIdeais[$doseBase], $diasIdeal) . '<br>';
                echo '________________________________________________________________<br><br>';
                */
                $datasDosesIdeais[$dose] = $data->IncrementarData($datasDosesIdeais[$doseBase], $diasIdeal);
            }
            $dataDoseIdeal = $data->InverterData($datasDosesIdeais[$dose]);
            if( $dataAtrasoMax != 'Indeterminado' ) $dataAtraso = $data->InverterData($dataAtrasoMax);
            else $dataAtraso = $dataAtrasoMax;


            $arrProximaDose = $this->ProximaDose($usuario_id, $vacina_id);

            if( $arrProximaDose )
            {
                list($proximaDose, $ultimaDoseAplicada) = $arrProximaDose;

                $proximaDosePosterior = $ultimaDoseAplicada + 2;

                if($ultimaDoseAplicada+1 == $dose) $novaDataDoseIdeal = $proximaDose;
                elseif($proximaDosePosterior <= $dose) $novaDataDoseIdeal = $data->IncrementarData($proximaDose, $diasIdeal);
                else $novaDataDoseIdeal =  ' * ';

                if($doseBase > 0){
                    list($novaDataDoseIdeal) = explode(' ',
                        $this->DataHoraVacinacao($usuario_id, $vacina_id, $doseBase));



                  

                    /*

                    Codigo antigo:
                    $novaDataDoseIdeal = $data->IncrementarData($novaDataDoseIdeal, $diasIdeal);

                    Adicionei esse if abaixo prq tava dando erro em tdata 140... a "$novaDataDoseIdeal"
                    as vezes ia vazia e dava erro!

                    Maykon | 25-11-2010

                    */

                   // ###########
                   if(strlen($novaDataDoseIdeal) > 0)
                        $novaDataDoseIdeal = $data->IncrementarData($novaDataDoseIdeal, $diasIdeal);
                   else $novaDataDoseIdeal = '';
                   // ###########



                   if( $data->CompararData($novaDataDoseIdeal, '<=', $dataUltimaVacinacao )
                        && $dataUltimaVacinacao > 0)
                    {
                        // Incrementa 60 dias a última dose aplicada (segundo Nádia)
                       $novaDataDoseIdeal = $data->IncrementarData($dataUltimaVacinacao, 60);
                    }
                }
                // data de aplicacao da dose
                list($dataUltimaVacinacao) = explode(' ', $this->DataHoraVacinacao($usuario_id, $vacina_id, $dose));


                // Se for uma data, inverte para o formato brasileiro:
                if( strlen($novaDataDoseIdeal) == 10 )
                {
                    $novaDataDoseIdeal = $data->InverterData($novaDataDoseIdeal);
                }
/*
                echo '________________________________________________________________<br><br>';
                echo 'Numero da dose : '. $dose . '<br>';
                echo 'Dose Base : '. $doseBase . '<br>';
                echo 'Proxima dose : '. $proximaDose . '<br>';
                echo 'Dose Ideal : '. $diasIdeal . '<br>';
                echo 'Data dose ideal : '. $datasDosesIdeais[$dose] . '<br>';
                echo 'Nova dose ideal : '. $novaDataDoseIdeal . '<br>';
                echo '<i><b>Data dose base : </b></i>'. $datasDosesIdeais[$doseBase] . '<br>';
                echo '________________________________________________________________<br><br>';
*/

            }

            // Icone e check descontar do estoque
            list($imagemDescontarEstoque, $descontarDoEstoque) = $this->CampoDescontarDoEstoque($vacina_id);

            if( $listarValidade )
            {
                // Listando a coluna "Validade da dose":
                $arrDoses[$dose] = array('dose'             => $dose,
                                 //   'ideal para vacinar'  => $diasIdeal,
                                    'data para vacinar'     => $dataDoseIdeal, //  baseado no nascimento
                                    'data validade'         => $dataAtraso,
                                  //  'validade da dose'    => $diasAtrasoMax,
                                    'nova data'             => $novaDataDoseIdeal,
                                    'aplicar'               => $botaoAplicar,
                                    $imagemDescontarEstoque => $descontarDoEstoque);
            }
            else
            {
                $arrDoses[$dose] = array('dose'              => $dose,
                                  //  'ideal para vacinar'   => $diasIdeal,
                                    'data para vacinar'      => $dataDoseIdeal, //  baseado no nascimento
                                    'nova data'              => $novaDataDoseIdeal,
                                    'aplicar'                => $botaoAplicar,
                                    $imagemDescontarEstoque  => $descontarDoEstoque);
            }

            if( $arrProximaDose ) unset($arrDoses[$dose]['data validade']);
            else                  unset($arrDoses[$dose]['nova data']);

            // Formatação da linha inteira para os textos e estilos de acordo
            // com o tipo de dose. ATENÇÃO: O método usa um ponteiro!
            $this->AplicarEstiloParaLinha($arrDoses[$dose], $tipoDaDose);

            $dataUltimaDose = $datasDosesIdeais[$dose];
        }

        list($ultimadose) = $this->VerificarUltimaDoseComData($usuario_id, $vacina_id);

        $proximaDose =    isset($proximaDose) ?
            $data->InverterData($proximaDose) :
            $data->InverterData($nascimento);

        $this->ExibirFormVacinacaoRetroativa($arrDoses, 
                                             $usuario_id,
                                             $vacina_id,
                                             $ultimadose,
                                             $proximaDose);
    }

	//--------------------------------------------------------------------------
    /**
     *
     */
	public function ExibirFormVacinacaoRetroativa($arrDoses, $usuario_id,
                               $vacina_id, $ultimadose, $proximaDose)
	{
        $crip = new Criptografia();

		$numerodadose = $ultimadose + 1;

		$qs = $crip->Cifrar('pagina=Adm/vacinarRetroativo&numerodadose='
                          . "$numerodadose&usuario_id=$usuario_id&vacina_id="
                          . "$vacina_id&campanha_id=0");
        ?>
         
		 <form method='post' name='formVacinarRetroativo' id='formVacinarRetroativo'
		 	action='?<?php echo $qs ?>'
			onsubmit="return (ValidarData(document.getElementsByName('dataRetroativa[]')[0])
				&& ValidarVacinarRetroativo('<?php echo trim($proximaDose)?>',
					document.getElementsByName('dataRetroativa[]')[0].value))">
		<?php

		Html::CriarTabelaDeArray($arrDoses);

        // Só exibe o botão para vacinar se a pessoa ainda não tomou todas as
        // doses:
        if( !$this->PessoaFoiImunizada($usuario_id, $vacina_id,
                                  $exibirMensagem = true,
                                  $considerarCiclos = false,
                                  $considerarDoseEspecial = true) )
        {
			$botao = new Vacina();
			$botao->ExibirBotoesDoFormulario('Confirmar');
        }

		echo '</form>';

        echo '<div style="padding-left:90px; padding-top:20px;">',
                $this->ListarObs($usuario_id, $vacina_id),
             '</div>';

        $voltar = new Form();
        $voltar->BotaoVoltarHistorico();

    }
	//--------------------------------------------------------------------------
    /**
     * Exibe o campo com input para o usuário digitar a data retroativa.
     */
	public function CampoDataRetroativa()
	{
       $campo = '<input type="text" name="dataRetroativa[]"  id="dataRetroativa[]"
                style="width: 100%; border: 0px none; background: none;
                text-align: center"
                maxlength="10"
                onfocus="if(this.value.length == 0)
                         {
                             this.style.backgroundImage=\'url(./Imagens/digiteDataDeVacinacao.png)\';
                             this.style.backgroundRepeat=\'no-repeat\'
                             this.style.backgroundPosition=\'center\'
                         }"
                onkeypress="return Digitos(event, this);"
                onkeydown="return Mascara(\'DATA\', this, event);"
                onkeyup="Mascara(\'DATA\', this, event);
                if(this.value.length > 0)   this.style.background = \'none\';
                else
                {
                    this.style.backgroundImage=\'url(./Imagens/digiteDataDeVacinacao.png)\';
                    this.style.backgroundRepeat=\'no-repeat\'
                    this.style.backgroundPosition=\'center\'
                }
                if(this.value.length == 10) document.getElementById(\'codigoDaImagem\').focus();"
                onblur="return ValidarData(this);"/>';

        if( $this->_vacinarHabilitado )
        {
            $valor = $campo;
            $this->_campoEstoqueHabilitado = true;
        }
        else
        {
            $valor = '<em><span style="color: #CCC">[data]</span></em>';
        }

        $this->_vacinarHabilitado = false;

        return $valor;
    }
	//--------------------------------------------------------------------------
    /**
     *
     */
	public function CampoDescontarDoEstoque($vacina_id, $desabilitado = false)
	{

        $semEstoque  = '';
        if(!$this->VerificarEstoqueDaUnidadeParaRotina($vacina_id))
            $semEstoque = 'onClick="alert(\'Vacina sem estoque!\'); '
                        . 'this.disabled=true; this.checked=false; "';


        $imagemDescontarEstoque = '<img src="'
			. $this->arquivoGerarIcone . '?imagem=decrementar" border="0"
			alt="Decrementar dose do estoque"  style="vertical-align: top"/>';

		$descontarDoEstoque = '<label><input type="checkbox" name="checkRetroativo"
			id="checkRetroativo" style="height: 15px;" '.$semEstoque.' /></label>';

        $descontarDoEstoqueDesable = '<label><input type="checkbox" disabled="true" style="height: 15px;"/></label>';

        $arr = Array($imagemDescontarEstoque, $this->_campoEstoqueHabilitado ?
                                              $descontarDoEstoque
                                              : $descontarDoEstoqueDesable);

        $this->_campoEstoqueHabilitado = false;

        return $arr;
    }
	//--------------------------------------------------------------------------
    /**
     * Exibe o campo com input para o usuário digitar a data retroativa.
     */
	public function CampoDataRetroativaDesabilitado()
	{
        return '<input type="text" name="dataRetroativa[]" id="dataRetroativa[]"
				  		style="width: 100%; border: 0px none; background: none;
				  		text-align: center" value="data" disabled="true" />';
    }
	//--------------------------------------------------------------------------
    /**
     * Método que exibe a tabela com vacinação retroativa.
     *
     * @staticvar int $numeroDoReforco
     * @param int $usuario_id
     * @param int $vacina_id
     */
	public function TabelaVacinarRetroativo($usuario_id, $vacina_id)
	{

        $semEstoque  = '';
        if(!$this->VerificarEstoqueDaUnidadeParaRotina($vacina_id))
            $semEstoque = 'onClick="alert(\'Vacina sem estoque!\'); '
                        . 'this.disabled=true; this.checked=false; "';
                        
		$this->ExibirDadosDeVacinacaoDoUsuario($usuario_id);
		
		$ultimadose =  $this->VerificarUltimaDose($usuario_id, $vacina_id);

        $arr = $this->CriarArrayDeConfiguracoesDeDoses($vacina_id);
		
		$diasAcumulados = $aplicacao = 0;
		
		$ultimaData = $this->Nascimento($usuario_id);
	        
		$imagemDescontarEstoque = '<img src="'
			. $this->arquivoGerarIcone . '?imagem=decrementar" border="0"
			alt="Decrementar dose do estoque"  style="vertical-align: top"/>';
		
		$descontarDoEstoque = '<label><input type="checkbox" name="checkRetroativo"
			id="checkRetroativo" style="height: 15px;" '.$semEstoque.' /></label>';
            
        $descontarDoEstoqueDesable = '<label><input type="checkbox" disabled="true" style="height: 15px;"/></label>';
		
		$inputData = $this->CampoDataRetroativa();
		
		$inputDataDesabilitado = $this->CampoDataRetroativaDesabilitado();
		
		$dataIdealDaDose = $dataIdealAplicada = false;
		
		// Usado também lá embaixo, para inserção ou não do botão "Confirmar"
		$pessoaImunizada = $this->PessoaFoiImunizada($usuario_id, $vacina_id, true);

		$i = 0;
		foreach ($arr as $valor) {

            list($diaidealparavacinar, $numerodadose) = $valor;

            //list($numerodadose, $diaidealparavacinar, $nascimento) = $valor;
			
			if($this->DataHoraVacinacao($usuario_id, $vacina_id, $numerodadose)) $i++;
		} 
		$ultimadose = $i;

		$data = new Data();
		foreach ($arr as $valor)
        {
            list($diaidealparavacinar, $numerodadose) = $valor;
			//list($numerodadose, $diaidealparavacinar, $nascimento) = $valor;
			
			$Estoque = $descontarDoEstoqueDesable;
            if($this->DosesDaUnidade($_SESSION['unidadeDeSaude_id'], $vacina_id)) $Estoque = $descontarDoEstoque;
			
			$aplicacao = false;
			$aplicacao = $this->DataHoraVacinacao($usuario_id, $vacina_id, $numerodadose);
			
			$dataIdeal =  $data->IncrementarData($ultimaData, $diaidealparavacinar);
			$ultimaData = $dataIdeal;
				
			if($aplicacao){
				list($ultimaData) = explode(' ', $aplicacao);
				$Estoque = $descontarDoEstoqueDesable;
			}
			elseif($numerodadose+1 >= $i) $ultimaData = $dataIdeal;
			
			if($numerodadose > $i+1) $aplicacao = $inputDataDesabilitado;
			
			if(!$aplicacao) $aplicacao = $inputData;
			
			if(!substr_count($aplicacao, '<'))
            {
				list($aplicacao) = explode(' ', $aplicacao);
				$aplicadoEm = "Vacinado em ".$data->InverterData($aplicacao);
                $this->_campoEstoqueHabilitado = false;
			}
			else $aplicadoEm = $aplicacao;

            $textoDataIdeal = $data->InverterData($dataIdeal);
            $textoNumeroDaDose = "{$numerodadose}ª";

            if($this->VerificarTipoDaDose($vacina_id, $numerodadose) == 2) {

                static $numeroDoReforco = 1;

                $titulo = "A {$numerodadose}ª dose é o {$numeroDoReforco}º reforço";

                $textoNumeroDaDose = $this->AplicarEstiloDoseDeReforco('R'. $numeroDoReforco++, $titulo);
                $textoDataIdeal    = $this->AplicarEstiloDoseDeReforco($textoDataIdeal, $titulo);
                $aplicadoEm        = $this->AplicarEstiloDoseDeReforco($aplicadoEm);
            }

			$arrDoses [] = array('dose' =>					$textoNumeroDaDose,
								 'data ideal' =>			$textoDataIdeal,
								 'aplicação' =>				$aplicadoEm,
								 $imagemDescontarEstoque =>	$Estoque);
			
		}
		
			$crip = new Criptografia();
	
			$numerodadose = $ultimadose + 1;
			
			$qs = $crip->Cifrar("pagina=Adm/vacinarRetroativo&numerodadose=$numerodadose&usuario_id=$usuario_id&vacina_id=$vacina_id&campanha_id=0");
		
			?>
		 <form method='post' name='formVacinarRetroativo' id='formVacinarRetroativo'
		 	action='?<?php echo $qs ?>'
			onsubmit="return (
				ValidarData(document.getElementsByName('dataRetroativa[]')[0])
				&& ValidarVacinarRetroativo('<?php echo $dataIdealDaDose;?>',
					document.getElementsByName('dataRetroativa[]')[0].value))">
		
		<?php	
		
		Html::CriarTabelaDeArray($arrDoses);
		
		if( !$pessoaImunizada ) {
			
			$botao = new Vacina();
			$botao->ExibirBotoesDoFormulario('Confirmar');
		}
		
		echo '</form>';

        echo '<div style="padding-left:90px; padding-top:20px;">',
                $this->ListarObs($usuario_id, $vacina_id),
                '</div>';


		$voltar = new Form();
		$voltar->BotaoVoltarHistorico();
	}
	//--------------------------------------------------------------------------
	public function ExibirConfirmacaoParaFazerNovoCadastro($mensagem, $pagina)
	{
		?>
        <div class="msgErro" id="containerCadastro" style="visibility: visible;">
            <div class="barraDeTituloMsgErro" id="tituloCadastro" title="Fechar" style="visibility: visible;"
                 onclick="document.getElementById('containerCadastro').style.visibility = 'hidden';
                 document.getElementById('tituloCadastro').style.visibility = 'hidden';
                 document.getElementById('corpoCadastro').style.visibility = 'hidden';">
                &nbsp;Novo cadastro
            </div>
            <div class="corpoMsgErro" id="corpoCadastro" style="visibility: visible;"
                 onclick="javascript://">
            <?php echo $mensagem?><p><center>
            <button type="button" onclick="window.location = '?<?php echo $pagina?>'" >Sim</button>
            <button type="button" onclick="document.getElementById('containerCadastro').style.visibility = 'hidden';
                 document.getElementById('tituloCadastro').style.visibility = 'hidden';
                 document.getElementById('corpoCadastro').style.visibility = 'hidden';">Não</button>
            </center></p></div>
        </div>
		<?php
	}
	//--------------------------------------------------------------------------
	/**
	 * Verificação que devolve o número da dose em atraso se houver
	 * 
	 * @param int $usuario_id
	 * @param int $vacina_id
	 * @return int
	 */
	public function VerificarDoseAtrasada($usuario_id, $vacina_id)
	{
        $aplicacoesPorPessoa = $this->TotalDeDoses($vacina_id);

        //Pegando a última dose e a data tomada referente ao último ciclo iniciado
        $conexao = $this->conexao->prepare('SELECT MAX(numerodadose) AS dose, 
                DATE_FORMAT(MAX(datahoravacinacao), "%Y/%m/%d") AS data,
                numerodociclo FROM `usuariovacinado` 
                WHERE Usuario_id = ? AND Vacina_id = ?
                AND numerodociclo = (SELECT MAX(numerodociclo) FROM `usuariovacinado`
                WHERE Usuario_id = ? AND Vacina_id = ?)
                GROUP BY Usuario_id
                LIMIT 1')
        or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
        
        $conexao->bind_param('iiii', $usuario_id, $vacina_id,$usuario_id, $vacina_id);
        $conexao->bind_result($numeroDaDose, $dataHoraVacinacao, $numeroDoCiclo);
        $conexao->execute();
        $conexao->store_result();
        
        $qto = $conexao->num_rows;
        
        $data = new Data();
        
        if ( $qto > 0 )	{
                    
            $conexao->fetch();
            $conexao->free_result();
    
        }
        else
            if( $qto == 0 ){
                
                $conexao->free_result();
                
                $conexao = $this->conexao->prepare('SELECT DATE_FORMAT(nascimento, "%Y/%m/%d")
                    FROM usuario WHERE id = ?');
    
                $conexao->bind_param('i', $usuario_id);
                $conexao->bind_result($nascimento);
                
                $conexao->execute();
                
                $conexao->fetch();
                
                $conexao->free_result();
                
                list($diaIdealParaVacinar) = $this->DiasIdeaisParaVacinar($vacina_id, 1);
                
                $diaProximaDose = $data->IncrementarData($nascimento, $diaIdealParaVacinar);
                
                if( $data->CompararData($diaProximaDose, '>') ) return false;
                else return 1;
        
    
            }

        if ( $numeroDaDose == $aplicacoesPorPessoa )
            return false;
        else {
            list($diaIdealParaVacinar) = $this->CalcularNovaDataIdeal($dataHoraVacinacao,$vacina_id, $numeroDaDose + 1);
  
            if( $data->CompararData($diaIdealParaVacinar, '>') ) return false;
            else return $numeroDaDose + 1;
            
        }
	}	
	//--------------------------------------------------------------------------
	/**
	 * Função que controla a função recursiva "CapturarDependencia"
	 *
	 * @param int $vacina_id
	 * @return array
	 */	
	public function GerarDependencia($vacina_id, $doMenorParaMaior = true)
	{

		$this->_dependencia = array();
		$this->CapturarDependencia($vacina_id, $doMenorParaMaior);
		return $this->_dependencia;
		
	}
	//--------------------------------------------------------------------------
	/**
	 * Função recursiva que captura todas as dependencia entre a vacina passada
	 * como parâmetro e salva em um array global "$_dependencia"
	 *
	 * @param int $vacina_id
	 */
	public function CapturarDependencia($vacina_id, $doMenorParaMaior)
	{
		
		if( $doMenorParaMaior ){
		
			$conexao = $this->conexao->prepare('SELECT Vacina_id_restringida
			FROM `dependenciadavacina` WHERE Vacina_id_restricao = ?');
		
			$conexao->bind_param('i', $vacina_id);
			$conexao->bind_result($restringida);
			
			$conexao->execute();
			$conexao->store_result();
					
			if( $conexao->num_rows > 0 ){
				
				$arrTempo = array();
				
				while($conexao->fetch()){
				
					$this->_dependencia[] = $restringida;	
					$arrTempo[] = $restringida;
					
				}
				
				$conexao->free_result();
				
				if( count($arrTempo) )
				foreach( $arrTempo as $tempo )				
				$this->CapturarDependencia($tempo, $doMenorParaMaior);
				
			} else				
				$conexao->free_result();
							
		} else {

			$conexao = $this->conexao->prepare('SELECT Vacina_id_restricao
			FROM `dependenciadavacina` WHERE Vacina_id_restringida = ?');
		
			$conexao->bind_param('i', $vacina_id);
			$conexao->bind_result($restricao);
			
			$conexao->execute();
			$conexao->store_result();
			
			$arrTempo = array();
					
			while( $conexao->fetch() ){
				
				$arrTempo[] = $restricao;
				$this->_dependencia[] = $restricao;
		
			}
			
			$conexao->free_result();
			
			if( count($arrTempo) )
			foreach( $arrTempo as $tempo )			
			$this->CapturarDependencia($tempo, $doMenorParaMaior);
			
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * Função que verifica se um vacina que restringe outra já foi aplicada,
	 * limitando as restringidas a somente vizualização para um determinado 
	 * usuário.
	 *
	 * @param int $usuario_id
	 * @param int $vacina_id
	 * @return int
	 */
	public function VerificarRestricao($usuario_id, $vacina_id)
	{
		
		$arrDependencias = $this->GerarDependencia($vacina_id);
		//print_r($arrDependencias);
		$ultimaDoseDependencias = 0;
		$ultimaDose = $this->VerificarUltimaDose($usuario_id, $vacina_id);
		$ultimoCiclo = $this->VerificarUltimoCiclo($usuario_id, $vacina_id);
		
		foreach( array_reverse($arrDependencias) as $vacinaDependente )
		{

			$ultimaDoseDependencias = $this->VerificarUltimaDose($usuario_id, $vacinaDependente);
			$ultimoCicloDependencias = $this->VerificarUltimoCiclo($usuario_id, $vacinaDependente);
			
			if( $ultimoCicloDependencias > $ultimoCiclo )
				return $vacinaDependente;
				
			elseif( $ultimoCicloDependencias == $ultimoCiclo ){
				
				if( $ultimaDoseDependencias > $ultimaDose )
					return $vacinaDependente;
				
			}
			
		}
		
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Função que verifica se uma vacina restringida foi realmente vacina por ela
	 * ou se há alguma "vácina que restringe" possue a mesma dose indicando
	 * que foi vacina por esta.Se for devolverá 0, se não devolverá a id da vacina. 
	 * 
	 * @param int vacina_id
	 * @param int usuario_id
	 * @return int
	 */
	public function CompararDosesDependentes($usuario_id, $vacina_id, $numeroDaDose, $numeroDoCiclo, $vacinar=true)
	{
		
		$arrDependentes = $this->GerarDependencia( $vacina_id, $vacinar);

		foreach( array_reverse($arrDependentes) as $vacinaDependente )
		{

			if($this->TestarSeDoseFoiAplicada($numeroDaDose, $usuario_id, 
			$vacinaDependente, $numeroDoCiclo)){
					
				return $vacinaDependente;	
				
			}
			
		}
		return 0;
	}
	// ??????? O CORRETO ABAIXO É NEGAR, PORÉM O MÉTODO DE CIMA, ESTÁ RETORNANDO
	// QUE HÁ DEPENDÊNCIA, MESMO PARA VACINAS QUE NÃO TEM... 
	//--------------------------------------------------------------------------
	public function PermiteVacinar($usuario_id, $vacina_id, $numeroDaDose, $numeroDoCiclo)
	{
		
		return !($this->CompararDosesDependentes($usuario_id, $vacina_id,
			$numeroDaDose, $numeroDoCiclo))	;
	}
	//--------------------------------------------------------------------------
	public function VerificarRigidezDeAplicacao($vacina_id)
	{
		$stmt = $this->conexao->prepare('SELECT seguirarisca FROM vacina WHERE id = ?');
		$stmt->bind_param('i', $vacina_id);
		$stmt->bind_result($aRisca);
		
		$stmt->execute();
		$stmt->store_result();
		if( $stmt->num_rows > 0 ){
			
			$stmt->fetch();
			$stmt->free_result();
			
			if ( $aRisca == false )
				return 0;
			else 
				return 1; 
			
		} else {

			return false;
			
		}
	}
	//--------------------------------------------------------------------------
	public function VerificarUltimaDoseComData($usuario_id,$vacina_id)
	{
        $sql = 'SELECT numerodadose, DATE(datahoravacinacao) '
		     . 'FROM usuariovacinado '
             . 'WHERE usuariovacinado.Vacina_id = ? '
             .     'AND usuariovacinado.Usuario_id = ? '
             .     'ORDER BY numerodadose DESC';

		$stmt = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

		$stmt->bind_param('ii', $vacina_id, $usuario_id);
		$stmt->bind_result($ultimaDose, $dataHoraVacinacao);
		$stmt->execute();
		$stmt->store_result();
		
		if( $stmt->num_rows > 0 ){

			$stmt->fetch();
			$arrDados = array($ultimaDose, $dataHoraVacinacao);
			$stmt->free_result();
			return $arrDados;
			
		}
		
		$stmt->free_result();
		return false;
		
	}
	//--------------------------------------------------------------------------
	public function UsuariosComDosePorUnidade($unidade_id, $vacina_id)
	{
		
		$stmt = $this->conexao->prepare('SELECT usuario.id, usuario.nome, 
		usuario.mae, usuario.nascimento, MAX( usuariovacinado.numerodadose ) + 1
		FROM usuariovacinado, usuario
		WHERE usuariovacinado.Vacina_id = ?
		AND usuario.id = usuariovacinado.Usuario_id
		AND usuariovacinado.UnidadeDeSaude_id = ?
		GROUP BY usuario.id
		ORDER BY usuario.nome ASC, usuariovacinado.numerodadose DESC')
        or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_param('ii', $vacina_id, $unidade_id);
		$stmt->bind_result($usuarioId, $usuarioNome, $usuarioMae, 
						   $nascimento, $proximaDose);
		$stmt->execute();
		$stmt->store_result();
		
		if( $stmt->num_rows > 0 ){
			
			$arrNomes = array();
			
			while( $stmt->fetch() )
			{

				$arrNomes[] = array('id' => $usuarioId,
									'nome' => $usuarioNome,
									'mae' => $usuarioMae,
									'nascimento' => $nascimento,
									'dose' => $proximaDose);
				
			}
			
			return $arrNomes;
					
		}

		$stmt->free_result();
		return false;
	}
	//--------------------------------------------------------------------------
	public function CicloFechado($usuario_id, $vacina_id, $ciclo_id)
	{
		
		$stmt = $this->conexao->prepare('SELECT MAX(numerodadose) FROM `usuariovacinado`
						WHERE Usuario_id = ? AND Vacina_id = ?
						AND numerodociclo = ? LIMIT 1')
        or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_param('iii', $usuario_id, $vacina_id, $ciclo_id);
		$stmt->bind_result($ultimaDose);
		
		$stmt->execute();
		
		$stmt->store_result();
		
		if( $stmt->num_rows < 1 ){
			
			$stmt->free_result();
			return false;
		}
		
		$stmt->fetch();
		
		$stmt->free_result();
				
		$ultimoCiclo = $this->VerificarUltimoCiclo($usuario_id, $vacina_id);
		$aplicacoes = $this->AplicacoesPorPessoa($vacina_id);
		
		if( $ultimoCiclo > $ciclo_id )
			return true;
		elseif( $ultimoCiclo < $ciclo_id )
			return false;
		
		if( $ultimaDose == $aplicacoes ) return true;
		
		return false;
	}
	//----------------------------------------------------------------------
	public function AplicacoesPorPessoa($vacina_id)
	{
		
		$stmt = $this->conexao->prepare('SELECT aplicacoesporpessoa FROM vacina
						WHERE id = ? AND ativo')
        or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_param('i', $vacina_id);
		$stmt->bind_result($aplicacoesPorPessoa);
		
		$stmt->execute();
		
		$stmt->store_result();
		
		if( $stmt->num_rows > 0 ){
			
			
			$stmt->fetch();
			$stmt->free_result();
			
			return $aplicacoesPorPessoa;
		
		}
		
		
		$stmt->free_result();
		return false;
		
		
	}
	//--------------------------------------------------------------------------
	/**
	 * Função que verifica se o usuário tomou pelo menos uma dose na campanha.
	 *
	 * @param int $usuario
	 * @param int $vacina_id
	 * @param int $campanha_id
	 *
	 * @return boolean
     */
	
	public function VacinadoPelaCampanha($usuario_id, $vacina_id, $campanha_id)
	{
		
		if( $campanha_id == null || $campanha_id == '' || $campanha_id == 0 )
		return false;
		
		$stmt = $this->conexao->prepare('SELECT usuariovacinado.id FROM `usuariovacinado`
										WHERE usuariovacinado.Usuario_id = ? AND
										usuariovacinado.Vacina_id = ? AND
										usuariovacinado.Campanha_id = ?')
		or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));
		
		$stmt->bind_param('iii', $usuario_id, $vacina_id, $campanha_id);
		$stmt->bind_result($usuario_id);
		
		$stmt->execute();
		
		$stmt->store_result();
		
		if( $stmt->num_rows > 0 ) {
				
				$stmt->free_result();
				echo "<p style='color: red;'>
				Este indivíduo já foi vacinado pela campanha.
				</p>";
				return true;
				
		}
		
		$stmt->free_result();
		return false;
		
	}
	
}

