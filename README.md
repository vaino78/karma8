# Комментарий к тестовому заданию

В рамках сервсиса существауют две функциональности — валидация e-mail адресов 
пользователей, и отправка им уведомлений.

Каждая из этих функциональностей реализована собственной парой скриптов, работа
которых организована одинаково. Это постоянно работающие процессы, взаимодействующие
между собой через импровизированную «очередь», в виде таблиц заданий:
- `notification` — для обработки уведомлений;
- `validation` — для валидации адресов.

Скрипты находятся в директории `bin` и представляют собой пары: `*observer` 
заполняет таблицу заданий, `*consumer` эти задания выполняет. Предполагается,
что скрипты должны работать в несколько потоков парраллельно, чтобы обеспечить
приемлемую скорость работы приложения.

В частности, например, если всего 20% пользователей имеют подписку (т.е. 1_000_000), и все они
подписались в один день, и если предположить, что время работы функции отправки
письма `send_mail()` максимальное, то с таким объемом уведомлений справились бы
примерно 112 процессов `bin/notification_consumer.php`.

Аналогично — если все 20% пользователей подписались в один день, никто из пользователей
не подтвердил свой e-mail, и если функция валидации `check_email()` будет работать
максимальное время в 60 секунд, с таким объемом справились бы 694 процесса 
`bin/validation_consumer.php`.

В нормальной эксплуатации следует отталкиваться от средних значений нагрузки
и наращивать мощности для обработки пиковых значений.

Вообще, здесь более эффективной была бы реализация на каком-либо асинхронном 
языке, потому что основное время занято ожиданием. Асинхронность можно бы
устроить и на php, но из коробки он это не умеет.

В таблице `user` внесены изменения:

- добавлен явный первичный ключ `id`, потому что он более эффективен при
  использовании, чем имя пользователя;

- добавлено поле `registerts`, которое должно содержать время и дату регистрации,
  чтобы дать пользователю возможность подтвердить свой адрес самостоятельно,
  и не производить дорогостоящую валидацию такого адреса. Данные этой колонки
  использует `bin/validation_observer.php`, когда отбирает адреса для валидации. 
  Значение времени ожидания задается переменной окружения `VALIDATION_OBSERVER_CONFIRMATION_GAP`;

- добавлены индексы для оптимизации производительности.

Скрипт `bin/notification_observer.php` принимает на вход аргумент — количество
секунд, за которое необходимо отправить уведомление. Для обеспечения условия 
отправки уведомлений за сутки и трое суток должно быть запущено как минимум два
экземпляра данного скрипта с аргументами `86400` и `259200` соответственно.

Вообще, конечно, решение получилось недостаточно простым в реализации, и более процедурным,
нежели функциональным.

