# Модуль оплаты beGateway для 1C-Битрикс

## Установка модуля

  * Создайте резервную копию вашего магазина и базы данных
  * Скачайте архив модуля [bitrix-begateway.zip](https://github.com/beGateway/bitrix-payment-module/raw/master/bitrix-begateway.zip) или [bitrix-begateway-windows-1251.zip](https://github.com/beGateway/bitrix-payment-module/raw/master/bitrix-begateway-windows-1251.zip), если у вас 1С-Битрикс запущен в кодировке Windows-1251
  * Распакуйте архив и скопируйте каталог `begateway.payment` в каталог
  `<1C-Bitrix/bitrix/modules/`
  * Зайдите в зону 1C-Битрикс администратора и выберите меню
  `Marketplace -> Установленные решения`
  * Установите модуль __Модуль оплаты beGateway__. Будет создана платежная система с обработчиками.

## Настройка модуля

  * Зайдите в зону 1C-Битрикс администратора и выберите меню `Магазин -> Настройки -> Платёжные системы`
  * Добавьте и настройте платёжную систему с обработчиком __beGateway__
  * Введите в полях _Домен платёжного шлюза_, _Домен страницы оплаты_, _ID магазина_, _Ключ магазина_ и _Публичный ключ магазина_ значения, полученные от вашей платежной компании
  * Выберите в _Тип транзакции_ тип транзакции _Оплата_ (немедленное списание средств с карты) или _Авторизация_ (блокировка средств на карте). Уточните у вашей платёжной компании поддерживается ли с вашим банком-эквайером тип _Авторизация_.
  * Укажите в _Адрес для уведомлений_ адрес страницы для уведомления, где был размещен и настроен компонент `sale.order.payment.receive`. В параметрах компонента указать тип плательщика и созданную платежную систему
  * Укажите в _Адрес при успешной оплате_ адрес страницы, куда будет перенаправлен покупатель в случае успешной оплаты.
  * Укажите в _Адрес при не успешной оплате_ адрес страницы, куда будет перенаправлен покупатель в случае неуспешной оплаты.
  * Укажите в _Адрес при ошибке оплаты_ адрес страницы, куда будет перенаправлен покупатель в случае, если возникнет ошибка в процесс оплаты
  * Задайте в _CSS_ CSS стили для переопределения дизайна страницы оплаты
  * Задайте параметры и их свойства, из которых будут взяты данные покупателя для передачи в платёжную систему.
  * Нажмите _Сохранить_

## Настройка дизайна

### Стиль кнопки Оплатить

Кнопка Оплатить использует стандартные CSS классы `btn btn-primary`:

```html
<div id="begateway-wrapper">
  <button class="btn btn-primary" onclick="payment();">Оплатить</button>
</div>
```

Чтобы переопределить стиль кнопки задайте свои стили для CSS-классов

```CSS
#begateway-wrapper .btn
#begateway-wrapper .btn-primary
```

### Стиль виджета

#### Стили, используемые в виджете

| Имя стиля                |
|--------------------------|
| widget                   |
| header                   |
| headerPrice              |
| headerDescription        |
| headerDescriptionText    |
| headerClose              |
| footer                   |
| footerText               |
| footerLink               |
| footerSecurity           |
| main                     |
| methodsMenu              |
| methodsMenuText          |
| methodsMenuCard          |
| methodsMenuCardText      |
| methodsMenuList          |
| methodsMenuListMethod    |
| methodsMenuGrid          |
| methodsMenuGridMethod    |
| cardsMenu                |
| cardsMenuText            |
| cardsMenuCard            |
| cardsMenuCardText        |
| card                     |
| cardSides                |
| cardFace                 |
| cardFaceContent          |
| cardBack                 |
| cardBackMagneticLine     |
| cardBackCVC              |
| cardBackCVCText          |
| cardBackCVCInput         |
| cardPoints               |
| cardCustomer             |
| cardCustomerField        |
| cardButton               |
| eripContent              |
| eripTitle                |
| eripOrder                |
| eripOrderTitle           |
| eripOrderNumber          |
| eripBanks                |
| eripBanksTitle           |
| eripQRCode               |
| eripBanksComment         |
| eripBanksLinks           |
| eripBanksBank            |
| eripBanksMore            |
| paymentResult            |
| paymentResultStatus      |
| paymentResultStatusText  |
| paymentResultDetails     |
| paymentResultDetailsText |
| paymentResultButton      |
| method                   |
| methodContent            |
| methodTitle              |
| methodForm               |
| methodButton             |
| methodWaiting            |
| phoneLabel               |
| inputGroup               |
| inputGroupField          |
| inputGroupSelect         |
| stepBack                 |
| stepBackText             |

#### Кастомизируемые CSS свойства

Никакие другие CSS свойства не поддерживаются.

| Параметр        | Соответствующее CSS свойство |
|-----------------|------------------------------|
| color           | color                        |
| backgroundColor | background-color             |
| border          | border                       |
| borderRadius    | border-radius                |
| fontFamily      | font-family                  |
| fontSize        | font-size                    |
| fontSmoothing   | font-smoothing               |
| fontStyle       | font-style                   |
| fontVariant     | font-variant                 |
| fontWeight      | font-weight                  |
| lineHeight      | line-height                  |
| letterSpacing   | letter-spacing               |
| margin          | margin                       |
| padding         | padding                      |
| textAlign       | text-align                   |
| textDecoration  | text-decoration              |
| textShadow      | text-shadow                  |
| textTransform   | text-transform               |

Значениями CSS свойств могут быть текстовые значения совместимые с CSS синтаксисом (см. пример выше).

Структуру html-документа виджета с используемыми стилями можно посмотреть с помощью Developer Tools браузера (Inspect element).

Пример

```javascript
header: {
  backgroundColor: '#fff',
  border: 'none'
},
headerPrice: {
  color: '#fff'
},
footer: {
  backgroundColor: '#fff',
  border: 'none'
},
cardButton: {
  backgroundColor: '#26d893',
  border: 'none'
},
methodButton: {
  backgroundColor: '#26d893',
  border: 'none'
},
paymentResultButton: {
  backgroundColor: '#26d893',
  border: 'none'
}
```

## Тестовые данные

Если вы настроите модуль со следующими значениями

  * Домен платёжного шлюза `demo-gateway.begateway.com`
  * Домен страницы оплаты `checkout.begateway.com`
  * Id магазина `361`
  * Секретный ключ магазина `b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d`
  * Публичный ключ магазина `MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArO7bNKtnJgCn0PJVn2X7QmhjGQ2GNNw412D+NMP4y3Qs69y6i5T/zJBQAHwGKLwAxyGmQ2mMpPZCk4pT9HSIHwHiUVtvdZ/78CX1IQJON/Xf22kMULhquwDZcy3Cp8P4PBBaQZVvm7v1FwaxswyLD6WTWjksRgSH/cAhQzgq6WC4jvfWuFtn9AchPf872zqRHjYfjgageX3uwo9vBRQyXaEZr9dFR+18rUDeeEzOEmEP+kp6/Pvt3ZlhPyYm/wt4/fkk9Miokg/yUPnk3MDU81oSuxAw8EHYjLfF59SWQpQObxMaJR68vVKH32Ombct2ZGyzM7L5Tz3+rkk7C4z9oQIDAQAB`
то вы сможете уже
осуществить тестовый платеж в вашем магазине. Используйте следующие
данные тестовой карты:

  * номер карты __4200000000000000__
  * имя на карте __John Doe__
  * месяц срока действия карты __01__, чтобы получить успешный платеж
  * месяц срока действия карты __10__, чтобы получить неуспешный платеж
  * CVC __123__

## Примечания

Разработанно и протестировано с 1С-Битрикс 15.5.x/16.0.x/20.200.300
