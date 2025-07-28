<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nome da Aplicação
    |--------------------------------------------------------------------------
    |
    | Este valor é o nome da sua aplicação, que será usado quando o framework
    | precisar exibir o nome da aplicação em notificações ou outros elementos
    | de interface onde o nome da aplicação precisa ser mostrado.
    |
    */

    'name' => env('APP_NAME', 'DashMEBoard'),

    /*
    |--------------------------------------------------------------------------
    | Ambiente da Aplicação
    |--------------------------------------------------------------------------
    |
    | Este valor determina o "ambiente" em que sua aplicação está rodando.
    | Isso pode determinar como você prefere configurar vários serviços
    | que a aplicação utiliza. Defina isso no seu arquivo ".env".
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Modo de Debug da Aplicação
    |--------------------------------------------------------------------------
    |
    | Quando sua aplicação está em modo debug, mensagens de erro detalhadas
    | com stack traces serão mostradas em todos os erros que ocorrem na
    | aplicação. Se desabilitado, uma página de erro genérica simples é mostrada.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL da Aplicação
    |--------------------------------------------------------------------------
    |
    | Esta URL é usada pelo console para gerar URLs corretamente quando
    | usando a ferramenta de linha de comando Artisan. Você deve definir
    | isso para a raiz da aplicação para que esteja disponível nos comandos Artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Fuso Horário da Aplicação
    |--------------------------------------------------------------------------
    |
    | Aqui você pode especificar o fuso horário padrão para sua aplicação,
    | que será usado pelas funções de data e hora do PHP. O fuso horário
    | é definido como "UTC" por padrão, sendo adequado para a maioria dos casos.
    |
    */

    'timezone' => 'America/Sao_Paulo',

    /*
    |--------------------------------------------------------------------------
    | Configuração de Localização da Aplicação
    |--------------------------------------------------------------------------
    |
    | A localização da aplicação determina o locale padrão que será usado
    | pelos métodos de tradução/localização do Laravel. Esta opção pode
    | ser definida para qualquer locale para o qual você planeja ter strings de tradução.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Chave de Criptografia
    |--------------------------------------------------------------------------
    |
    | Esta chave é utilizada pelos serviços de criptografia do Laravel e deve
    | ser definida como uma string aleatória de 32 caracteres para garantir
    | que todos os valores criptografados sejam seguros. Você deve fazer isso
    | antes de fazer o deploy da aplicação.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Driver do Modo de Manutenção
    |--------------------------------------------------------------------------
    |
    | Estas opções de configuração determinam o driver usado para determinar
    | e gerenciar o status do "modo de manutenção" do Laravel. O driver "cache"
    | permitirá que o modo de manutenção seja controlado em múltiplas máquinas.
    |
    | Drivers suportados: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
