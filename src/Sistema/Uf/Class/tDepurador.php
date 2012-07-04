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

            // Se não for um array, mostra o que é, usando var_dump:
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