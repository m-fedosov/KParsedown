![KParsedown](_docs/logo.png)

# Overview

Markdown Parser in KPHP

**KParsedown** is a fork from [Parsedown](https://github.com/erusev/parsedown)

### Features

- One File
- No Dependencies
- Cross-language support: works for both PHP and KPHP

KParsedown provide two methods:

```php
# parse file
$text = file_get_contents('README.md');
var_dump($parse->text($text));

# parse string
var_dump($parse->text('# Hello'));
```

Both methods return HTML string(s). See Quick Start.

## Quick Start
Create **vendor/autoload.php** with composer

```bash
composer dump-autoload
```

Create **index.php** and write here:
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Markdown\kparser\parsedown;

$parse = new parsedown();

var_dump($parse->text('Hello _Parsedown_!')); 
```
Run  with **PHP**

```bash
php -f index.php
string(14) "<h1>Hello</h1>"
```

Run with **KPHP**

```bash
# Execute result in Terminal
./kphp2cpp --composer-root $(pwd) --mode cli index.php
./kphp_out/cli
string(14) "<h1>Hello</h1>"
```

or

```bash
# Execute result in localhost
./kphp2cpp --composer-root $(pwd) index.php
./kphp_out/server -H 8080 -f 1
```
http://localhost:8080/

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
