# Yii2-RBAC - README
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

Provides various interfaces to deal with routine RBAC tasks.

> This extension is under heavy development and requires the use of Yii framework version 2.0.7
> This version of the framework is currently in-active development  

> This module should be considered highly unstable and it's use is discouraged until further notice (really)

## Features

This module aims to be a clean, modular and simple RBAC-administration module that can be used for Yii 2 projects version 2.0.7 and up.

A first goal is to provide a complete CLI wrapper to the default Yii2 RBAC interface. For this, the interface is kept as close to the programmatic interface as possible.



## Installation

### Install Using Composer

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require humanized/yii2-rbac "dev-master"
```

or add

```
"humanized/yii2-rbac": "dev-master"
```

to the ```require``` section of your `composer.json` file.


### Add Module to Configuration

Add following lines to the configuration file:

```php
'modules' => [
    'rbac' => [
        'class' => 'humanized\rbac\Module',
    ],
],
```
