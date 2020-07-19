# To specify text which are not real files or dir
.PHONY: install help up build prune down app clean

# make|make help, Displays help
.DEFAULT_GOAL = help

# Docker and docker-compose start commands
DOCKER_COMPOSE = docker-compose
DOCKER = docker

EXEC_PHP = $(DOCKER_COMPOSE) exec -T php

vendor: ## Install symfony application dependencies
	cd project; composer install

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-10s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

up: down ## Wakes up containers in the detached mode
	$(DOCKER_COMPOSE) up -d

install: down clean build up vendor ## Builds containers and run them

build: down prune ## Builds images
	$(DOCKER_COMPOSE) build

prune: down ## Cleans up unused containers and images
	$(DOCKER) system prune -a -f

down: ## Switches off all running containers
	$(DOCKER_COMPOSE) down

bash:
	$(DOCKER_COMPOSE) exec php bash

fixtures: migration ## Makes data available for the application
	$(EXEC_PHP) ./bin/console doctrine:fixtures:load --no-interaction

migration: db-drop db-create ## Updates database schema
	$(EXEC_PHP) ./bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

fixtures-test:
	$(EXEC_PHP) ./bin/console doctrine:database:drop --if-exists --force --env=test
	$(EXEC_PHP) ./bin/console doctrine:database:create --if-not-exists --env=test
	$(EXEC_PHP) ./bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=test
	$(EXEC_PHP) ./bin/console doctrine:fixtures:load --no-interaction --env=test

functional-tests:
	docker-compose exec -T php /app/my-api/vendor/bin/simple-phpunit --configuration /app/my-api/phpunit.xml.dist --testsuite "Functional testing"

db-drop: ## Drops mysql database
	$(EXEC_PHP) ./bin/console doctrine:database:drop --if-exists --force

db-create: ## Creates mysql database
	$(EXEC_PHP) ./bin/console doctrine:database:create --if-not-exists

app: clean up project/vendor ## Builds vendor and assets. App will be ready

clean: prune ## Stops and clean all containers and volumes; removes node_modules, vendor and public/build folders
	rm -rf project/node_modules; rm -rf project/vendor; rm -rf project/public/build; rm -rf project/var
