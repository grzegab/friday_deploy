# Sample Company Project
This is a sample project for demonstrating Symfony 7.2 with PHP 8.3. The goal here is to show how to use Domain-Driven Design (DDD).
In this project, I decided not to use Doctrine for two reasons. First, this allows me to write and test more of my own code. Second, Doctrine can hinder maintaining clean DDD principles. The simplest approach to using Doctrine is to treat Domain models as Doctrine models. However, this compromises the independence of the Domain layer. The Domain should always remain independent of both Infrastructure and Application layers.
To avoid redundant checks, I have only applied a `UNIQUE` constraint on the Company tax number (as this is sufficient for the sample).
One more challenge I incorporated is the use of both ID and UUID in the database. Normally, UUIDs should be provided to users as URL parameters, while IDs help in database operations, such as inserting records or paginating data, if needed.
This project serves as an example of DDD architecture. It includes a Domain layer with...
(All commands provided below should be executed inside the Docker container.)
## Technology Stack
- PHP 8.3
- Symfony 7.2
- Redis (for message bus)
- MySQL (for data persistence)

### Installation & Running
To start the container, run:
``` bash
docker compose up -d
```
To make sure you have the correct database, create it with the following commands:
``` mysql
CREATE SCHEMA sample;

GRANT ALTER, ALTER ROUTINE, CREATE, CREATE ROUTINE, CREATE TEMPORARY TABLES, CREATE VIEW, DELETE, DROP, EVENT, EXECUTE, INDEX, INSERT, LOCK TABLES, REFERENCES, SELECT, SHOW VIEW, TRIGGER, UPDATE 
ON sample.* 
TO 'john';
```
### Documentation
As this is an API project, documentation is provided in JSON format using the Nelmio API doc bundle.
### Tests
The standard Symfony test package is included in this project.
- To run all tests:
``` bash
  php bin/phpunit ./tests
```
- To run unit tests:
``` bash
  php bin/phpunit ./tests/Unit
```
- To run integration tests (database):
``` bash
  php bin/phpunit ./tests/Integration
```
I don't prefer using Symfony's client for application (E2E) testing because it's not ideal for API testing. Instead, I use my own tool written in Go.
## Clean Code
To maintain clean code, PHP-CS-Fixer and PHPStan are used. For this sample project, the default configurations of these tools are applied.
- To run PHP-CS-Fixer on the `src` directory:
``` bash
  ./vendor/bin/php-cs-fixer fix src
```
- To run PHPStan:
``` bash
  vendor/bin/phpstan analyse src
```
