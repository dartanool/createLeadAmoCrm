# Отправка заявок с сайта в AmoCRM

- Форма из 4 полей: имя, email, телефон, цена.
- Создание контакта в AmoCRM (имя, email, телефон).
- Создание сделки с прикрепленным контактом и ценой.
- Передача в сделку дополнительного поля: «пользователь провёл на сайте > 30 секунд» (boolean).

# Требования

- PHP 8.1+
- Composer
- Аккаунт в AmoCRM
- Созданная интеграция в AmoCRM

# Установка

1. Клонируйте репозиторий:

   ```bash
   git clone <URL-РЕПОЗИТОРИЯ>
   cd <ПУТЬ-К-ПРОЕКТУ>
   ```
2. Установите зависимости:

   ```bash
   composer install
   ```
3. Настройте переменные окружения.

   Создайте файл `.env` в корне проекта:

   ```env
   AMOCRM_DOMAIN=your-subdomain.amocrm.ru
   AMOCRM_ACCESS_TOKEN=YOUR_ACCESS_TOKEN
   AMOCRM_TIME_FIELD_ID=123456
   AMOCRM_PHONE_FIELD_ID=123456
   AMOCRM_EMAIL_FIELD_ID=123456
   ```

# Запуск

Из корневой папки проекта:

```bash
php -S localhost:8000
```

Откройте в браузере:

```text
http://localhost:8000/public/index.html
```
