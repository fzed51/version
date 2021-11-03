# version
Class permet de manipuler des numéros de version

## Installation

```shell
composer require fzed51/version
```

## usage

```php
$version = new \Version\Version(1,0,0);
echo $version; // v1.0.0
echo $version->major(); // 1
```
```php
$version = new \Version\Version(1,1,0);
$version->nextMinor();
echo $version; // v1.2.0
```
```php
$version = new \Version\Version(1,1,0);
$version->nextMajor();
echo $version; // v2.0.0
```
```php
$version = new \Version\Version(1,0,0,'dev');
echo $version; // v1.0.0-dev
$version->setTag('rc1')
echo $version; // v1.0.0-rc1
$version->clearTag();
echo $version; // v1.0.0
```
```php
$version = \Version\Version::fromString('v2.4');
echo $version; // v2.4.0
```
```php
$version1 = new \Version\Version(1,1,0);
$version2 = new \Version\Version(11,0,0);
var_dump($version2->greatThan($version1)); // false
```

Implémente l'interface JsonSerializable. Dans une future version, _Version_ implémentera l'interface Stringable.