-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Mar 13, 2012 as 01:45 PM
-- Versão do Servidor: 5.1.54
-- Versão do PHP: 5.3.5-1ubuntu7.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `estado`
--

CREATE TABLE IF NOT EXISTS `estado` (
  `id` char(2) NOT NULL,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `estado`
--

INSERT INTO `estado` (`id`, `nome`) VALUES
('AC', 'Acre'),
('AL', 'Alagoas'),
('AM', 'Amazonas'),
('AP', 'Amapá'),
('BA', 'Bahia'),
('CE', 'Ceará'),
('DF', 'Distrito Federal'),
('ES', 'Espírito Santo'),
('ET', 'Estado de Teste'),
('GO', 'Goiás'),
('MA', 'Maranhão'),
('MG', 'Minas Gerais'),
('MS', 'Mato Grosso do Sul'),
('MT', 'Mato Grosso'),
('PA', 'Pará'),
('PB', 'Paraíba'),
('PE', 'Pernambuco'),
('PI', 'Piauí'),
('PR', 'Paraná'),
('RJ', 'Rio de Janeiro'),
('RN', 'Rio Grande do Norte'),
('RO', 'Rondônia'),
('RR', 'Roraima'),
('RS', 'Rio Grande do Sul'),
('SC', 'Santa Catarina'),
('SE', 'Sergipe'),
('SP', 'São Paulo'),
('TO', 'Tocantins');

-- --------------------------------------------------------

--
-- Estrutura da tabela `estadoqueusa`
--

CREATE TABLE IF NOT EXISTS `estadoqueusa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Estado_id` char(2) NOT NULL,
  `datahoracadastro` datetime NOT NULL,
  `datahoraultimoacesso` datetime NOT NULL,
  `localbd` varchar(100) NOT NULL,
  `loginbd` varchar(100) NOT NULL,
  `senhabd` varchar(100) NOT NULL,
  `bd` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `EstadoQueUsa_FKIndex1` (`Estado_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Extraindo dados da tabela `estadoqueusa`
--

INSERT INTO `estadoqueusa` (`id`, `Estado_id`, `datahoracadastro`, `datahoraultimoacesso`, `localbd`, `loginbd`, `senhabd`, `bd`) VALUES
(1, 'RJ', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(2, 'ET', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'localhost', 'root', 'ippes', 'sivac_estado'),
(8, 'AC', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(9, 'AL', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(10, 'AM', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(11, 'AP', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(12, 'BA', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(13, 'CE', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(14, 'DF', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(15, 'ES', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(16, 'GO', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(17, 'MA', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(18, 'MG', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(19, 'MS', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(20, 'MT', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(21, 'PA', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(22, 'PB', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(23, 'PE', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(24, 'PI', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(25, 'PR', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(26, 'RN', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(27, 'RO', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(28, 'RR', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(29, 'RS', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(30, 'SC', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(31, 'SE', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(32, 'SP', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac'),
(33, 'TO', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'www.sivac.com.br', 'sivac', 'sivac', 'sivac');

--
-- Restrições para as tabelas dumpadas
--

--
-- Restrições para a tabela `estadoqueusa`
--
ALTER TABLE `estadoqueusa`
  ADD CONSTRAINT `estadoqueusa_ibfk_1` FOREIGN KEY (`Estado_id`) REFERENCES `estado` (`id`) ON UPDATE CASCADE;
