<?php
/**
 * Criptografia. Classe com diversos métodos para criptografar textos e arquivos
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
	 * a criptografia dos dados. O mais seguro que existe hoje é o sha512, que é
	 * o padrão. Para mudar o algoritmo para o md5 p.ex. é só especificá-lo no
	 * primeiro parâmetro do construtor. O modo binário é um meio de transformar
	 * a string em caracteres binários não legíveis. Pode ser útil para cifrar o
	 * conteúdo de um arquivo em um outro arquivo.
	 *
	 * @param String $algoritmo Algoritmo de hashing preferencial
	 * @param boolean $modoBinario Modo binário não legível
	 */
	public function __construct($algoritmo = 'sha512', $modoBinario = false)
	{
		if( !in_array( $algoritmo, hash_algos() ) ) {

			die("Algoritmo '$algoritmo' não disponível neste sistema.");			
		}
		
		if( $modoBinario == true ) {
			
			if(!function_exists('gzdeflate') || !function_exists('gzinflate')) {
				
				die('Modo binário não disponível neste sistema');
			}
		}
		
		$this->_algoritmo = $algoritmo;
		$this->_modoBinario = $modoBinario;
		
	}
	//--------------------------------------------------------------------------
	/**
	 * Cifrar. Método que gera um texto cifrado de linha única, representado em
	 * caracteres "hexadecimais" disfarçados. Se o modo binário estiver ativo,
	 * o texto não será mais disposto em caracteres hexadecimais legíveis. A
	 * chave secreta é opcional, mas é mais seguro usá-la, pois só poderá
	 * decifrar o conteúdo do texto cifrado quem conhecer e informar a chave
	 * para o método que decifra.
	 *
	 * @param String $texto Texto limpo (não cifrado)
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
	 * Decifrar. Método responsável por decifrar o conteúdo do texto cifrado
	 * pelo método Cifrar() desta classe. O texto só poderá ser decifrado com o
	 * conhecimento da chave, ou se a chave não foi usada na operação de cifra.
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
	 * Senha. Método usado para criptografar uma senha que contenha no mínimo um
	 * caracter e no máximo dez caracteres. A senha criptografada, seja qual for
	 * o seu tamanho (compreendido entre um e dez caracteres) irá ser gerada com
	 * trinta e dois caracteres em formato "hexadecimal" disfarçado. O mesmo
	 * método servirá para criptografar ou descriptografar a senha. Uma senha
	 * criptografada sempre conterá trinta e dois caracteres, nos quais os dois
	 * últimos representam o número de caracteres reais da senha informada. Para
	 * decriptar a senha e exibí-la novamente conforme foi digitada, basta
	 * aplicar este mesmo método na senha criptografada.
	 *
	 * @param String $senha Senha limpa ou criptografada
	 * @return String Senha limpa ou criptografada
	 */
	public function Senha($senha)
	{
		$tamanhoDaSenha = sprintf('%02d', strlen($senha) );
		
		// A senha não pode ter mais de 10 caracteres, infelizmente
		if(((int)$tamanhoDaSenha > 10 && (int)$tamanhoDaSenha != 32) ||
			(int)$tamanhoDaSenha == 0) {
			
			return false;
		}
		
		// Se a senha é a senha crua, cifrar a mesma:
		if( (int)$tamanhoDaSenha <= 10 ) {
			
			$ch = strrev( hash($this->_algoritmo, $tamanhoDaSenha) );
			
			$senhaCifrada = '';
			
			for($i=0; $i<(int)$tamanhoDaSenha; $i++) {
				
				$senhaCifrada .= sprintf('%03d', (ord($senha[$i]) + ord($ch[$i])));
				
			}
	
			$senhaCifrada = str_replace($this->_nums, $this->_letr, $senhaCifrada);
			
			// Completando com valores de onde termina a senha até 30 caracteres

			$completude = hash($this->_algoritmo, $senha);
			
			for($i=(strlen($senhaCifrada) - 1); $i<29; $i++) {
				
				$senhaCifrada .= $completude[$i];
				
			}
			
			return $senhaCifrada . $tamanhoDaSenha;
		}
		
		// Se a senha é a senha cifrada, decifrar a mesma:
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
	 * GeraSenha. Método que gera uma senha aleatória com no máximo dez
	 * caracteres.
	 *
	 * @param int $tamanhoMinimo Tamanho mínimo de caracteres para a senha
	 * @return String Senha gerada
	 */
	public function GerarSenha($tamanhoMinimo = 3)
	{
		return substr( str_shuffle( uniqid() ), 0, mt_rand($tamanhoMinimo, 10));
	}
	//--------------------------------------------------------------------------
	/**
	 * AlgoritmosDisponiveis. Exibe uma lista completa de algoritmos disponíveis
	 * no sistema para o uso nesta classe. Até o presente momento é recomendado
	 * o uso do sha512, que é o mais seguro.
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