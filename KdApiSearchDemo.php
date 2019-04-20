<?php
//电商ID
defined('1293614') or define('EBusinessID', '1293614');
//电商加密私钥，快递鸟提供，注意保管，不要泄漏
defined('b407421f-e4f2-4ed1-a0a2-cd93358ceab1') or define('AppKey', 'b407421f-e4f2-4ed1-a0a2-cd93358ceab1');
//请求url
defined('ReqURL') or define('ReqURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');

//调用查询物流轨迹
//---------------------------------------------

$logisticResult=getOrderTracesByJson();
$array = json_decode($logisticResult,true);
$array['Traces'] = array_reverse($array['Traces']);
foreach ($array['Traces'] as $key=>$value){
	$array['Traces'][$key]['time'] = explode(":", explode(" ", $value['AcceptTime'])[1])[0].":".explode(":", explode(" ", $value['AcceptTime'])[1])[1];
	$array['Traces'][$key]['month'] = explode("-", explode(" ", $value['AcceptTime'])[0])[1];
	$array['Traces'][$key]['day'] = explode("-", explode(" ", $value['AcceptTime'])[0])[2];
	$array['info'][$array['Traces'][$key]['day']][] = $array['Traces'][$key];
	
}
$array['info'] = array_values($array['info']);
$logisticResult = json_encode($array);
//print_r($array);
echo $logisticResult;

//---------------------------------------------
 
/**
 * Json方式 查询订单物流轨迹
 */
function getOrderTracesByJson(){
	$requestData= "{'OrderCode':'','ShipperCode':'YTO','LogisticCode':'883146425679074459'}";
	
	$datas = array(
        'EBusinessID' => EBusinessID,
        'RequestType' => '1002',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = encrypt($requestData, AppKey);
	$result=sendPost(ReqURL, $datas);	
	
	//根据公司业务处理返回的信息......
	
	return $result;
}
 
/**
 *  post提交数据 
 * @param  string $url 请求Url
 * @param  array $datas 提交的数据 
 * @return url响应返回的html
 */
function sendPost($url, $datas) {
    $temps = array();	
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);		
    }	
    $post_data = implode('&', $temps);
    $url_info = parse_url($url);
	if(empty($url_info['port']))
	{
		$url_info['port']=80;	
	}
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
    $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader.= "Connection:close\r\n\r\n";
    $httpheader.= $post_data;
    $fd = fsockopen($url_info['host'], $url_info['port']);
    fwrite($fd, $httpheader);
    $gets = "";
	$headerFlag = true;
	while (!feof($fd)) {
		if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
			break;
		}
	}
    while (!feof($fd)) {
		$gets.= fread($fd, 128);
    }
    fclose($fd);  
    
    return $gets;
}

/**
 * 电商Sign签名生成
 * @param data 内容   
 * @param appkey Appkey
 * @return DataSign签名
 */
function encrypt($data, $appkey) {
    return urlencode(base64_encode(md5($data.$appkey)));
}

?>