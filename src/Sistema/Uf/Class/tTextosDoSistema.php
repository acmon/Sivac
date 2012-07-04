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

class TextosDoSistema {
	
	const TITULO_INICIAL = 'Digite o t�tulo';
	const TEXTO_INICIAL = 'Digite o texto de apresenta��o';
	
	protected $composicao;   	 //Objeto Campanha      
	protected $tituloInicial;    //String    
	protected $textoInicial;     //String    
	
	public function __construct()
	{
		$this->composicao = new Campanha();
	}
	
	public function __destruct()
	{
		unset($this->composicao);
	}
	//--------------------------------------------------------------------------
	public function SetarDados($post) {
		
		$this->composicao->UsarBaseDeDados();
		$clean = Preparacao::GerarArrayLimpo($post, $this->composicao->Conexao());
		
		$this->SetarTituloInicial($clean['tituloInicio']);
		$this->SetarTextoInicial(str_replace(array('\\'),array(''),$clean['textoInicio']));
		
	}
	//--------------------------------------------------------------------------
	public function SetarTituloInicial($titulo) 
	{
		$this->tituloInicial = $titulo;
	}
	//--------------------------------------------------------------------------
	public function SetarTextoInicial($texto) 
	{		
		$this->textoInicial = $texto;
	}
	//--------------------------------------------------------------------------
	public function TituloInicial() 
	{
		return $this->tituloInicial;
	}
	//--------------------------------------------------------------------------
	public function TextoInicial() 
	{
		return $this->textoInicial;
	}
	//--------------------------------------------------------------------------
	public function ExibirFormularioTextoInicio()
	{
		list($titulo, $texto) = $this->SelecionarTextoInicio();
		
		if (!isset($titulo, $texto)) return false;
		
		?>
		<h3 align="center">Inicio</h3>

		<form id="textoInicio" name="textoInicio" method="post" action="<?php echo $_SERVER['REQUEST_URI']?> ">
			Titulo:
			<br />
			<input type="text" name="tituloInicio" value="<?php echo $titulo; ?>" size="90" maxlength="100"/>
			<br />
			Texto:<br />
			<textarea  name="textoInicio" rows="5" style="width:550px;"><?php 
			echo $texto?></textarea><br />
		
			<?php
			$this->composicao->ExibirBotoesDoFormulario('Confirmar', 'Limpar');
			
			?>
		
		</form>
		<?php
		
		
		return true;
	}
	
	//--------------------------------------------------------------------------
	private function SelecionarTextoInicio()
	{
		$this->composicao->UsarBaseDeDados();
		
		$stmt = $this->composicao->Conexao()->prepare('SELECT titulo, texto FROM
			`textoinicio`') or die(Bd::TratarErroSql($this->composicao->Conexao()->error, __FILE__, __LINE__));
		
		$titulo = $this->TituloInicial(); 
		$texto  = $this->TextoInicial();

		$stmt->bind_result($titulo, $texto);
		
		$stmt->execute();
		
		$stmt->store_result();
		
		$existe = $stmt->num_rows;
		
		if ($existe > 0) {
			
			$stmt->fetch();
			
			$stmt->free_result();
			
			return array($titulo, $texto);
			
		}
		
		if ($existe == 0)
		{
			$this->composicao->ExibirMensagem('N�o existe texto inicial.');
			
			return array(self::TITULO_INICIAL, self::TEXTO_INICIAL);
			
		}
		
		if ($existe < 0) {
			
			$this->composicao->AdicionarMensagemDeErro('Ocorreu um erro ao
				selecionar os dados do texto inicial');
		
			return false;
		}
		
	}
	//--------------------------------------------------------------------------
	public function ExibirMensagemDeErro()
	{
		$this->composicao->ExibirMensagensDeErro();	
	}
	//--------------------------------------------------------------------------
	public function InserirTextoInicial()
	{
		$titulo = $this->TituloInicial(); 
		$texto  = $this->TextoInicial();
		
		$titulo = str_replace(array('\\r\\n','\\r','\\n'),'<br />', $titulo);
		$texto = str_replace(array('\\r\\n','\\r','\\n'),'<br />', $texto);
		
		$this->composicao->UsarBaseDeDados();
		
		$this->composicao->Conexao()->query('TRUNCATE TABLE `textoinicio`')
			or die(Bd::TratarErroSql($this->composicao->Conexao()->error, __FILE__, __LINE__));

		if ($this->composicao->Conexao()->errno == 0) {

			$stmt = $this->composicao->Conexao()->prepare('INSERT INTO
				`textoinicio` VALUES (NULL, ?, ?)')
				or die(Bd::TratarErroSql($this->composicao->Conexao()->error, __FILE__, __LINE__));
			
			$stmt->bind_param('ss', $titulo, $texto);
			
			$stmt->execute();
			
			$inseriu = $stmt->affected_rows;
			
			$stmt->close();
			
			if ($inseriu > 0) return true;
			
			if ($inseriu <= 0 ) {
				
				$this->composicao->AdicionarMensagemDeErro('Erro ao incluir o
					texto de apresenta��o do sistema');
			}
		}
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarTitulo($titulo)
	{
		if(strpos($titulo, self::TITULO_INICIAL) !== false) return false;
		
		if(strlen($titulo) > 5 && strlen($titulo) < 50) {
			
			return true;
		}
		$this->composicao->AdicionarMensagemDeErro('O t�tulo inv�lido ou grande de mais.');
		return false;

	}
	//--------------------------------------------------------------------------
	public function ValidarTexto($texto)
	{
		if(strpos($texto, self::TEXTO_INICIAL) !== false) return false;
		
		if(strlen($texto) > 50 && substr_count($texto, ' ') > 5) {
			
			return true;
		}
		$this->composicao->AdicionarMensagemDeErro('A texto inv�lido ou pequeno de mais.');
		return false;
	}
	//--------------------------------------------------------------------------
	public function ValidarFormulario($nomeDoFormulario)
	{
		switch($nomeDoFormulario) {

			case 'textoInicio':
				
				$tituloValido = $this->ValidarTitulo($_POST['tituloInicio']);
				$textoValido = $this->ValidarTexto($_POST['textoInicio']);
				
				if($tituloValido && $textoValido) return true;
				return false;
				
				
			default:
				return false;
				
		}
	}
	//--------------------------------------------------------------------------
}
