<?php
session_start(); //เปิด seesion เพื่อทำงาน
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

//connectdb
$host = "sql12.freemysqlhosting.net";
$username = "sql12218252";
$password = "ARBn1864yi";
$objConnect = mysqli_connect($host,$username,$password);

if($objConnect)
{
	echo "MySQL Connected";
}
else
{
	echo "MySQL Connect Failed : Error : ".mysqli_error();
}

//Line Token
$strAccessToken = '9B9ffZ7XJ/iWMWgJuqRV/oaVVMfELLEMmBjoIhqG9E5xdFvOHDNpiZBDjdi2deqYm4SdFCezBQjddXs1EgjLXmCJgBorihv3bfwUxW8zMCoT9EqBEs5CW6wnsUqEoJcKTGPyYznGlnG293DicIlZowdB04t89/1O/w1cDnyilFU=';
$content = file_get_contents('php://input');
$arrJson = 	json_decode($content, true);
$strUrl = "https://api.line.me/v2/bot/message/reply";
$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$check = $arrJson['events'][0]['source']['userId'];

if($arrJson == ""){

	echo "No Token";
}else{
	$objDB = mysqli_select_db($objConnect,"sql12218252");
		$s = "SELECT * FROM user Where token = '$check'";
	$sql = mysqli_query($objConnect,$s);

	if(mysqli_num_rows($sql)==1){

			$row = mysqli_fetch_array($sql);

			$_SESSION["UserID"] = $row["id"];
			$_SESSION["token"] = $row["token"];
			$_SESSION["username"] = $row["username"];

				if($arrJson['events'][0]['message']['text'] == "ID"){
			  $arrPostData = array();
			  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
			  $arrPostData['messages'][0]['type'] = "text";
			  $arrPostData['messages'][0]['text'] = "สวัสดี ID คุณคือ ".$arrJson['events'][0]['source']['userId'];
					
			}else if($arrJson['events'][0]['message']['text'] == "รถ"){

				$objDB = mysqli_select_db($objConnect,"sql12218252");
				$s1 = "SELECT * FROM cars Where token = '$check'";
				$sql1 = mysqli_query($objConnect,$s1);

				$row = mysqli_fetch_array($sql1);
					
				$_SESSION["Cartype"] = $row["cartype"];
				$_SESSION["License"] = $row["license"];

			  $arrPostData = array();
			  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
			  $arrPostData['messages'][0]['type'] = "text";
			  $arrPostData['messages'][0]['text'] = "รถของท่าน ".$_SESSION["Cartype"]."ทะเบียน ".$_SESSION["License"];
						
			
			}else if($arrJson['events'][0]['message']['text'] == "พิกัด"){
				
				$objDB = mysqli_select_db($objConnect,"sql12218252");
				
				$s1 = "SELECT car.license, car.cartype,livedata.latitude,livedata.longitude 
				FROM car INNER JOIN livedata ON car.token = livedata.token and car.token = '$check' ";
				
					$sql1 = mysqli_query($objConnect,$s1);
					
				

				$row = mysqli_fetch_array($sql1);

				$_SESSION["Cartype"] = $row["cartype"];
				$_SESSION["License"] = $row["license"];
				$_SESSION["Latitude"] = $row["latitude"];
				$_SESSION["Longitude"] = $row["longitude"];
					
					 

			  $arrPostData = array();
			  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
			  
			$arrPostData['messages'][0]['type'] = "location";
			$arrPostData['messages'][0]['title'] = "".$_SESSION["Cartype"];
			$arrPostData['messages'][0]['address'] = "".$_SESSION["License"];
			$arrPostData['messages'][0]['latitude'] = $_SESSION["Latitude"];
			$arrPostData['messages'][0]['longitude'] = $_SESSION["Longitude"];

		
			
		}else if($arrJson['events'][0]['message']['text'] == "ที่ตั้ง"){

			  $arrPostData = array();
			  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
					
			$latitude = '13.716013';
			$longitude = '100.656906';
			  
			$arrPostData['messages'][0]['type'] = "location";
			$arrPostData['messages'][0]['title'] = "บริษัท อีสท์อินโนเวชั่น จำกัด";
			$arrPostData['messages'][0]['address'] = "ซอย อ่อนนุช 74/1 แยก 1 แขวง ประเวศ เขต ประเวศ กรุงเทพมหานคร 10250 โทรศัพท์: 02-052-4466";
			$arrPostData['messages'][0]['latitude'] = $latitude ;
			$arrPostData['messages'][0]['longitude'] = $longitude ;


			
		}
		}else {

			$arrPostData = array();
			  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
			  $arrPostData['messages'][0]['type'] = "text";
			  $arrPostData['messages'][0]['text'] = "ขออภัยค่ะ Line ID ยังไม่ได้ลงทะบียนค่ะ ".$arrJson['events'][0]['source']['userId'];
			//echo "<BR>ขออภัยค่ะ Line ID ยังไม่ได้ลงทะบียนค่ะ";
		}

}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$strUrl);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close ($ch);
?>
