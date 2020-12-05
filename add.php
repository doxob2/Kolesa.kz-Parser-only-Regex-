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
              <b> Kolesa.kz </b></a> </li>
          <li class="nav-item mx-2"> <a class="nav-link" href="#">About us</a> </li>
          <li class="nav-item mx-2"> <a class="nav-link" href="#">Contacts </a> </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <ul class="nav nav-pills justify-content-center">
            <li class="nav-item"> <a href="" class="active nav-link" data-toggle="pill" data-target="#tabone">По целым страницам</a> </li>
            <li class="nav-item"> <a class="nav-link" href="" data-toggle="pill" data-target="#tabtwo">Добавить одно объявление</a> </li>
          </ul>
          <div class="tab-content mt-2">
            <div class="tab-pane fade show active" id="tabone" role="tabpanel">
                <form id="data" name="data" action="Parser.php" method="POST">
                    <input name="mod" type="text" value='many' style="display: none">
              <div class="row text-left justify-content-center">
                <div class="col-md-2 mt-1">
                  <p syle="font-size: 15px">City</p>
                </div>
                <div class="col-md-4">
                  <select class="custom-select" id="inputGroupSelect04" name="City" aria-label="Example select with button addon">
                    <?php foreach (getCityListFromDB() as $value) {
                                    echo ("<option value='$value'-->$value"); }?>
                  </select>
                </div>
              </div>
              <div class="row justify-content-center">
                <div class="col-md-2 mt-1">
                  <p>Brand</p>
                </div>
                <div class="col-md-4">
                  <select class="custom-select" id="inputGroupSelect04" name="Brand" form="data" aria-label="Example select with button addon">
                    <?php foreach (getBrandListFromDB() as $value) {
                                    echo ("<option value='$value'-->$value"); }?>
                  </select>
                </div>
              </div>
              <div class="row justify-content-center">
                <div class="col-md-2 mt-1">
                  <p>Count</p>
                </div>
                <div class="col-md-4">
                  <div class="row" style="">
                    <div class="col-md-4 text-right mt-1" id='minus'><a class="btn btn-outline-primary">-</a></div>
                    <div class="col-md-4">
                        <input id = "pageCount" name="pages" type="text" style="display: none">
                      <h1 form="data" class="text-center"></h1>
                    </div>
                    <div class="col-md-4 text-left mt-1" id='plus'><a class="btn btn-outline-primary">+</a></div>
                  </div>
                </div>
              </div>
              <div class="row pt-2 justify-content-center">
                <div class="col-md-6"><input class="btn btn-outline-primary btn-block" type="submit" value='Начать парсинг' href="/test.php"></input></div>
              </div>
              </form>
            </div>
           <div class="tab-pane fade" id="tabtwo" role="tabpanel">
              <p class="text-center">Скопируйте и вставьте ссылку на объявление с сайта Kolesa.kz</p>
              <form class="form-inline justify-content-center" method="POST" action="Parser.php">
                <input name="mod" type="text" value='single' style="display: none">
                <div class="input-group w-75">
                  <input type="text" class="form-control" id="inlineFormInputGroup" name='url' placeholder="Введите ссылку на объяваление">
                  <div class="input-group-append"><button class="btn btn-primary" type="submit"><i class="fa fa-chevron-circle-right"></i></button></div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous" style=""></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script>
    $("h1").html(1);
    $("#pageCount").val(1);
    var count = 1
    $(document).ready(function() {
            $("#plus").click(function() {
                if (count<5){
                    count +=1;
                    $("h1").html(count);
                    $("#pageCount").val(count);
                }
            });
            $("#minus").click(function() {
                if (count>1){
                    count -=1;
                    $("h1").html(count);
                    $("#pageCount").val(count);
                }
            });
        });
  </script>
</body>

</html>