<?php
define('URL', 'https://no1s.biz/');

$url_list[URL] = ['url' => URL, 'title' => null];
function ckeck_web(string $url,array &$url_list = [], $flg = false){
	if (!ckeck_ext($url)) {
		// WEBページでない
		return false;
	}
	
	if (!empty($url_list[$url]['title'])) {
		// チェック済み
		return false;
	}
	
	$homepage = @file_get_contents($url);
	if (!empty($homepage)) {
		preg_match('@<title>([^<]++)</title>@i', $homepage, $result);
		echo 'url::' , $url, "\r\n" ,' title:: ' , $result[1] , "\r\n\r\n";
		$url_list[$url] = ['url' => $url, 'title' => $result[1]];
	} else {
		echo 'url::' , $url, "\r\n" ,' WEBサイトERROR::相手先で取得ができない' , "\r\n\r\n";
		return false;
	}
	$check_url=[];
	if(preg_match_all('|<a href=\"(.*?)\".*?>(.*?)</a>|mis', $homepage, $result) !== false){
	    foreach ($result[1] as $value){
	    	if (preg_match('/^(https:\/\/no1s.biz|http:\/\/no1s.biz)([-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $value) && empty($url_list[$value]['title'])) {
				$url_list[$value] = ['url' => $value , 'title' => null];
		    	if (!isset($check_url[$value]) && $url !== $value ) {
		    		$check_url[$value] = $value;
		    	}
	    	}
	    }

	    // チェック済み削除
	    if ($flg) {
		    foreach ($url_list as $value1){
		    	if (isset($check_url[$value1['url']]) && !empty($value1['title'])) {
		    		unset($check_url[$value1['url']]);
		    	}
		    }
	    }
	    // 抽出URLチェック
	    foreach ($check_url as $value2){
	    	ckeck_web($value2, $url_list, true);
	    }
	}
}

// 拡張子による除外判定
function ckeck_ext(string $url){
	$url_array = explode('/', $url);
	$url_array = array_filter($url_array, "strlen");
	$value = end($url_array);
	switch (true) {
	    case (strpos($value, '.jpg') !== false):
	    case (strpos($value, '.png') !== false):
	    case (strpos($value, '.gif') !== false):
	    case (strpos($value, '.css') !== false):
	    case (strpos($value, '.js') !== false):
	    case (strpos($value, '.xml') !== false):
	    case (strpos($value, '.ico') !== false):
	    case (strpos($value, '.php') !== false):
			return false;
	}
	
	return true;
}


try {
    ckeck_web(URL, $url_list);
} catch (Exception $e) {
    echo 'モジュールERROR: ',  $e->getMessage(), "\r\n";
}
