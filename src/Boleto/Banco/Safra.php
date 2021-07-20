<?php
namespace Eduardokum\LaravelBoleto\Boleto\Banco;

use Eduardokum\LaravelBoleto\Boleto\AbstractBoleto;
use Eduardokum\LaravelBoleto\CalculoDV;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto;
use Eduardokum\LaravelBoleto\Util;

class Safra  extends AbstractBoleto implements BoletoContract
{
    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = Boleto::COD_BANCO_SAFRA;
    /**
     * Define as carteiras disponíveis para este banco
     * '09' => Com registro | '06' => Sem Registro | '21' => Com Registro - Pagável somente no Bradesco | '22' => Sem Registro - Pagável somente no Bradesco | '25' => Sem Registro - Emissão na Internet | '26' => Com Registro - Emissão na Internet
     *
     * @var array
     */
    protected $carteiras = ['01','02', '09', '21', '26'];
    /**
     * Trata-se de código utilizado para identificar mensagens especificas ao cedente, sendo
     * que o mesmo consta no cadastro do Banco, quando não houver código cadastrado preencher
     * com zeros "000".
     *
     * @var int
     */
    protected $cip = '000';
	
	
	/**
     * Retorna o campo Agência/Beneficiário do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoBeneficiario()
    {
        $agencia = $this->getAgenciaDv() !== null ? $this->getAgencia() . '' . $this->getAgenciaDv() : $this->getAgencia();
        $codigoCliente = $this->getCodigoCliente();

        return $agencia . ' / ' . substr($codigoCliente,5,9);
    }
	
	protected $codigoCliente;
    /**
     * Seta o codigo do cliente.
     *
     * @param mixed $codigoCliente
     *
     * @return $this
     */
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
     * Variaveis adicionais.
     *
     * @var array
     */
    public $variaveis_adicionais = [
        'cip' => '000',
        'mostra_cip' => true,
    ];
    /**
     * Espécie do documento, coódigo para remessa
     *
     * @var string
     */
    protected $especiesCodigo = [
        'DM' => '01',
        'NP' => '02',
        'NS' => '03',
        'CS' => '04',
        'REC' => '05',
        'LC' => '10',
        'ND' => '11',
        'DS' => '12',
    ];
    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        return Util::numberFormatGeral($this->getNumero(), 11)
            . CalculoDV::safraNossoNumero($this->getCarteira(), $this->getNumero());
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
     * Método que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenças.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {				
        return Util::numberFormatGeral(substr(substr_replace($this->getNossoNumero(), '', -1, 1),3,11),9);
    }
    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     */
    protected function getCampoLivre()
    {
        if ($this->campoLivre) {
            return $this->campoLivre;
        }
		//echo substr($this->getCodigoCliente(),5,9)."<br/>";
		//echo Util::numberFormatGeral(substr($this->getNossoNumero(),3,8),9).'<br/>';
		$campoLivre  = '7';
		$campoLivre .= Util::numberFormatGeral($this->getAgencia(), 4);
		$campoLivre .= substr(Util::numberFormatGeral($this->getAgencia(), 4),-1,1);		
		$campoLivre .= substr($this->getCodigoCliente(),5,9);        
        $campoLivre .= Util::numberFormatGeral(substr($this->getNossoNumero(),3,8),9);
        $campoLivre .= '2';
		
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
            'contaCorrenteDv' => null,
            'agencia' => substr($campoLivre, 0, 4),
            'carteira' => substr($campoLivre, 4, 2),
            'nossoNumero' => substr($campoLivre, 6, 11),
            'nossoNumeroDv' => null,
            'nossoNumeroFull' => substr($campoLivre, 6, 11),
            'contaCorrente' => substr($campoLivre, 17, 7),
        ];
    }

    /**
     * Define o campo CIP do boleto
     *
     * @param  int $cip
     * @return Bradesco
     */
    public function setCip($cip)
    {
        $this->cip = $cip;
        $this->variaveis_adicionais['cip'] = $this->getCip();
        return $this;
    }

    /**
     * Retorna o campo CIP do boleto
     *
     * @return string
     */
    public function getCip()
    {
        return Util::numberFormatGeral($this->cip, 3);
    }
}
