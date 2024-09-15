# hubdodesenvolvedor

[![Última Versão no Packagist](https://img.shields.io/packagist/v/paulorlima9/laravel-sdk-hubdodesenvolvedor.svg?style=flat-square)](https://packagist.org/packages/paulorlima9/laravel-sdk-hubdodesenvolvedor)
[![Status dos Testes no GitHub](https://img.shields.io/github/actions/workflow/status/paulorlima9/laravel-sdk-hubdodesenvolvedor/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/paulorlima9/laravel-sdk-hubdodesenvolvedor/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Status do Código no GitHub](https://img.shields.io/github/actions/workflow/status/paulorlima9/laravel-sdk-hubdodesenvolvedor/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/paulorlima9/laravel-sdk-hubdodesenvolvedor/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total de Downloads](https://img.shields.io/packagist/dt/paulorlima9/laravel-sdk-hubdodesenvolvedor.svg?style=flat-square)](https://packagist.org/packages/paulorlima9/laravel-sdk-hubdodesenvolvedor)

Pacote Laravel para integração com a API do [Hub do Desenvolvedor](https://www.hubdodesenvolvedor.com.br/), permitindo consultas de CPF, CNPJ, CEP, informações dos Correios, e outras funcionalidades de forma simples e eficiente. Este pacote facilita a interação com a API, incluindo validações prévias para evitar chamadas desnecessárias.

## Instalação

Você pode instalar o pacote via Composer:

```bash
composer require paulorlima9/laravel-sdk-hubdodesenvolvedor
```

### Publicação da Configuração

Para publicar o arquivo de configuração, execute:

```bash
php artisan vendor:publish --provider="PauloRLima\HubDoDesenvolvedor\HubDoDesenvolvedorServiceProvider" --tag="hubdodesenvolvedor-config"
```

Isso irá criar o arquivo `config/hubdodesenvolvedor.php`, onde você pode definir o token da API e outras configurações.

### Configuração do Token

No arquivo `.env` da sua aplicação Laravel, adicione o seguinte:

```env
HUBDODESENVOLVEDOR_TOKEN=seu_token_aqui
HUBDODESENVOLVEDOR_TIMEOUT=600
```

Substitua `seu_token_aqui` pelo token fornecido pelo Hub do Desenvolvedor.

## Uso

### Utilizando a Facade

O pacote disponibiliza uma Facade para facilitar o uso. Certifique-se de que a Facade `HubDoDesenvolvedor` está registrada (deve estar automaticamente se você usa o Laravel 5.5 ou superior).

#### Exemplo: Consulta de CNPJ

```php
use PauloRLima\HubDoDesenvolvedor\Facades\HubDoDesenvolvedor;

class EmpresaController extends Controller
{
    public function consultarCNPJ($cnpj)
    {
        try {
            $resultado = HubDoDesenvolvedor::getCNPJ($cnpj);

            // Faça algo com o resultado
            return response()->json($resultado);
        } catch (\Exception $e) {
            // Trate erros
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

### Injetando Dependência

Você também pode injetar a classe `HubDevApiClient` diretamente no seu controlador ou serviço:

```php
use PauloRLima\HubDoDesenvolvedor\HubDevApiClient;

class EmpresaController extends Controller
{
    protected $hubDevApi;

    public function __construct(HubDevApiClient $hubDevApi)
    {
        $this->hubDevApi = $hubDevApi;
    }

    public function consultarCNPJ($cnpj)
    {
        try {
            $resultado = $this->hubDevApi->getCNPJ($cnpj);

            // Faça algo com o resultado
            return response()->json($resultado);
        } catch (\Exception $e) {
            // Trate erros
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

### Exemplos de Uso

#### Consulta de CNPJ

##### Métodos Disponíveis:

- **getCNPJ($cnpj, $tipoRetorno = 'json')**: Consulta de CNPJ (modalidade 1).
- **getCNPJModalidade2($cnpj, $tipoRetorno = 'json')**: Consulta de CNPJ (modalidade 2 - ignora cache).
- **getCNPJLastUpdate($cnpj, $tipoRetorno = 'json')**: Consulta a última atualização do CNPJ.
- **getCNPJInscricoesEstaduais($cnpj, $ie = 3, $tipoRetorno = 'json')**: Consulta as inscrições estaduais do CNPJ.

##### Exemplo de Uso:

```php
use PauloRLima\HubDoDesenvolvedor\Facades\HubDoDesenvolvedor;

$cnpj = '12.345.678/0001-90';

try {
    // Consulta básica de CNPJ
    $resultado = HubDoDesenvolvedor::getCNPJ($cnpj);

    // Consulta ignorando cache
    $resultadoModalidade2 = HubDoDesenvolvedor::getCNPJModalidade2($cnpj);

    // Consulta última atualização
    $ultimaAtualizacao = HubDoDesenvolvedor::getCNPJLastUpdate($cnpj);

    // Consulta inscrições estaduais
    $inscricoesEstaduais = HubDoDesenvolvedor::getCNPJInscricoesEstaduais($cnpj);

    // Manipule os resultados conforme necessário
} catch (\Exception $e) {
    // Trate erros
    echo 'Erro: ' . $e->getMessage();
}
```

#### Consulta de CPF

##### Métodos Disponíveis:

- **getCPF($cpf, $dataNascimento, $tipoRetorno = 'json')**: Consulta de CPF (modalidade 1).
- **getCPFModalidade2($cpf, $dataNascimento, $tipoRetorno = 'json')**: Consulta de CPF (modalidade 2 - ignora cache).
- **getCPFLastUpdate($cpf, $dataNascimento, $tipoRetorno = 'json')**: Consulta a última atualização do CPF.
- **getNomeDataNascimentoCPF($cpf, $tipoRetorno = 'json')**: Consulta nome e data de nascimento pelo CPF.

##### Exemplo de Uso:

```php
use PauloRLima\HubDoDesenvolvedor\Facades\HubDoDesenvolvedor;

$cpf = '123.456.789-09';
$dataNascimento = '01/01/1990';

try {
    // Consulta básica de CPF
    $resultado = HubDoDesenvolvedor::getCPF($cpf, $dataNascimento);

    // Consulta ignorando cache
    $resultadoModalidade2 = HubDoDesenvolvedor::getCPFModalidade2($cpf, $dataNascimento);

    // Consulta última atualização
    $ultimaAtualizacao = HubDoDesenvolvedor::getCPFLastUpdate($cpf, $dataNascimento);

    // Consulta nome e data de nascimento
    $nomeData = HubDoDesenvolvedor::getNomeDataNascimentoCPF($cpf);

    // Manipule os resultados conforme necessário
} catch (\Exception $e) {
    // Trate erros
    echo 'Erro: ' . $e->getMessage();
}
```

#### Consulta de CEP

##### Método Disponível:

- **getCEP($cep, $tipoRetorno = 'json')**: Consulta de CEP.

##### Exemplo de Uso:

```php
use PauloRLima\HubDoDesenvolvedor\Facades\HubDoDesenvolvedor;

$cep = '01001-000';

try {
    $resultado = HubDoDesenvolvedor::getCEP($cep);

    // Manipule os resultados conforme necessário
} catch (\Exception $e) {
    // Trate erros
    echo 'Erro: ' . $e->getMessage();
}
```

#### Consulta de Frete dos Correios

##### Método Disponível:

- **getCorreiosFrete(array $params, $tipoRetorno = 'json')**: Consulta de frete nos Correios.

Parâmetros obrigatórios em `$params`:

- `servico`: Código do serviço (ex: '40010' para SEDEX).
- `cepOrigem`: CEP de origem.
- `cepDestino`: CEP de destino.
- `altura`: Altura do pacote em centímetros.
- `largura`: Largura do pacote em centímetros.
- `peso`: Peso do pacote em quilogramas.
- `comprimento`: Comprimento do pacote em centímetros.
- `formato`: Formato do pacote (1 para caixa/pacote).
- `tipoServico`: Tipo de serviço.

##### Exemplo de Uso:

```php
use PauloRLima\HubDoDesenvolvedor\Facades\HubDoDesenvolvedor;

$params = [
    'servico' => '40010', // SEDEX
    'cepOrigem' => '01001-000',
    'cepDestino' => '20040-000',
    'altura' => '20',
    'largura' => '20',
    'peso' => '1',
    'comprimento' => '20',
    'formato' => '1',
    'tipoServico' => '1',
];

try {
    $resultado = HubDoDesenvolvedor::getCorreiosFrete($params);

    // Manipule os resultados conforme necessário
} catch (\Exception $e) {
    // Trate erros
    echo 'Erro: ' . $e->getMessage();
}
```

#### Consulta de Saldo

##### Método Disponível:

- **getSaldo($tipoRetorno = 'json')**: Consulta o saldo disponível na API.

##### Exemplo de Uso:

```php
use PauloRLima\HubDoDesenvolvedor\Facades\HubDoDesenvolvedor;

try {
    $saldo = HubDoDesenvolvedor::getSaldo();

    // Manipule o saldo conforme necessário
} catch (\Exception $e) {
    // Trate erros
    echo 'Erro: ' . $e->getMessage();
}
```

### Observações

- **Validações**: O pacote realiza validações dos documentos (CPF, CNPJ, CEP) antes de realizar as requisições à API, evitando o consumo desnecessário de créditos.
- **Tratamento de Exceções**: As exceções lançadas podem ser capturadas para tratamento adequado, como exibir mensagens de erro amigáveis ao usuário.
- **Timeout**: O tempo limite para as requisições pode ser configurado no arquivo de configuração.

## Configurações Adicionais

O arquivo de configuração `config/hubdodesenvolvedor.php` permite ajustar outras opções:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Token da API
    |--------------------------------------------------------------------------
    |
    | Insira aqui o seu token da API do Hub do Desenvolvedor.
    |
    */

    'token' => env('HUBDODESENVOLVEDOR_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Timeout das Requisições
    |--------------------------------------------------------------------------
    |
    | Defina o tempo máximo (em segundos) para as requisições à API.
    |
    */

    'timeout' => env('HUBDODESENVOLVEDOR_TIMEOUT', 600),

];
```

Certifique-se de que o token está corretamente definido para que as chamadas à API funcionem.

## Testes

Para executar os testes, utilize:

```bash
composer test
```

## Changelog

Por favor, veja o [CHANGELOG](CHANGELOG.md) para mais informações sobre as alterações recentes.

## Contribuindo

Por favor, veja o [CONTRIBUTING](CONTRIBUTING.md) para detalhes.

## Vulnerabilidades de Segurança

Por favor, consulte a [nossa política de segurança](../../security/policy) sobre como reportar vulnerabilidades de segurança.

## Créditos

- [Paulo R. Lima](https://github.com/paulorlima9)
- [Todos os Contribuidores](../../contributors)

## Licença

The MIT License (MIT). Por favor, veja o [Arquivo de Licença](LICENSE.md) para mais informações.

---

Esperamos que este pacote facilite a integração com a API do Hub do Desenvolvedor em seus projetos Laravel. Para qualquer dúvida ou sugestão, fique à vontade para abrir uma _issue_ ou enviar uma _pull request_.
