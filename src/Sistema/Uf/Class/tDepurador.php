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
 *  
 * @package Sivac/Class
 *
 * @author Maykon Monnerat (maykon_ttd@hotmail.com)
 *
 * @copyright 2008 
 */

class Depurador 
{
	//--------------------------------------------------------------------------------
	private static function Abertura($metodo)
	{
		echo '<hr />';
		echo '<li>';
		echo '<span style="color: red; font: bold 14px Arial, Helvetica; background-color: #DDD">Debug: ';
		echo $metodo;
		echo '</li>';
		echo '</span>';
		echo '<br /><pre>';
		echo '<span style="color: olive">';
	}
    //--------------------------------------------------------------------------
    private static function TestarServidorLocal()
    {
        if( $_SERVER['HTTP_HOST'] != /*'pimba'*/ Constantes::SERVIDOR_LOCAL ) return false;

        return true;
    }
	//--------------------------------------------------------------------------------
	private static function Fechamento()
	{
		echo '</span>';
		echo '</pre>';
		echo '<hr />';	
	}
	//--------------------------------------------------------------------------------
	public static function Print_r($array)
	{
        if( self::TestarServidorLocal() )
        {
            self::Abertura(__METHOD__);

            // Se n�o for um array, mostra o que �, usando var_dump:
            if( !is_array($array) )
            {
                var_dump($array);
            }
            else
            {
                print_r($array);
            }
            self::Fechamento();
        }
	}
	//--------------------------------------------------------------------------------
	public static function Pre($texto)
	{
        if( self::TestarServidorLocal() ) {

            self::Abertura(__METHOD__);
            echo '<pre>', $texto, '</pre>';
            self::Fechamento();
        }
	}
	//--------------------------------------------------------------------------------
	public static function Querystring($string)
	{
        if( self::TestarServidorLocal() ) {

            self::Abertura(__METHOD__);
            echo "<code>$string</code>";
            self::Fechamento();
        }
	}
	//--------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------
}