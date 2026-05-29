# Команды разработки package-core. Тесты — в изолированных Docker-контейнерах,
# composer/php/node на хосте не нужны.

SHELL := /bin/bash
DC := docker compose -f docker-compose.test.yml

.DEFAULT_GOAL := help
.PHONY: help test test-backend test-frontend test-watch clean

help: ## Показать список таргетов
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-16s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

test: ## Прогнать все тесты (backend + frontend)
	@$(MAKE) test-backend
	@$(MAKE) test-frontend

test-backend: ## PHPUnit + Orchestra Testbench (контейнер на PHP 8.4-cli)
	$(DC) run --rm --build test-backend

test-frontend: ## tsc --noEmit + Vitest (контейнер node:20-alpine)
	$(DC) run --rm test-frontend

test-watch: ## Vitest в watch-режиме (разработка фронта)
	$(DC) run --rm test-frontend sh -c "npm install --no-fund --no-audit && npm run test:watch"

clean: ## Удалить тестовые volumes и контейнеры
	$(DC) down -v
