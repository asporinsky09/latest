<?php
	function processTransaction($firstName, $lastName, $product, $price, $ccnum, $ccexp, $cvv) {
		$loginname = "94AJGkc88s";
		$transactionkey = "558mE7e385fRp8Ku";
		$post_url = "https://secure.authorize.net/gateway/transact.dll";

		$post_values = array(
        	"x_test_request" => "TRUE",
            "x_login" => $loginname,
            "x_tran_key" => $transactionkey,
            "x_version" => "3.1",
            "x_delim_data" => "TRUE",
            "x_delim_char" => "|",
            "x_relay_response" => "FALSE",
            "x_type" => "AUTH_CAPTURE",
            "x_method" => "CC",
            "x_card_num" => $ccnum,
            "x_exp_date" => $ccexp,
            "x_card_code" => $cvv,
            "x_amount" => $price,
            "x_description" => $product,
            "x_first_name" => $firstName,
            "x_last_name" => $lastName);

        $post_string = "";
        foreach ($post_values as $key => $value) {
            $post_string .= "$key=" . urlencode($value) . "&";
        }
        $post_string = rtrim($post_string, "& ");


        $request = curl_init($post_url);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        $post_response = curl_exec($request);
        curl_close($request);
        $response_array = explode($post_values["x_delim_char"], $post_response);

        $i = 1;
        foreach ($response_array as $value) {
            $data[$i] = $value;
            $i++;
        }

        $responseCode = $data[1];
        if ($responseCode == 1) {
        	if(! $data[10] == $price) {
        		error_log('Price from authorize of ' . $data[10] . ' does not match given price of ' . $price);
        		return false;
        	}
        	$transId = $data[7];
        	return $transId;
        } else if ($responseCode == 2) {
        	return "Declined";
        } else {
        	return false;
        }
    }
?>