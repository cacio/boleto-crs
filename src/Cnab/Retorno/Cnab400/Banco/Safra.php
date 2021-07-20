<?php
namespace Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Eduardokum\LaravelBoleto\Util;
use Eduardokum\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;

class Safra extends AbstractRetorno implements RetornoCnab400
{
    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_SAFRA;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '02' => 'ENTRADA CONFIRMADA',
		'03' => 'ENTRADA REJEITADA',
		'04' => 'TRANSFERÊNCIA DE CARTEIRA (ENTRADA)',
		'05' => 'TRANSFERÊNCIA DE CARTEIRA (BAIXA)',
		'06' => 'LIQUIDAÇÃO NORMAL',
		'09' => 'BAIXADO AUTOMATICAMENTE',
		'10' => 'BAIXADO CONFORME INSTRUÇÕES',
		'11' => 'TÍTULOS EM SER (PARA ARQUIVO MENSAL)',
		'12' => 'ABATIMENTO CONCEDIDO',
		'13' => 'ABATIMENTO CANCELADO',
		'14' => 'VENCIMENTO ALTERADO',
		'15' => 'LIQUIDAÇÃO EM CARTÓRIO',
		'19' => 'CONFIRMAÇÃO DE INSTRUÇÃO DE PROTESTO',
		'20' => 'CONFIRMAÇÃO DE SUSTAR PROTESTO',
		'21' => 'TRANSFERÊNCIA DE BENEFICIÁRIO',
		'23' => 'TÍTULO ENVIADO A CARTÓRIO',
		'40' => 'BAIXA DE TÍTULO PROTESTADO',
		'41' => 'LIQUIDAÇÃO DE TÍTULO BAIXADO',
		'42' => 'TÍTULO RETIRADO DO CARTÓRIO',
		'43' => 'DESPESA DE CARTÓRIO',
		'44' => 'ACEITE DO TÍTULO DDA PELO PAGADOR',
		'45' => 'NÃO ACEITE DO TÍTULO DDA PELO PAGADOR',
		'51' => 'VALOR DO TÍTULO ALTERADO',
		'52' => 'ACERTO DE DATA DE EMISSAO',
		'53' => 'ACERTO DE COD ESPECIE DOCTO',
		'54' => 'ALTERACAO DE SEU NUMERO',
		'56' => 'INSTRUÇÃO NEGATIVAÇÃO ACEITA',
		'57' => 'INSTRUÇÃO BAIXA DE NEGATIVAÇÃO ACEITA',
		'58' => 'INSTRUÇÃO NÃO NEGATIVAR ACEITA',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '01' => 'MOEDA INVÁLIDA',
		'02' => 'MOEDA INVÁLIDA PARA CARTEIRA',
		'07' => 'CEP NÃO CORRESPONDE UF',
		'08' => 'VALOR JUROS AO DIA MAIOR QUE 5% DO VALOR DO TÍTULO',
		'09' => 'USO EXCLUSIVO NÃO NUMÉRICO PARA COBRANCA EXPRESS',
		'10' => 'IMPOSSIBILIDADE DE REGISTRO => CONTATE O SEU GERENTE',
		'11' => 'NOSSO NÚMERO FORA DA FAIXA',
		'12' => 'CEP DE CIDADE INEXISTENTE',
		'13' => 'CEP FORA DE FAIXA DA CIDADE',
		'14' => 'UF INVÁLIDO PARA CEP DA CIDADE',
		'15' => 'CEP ZERADO',
		'16' => 'CEP NÃO CONSTA NA TABELA SAFRA',
		'17' => 'CEP NÃO CONSTA TABELA BANCO CORRESPONDENTE',
		'19' => 'PROTESTO IMPRATICÁVEL',
		'20' => 'PRIMEIRA INSTRUÇÃO DE COBRANÇA INVALIDA',
		'21' => 'SEGUNDA INSTRUÇÃO DE COBRANÇA INVÁLIDA',
		'23' => 'TERCEIRA INSTRUÇÃO DE COBRANÇA INVÁLIDA',
		'26' => 'CÓDIGO DE OPERAÇÃO/OCORRÊNCIA INVÁLIDO',
		'27' => 'OPERAÇÃO INVÁLIDA PARA O CLIENTE',
		'28' => 'NOSSO NÚMERO NÃO NUMÉRICO OU ZERADO',
		'29' => 'NOSSO NÚMERO COM DÍGITO DE CONTROLE ERRADO/INCONSISTENTE',
		'30' => 'VALOR DO ABATIMENTO NÃO NUMÉRICO OU ZERADO',
		'31' => 'SEU NÚMERO EM BRANCO',
		'32' => 'CÓDIGO DA CARTEIRA INVÁLIDO',
		'36' => 'DATA DE EMISSÃO INVÁLIDA',
		'37' => 'DATA DE VENCIMENTO INVÁLIDA',
		'38' => 'DEPOSITÁRIA INVÁLIDA',
		'39' => 'DEPOSITÁRIA INVÁLIDA PARA O CLIENTE',
		'40' => 'DEPOSITÁRIA NÃO CADASTRADA NO BANCO',
		'41' => 'CÓDIGO DE ACEITE INVÁLIDO',
		'42' => 'ESPÉCIE DE TÍTULO INVÁLIDO',
		'43' => 'INSTRUÇÃO DE COBRANÇA INVÁLIDA',
		'44' => 'VALOR DO TÍTULO NÃO NUMÉRICO OU ZERADO',
		'46' => 'VALOR DE JUROS NÃO NUMÉRICO OU ZERADO',
		'47' => 'DATA LIMITE PARA DESCONTO INVÁLIDA',
		'48' => 'VALOR DO DESCONTO INVÁLIDO',
		'49' => 'VALOR IOF. NÃO NUMÉRICO OU ZERADO (SEGUROS)',
		'51' => 'CÓDIGO DE INSCRIÇÃO DO SACADO INVÁLIDO',
		'53' => 'NÚMERO DE INSCRIÇÃO DO SACADO NÃO NÚMERICO OU DÍGITO ERRADO',
		'54' => 'NOME DO SACADO EM BRANCO',
		'55' => 'ENDEREÇO DO SACADO EM BRANCO',
		'56' => 'CLIENTE NÃO CADASTRADO',
		'58' => 'PROCESSO DE CARTÓRIO INVÁLIDO',
		'59' => 'ESTADO DO SACADO INVÁLIDO',
		'60' => 'CEP/ENDEREÇO DIVERGEM DO CORREIO',
		'61' => 'INSTRUÇÃO AGENDADA PARA AGÊNCIA',
		'62' => 'OPERAÇÃO INVÁLIDA PARA A CARTEIRA',
		'64' => 'TÍTULO INEXISTENTE (TFC)',
		'65' => 'OPERAÇÃO / TITULO JÁ EXISTENTE',
		'66' => 'TÍTULO JÁ EXISTE (TFC)',
		'67' => 'DATA DE VENCIMENTO INVÁLIDA PARA PROTESTO',
		'68' => 'CEP DO SACADO NÃO CONSTA NA TABELA',
		'69' => 'PRAÇA NÃO ATENDIDA PELO SERVIÇO CARTÓRIO',
		'70' => 'AGÊNCIA INVÁLIDA',
		'72' => 'TÍTULO JÁ EXISTE (COB)',
		'74' => 'TÍTULO FORA DE SEQÜÊNCIA',
		'78' => 'TÍTULO INEXISTENTE (COB)',
		'79' => 'OPERAÇÃO NÃO CONCLUÍDA',
		'80' => 'TÍTULO JÁ BAIXADO',
		'83' => 'PRORROGAÇÃO/ALTERAÇÃO DE VENCIMENTO INVÁLIDA',
		'85' => 'OPERAÇÃO INVÁLIDA PARA A CARTEIRA',
		'86' => 'ABATIMENTO MAIOR QUE VALOR DO TÍTULO',
		'88' => 'TÍTULO RECUSADO COMO GARANTIA (SACADO/NOVO/EXCLUSIVO/ALÇADA COMITÊ)',
		'89' => 'ALTERAÇÃO DE DATA DE PROTESTO INVÁLIDA',
		'94' => 'ENTRADA TÍTULO COBRANÇA DIRETA INVÁLIDA',
		'95' => 'BAIXA TÍTULO COBRANÇA DIRETA INVÁLIDA',
		'96' => 'VALOR DO TÍTULO INVÁLIDO',
		'98' => 'PCB DO TFC DIVERGEM DA PCB DO COB',
		'100' => 'INSTRUÇÃO NÃO PERMITIDA => TÍT COM PROTESTO (SE TÍTULO PROTESTADO NÃO PODE NEGATIVAR)',
		'101' => 'INSTRUÇÃO INCOMPATÍVEL => NÃO EXISTE INSTRUÇÃO DE NEGATIVAR PARA O TÍTULO',
		'102' => 'INSTRUÇÃO NÃO PERMITIDA => PRAZO INVÁLIDO PARA NEGATIVAÇÃO(MÍNIMO 2 DIAS CORRIDOS APÓS O VENCIMENTO)',
		'103' => 'INSTRUÇÃO NÃO PERMITIDA => TÍT INEXISTENTE ',
    ];

    /**
     * Roda antes dos metodos de processar
     */
    protected function init()
    {
        $this->totais = [
            'liquidados'  => 0,
            'entradas'    => 0,
            'baixados'    => 0,
            'protestados' => 0,
            'erros'       => 0,
            'alterados'   => 0,
        ];
    }

    /**
     * @param array $header
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarHeader(array $header)
    {
        $this->getHeader()
            ->setOperacaoCodigo($this->rem(2, 2, $header))
            ->setOperacao($this->rem(3, 9, $header))
            ->setServicoCodigo($this->rem(10, 11, $header))
            ->setServico($this->rem(12, 26, $header))
            ->setAgencia($this->rem(27, 30, $header))
            ->setConta($this->rem(33, 37, $header))
            ->setContaDv($this->rem(38, 38, $header))
            ->setData($this->rem(95, 100, $header));

        return true;
    }

    /**
     * @param array $detalhe
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarDetalhe(array $detalhe)
    {
        $d = $this->detalheAtual();
	
        $d->setCarteira($this->rem(108, 108, $detalhe))
            ->setNossoNumero($this->rem(63, 71, $detalhe))
            ->setNumeroDocumento($this->rem(117, 126, $detalhe))
            ->setNumeroControle($this->rem(38, 62, $detalhe))
            ->setOcorrencia($this->rem(109, 110, $detalhe))
            ->setOcorrenciaDescricao(array_get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
            ->setDataOcorrencia($this->rem(111, 116, $detalhe))
            ->setDataVencimento($this->rem(147, 152, $detalhe))
            ->setDataCredito($this->rem(296, 301, $detalhe))
            ->setCodigoLiquidacao($this->rem(393, 394, $detalhe))
            ->setValor(Util::nFloat($this->rem(153, 165, $detalhe) / 100, 2, false))
            ->setValorTarifa(Util::nFloat($this->rem(176, 188, $detalhe) / 100, 2, false))
            ->setValorIOF(Util::nFloat($this->rem(215, 227, $detalhe) / 100, 2, false))
            ->setValorAbatimento(Util::nFloat($this->rem(228, 240, $detalhe) / 100, 2, false))
            ->setValorDesconto(Util::nFloat($this->rem(241, 253, $detalhe) / 100, 2, false))
            ->setValorRecebido(Util::nFloat($this->rem(254, 266, $detalhe) / 100, 2, false))
            ->setValorMora(Util::nFloat($this->rem(267, 279, $detalhe) / 100, 2, false))
            ->setValorMulta(Util::nFloat($this->rem(280, 292, $detalhe) / 100, 2, false));

        $msgAdicional = str_split(sprintf('%08s', $this->rem(105, 107, $detalhe)), 2) + array_fill(0, 4, '');
        if ($d->hasOcorrencia('06', '07', '08', '10', '59')) {
            $this->totais['liquidados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
        } elseif ($d->hasOcorrencia('02', '64', '71', '73')) {
            $this->totais['entradas']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
        } elseif ($d->hasOcorrencia('05', '09', '47', '72')) {
            $this->totais['baixados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
        } elseif ($d->hasOcorrencia('32')) {
            $this->totais['protestados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
        } elseif ($d->hasOcorrencia('14')) {
            $this->totais['alterados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
        } elseif ($d->hasOcorrencia('03', '15', '16', '17', '18', '60')) {
            $this->totais['erros']++;
			 $d->setError(array_get($this->rejeicoes, $this->rem(106, 107, $detalhe), 'Consulte seu Internet Banking'));
            /*$error = Util::appendStrings(
                array_get($this->rejeicoes, $msgAdicional[0], ''),
                array_get($this->rejeicoes, $msgAdicional[1], ''),
                array_get($this->rejeicoes, $msgAdicional[2], ''),
                array_get($this->rejeicoes, $msgAdicional[3], '')
            );
            $d->setError($error);*/
        } else {
            $d->setOcorrenciaTipo($d::OCORRENCIA_OUTROS);
        }

        return true;
    }

    /**
     * @param array $trailer
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarTrailer(array $trailer)
    {
        $this->getTrailer()
            ->setQuantidadeTitulos((int) $this->rem(18, 25, $trailer) + (int) $this->rem(58, 65, $trailer) + (int) $this->rem(178, 185, $trailer))
            ->setValorTitulos((float) Util::nFloat($this->rem(221, 234, $trailer) / 100, 2, false))
            ->setQuantidadeErros((int) $this->totais['erros'])
            ->setQuantidadeEntradas((int) $this->totais['entradas'])
            ->setQuantidadeLiquidados((int) $this->totais['liquidados'])
            ->setQuantidadeBaixados((int) $this->totais['baixados'])
            ->setQuantidadeAlterados((int) $this->totais['alterados']);

        return true;
    }
}
