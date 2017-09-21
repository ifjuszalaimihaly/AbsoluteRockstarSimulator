<?php
header('Content-type: text/plain; charset=utf-8');
include('vendor/apache/log4php/src/main/php/Logger.php');
Logger::configure('config.xml');
$logger = Logger::getLogger("main");
$logger->info("------------------------------"."<br>");
$logger->info("postmessage"."<br>");
$txt = implode(",", $_POST);
$logger->info($txt."<br>");
$txt = implode(",", array_keys($_POST));
$logger->info($txt."<br>");
include("db_connect.php");
$messenger_user_id = "";
$first_name = "";
$last_name = "";
$pont = 0;
$badge_id = "";
$card_id = "";
if (isset($_POST["messenger_user_id"]) && isset($_POST["first_name"]) && isset($_POST["last_name"])) {
    $messenger_user_id = htmlspecialchars($_POST["messenger_user_id"]);
    $first_name = htmlspecialchars($_POST["first_name"]);
    $last_name = htmlspecialchars($_POST["last_name"]);
    if (isset($_POST["pont"])) {
        $pont = $_POST["pont"];
        $logger->info("pont " . $pont."<br>");
    }
    if (isset($_POST["jelveny"])) {
        $badge_id = $_POST["jelveny"];
    }
    if(isset($_POST["card"])){
        $card_id = $_POST["card"];
        $logger->info("card id ".$card_id."<br>");
    }
    
    $res = $mysqli->query("select messenger_user_id, pont from users where messenger_user_id='$messenger_user_id'"); //select the given user
    if ($mysqli->affected_rows == 1) { //users already exists
    $logger->info("user exists"."<br>");
        if ($row = $res->fetch_assoc()) {
            $logger->info("users current points ". $row["pont"]."<br>");
            $pont = $pont + $row["pont"];
            $pont_before = $row["pont"];
            $logger->info("pont ".$pont."<br>");
            $logger->info("badge id ".$badge_id."<br>");
            $logger->info("badge not null"."<br>");
            $mysqli->query("select messenger_user_id, badge_id from badges_users where messenger_user_id = '$messenger_user_id' and badge_id = '$badge_id';");//check if user has the given badge
            $logger->info("after query"."<br>");
            $logger->info("badge test ".$mysqli->error."<br>");
            $logger->info("badge test affected rows ".$mysqli->affceted_rows."<br>");
            if ($mysqli->affected_rows != 1) { //if don't has, gives the badge with extra points
                $pont = $pont + 25;
                $logger->info("update user with badge pont: ".$pont."<br>");
                $res = $mysqli->query("update users set pont=$pont, pont_before=$pont_before, inserted_at=now() where messenger_user_id='$messenger_user_id'");
                $res = $mysqli->query("insert into badges_users (messenger_user_id, badge_id) values ('$messenger_user_id', '$badge_id');");
            } else {// update points without update badges
                $logger->info("update points without update badges, new pont " . $pont."<br>");
                $mysqli->query("update users set pont=$pont, pont_before=$pont_before, inserted_at=now() where messenger_user_id='$messenger_user_id'");
            }
            if($card_id != ""){
                $inserted_at = "";
                $last_card_id = "";
                $logger->info("card id ". $card_id."<br>");
                $res = $mysqli->query("select max(inserted_at) from cards_users where messenger_user_id='$messenger_user_id';");
                $logger->info($mysqli->error."<br>");
                if($row = $res->fetch_assoc()){
                    $inserted_at=$row["max(inserted_at)"];
                }
                $res = $mysqli->query("select card_id from cards_users where messenger_user_id='$messenger_user_id' and inserted_at='$inserted_at';");
                if($row = $res->fetch_assoc()){
                    $last_card_id=$row["card_id"];
                }
                if($last_card_id != $card_id){
                    $res = $mysqli->query("insert into cards_users (messenger_user_id, card_id) values ('$messenger_user_id', '$card_id');");
                    $logger->info($mysqli->error."<br>");
                }
            }
        }
    } else { //users don't exists
    $logger->info("user does not exist"."<br>");
        if ($badge_id == "") {
            $logger->info("insert new user with badge pont: ".$pont."<br>");
            $mysqli->query("insert into users (messenger_user_id, chatfuel_user_id, first_name, last_name, pont) values ('$messenger_user_id', NULL, '$first_name', '$last_name', '$pont');");
        } else {
                $pont = $pont + 25;
                $logger->info("insert new user with badge pont: ".$pont."<br>");
                $res = $mysqli->query("insert into users (messenger_user_id, chatfuel_user_id, first_name, last_name, pont) values ('$messenger_user_id', NULL, '$first_name', '$last_name', '$pont');");
                $res = $mysqli->query("insert into badges_users (messenger_user_id, badge_id) values ('$messenger_user_id', '$badge_id');");
        }
    }
    $time = time();
    $link = $time.rand(10000,99999);
    $res = $mysqli->query("insert into share_links (link, messenger_user_id, pont, inserted_at) values ($link, $messenger_user_id, $pont, now());");
}
if(isset($_POST["messenger_user_id"]) && isset($_POST["torol"])){
    $logger->info("törlés <br>");
    $torol = htmlspecialchars($_POST["torol"]);
    if($torol == 1){
        $logger->info("törlés <br>");
        $messenger_user_id = htmlspecialchars($_POST["messenger_user_id"]);
        $mysqli->query("DELETE FROM users where messenger_user_id='$messenger_user_id'");
    }
}
$mysqli->close();
?>