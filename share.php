<?php
error_log("******************************");
if(!isset($_GET["link"])){
    die();
} else {
    $link = htmlspecialchars($_GET["link"]);
    error_log("link setted ".$link,0);
}
error_log("go forward",0);
$messenger_user_id = "";
$pont = 0;
$first_name = "";
$last_name = "";
$inserted_at = "";
include("db_connect.php");
$res = $mysqli->query("select messenger_user_id, pont, inserted_at from share_links where link = '$link';");
if($mysqli->affected_rows != 1){
    die();
}
error_log("1 error ".$mysqli->error);
error_log("affected rows ".$mysqli->affected_rows,0);
if($row = $res->fetch_assoc()){
    error_log("row ". count($row),0);
    $messenger_user_id = $row["messenger_user_id"];
    error_log($messenger_user_id,0);
    $pont = $row["pont"];
    error_log($pont,0);
    $inserted_at = $row["inserted_at"];
}
$res = $mysqli->query("select last_name, first_name from users where messenger_user_id = '$messenger_user_id';");

if($row = $res->fetch_assoc()){
    $first_name = $row["first_name"];
    $last_name = $row["last_name"];
}
$res = $mysqli->query("select badge from badges_users inner join badges where badges_users.badge_id = badges.id and messenger_user_id = '$messenger_user_id' and inserted_at<='$inserted_at' order by (inserted_at) desc;;");
$badges = array();
$i=0;
while($row = $res->fetch_assoc()){
    $badge = $row["badge"];
    $badges[$i] = $badge;
    $i++;
}
$res = $mysqli->query("select count(inserted_at), card from cards_users inner join cards where cards_users.card_id = cards.id and messenger_user_id = '$messenger_user_id' and inserted_at<='$inserted_at' group by(card) order by (inserted_at) desc");
$cards = array();
$i=0;
while($row = $res->fetch_assoc()){
    error_log($row,0);
    $card = $row["card"];
    $count = $row["count(inserted_at)"];
    $cards[$i]["card"] = $card;
    $cards[$i]["count"] = $count;
    $i++;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title><?= $last_name ?> <?= $first_name ?> játszott az Absolute Rockstar Simulator játékkal</title>
  <!-- Bootstrap -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css"/>
  <link rel="shortcut icon" type="image/png" href="/ars.png"/>
  <style type="text/css" rel="stylesheet">
        h1{
            margin-left: 1em;
        }
  </style>
  </head>
  <body>
  <div class="jumbotron">
      <h1>Absolute Rockstar Simulator</h1>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-xs-12">
      <h3><?= $last_name ?> <?= $first_name ?> játszott az Absolute Rockstar Simulator játékkal</h3>
      <h4>Összegyűjött <?= $pont ?> pontot</h4>
      <h4>Eredményei:</h4>
      <ul>
        <?php for($i = 0; $i<count($badges); $i++) { ?>
        <li><?= $badges[$i] ?></li>
        <?php } ?>
      </ul>
      <h4>Kártyái:</h4>
       <ul>
       <?php for($i = 0; $i<count($cards); $i++) { ?>
       <li><?= $cards[$i]["count"] ?> db <?= $cards[$i]["card"] ?></li>
       <?php } ?>
      </ul>
      <a class="btn btn-primary" href="https://www.messenger.com/t/1904331246556329">Itt te is játszhatsz</a>
      </div>
    </div>
  </div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script
    src="https://code.jquery.com/jquery-3.2.1.min.js"
    integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
    crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  </body>
  </html>