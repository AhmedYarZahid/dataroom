yii2-widget-isloading
=====================

Simple widget show visual feedback when loading data or any action that would take time. http://hekigan.github.io/is-loading/


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist tiagocardosos/yii2-widget-isloading "*"
```

or add

```
"tiagocardosos/yii2-widget-isloading": "*"
```

to the require section of your `composer.json` file.


Usage
-----
Add the rules as the following example

add view "main.php" file.

```php

use tiagocardosos\isloading\IsLoading;

```


### Simple

```php

<?=IsLoading::widget();?>

```

### Set Options
```php
<?php

echo IsLoading::widget([
    'text' => 'O sistema está processando...',
    'position => 'overlay', // right | inside | overlay
    'class' => 'fa fa-refresh',
    'tpl' => '<span class=\"isloading-wrapper %wrapper%\">%text% <i class=\"%class% fa-spin\"></i></span>',
    'disableSource' => true,
    'disableOthers' => ['$( "#load-in-div .btn" )'], //more access http://hekigan.github.io/is-loading/
	'timeout'=>true,
	'time'=>1000
]);

?>

```