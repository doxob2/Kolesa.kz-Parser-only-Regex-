<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class DB
{
    private static $instance = null;
    private static $host = '127.0.0.1';
    private static $user = 'boxxy';
    private static $pass = '123';
    private static $name = 'kolesa';

    private function __construct()
    {


    }
    
    public static function getInstance()
    {
        if(self::$instance === null)
        {
            self::$instance = self::getConnection();
        }
        return self::$instance;
        
    }
    
    private static function getConnection()
    {
        $host = self::$host;
        $db   = self::$name;
        $user = self::$user;
        $pass = self::$pass;
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dsn, $user, $pass, $opt);
    }
}


$pdo = DB::getInstance();

$pdo->query("CREATE TABLE IF NOT EXISTS cars(
    id INT(30) AUTO_INCREMENT PRIMARY KEY,
    image_link VARCHAR(100),
    id_car INT(30) UNIQUE,
    brand VARCHAR(55),
    model VARCHAR(55),
    year INT(10),
    city VARCHAR(55),
    bodystyle VARCHAR(55),
    mileage VARCHAR(55),
    transmission VARCHAR(55),
    drivetrain VARCHAR(55),
    price INT(30),
    description VARCHAR(250),
    customs_status INT(1),
    append_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)
");




function setDataOnDB($array){
    $pdo = DB::getInstance();
    $stmt = $pdo->prepare("INSERT INTO cars 
                (id_car, image_link, brand, model, bodystyle, mileage, transmission, drivetrain, year, city, price, description, customs_status)
                VALUES 
                (:id_car, :image_link, :brand, :model, :bodystyle, :mileage, :transmission, :drivetrain, :year, :city, :price, :description, :customs_status)");
    
    
    $stmt->execute([
            ':id_car' => $array['car_id'],
            ':image_link' => $array['img'],
            ':brand' => $array['brand'],
            ':model' => $array['name'],
            ':year' => $array['year'],
            ':city' => $array['Город'],
            /* 
            array_key_exists('Кузов', $array)==1 ? $array['Кузов'] : 'None'   ----- тернарный оператор
            
            if() ? TRUE : FALSE    ----- тернарный оператор  (УСЛОВИЯ ? Что делать если true : что делать если false)
            
            array_key_exists = проверяю ключи массива(собранные данные с Parser.php)
            если они есть говорю записать $array['key'];
            а если нет ключа запиши строку None;
               
            109:    У меня в базу вместо да или нет в поле customs_status записывает int(0 or 1)

               */
            ':bodystyle' => array_key_exists('Кузов', $array)==1 ? $array['Кузов'] : 'None',
            ':mileage' => array_key_exists('Пробег', $array)==1 ? $array['Пробег'] : 'None',
            ':transmission' => array_key_exists('Коробка передач', $array)==1 ? $array['Коробка передач'] : 'None',
            ':drivetrain' => array_key_exists('Привод', $array)==1 ? $array['Привод']  : 'None',
            ':price' => $array['price'],
            ':description' => 'asdasdasdasdasd',    # эту строку регулярки для поиска описании не написал все еще
            ':customs_status' => 1 ? $array['Растаможен в Казахстане']=='Да' : 0]);
    #print_r($array);
}



/*
checkUnique проверяет бд на уникальность загружаемых данных по ключу id_car
*/
function checkUnique($id_car){
    $pdo = DB::getInstance();
    $stmt = $pdo->prepare("
        SELECT id_car FROM cars
        WHERE id_car= $id_car ");

    $stmt->execute();
    $result = $stmt->fetchAll();
    return count($result);
}





function getDataFromDB(){
    $pdo = DB::getInstance();
    $stmt = $pdo->prepare("SELECT id_car, image_link, brand, model, year, city,
                           DATE_FORMAT(append_time, '%d.%m.%Y   %H:%i') FROM cars");

    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}

function getDataFromDBbyID($id){
    $pdo = DB::getInstance();
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id_car=$id ");

    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}

function getLast8cars(){
    $result=[];
    $temp=[];
    $pdo = DB::getInstance();
    $stmt = $pdo->prepare("SELECT DISTINCT id_car, image_link FROM cars");
    $stmt->execute();
    foreach ($stmt as $value) {
        $temp[]=$value;
    }
    $temp = array_reverse($temp);
    for ($i=0;$i<8;$i++){
        $result[]=$temp[$i];
    }
    return array_reverse($result);
}


function getCityListFromDB(){
    $result=[];
    $pdo = DB::getInstance();
    $stmt = $pdo->prepare("SELECT DISTINCT city FROM cars");
    $stmt->execute();
    foreach ($stmt as $value) {
        $result[]=$value['city'];
    }
    return $result;
}

function getBrandListFromDB(){
    $result=[];
    $pdo = DB::getInstance();
    $stmt = $pdo->prepare("SELECT DISTINCT brand FROM cars");
    $stmt->execute();
    foreach ($stmt as $value) {
        $result[]=$value['brand'];
    }
    return $result;
}

function getCountCarsOnCitys(){
    $result=[];
    $pdo = DB::getInstance();
    
    foreach (getCityListFromDB() as $key) {
        $count = 0;
        $stmt = $pdo->prepare("SELECT id FROM cars WHERE city='$key' ");
        $stmt->execute();
        foreach ($stmt as $value) {
            $count++;
        }
        #echo("<br> ${count} <br>");
        $result[$key]=$count;
    }
    return $result;
}


?>