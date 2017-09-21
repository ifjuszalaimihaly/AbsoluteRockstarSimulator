<?php
header('Content-type: text/plain; charset=utf-8');
include('vendor/apache/log4php/src/main/php/Logger.php');
Logger::configure('config.xml');
$logger = Logger::getLogger("main");
$logger->info("====================="."<br>");
$logger->info("getmessage"."<br>");
include("db_connect.php");
if(isset($_GET["messenger_user_id"])){
  $logger->info(implode(", ",$_GET)."<br>");
  $logger->info(implode(", ",array_keys($_GET))."<br>");
  $messenger_user_id = htmlspecialchars($_GET["messenger_user_id"]);
  $res = $mysqli->query("select pont from users where messenger_user_id = '$messenger_user_id';");
  if($row = $res->fetch_assoc()){
    $pont = $row["pont"];
  }
  $res = $mysqli->query("select count(id) as rate from users where pont > $pont;");
  $logger->info($mysqli->error);
  if($row = $res->fetch_assoc()){
    $rate = $row["rate"] + 1;
  }
  $res = $mysqli->query("select badge from badges_users inner join badges where badges_users.badge_id=badges.id and messenger_user_id = '$messenger_user_id' order by (inserted_at) desc;");
  $logger->info("1 ".$mysqli->error."<br>");
  $logger->info("badges ".$mysqli->affected_rows."<br>");
  $badges = "";
  while($row = $res->fetch_assoc()){
    $badges .= $row["badge"].", ";
  }
  $res = $mysqli->query("select count(inserted_at), card from cards_users inner join cards where cards_users.card_id = cards.id and messenger_user_id = '$messenger_user_id' group by(card) order by (inserted_at) desc");
  $logger->info("2 ".$mysqli->error."<br>");
  $logger->info("cards ".$mysqli->affected_rows."<br>");
  $cards = "";
  $countcards = 0;
  while($row = $res->fetch_assoc()){
    $cards .= $row["count(inserted_at)"]." db ".$row["card"].", ";
    $countcards++;
  }
  $badges = substr($badges,0,strlen($badges)-2);
  $cards = substr($cards,0,strlen($cards)-2);
  $ponttext = "Pontok: ". $pont;
  $ratetext = "Ranglista helyezésed: ".$rate;
  $logger->info($badges."<br>");
  $badgetext = "Eredmények: ".$badges;
  $logger->info($badgetext."<br>");
  $cardtext = "Kártyáid:  ".$cards;
  $res = $mysqli->query("select max(inserted_at) from share_links where messenger_user_id = '$messenger_user_id'");
  if($row = $res->fetch_assoc()){
    $inserted_at = $row["max(inserted_at)"];   
  }
  $logger->info($inserted_at."<br>");
  $res = $mysqli->query("select link from share_links where messenger_user_id = '$messenger_user_id' and inserted_at = '$inserted_at'");
  if($row = $res->fetch_assoc()){
    $link = $row["link"];   
  }
  $logger->info($link."<br>");
  $logger->info("countcards". $countcards. "<br>");
  if($countcards != 0){

    $json = '{
      "messages": [
      {"text": "Így állsz most a játékban:"},
      {"text": "'.$ponttext.'"},
      {"text": "'.$ratetext.'"},
      {"text": "'.$badgetext.'"},
      {"text": "'.$cardtext.'"},
      {
        "attachment": {
          "type": "template",
          "payload": {
            "template_type": "button",
            "text": "Megosztom a barátaimmal!",
            "buttons": [
            {
              "type": "web_url",
              "url": "https://www.facebook.com/sharer/sharer.php?u=http%3A//absoluteplatform.com/share.php?link='.$link.'",
              "title": "Megosztás"
            }
            ]
          }
        }
      }
      ]
    }
    ';
  } else {
    $json = '{
      "messages": [
      {"text": "Így állsz most a játékban:"},
      {"text": "'.$ponttext.'"},
      {"text": "'.$ratetext.'"},
      {"text": "'.$badgetext.'"},
      {
        "attachment": {
          "type": "template",
          "payload": {
            "template_type": "button",
            "text": "Megosztom a barátaimmal!",
            "buttons": [
            {
              "type": "web_url",
              "url": "https://www.facebook.com/sharer/sharer.php?u=http%3A//absoluteplatform.com/share.php?link='.$link.'",
              "title": "Megosztás"
            }
            ]
          }
        }
      }
      ]
    }';
  }
  $mysqli->close();
  echo($json);
}
?>