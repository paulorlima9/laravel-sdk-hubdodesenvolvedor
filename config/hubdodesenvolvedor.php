<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Token da API
    |--------------------------------------------------------------------------
    |
    | Insira aqui o seu token da API do Hub do Desenvolvedor. Você pode definir
    | isso no arquivo .env usando a variável HUBDODESENVOLVEDOR_TOKEN.
    |
    */

    'token' => env('HUBDODESENVOLVEDOR_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Timeout da API
    |--------------------------------------------------------------------------
    |
    | Define o tempo máximo de execução para requisições na API. Você pode
    | definir isso no arquivo .env usando a variável HUBDODESENVOLVEDOR_TIMEOUT.
    |
    */

    'timeout' => env('HUBDODESENVOLVEDOR_TIMEOUT', 600),

    /*
    |--------------------------------------------------------------------------
    | Tipo de Retorno da API
    |--------------------------------------------------------------------------
    |
    | Defina o formato de retorno da API: 'json' ou 'xml'. Você pode definir isso
    | no arquivo .env usando a variável HUBDODESENVOLVEDOR_TIPO_RETORNO.
    |
    */

    'tipoRetorno' => env('HUBDODESENVOLVEDOR_TIPO_RETORNO', 'json'),

];
