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


class Navegacao
{
	//--------------------------------------------------------------------------
	public static function ListarPessoasVacinaveis()
	{
		if( isset($_SESSION['listarPessoasVacinaveis']) ) {
			
			return $_SESSION['listarPessoasVacinaveis'];
		}
		
		return false;
	}
	//--------------------------------------------------------------------------
	public static function GravarDadosListarPessoasVacinaveis($unidade_id,
		$cidade_id, $campanha_id, $vacina_id, $vacinaFilha_id, $pesquisa, $mae = 'vazio', $cpf = 'vazio',
		$nasc = 'vazio', $emAtraso)
	{
		$_SESSION['listarPessoasVacinaveis']['unidadeDeSaude_id'] = $unidade_id;
		$_SESSION['listarPessoasVacinaveis']['cidade_id']         = $cidade_id;
		$_SESSION['listarPessoasVacinaveis']['campanha']		  = $campanha_id;
		$_SESSION['listarPessoasVacinaveis']['vacina']            = $vacina_id;
		$_SESSION['listarPessoasVacinaveis']['VacinaFilha_id']    = $vacinaFilha_id;
		$_SESSION['listarPessoasVacinaveis']['pesquisa']          = $pesquisa;
		$_SESSION['listarPessoasVacinaveis']['mae']               = $mae;
		$_SESSION['listarPessoasVacinaveis']['cpf']       		  = $cpf;
		$_SESSION['listarPessoasVacinaveis']['datadenasc']        = $nasc;
		$_SESSION['listarPessoasVacinaveis']['emAtraso']          = $emAtraso;

		return $_SESSION['listarPessoasVacinaveis'];
	}
	//--------------------------------------------------------------------------
	public static function ListarPessoa()
	{
		if( isset($_SESSION['listarPessoa']) ) {
			
			return $_SESSION['listarPessoa'];
		}
		
		return false;
	}
	//--------------------------------------------------------------------------
	public static function AdicionarIntercorrencia()
	{
		if( isset($_SESSION['AdicionarIntercorrencia']) ) {
			
			return $_SESSION['AdicionarIntercorrencia'];
		}
		
		return false;
	}
	//--------------------------------------------------------------------------
	public static function GravarDadosListarPessoa( $cidade_id, $pesquisa,
		$mae = 'vazio', $cpf = 'vazio', $ultimaAba = 'aba1', $nasc = 'vazio')
	{
		
		$_SESSION['listarPessoa']['cidade_id']      = $cidade_id;
		$_SESSION['listarPessoa']['pesquisa']       = $pesquisa;
		$_SESSION['listarPessoa']['mae']			= $mae;
		$_SESSION['listarPessoa']['cpf']       		= $cpf;
		$_SESSION['listarPessoa']['ultimaAba']      = $ultimaAba;
		$_SESSION['listarPessoa']['datadenasc']		= $nasc;

        // Dentro do mйtodo listar jб grava esse array:
        //$_SESSION['listarPessoa']['arr']		    = dentro do mйtodo listar;
		
		return $_SESSION['listarPessoa'];
	}
	//--------------------------------------------------------------------------
	public static function GravarDadosAdicionarIntercorrencia( $cidade_id, $pesquisa,
		$mae = 'vazio', $cpf = 'vazio', $nasc = 'vazio', $intercorrencia_id = 'vazio', 
        $vacina_id = 'vazio', $campanha_id = 'vazio')
	{
		
		$_SESSION['AdicionarIntercorrencia']['cidade_id']       = $cidade_id;
		$_SESSION['AdicionarIntercorrencia']['pesquisa']        = $pesquisa;
		$_SESSION['AdicionarIntercorrencia']['mae']		= $mae;
		$_SESSION['AdicionarIntercorrencia']['cpf']       	= $cpf;
		$_SESSION['AdicionarIntercorrencia']['datadenasc']	= $nasc;
		$_SESSION['AdicionarIntercorrencia']['intercorrencia']	= $intercorrencia_id;
		$_SESSION['AdicionarIntercorrencia']['vacina_id']	= $vacina_id;
		$_SESSION['AdicionarIntercorrencia']['campanha_id']	= $campanha_id;

		return $_SESSION['AdicionarIntercorrencia'];
	}
}
?>