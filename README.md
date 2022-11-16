![KParsedown](_docs/logo.png)

# Overview

Markdown Parser in KPHP

**KParsedown** is a fork from [Parsedown](https://github.com/erusev/parsedown)

### Features

One File
No Dependencies
Cross-language support: works for both PHP and KPHP


Env file & markdown parser for KPHP.

kParsedown provide two methods:

```php
parse_env_file(string $filename); # parse file

parse_env_string(string $markdown_string); # parse string
```

Both methods return HTML string(s). See example.

## Quick Start
Create **index.php** and write here:
```
<?php

require_once __DIR__ . '/vendor/autoload.php';

use mifedosov\markdown\parser\Parsedown;

$Parsedown = new Parsedown();

echo $Parsedown->text('Hello _Parsedown_!');
```

## Example

```php
$Parsedown = new Parsedown();

echo $Parsedown->text('Hello _Parsedown_!'); # prints: <p>Hello <em>Parsedown</em>!</p>
```

```php
<?php

    require_once 'vendor/autoload.php';

    use pmswga\kenv\Env;

    $env = Env::parse_env_file('.env');

    print_r($env);
```
Run with PHP:

```bash
$ php -f index.php

Array
(
    [APP_NAME] => Laravel
    [APP_ENV] => local
    [APP_KEY] => base64:mtlb8hldh5hZ0GlLzbhInsV531MSylspRI4JsmwVal8=
    [APP_DEBUG] => true
    [APP_URL] => http://localhost
    [APP_12] => asfasf
    [LOG_CHANNEL] => stack
    [LOG_DEPRECATIONS_CHANNEL] => null
    [LOG_LEVEL] => debug
    [DB_CONNECTION] => mysql
    [DB_HOST] => 127.0.0.1
    [DB_PORT] => 3306
    [DB_DATABASE] => laravel
    [DB_USERNAME] => root
    [DB_PASSWORD] =>
    [BROADCAST_DRIVER] => log
    [CACHE_DRIVER] => file
    [FILESYSTEM_DISK] => local
    [QUEUE_CONNECTION] => sync
    [SESSION_DRIVER] => file
    [SESSION_LIFETIME] => 120
    [MEMCACHED_HOST] => 127.0.0.1
    [REDIS_HOST] => 127.0.0.1
    [REDIS_PASSWORD] => null
    [REDIS_PORT] => 6379
    [MAIL_MAILER] => smtp
    [MAIL_HOST] => mailhog
    [MAIL_PORT] => 1025
    [MAIL_USERNAME] => null
    [MAIL_PASSWORD] => null
    [MAIL_ENCRYPTION] => null
    [MAIL_FROM_ADDRESS] => null
    [MAIL_FROM_NAME] => ${APP_NAME}
    [AWS_ACCESS_KEY_ID] =>
    [AWS_SECRET_ACCESS_KEY] =>
    [AWS_DEFAULT_REGION] => us-east-1
    [AWS_BUCKET] =>
    [AWS_USE_PATH_STYLE_ENDPOINT] => false
    [PUSHER_APP_ID] =>
    [PUSHER_APP_KEY] =>
    [PUSHER_APP_SECRET] =>
    [PUSHER_APP_CLUSTER] => mt1
    [MIX_PUSHER_APP_KEY] => ${PUSHER_APP_KEY}
    [MIX_PUSHER_APP_CLUSTER] => ${PUSHER_APP_CLUSTER}
)
```

Run with KPHP:

```bash
# 1. Compile
$ kphp --composer-root $(pwd) --mode cli example.php
# 2. Execute
$ ./kphp_out/cli
```

## Advanced settings

Parsedown features were implemented in KParsedown =^_^=

### Strict Mode

By default, KParsedown tries to parse any string

> StrictMode - **enabled**
> 
> ####Level 1 
> #### Level 1

`$Parsedown->setStrictMode(false);`

> StrictMode - **disabled**
>
> ####Level 1
>
> `<p>####Level 1<\p>`

### Breaks

By default, when reading a new line in a file, a newline is automatically set to markdown

If you need to disable it, use:

`$Parsedown->setBreaksEnabled(false);`

### Security

KParsedown is capable of escaping user-input within the HTML that it generates. Additionally KParsedown will apply sanitisation to additional scripting vectors (such as scripting link destinations) that are introduced by the markdown syntax itself.

To tell KParsedown that it is processing untrusted user-input, use the following:

`$Parsedown->setSafeMode(true);`

### Markup Escaped

If you wish to escape HTML in trusted input, you can use the following:

`$Parsedown->setMarkupEscaped(true);`

> Beware that this still allows users to insert unsafe scripting vectors, such as links like [xss](javascript:alert%281%29).

## Questions

**How does Parsedown work?**

It tries to read Markdown like a human. First, it looks at the lines. It’s interested in how the lines start. This helps it recognise blocks. It knows, for example, that if a line starts with a - then perhaps it belongs to a list. Once it recognises the blocks, it continues to the content. As it reads, it watches out for special characters. This helps it recognise inline elements (or inlines).

**Is it compliant with CommonMark?**

It passes most of the CommonMark tests. Most of the tests that don't pass deal with cases that are quite uncommon. Still, as CommonMark matures, compliance should improve.
