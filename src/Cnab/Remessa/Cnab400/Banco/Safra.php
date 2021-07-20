<?php
namespace Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco;

use DeepCopyTest\B;
use Eduardokum\LaravelBoleto\CalculoDV;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\AbstractRemessa;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Remessa as RemessaContract;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Util;

class Safra extends AbstractRemessa implements RemessaContract
{
    const ESPECIE_DUPLICATA = '01';
    const ESPECIE_NOTA_PROMISSORIA = '02';
    const ESPECIE_NOTA_SEGURO = '03';
    const ESPECIE_COBRANCA_SERIADA = '04';
    const ESPECIE_RECIBO = '05';
    const ESPECIE_LETRAS_CAMBIO = '10';
    const ESPECIE_NOTA_DEBITO = '11';
    const ESPECIE_DUPLICATA_SERVICO = '12';
    const ESPECIE_OUTROS = '99';

    const OCORRENCIA_REMESSA = '01';
    const OCORRENCIA_PEDIDO_BAIXA = '02';
    const OCORRENCIA_CONCESSAO_ABATIMENTO = '04';
    const OCORRENCIA_CANC_ABATIMENTO_CONCEDIDO = '05';
    const OCORRENCIA_ALT_VENCIMENTO = '06';
    const OCORRENCIA_ALT_CONTROLE_PARTICIPANTE = '07';
    const OCORRENCIA_ALT_SEU_NUMERO = '08';
    const OCORRENCIA_PEDIDO_PROTESTO = '09';
    const OCORRENCIA_SUSTAR_PROTESTO_BAIXAR_TITULO = '18';
    const OCORRENCIA_SUSTAR_PROTESTO_MANTER_TITULO = '19';
    const OCORRENCIA_TRANS_CESSAO_CREDITO_ID10 = '22';
    const OCORRENCIA_TRANS_CARTEIRAS = '23';
    const OCORRENCIA_DEVOLUCAO_TRANS_CARTEIRAS = '24';
    const OCORRENCIA_ALT_OUTROS_DADOS = '31';
    const OCORRENCIA_DESAGENDAMENTO_DEBITO_AUT = '35';
    const OCORRENCIA_ACERTO_RATEIO_CREDITO = '68';
    const OCORRENCIA_CANC_RATEIO_CREDITO = '69';
	

