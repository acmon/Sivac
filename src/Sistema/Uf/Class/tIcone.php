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

/**
 * Icone: Classe que contem todos os ícones do sistema.
 *
 * Esta classe exibe ícones em todo sistema quando chamados. Nela os ícones 
 * codificados em base 64 são decodificados e exibidos.  Com essa classe possibilitamos                                
 * que o sistema não tenha imagens só o código delas. As imagens só existem no 
 * sistema quando são solicitadas em um botão, tabela, etc.
 *
 * 
 * @package Sivac/Class
 *
 * @author Douglas, v 1.0, 2008-10-2 11:46
 *
 * @copyright 2008 
*/
class Icone
{
	//--------------------------------------------------------------------------
	public function Adicionar()
	{
		$imagem = 'R0lGODlhFAAUAJEDABFE7hGT7szMzP///yH5BAEAAAMALAAAAAAUABQAQAJA'
				. 'nI+pE+3vhFhD2kuPcACvy11WRn5SKCoCwHLsK6ln06XljeNRXM5BPVJAho1'
				. 'JgjjkaSwoG4JJExllTWXGmSsVAAA7';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Adicionar_desab()
	{
		$imagem = 'R0lGODlhFAAUAJEAAJubm7S0tMzMzP///yH5BAEAAAMALAAAAAAUABQAQAJA'
				. 'nI+pE+3vhFhD2kuPcACvy11WRn5SKCoCwHLsK6ln06XljeNRXM5BPVJAho1'
				. 'JgjjkaSwoG4JJExllTWXGmSsVAAA7';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Decrementar()
	{
		$imagem = 'R0lGODlhFAAUAJEAAIsAAP+RDszMzP///yH5BAEAAAMALAAAAAAUABQAAAI2'
				. 'nI+py+0/hJQwzisc3mz7JQTiOE6KRKaAiaDpuFKtBNS2zR7enuxf78vNghl'
				. 'QUMODYCrMpqIAADs=';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Decrementar_desab()
	{
		$imagem = 'R0lGODlhFAAUAJEAAIeHh+fn58zMzP///yH5BAEAAAMALAAAAAAUABQAAAI2'
				. 'nI+py+0/hJQwzisc3mz7JQTiOE6KRKaAiaDpuFKtBNS2zR7enuxf78vNghl'
				. 'QUMODYCrMpqIAADs=';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Desmarcar()
	{
		$imagem = 'R0lGODlhFAAUAJEDAKeXl////8zMzP///yH5BAEAAAMALAAAAAAUABQAQAJF'
				. 'nI+pIy0AggxxQuUabCtld2EgNFFbh6KfVkmhMZYtBHgZTXPpzvfIx1tpXIs'
				. 'PqWQ5gGQu12vUsryUUCdA0DHmsDugr1cAADs=';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Detalhes()
	{
		$imagem = 'R0lGODlhFAAUAKIAAAuf/wBLfLa2tgByvMzMzP///wAAAAAAACH5BAEAAAUA'
				. 'LAAAAAAUABQAQANTWLrcXmAQEqe9r9wdQ97YI4zCVBHC0GVsFo2ANQTEXHv'
				. 'LGQiVGjIg1IgyaBmPyMamFRlILJUIpJh7nkCOU8+5ZDQHpS8YQHOAnDviJ2'
				. 'iisrrJYwIAOw==';

		$this->ExibirGif($imagem);
	}
	
	//--------------------------------------------------------------------------
	public function Detalhes_desab()
	{
		$imagem = 'R0lGODlhFAAUAKIAAKenp3BwcLa2tpeXl8zMzP///wAAAAAAACH5BAEAAAUA'
				. 'LAAAAAAUABQAAANTWLrc/jDKWYi1tN5NIBjg9hFfUS6fIADXEBAuHDAjOXC'
				. 'XU9d4R982WGjTqAVWtwEr9wMJbKAb4NVQqSw8pcuBA71sj55oABFjyWXxZx'
				. 'bhZN5wRwIAOw==';

		$this->ExibirGif($imagem);
	}
	
	//--------------------------------------------------------------------------
	public function Editar()
	{
		$imagem = 'R0lGODlhFAAUAKIAAMzMzAAAADMzmWZm//+ZM////wAAAAAAACH5BAEAAAUA'
				. 'LAAAAAAUABQAAANSWLrc/jDKFSC4l4XtsL9b2HxYSHAMCQbEWY3f1rpdPAz'
				. 'slqUee+MBDKzUugWFvItApvPAAoJo89O4RaMqQPWHzVavgqyWAT6qHNHhzv'
				. 'GauN+FBAA7';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Excluir()
	{
		$imagem = 'R0lGODlhFAAUAKIFAPlxSIZ5eZ84GONCEczMzP///wAAAAAAACH5BAEAAAUA'
				. 'LAAAAAAUABQAQANKWLrcXiSSISSgIT4SsozC8CxfOX4XNQzm6LpUKVGOvGJ'
				. 'ffQteyBKvoHDYaL1kJlFxUkHFap/YhSdZWm6e6nIX8GkdAZzEJzQSgwkAOw'
				. '==';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Excluir_desab()
	{
		$imagem = 'R0lGODlhFAAUAKIAAOrq6l9eXnl1dbGtrczMzP///wAAAAAAACH5BAEAAAUA'
				. 'LAAAAAAUABQAAANQWLrc/jDKWYi1tN5NJP/F8AzCZ5HPWW4A2TWba7VCgDG'
				. 'cS9v38g3A2gbGAg54F+IsCOQ4TsGAoNnDGYWE6aDKCJCQWWCEEABbphRnZs'
				. '1uJAAAOw==';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function FundoLegenda()
	{
		$imagem = 'iVBORw0KGgoAAAANSUhEUgAAASwAAAABCAIAAADrWgR+AAAAGXRFWHRTb2Z0'
				. 'd2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAJlJREFUeNqMUgkOwDAIkv3'
				. '/z2ytttIrq8mWaADBDSTN7HtgWaw9WOZr+RDOOWCONBN8VSAXFA3LfMLAht'
				. 'V90gXZHP7qxFsjR8IyhBimGxsy3GZvkhtqGuiFdmHF42+vAkZw6VpUSJZ08'
				. 'Mz4PM11KSu2jG0iL8WVWCk9CHBG7p0FPW7rOvLbJEwml+ndDG2m+BbIR8nQ'
				. 'Ev8VYADRsU//V4nmIQAAAABJRU5ErkJggg==';

		$this->ExibirPng($imagem);
	}
	//--------------------------------------------------------------------------
	public function Listar()
	{
		$imagem = 'R0lGODlhFAAUAJEDABGT7hFE7szMzP///yH5BAEAAAMALAAAAAAUABQAQAJH'
				. 'nI+pIyAB4xNULFazvQDrsCnNE0FTdaWpU5ogJbLtCSeORr2hyvc8AAwGA6o'
				. 'b7sXB5VCJkVK3Yxg/GcU0o1u0ZtGFxgceFAAAOw==';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Listar_desab()
	{
		$imagem = 'R0lGODlhFAAUAJEAALGxsZ2dnczMzP///yH5BAEAAAMALAAAAAAUABQAAAJF'
				. 'nI+py+0MopTBgSGyFkE0sG2dt4DBiXJaGWojiQgmeqprDLZ2lsgc3XnBDD6'
				. 'dsDej7YaGXOvFMt4SQCCvobs+Qo+ut1sAADs=';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Ok()
	{
		$imagem = 'R0lGODlhFAAUAJEDABlODzOeHszMzP///yH5BAEAAAMALAAAAAAUABQAQAI+'
				. 'nI+pIxLfogtALCblRTne/ABVtpULEHRkIj0upbHR2Jn2jWOeqcbK1HOIOA1'
				. 'XJDSSFV+0xu/Y3D2DtlXOVAAAOw==';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Ok_vermelho()
	{
		$imagem = 'R0lGODlhFAAUAJEAAE0aHf8AAMzMzP///yH5BAEAAAMALAAAAAAUABQAQAI+'
				. 'nI+pIxLfogtALCblRTne/ABVtpULEHRkIj0upbHR2Jn2jWOeqcbK1HOIOA1'
				. 'XJDSSFV+0xu/Y3D2DtlXOVAAAOw==';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Pesquisar()
	{
		$imagem = 'R0lGODlhFAAUAKIAAB56uLX5/8zMzP///xcwQYe63AAAAAAAACH5BAEAAAMA'
				. 'LAAAAAAUABQAAANYOLrc/jDKWKoVYlYGAM7P1okeFg5AEAxBMRAfyLipOrj'
				. 'w57ju2r4xmYLnuwFjjQLgZkHldAxBh0MglExRabUqsFqh2WAX8H2IP96l+T'
				. 'xWr8+EySAor9sjCQA7';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Vacinar()
	{
		$imagem = 'R0lGODlhFAAUAKIAAB56uP//88zMzP///xcwQYe63AAAAAAAACH5BAEAAAMA'
				. 'LAAAAAAUABQAAANSOLrc/jDKOYS1A8xbriga5AGABRQF8XgWeBKYwwolecl'
				. 'sScM3k5sEUG8xCuwswRjRZEzNBA3mhzeTAQLO54oAoFYdXq0j9FQyQstho0'
				. 'B5BNqOBAA7';

		$this->ExibirGif($imagem);
	}
	//--------------------------------------------------------------------------
	public function Vacinar_desab()
	{
		$imagem = 'R0lGODlhFAAUAKIAALSzs+7u7szMzP///6Khobi4uAAAAAAAACH5BAEAAAMA'
				. 'LAAAAAAUABQAAANSOLrc/jDKOYS1A8xbriga5AGABRQF8XgWeBKYwwolecl'
				. 'sScM3k5sEUG8xCuwswRjRZEzNBA3mhzeTAQLO54oAoFYdXq0j9FQyQstho0'
				. 'B5BNqOBAA7';

		$this->ExibirGif($imagem);
	}
    //--------------------------------------------------------------------------
    /**
     * Converte a $imagem que é um binário para base64 e exibe um GIF
     *
     * @param String $imagem
     */
    private function ExibirGif($imagem)
    {
        header("Content-type: image/gif");
        echo base64_decode($imagem); 
    }
    //--------------------------------------------------------------------------
    /**
     * Converte a $imagem que é um binário para base64 e exibe um PNG
     *
     * @param String $imagem
     */
    private function ExibirPng($imagem)
    {
        header("Content-type: image/png");
        echo base64_decode($imagem); 
    }
    //--------------------------------------------------------------------------
}