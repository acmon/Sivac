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
* Transporte: Classe que representa um transporte de vacina de uma unidade
* para a outra.
*
* A classe é responsável por tratar todos os processos relativos ao
*  transporte de uma vacina, guardando suas características. A classe
* é responsável também pelo acesso ao banco de dados (não há camada 
* específica para isso)
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
         * Método responsável por inserir no BD uma nova entrada de transporte
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

                // Retorna as linahs afetadas pelo execute() para a variável $inserido
                $inserido = $inserirTransporte->affected_rows;

                // /fecha a conexão
                $inserirTransporte->close();

                // Se houver resultado...
                if ($inserido > 0) {

                        //... atualiza o estoque
                        return $idIncluido;
                }

                if($inserido < 0) {
                        //die('1');
                        $this->AdicionarMensagemDeErro("Ocorreu algum erro ao
                            cadastrar. Verifique se a operação já foi feita
                            anteriormente.");
                        return false;
                }

                if($inserido == 0) {

                        //die('2');
                        $this->AdicionarMensagemDeErro("Não foi possível
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
         * Método responsável por excluir no BD uma entrada de transporte
         *
         */
        public function ExcluirTransporte ($idIncluido) {


                // prepara uma inserção...
                $removerTransporte = $this->conexao->prepare('DELETE FROM `transporte` WHERE id = ?')
                or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                $removerTransporte->bind_param('i', $idIncluido);

                //... e executa essa sql
                $removerTransporte->execute();

                // Retorna as linahs afetadas pelo execute() para a variável $inserido
                $removido = $removerTransporte->affected_rows;

                // /fecha a conexão
                $removerTransporte->close();

                // Se houver resultado...
                if ($removido > 0) {

                        //... atualiza o estoque
                        return true;
                }

                if($removido < 0) {

                        //die('3');
                        $this->AdicionarMensagemDeErro("Ocorreu algum erro ao
                            descartar a vacina. Verifique se a operação já
                            foi feita anteriormente.");
                        return false;
                }

                if($removido == 0) {

                        //die('4');
                        $this->AdicionarMensagemDeErro("Não foi possível
                            descartar esta vacina.");
                        return false;
                }
        }
}
