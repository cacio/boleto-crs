<?php
namespace Eduardokum\LaravelBoleto;

use Eduardokum\LaravelBoleto\Contracts\NotaFiscal as NotaFiscalContract;

class NotaFiscal implements NotaFiscalContract
{
           
    /**
     * @var string
     */
    protected $documento;
	protected $valornota;
	protected $dataemissao;
	protected $chaveacesso;
    /**
     * @var boolean
     */
    protected $dda = false;

    /**
     * Cria a pessoa passando os parametros.
     *e
     * @param $nome
     * @param $documento
     * @param null      $endereco
     * @param null      $cep
     * @param null      $cidade
     * @param null      $uf
     *
     * @return Pessoa
     */
    public static function create($documento, $valornota = null, $dataemissao = null, $chaveacesso = null)
    {
        return new static([            
            'documento' => $documento,
			'valornota' => $valornota,
			'dataemissao' => $dataemissao,
			'chaveacesso' => $chaveacesso,
        ]);
    }

    /**
     * Construtor
     *
     * @param array $params
     */
    public function __construct($params = [])
    {
        Util::fillClass($this, $params);
    }
    /**
     * Define o VALOR NOTA
     *
     * @param string $valornota
     */
    public function setValorNota($vlnota)
    {
        $this->valornota = $vlnota;
    }
    /**
     * Retorna o VALOR NOTA
     *
     * @return string
     */
    public function getValorNota()
    {
        return $this->valornota;
    }
    /**
     * Define a DATA EMISSÃO
     *
     * @param string $dataemissao
     */
    public function setDataEmissao($dtemiss)
    {
        $this->dataemissao = $dtemiss;
    }
    /**
     * Retorna a DATA EMISSÃO
     *
     * @return string
     */
    public function getDataEmissao()
    {
        return $this->dataemissao;
    }

    /**
     * Define o documento NOTA
     *
     * @param string $documento
     *
     * @throws \Exception
     */
    public function setDocumento($documento)
    {        
        $this->documento = $documento;
    }
    /**
     * Retorna o documento NOTA
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }
    
	public function setChaveAcesso($ch)
    {        
        $this->chaveacesso = $ch;
    }
	public function getChaveAcesso()
    {
        return $this->chaveacesso;
    }
     
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'documento' => $this->getDocumento(),
            'valornota' => $this->getValorNota(),
            'dataemissao' => $this->getDataEmissao(),
			'chaveacesso'=>$this->getChaveAcesso(),
        ];
    }
}
