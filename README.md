# Widget

Прием оплаты в магазине в прозрачном режиме, как будто оплата происходит на странице самого магазина. 

Проект с документацией для магазина - https://gitlab.paypoint.pro/web/services/widget-documentation

### Запуск в докере

```
docker-compose build
docker-compose up -d
cp config/config.sample.php config/config.php
```

Далее настройте `config/config.php`.

### Использование

Адрес виджета, который должен магазин вписать у себя:
```
http://host:3300
```
где вместо host - домен виджета.

Этот же урл нужно указать в документации (есть же конфиг в доке???)
