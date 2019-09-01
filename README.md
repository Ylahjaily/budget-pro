## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

What things you need to install the software and how to install them?

- [Docker CE](https://www.docker.com/community-edition)
- [Docker Compose](https://docs.docker.com/compose/install)

### Install

- (optional) Create your `docker-compose.override.yml` file

```bash
cp docker-compose.override.yml.dist docker-compose.override.yml
```
> Notice : Check the file content. If other containers use the same ports, change yours.

#### Init

```bash
docker-compose up -d
docker-compose exec --user=application web bash
docker-compose exec web composer install
docker-compose exec web d:s:u --force
docker-compose exec web doctrine:fixtures:load --purge-with-truncate

#### Heroku

https://budgetprojetcapi.herukoapp.com/api/doc
