<?php

namespace PauloRLima\HubDoDesenvolvedor;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HubDoDesenvolvedor
{
    protected $client;

    protected $baseUrl = 'https://ws.hubdodesenvolvedor.com.br/v2/';

    protected $token;

    protected $timeout;

    protected $tipoRetorno;

    /**
     * Construtor da classe, inicializa o cliente Guzzle e define o token e timeout.
     *
     * @param  string  $token
     * @param  int  $timeout
     */
    public function __construct($token, $timeout = 600)
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $timeout,
        ]);

        $this->token = $token;
        $this->timeout = $timeout;
        $this->tipoRetorno = config('hubdodesenvolvedor.tipoRetorno');
    }

    /**
     * Valida um CNPJ.
     *
     * @param  string  $cnpj
     * @return bool
     */
    protected function validateCNPJ($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

        // Valida tamanho
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Valida dígitos verificadores
        $sum = 0;
        $multipliers = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $multipliers[$i];
        }

        $rest = $sum % 11;
        $digit1 = $rest < 2 ? 0 : 11 - $rest;

        if ($cnpj[12] != $digit1) {
            return false;
        }

        $sum = 0;
        $multipliers = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $multipliers[$i];
        }

        $rest = $sum % 11;
        $digit2 = $rest < 2 ? 0 : 11 - $rest;

        if ($cnpj[13] != $digit2) {
            return false;
        }

        return true;
    }

    /**
     * Valida um CPF.
     *
     * @param  string  $cpf
     * @return bool
     */
    protected function validateCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);

        // Valida tamanho
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula os dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $sum = 0;

            for ($c = 0; $c < $t; $c++) {
                $sum += $cpf[$c] * ($t + 1 - $c);
            }

            $rest = ((10 * $sum) % 11) % 10;

            if ($cpf[$c] != $rest) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida um CEP.
     *
     * @param  string  $cep
     * @return bool
     */
    protected function validateCEP($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', (string) $cep);

        return strlen($cep) === 8;
    }

    /**
     * Valida uma data no formato DD/MM/AAAA.
     *
     * @param  string  $date
     * @return bool
     */
    protected function validateDate($date)
    {
        $d = \DateTime::createFromFormat('d/m/Y', $date);

        return $d && $d->format('d/m/Y') === $date;
    }

    /**
     * Consulta CNPJ modalidade 1.
     *
     * @param  string  $cnpj
     * @param  string  $tipoRetorno  ('json', 'xml', etc.)
     * @return array
     *
     * @throws Exception
     */
    public function getCNPJ($cnpj)
    {
        if (! $this->validateCNPJ($cnpj)) {
            throw new Exception('CNPJ inválido.');
        }

        $endpoint = "cnpj/?{$this->tipoRetorno}&cnpj={$cnpj}&token={$this->token}";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta CNPJ modalidade 2 (ignora cache).
     *
     * @param  string  $cnpj
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getCNPJModalidade2($cnpj)
    {
        if (! $this->validateCNPJ($cnpj)) {
            throw new Exception('CNPJ inválido.');
        }

        $endpoint = "cnpj/?{$this->tipoRetorno}&cnpj={$cnpj}&token={$this->token}&ignore_db";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta última atualização do CNPJ.
     *
     * @param  string  $cnpj
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getCNPJLastUpdate($cnpj)
    {
        if (! $this->validateCNPJ($cnpj)) {
            throw new Exception('CNPJ inválido.');
        }

        $endpoint = "cnpj/?{$this->tipoRetorno}&cnpj={$cnpj}&token={$this->token}&last_update=2";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta Inscrições Estaduais do CNPJ.
     *
     * @param  string  $cnpj
     * @param  int  $ie  (1 para tempo real, 3 para cache)
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getCNPJInscricoesEstaduais($cnpj, $ie = 3)
    {
        if (! $this->validateCNPJ($cnpj)) {
            throw new Exception('CNPJ inválido.');
        }

        $endpoint = "cnpj/?{$this->tipoRetorno}&cnpj={$cnpj}&token={$this->token}&ie={$ie}";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta Simples Nacional modalidade 1.
     *
     * @param  string  $cnpj
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getSimples($cnpj)
    {
        if (! $this->validateCNPJ($cnpj)) {
            throw new Exception('CNPJ inválido.');
        }

        $endpoint = "simples/?{$this->tipoRetorno}&cnpj={$cnpj}&token={$this->token}";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta Simples Nacional modalidade 2 (ignora cache).
     *
     * @param  string  $cnpj
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getSimplesModalidade2($cnpj)
    {
        if (! $this->validateCNPJ($cnpj)) {
            throw new Exception('CNPJ inválido.');
        }

        $endpoint = "simples/?{$this->tipoRetorno}&cnpj={$cnpj}&token={$this->token}&ignore_db";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta última atualização do Simples Nacional.
     *
     * @param  string  $cnpj
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getSimplesLastUpdate($cnpj)
    {
        if (! $this->validateCNPJ($cnpj)) {
            throw new Exception('CNPJ inválido.');
        }

        $endpoint = "simples/?{$this->tipoRetorno}&cnpj={$cnpj}&token={$this->token}&last_update=2";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta CEP.
     *
     * @param  string  $cep
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getCEP($cep)
    {
        if (! $this->validateCEP($cep)) {
            throw new Exception('CEP inválido.');
        }

        $endpoint = "cep/?{$this->tipoRetorno}&cep={$cep}&token={$this->token}";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta CEP3.
     *
     * @param  string  $cep
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getCEP3($cep)
    {
        return $this->getCEP($cep);
    }

    /**
     * Consulta CPF modalidade 1.
     *
     * @param  string  $cpf
     * @param  string  $dataNascimento  (DD/MM/AAAA)
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getCPF($cpf, $dataNascimento)
    {
        if (! $this->validateCPF($cpf)) {
            throw new Exception('CPF inválido.');
        }

        if (! $this->validateDate($dataNascimento)) {
            throw new Exception('Data de nascimento inválida.');
        }

        $endpoint = "cpf/?{$this->tipoRetorno}&cpf={$cpf}&data={$dataNascimento}&token={$this->token}";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta CPF modalidade 2 (ignora cache).
     *
     * @param  string  $cpf
     * @param  string  $dataNascimento
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getCPFModalidade2($cpf, $dataNascimento)
    {
        if (! $this->validateCPF($cpf)) {
            throw new Exception('CPF inválido.');
        }

        if (! $this->validateDate($dataNascimento)) {
            throw new Exception('Data de nascimento inválida.');
        }

        $endpoint = "cpf/?{$this->tipoRetorno}&cpf={$cpf}&data={$dataNascimento}&token={$this->token}&ignore_db";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta última atualização do CPF.
     *
     * @param  string  $cpf
     * @param  string  $dataNascimento
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getCPFLastUpdate($cpf, $dataNascimento)
    {
        if (! $this->validateCPF($cpf)) {
            throw new Exception('CPF inválido.');
        }

        if (! $this->validateDate($dataNascimento)) {
            throw new Exception('Data de nascimento inválida.');
        }

        $endpoint = "cpf/?{$this->tipoRetorno}&cpf={$cpf}&data={$dataNascimento}&token={$this->token}&last_update=2";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta Nome e Data de Nascimento pelo CPF modalidade 1.
     *
     * @param  string  $cpf
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getNomeDataNascimentoCPF($cpf)
    {
        if (! $this->validateCPF($cpf)) {
            throw new Exception('CPF inválido.');
        }

        $endpoint = "cpf/?{$this->tipoRetorno}&cpf={$cpf}&onlyBirthDate=&token={$this->token}";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta Nome e Data de Nascimento pelo CPF modalidade 2 (ignora cache).
     *
     * @param  string  $cpf
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getNomeDataNascimentoCPFModalidade2($cpf)
    {
        if (! $this->validateCPF($cpf)) {
            throw new Exception('CPF inválido.');
        }

        $endpoint = "cpf/?{$this->tipoRetorno}&cpf={$cpf}&onlyBirthDate=&token={$this->token}&ignore_db";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta última atualização do Nome e Data de Nascimento pelo CPF.
     *
     * @param  string  $cpf
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getNomeDataNascimentoCPFLastUpdate($cpf)
    {
        if (! $this->validateCPF($cpf)) {
            throw new Exception('CPF inválido.');
        }

        $endpoint = "cpf/?{$this->tipoRetorno}&cpf={$cpf}&token={$this->token}&last_update=2&onlyBirthDate";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta frete nos Correios.
     *
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getCorreiosFrete(array $params)
    {
        // Valida os parâmetros necessários
        $requiredParams = [
            'servico',
            'cepOrigem',
            'cepDestino',
            'altura',
            'largura',
            'peso',
            'comprimento',
            'formato',
        ];

        foreach ($requiredParams as $param) {
            if (! isset($params[$param])) {
                throw new Exception("O parâmetro {$param} é obrigatório.");
            }
        }

        // Inclui o tipo de retorno nos parâmetros
        $params['retorno'] = $this->tipoRetorno;

        // Adiciona o token aos parâmetros
        $params['token'] = $this->token;

        // Construção da query string
        $queryParams = http_build_query($params);

        // Exibir endpoint para depuração
        echo "Endpoint: {$this->baseUrl}correios/?{$queryParams}\n";

        $endpoint = "correios/?{$queryParams}";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta IBGE.
     *
     * @param  string  $codigoCidade
     * @param  string  $tipoRetorno
     * @return array
     *
     * @throws Exception
     */
    public function getIBGE($codigoCidade)
    {
        if (! is_numeric($codigoCidade)) {
            throw new Exception('Código da cidade inválido.');
        }

        $endpoint = "ibge/?{$this->tipoRetorno}&cod_cidade={$codigoCidade}&token={$this->token}";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Consulta saldo.
     *
     * @return array
     *
     * @throws Exception
     */
    public function getSaldo()
    {
        $endpoint = "saldo/?{$this->tipoRetorno}&info&token={$this->token}";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Método genérico para realizar requisições.
     *
     * @param string $method
     * @param string $endpoint
     * @return mixed
     * @throws Exception
     */
    protected function makeRequest($method, $endpoint)
    {
        try {
            // Realiza a requisição
            $response = $this->client->request($method, $endpoint);
            $body = $response->getBody()->getContents();

            // Verifica o tipo de retorno configurado (json ou xml)
            if ($this->tipoRetorno === 'json') {
                // Decodifica a resposta JSON
                $data = json_decode($body, true);

                if (!is_array($data)) {
                    throw new Exception('Resposta inválida da API (não é JSON): ' . $body);
                }

                if (isset($data['return']) && $data['return'] === 'NOK') {
                    throw new Exception($data['message'] ?? 'Erro desconhecido na API');
                }

                return $data; // Retorna a resposta JSON decodificada como array
            } elseif ($this->tipoRetorno === 'xml') {
                // Converte a string XML em um objeto SimpleXMLElement
                $xml = simplexml_load_string($body, "SimpleXMLElement", LIBXML_NOCDATA);

                if ($xml === false) {
                    throw new Exception('Resposta inválida da API (não é XML): ' . $body);
                }

                // Verifica o status da resposta no XML
                if (isset($xml->return) && (string)$xml->return === 'NOK') {
                    $message = isset($xml->message) ? (string)$xml->message : 'Erro desconhecido na API';
                    throw new Exception($message);
                }

                return $xml; // Retorna o XML como objeto SimpleXMLElement
            } else {
                throw new Exception("Tipo de retorno não suportado: {$this->tipoRetorno}");
            }
        } catch (RequestException $e) {
            $message = $e->hasResponse()
                ? $e->getResponse()->getBody()->getContents()
                : $e->getMessage();

            throw new Exception("Erro na requisição: {$message}");
        } catch (Exception $e) {
            throw new Exception("Erro: {$e->getMessage()}");
        }
    }
}
