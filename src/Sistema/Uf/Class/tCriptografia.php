<?php
/**
 * Criptografia. Classe com diversos m�todos para criptografar textos e arquivos
 *
 */
class Criptografia
{
	private $_algoritmo;
	private $_modoBinario;
	private $_nums = array('0', '1', '2', '3', '4', '5');
	private $_letr = array('a', 'b', 'c', 'd', 'e', 'f');

	//--------------------------------------------------------------------------
	/**
	 * Construtor. Pode-se definir o algoritmo de hashing usado para a
	 * a criptografia dos dados. O mais seguro que existe hoje � o sha512, que �
	 * o padr�o. Para mudar o algoritmo para o md5 p.ex. � s� especific�-lo no
	 * primeiro par�metro do construtor. O modo bin�rio � um meio de transformar
	 * a string em caracteres bin�rios n�o leg�veis. Pode ser �til para cifrar o
	 * conte�do de um arquivo em um outro arquivo.
	 *
	 * @param String $algoritmo Algoritmo de hashing preferencial
	 * @param boolean $modoBinario Modo bin�rio n�o leg�vel
	 */
	public function __construct($algoritmo = 'sha512', $modoBinario = false)
	{
		if( !in_array( $algoritmo, hash_algos() ) ) {

			die("Algoritmo '$algoritmo' n�o dispon�vel neste sistema.");			
		}
		
		if( $modoBinario == true ) {
			
			if(!function_exists('gzdeflate') || !function_exists('gzinflate')) {
				
				die('Modo bin�rio n�o dispon�vel neste sistema');
			}
		}
		
		$this->_algoritmo = $algoritmo;
		$this->_modoBinario = $modoBinario;
		
	}
	//--------------------------------------------------------------------------
	/**
	 * Cifrar. M�todo que gera um texto cifrado de linha �nica, representado em
	 * caracteres "hexadecimais" disfar�ados. Se o modo bin�rio estiver ativo,
	 * o texto n�o ser� mais disposto em caracteres hexadecimais leg�veis. A
	 * chave secreta � opcional, mas � mais seguro us�-la, pois s� poder�
	 * decifrar o conte�do do texto cifrado quem conhecer e informar a chave
	 * para o m�todo que decifra.
	 *
	 * @param String $texto Texto limpo (n�o cifrado)
	 * @param String $chave Chave para cifrar o texto (opcional)
	 * @return String Texto cifrado
	 */
	public function Cifrar($texto, $chave = 'chave')
	{
		if( $this->_modoBinario ) {
            $ch = strrev( hash($this->_algoritmo, $chave) );
        }
		else {
            $ch = strrev( hash($this->_algoritmo, $chave . strlen($texto)) );
        }
		
		$tamTexto = strlen($texto);
		$tamChave = strlen($ch);
		
		$textoCifrado = '';
		
		for($i=0, $j=0; $i<$tamTexto; $i++) {
			
			if( $j < ($tamChave - 1)) $j++;
			else $j = 0;
			
			$textoCifrado .= sprintf('%03d', (ord($texto[$i]) + ord($ch[$j])));
			
		}

		if($this->_modoBinario) {
			
			return gzdeflate(str_replace($this->_nums, $this->_letr,
				$textoCifrado) );
		}
		
		return str_replace($this->_nums, $this->_letr, $textoCifrado);
	}
	//--------------------------------------------------------------------------
	/**
	 * Decifrar. M�todo respons�vel por decifrar o conte�do do texto cifrado
	 * pelo m�todo Cifrar() desta classe. O texto s� poder� ser decifrado com o
	 * conhecimento da chave, ou se a chave n�o foi usada na opera��o de cifra.
	 *
	 * @param String $textoCifrado Texto cifrado
	 * @param String $chave Chave para decifrar o texto (opcional)
	 * @return String Texto decifrado
	 */
	public function Decifrar($textoCifrado, $chave = 'chave')
	{
		if($this->_modoBinario) {
            
            $ch = strrev( hash($this->_algoritmo, $chave) );
			$textoCifrado = gzinflate($textoCifrado);
		}
        else {
        
            $ch = strrev( hash($this->_algoritmo, 
                          $chave . ( strlen($textoCifrado)/3 ) ) );
        }

		
		$texto = str_replace($this->_letr, $this->_nums, $textoCifrado);
		
		
		$tamTexto = strlen($texto);
		$tamChave = strlen($ch);
		
		$textoDecifrado = '';
		
		$arrayDeCaracteres = str_split($texto, 3);
		
		for($i=0, $j=0; $i < (($tamTexto / 3)); $i++) {
			
			if( $j < ($tamChave - 1) ) $j++;
			else $j = 0;
			
			$textoDecifrado .= chr($arrayDeCaracteres[$i] - ord($ch[$j]) );
		}
		
		return $textoDecifrado;	
	}
	//--------------------------------------------------------------------------
	/**
	 * Senha. M�todo usado para criptografar uma senha que contenha no m�nimo um
	 * caracter e no m�ximo dez caracteres. A senha criptografada, seja qual for
	 * o seu tamanho (compreendido entre um e dez caracteres) ir� ser gerada com
	 * trinta e dois caracteres em formato "hexadecimal" disfar�ado. O mesmo
	 * m�todo servir� para criptografar ou descriptografar a senha. Uma senha
	 * criptografada sempre conter� trinta e dois caracteres, nos quais os dois
	 * �ltimos representam o n�mero de caracteres reais da senha informada. Para
	 * decriptar a senha e exib�-la novamente conforme foi digitada, basta
	 * aplicar este mesmo m�todo na senha criptografada.
	 *
	 * @param String $senha Senha limpa ou criptografada
	 * @return String Senha limpa ou criptografada
	 */
	public function Senha($senha)
	{
		$tamanhoDaSenha = sprintf('%02d', strlen($senha) );
		
		// A senha n�o pode ter mais de 10 caracteres, infelizmente
		if(((int)$tamanhoDaSenha > 10 && (int)$tamanhoDaSenha != 32) ||
			(int)$tamanhoDaSenha == 0) {
			
			return false;
		}
		
		// Se a senha � a senha crua, cifrar a mesma:
		if( (int)$tamanhoDaSenha <= 10 ) {
			
			$ch = strrev( hash($this->_algoritmo, $tamanhoDaSenha) );
			
			$senhaCifrada = '';
			
			for($i=0; $i<(int)$tamanhoDaSenha; $i++) {
				
				$senhaCifrada .= sprintf('%03d', (ord($senha[$i]) + ord($ch[$i])));
				
			}
	
			$senhaCifrada = str_replace($this->_nums, $this->_letr, $senhaCifrada);
			
			// Completando com valores de onde termina a senha at� 30 caracteres

			$completude = hash($this->_algoritmo, $senha);
			
			for($i=(strlen($senhaCifrada) - 1); $i<29; $i++) {
				
				$senhaCifrada .= $completude[$i];
				
			}
			
			return $senhaCifrada . $tamanhoDaSenha;
		}
		
		// Se a senha � a senha cifrada, decifrar a mesma:
		else {
			
			$tamanhoDaSenha = substr($senha, 30);
			
			$ch = strrev( hash($this->_algoritmo, $tamanhoDaSenha) );
			
			$senhaSemLixo = substr($senha, 0, ( (int)$tamanhoDaSenha * 3) );
			
			$senhaNums = str_replace($this->_letr, $this->_nums, $senhaSemLixo);
			
			$senhaDecifrada = '';
			
			$arrayDeCaracteres = str_split($senhaNums, 3);
			
			for($i=0; $i<(int)$tamanhoDaSenha; $i++) {
				
				$senhaDecifrada .= chr($arrayDeCaracteres[$i] - ord($ch[$i]) );
			}
			
			return $senhaDecifrada;
		}
	}
	//--------------------------------------------------------------------------
	/**
	 * GeraSenha. M�todo que gera uma senha aleat�ria com no m�ximo dez
	 * caracteres.
	 *
	 * @param int $tamanhoMinimo Tamanho m�nimo de caracteres para a senha
	 * @return String Senha gerada
	 */
	public function GerarSenha($tamanhoMinimo = 3)
	{
		return substr( str_shuffle( uniqid() ), 0, mt_rand($tamanhoMinimo, 10));
	}
	//--------------------------------------------------------------------------
	/**
	 * AlgoritmosDisponiveis. Exibe uma lista completa de algoritmos dispon�veis
	 * no sistema para o uso nesta classe. At� o presente momento � recomendado
	 * o uso do sha512, que � o mais seguro.
	 */
	public function AlgoritmosDisponiveis()
	{
		$algoritmos = hash_algos();
		
		if( $total = count($algoritmos) ) {
			
			echo "<fieldset><legend>Algoritmos - total: $total</legend>";
			foreach ($algoritmos as $algoritmo) {
				
				$tam = strlen( hash($algoritmo, 'a') );
				echo "<li>$algoritmo - Tamanho da string: $tam</li>";
			}
			echo '</fieldset>';
		}
	}
	//--------------------------------------------------------------------------
}