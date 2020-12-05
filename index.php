<?php
include ('DataBase.php');


$lastCarsOnDB = getLast8cars();   #Последние 8 машин добавленные в бд

function CitysCount($city, $count){
    $tm = <<<EOD
        <li class="list-group-item d-flex justify-content-between align-items-center">
            ${city}
            <span class="badge badge-primary badge-pill">${count}</span>
        </li>
    EOD;
    return $tm;
}
#print_r(getCountCarsOnCitys());

function countCity(){
    $citys = getCountCarsOnCitys();
    if (intdiv(count($citys),5)<2){
        foreach ($citys as $key => $value) {
            echo (CitysCount($key, $value));
        }
    }
}


function setImages(){
    $lastCarsOnDB = getLast8cars();
    foreach ($lastCarsOnDB as $value) {
        $id = $value['id_car'];
        $src = $value['image_link'];
        $tm = <<<EOD
            <div class="col-lg-3 pb-4"><a href="/view.php?id=${id}"><img href='/view.php' class="img-fluid d-block" src=${src}></a></div>
        EOD;
        echo($tm);
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
</head>

<body >
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container"> <a class="navbar-brand" href="#">
            <i class="fa d-inline fa-lg fa-circle-o"></i>
            <b> BRAND</b>
            </a>
                <button class="navbar-toggler navbar-toggler-right border-0" type="button" data-toggle="collapse" data-target="#navbar11">
                    <span class="navbar-toggler-icon"></span>
                </button>
            <div class="collapse navbar-collapse" id="navbar11">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"> <a class="nav-link" href="add.php">Добавить объявление</a> </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"> <a class="nav-link" href="#">Services</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#">About</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="#">FAQ</a> </li>
                </ul>
                <a class="btn btn-primary navbar-btn ml-md-2">Contact us</a>
            </div>
        </div>
    </nav>
    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="text" >Последние добавленные машины</h3>
                    <div class="row">
                        <?php setImages(); ?>
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="mt-4">
                        <h3>На данный момент в базе <?php echo count(getDataFromDB());?> машин</h3>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mt-1">
                            <p syle='font-size: 15px'>City</p>
                        </div>
                        <div class="col-md-8">
                            <select class="custom-select" id="inputGroupSelect04" name='City' aria-label="Example select with button addon">
                                <option selected>All</option>
                                <?php foreach (getCityListFromDB() as $value) {
                                    echo ("<option value='$value'>$value</option>");
                                }?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mt-1">
                            <p>Brand</p>
                        </div>
                        <div class="col-md-8" >
                            <select class="custom-select" id="inputGroupSelect04" name='Brand' aria-label="Example select with button addon">
                                <option selected>All</option>
                                <?php foreach (getBrandListFromDB() as $value) {
                                    echo ("<option value='$value'>$value</option>");
                                }?>
                            </select>
                        </div>
                    </div>
                    <div class="row pt-2" >
                        <div class="col-md-12"><a class="btn btn-primary btn-block" href="#">Поиск</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="">Количество спарсенных машин по городам</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="col-md-4">
                                <ul class="list-group">
                                    <?php echo countCity()?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>