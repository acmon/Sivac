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

/**
* Transporte: Classe que representa um transporte de vacina de uma unidade
* para a outra.
*
* A classe � respons�vel por tratar todos os processos relativos ao
*  transporte de uma vacina, guardando suas caracter�sticas. A classe
* � respons�vel tamb�m pelo acesso ao banco de dados (n�o h� camada 
* espec�fica para isso)
*
*
* @package Sivac/Class
*
* @author Rafael 06/04/2009
*
* @copyright 2008 
*
*/

class Transporte extends AgenteImunizador {

        private $_unidadeDeSaudeDestino;      //obj
        private $_unidadeDeSaudeOrigem;       //obj
        private $_vacina;                     //obj
        private $_quantidade;                 //int
        private $_dataDeTransporte;           //string
        private $_obs;                        //string
        private $_lote;		              //int
        private $_validadeDoLote;             //string
        private $_atualizadorDeEstoque;       //obj

        //--------------------------------------------------------------------------------
        public function __construct() {

                parent::__construct();

                $this->_unidadeDeSaudeDestino = new UnidadeDeSaude();
                $this->_unidadeDeSaudeOrigem  = new UnidadeDeSaude();
                $this->_vacina                = new Vacina();
                $this->_atualizadorDeEstoque  = new UnidadeDeSaude();
        }

        //--------------------------------------------------------------------------------

        public function __destruct() {

                if( isset($this->_conexao) ) $this->_conexao->close();
        }

        //--------------------------------------------------------------------------------

        public function SetarUnidadeDeSaudeDestino($unidadeDeSaudeDestino){

                $this->_unidadeDeSaudeDestino = $unidadeDeSaudeDestino;

        }

        //--------------------------------------------------------------------------------

        public function SetarUnidadeDeSaudeOrigem($unidadeDeSaudeOrigem){

                $this->_unidadeDeSaudeOrigem = $unidadeDeSaudeOrigem;
        }

        //--------------------------------------------------------------------------------

        public function SetarVacina($vacina){

                $this->_vacina = $vacina;

        }

        //--------------------------------------------------------------------------------

        public function SetarQuantidade($quantidade) {

                $this->_quantidade = $quantidade;

        }

        //--------------------------------------------------------------------------------
        public function SetarDataDeTransporte($dataDeTransporte){

                $this->_dataDeTransporte = $dataDeTransporte;

        }

        //--------------------------------------------------------------------------------

        public function SetarObs($obs){

                $this->_obs = $obs;

        }

        //--------------------------------------------------------------------------------

        public function SetarLote($lote){

                $this->_lote = $lote;

        }

        //--------------------------------------------------------------------------------

        public function SetarValidadeDoLote($validadeDoLote){

                $this->_validadeDoLote = $validadeDoLote;

        }

        //--------------------------------------------------------------------------------

        public function SetarDados($post) {

                $clean = Preparacao::GerarArrayLimpo($post, $this->conexao);
                $this->SetarUnidadeDeSaudeDestino = $clean['unidadeCentral'];
                $this->SetarUnidadeDeSaudeOrigem  = $clean['unidade'];
                $this->SetarVacina                = $clean['vacina'];
                $this->SetarQuantidade            = $clean['quantidade'];
                $this->SetarDataDeTransporte      = $clean['datadeenvio'];
                $this->SetarObs                   = $clean['obs'];
                $this->SetarLote                  = $clean['lote'];
                $this->SetarValidadeDoLote        = $clean['validade'];

        }
        //--------------------------------------------------------------------------------
        public function UnidadeDeSaudeDestino() {

                return $this->_unidadeDeSaudeDestino;

        }

        //--------------------------------------------------------------------------------

        public function UnidadeDeSaudeOrigem() {

                return $this->_unidadeDeSaudeOrigem;
        }

        //--------------------------------------------------------------------------------

        public function Vacina() {

                return $this->_vacina;

        }

        //--------------------------------------------------------------------------------

        public function Quantidade() {

                return $this->_quantidade;

        }

        //--------------------------------------------------------------------------------

        public function DataDeTransporte () {

                return $this->_dataDeTransporte;

        }

