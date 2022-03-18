# Commission Task

## System requirements
* PHP v8.0 with extensions:
  * cURL
  * BC Math
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
php src/script.php /absolute/path/input.csv
```

## Testing
```shell
composer run phpunit
```
