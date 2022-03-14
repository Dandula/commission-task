# Commission Task

## System requirements
* PHP v7.0 with extensions:
  * curl
  * json
  * bcmath
* Composer 2

## Installation
1. Execute:
```shell
composer install
composer run post-root-package-install
```
2. Set API URL at `.env` file.

## Execution
```shell
php src/script.php input.csv
```

## Testing
```shell
composer run phpunit
```
