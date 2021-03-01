<a href="https://aimeos.org/">
    <img src="https://aimeos.org/fileadmin/template/icons/logo.png" alt="Aimeos logo" title="Aimeos" align="right" height="60" />
</a>

# Aimeos GrapesJS CMS

[![Build Status](https://circleci.com/gh/aimeos/ai-cms-grapesjs.svg?style=shield)](https://circleci.com/gh/aimeos/ai-cms-grapesjs)
[![Coverage Status](https://coveralls.io/repos/aimeos/ai-cms-grapesjs/badge.svg?branch=master)](https://coveralls.io/r/aimeos/ai-cms-grapesjs?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aimeos/ai-cms-grapesjs/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aimeos/ai-cms-grapesjs/?branch=master)
[![License](https://poser.pugx.org/aimeos/ai-cms-grapesjs/license.svg)](https://packagist.org/packages/aimeos/ai-cms-grapesjs)

The Aimeos GrapesJS CMS extension provides a simple to use but powerful page editor for creating content pages based on extensible components.

## Installation

As every Aimeos extension, the easiest way is to install it via [composer](https://getcomposer.org/). If you don't have composer installed yet, you can execute this string on the command line to download it:

```
php -r "readfile('https://getcomposer.org/installer');" | php -- --filename=composer
```

To add the extionsion to your composer-based installation, execute:

```
composer req "aimeos/ai-cms-grapesjs"
```

These command will install the Aimeos extension into the extension directory and it will be available immediately.

## Integration

### Laravel

First, you have to create the new tables required for the pages by executing this command in the **root directory of your Laravel application**:

```bash
php artisan aimeos:setup
```

To show the content for the CMS page URLs, you have to add this at the **end** of the `./routes/web.php` file in your Laravel application:

```php
Route::get('{path?}', '\Aimeos\Shop\Controller\PageController@indexAction')
    ->name('aimeos_page')->where( 'path', '.*' );
```

This will add a "catch all" route for every URL that hasn't been matched before so **don't put routes after** that line because they won't be used any more!