    const INSTRUCAO_SEM = '00';
    const INSTRUCAO_PROTESTAR_FAMILIAR_XX = '05';
    const INSTRUCAO_PROTESTAR_XX = '06';
    const INSTRUCAO_NAO_COBRAR_JUROS = '08';
    const INSTRUCAO_NAO_RECEBER_APOS_VENC = '09';
    const INSTRUCAO_MULTA_10_APOS_VENC_4 = '10';
    const INSTRUCAO_NAO_RECEBER_APOS_VENC_8 = '11';
    const INSTRUCAO_COBRAR_ENCAR_APOS_5 = '12';
    const INSTRUCAO_COBRAR_ENCAR_APOS_10 = '13';
    const INSTRUCAO_COBRAR_ENCAR_APOS_15 = '14';
    const INSTRUCAO_CENCEDER_DESC_APOS_VENC = '15';
    const INSTRUCAO_DEVOLVER_XX = '18';
	const INSTRUCAO_VALOR_SOMA_MORA = '16';
	const INSTRUCAO_PROTESTAR_VENC_XX = '16';
	
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->addCampoObrigatorio('idremessa');
    }


    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_SAFRA;

    /**
     * Define as carteiras disponíveis para cada banco
     *
     * @var array
     */

    protected $carteiras = ['01','02', '09', '28'];

    /**
     * Caracter de fim de linha
     *
     * @var string
     */
    protected $fimLinha = "\r\n";

    /**
     * Caracter de fim de arquivo
     *
     * @var null
     */
    protected $fimArquivo = "\r\n";

    /**
     * Codigo do cliente junto ao banco.
     *
     * @var string
     */
    protected $codigoCliente;

    /**
     * Retorna o codigo do cliente.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCodigoCliente()
    {
        if (empty($this->codigoCliente)) {
            $this->codigoCliente = Util::formatCnab('9', $this->getCarteiraNumero(), 4) .
            Util::formatCnab('9', $this->getAgencia(), 5) .
            Util::formatCnab('9', $this->getConta(), 7) .
            Util::formatCnab('9', $this->getContaDv() ?: CalculoDV::safraContaCorrente($this->getConta()), 1);
        }

        return $this->codigoCliente;
    }

    /**
     * Seta o codigo do cliente.
     *
     * @param mixed $codigoCliente
     *
     * @return Bradesco
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }

	/**
     * Valor total dos titulos.
     *
     * @var float
     */
    private $total = 0;

    /**
     * @return $this
     * @throws \Exception
     */
    protected function header()
    {
        $this->iniciaHeader();

        $this->add(1, 1, '0');
        $this->add(2, 2, '1');
        $this->add(3, 9, 'REMESSA');
        $this->add(10, 11, '01');
        $this->add(12, 19, Util::formatCnab('X', 'COBRANCA', 8));
		$this->add(20, 26, '');		
        $this->add(27, 40, Util::formatCnab('9', $this->getCodigoCliente(), 14));
		$this->add(41, 46, '');		
        $this->add(47, 76, Util::formatCnab('X', $this->getBeneficiario()->getNome(), 30));		
        $this->add(77, 79, $this->getCodigoBanco());		
        $this->add(80, 90, Util::formatCnab('X', 'BANCO SAFRA', 11));
		$this->add(91, 94, '');		
        $this->add(95, 100, $this->getDataRemessa('dmy'));		
        $this->add(101, 391, '');		        
        $this->add(392, 394, Util::formatCnab('9', $this->getIdremessa(), 3));        
        $this->add(395, 400, Util::formatCnab('9', 1, 6));

        return $this;
    }

    /**
     * @param BoletoContract $boleto
     *
     * @return $this
     * @throws \Exception
     */
    public function addBoleto(BoletoContract $boleto)
    {
        $this->boletos[] = $boleto;
        $this->iniciaDetalhe();
		
        $this->total += $boleto->getDesconto() > 0 ? $boleto->getValor() + $boleto->getDesconto() : $boleto->getValor();
		//echo Util::formatCnab('9', substr($boleto->getNossoNumero(), 3, 8), 9);
        $this->add(1, 1, '1');
        $this->add(2, 3, strlen(Util::onlyNumbers($this->getBeneficiario()->getDocumento())) == 14 ? '02' : '01');
        $this->add(4, 17, Util::formatCnab('9', Util::onlyNumbers($this->getBeneficiario()->getDocumento()), 14));
        $this->add(18, 31, Util::formatCnab('9', $this->getCodigoCliente(), 14));
        $this->add(32, 37, '');
		$this->add(38, 62, Util::formatCnab('X', $boleto->getNumeroControle(), 25)); // numero de controle
		$this->add(63, 71, Util::formatCnab('9', substr($boleto->getNossoNumero(), 3, 8), 9));
		$this->add(72, 101, '');
		$this->add(102, 102, '0');
		$this->add(103, 104, '00');
		$this->add(105, 105, '');
		$this->add(106, 107, '00');
		$this->add(108, 108, Util::formatCnab('9', $this->getCarteiraNumero(), 1));
		$this->add(109, 110, self::OCORRENCIA_REMESSA); // REGISTRO
        if ($boleto->getStatus() == $boleto::STATUS_BAIXA) {
            $this->add(109, 110, self::OCORRENCIA_PEDIDO_BAIXA); // BAIXA
        }
        if ($boleto->getStatus() == $boleto::STATUS_ALTERACAO) {
            $this->add(109, 110, self::OCORRENCIA_ALT_VENCIMENTO); // ALTERAR VENCIMENTO
        }
		
		$this->add(111, 120, Util::formatCnab('X', $boleto->getNumeroDocumento(), 10));
		$this->add(121, 126, $boleto->getDataVencimento()->format('dmy'));
		$this->add(127, 139, $boleto->getDesconto() > 0 ?  Util::formatCnab('9', $boleto->getValor() + $boleto->getDesconto(), 13, 2) : Util::formatCnab('9', $boleto->getValor(), 13, 2));
		$this->add(140, 142, $this->getCodigoBanco());
		$this->add(143, 147, '00000');
		$this->add(148, 149, $boleto->getEspecieDocCodigo());
		$this->add(150, 150, $boleto->getAceite());
		$this->add(151, 156, $boleto->getDataDocumento()->format('dmy'));
		
		$this->add(157, 158, self::INSTRUCAO_SEM);
		 if ($boleto->getDiasProtesto() > 0) {
            $this->add(157, 158, self::INSTRUCAO_PROTESTAR_VENC_XX);
        } elseif ($boleto->getDiasBaixaAutomatica() > 0) {
            $this->add(157, 158, self::INSTRUCAO_DEVOL_VENC_XX);
        }
		//echo Util::formatCnab('9', $boleto->getMulta(), 13, 2);
		$this->add(159, 160, self::INSTRUCAO_VALOR_SOMA_MORA);
		$this->add(161, 173, Util::formatCnab('9', $boleto->getMoraDia(), 13, 2));
		$this->add(174, 179, $boleto->getDesconto() > 0 ? $boleto->getDataDesconto()->format('dmy') : '000000');
		$this->add(180, 192, Util::formatCnab('9', $boleto->getDesconto(), 13, 2));
		$this->add(193, 205, Util::formatCnab('9', 0, 13, 2));
		$this->add(206, 211, date('dmy',strtotime('+1 days', strtotime($boleto->getDataVencimento()))));
		$this->add(212, 215, Util::formatCnab('9', $boleto->getMulta(), 4, 2));
		$this->add(216, 218, '000');
		$this->add(219, 220, strlen(Util::onlyNumbers($boleto->getPagador()->getDocumento())) == 14 ? '02' : '01');
		$this->add(221, 234, Util::formatCnab('9', Util::onlyNumbers($boleto->getPagador()->getDocumento()), 14));
		$this->add(235, 274, Util::formatCnab('X', $boleto->getPagador()->getNome(), 40));
		$this->add(275, 314, Util::formatCnab('X', $boleto->getPagador()->getEndereco(), 40));
		$this->add(315, 324, Util::formatCnab('X', $boleto->getPagador()->getBairro(), 10));
		$this->add(325, 326, '');
		$this->add(327, 334, Util::formatCnab('9', Util::onlyNumbers($boleto->getPagador()->getCep()), 8));
		$this->add(335, 349, Util::formatCnab('X', $boleto->getPagador()->getCidade(), 15));
		$this->add(350, 351, Util::formatCnab('X', $boleto->getPagador()->getUf(), 2));
		$this->add(352, 381, Util::formatCnab('X', $boleto->getSacadorAvalista() ? $boleto->getSacadorAvalista()->getNome() : '', 30));
		$this->add(382, 388, '');
		$this->add(389, 391, $this->getCodigoBanco());
		$this->add(392, 394, Util::formatCnab('9', $this->getIdremessa(), 3));
		$this->add(395, 400, Util::formatCnab('9', $this->iRegistros + 1, 6));
		/****/
		
		
        /*$this->add(24, 28, Util::formatCnab('9', $this->getConta(), 5));
        $this->add(29, 29, $this->getContaDv() ?: CalculoDV::itauContaCorrente($this->getAgencia(), $this->getContaDv()));
        $this->add(30, 33, '');
        $this->add(34, 37, '0000');
        $this->add(38, 62, Util::formatCnab('X', $boleto->getNumeroControle(), 25)); // numero de controle
        
        $this->add(71, 83, Util::formatCnab('9', '0', 13, 2));
        $this->add(84, 86, Util::formatCnab('9', $this->getCarteiraNumero(), 3));
        $this->add(87, 107, '');
        $this->add(108, 108, 'I');
                 
        $this->add(386, 391, $boleto->getJurosApos() === false ? '000000' : $boleto->getDataVencimento()->copy()->addDays($boleto->getJurosApos())->format('dmy'));
        $this->add(392, 393, Util::formatCnab('9', $boleto->getDiasProtesto($boleto->getDiasBaixaAutomatica()), 2));
        $this->add(394, 394, '');*/
        

        // Verifica multa
        /*if ($boleto->getMulta() > 0) {
            // Inicia uma nova linha de detalhe e marca com a atual de edição
            $this->iniciaDetalhe();
            // Campo adicional para a multa
            $this->add(1, 1, 2); // Adicional Multa
            $this->add(2, 2, 2); // Cód 2 = Informa Valor em percentual
            $this->add(3, 10, $boleto->getDataVencimento()->format('dmY')); // Data da multa
            $this->add(11, 23, Util::formatCnab('9', Util::nFloat($boleto->getMulta(), 2), 13));
            $this->add(24, 394, '');
            $this->add(395, 400, Util::formatCnab('9', $this->iRegistros + 1, 6));
        }*/

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function trailer()
    {
        $this->iniciaTrailer();
		//echo Util::formatCnab('9', $this->total, 13, 2);
        $this->add(1, 1, '9');
        $this->add(2, 368, Util::formatCnab('X', '', 367));
        $this->add(369, 376, Util::formatCnab('9', $this->iRegistros, 8));
		$this->add(377, 391, Util::formatCnab('9', $this->total, 15, 2));
		$this->add(392, 394, Util::formatCnab('9', $this->getIdremessa(), 3));
		$this->add(395, 400, Util::formatCnab('9', $this->getCount(), 6));
		
        return $this;
    }
}
