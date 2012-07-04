<?php


/*
 *  Sivac - Sistema Online de Vacinaзгo     
 *  Copyright (C) 2012  IPPES  - Institituto de Pesquisa, Planejamento e Promoзгo da Educaзгo e Saъde   
 *  www.sivac.com.br                     
 *  ippesaude@uol.com.br                   
 *                                                                    
 *  Este programa e software livre; vocк pode redistribui-lo e/ou     
 *  modificб-lo sob os termos da Licenзa Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a versгo 2 da      
 *  Licenзa como (a seu critйrio) qualquer versгo mais nova.          
 *                                                                    
 *  Este programa й distribuнdo na expectativa de ser ъtil, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia implнcita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licenзa Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Vocк deve ter recebido uma copia da Licenзa Publica Geral GNU     
 *  junto com este programa; se nгo, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licenзa no diretуrio Sistema/licenca_en.txt 
 *                                Sistema/licenca_pt.txt 
 */


class Email
{
	
	private $_destinatario;
	private $_assunto;
	private $_mensagem;
	private $_cabecalho;
	private $_remetente;
	private $_replicados;
	//------------------------------------------------------------------------------
	
	public function NovoEmail()
	{
		$this->_destinatario = '';
		$this->_assunto = '';
		$this->_mensagem = '';
		$this->_cabecalho = '';
		$this->_remetente = '';
		$this->_replicados = '';
	}
	//------------------------------------------------------------------------------
	
	public function SetarDestinatario($email)
	{
		//if ( $this->ValidarEmail($email) )
		$this->_destinatario = $email;
	}
	//------------------------------------------------------------------------------
	
	public function SetarAssunto($assunto)
	{
		$this->_assunto = $assunto;
	}
	//------------------------------------------------------------------------------
	
	public function SetarMensagem($mensagem)
	{
		$this->_mensagem = $mensagem;
	}
	//------------------------------------------------------------------------------
	
	public function SetarCabecalho()
	{
		$formatado = "From: $this->_remetente" . PHP_EOL;
		$formatado .= "Reply-To: $this->_replicados" . PHP_EOL;
		$formatado .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
		$formatado .= 'Content-type: text/html; charset = iso-8859-1' . PHP_EOL;
		$this->_cabecalho = $formatado;
	}
	//------------------------------------------------------------------------------
	
	public function SetarRemetente($remetente)
	{
		//if ( $this->ValidarEmail($remetente) )
		$this->_remetente = $remetente;
	}
	//------------------------------------------------------------------------------
	
	public function SetarReplicacao($email)
	{
		//if ( $this->ValidarEmail($email) )
		$this->_replicados = $email;
	}
	//------------------------------------------------------------------------------
	
	public function Enviar()
	{
		//die($this->_cabecalho);
		$enviado = mail($this->_destinatario, $this->_assunto, 
		$this->_mensagem, $this->_cabecalho);
		
		return $enviado;
	}
	//------------------------------------------------------------------------------
	
	public function FormatoDeEmailValido($email)
	{
		$er1 = '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])'
			 . '+([a-zA-Z0-9\._-]+)+$/';
	
		$er2 = '/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*'
		     . '[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|'
		     . 'arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|'
		     . 'bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|'
		     . 'co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|'
		     . 'ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|'
		     . 'gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|'
		     . 'id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|'
		     . 'km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|'
		     . 'mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|'
		     . 'mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|'
		     . 'om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|'
		     . 'ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|'
		     . 'sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|'
		     . 'us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)|(([0-9]'
		     . '[0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9]'
		     . '[0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i';
	
		if ( preg_match($er1, $email) &&  preg_match($er2, $email)) return true;
	
		return false;
	}
	//------------------------------------------------------------------------------
	
	public function DominioDeEmailExiste($email)
	{
		$dominio = explode('@', $email);
	
		if( checkdnsrr($dominio[1], 'MX')) return 1;
		if( checkdnsrr($dominio[1], 'A')) return 2;
		if( checkdnsrr($dominio[1], 'CNAME') ) return 3;
		if( gethostbyname($dominio[1]) != $dominio[1] ) return 4;
	
		return false;
	}
	//------------------------------------------------------------------------------
	public function  ValidarEmail($email)
	{
		if( strlen($email) > 5 ) {
				
			if( $this->FormatoDeEmailValido($email)
				&& $this->DominioDeEmailExiste($email) // Descomentar ao usar online! //????
			) {
				return true;
			}
		}
		
		return false;
	}
	

}
?>