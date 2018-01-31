<?php
session_start(); //เปิด seesion เพื่อทำงาน

//connectdb
$host = "sql12.freemysqlhosting.net";
$username = "sql12218252";
$password = "ARBn1864yi";
$objConnect = mysqli_connect($host,$username,$password);
mysqli_set_charset($objConnect,"utf8");

if($objConnect)
{
	echo "MySQL Connected";
}
else
{
	echo "MySQL Connect Failed : Error : ".mysqli_error();
}

//Line Token
$strAccessToken = '1OFasil/2dmg4zfIvklnFzY23slCclWjIgKyIwHnQcbg7ztGPVMZny6479Vnyeh8gCNpL9KJl5I6YfMpmNveUjbwcoi4f943KMjpHwmxb+pXKetgldM4DK2CUVZhRCvCoQYEAS5+yPkDLjwLQvm3RgdB04t89/1O/w1cDnyilFU=';
$content = file_get_contents('php://input');
$arrJson = 	json_decode($content, true);
$strUrl = "https://api.line.me/v2/bot/message/reply";
$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";
$check = $arrJson['events'][0]['source']['userId'];
$license = $arrJson['events'][0]['message']['text'];

if($arrJson == ""){

	echo "No Token";
}else{
	$objDB = mysqli_select_db($objConnect,"sql12218252");
		$s = "SELECT * FROM user Where token = '$check'";
	$sql = mysqli_query($objConnect,$s);

	if(mysqli_num_rows($sql)==1){
		
		$s1 = "SELECT * FROM car Where license = '$license' and token = '$check' or token2 = '$check'";
		$sql1 = mysqli_query($objConnect,$s1);

		if(mysqli_num_rows($sql1)>=1){

			$s1 = "SELECT car.cartype,car.license,livedata.latitude,livedata.longitude FROM car INNER JOIN livedata ON car.carid = livedata.carid and car.license ='$license'";
			$sql1 = mysqli_query($objConnect,$s1);
		
				$row = mysqli_fetch_array($sql1);
				
				$_SESSION["cartype"] = $row[cartype];
				$_SESSION["license"] = $row[license];
				$_SESSION["latitude"] = $row[latitude];
				$_SESSION["longitude"] = $row[longitude];
				
			$arrPostData = array();
			 $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
			  
			$arrPostData['messages'][0]['type'] = "location";
			$arrPostData['messages'][0]['title'] = "".$_SESSION["cartype"];
			$arrPostData['messages'][0]['address'] = "".$_SESSION["license"];
			$arrPostData['messages'][0]['latitude'] = $_SESSION["latitude"];
			$arrPostData['messages'][0]['longitude'] = $_SESSION["longitude"];
			
			
		
			
		
		}else{
			$arrPostData = array();
			  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
			  $arrPostData['messages'][0]['type'] = "text";
			  $arrPostData['messages'][0]['text'] = "ขออภัยค่ะเลขทะเบียนหรือข้อความไม่ถูกต้อง";
			//echo "<BR>ขออภัยค่ะ Line ID ยังไม่ได้ลงทะบียนค่ะ";
		}
				 if($arrJson['events'][0]['message']['text'] == "รถ"){

				$objDB = mysqli_select_db($objConnect,"sql12218252");
				$s1 = "SELECT * FROM user Where token = '$check'";
				$sql1 = mysqli_query($objConnect,$s1);

				$row = mysqli_fetch_array($sql1);
				
				$_SESSION["Car"] = $row["car"];
				

			  $arrPostData = array();
			  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
			  $arrPostData['messages'][0]['type'] = "text";
			  $arrPostData['messages'][0]['text'] = "รถของท่าน ".$_SESSION["Car"];
				
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
