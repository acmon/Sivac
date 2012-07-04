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



Sessao::Singleton()->ChecarAcesso();

$navegacao = Navegacao::AdicionarIntercorrencia();

$intercorrencia = new Intercorrencia();

$intercorrencia->UsarBaseDeDados();

if( $intercorrencia->VerificarSeEmitiuFormulario() ) {

	
	if( $intercorrencia->VerificarNaoDuplicidadeDeIntercorrencia($usuariovacinado_id, $_POST['intercorrencia_id'],
            $_POST['data_inicio'], $_POST['hora_inicio'], $campanha_id) ) {

		if($inseriu = $intercorrencia->RegistrarIntercorrenciaOcorrida($usuariovacinado_id,
																	   $_POST['intercorrencia_id'],
																	   $_POST['data_inicio'],
																	   $_POST['hora_inicio'],
																	   $_POST['obs'],
                                                                       $campanha_id )) $_POST = array();
	}
}

//if( isset($inseriu) && $inseriu == true) {
if( isset($inseriu) && $inseriu) {

    $crip = new Criptografia();
	//$intercorrencia->ExibirMensagem('Registro inserido com sucesso!');
    echo "<script>window.location='?" . $crip->Cifrar('pagina=Adm/intercorrencias') . "';</script>";
}
else if($intercorrencia->VerificarSeEmitiuFormulario()) $intercorrencia->ExibirMensagem('Erro ao inserir!');

$intercorrencia->ExibirFormularioConfirmarIntercorrencia($id, $vacina_id, $campanha_id);
$intercorrencia->ExibirMensagensDeErro();

