Краткое руководство

1. Склонируйте приложение: git clone https://github.com/incursionN1/Symfony_test1.

2. Перейдите в ресурсы: cd Symfony_test1/src.

3. Выполните команду composer install.

4. По необходимости отредактируйте .env, указав свои настройки для подключения к БД: DATABASE_URL="mysql://root:rootpassword@db/test?serverVersion=8.0&charset=utf8mb4".

5. Вернитесь в папку с проектом: cd .. . Запустите контейнеры: docker-compose up -d.

6. Создайте БД с именем «test».

7. Для запуска миграции используйте команду docker-compose exec app php bin/console doctrine:migrations:migrate.

8. Для заполнения тестовыми данными используйте docker-compose exec app php bin/console ImportCsvTenders.

Документация

Базовый URL: http://localhost/

phpMyAdmin: http://localhost:8080/

Получение списка тендеров с пагинацией и фильтрацией

URL: /api/rest/tenders/

Метод: GET

Параметры запроса:

page        (integer)	  Необязательный   - Номер страницы (по умолчанию 1)

limit	    (integer)	  Необязательный   - Количество элементов на странице (по умолчанию 10)

name	    (string)	  Необязательный   - Фильтр по названию тендера (частичное совпадение)

date_from	(string)      Необязательный   - Начальная дата для фильтрации по дате обновления (формат: dd.mm.YYYY HH:MM:SS)

date_to	    (string)	  Необязательный   - Конечная дата для фильтрации по дате обновления (формат: dd.mm.YYYY HH:MM:SS)

Пример запроса: GET http://localhost/api/rest/tenders/?page=1&limit=2&name=Годовой&date_from=13.08.2022 19:27:12&date_to=15.08.2022 19:22:00


{
    "items": [
        {
            "id": 3,
            "externalCode": "152467230",
            "number": "16887-3",
            "name": "Годовой конкурс на поставку конвейерных роликов 2018-2019",
            "dateUpdate": "14.08.2022 19:25:14",
            "status": {
                "id": 1,
                "name": "Закрыто"
            }
        },
        {
            "id": 5,
            "externalCode": "152466906",
            "number": "17358-2",
            "name": "Текстолит толщина 12,0-15,0 ММ",
            "dateUpdate": "14.08.2022 19:25:13",
            "status": {
                "id": 3,
                "name": "Открыто"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "total_pages": 698,
        "total_items": 1395,
        "items_per_page": 2
    }
}
Получение информации о конкретном тендере

URL: /api/rest/tenders/{id}

Метод: GET

Параметры пути:

id	(integer)	обязательный -	ID тендера

Пример запроса: GET http://localhost/api/rest/tenders/5431

{
    "id": 54,
    "external_code": "152466838",
    "number": "17511-2",
    "name": "Поставка трубы в адрес ОАО Компания г.Волхов ЗАПРОС СКИДКИ",
    "date_update": "14.08.2022 19:25:12",
    "status": {
        "name": "Закрыто"
    }
}
Создание нового тендера

URL: /api/rest/tenders/

Метод: POST

Тело запроса:

external_code  (string)	  обязательный	Внешний код тендера	

number	       (string)	  обязательный	Номер тендера	

name	         (string)	  обязательный	Название тендера	

date_update	   (string)	  обязательный	Дата обновления (формат: dd.mm.YYYY HH:MM:SS)

status	       (integer)	обязательный	ID статуса тендера

Пример запроса:POST http://localhost/api/rest/tenders/

Content-Type: application/json

{
    "external_code": "1",
    "number": "1",
    "name": "1",
    "date_update": "2025-05-07T00:00:00+00:00",
    "status": 1
}
Ответ:
{
    "id": 5431,
    "external_code": "1",
    "number": "1",
    "name": "1",
    "date_update": "07.05.2025 00:00:00",
    "status": {
        "name": "Закрыто"
    }
}
