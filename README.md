# orgdirectory-api

REST API приложение для управления справочником организаций, зданий и видов деятельности.

## 📚 Документация API

Документация Swagger доступна по корневому пути: `/`

## Технологии

-   **Backend**: Laravel 12
-   **База данных**: MySQL
-   **Документация**: Swagger
-   **Тесты**: PHPUnit

## 🚀 Функционал

### Основны сущности

-   Организации (с названием, телефонами, привязкой к зданию и видам деятельности)
-   Здания (с адресом и географическими координатами)
-   Виды деятельности (древовидная структура с ограничением вложенности 3 уровня)

### API методы:

Получение организаций по зданию

```bash
api/buildings/{building}/organizations
```

Получение организаций по виду деятельности

```bash
api/activities/{activity}/organizations
```

Поиск организаций в географической области

```bash
api/organizations/nearby
```

Получение информации об организации

```bash
api/organizations/{organization}
```

Поиск по дереву видов деятельности

```bash
api/organizations/search/activity/{activity}
```

Поиск организаций по названию

```bash
api/organizations/{organization}
```

### Использование API

-   Все запросы требуют статического API ключа, который передается в заголовке:
    `X-API-KEY : your-static-api-key`
-   Для установки статического API ключа в .env файл используйте команду:

```bash
php artisan app:set-api-key
```

### Тестирование

Добавлены 2-3 для всех роутов.
Для запуска тестирования используйте команду:

```bash
php artisan test
```

## Установка

1. Клонировать репозиторий:

```bash
git clone https://github.com/ManasArs13/orgdirectory-api.git && cd orgdirectory-api
```

2. Установите зависимости:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

3. Запустити Laravel Sail:

```bash
sail build --no-cache
./vendor/bin/sail composer install
./vendor/bin/sail up -d
```

4. Запустить миграции (для удобства тестирования созданы тестовые данные, используя Factories и Seeders):

```bash
php artisan migrate --seed
```

5. Установить статический API ключ:

```bash
php artisan app:set-api-key
```
