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

/*
 * @package Sivac/Class
 *
 * @author Maykon Monnerat (maykon_ttd@hotmail.com), v 1.0, 2008-07-20
 *
 * @copyright 2008 
 */

class Antibot
{
	//--------------------------------------------------------------------------
	// Gera uma "palavra" randomica
	private function PalavraRandomica($tamanho)
	{	
		$palavra = '';
		
		for($i=0; $i<$tamanho; $i++) {
			
			// Sorteio de parte do intervalo da tabela ASCII para alfanum�ricos
			$ch = rand(48, 122);
			
			// Remove qualquer s�mbolo (como ~[�) entre as letras e n�meros
			if($ch > 57 && $ch < 65 ||  $ch > 90 && $ch < 97 || 
			
				// Remove zero e letras "O" (mai�sculo e min�sculo)
				$ch == 79 || $ch == 111 || $ch == 48) $i--;
			
			else $palavra .= chr( $ch );
		}
		return $palavra;
	}
	//--------------------------------------------------------------------------
	public function GerarImagem()
	{
		
		// Gera o c�digo que ser� digitado
		$codigo = $this->PalavraRandomica( rand(4, 8) );
		
		// Armazena o c�digo na vari�vel de sess�o
		$_SESSION['codigoDaImagem'] = $codigo;
		
		// Largura e altura da imagem
		$tamanhoX = 200;
		$tamanhoY = 75;
		
		
		// Varia a dist�ncia entre as letras de acordo com a qtd (entre 4 e 8 letras)
		$espaco = $tamanhoX / (strlen($codigo) + 1);
		
		// Cria a tela de imagem
		$img = imagecreatetruecolor($tamanhoX, $tamanhoY);
		
		// Aloca as cores para o fundo e para a borda
		$fundo = imagecolorallocate($img, 255, 240, 240); // Clara para o fundo
		$borda = imagecolorallocate($img, 50, 50, 50);    // Cinza para borda
		
		// Aloca as cores para as letras e para os tra�os
		$cores[] = imagecolorallocate($img, 10, 10, 150); // Azul escuro
		$cores[] = imagecolorallocate($img, 150, 10, 10); // Vermelho escuro
		$cores[] = imagecolorallocate($img, 10, 150, 10); // Verde escuro
		
		// Preenche o fundo
		imagefilledrectangle($img, 1, 1, $tamanhoX - 2, $tamanhoY - 2, $fundo);
		imagerectangle($img, 0, 0, $tamanhoX - 1, $tamanhoY - 1, $borda);
		
		/// Desenha o texto
		for ($i = 0; $i < strlen($codigo); $i++)
		{
			// Alterna a cor usada para cada letra
			$cor = $cores[$i % count($cores)];
			
			// Para cada caracter por vez...
			imagettftext(
				$img,
				20 + rand(0, 20),     // Tamanho da fonte (varia entre 20 e 40)
				-20 + rand(0, 40),    // Rota��o (0 deixaria a letra em p�)
				($i + 0.3) * $espaco, // Dist�ncia da margem esquerda
				50 + rand(0, 10),     // Dist�ncia do topo (para o centro da letra)
				$cor,                 // Cor escolhida acima
				'fonte.ttf',          // Arquivo de fonte (usar tamanho de arq pequeno)
				$codigo[$i]           // Caracter exibido (poderia ser uma palavra)
			);
		}
		
		// Adiciona suavidade ao contorno da imagem
		imageantialias($img, true);
		
		// Adiciona algumas linhas da mesma cor do texto, provocando distor��es
		for ($i = 0; $i < 300; $i++)
		{
			// Margem m�nima de 5px (ponto inicial)
			$x1 = rand(5, $tamanhoX - 5);
			$y1 = rand(5, $tamanhoY - 5);
			
			// Tamanho vari�vel (ponto final)
			$x2 = $x1  + rand(-10, 10);
			$y2 = $y1  + rand(-10, 10);
			
			// Cria a linha (ponto inicial, ponto final e cor)
			imageline($img, $x1, $y1, $x2, $y2, $cores[$i % count($cores)]);
		}
		
		// Sa�da para o navegador
		header('Content-type: image/png');
		imagepng($img);
	}
	//--------------------------------------------------------------------------
}