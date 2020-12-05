<?php
include ("DataBase.php");

ini_set('display_errors', 1); // Для вывода ошибок, в настройках я не включал вывод ошибок php
ini_set('display_startup_errors', 1); // Для вывода ошибок, в настройках я не включал вывод ошибок php
error_reporting(E_ALL); // Для вывода ошибок, в настройках я не включал вывод ошибок php


$sp = ' '; // Просто 1x пробел)




/*
Зачем тут проверка на гет запрос, хз. По-любому post запрос не настроен....

Усли у вас customs_status вместо INT прописан VARCHAR, лучше удали строку(27)
                            ⇣
27: all_data['customs_status'] = $all_data['customs_status']==1 ? 'Да' : 'Нет'; 


getDataFromDBbyID возвращает массив в массив, из-за этого сразу пишу индекс ноль. Лучше сами проверьте что он возвращает
скорее всего [0 =>[id=>12, $id_car => 1212134, brand => name1, model => bmx]]
*/
if ($_GET){
    $all_data = (getDataFromDBbyID($_GET['id'])[0]);
    $all_data['customs_status'] = $all_data['customs_status']==1 ? 'Да' : 'Нет';
}


/*
Форматирование цен (1950000 -> 1 950 000 ₸)
*/
function format_price($value, $unit = '₸')
{
	if ($value > 0) {
		$value = number_format($value, 2, ',', ' ');
		$value = str_replace(',00', '', $value);

		if (!empty($unit)) {
			$value .= ' ' . $unit;
		}
	}
	return $value;
}


/* 
get_other_photos() Парсер остальных фотографии объявлении имея только первую ссылку на фотографию.
При входе получает url картинки ($all_data['image_link])=> https://photos-kl.kcdn.kz/webp/f1/f1efe3e1-3a56-49e8-8d33-4441e4da100e/1-750x470.jpg
Парсер пытается изменить нумерацию картинки 1-750x470.jpg на 2-750x470.jpg, 3-750x470.jpg, 4-750x470.jpg и тд.


Функцией get_headers(url) отправляю get запрос на url и сравнивает результаты возвращенного ответа array[0] == 'HTTP/1.1 200 OK'
'HTTP/1.1 200 OK' -> статус код страницы, если ответ не совпадает, цикл прекращает работу командой break.

68:     Для изменении нумерации, использую команду strrev($string), а потом строку разбиваю на массив
71:     [12] = нумерацией страницы.. (P.S. возможно скрипт не сможет найти больше 10 фотографии(в этом уверен на 99.9%), 
        для этого придется добавлять условия на $i>9, и переходить на [11] или [13] индексы)

72 & 73:    Команда implode($array) собирает строку из массива, strrev($new_string) вернуть изначальную реверс строку в порядок

P.S. без команды break функция будет загружать страницу на несколько раз дольше.
*/
function get_other_photos(){
    global $all_data;
    $photo = [];  // Массив аккумулятор для записи нормальных url
    $res = strrev($all_data['image_link']);
    for ($i=2;$i<30;$i++){
        $temp = str_split($res);
        $temp[12] = $i;
        if (get_headers(strrev(implode($temp)))[0]=='HTTP/1.1 200 OK'){
            $photo [] = strrev(implode($temp));
        } else {
            break;
        }
    }
    return $photo;

}

/*
Функция для вставки ключа и значении в шаблон $tr для get_template_tr()
P.S. тег table

$tr => EOD = heredoc строка
*/
function get_tr_tag($key, $value) {
    $tr = <<<EOD
            <tr>
                <td>$key</td>
                <td>$value</td>
            </tr>
        EOD;
    return $tr;
}


