.DEFAULT_GOAL: help
.PHONY: help

INTERACTIVE := $(shell [ -t 0 ] && echo 1 || echo 0)
ifeq ($(INTERACTIVE), 1)
	DOCKER_FLAGS += -it
endif
COMPOSER_HOME      ?= ${HOME}/.composer
COMPOSER_SHELL = docker run $(DOCKER_FLAGS) -i --rm \
	--env COMPOSER_HOME=${COMPOSER_HOME} \
	--volume ${COMPOSER_HOME}:${COMPOSER_HOME} \
	--volume ${PWD}:/app \
	--user $(shell id -u):$(shell id -g) \
	--workdir /app \
	composer:2

DOCKER = docker compose -p agent

help: ## Display this help
	@awk 'BEGIN {FS = ":.* ##"; printf "\n\033[1mUsage:\033[0m\n  make \033[32m<target>\033[0m\n"} /^[a-zA-Z_-]+:.* ## / { printf "  \033[33m%-25s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Installation
install: ## Install all necessary things
	$(MAKE) vendor

require: ## Require composer packages
	$(COMPOSER_SHELL) require $(ARG)

vendor: composer.lock composer.json
	$(COMPOSER_SHELL) install --ignore-platform-reqs --no-scripts
	@touch vendor

##@ Ccnsole
.PHONY: console
console: ## Run console
	$(DOCKER) -p agent run --rm --no-deps agent-fpm php bin/console $(ARG)

##@ Code analysis
.PHONY: apply-cs
apply-cs: ## Run php-cs-fixer
	$(DOCKER) -p agent run --rm --no-deps agent-fpm ./vendor/bin/php-cs-fixer fix --show-progress=dots --diff --config=.php-cs-fixer.dist.php

.PHONY: static-code-analysis
static-code-analysis: ## Run phpstan
	$(DOCKER) -p agent run --rm --no-deps agent-fpm ./vendor/bin/phpstan analyse --memory-limit=-1

##@ tests
.PHONY: test
test: ## Run tests
	$(DOCKER) -p agent run --rm --no-deps agent-fpm ./vendor/bin/phpunit $(ARG)
