<?php
namespace Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco;

use Carbon\Carbon;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\AbstractRemessa;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Remessa as RemessaContract;
use Eduardokum\LaravelBoleto\Util;

class Unicred extends AbstractRemessa implements RemessaContract
{
    const ESPECIE_DUPLICATA = 'A';
    const ESPECIE_DUPLICATA_RURAL = 'B';
    const ESPECIE_NOTA_PROMISSORIA = 'C';
    const ESPECIE_NOTA_PROMISSORIA_RURAL = 'D';
    const ESPECIE_NOTA_SEGURO = 'E';
    const ESPECIE_RECIBO = 'G';
    const ESPECIE_LETRA_CAMBIO = 'H';
    const ESPECIE_NOTA_DEBITOS = 'I';
    const ESPECIE_NOTA_SERVICOS = 'J';
    const ESPECIE_OUTROS = 'K';

    const OCORRENCIA_REMESSA = '01';
    const OCORRENCIA_BAIXA = '02';
    const OCORRENCIA_CONCESSAO_ABATIMENTO = '04';
    const OCORRENCIA_CANC_ABATIMENTO = '05';
    const OCORRENCIA_ALT_VENCIMENTO = '06';
    const OCORRENCIA_PROTESTAR = '09';
    const OCORRENCIA_SUSTAR_PROTESTO = '18';
    const OCORRENCIA_SUSTAR_PROTESTO_MANTER_CARTEIRA = '19';
    const OCORRENCIA_ALT_OUTROS_DADOS = '31';

    const INSTRUCAO_SEM = '0';
    const INSTRUCAO_PROTESTO = '2';

    public function __construct(array $params)
    {
        parent::__construct($params);
      
        $this->addCampoObrigatorio('idremessa');
    }

   

    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_UNICRED;

    /**
     * Define as carteiras disponíveis para cada banco
     *
     * @var array
     */
    protected $carteiras = ['11', '21', '31','41','51'];

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
     * @return string
     */
    public function getCodigoCliente()
    {
        return $this->codigoCliente;
    }
	
	 /**
     * Seta o codigo do cliente.
     *
     * @param mixed $codigoCliente
     *
     * @return daycoval
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }	
	
	protected $agenciadv;
	
	public function getAgenciaDv(){
		return $this->agenciadv;
	}
	
	public function setAgenciaDv($agenciadv){
		$this->agenciadv = $agenciadv;
		return $this;
	}
	
    protected function header()
    {
        $this->iniciaHeader();
		
        $this->add(1, 1, '0');
        $this->add(2, 2, '1');
        $this->add(3, 9, 'REMESSA');
        $this->add(10, 11, '01');
        $this->add(12, 26, Util::formatCnab('X', 'COBRANCA', 15));
        $this->add(27, 46, Util::formatCnab('9', $this->getCodigoCliente(), 20));
        $this->add(47, 76, Util::formatCnab('X', $this->getBeneficiario()->getNome(), 30));
        $this->add(77, 79, $this->getCodigoBanco());
        $this->add(80, 94, Util::formatCnab('X', 'UNICRED', 15));
        $this->add(95, 100, $this->getDataRemessa('dmy'));
        $this->add(101, 107, '');
        $this->add(108, 110, '0');
        $this->add(111, 117, Util::formatCnab('9', $this->getIdremessa(), 7));
        $this->add(118, 394, '');
        $this->add(395, 400, Util::formatCnab('9', 1, 6));

        return $this;
    }

    public function addBoleto(BoletoContract $boleto)
    {
        $this->boletos[] = $boleto;
        if (!$boleto->isComRegistro()) {
            return $this;
        }
		/*echo "<pre>";
		print_r($boleto);
		echo "</pre>";*/
        $this->iniciaDetalhe();
		//echo "{$boleto->getAgenciaDv()}\n";
        $this->add(1, 1, '1');
        $this->add(2, 6, Util::formatCnab('9', $this->getAgencia(), 5));
		$this->add(7, 7, Util::formatCnab('9', $boleto->getAgenciaDv(), 1));
		$this->add(8, 19, Util::formatCnab('9', $this->getConta(), 12));
		$this->add(20, 20, Util::formatCnab('9', $boleto->getContaDv(), 1));
		$this->add(21, 21, '0');		
        $this->add(22, 24, Util::formatCnab('9', $this->getCarteiraNumero(), 3));
		$this->add(25, 37, '0000000000000');//Util::formatCnab('9', $this->getCodigoCliente(), 13)
        $this->add(38, 62, '');		
        $this->add(63, 65, '136');
		$this->add(66, 67, '00');
		$this->add(68,92, '');		        
		$this->add(93, 93, '0');
		$this->add(94, 94, '2'); //‘1’ = Valor Fixo (R$) ‘2’ = Taxa (%) ‘3’ = Isento
		$this->add(95, 104, Util::formatCnab('9', $boleto->getMulta(), 10, 2));
		$this->add(105, 105, '4'); // ‘1’ = Valor Diário (R$)‘2’ = Taxa Mensal (%)‘3’= Valor Mensal (R$) *‘4’ = Taxa diária (%)‘5’ = Isento
		$this->add(106, 106, 'N');
		$this->add(107, 108, '');
		$this->add(109, 110, '01');
		$this->add(111, 120, Util::formatCnab('X', $boleto->getNumeroDocumento(), 10));        
        $this->add(121, 126, $boleto->getDataVencimento()->format('dmy'));		
        $this->add(127, 139, $boleto->getDesconto() > 0 ?  Util::formatCnab('9', $boleto->getValor() + $boleto->getDesconto(), 13, 2) : Util::formatCnab('9', $boleto->getValor(), 13, 2));		
        $this->add(140, 149, '0000000000');
		$this->add(150, 150, '0');		
        $this->add(151, 156, $boleto->getDataDocumento()->format('dmy'));		
        $this->add(157, 157, '0');		
		$this->add(158, 158, self::INSTRUCAO_SEM);		
        $this->add(159, 160, self::INSTRUCAO_SEM);
		