/*
Функция для для записи в html коде блок кода для table

$continues_key = массив ключей которые не надо выводить (На самом деле лучше подправить фукнцию DataBase.getDataFromDBbyID() чтобы он
не забирал все поля с таблицы БД)

$translate_arr = поля которые желательно перевести на русский лад,

128:    in_array($string, $array) = Проверяет ключ массива $alldata с $continues_key или $value=='None', если True пропускает 1 ход цикла, без дальнейших операции

130:    array_key_exists() проверка ключей с массивом translate_arr и автоподставление русской версии

131 & 132:    проверка на ключ==price. Если True запись цены через format_price()
*/
function get_template_tr_tag($alldata) {
    $continues_key = ['id', 'id_car', 'image_link', 'append_time', 'description'];
    $translate_arr = [
        'brand' => 'Производитель',
        'model' => 'Модель',
        'year' => 'Год выпуска',
        'city' => 'Город',
        'bodystyle' => 'Кузов',
        'mileage' => 'Пробег',
        'transmission' => 'Коробка передач',
        'drivetrain' => 'Привод',
        'price' => 'Цена',
        'customs_status' => 'Растоможен в Казахстане'
    ];
    foreach ($alldata as $key => $value) {
        if (in_array($key, $continues_key) || $value=='None'){
           continue;
        } elseif (array_key_exists($key, $translate_arr)) {
            if ($key=='price'){
                echo (get_tr_tag($translate_arr[$key], format_price($value)));
            } else {
                echo (get_tr_tag($translate_arr[$key], $value));
            }
        } else {
            echo (get_tr_tag($key, $value));
        }
    }
}

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title><?php echo $all_data['brand'],$sp, $all_data['model'],$sp ,$all_data['year'];?></title>
    <style>
        .styled-table {
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: sans-serif;
            min-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }

        .blur {
            text-shadow: 0px 0px 2px #000;
            color: transparent;
        }
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        td,th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #ccd;
        }
    </style>
</head>

<body class="">
    <nav class="navbar navbar-expand-md navbar-dark bg-info">
        <div class="container justify-content-center">
            <button class="navbar-toggler navbar-toggler-right border-0" type="button" data-toggle="collapse" data-target="#navbar9">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse text-center justify-content-center" id="navbar9">
                <ul class="navbar-nav">
                    <li class="nav-item mx-2 text-dark"> <a class="nav-link" href="#">Products</a> </li>
                    <li class="nav-item mx-2"> <a class="nav-link" href="#">FAQ</a> </li>
                    <li class="nav-item mx-2"> <a class="nav-link navbar-brand mr-0 text-white" href="/"><i class="fa d-inline fa-lg fa-stop-circle-o"></i>
                            <b> BRAND</b></a> </li>
                    <li class="nav-item mx-2"> <a class="nav-link" href="#">About us</a> </li>
                    <li class="nav-item mx-2"> <a class="nav-link" href="#">Contacts </a> </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="carousel slide" data-ride="carousel" id="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active"> <img class="d-block img-fluid w-100" src=<?php echo $all_data['image_link']; ?>>
                            </div>
                            <?php
                                $photos = get_other_photos();
                                foreach ($photos as $value) {
                                    $tm = <<<EOD
                                       <div class="carousel-item"> <img class="d-block img-fluid w-100" src=${value}></div>
                                    EOD;
                                    echo ($tm);}
                            ?>
                        </div>
                        <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev"> <span class="carousel-control-prev-icon"></span> <span class="sr-only">Previous</span> </a> <a class="carousel-control-next" href="#carousel" role="button" data-slide="next"> <span class="carousel-control-next-icon"></span> <span class="sr-only">Next</span> </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"> <a href="" class="active nav-link" data-toggle="pill" data-target="#tabone"><i class="fa fa-home"></i> Preview </a> </li>
                        <li class="nav-item"> <a class="nav-link" href="" data-toggle="pill" data-target="#tabtwo"><i class="fa fa-info-circle"></i> Info </a> </li>
                        <li class="nav-item"> <a href="" class="nav-link" data-toggle="pill" data-target="#tabthree"><i class="fa fa-address-book"></i> Contact </a> </li>
                    </ul>
                    <div class="tab-content mt-2">
                        <div class="tab-pane fade show active" id="tabone" role="tabpanel">
                            <p class=""><?php echo $all_data['brand'], $sp, $all_data['model']; ?></p>
                            <p class=""><?php echo $all_data['city']; ?></p>
                            <p class=""><?php echo format_price($all_data['price']); ?></p>
                        </div>
                        <div class="tab-pane fade" id="tabtwo" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table tblData"> <!--styled-table-->
                                    <tbody> <?php get_template_tr_tag($all_data); ?> </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tabthree" role="tabpanel"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        //$('tr').css('border', 'solid 3px red');
        $(document).ready(function() {
            $(".tblData tr:has(td)").hover(function(e) {
                $(this).css("cursor", "pointer");
                $(this).css('font-size', '20px');
                $(".tblData tr:has(td)").addClass('blur');
                $(this).removeClass('blur');
            },function(e) {
                $(".tblData tr:has(td)").removeClass('blur');
                $(this).css('font-size', '');
            });
        });
    </script>
</body>

</html>