<?php
require 'autoload.php';
$beneficiario = new \Eduardokum\LaravelBoleto\Pessoa(
    [
        'nome'      => 'COMERCIAL DE CARNES AB & E LTDA',
        'endereco'  => 'RUA 25 DE JULHO, 2351',
        'cep'       => '93995-000',
        'uf'        => 'RS',
        'cidade'    => 'SANTA MARIA DO HERVAL',
        'documento' => '08.035.788/0001-83',
    ]
);

$pagador = new \Eduardokum\LaravelBoleto\Pessoa(
    [
        'nome'      => 'COMERCIAL NOVO ALIMENTOS LTDA',
        'endereco'  => 'AV SAO PEDRO, 177',
        'bairro'    => 'SAO GERALDO',
        'cep'       => '90230-122',
        'uf'        => 'RS',
        'cidade'    => 'PORTO ALEGRE',
        'documento' => '06.980.538/0001-96',
    ]
);

$boleto = new Eduardokum\LaravelBoleto\Boleto\Banco\Banrisul(
    [
        'logo'                   => realpath(__DIR__ . '/../logos/') . DIRECTORY_SEPARATOR . '041.png',
        'dataVencimento'         => new \Carbon\Carbon('2018-06-09'),
		'datadocumento'          => new \Carbon\Carbon('2018-05-10'),
		'valor'                  => 1723.80,
		'multa'                  => false,
		'juros'                  => false,
		'numero'                 => 52307,
		'numeroDocumento'        => 52307,
		'pagador'                => $pagador,
		'beneficiario'           => $beneficiario,
		'carteira'               => 1,
		'agencia'                => 0549,
		'conta'                  => 8583600,
		'contaDv'				 => 31,
		'agenciaDv'              => 68,  
		'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
		'instrucoes'             => ['- Intruções', '- Após o Vencimento Cobrar Multa de R$65,36 e juros de R$10,78 Dia', '- Protestar 5 dias após o vencimento'],
		'aceite'                 => 'S',
		'especieDoc'             => 'DM',
		'codigoCliente'          => '0549 858360031',
    ]
);

$pdf = new Eduardokum\LaravelBoleto\Boleto\Render\Pdf();
$pdf->addBoleto($boleto);
$pdf->gerarBoleto($pdf::OUTPUT_SAVE, __DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . 'banrisul.pdf');
