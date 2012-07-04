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


?>
<ul>
<li><a href="<?php echo '?', $crip->Cifrar('pagina=inicio'); ?>">In�cio</a></li>
<li><a href="<?php echo '?', $crip->Cifrar('pagina=login'); ?>">Login</a></li>
<?php
if ( Sessao::Permissao('UNIDADES_') ||
     Sessao::Permissao('ACS_') ||
     Sessao::Permissao('CAMPANHAS_') ||
     Sessao::Permissao('CONFIGURAR_') ||
     Sessao::Permissao('USUARIO_ACESSOS')) {
?>
<li class="comtic"><a href="#">Administra��o</a>
		
	<ul>
	<div align="center"><span style="font-size:x-small;">
		<strong>Administra��o</strong></span></div>
		<?php
		$crip = new Criptografia();
		if ( Sessao::Permissao('UNIDADES_') ) {
		?>
	<li><a href="#">Unidades</a>
		<ul class="semtic">
			<div align="center"><span style="font-size:x-small;">
			<strong>Unidade de Sa�de</strong></span></div>
				<?php
				if( Sessao::Permissao('UNIDADES_INSERIR') ) {
				?>
				<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/inserirUnidadeDeSaude'); ?>" >Inserir</a></li>
				<?
				}
				if(Sessao::Permissao('UNIDADES_LISTAR')) {
				?>
				<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/listarUnidades'); ?>" >Listar</a></li>
				<?php
				}
				if( Sessao::Permissao('UNIDADES_ALTERAR') == 2 )
				{
				?>
				<li><a href="?<?php echo $crip->Cifrar("pagina=Adm/editarUnidadeDeSaude&id={$_SESSION['unidadeDeSaude_id']}"); ?>" >Alterar</a></li>
				<?php
				}
				if( Sessao::Permissao('UNIDADES_ESTOQUE') ) {
				?>
				<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/estoqueDaUnidade'); ?>" >Estoque</a></li>
				<?php
				}
				?>
		</ul>
	</li>
		<?php
		}
			
		if( Sessao::Permissao('ACS_') ) {
		?>
	<li><a href="#">ACS</a>
		<ul class="semtic">
			<div align="center"><span style="font-size:x-small;">
			<strong>ACS</strong></span></div>
			<?php
			if( Sessao::Permissao('ACS_INSERIR') ) {
			?>
			<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/inserirAcs'); ?>" >Inserir</a></li>
			<?php
			}
			if( Sessao::Permissao('ACS_LISTAR') ) {
			?>
			<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/listarAcs'); ?>" >Listar</a></li>
			<?php
			}
			?>
		</ul>
	</li>
		<?php
		}
			
		if( Sessao::Permissao('CAMPANHAS_') ) {
		?>
	<li><a href="#">Campanhas</a>
		<ul class="semtic">
			<div align="center"><span style="font-size:x-small;">
			<strong>Campanha</strong></span></div>
			<?php
			if( Sessao::Permissao('CAMPANHAS_INSERIR') ) {
			?>
			<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/inserirCampanha'); ?>" >Inserir</a></li>
			<?php
			}
			
			if( Sessao::Permissao('CAMPANHAS_LISTAR') ) {
			?>
			<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/listarCampanhas'); ?>" >Listar</a></li>
			<?php
			}
			?>
		</ul>
	</li>
		<?php
		}
			
		if( Sessao::Permissao('CONFIGURAR_') ) {
		?>
	
	<li><a href="#">Configurar</a>
		<ul class="semtic">
			<div align="center"><span style="font-size:x-small;">
			<strong>Configurar</strong></span></div>
			<?php
			if (Sessao::Permissao('CONFIGURAR_TEXTOINICIAL')) {
			?>
			<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/editarInicio'); ?>" >Texto Inicial</a></li>
			<?php
			}
			
			if(Sessao::Permissao('CONFIGURAR_NOTA')){
			?>
			<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/nota'); ?>" >Nota</a></li>
			<?php
			}
			?>
		</ul>
	</li>
		<?php
		}
			
		if ( Sessao::Permissao('USUARIOS_ACESSOS') ) {
		?>
	
	<li><a href="#">Usu�rios</a>
		<ul class="semtic">
			<div align="center"><span style="font-size:x-small;">
			 <strong>Visualizar</strong></span></div>
			<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/acessos'); ?>" >Visualizar</a></li>
		</ul>
	</li>
		<?php
		}
		?>
	<!--	////////////////////////////////////// -->
	<li><a href="#">Visualizar</a>
		<ul class="semtic">
			<div align="center"><span style="font-size:x-small;">
			 <strong>Visualizar</strong></span></div>
			<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/vacinas'); ?>" >Vacinas</a></li>
		</ul>
	</li>
	<!--	////////////////////////////////////// -->
	</ul>
	<?php
	}
	
	if( Sessao::Permissao('INDIVIDUOS_') ) {
	?>
	<li class="comtic"><a href="#">Indiv�duo</a>
	
		<ul class="semtic">
		<div align="center"><span style="font-size:x-small;">
		<strong>Indiv�duo</strong></span></div>
		<?php
		if( Sessao::Permissao('INDIVIDUOS_BUSCAR') ) {
		?>
		<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/listarPessoa'); ?>">
		Buscar</a></li>
		<?php
		}
		
		if( Sessao::Permissao('INDIVIDUOS_CADASTRAR') ) {
		?>
		<li><a href="?<?php echo $crip->Cifrar('pagina=Adm/inserirPessoa'); ?>">
		Cadastrar</a></li>
		<?php
		}
		
		if( Sessao::Permissao('INDIVIDUOS_VACINAR') ) {
		?>
		<li><a href="?<?php echo  $crip->Cifrar('pagina=Adm/listarPessoasVacinaveis'); ?>">
		Vacinar</a></li>
		<?php
		}
		
		if( Sessao::Permissao('INDIVIDUOS_CADERNETA') ) {
		?>
		<li><a href="?<?php echo  $crip->Cifrar('pagina=Adm/listarPessoasVacinaveis&retroativo'); ?>">
		Caderneta</a></li>
		<?php
		}

		if( Sessao::Permissao('INDIVIDUOS_INTERCORRENCIA') ) {
		?>
		<li><a href="?<?php echo  $crip->Cifrar('pagina=Adm/intercorrencias'); ?>">
		Evento Adverso</a></li>
		<?php
		}
		?>
		</ul>
	</li>
	<?php
	}
	
	if( Sessao::Permissao('RELATORIOS_') ) {
	?>
	<li class="comtic">
		<a href="<?php echo '?', $crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=geral'); ?>">Relat�rios</a>
		<ul class="semtic">
		<div align="center"><span style="font-size:x-small;">
		<strong>Relat�rios</strong></span></div>
		<?php
		if( Sessao::Permissao('RELATORIOS_INDIVIDUO') ) {
		?>
		<li><a href="<?php echo '?', $crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=individuo'); ?>" >Indiv�duo</a></li>
		<?php
		}
		
		if( Sessao::Permissao('RELATORIOS_ROTINA') ) {
		?>
		<li><a href="<?php echo '?', $crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=rotina'); ?>" >Rotina</a></li>
		<?php
		}

		if( Sessao::Permissao('RELATORIOS_CAMPANHA') ) {
		?>
		<li><a href="<?php echo '?', $crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=campanha'); ?>" >Campanha</a></li>
		<?php
		}
		
		if( Sessao::Permissao('RELATORIOS_INTERCORRENCIA') ) {
		?>
		<li><a href="<?php echo '?', $crip->Cifrar('pagina=Rel/listarRelatorios&subtipo=intercorrencia'); ?>" >Evento Adverso</a></li>
		<?php
		}
		?>
		</ul>

	</li>
	<?php
	}
	?>

<li><a href="?<?php echo $crip->Cifrar('pagina=contato')?>">Contato</a></li>
	
</ul>