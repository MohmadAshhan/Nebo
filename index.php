<head>
    <title>Определение адреса клика на карте с помощью обратного геокодирования</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--
        Укажите свой API-ключ. Тестовый ключ НЕ БУДЕТ работать на других сайтах.
        Получить ключ можно в Кабинете разработчика: https://developer.tech.yandex.ru/keys/
    -->
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=f1e42c05-1227-46e5-a8b3-e13b22af834c" type="text/javascript"></script>
<!--    <script src="event_reverse_geocode.js" type="text/javascript"></script>-->
    <style type="text/css">
        html, body {
            width: 100%;
            height: 95%;
            margin: 0;
            padding: 0;
            font-family: Arial;
            font-size: 14px;
        }
        #map {
            width: 100%;
            height: 95%;
        }
        .header {
            padding: 5px;
        }
    </style>
</head>
<body>

<p class="header">Кликните по карте, чтобы узнать адрес</p>
<div id="map" ></div>

</body>

<script>
    ymaps.ready(init);

    function init() {
        var myPlacemark,
            myMap = new ymaps.Map('map', {
                center: [55.753994, 37.622093],
                zoom: 9
            }, {
                searchControlProvider: 'yandex#search'
            });

        // Слушаем клик на карте.
        myMap.events.add('click', function (e) {
            var coords = e.get('coords');

            if (myPlacemark) {
                myPlacemark.geometry.setCoordinates(coords);
            }
            else {
                myPlacemark = createPlacemark(coords);
                myMap.geoObjects.add(myPlacemark);
                // Слушаем событие окончания перетаскивания на метке.
                myPlacemark.events.add('dragend', function () {
                    getAddress(myPlacemark.geometry.getCoordinates());
                });
            }
            getAddress(coords);
        });

        function createPlacemark(coords) {
            return new ymaps.Placemark(coords, {
                iconCaption: 'поиск...'
            }, {
                preset: 'islands#violetDotIconWithCaption',
                draggable: true
            });
        }

        function getAddress(coords) {
            myPlacemark.properties.set('iconCaption', 'поиск...');
            ymaps.geocode(coords).then(function (res) {
                var firstGeoObject = res.geoObjects.get(0);

                myPlacemark.properties
                    .set({
                        iconCaption: [
                            firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                            firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                        ].filter(Boolean).join(', '),
                        balloonContent: firstGeoObject.getAddressLine()
                    });
            });
        }
    }

</script>

</html>

