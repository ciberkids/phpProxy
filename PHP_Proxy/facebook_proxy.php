 <?php
//remote TSC web Server Proxy

$url = 'FACEBOOK URL';

function objectToArray($d) {
 if (is_object($d)) {
 // Gets the properties of the given object
 // with get_object_vars function
 $d = get_object_vars($d);
 }
 
 if (is_array($d)) {
 /*
 * Return array converted to object
 * Using __FUNCTION__ (Magic constant)
 * for recursive call
 */
 return array_map(__FUNCTION__, $d);
 }
 else {
 // Return array
 return $d;
 }
 }
 
/*
 * expeting json from gui:
 *  { protocol api field,
 *    api: "apiname"
 *  }
 */
 


$ch = curl_init();
 //set POST variables
//print_r($fields);
//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);

//execute post
$responce = curl_exec($ch);
$result = array( 'header' => '', 
                         'body' => '', 
                         'curl_error' => '', 
                         'http_code' => '',
                         'last_url' => '');
$output=$responce;

$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header=substr($responce, 0, $header_size);
$body = substr( $responce, $header_size, strlen($responce) );
//storing result
$result['header'] = $header;
$result['http_code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
$result['last_url'] = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);

if($result['http_code'] >= 500 ) {
  $result['body'] = array(  'action' => 'failed',
                            'reason' => 'remote services has replied with ' . $result['http_code'] . ' Error\n contact sysadmin',
                            'remote_url' => $result['last_url']
                          );
}


//var_dump($result);
//close connection
curl_close($ch);

//Building answer for gui
header('Content-Type: application/json');
http_response_code($result['http_code']);
//answering
echo json_encode($result['body']);

