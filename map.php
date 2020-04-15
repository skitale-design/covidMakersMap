<?php 

// php function to convert csv to json format
function csvToJson($fname) {
    // open csv file
    if (!($fp = fopen($fname, 'r'))) {
        die("Can't open file...");
    }
    
    //read csv headers
    $key = fgetcsv($fp,"1024",",");
    
    // parse csv rows into array
    $json = array();
        while ($row = fgetcsv($fp,"1024",",")) {
        $json[] = array_combine($key, $row);
    }
    
    // release file handle
    fclose($fp);
    // encode array to json
    return json_encode($json);
}

$fromCsv = csvToJson("json/data.csv");
//echo $fromCsv;//$file = ""

file_put_contents("json/dataFromCsv.json", $fromCsv);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Оптимальное добавление множества меток</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--
        Укажите свой API-ключ. Тестовый ключ НЕ БУДЕТ работать на других сайтах.
        Получить ключ можно в Кабинете разработчика: https://developer.tech.yandex.ru/keys/
    -->
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru-RU&amp;apikey=d58880c0-0702-4b89-979f-4c386b4a30d4" type="text/javascript"></script>
    <script src="https://yandex.st/jquery/2.2.3/jquery.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="https://yastatic.net/bootstrap/3.3.4/css/bootstrap.min.css">
	<style>
        html, body, #map {
            width: 100%; height: 100%; padding: 0; margin: 0;
        }
        a {
            color: #04b; /* Цвет ссылки */
            text-decoration: none; /* Убираем подчеркивание у ссылок */
        }
        a:visited {
            color: #04b; /* Цвет посещённой ссылки */
        }
        a:hover {
            color: #f50000; /* Цвет ссылки при наведении на нее курсора мыши */
        }
    </style>
</head>
<body>
<div id="map"></div>

<script>
ymaps.ready(init);
$g = "g";
$data = "dataS";
function init () {
    var myMap = new ymaps.Map('map', {
            center: [55.76, 37.64],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        }),
        objectManager = new ymaps.ObjectManager({
            // Чтобы метки начали кластеризоваться, выставляем опцию.
            clusterize: true,
            // ObjectManager принимает те же опции, что и кластеризатор.
            gridSize: 32,
            clusterDisableClickZoom: true
        });

    // Чтобы задать опции одиночным объектам и кластерам,
    // обратимся к дочерним коллекциям ObjectManager.
    objectManager.objects.options.set('preset', 'islands#greenDotIcon');
    objectManager.clusters.options.set('preset', 'islands#greenClusterIcons');
    // $data.features.forEach(function(el){console.log(el);});
    // myMap.geoObjects.add(objectManager);
        
 

    $.ajax({
        url: "json/dataFromCsv.json"
    }).done(function(data) {
        $data = data;
        
        //alert(data.features[0].geometry.coordinates);
        objectManager.add(data);
        $data.forEach(function(el){
            //console.log(el);
            //$arr = el.геокод.split(", ");
            //console.log($arr);
            console.log(el.адрес);
            ymaps.geocode(el.адрес).then(function (res) {
                $g = res.geoObjects.get(0).geometry._coordinates;
                console.log("$g = ".concat(res));
                $id = el.id;
                //console.log($id);
                $щитков = parseInt(el.щитков);
                $заколок = parseInt(el.заколок);
                $боксов = parseInt(el.боксов);
                $telegram = el.telegram;
                //$iconCaption = el.properties.iconCaption;
                //$description = el.properties.description;
                var myPieChart = new ymaps.Placemark($g,
                                {
                                    // Данные для построения диаграммы.
                                    data: [
                                        {weight: $щитков, color: '#ed7070'},
                                        {weight: $заколок, color: '#268E00'},
                                        {weight: $боксов, color: '#4F4FD9'}
                                    ],
                                   // iconCaption: $iconCaption,
                                    //iconContent: $id,
                                   balloonContent: $telegram
    
                                }, {
                                    // Зададим произвольный макет метки.
                                    iconLayout: 'default#pieChart',
                                    // Радиус диаграммы в пикселях.
                                    iconPieChartRadius: 20,
                                    // Радиус центральной части макета.
                                    iconPieChartCoreRadius: 10,
                                    // Стиль заливки центральной части.
                                    iconPieChartCoreFillStyle: '#ffffff',
                                    // Cтиль линий-разделителей секторов и внешней обводки диаграммы.
                                    iconPieChartStrokeStyle: '#ffffff',
                                    // Ширина линий-разделителей секторов и внешней обводки диаграммы.
                                    iconPieChartStrokeWidth: 3,
                                    // Максимальная ширина подписи метки.
                                    iconPieChartCaptionMaxWidth: 200
                                });
                myMap.geoObjects.add(myPieChart);
            });


        }); 
    });
    
}
</script>
</body>
</html>


