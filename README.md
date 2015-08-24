# CakePHP Fio

[![Build Status](https://travis-ci.org/LubosRemplik/CakePHP-Fio.svg)](https://travis-ci.org/LubosRemplik/CakePHP-Fio)
[![Latest Stable Version](https://poser.pugx.org/lubos/fio/v/stable.svg)](https://packagist.org/packages/lubos/fio) 
[![Total Downloads](https://poser.pugx.org/lubos/fio/downloads.svg)](https://packagist.org/packages/lubos/fio) 
[![Latest Unstable Version](https://poser.pugx.org/lubos/fio/v/unstable.svg)](https://packagist.org/packages/lubos/fio) 
[![License](https://poser.pugx.org/lubos/fio/license.svg)](https://packagist.org/packages/lubos/fio)

CakePHP 3.x plugin for interacting with [Fio api](http://www.fio.cz/bankovni-sluzby/api-bankovnictvi)

## Installation & Configuration

```
composer require lubos/fio
```

Load plugin in bootstrap.php file

```php
Plugin::load('Lubos/Fio');
```

Get Fio token on [Fio](http://fio.com) and put them in config
```php
'Fio' => [
    'token' => 'your-token'
]
```

## Usage

run `bin/cake` to see shells and its options  

## Bugs & Features

For bugs and feature requests, please use the issues section of this repository.

If you want to help, pull requests are welcome.  
Please follow few rules:  

- Fork & clone
- Code bugfix or feature
- Follow [CakePHP coding standards](https://github.com/cakephp/cakephp-codesniffer)