        if ($boleto->getDiasProtesto() > 0) {
            $this->add(158, 158, self::INSTRUCAO_PROTESTO);
            $this->add(159, 160, Util::formatCnab('9', $boleto->getDiasProtesto(), 2));
        }
		
        $this->add(161, 173, Util::formatCnab('9', $boleto->getJuros(), 13, 2));		
        $this->add(174, 179, $boleto->getDesconto() > 0 ? $boleto->getDataDesconto()->format('dmy') : '000000');		
        $this->add(180, 192, Util::formatCnab('9', $boleto->getDesconto(), 13, 2));		
        $this->add(193, 203, Util::formatCnab('9', str_replace('-','',$boleto->getNossoNumero()), 11));		        
		$this->add(204, 205, '00');
		$this->add(206, 218, Util::formatCnab('9', 0, 13, 2));		
	    $this->add(219, 220, strlen(Util::onlyNumbers($boleto->getPagador()->getDocumento())) == 14 ? '02' : '01');				
        $this->add(221, 234, Util::formatCnab('9L', $boleto->getPagador()->getDocumento(), 14));				
        $this->add(235, 274, Util::formatCnab('X', $boleto->getPagador()->getNome(), 40));				
        $this->add(275, 314, Util::formatCnab('X', $boleto->getPagador()->getEndereco(), 40));			
        $this->add(315, 326, Util::formatCnab('X', $boleto->getPagador()->getBairro(), 12));		
        $this->add(327, 334, Util::formatCnab('9L', $boleto->getPagador()->getCep(), 8));		
        $this->add(335, 354, Util::formatCnab('X', $boleto->getPagador()->getCidade(), 20));
		$this->add(355, 356, Util::formatCnab('X', $boleto->getPagador()->getUf(), 2));		       
        $this->add(357, 394, Util::formatCnab('X', $boleto->getSacadorAvalista() ? $boleto->getSacadorAvalista()->getNome() : '', 38));		
        $this->add(395, 400, Util::formatCnab('9', $this->iRegistros + 1, 6));

       /* if ($boleto->getByte() == 1) {
            $this->iniciaDetalhe();

            $this->add(1, 1, '2');
            $this->add(2, 12, '');
            $this->add(13, 21, Util::formatCnab('9', $boleto->getNossoNumero(), 9));
            $this->add(22, 101, Util::formatCnab('X', $boleto->getInstrucoes()[0], 80));
            $this->add(102, 181, Util::formatCnab('X', $boleto->getInstrucoes()[1], 80));
            $this->add(182, 261, Util::formatCnab('X', $boleto->getInstrucoes()[2], 80));
            $this->add(262, 341, Util::formatCnab('X', $boleto->getInstrucoes()[3], 80));
            $this->add(342, 351, Util::formatCnab('9', $boleto->getNumeroDocumento(), 10));
            $this->add(352, 394, '');
            $this->add(395, 400, Util::formatCnab('9', $this->iRegistros + 1, 6));
        }*/

        return $this;
    }

    protected function trailer()
    {
        $this->iniciaTrailer();

        $this->add(1, 1, '9');
        $this->add(2, 394, '');        
        $this->add(395, 400, Util::formatCnab('9', $this->getCount(), 6));

        return $this;
    }
}