        //--------------------------------------------------------------------------------

        public function Obs() {

                return $this->_obs;
        }

        //--------------------------------------------------------------------------------

        public function Lote() {

                return $this->_lote;

        }

        //--------------------------------------------------------------------------------

        public function ValidadeDoLote() {

                $this->_validadeDoLote;

        }

        //--------------------------------------------------------------------------------
        /**
         * InserirTransposte
         *
         * M�todo respons�vel por inserir no BD uma nova entrada de transporte
         *
         */
        public function InserirTransporte($unidadeDeSaudeDestino_id, $unidadeDeSaudeOrigem_id,
                                           $vacina_id, $quantidade, $dataDeTransporte, $lote,
                                           $validadeDoLote, $obs, $codigoDeBarras, $produto) {

                $inserirTransporte = $this->conexao->prepare('INSERT INTO `transporte` (id, UnidadeDeSaudeDestino_id,
                UnidadeDeSaudeOrigem_id, Vacina_id, quantidade, datadetransporte, obs, lote, validadedolote, codigoDeBarras, produto) VALUES
                (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)') or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));;

                $inserirTransporte->bind_param('iiiissssss', $unidadeDeSaudeDestino_id, $unidadeDeSaudeOrigem_id,
                $vacina_id, $quantidade, $dataDeTransporte, $obs, $lote, $validadeDoLote, $codigoDeBarras, $produto);

                //... e executa essa sql
                $inserirTransporte->execute(); 

                $idIncluido = $inserirTransporte->insert_id;

                // Retorna as linahs afetadas pelo execute() para a vari�vel $inserido
                $inserido = $inserirTransporte->affected_rows;

                // /fecha a conex�o
                $inserirTransporte->close();

                // Se houver resultado...
                if ($inserido > 0) {

                        //... atualiza o estoque
                        return $idIncluido;
                }

                if($inserido < 0) {
                        //die('1');
                        $this->AdicionarMensagemDeErro("Ocorreu algum erro ao
                            cadastrar. Verifique se a opera��o j� foi feita
                            anteriormente.");
                        return false;
                }

                if($inserido == 0) {

                        //die('2');
                        $this->AdicionarMensagemDeErro("N�o foi poss�vel
                            cadastrar o estoque.");
                        return false;
                }
        }

        //--------------------------------------------------------------------------------
        public function RetornarEstoque($unidadeDeSaude_id, $vacina_id)
        {

            $sql = "SELECT quantidade
                        FROM vacinadaunidade
                            WHERE UnidadeDeSaude_id = ?
                            AND Vacina_id = ?";
            
            $quantidade = false;

            $stmt = $this->conexao->prepare($sql)
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $stmt->bind_param('ii', $unidadeDeSaude_id, $vacina_id);
            $stmt->bind_result($quantidade);
            $stmt->execute();
            $stmt->fetch();
            $stmt->free_result();

            return $quantidade;
        }
        //--------------------------------------------------------------------------------
        /**
         * ExcluirTransposte
         *
         * M�todo respons�vel por excluir no BD uma entrada de transporte
         *
         */
        public function ExcluirTransporte ($idIncluido) {


                // prepara uma inser��o...
                $removerTransporte = $this->conexao->prepare('DELETE FROM `transporte` WHERE id = ?')
                or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                $removerTransporte->bind_param('i', $idIncluido);

                //... e executa essa sql
                $removerTransporte->execute();

                // Retorna as linahs afetadas pelo execute() para a vari�vel $inserido
                $removido = $removerTransporte->affected_rows;

                // /fecha a conex�o
                $removerTransporte->close();

                // Se houver resultado...
                if ($removido > 0) {

                        //... atualiza o estoque
                        return true;
                }

                if($removido < 0) {

                        //die('3');
                        $this->AdicionarMensagemDeErro("Ocorreu algum erro ao
                            descartar a vacina. Verifique se a opera��o j�
                            foi feita anteriormente.");
                        return false;
                }

                if($removido == 0) {

                        //die('4');
                        $this->AdicionarMensagemDeErro("N�o foi poss�vel
                            descartar esta vacina.");
                        return false;
                }
        }
}
