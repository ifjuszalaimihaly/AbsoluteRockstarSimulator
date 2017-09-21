<?php
header('Content-type: text/plain; charset=utf-8');
include('vendor/apache/log4php/src/main/php/Logger.php');
Logger::configure('config.xml');
$logger = Logger::getLogger("main");
$logger->info("%%%%%%%%%%%%%%%%%%%%%%%%<br>");
$logger->info("routing"."<br>");
$txt = implode(",", $_POST);
$logger->info($txt."<br>");
$txt = implode(",", array_keys($_POST));
$logger->info($txt."<br>");
include("db_connect.php");
if(isset($_POST["messenger_user_id"]) && isset($_POST["block_name"])){
	$block_name = htmlspecialchars($_POST["block_name"]);
	$messenger_user_id = htmlspecialchars($_POST["messenger_user_id"]);
	if($block_name == "turne_zaro_pont"){
		$pont = 0;
		$pont_before = 0;
		$res = $mysqli->query("select pont, pont_before from users where messenger_user_id='$messenger_user_id'");
		if($row = $res->fetch_assoc()){
			$pont = $row["pont"];
			$pont_before = $row["pont_before"];
		}
		if($pont<1000) {
			$json = '{
  				"redirect_to_blocks": ["Újabb turné"]
			}';
		} else {
			$json = '{
  				"redirect_to_blocks": ["Játék zárás"]
			}';
		}
	}
	echo $json;
}
$mysqli->close();