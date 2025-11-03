.PHONY: help up down restart logs install migrate sync clean

help:
	@echo "STON.fi Pools Dashboard - Makefile commands"
	@echo ""
	@echo "  make up         - Запустить Docker контейнеры"
	@echo "  make down       - Остановить контейнеры"
	@echo "  make restart    - Перезапустить контейнеры"
	@echo "  make logs       - Показать логи всех контейнеров"
	@echo "  make install    - Установить зависимости и применить миграции"
	@echo "  make migrate    - Применить миграции БД"
	@echo "  make sync       - Синхронизировать данные с API"
	@echo "  make clean      - Удалить контейнеры и volumes"
	@echo ""

up:
	@mkdir -p docker/mysql/data
	docker-compose up -d
	@echo "Приложение доступно: http://localhost:8080"

down:
	docker-compose down

restart:
	docker-compose restart

logs:
	docker-compose logs -f

install:
	docker-compose exec php composer install
	docker-compose exec php chmod +x yii
	docker-compose exec php php yii migrate --interactive=0
	docker-compose exec php php yii pools/sync
	@echo "Установка завершена! Откройте http://localhost:8080"

migrate:
	docker-compose exec php php yii migrate --interactive=0

sync:
	docker-compose exec php php yii pools/sync

clean:
	docker-compose down
	rm -rf vendor/ runtime/logs/* web/assets/*
	@echo "ВНИМАНИЕ: Данные MySQL сохранены в docker/mysql/data/"
	@echo "Для полной очистки выполните: rm -rf docker/mysql/data/"

