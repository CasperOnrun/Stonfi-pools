# Инструкция по настройке

## Структура монтирования

Проект настроен так, чтобы все данные хранились на диске хоста:

### 1. Код приложения (Yii2)
```
./ (текущая директория) → /var/www/html (в контейнерах)
```

Все изменения в коде на хосте **мгновенно** отражаются в контейнерах.

### 2. База данных MySQL
```
./docker/mysql/data/ → /var/lib/mysql (в контейнере mysql)
```

Все данные MySQL хранятся в `docker/mysql/data/` на диске хоста.

**Преимущества:**
- ✅ Данные сохраняются при пересборке контейнеров
- ✅ Можно делать бэкапы простым копированием папки
- ✅ Быстрый доступ к данным с хоста

## Первый запуск

```bash
# 1. Создать директорию для MySQL (автоматически создается через make)
mkdir -p docker/mysql/data

# 2. Запустить контейнеры
docker-compose up -d

# 3. Установить зависимости
docker-compose exec php composer install

# 4. Применить миграции
docker-compose exec php php yii migrate --interactive=0

# 5. Первая синхронизация
docker-compose exec php php yii pools/sync
```

**Или через Makefile:**
```bash
make up
make install
```

## Разработка

### Редактирование кода
Просто редактируйте файлы в текущей директории. Изменения сразу применяются:

```bash
# Пример: добавили новый метод в контроллер
nano controllers/PoolController.php
# Сохранили → обновите браузер, изменения уже работают
```

### Работа с БД

```bash
# Подключение к MySQL из контейнера
docker-compose exec mysql mysql -uroot -proot stonfi_pools

# Подключение с хоста (порт 3307)
mysql -h127.0.0.1 -P3307 -uroot -proot stonfi_pools

# Бэкап БД
docker-compose exec mysql mysqldump -uroot -proot stonfi_pools > backup.sql

# Восстановление БД
docker-compose exec -T mysql mysql -uroot -proot stonfi_pools < backup.sql
```

### Логи

```bash
# Все контейнеры
docker-compose logs -f

# Конкретный сервис
docker-compose logs -f php
docker-compose logs -f mysql
docker-compose logs -f cron

# Логи приложения Yii2
tail -f runtime/logs/app.log
tail -f runtime/logs/console.log
```

## Управление данными

### Бэкап данных MySQL (копирование директории)

```bash
# Остановить контейнер для безопасности
docker-compose stop mysql

# Создать бэкап
tar -czf mysql-backup-$(date +%Y%m%d).tar.gz docker/mysql/data/

# Запустить контейнер обратно
docker-compose start mysql
```

### Восстановление из бэкапа

```bash
# Остановить контейнер
docker-compose stop mysql

# Удалить текущие данные
rm -rf docker/mysql/data/*

# Восстановить из архива
tar -xzf mysql-backup-20241103.tar.gz

# Запустить контейнер
docker-compose start mysql
```

### Полная очистка

```bash
# Остановить и удалить контейнеры (данные сохранятся)
docker-compose down

# Удалить данные MySQL
rm -rf docker/mysql/data/*

# Удалить vendor и runtime
rm -rf vendor/ runtime/logs/* web/assets/*

# Или через Makefile
make clean
# И вручную удалить: rm -rf docker/mysql/data/
```

## Пересборка контейнеров

```bash
# Пересобрать образы
docker-compose build --no-cache

# Запустить заново
docker-compose up -d

# Переустановить зависимости (если нужно)
docker-compose exec php composer install
```

## Права доступа

### Если возникли проблемы с правами:

```bash
# Дать права на запись для PHP
sudo chown -R $(id -u):$(id -g) runtime/ web/assets/

# Дать права на запись для MySQL
sudo chown -R 999:999 docker/mysql/data/

# Или сделать исполняемым yii
chmod +x yii
```

## Переменные окружения

Отредактируйте `.env` файл:

```bash
cp .env.example .env
nano .env
```

Доступные переменные:
```env
DB_HOST=mysql
DB_NAME=stonfi_pools
DB_USER=root
DB_PASSWORD=root
STONFI_API_URL=https://rpc.ston.fi/
```

После изменения переменных перезапустите контейнеры:
```bash
docker-compose restart
```

## Производственное окружение

Для продакшена измените в файлах:

### web/index.php
```php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');
```

### config/web.php
Уберите debug и gii модули (они уже обернуты в `if (YII_ENV_DEV)`).

### docker-compose.yml
Для продакшена лучше использовать именованные volumes вместо bind mounts для MySQL.

