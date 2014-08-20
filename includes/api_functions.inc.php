<?php

/**
 * curl_get performs a request on the MWRC server and returns xml
 *
 * @param string $mwrc_domain
 * 		- client's mwrc domain: http://[clients_subdomain].mwrc.net
 * 
 * @param string $mwrc_lang_abbrev
 * 		- typically: en
 * 
 * @param string $mwrc_script_name
 * 		- the API you are making a request against: category.xml, product.xml, locator.xml
 * 
 * @param string $mwrc_script_args
 * 		- the API parameters. All the possible params the API script can accept * 
 * 
 * @param string $cookie_string_for_api
 * 		- the value returned by mwrc_session_handler
 * 
 * @return string
 * 		- XML
 */

///////////////////////////////////////
function curl_get ($mwrc_domain, $mwrc_lang_abbrev, $mwrc_script_name, $mwrc_script_args)
///////////////////////////////////////
{
	$ch=curl_init();
	
	
    $url = "$mwrc_domain/xml/$mwrc_lang_abbrev/$mwrc_script_name?$mwrc_script_args";
    
/*     print "<pre>".$url."</pre>"; */
    
	curl_setopt($ch, CURLOPT_URL, $url);

	if (isset($_COOKIE['mwrc_session_code_1_1'])) {
	    curl_setopt($ch, CURLOPT_COOKIE, "mwrc_session_code_1_1=".$_COOKIE['mwrc_session_code_1_1'].";");
    }
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$xml=curl_exec($ch);

	$error = curl_error($ch);
	
	$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
/*
	print "==".$http_status."==";
	print $xml;exit;
*/
	curl_close($ch);
	return $xml;
}