<?php
    include ("config.php");
	include ($oauth_file);
	include ("tokens.php");
	
	$fullToken["accessToken"] = $accessToken;
    $fullToken["refreshToken"] = $refreshToken;
    $fullToken["refreshTime"] = $refreshTime;
    $fullToken["updateTime"] = $updateTime;
 
    $fullToken = check_token($FQDN, $api_key, $secret_key, $scope, $fullToken, $oauth_file);
    $accessToken = $fullToken["accessToken"];
 
        // Form the URL to send SMS
    $CMS_RequestBody = json_encode(array('numTo' => $numTo, 'numFrom'=>$numFrom, 'caller'=>$caller));
 
    $CMS_Url = $FQDN . "/rest/1/Sessions";
    $authorization = 'Authorization: Bearer ' . $accessToken;
    $content = "Content-Type: application/json";
 
    //Invoke the URL
        $CMS = curl_init();
        curl_setopt($CMS, CURLOPT_URL, $CMS_Url);
        curl_setopt($CMS, CURLOPT_POST, 1);
        curl_setopt($CMS, CURLOPT_HEADER, 0);
        curl_setopt($CMS, CURLINFO_HEADER_OUT, 0);
        curl_setopt($CMS, CURLOPT_HTTPHEADER, array (
            $authorization,
            $content
        ));
        curl_setopt($CMS, CURLOPT_POSTFIELDS, $CMS_RequestBody);
        curl_setopt($CMS, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($CMS, CURLOPT_SSL_VERIFYPEER, false);
        $CMS_response = curl_exec($CMS);
        $responseCode = curl_getinfo($CMS, CURLINFO_HTTP_CODE);
 
        if ($responseCode == 200) 
        {
        	echo $CMS_response;
			$jsonResponse = json_decode($CMS_response, true);
            $id = $jsonResponse["id"];
    		
			$fh = fopen($session_file, 'w');
        	fwrite($fh, $id);
        	fclose($fh);
		}
?>