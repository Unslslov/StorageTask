## 🚀 Установка
### Клонируйте репозиторий:
```bash
git clone https://github.com/Unslslov/StorageTask.git 
cd storagetask
```

### Установите зависимости:
```bash
composer install
```
### Настройте .env файл:
```bash
cp .env.example .env
php artisan key:generate
```
### Выполните миграции и запустите команду импорта:
```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan incomes:import
```
### Запустите сервер:
```bash
php artisan serve
```

## 🔧 Функционал
- **Мульти-аккаунтность**
- **Поддержка различных API-сервисов**
- **Типы аутентификации**
- **Автоматическое обновление данных**

## 🗄️ Структура базы данных
### Основные сущности:
- **Companies - компании**

- **Accounts - аккаунты компаний**

- **ApiServices - поддерживаемые API-сервисы**

- **TokenTypes - типы токенов (api-key, bearer, login-password)**

- **Tokens - токены доступа к API**

### Таблицы данных:
- **incomes - доходы**

- **stocks - остатки на складах**

- **sales - продажи**

- **orders - заказы**

Все таблицы данных содержат поле account_id для разделения данных между аккаунтами.

## 🌐 Доступные API-маршруты
Все маршруты защищены с помощью middleware api.key.

### Доступные endpoints:
- **GET /api/incomes - получение доходов**

- **GET /api/stocks - получение остатков**

- **GET /api/sales - получение продаж**

- **GET /api/orders - получение заказов**

### Пример запроса:
```bash
http://127.0.0.1:8000/api/incomes?dateFrom=2000-02-10&dateTo=2027-05-29&page=1&key=API_KEY&limit=100
```

## ⚙️ Консольные команды

### Управление сущностями:
```bash
# Добавление компании
./vendor/bin/sail artisan company:add

# Добавление аккаунта
./vendor/bin/sail artisan account:add

# Добавление типа токена
./vendor/bin/sail artisan token-type:add

# Добавление API сервиса
./vendor/bin/sail artisan api-service:add

# Добавление токена
./vendor/bin/sail artisan token:add
```
### Импорт данных:
```bash
# Полный импорт данных для всех аккаунтов
./vendor/bin/sail artisan app:fetch-all-data

# Импорт доходов для конкретного аккаунта
./vendor/bin/sail artisan incomes:import --account=1 --token=1

# Импорт заказов
./vendor/bin/sail artisan orders:import --account=1 --token=1

# Импорт продаж
./vendor/bin/sail artisan sales:import --account=1 --token=1

# Импорт остатков
./vendor/bin/sail artisan stocks:import --account=1 --token=1
```

## 🔐 Настройка аккаунтов и токенов

1. Создайте компанию: company:add

2. Добавьте аккаунт к компании: account:add

3. Создайте типы токенов: token-type:add

4. Добавьте API сервисы: api-service:add

5. Настройте токены доступа: token:add

## ⚠️ Обработка ошибок
Приложение включает расширенную обработку ошибок:

- **Автоматический повтор запросов при ошибке 429**

- **Экспоненциальная задержка между повторами**

- **Подробное логирование в консоль и файлы**

## 📊 Планировщик задач
Данные автоматически обновляются дважды в день в 6:00 и 18:00 через команду:
```bash
./vendor/bin/sail artisan app:fetch-all-data
```
