<?php
namespace Eduardokum\LaravelBoleto\Boleto\Banco;

use Eduardokum\LaravelBoleto\Boleto\AbstractBoleto;
use Eduardokum\LaravelBoleto\CalculoDV;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Util;

class Daycoval extends AbstractBoleto implements BoletoContract
{

    /**
     * Local de pagamento
     *
     * @var string
     */
    protected $localPagamento = 'Até o vencimento, preferencialmente no Daycoval';

    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_DAYCOVAL;
    /**
     * Variáveis adicionais.
     *
     * @var array
     */
    public $variaveis_adicionais = [
        'carteira_nome' => '',
    ];
    /**
     * Define as carteiras disponíveis para este banco
     *
     * @var array
     */
    protected $carteiras = ['112', '115', '188', '109', '121', '180', '110', '111'];
    /**
     * Espécie do documento, coódigo para remessa
     *
     * @var string
     */
    protected $especiesCodigo = [
        'DM' => '01',
        'NP' => '02',
        'NS' => '03',
        'ME' => '04',
        'REC' => '05',
        'CT' => '06',
        'CS' => '07',
        'DS' => '08',
        'LC' => '09',
        'ND' => '13',
        'CDA' => '15',
        'EC' => '16',
        'CPS' => '17',
    ];
	
	 /**
     * Codigo do cliente junto ao banco.
     *
     * @var string
     */
    protected $codigoCliente;
    /**
     * Seta o codigo do cliente.
     *
     * @param mixed $codigoCliente
     *
     * @return $this
     */	 
	
	protected $operacao;	
	
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }
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
     * Define o posto do cliente
     *
     * @param  int $posto
     * @return $this
     */
    public function setOperacao($operacao)
    {
        $this->operacao = $operacao;
        return $this;
    }
    /**
     * Retorna o posto do cliente
     *
     * @return int
     */
    public function getOperacao()
    {
        return $this->operacao;
    }
	
	
    /**
     * Seta dias para baixa automática
     *
     * @param int $baixaAutomatica
     *
     * @return $this
     * @throws \Exception
     */
    public function setDiasBaixaAutomatica($baixaAutomatica)
    {
        if ($this->getDiasProtesto() > 0) {
            throw new \Exception('Você deve usar dias de protesto ou dias de baixa, nunca os 2');
        }
        $baixaAutomatica = (int) $baixaAutomatica;
        $this->diasBaixaAutomatica = $baixaAutomatica > 0 ? $baixaAutomatica : 0;
        return $this;
    }

    /**
     * Gera o Nosso Número.
     *
     * @return string
     * @throws \Exception
     */
    protected function gerarNossoNumero()
    {
        $numero_boleto = Util::numberFormatGeral($this->getNumero(), 8);
        $carteira = Util::numberFormatGeral($this->getCarteira(), 3);
        $agencia = Util::numberFormatGeral($this->getAgencia(), 4);
        //$conta = Util::numberFormatGeral($this->getConta(), 5);
        $dv = CalculoDV::daycovalNossoNumero($agencia, $carteira, $numero_boleto);
        return $numero_boleto . $dv;
    }
    /**
     * Método que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenças.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {					
        return $this->getCarteira() . '/' . substr_replace($this->getNossoNumero(), '-', -1, 0);
    }
    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \Exception
     */
      protected function getCampoLivre()
    {
        if ($this->campoLivre) {
            return $this->campoLivre;
        }
		//echo Util::numberFormatGeral($this->getNossoNumero(), 11);
		$campoLivre  = Util::numberFormatGeral($this->getAgencia(), 4);
        $campoLivre .= Util::numberFormatGeral($this->getCarteira(), 3);
		$campoLivre .= Util::numberFormatGeral($this->getOperacao(), 7);
        $campoLivre .= Util::numberFormatGeral($this->getNossoNumero(), 11);        
       // $campoLivre .= CalculoDV::daycovalContaCorrente($this->getAgencia(), $this->getConta());
        //$campoLivre .= '000';

        return $this->campoLivre = $campoLivre;
    }
	
	
	
    /**
     * Método onde qualquer boleto deve extender para gerar o código da posição de 20 a 44
     *
     * @param $campoLivre
     *
     * @return array
     */
    public static function parseCampoLivre($campoLivre) {
		
        return [
            'convenio' => null,
            'agenciaDv' => null,
            'codigoCliente' => null,
            'carteira' => substr($campoLivre, 0, 3),
            'nossoNumero' => substr($campoLivre, 3, 8),
            'nossoNumeroDv' => substr($campoLivre, 11, 1),
            'nossoNumeroFull' => substr($campoLivre, 3, 9),
            'agencia' => substr($campoLivre, 12, 4),
            'contaCorrente' => substr($campoLivre, 16, 5),
            'contaCorrenteDv' => substr($campoLivre, 21, 1),
        ];
    }
}
