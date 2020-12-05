<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'DataBase.php';



#####################################################################################
# Генерирует ссылки для парсинга с помощью 3 аргументов
# model => string  					= марка(бренд) машины
# city => string  					= название города
# how_many_pages => integer  		= количество страниц которые нужно спарсить
# return => array  					= возвращает один массив со всеми данными из функции parse!
#####################################################################################
function generate_url($model = 'mercedes-benz', $city = 'almaty', $how_many_pages=1){ #=> array()
	$data = [];
	for ($i=1; $i<=$how_many_pages; $i++){
		$subject = "https://kolesa.kz/cars/${model}/${city}/?page=${i}";
		foreach (parse($subject) as $index) {
			#print_r($index);
			
			if (checkUnique($index['car_id'])){
				continue;
			} else {
				setDataOnDB($index);
			}
		}
	}
	header('Location: /index.php ');
}



function onePageParse($url){
	$pattern_info = '/<dd class=.*?>.*?<\/dd>/us';
	$pattern_block_info = '/<dl class.*?<\/dl>/us';
	$allinfo_pattern = '/itemprop="description">(.*?)<\/div>/gus';
	$bny = '/class="offer__title">.*?<\/h1>/su';
	$intotag_pattern = '/>(.+?)</su';
	$pattern_img = '/src="(.*?)\.jpg/m';
	$pattern_price = '/<div class="offer__price">(.+?)<span class/su';
	
	if (preg_match( '/[A-Z|a-z]/', $url )){
		$url = $url;
	} else {
		$url = 'https://kolesa.kz/a/show/' . $url;
	}

	
	if (get_headers($url)[0]!='HTTP/1.1 200 OK'){
		header('Location: /error.php ');
	} 

	$page = file_get_contents($url);
	
	preg_match($pattern_price, $page, $price_arr);	# Поиск цены машины
	$price = trim($price_arr[1], " \x00..\x1F");	# Удаление пробелов в начале и конце строки
	$price = str_replace("&nbsp;", '', $price);		# Удаление неразрывных пробелов внутри текста
	$result ['price'] = $price;
	preg_match('/[0-9]+/', $url, $test);
	$result['car_id'] = ($test[0]);
	#print_r($price[1]);
	echo('<br>');
	
	preg_match($bny, $page, $res2); 
	$temp[]=$res2[0];   #результать регулярки. Должен найти сегмент HTML кода с инфой 'BRAND','NAME MODEL', 'YEAR'
	
	preg_match_all($pattern_block_info, $page, $res4);
	$temp2[]=$res4[0];  #результать регулярки. Должен найти сегмент HTML кода доп информацией
	#ГОРОД => value,  КУЗОВ => value, ОБЪЕМ ДВИГАТЕЛЯ => value, КОРОБКА ПЕРЕДАЧ => value, РУЛЬ, ЦВЕТ и тд.
	
	foreach ($temp as $key) {
		preg_match_all($intotag_pattern, $key, $res3); #регулярка находит текст между тегов
		preg_match($pattern_img,$page,$img);
		$result ['img'] = $img[1] . ".jpg";
		$result ['brand'] = $res3[1][1];
		$result ['name'] = $res3[1][3];
		$result ['year'] = trim($res3[1][5], " \x00..\x1F"); #Функция trim удаляет лишние пробелы в начале и конце строки		
	}

	foreach ($temp2 as $key) {
		foreach ($key as $value) {
			preg_match_all('/title="(\s*.*?\s*)"/u', $value, $res5); #находит ключи для массива ГОРОД, КУЗОВ, ОБЪЕМ ДВИГАТЕЛЯ, КОРОБКА ПЕРЕДАЧ, РУЛЬ, ЦВЕТ и тд.
			preg_match_all('/class=".*?">(\s*.*?\s*)<\/dd>/u', $value, $res6); #находит значения для ключей
			$result[trim($res5[1][0], " \x00..\x1F")] = trim($res6[1][0], " \x00..\x1F");
		}
		$data[] = $result;
	}
	return ($data[0]);

}


function parse($url){
	$pattern_id = '/(advert-)([\d]+)/su';
	$pattern_info = '/<dd class=.*?>.*?<\/dd>/us';
	$pattern_block_info = '/<dl class.*?<\/dl>/us';
	$allinfo_pattern = '/itemprop="description">(.*?)<\/div>/gus';
	$bny = '/class="offer__title">.*?<\/h1>/su';
	$intotag_pattern = '/>(.+?)</su';
	$pattern_img = '/src="(.*?)\.jpg/m';
	$pattern_price = '/<div class="offer__price">(.+?)<span class/su';
	$all_data = [];

	
	
	$subject = file_get_contents($url);
	
	preg_match_all($pattern_id, $subject, $result);		#поиск id объявлении
	
	$car_id = $result[2]; /// only id
	
	foreach ($car_id as $value) {
		$all_data[] = onePageParse($value);
	}
	return $all_data;
}




if ($_POST){
	if ($_POST['mod'] == 'many'){
		$eng_name_city = ['Алматы'=>'almaty', 'Нур-Султан (Астана)' => 'nur-sultan', 'Шымкент' => 'shymkent',
		'Актобе' => 'aktobe', 'Караганда' => 'karaganda', 'Уральск' => 'uralsk'];
		$city = $eng_name_city[($_POST)['City']];
		$brand =strtolower(($_POST)['Brand']);
		$pages = ($_POST)['pages'];
		#print_r($_POST);
		generate_url($brand, $city, $pages);
	} elseif ($_POST['mod'] == 'single') {
		#print_r($_POST);
		try{
			$data = onePageParse(($_POST)['url']);
			if (checkUnique($data['car_id'])){
				echo('Такая машина уже есть в БД');
			} else {
				setDataOnDB($data);
				header('Location: /index.php ');
			}
		} catch (Exception $e) {
			echo 1;
		}
	}
}

#generate_url($model = 'bmw', $city = 'uralsk', $how_many_pages=2);

#print_r(onePageParse('https://kolesa.kz/a/show/109833360'));

?>