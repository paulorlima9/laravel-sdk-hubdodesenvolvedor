<?php

namespace PauloRLima\HubDoDesenvolvedor\Tests\Feature;

use Exception;
use PauloRLima\HubDoDesenvolvedor\Tests\TestCase;
use PauloRLima\HubDoDesenvolvedor\HubDoDesenvolvedor;

class HubDoDesenvolvedorTest extends TestCase
{
    protected $hubDevApiClient;

    protected function setUp(): void
    {
        parent::setUp();

        // Obtenha o token da configuração
        $token = config('hubdodesenvolvedor.token');

        // Inicialize o cliente da API
        $this->hubDevApiClient = new HubDoDesenvolvedor($token);
    }

    /** @test */
    public function it_can_get_cnpj_data()
    {
        $cnpj = '00000000000191'; // CNPJ da Receita Federal do Brasil (público e utilizado em exemplos)

        try {
            $result = $this->hubDevApiClient->getCNPJ($cnpj);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
            $this->assertArrayHasKey('result', $result);
            $this->assertEquals($cnpj, preg_replace('/[^0-9]/', '', $result['result']['numero_de_inscricao']));
        } catch (Exception $e) {
            $this->fail('Exceção lançada durante getCNPJ: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_can_get_cnpj_modalidade2_data()
    {
        $cnpj = '00000000000191';

        try {
            $result = $this->hubDevApiClient->getCNPJModalidade2($cnpj);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
        } catch (Exception $e) {
            $this->fail('Exceção lançada durante getCNPJModalidade2: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_can_get_cnpj_last_update()
    {
        $cnpj = '00000000000191';

        try {
            $result = $this->hubDevApiClient->getCNPJLastUpdate($cnpj);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
            $this->assertArrayHasKey('result', $result);
            $this->assertArrayHasKey('last_update', $result['result']);
            $this->assertNotEmpty($result['result']['last_update']);
        } catch (Exception $e) {
            $this->fail('Exceção lançada durante getCNPJLastUpdate: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_can_get_cnpj_inscricoes_estaduais()
    {
        $cnpj = '00000000000191';

        try {
            $result = $this->hubDevApiClient->getCNPJInscricoesEstaduais($cnpj);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
            // Dependendo da resposta, você pode verificar os dados específicos
        } catch (Exception $e) {
            $this->fail('Exceção lançada durante getCNPJInscricoesEstaduais: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_can_get_simples_data()
    {
        $cnpj = '00000000000191';

        try {
            $result = $this->hubDevApiClient->getSimples($cnpj);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
            // Verifique os dados retornados conforme necessário
        } catch (Exception $e) {
            $this->fail('Exceção lançada durante getSimples: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_can_get_simples_modalidade2_data()
    {
        $cnpj = '00000000000191';

        try {
            $result = $this->hubDevApiClient->getSimplesModalidade2($cnpj);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
        } catch (Exception $e) {
            $this->fail('Exceção lançada durante getSimplesModalidade2: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_can_get_simples_last_update()
    {
        $cnpj = '00000000000191';

        try {
            $result = $this->hubDevApiClient->getSimplesLastUpdate($cnpj);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
        } catch (Exception $e) {
            $this->fail('Exceção lançada durante getSimplesLastUpdate: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_can_get_cep_data()
    {
        $cep = '01001000'; // CEP do Pátio do Colégio, São Paulo/SP

        try {
            $result = $this->hubDevApiClient->getCEP($cep);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
            $this->assertEquals('01001-000', $result['result']['cep']);
            $this->assertEquals('São Paulo', $result['result']['localidade']);
        } catch (Exception $e) {
            $this->fail('Exceção lançada durante getCEP: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_can_get_cpf_data()
    {
        $cpf = '12345678909'; // CPF de teste (não pertence a uma pessoa real)
        $dataNascimento = '01/01/1990';

        try {
            $result = $this->hubDevApiClient->getCPF($cpf, $dataNascimento);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
            // Verifique os dados retornados conforme necessário
        } catch (Exception $e) {
            // Pode ser que o CPF não exista na base, então verificamos o retorno
            if ($e->getMessage() === 'CPF Inválido.') {
                $this->markTestSkipped('CPF inválido ou não encontrado na base de dados.');
            } else {
                $this->fail('Exceção lançada durante getCPF: ' . $e->getMessage());
            }
        }
    }

    /** @test */
    public function it_can_get_nome_data_nascimento_cpf()
    {
        $cpf = '12345678909'; // CPF de teste

        try {
            $result = $this->hubDevApiClient->getNomeDataNascimentoCPF($cpf);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
            // Verifique os dados retornados conforme necessário
        } catch (Exception $e) {
            if ($e->getMessage() === 'CPF Inválido.') {
                $this->markTestSkipped('CPF inválido ou não encontrado na base de dados.');
            } else {
                $this->fail('Exceção lançada durante getNomeDataNascimentoCPF: ' . $e->getMessage());
            }
        }
    }

    /** @test */
    public function it_can_get_correios_frete()
    {
        $params = [
            'servico' => '40010', // SEDEX
            'cepOrigem' => '01001000',
            'cepDestino' => '20040000',
            'altura' => '2',
            'largura' => '11',
            'peso' => '0.5',
            'comprimento' => '16',
            'formato' => '1',
            'avisoRecebimento' => 'N',
            'maoPropria' => 'N',
            // 'valorDeclarado' => '0',
        ];

        try {
            $result = $this->hubDevApiClient->getCorreiosFrete($params);

            $this->assertIsArray($result);
            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
            $this->assertArrayHasKey('result', $result);
            $this->assertArrayHasKey('valor_total', $result['result']);
            $this->assertNotEmpty($result['result']['valor_total']);
        } catch (Exception $e) {
            echo 'Mensagem de erro: ' . $e->getMessage();
            $this->fail('Exceção lançada durante getCorreiosFrete: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_can_get_ibge_data()
    {
        $codigoCidade = '3550308'; // Código IBGE de São Paulo/SP

        try {
            $result = $this->hubDevApiClient->getIBGE($codigoCidade);

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
            $this->assertEquals('São Paulo', $result['result']['nome']);
            $this->assertEquals('São Paulo', $result['result']['estado']);
        } catch (Exception $e) {
            $this->fail('Exceção lançada durante getIBGE: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_can_get_saldo()
    {
        try {
            $result = $this->hubDevApiClient->getSaldo();

            $this->assertTrue($result['status']);
            $this->assertEquals('OK', $result['return']);
            $this->assertArrayHasKey('result', $result);
            // Verifique os dados de saldo conforme necessário
        } catch (Exception $e) {
            $this->fail('Exceção lançada durante getSaldo: ' . $e->getMessage());
        }
    }

    /** @test */
    public function it_throws_exception_for_invalid_cnpj()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('CNPJ inválido.');

        $cnpj = '123'; // CNPJ inválido
        $this->hubDevApiClient->getCNPJ($cnpj);
    }

    /** @test */
    public function it_throws_exception_for_invalid_cpf()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('CPF inválido.');

        $cpf = '123'; // CPF inválido
        $dataNascimento = '01/01/1990';
        $this->hubDevApiClient->getCPF($cpf, $dataNascimento);
    }

    /** @test */
    public function it_throws_exception_for_invalid_cep()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('CEP inválido.');

        $cep = '123'; // CEP inválido
        $this->hubDevApiClient->getCEP($cep);
    }

    /** @test */
    public function it_throws_exception_for_invalid_date()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Data de nascimento inválida.');

        $cpf = '12345678909';
        $dataNascimento = '31/02/1990'; // Data inválida
        $this->hubDevApiClient->getCPF($cpf, $dataNascimento);
    }

    /** @test */
    public function it_throws_exception_for_invalid_ibge_code()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Código da cidade inválido.');

        $codigoCidade = 'ABC'; // Código inválido
        $this->hubDevApiClient->getIBGE($codigoCidade);
    }

    /** @test */
    public function it_throws_exception_for_missing_correios_params()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('O parâmetro servico é obrigatório.');

        $params = [
            // 'servico' => '40010', // Parâmetro ausente
            'cepOrigem' => '01001-000',
            'cepDestino' => '20040-000',
            'altura' => '20',
            'largura' => '20',
            'peso' => '1',
            'comprimento' => '20',
            'formato' => '1',
            'tipoServico' => '1',
        ];

        $this->hubDevApiClient->getCorreiosFrete($params);
    }
}
