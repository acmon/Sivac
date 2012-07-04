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
 *  Classe para tratamento de datas
 *
 * 
 * @package Sivac/Class
 *
 * @author Maykon Monnerat (maykon_ttd@hotmail.com), v 1.0, 2008-08
 *
 * @copyright 2008 
 */
class Data
{
	private $arrDias = array(31,28,31,30,31,30,31,31,30,31,30,31);

	public function __construct()
	{
		date_default_timezone_set('America/Sao_Paulo');
	}
	//--------------------------------------------------------------------------
	/**
	 *  Devolve um intervalo entre datas por dias
	 *
	 *  @param int $dataInicial
	 *  @param int $dataFinal
	 *
	 * 	@return String
	 */
	public function IntervaloDeDatasEmDias( $dataInicial = 1, $dataFinal = false )
	{
		date_default_timezone_set('America/Sao_Paulo');

		if ( $dataFinal == false ) {
			$dataFinal = $dataInicial + 7;
		} else {
			if ( $dataInicial > $dataFinal ) {
				return false;
			}
		}

		$dia_atual = date("Y/m/d");

		$inicio = "";
		$fim = "";

		$inicio = $this->DecrementarData( $dia_atual, $dataInicial );
		$fim = $this->DecrementarData( $dia_atual, $dataFinal );

		return "$inicio,$fim";
	}
	//--------------------------------------------------------------------------
	/**
	 *  Devolve um intervalo entre datas por anos
	 *
	 *  @param data $idade
	 *  @param data $idadeFinal
	 *
	 * 	@return String
	 */
	public function IntervaloDeDatasEmAnos( $idade, $idadeFinal = false )
	{
		date_default_timezone_set('America/Sao_Paulo');

		$dia_atual = date("d");
		$mes_atual = date("m");
		$ano_atual = date("Y");

		$resultado = "";

		$dia_inicial = $dia_atual + 1;
		$mes_inicial = $mes_atual;
		if ( $idadeFinal == false ) {
			$ano_inicial = $ano_atual - ($idade + 1);
		} else {
			$ano_inicial = $ano_atual - ($idadeFinal + 1);
		}


		if ( $dia_inicial > $this->arrDias[ $mes_atual - 1 ] ) {
			switch($mes_inicial) {
				case 2:
				case 02:
					if ( $dia_inicial > 28 && !checkdate('2','29',$ano_inicial ) ) {
						$dia_inicial = 1;
						if ( $mes_inicial == 12 ) {
							$mes_inicial = 1;
							$ano_inicial++;
						} else {
							$mes_inicial++;
						}
					} elseif ( $dia_inicial > 29 ) {
						$dia_inicial = 1;
						if ( $mes_inicial == 12 ) {
							$mes_inicial = 1;
							$ano_inicial++;
						} else {
							$mes_inicial++;
						}
					}
					break;
				default:
					$dia_inicial = 1;
					if ( $mes_inicial == 12 ) {
						$mes_inicial = 1;
						$ano_inicial++;
					} else {
						$mes_inicial++;
					}
					break;
			}
		}

		$data_inicial = $ano_inicial."/".$mes_inicial."/".$dia_inicial;

		if ( $mes_atual == 2 && $dia_atual == 29 && checkdate('2','29',$ano_atual) ) {
			$dia_atual--;
		}

		$data_final = $ano_atual - $idade."/".$mes_atual."/".$dia_atual;

		$inicialValida = checkdate($mes_inicial, $dia_inicial, $ano_inicial);
		$finalValida = checkdate($mes_atual, $dia_atual, $ano_atual - $idade);

		$resultado = $data_inicial . "," . $data_final;

		if($inicialValida && $finalValida) return $resultado;

		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Incrementa uma data passada no formato ano/mes/dia. A unidade de
	 * incremento padrão é 1 e o tempo padrão é em dia, podendo ser também
	 * semana, mês, e ano (day, week, month, year).
	 *
	 * @param String $data
	 * @param int $incremento
	 * @param String $unidadeDeTempo
	 */
	public function IncrementarData($data, $incremento = 1, $unidadeDeTempo = 'day')
	{
        //echo "<script>alert('$data')</script>";
        
		list($ano, $mes, $dia) = preg_split('@[^0-9]{1}@', $data);

		if( checkdate($mes, $dia, $ano) ) {

			$dataNova = new DateTime($data);
			$dataNova->modify("+$incremento $unidadeDeTempo");

			return $dataNova->format('Y/m/d');
		}
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 * Decrementa uma data passada no formato ano/mes/dia. A unidade de
	 * incremento padrão é 1 e o tempo padrão é em dia, podendo ser também
	 * semana, mês, e ano (day, week, month, year).
	 *
	 * @param String $data
	 * @param int $incremento
	 * @param String $unidadeDeTempo
	 */
	//--------------------------------------------------------------------------
	public function DecrementarData($data, $decremento = 1, $unidadeDeTempo = 'day')
	{
		list($ano, $mes, $dia) = preg_split('@[^0-9]{1}@', $data);

		if( checkdate($mes, $dia, $ano) ) {

			$dataNova = new DateTime($data);
			$dataNova->modify("-$decremento $unidadeDeTempo");

			return $dataNova->format('Y/m/d');
		}
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 *  Função que inverte a posição da data de 1-2-3 para 3-2-1
	 *
	 *  @param data $data
	 *  @param int $incremento
	 *
	 * 	@return data
	 */
	public function InverterPosicaoData($data)
	{
		$arrData = split('/',$data);
		return $arrData[2].'/'.$arrData[1].'/'.$arrData[0];
	}
	//--------------------------------------------------------------------------
	/**
	 * Inverte a data para o padrão americano
	 *
	 * @param int $data
	 * @return int
	 */
	public function InverterData($data)
	{
        if( strlen($data) > 10 ) $data = substr($data, 0, 10);

		if( strlen($data) < 2 ) return $data;
		
		list($p1, $p2, $p3) = preg_split('@[^0-9]{1}@', $data);

		if(  isset($p1, $p2, $p3)  ) return "$p3/$p2/$p1";
	}
	//-------------------------------------------------------------------------
	/**
	 * Recebe uma data de nascimento e retorna a idade exata (dias, meses e
	 * anos) do indivíduo. O retorno é no formato anos/meses/dias. P.ex. Se o
	 * o indivíduo tem menos de um ano, p.ex, 11 meses e 3 dias, o retorno será
	 * 0/11/3.
	 *
	 * @param String $data Data de nascimento do indivíduo
	 * @return String|boolean Anos, meses e dias no formato anos/meses/dias. Se
	 * 			a data de nascimento não for válida, o método retorna false
	 */
	public function IdadeExata($data, $ate = false)
	{
		list($ano, $mes, $dia) = preg_split('@[^0-9]{1}@', $data);

		if( isset($ano, $mes, $dia) && checkdate($mes, $dia, $ano) ) {

			date_default_timezone_set('America/Sao_Paulo');
			$dia_atual = date("d");
			$mes_atual = date("m");
			$ano_atual = date("Y");

			if($ate) {
				list($ano_atual, $mes_atual, $dia_atual) = preg_split('@[^0-9]{1}@', $ate);
			}

			if( $dia_atual < $dia ) {

				$dia_atual += 30;
				$mes_atual--;
			}

			$dias = $dia_atual - $dia;

			if( $mes_atual < $mes ) {

				$mes_atual += 12;
				$ano_atual--;
			}

			$meses = $mes_atual - $mes;

			$anos = $ano_atual - $ano;

			return "$anos/$meses/$dias";
		}

		return false;
	}
    //--------------------------------------------------------------------------
	/**
	 * Conexão com a Base de Dados
	 *
	 */
	public function UsarBaseDeDados()
	{
		$this->conexao = mysqli_connect($_SESSION['local'], $_SESSION['login'],
			$_SESSION['senha']);


		$this->conexao->select_db($_SESSION['banco']);

		//echo mysqli_connect_error(); die;
	}
    //--------------------------------------------------------------------------
    /**
     * Atualiza os campos idadeano, idademes e idadedia das tabelas
     * usuariovacinado e usuariovacinadocampanha do sivac_bduf:
     *
     * @param String $tabela deve ser usuariovacinado ou usuariovacinadocampanha
     */
    public function AtualizarIdadeAnoMesDiaDaTabela($tabela)
    {
        if( !($tabela == 'usuariovacinado' || $tabela == 'usuariovacinadocampanha') )
        {
            echo 'tabela incompatível';
            return;
        }
        
        $registros = $this->conexao->prepare("SELECT {$tabela}.id, datahoravacinacao, nascimento
                                                FROM `{$tabela}`, usuario
                                                    WHERE usuario.id = Usuario_id
                                                        ORDER BY nascimento")
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        $registros->bind_result($usuarioVacinadoId, $data, $nascimento);
        $registros->execute();
        $registros->store_result();

        $qtdRegistros = $registros->num_rows;

        $galerao = array();

        while (  $registros->fetch()  ) {
            list($ano,$mes,$dia) = explode('/',$this->IdadeExata($nascimento, $data));

            $galerao[$usuarioVacinadoId] = array('ano' => $ano, 'mes' => $mes, 'dia' => $dia);

        }

        $registros->free_result();

        foreach($galerao as $id => $sujeito) {

            //echo '<br />Sujeito: ', $id, '; ';
            //echo "$sujeito[ano] Anos -  $sujeito[mes] Meses  -  $sujeito[dia] Dias<br />";

            $ano = $sujeito['ano'];
            $mes = $sujeito['mes'];
            $dia = $sujeito['dia'];

            $registros = $this->conexao->prepare("UPDATE
                                        `{$tabela}`
                                            SET idadeano = ?,
                                                idademes = ?,
                                                idadedia = ?
                                                    WHERE {$tabela}.id = ?")
            or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

            $registros->bind_param('iiii', $ano, $mes, $dia, $id);
            $registros->execute();

        }
			
    }

    //--------------------------------------------------------------------------

    public function AtualizarProximaDoseNoBanco($exibirInformacoes = false)
    {
         $sql = "SELECT id, Vacina_id,
                                   diaidealparavacinar,
                                   numerodadose,
                                   dosebase
                                       FROM `intervalodadose`";
         
         $registros = $this->conexao->prepare($sql)
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        $registros->bind_result($id,
                                $vacina_id,
                                $diaidealparavacinar,
                                $numerodadose,
                                $dosebase);
        $registros->execute();
        $registros->store_result();

        $qtdRegistros = $registros->num_rows;

        $intervalosDeDoses = array();

        while (  $registros->fetch()  ) {

            $vacina[$vacina_id][$numerodadose] = array('diaidealparavacinar' => $diaidealparavacinar, 'dosebase' => $dosebase);
        }

        $registros->free_result();

        ////////////////////////////////////////////////////////////////////////

        $sql = "SELECT id,
                 DATE(datahoravacinacao) as datahoravacinacao,
                 Vacina_id,
                 numerodadose,
                 Usuario_id
                    FROM `usuariovacinado`";

        $registros = $this->conexao->prepare($sql)
				or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

        $registros->bind_result($usuarioVacinado_id,
                                $datahoravacinacao,
                                $vacina_id,
                                $numerodadose,
                                $usuario_id);
        $registros->execute();
        $registros->store_result();

        $qtdRegistros = $registros->num_rows;

        $galerao = array();

        while (  $registros->fetch()  ) {

            $galerao[$usuarioVacinado_id] = array('datahoravacinacao' => $datahoravacinacao,
                                                  'Vacina_id' => $vacina_id,
                                                  'numerodadose' => $numerodadose,
                                                  'Usuario_id' => $usuario_id);
                                              
            $arr[$numerodadose][$usuario_id][$vacina_id] = $datahoravacinacao;

        }

        $registros->free_result();

        // Atualiza a data da próxima dose para todas as doses aplicadas.
        // Quando a próxima dose não existe (o cara aplicou a úlitma dose)
        // então proximadose é gravado com 0.
        foreach($galerao as $id => $camaradaVacinado)
        {
            // Verifica se a próxima dose existe:
            $existe = isset($vacina[$camaradaVacinado['Vacina_id']][$camaradaVacinado['numerodadose']+1]['diaidealparavacinar']);

            if($existe)
            {
                $doseBase = $vacina[$camaradaVacinado['Vacina_id']][$camaradaVacinado['numerodadose']+1]['dosebase'];

                $doseAtual = $camaradaVacinado['numerodadose'];
                $proximaDose = $doseAtual + 1;
                $diasParaIncrementar = $vacina[$camaradaVacinado['Vacina_id']][$camaradaVacinado['numerodadose']+1]['diaidealparavacinar'];
                $dataVacinacao = $camaradaVacinado['datahoravacinacao'];
                $usuarioId = $camaradaVacinado['Usuario_id'];
                $vacinaId = $camaradaVacinado['Vacina_id'];
                
                if( $doseBase == 0)
                {
                    $dataIncrementada = $this->IncrementarData($dataVacinacao,  $diasParaIncrementar);

                    if( $exibirInformacoes ) {

                        echo '<h2>Quando a data da dose deve somar na dose anterior</h2>';

                        echo "<p>UsuarioVacinadoId: $id; Vacina_id: $vacinaId; dose: ",
                             "$proximaDose; tem que ser com $diasParaIncrementar",
                             " dias. $dataVacinacao, incrementando: $dataIncrementada</p>";
                    }
                    
                }
                else
                {
                    $dataDoseBase = $arr[$vacina[$vacinaId][$proximaDose]['dosebase']][$usuarioId][$vacinaId];
                    $dataIncrementada = $this->IncrementarData($dataDoseBase, $diasParaIncrementar);

                    if( $exibirInformacoes ) {

                        echo "<h2>Quando a data da dose deve somar na dose base ($doseBase)</h2>";

                        echo "<p>Usuario_id: $usuarioId; UsuarioVacinadoId: $id; Vacina_id: ",
                             "$vacinaId; dose: $proximaDose; tem que ser com $diasParaIncrementar",
                             " dias,<br>baseada na {$doseBase}a, com a data: $dataDoseBase",
                             " vai ser em: $dataIncrementada</p>";
                    }
                     // Se a a data incrementada baseada na dose base ficou menor do que a
                     // última dose aplicada, incrementa mais 30 na última dose
                     // aplicada:
                     if( $this->CompararData($dataIncrementada, '<', $dataVacinacao ) )
                     {

                       // Incrementa 60 dias a última dose aplicada (segundo Nádia)
                        $dataIncrementada = $this->IncrementarData($dataVacinacao, 60);

                        if( $exibirInformacoes ) {

                            echo "<p>A data da dose $doseAtual é maior do que a da dose $proximaDose!",
                                 "<br>Neste caso, como a dose $doseAtual foi posterior ",
                                 "aos dias em que a dose $proximaDose se baseou,<br>",
                                 "então ficou com mais 30 dias após a data $dataVacinacao",
                                 " $dataIncrementada</p>";
                        }
                     }
                }

                $sql = 'UPDATE `usuariovacinado`'
                     .     "SET proximadose = '$dataIncrementada'"
                     .         "WHERE id = $id";

                $registros = $this->conexao->prepare($sql)
                    or die(Bd::TratarErroSql($this->conexao->error, __FILE__, __LINE__));

                $registros->execute();
            }
        }
        $registros->close();
    }
    
	//--------------------------------------------------------------------------
	/**
	 * Retorna a quantidade de dias baseando-se em uma faixa de tempo (dias,
	 * meses e anos) enviada como argumento. Esta data deve estar no formato
	 * anos/meses/dias.
	 *
	 * @param String $idade Data no formato anos/meses/dias
	 * @return int Dias totais
	 */
	public function ConverterIdadeExataParaDias($idade)
	{
		list($anos, $meses, $dias) = explode('/', $idade);

		return ($anos * 365 + $meses * 30 + $dias);
	}

	//--------------------------------------------------------------------------

	// Data -> yyyy-mm-dd intervalos -> 500|250|120

	//--------------------------------------------------------------------------
	public function CompararData($data1, $operador, $data2 = 'hoje')
	{
		if( strpos($data1, ':') )	$dataA = new DateTime("$data1 GMT");
		else 						$dataA = new DateTime("$data1 00:00:00 GMT");
		

		if($data2 == 'hoje') $dataB = new DateTime(date('Y/m/d H:i:s') . ' GMT');
		
		else {
			if( strpos($data2, ':') )	$dataB = new DateTime("$data2 GMT");
			else						$dataB = new DateTime("$data2 00:00:00 GMT");
		}
		
		$dataUm = $dataA->format('U');
		$dataDois = $dataB->format('U');
		
		eval("\$result = ($dataUm $operador $dataDois);");

		if( isset($result) ) return $result;
	
		return false;
	}
	//--------------------------------------------------------------------------
	/*
	public function CompararData($data1, $operador, $data2 = 'hoje')
	{	
		/**
		 * OBSERVAÇÃO: strtotime() só funciona nos intervalos entre os anos de
		 * 1902 até no máximo 2038, por causa dos bits de alocação para um tipo
		 * short. Por isso, devemos chamar o método que verifica esse intervalo
		 * antes de fazer o teste.
		 */
	/*
		list($ano_A, $mes_A, $dia_A) = preg_split('@[^0-9]{1}@', $data1);

		date_default_timezone_set('America/Sao_Paulo');

		if($data2 == 'hoje') {

			$mes_B = date('m');
			$dia_B = date('d');
			$ano_B = date('Y');

			$data2 = "$ano_B/$mes_B/$dia_B";
		}
		else {

			list($ano_B, $mes_B, $dia_B) = preg_split('@[^0-9]{1}@', $data2);
		}

		if(checkdate($mes_A, $dia_A, $ano_A) && checkdate($mes_B, $dia_B, $ano_B)) {

			// Método para compatibilizar uma data ou maior que 2038 ou menor
			// que 1900 (anos menores que 1/1/1970 irão ser negativos, porém,
			// isso não afeta o cálculo

			list($novo_ano_A, $novo_ano_B) =
						$this->CompatibilizarDataParaStrtotime($ano_A, $ano_B);

			if ( $operador == '<' ) {
				
				$result = ($ano_A < $ano_B);
				$tempoAnoIgual = ($ano_A == $ano_B);
				
				if ( $tempoAnoIgual ) {
					
					$result = ($mes_A < $mes_B);
					$tempoMesIgual = ($mes_A == $mes_B);
					
					if ( $tempoMesIgual ) {
						
						$result = ($dia_A < $dia_B);
						
					}
					
				}
				
				return $result;
				
			} elseif ( $operador == '>' ) {
				
				$result = ($ano_A > $ano_B);
				$tempoAnoIgual = ($ano_A == $ano_B);
				
				if ( $tempoAnoIgual ) {
					
					$result = ($mes_A > $mes_B);
					$tempoMesIgual = ($mes_A == $mes_B);
					
					if ( $tempoMesIgual ) {
						
						$result = ($dia_A > $dia_B);
						
					}
					
				}
				
				return $result;
				
			} else {
				$dataUm = strtotime("$mes_A/$dia_A/$novo_ano_A");

				$dataDois = strtotime("$mes_B/$dia_B/$novo_ano_B");

				echo "<p>\$dataUm=$dataUm - \$mes_A=$mes_A/\$dia_A=$dia_A/\$novo_ano_A=$novo_ano_A<br />";
				echo "\$dataDois=$dataDois - \$mes_B=$mes_B/\$dia_B=$dia_B/\$novo_ano_B=$novo_ano_B</p>";
				
				eval("\$result = ($dataUm $operador $dataDois);");

				return $result;
			}
		}
		return -1;
	}
	*/
	//--------------------------------------------------------------------------
	/*
	private function CompatibilizarDataParaStrtotime($ano1, $ano2)
	{
		while($ano1 < 1902 || $ano2 < 1902) {

			$ano1++;
			$ano2++;
		}

		while($ano1 > 2037 || $ano2 > 2037) {

			$ano1--;
			$ano2--;
		}
	
		return array($ano1, $ano2);
	}
	*/
	//--------------------------------------------------------------------------
	public function VerificarPadraoData($data)
	{
		$simbolosDivisores = array('-', '.', ' ', '|', '\\');
		
		$data = str_replace($simbolosDivisores, '/', $data);
		
		if ( strlen($data) == 10 && $data[2] == '/' && $data[5] == '/' ) {
			if ( substr_count( $data, '/' ) == 2 ) {
				$ponteiro = 0;
				for ( $ponteiro=0; $ponteiro<strlen($data);$ponteiro++ ) {
					if ( substr_count('1234567890/',$data[$ponteiro] ) == 0 ) {
						return false;
					} else {
						list($dia, $mes, $ano) = explode('/',$data);
						if ( !checkdate($mes, $dia, $ano) ) return false;
					}
				}
				return true;
			}
		}
		
		return false;
	}
	//--------------------------------------------------------------------------
	public function ConverterDataParaDias($data)
	{
		
		if ( $this->VerificarPadraoData($data) ) {
			
			$arrDias = array (31,28,31,30,31,30,31,31,30,31,30,31);
			
			list( $dia, $mes, $ano ) = explode('/', $data);	
			
			$total = $ano * 365;
			
			$ponteiro = 0;
			
			$diasDoAno = 0;
			
			if ( $mes > 0 ) {
			
				for ( $ponteiro = 0; $ponteiro < $mes - 1 ; $ponteiro++ ) {
					$diasDoAno += $arrDias[$ponteiro];
				}
			}
			
			$diasDoAno += $dia;
			
			$total += $diasDoAno;
			
			return $total;
		}	
		
		return false;
	}
	//--------------------------------------------------------------------------
	public function IntervaloDeTempo($data1, $data2)
	{
		$intData1 = (int)strtotime("$data1 GMT");
		$intData2 = (int)strtotime("$data2 GMT");
		
		// A data1 deve ser sempre maior que data2:
		if($intData1 < $intData2) return false;
		
		date_default_timezone_set('GMT');
		$intervalo = ($intData1 - $intData2);

		return date('H:i:s', $intervalo);
	}
	//--------------------------------------------------------------------------
	public function ConverterUnidadeDeTempoParaData($tempo, $unidade)
	{

		$dataDeHoje = date('Y/m/d');
		
		$data = $this->DecrementarData($dataDeHoje, $tempo, $unidade);
		
		return $data;
	}
	
//------------------------------------------------------------------------------

    /**
     * Retorna a diferença entre duas datas, que pode ser em anos, dias, meses,
     * ou a combinação dos mesmos.
     * 
     * @param String $data1
     * @param String $data2
     * @return String
     */

    public function Diferenca($data2, $data1, $textual = false)
    {
        $intDias = 0;

        while( $this->CompararData($data2, '>', $data1) )
        {
            $data1 = $this->IncrementarData($data1);
            $intDias++;
        }

        if( !$textual ) return $intDias;
        
        $strTempo = $this->ConverterDiasParaUnidadeDeTempo($intDias);

        return $strTempo;
    }

//------------------------------------------------------------------------------
    /**
     * @param int $ano
     * @return bool
     *
     * Verifica se um ano é ou não bisexto:
     */
    public function AnoBisexto($ano)
    {
        if( ($ano % 4 == 0 && $ano % 100 != 0) || $ano % 400 == 0 ) {

            return true;
        }
        return false;
    }

//------------------------------------------------------------------------------

	/**
	 * Converte os dias passados para a unidade de tempo (anos, meses, semanas
	 * ou dias)
	 *
	 * @param int $dias
	 * @return boolean|string
	 */
	public function ConverterDiasParaUnidadeDeTempo($dias)
	{
		if ($dias > 0) {

            // Verifica a quantidade de dias que foram adicionadas no caso do
            // ano bisexto (1 dia a cada 4 anos no sistema):
            
            if($dias > 365)
            {
                $diasDoAnoBisexto = (int)($dias / 365);

                // Esta operação considera que de 4 em 4 anos é adicionado um dia
                // para o ano bisexto:
                if($dias % 365 == $diasDoAnoBisexto)
                {
                    $exato = (int)( ($dias - $diasDoAnoBisexto)   / 365 );
                    $resto = ( (int)($dias - $diasDoAnoBisexto) ) % 365;

                    if( $exato > 0 && $resto == 0 ) return "$exato ano(s)";
                    if( $exato > 0 ) return "$exato ano(s), $resto dia(s)";
                }
            }
            else
            {
                $diasDoAnoBisexto = $anos = 0;
            }
            
			if($dias % 365 < 30)
            {
                $exato = (int)($dias / 365);
                $resto = $dias % 365;
                
                if( $exato > 0 && $resto == 0 ) return  "$exato ano(s)";
                if( $exato > 0 ) return "$exato ano(s), $resto dia(s)";
			}

            // Considerar também ANOS se o usuário colocou p.ex 12 MESES (de 30
            // dias cada, o que dá 360 dias exatos) com folga de 1 semana:
			if($dias % 360 == 0)
            {
                $exato = $dias / 360;
                
				return "$exato ano(s) - considerando 12 meses de 30 dias";
			}

            // Entre 01-01-2000 e 01-12-2000 tem 11 meses, porém dá diferença de
            // no máximo 29, por isso não pode ser == 0:
			if($dias % 30 < 30)
            {
                $exato = (int)($dias / 30);
                $resto = $dias % 30;

                if( $exato > 0 && $resto == 0 ) return  "$exato mes(es)";
                if( $exato > 0 ) return "$exato mes(es), $resto dia(s)";
			}

			if($dias % 7 < 7)
            {
                $exato = (int)($dias / 7);
                $resto = $dias % 7;

                if( $exato > 0 && $resto == 0 ) return  "$exato semana(s)";
                if( $exato > 0 ) return "$exato semana(s), $resto dia(s)";
			}

			return $dias . ' dia(s)';
		}
		return false;
	}
}