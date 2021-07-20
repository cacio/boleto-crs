<?php
namespace Eduardokum\LaravelBoleto\Boleto\Banco;

use Eduardokum\LaravelBoleto\Boleto\AbstractBoleto;
use Eduardokum\LaravelBoleto\CalculoDV;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Util;

class Unicred extends AbstractBoleto implements BoletoContract
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
       // $this->addCampoObrigatorio('byte', 'posto');
    }

    /**
     * Local de pagamento
     *
     * @var string
     */
    protected $localPagamento = 'Pagável preferencialmente nas cooperativas de crédito do Unicred';
    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_UNICRED;
    /**
     * Define as carteiras disponíveis para este banco
     *
     * @var array
     */
    protected $carteiras = ['11', '21', '31','41','51'];
    /**
     * Espécie do documento, coódigo para remessa
     *
     * @var string
     */
    protected $especiesCodigo = [
        'DMI' => 'A', // Duplicata Mercantil por Indicação
        'DM' => 'A', // Duplicata Mercantil por Indicação
        'DR' => 'B', // Duplicata Rural
        'NP' => 'C', // Nota Promissória
        'NR' => 'D', // Nota Promissória Rural
        'NS' => 'E', // Nota de Seguros
        'RC' => 'G', // Recibo
        'LC' => 'H', // Letra de Câmbio
        'ND' => 'I', // Nota de Débito
        'DSI' => 'J', // Duplicata de Serviço por Indicação
        'OS' => 'K', // Outros
    ];
    /**
     * Se possui registro o boleto (tipo = 1 com registro e 3 sem registro)
     *
     * @var bool
     */
    protected $registro = true;
    /**
     * Código do posto do cliente no banco.
     *
     * @var int
     */
    protected $posto;
    /**
     * Byte que compoe o nosso número.
     *
     * @var int
     */
    protected $byte = 2;
    /**
     * Define se possui ou não registro
     *
     * @param  bool $registro
     * @return $this
     */
    public function setComRegistro($registro)
    {
        $this->registro = $registro;
        return $this;
    }
    /**
     * Retorna se é com registro.
     *
     * @return bool
     */
    public function isComRegistro()
    {
        return $this->registro;
    }
    /**
     * Define o posto do cliente
     *
     * @param  int $posto
     * @return $this
     */
    public function setPosto($posto)
    {
        $this->posto = $posto;
        return $this;
    }
    /**
     * Retorna o posto do cliente
     *
     * @return int
     */
    public function getPosto()
    {
        return $this->posto;
    }

    /**
     * Define o byte
     *
     * @param  int $byte
     *
     * @return $this
     * @throws \Exception
     */
    public function setByte($byte)
    {
        if ($byte > 9) {
            throw new \Exception('O byte deve ser compreendido entre 1 e 9');
        }
        $this->byte = $byte;
        return $this;
    }
    /**
     * Retorna o byte
     *
     * @return int
     */
    public function getByte()
    {
        return $this->byte;
    }
    /**
     * Retorna o campo Agência/Beneficiário do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoBeneficiario()
    {
		$agencia = Util::numberFormatGeral($this->getAgencia(),4);
		$conta   = Util::numberFormatGeral($this->getConta(),10);
		$contadv = $this->getContaDv();
		
        return sprintf('%04s/%05s-%01s', $this->getAgencia(), $this->getConta(),$contadv);
    }
    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        		
		$numero  = Util::numberFormatGeral($this->getNumero(), 10);
		$dv      = Util::modulo11($numero);	
		$numero .= '-' . $dv;	
		
        return $numero;
    }
    /**
     * Método que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenças.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {
		//echo "{$this->getNossoNumero()}";
        return $this->getNossoNumero();
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
		//echo "{$this->getAgenciaDv()}\n";
		$agenciaDv = empty($this->getAgenciaDv()) ? 0 : $this->getAgenciaDv();
        $campoLivre = "";                
        $campoLivre .= Util::numberFormatGeral($this->getAgencia(), 4);        
        $campoLivre .= Util::numberFormatGeral($this->getConta().$agenciaDv, 10);
		$campoLivre .= Util::numberFormatGeral($this->getNossoNumero(), 11);        
        
        return $this->campoLivre .= $campoLivre;
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
            'codigoCliente' => null,
            'carteira' => substr($campoLivre, 1, 1),
            'nossoNumero' => substr($campoLivre, 2, 8),
            'nossoNumeroDv' => substr($campoLivre, 10, 1),
            'nossoNumeroFull' => substr($campoLivre, 2, 9),
            'agencia' => substr($campoLivre, 11, 4),
            'contaCorrente' => substr($campoLivre, 17, 5),
        ];
    }
}
