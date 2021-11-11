# version
Class permet de manipuler des numéros de version sementique

## Installation

```shell
composer require fzed51/version
```

## usage

Création à partir du constructeur, l'objet SemVer est converti automatiquement en string. Il a des getter pour chaque élément de la version. Il prend en charge le numéro de version majeur, mineur, les patch, les pre release et les meta de build.
```php
$version = new \Version\SemVer(1,0,0);
echo $version; // v1.0.0
echo $version->major(); // 1
```
```php
$version = new \Version\SemVer(1,1,0);
$version->nextMinor();
echo $version; // v1.2.0
```
```php
$version = new \Version\SemVer(1,1,0);
$version->nextMajor();
echo $version; // v2.0.0
```
```php
$version = new \Version\SemVer(1,0,0,'dev');
echo $version; // v1.0.0-dev
$version->setRelease('rc1')
echo $version; // v1.0.0-rc1
$version->setMetaBuild('01122021-1630')
echo $version; // v1.0.0-rc1+01122021-1630
$version->setMetaBuild();
echo $version; // v1.0.0-rc1
$version->setRelease();
echo $version; // v1.0.0
```
```php
$version = \Version\SemVer::fromString('v2.4');
echo $version; // v2.4.0
```
```php
$version1 = new \Version\SemVer(1,1,0);
$version2 = new \Version\SemVer(11,0,0);
var_dump($version2->gt($version1)); // true
var_dump($version2->ge($version1)); // true
var_dump($version2->eq($version1)); // false
var_dump($version2->le($version1)); // false
var_dump($version2->lt($version1)); // false
var_dump($version2->ne($version1)); // true
```
```php
$version = new \Version\SemVer(1,1,0,'rc1','12242010');
$json = json_encode($version) ;
echo $json; // {"major":1,"minor":0,"patch":0,"preRelease":"rc1","metaBuild":"12242010"}
```