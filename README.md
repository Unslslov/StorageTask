## 🧰 Подключение к бд
### Адрес: 185.105.110.5
### Пароль: 133455oPS@ar
### Я использовал хостинг Макхост, чтобы обратиться к бд, нужно добавить там пользователя при помощи ip, который можно получить, например, через 2ip, свяжитесь со мной в тг(@unsolino), я постараюсь, как можно быстрее добавить вас

## 🔧 Функционал
- **(GET http://109.73.206.144:6969/api/incomes)** — Импорт данных из внешнего API
- **Хранение данных в базе данных**
- **api.key** - Защита маршрутов с помощью middleware  
- **Обработка пагинации и больших объёмов данных** — Авторизация через токены

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
php artisan migrate
php artisan incomes:import
```
### Запустите сервер:
```bash
php artisan serve
```

## 🌐 Доступные API-маршруты
Все маршруты защищены с помощью middleware api.key, то есть для доступа к ним необходимо передать корректный API-ключ.
Каждый маршрут возвращает данные в формате JSON.
### Получить список Income
```bash
http://127.0.0.1:8000/api/incomes?dateFrom=2000-02-10&dateTo=2027-05-29&page=1&key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie&limit=10
```
### Получить список Stocks
```bash
http://127.0.0.1:8000/api/stocks?dateFrom=1988-12-29&dateTo=2020-10-25&page=1&key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie&limit=10
```
### Получить список Sales
```bash
http://127.0.0.1:8000/api/sales?dateFrom=1974-12-29&dateTo=1987-10-25&page=2&key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie&limit=10
```
### Получить список Orders
```bash
http://127.0.0.1:8000/api/orders?dateFrom=1974-12-29&dateTo=1987-10-25&page=2&key=E6kUTYrYwZq2tN4QEtyzsbEBk3ie&limit=10
```
## 📥 Команда для импорта
Команда: incomes:import
Импортирует данные по приходам из внешнего API и сохраняет их в БД.

Как запустить:
```bash
php artisan incomes:import
```
Особенности:
- Обрабатывает пагинацию и большие объемы данных.
- Поддерживает автоматическое повторение запросов при получении ошибки 429 Too Many Requests.
- Использует транзакции при записи в БД для обеспечения целостности данных.
