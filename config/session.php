<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Driver de Sessão Padrão
    |--------------------------------------------------------------------------
    |
    | Esta opção determina o driver de sessão padrão que é utilizado para
    | requisições recebidas. O Laravel suporta uma variedade de opções de
    | armazenamento para persistir dados de sessão. O armazenamento em
    | arquivo é uma ótima escolha padrão.
    |
    | Suportados: "file", "cookie", "database", "memcached",
    |            "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Tempo de Vida da Sessão
    |--------------------------------------------------------------------------
    |
    | Aqui você pode especificar o número de minutos que deseja que a sessão
    | seja permitida permanecer ociosa antes de expirar. Se você quiser que
    | elas expirem imediatamente quando o navegador for fechado, você pode
    | indicar isso através da opção de configuração expire_on_close.
    |
    */

    'lifetime' => env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,

    /*
    |--------------------------------------------------------------------------
    | Session Encryption
    |--------------------------------------------------------------------------
    |
    | This option allows you to easily specify that all of your session data
    | should be encrypted before it is stored. All encryption will be run
    | automatically by Laravel and you can use the Session like normal.
    |
    */

    'encrypt' => false,

    /*
    |--------------------------------------------------------------------------
    | Localização dos Arquivos de Sessão
    |--------------------------------------------------------------------------
    |
    | Ao utilizar o driver de sessão "file", os arquivos de sessão são
    | colocados no disco. A localização de armazenamento padrão é definida
    | aqui; no entanto, você é livre para fornecer outra localização onde
    | eles devem ser armazenados.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Conexão do Banco de Dados da Sessão
    |--------------------------------------------------------------------------
    |
    | Ao usar os drivers de sessão "database" ou "redis", você pode especificar
    | uma conexão que deve ser usada para gerenciar essas sessões. Isso deve
    | corresponder a uma conexão em suas opções de configuração de banco de dados.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Tabela do Banco de Dados da Sessão
    |--------------------------------------------------------------------------
    |
    | Ao usar o driver de sessão "database", você pode especificar a tabela
    | a ser usada para armazenar sessões. Claro, um padrão sensato é definido
    | para você; no entanto, você é bem-vindo para mudar isso para outra tabela.
    |
    */

    'table' => env('SESSION_TABLE', 'sessions'),

    /*
    |--------------------------------------------------------------------------
    | Armazenamento de Cache da Sessão
    |--------------------------------------------------------------------------
    |
    | Ao usar um dos backends de sessão dirigidos por cache do framework,
    | você pode definir o armazenamento de cache que deve ser usado para
    | armazenar os dados da sessão entre requisições. Isso deve corresponder
    | a um dos seus armazenamentos de cache definidos.
    |
    | Afeta: "dynamodb", "memcached", "redis"
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Loteria de Limpeza da Sessão
    |--------------------------------------------------------------------------
    |
    | Alguns drivers de sessão devem limpar manualmente sua localização de
    | armazenamento para se livrar de sessões antigas do armazenamento.
    | Aqui estão as chances de que isso aconteça em uma requisição dada.
    | Por padrão, as chances são 2 em 100.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Nome do Cookie da Sessão
    |--------------------------------------------------------------------------
    |
    | Aqui você pode alterar o nome do cookie de sessão que é criado pelo
    | framework. Tipicamente, você não deve precisar alterar este valor
    | já que fazer isso não concede uma melhoria de segurança significativa.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Caminho do Cookie da Sessão
    |--------------------------------------------------------------------------
    |
    | O caminho do cookie de sessão determina o caminho para o qual o cookie
    | será considerado disponível. Tipicamente, isso será o caminho raiz
    | da sua aplicação, mas você é livre para alterar isso quando necessário.
    |
    */

    'path' => env('SESSION_PATH', '/'),

    /*
    |--------------------------------------------------------------------------
    | Domínio do Cookie da Sessão
    |--------------------------------------------------------------------------
    |
    | Este valor determina o domínio e subdomínios para os quais o cookie
    | de sessão está disponível. Por padrão, o cookie estará disponível
    | para o domínio raiz e todos os subdomínios. Tipicamente, isso não deve ser alterado.
    |
    */

    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Cookies Apenas HTTPS
    |--------------------------------------------------------------------------
    |
    | Ao definir esta opção como true, os cookies de sessão só serão enviados
    | de volta para o servidor se o navegador tiver uma conexão HTTPS. Isso
    | manterá o cookie de ser enviado para você quando não puder ser feito com segurança.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /*
    |--------------------------------------------------------------------------
    | Acesso Apenas HTTP
    |--------------------------------------------------------------------------
    |
    | Definir este valor como true impedirá que JavaScript acesse o valor
    | do cookie e o cookie só será acessível através do protocolo HTTP.
    | É improvável que você deva desabilitar esta opção.
    |
    */

    'http_only' => env('SESSION_HTTP_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Cookies Same-Site
    |--------------------------------------------------------------------------
    |
    | Esta opção determina como seus cookies se comportam quando requisições
    | cross-site acontecem, e pode ser usada para mitigar ataques CSRF.
    | Por padrão, definiremos este valor como "lax" para permitir requisições
    | cross-site seguras.
    |
    | Veja: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
    |
    | Suportados: "lax", "strict", "none", null
    |
    */

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /*
    |--------------------------------------------------------------------------
    | Cookies Partitioned
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will tie the cookie to the top-level site for
    | a cross-site context. Partitioned cookies are accepted by the browser
    | when flagged "secure" and the Same-Site attribute is set to "none".
    |
    */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
