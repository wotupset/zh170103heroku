<?php

extract($_POST,EXTR_SKIP);extract($_GET,EXTR_SKIP);extract($_COOKIE,EXTR_SKIP);
//header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE); //所有錯誤中排除NOTICE提示
//$query_string=$_SERVER['QUERY_STRING'];
//$url=$query_string;
$FFF=pathinfo($_SERVER["SCRIPT_FILENAME"]);
$phpself  = $FFF['basename'];
$phpself2 = $FFF['filename'];
date_default_timezone_set("Asia/Taipei");//時區設定
$time = (string)time();
//
require_once('simple_html_dom.php');
require_once('curl_getinfo.php');
//
if(isset($_POST['inputurl'])){
	$url=$_POST['inputurl'];
}else{
	$url=$_SERVER['QUERY_STRING'];
}
//修飾
$FFF=explode('#',$url);
$url=$FFF[0];

//
$html_inputbox=<<<EOT
<form id='form01' enctype="multipart/form-data" action='$phpself' method="post" onsubmit="">
<input type="text" name="inputurl" size="20" value="">
<input type="submit" name="sendbtn" value="送出">
</form>
EOT;
//
if( substr_count($url, "?res=")>0 ){
	//ok
}else{
	//echo "?res=";
	$FFF=$html_inputbox;
	echo html_body($FFF);
	exit;
}

//exit;

if(1){
  $x=curl_FFF($url);
  //echo print_r($x,true);exit;
  $getdata =$x_0 =$x[0];//資料
  $getinfo =$x_1 =$x[1];//訊息
  $geterror=$x_2 =$x[2];//錯誤
  //simple_html_dom
  if(!$getdata){echo print_r($getinfo,true);exit;}
  //echo print_r($getinfo,true);//檢查點
  $content=$getdata;
}

//
$html = str_get_html($content) or die('沒有收到資料');//simple_html_dom自訂函式
$chat_array='';
$chat_array = $html->outertext;
//echo print_r($chat_array,true);exit;//檢查點
//

$url_num='0000';
$pattern="%\?res=([0-9]+)%";
if(preg_match($pattern, $url, $matches_url)){
	//echo $matches_url[1];
	$url_num=$matches_url[1];
}
//echo $url_num;echo "\n";
$board_title = $html->find('title',0)->innertext;//版面標題
//echo $board_title;echo "\n";



$ymdhis=date('y/m/d H:i:s',$time);//輸出的檔案名稱
$board_title2=''.$board_title.'=第'.$url_num.'篇 於'.$ymdhis.'擷取';
//echo $board_title2;echo "\n";


$cc=0;
foreach( $html->find('div.quote') as $k => $v){$cc++;}
if($cc>0){
	//echo $cc;echo "\n";
}else{
	die('[x]blockquote');
}
////////////
//批次找留言
$chat_array=array();
$cc=0;$qlink_newest='';
foreach($html->find('div.post') as $k => $v){
	//$vv=$v->parent;
	$chat_array[$k]['org_text']=$v->outertext;
	//
	foreach($v->find('span.name') as $k2 => $v2){
		$chat_array[$k]['name'] =$v2->plaintext;
		$v2->outertext="";
	}
	foreach($v->find('span.title') as $k2 => $v2){
		$chat_array[$k]['title'] =$v2->plaintext;
		$v2->outertext="";
	}

	foreach($v->find('span.now') as $k2 => $v2){
		$chat_array[$k]['now'] =$v2->plaintext;
		$v2->outertext="";
	}
	foreach($v->find('span.id') as $k2 => $v2){
		$chat_array[$k]['id'] =$v2->plaintext;
		$v2->outertext="";
	}
	foreach($v->find('span.qlink') as $k2 => $v2){
		$chat_array[$k]['qlink'] =$v2->plaintext;
		$v2->outertext="";
		//
		$qlink_newest=$chat_array[$k]['qlink'];
	}
	//
	foreach($v->find('div.quote') as $k2 => $v2){
		$FFF=$v2->outertext;
		$FFF=strip_tags($FFF,"<br><span>");//留下換行標籤
		$chat_array[$k]['quote']=$FFF;
		//
		$v2->outertext="";
	}

	foreach($v->find('a.file-thumb') as $k2 => $v2){
		//$chat_array[$k]['image0'][]=$v2->outertext;
		//$chat_array[$k]['image']
		$FFF=$v2->href;
		$FFF='http:'.$FFF;
		$chat_array[$k]['image']=$FFF;
		

		foreach($v2->find('img') as $k3 => $v3){
			//$chat_array[$k]['image1'][]=$v3->outertext;
			$FFF=$v3->src;
			$FFF='http:'.$FFF;
			$chat_array[$k]['image_t']=$FFF;

		}
		//
		$v2->outertext="";
	}

	//
	$chat_array[$k]['zzz_text']=$v->outertext;

}
	
//echo print_r($chat_array,true);exit;//檢查點
////////////

//用迴圈叫出資料
$cc=$cc2=$cc3=0;$htmlbody='';
$array_imgurl=array();
foreach($chat_array as $k => $v){//迴圈
	$cc++;
	//
	$htmlbody.= '<div id="block'.$cc.'">'."\n";
	$htmlbody.= '<div id="box1">'."\n";
	$htmlbody.= '<span class="sort_num">#'.$cc.'</span> ';
	if(count($v['name'])){$htmlbody.= '<span class="name">'.$v['name'].'</span> ';}
	if(count($v['title'])){$htmlbody.= '<span class="title" title="'.$v['title'].'">'.$v['title'].'</span> ';}
	$htmlbody.= '<span class="idno">'.$v['now'].$v['qlink'].'</span> ';
	//$htmlbody.= '<span class="qlink">'.$v['qlink'].'</span> ';
	$htmlbody.= '</div>'."\n";
	$htmlbody.= '<div id="box2">'."\n";
	$htmlbody.= '<span class="quote"><blockquote>'.$v['quote'].'</blockquote></span> '."\n";
	if(count($v['image'])){
		//
		if( preg_match('/\.webm$/',$v['image'])){
			$cc3++;
			//echo "影".$cc3;
			$FFF=''.$v['image'];
			$FFF2=''.$v['image'];//http://web.archive.org/web/20170101020202/
			$htmlbody.= '<span class="image"><img class="zoom" src="'.$FFF.'"/>'.'影'.$cc3.'</span>'."\n";
			$htmlbody.='<video controls class="vv"><source src="2017" type="video/webm">video</video>'."\n";
			$htmlbody.='<img src="'.$v['image_t'].'">';//video的縮圖 jquery啟動後會消失
			//
			$FFF=$cc2+$cc3;
			$array_imgurl[$FFF]='http://web.archive.org/save/'.$v['image'];//js
		}else{
			$cc2++;//計算圖片數量
			//echo "圖".$cc2;
			$FFF=''.$v['image'];
			$htmlbody.= '圖'.$cc2.'<br/><span class="image"><img class="zoom" src="'.$FFF.'"/></span>'."\n";
			//
			$FFF=$cc2+$cc3;
			$array_imgurl[$FFF]=''.$v['image'];//js
		}
	}

	$htmlbody.= '</div>'."\n";
	$htmlbody.= '</div>'."\n";
}
//print_r($array_imgurl);exit;
$json_imgurl=json_encode($array_imgurl);
//print_r($json_imgurl);

$hash_url=hash('crc32',$url);
$FFF=substr($hash_url, 0, 6);
$htmlbody='<div style="border-LEFT:#'.$FFF.' 10px solid;">'.$htmlbody.'</div>';
$htmlbody=$board_title2.$url.$htmlbody;//加上網址
$reply_count=$cc;
$image_count=$cc2;
$webm_count=$cc3;
//$qlink_newest

//echo print_r($htmlbody,true);exit;//檢查點

$chat_array[0]=$htmlbody;
$chat_array[1]="國";
$chat_array[2]=$qlink_newest;
$chat_array[3]=$board_title;
$chat_array[4]=$board_title2;
$chat_array[5]=$webm_count;

//////////
$FFF=pathinfo($_SERVER["SCRIPT_FILENAME"]);
$phpself  = $FFF['basename'];
$phpself2 = $FFF['filename'];

$output_filename  = $phpself2.'.htm';
$output_content   = poi($chat_array);
file_put_contents($output_filename,$output_content);

$FFF="http://".$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];
$FFF=substr($FFF,0,strrpos($FFF,"/")+1); //根目錄
$output_fileurl=$FFF.$output_filename;

//$hash_url=hash('crc32',$url);
//$hash_url=hash('md5',$url);
//$ymdhis=date('ymd',$time);//輸出的檔案名稱
//$hash_url='?'.$hash_url;//取消
$hash_url='';//取消

///
header('Content-Type: text/html; charset=utf-8');
$FFF=''.$html_inputbox;
$FFF.=$url."<br/>\n";
$FFF.='<a href="'.$output_fileurl.'">'.$output_fileurl.'</a>'."<br/>\n";
$FFF.='<a href="https://web.archive.org/save/'.$output_fileurl.'">archive.org</a>'."<br/>\n";
$FFF.='<a href="https://archive.is/?run=1&url='.$output_fileurl.'">archive.is</a>'."<br/>\n";
$FFF.='reply_count='.$reply_count."<br/>\n";
$FFF.='image_count='.$image_count."<br/>\n";
$FFF.='webm_count='.floor($webm_count)."<br/>\n";
$FFF.='qlink_newest='.$qlink_newest."<br/>\n";
$FFF.='<div id="poi"></div>'."\n";
$FFF.='<div id="ppp"></div>'."\n";

$FFF.=<<<EOT
<script>
var tmp='$json_imgurl';
var ary=JSON.parse(tmp);;
console.log(ary);
console.log(ary[1]);
</script>
EOT;

$FFF.=<<<EOT
<script>
console.log( '測試1');
document.addEventListener("DOMContentLoaded", function(event) { 
	console.log( '測試2');
	poi();
	ppp();
});
function poi(){
	console.log( '測試2-2');
	time_orig = new Date().getTime();
	cc=0;
	var time_countdown = setInterval(function(){
		cc=cc+1;
		//
		var item = document.getElementById('poi');
		time_now = new Date().getTime();
		tmp=(time_now-time_orig);
		tmp=Math.floor(tmp/1000);
		var tmp1=tmp2=tmp3=tmp4=0;
		tmp1=tmp+0;
		if(tmp1>60){
			tmp2=Math.floor(tmp1/60);//分
			tmp1=tmp1%60;//秒
		}
		if(tmp2>60){
			tmp3=Math.floor(tmp2/60);//時
			tmp2=tmp2%60;//分
		}
		if(tmp3>60){
			tmp4=Math.floor(tmp3/24);//天
			tmp3=tmp3%24;//時
		}

		item.innerHTML='#'+tmp4+'天'+tmp3+'時'+tmp2+'分'+tmp1+'秒'+'#'+cc;
	},1000);
	//clearInterval(time_countdown);//沒有陣列項目就結束

}
function ppp(){
	console.log( '測試2-1');
	var cc=0;
	var timeinterval = setInterval(function(){
		cc=cc+1;
		//
		if(typeof ary[cc] !== 'undefined'){
			var fragment = document.createDocumentFragment();//创建一个文档片段
			var item = document.getElementById('ppp');
			var newitem = document.createElement('IMG');
			var newtext = document.createTextNode('#'+cc);
			newitem.src=ary[cc];
			newitem.style='height: 100px;';
			fragment.appendChild(newitem);
			fragment.appendChild(newtext);
			item.appendChild(fragment);
		}else{
			var fragment = document.createDocumentFragment();//创建一个文档片段
			var item = document.getElementById('ppp');
			var newtext = document.createTextNode('#結束');
			fragment.appendChild(newtext);
			item.appendChild(fragment);
			item.style="background-color:#bdbdbd;border-TOP:#f00 10px solid;";
			//
			clearInterval(timeinterval);//沒有陣列項目就結束
		}
		
	},2000);
}
</script>
EOT;
echo html_body($FFF);

exit;
////////////////

function html_body($x){
	//$webm_count  =$x[5];
	//
$x=<<<EOT
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<head>
<body>
$x
</body>	
</html>
EOT;
	//	
	return $x;
}
function poi($x){
	$htmlbody    =$x[0];
	$board_title2=$x[4];
//
$FFF=<<<EOT
<html>
<head>
<title>$board_title2</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<STYLE>
img.zoom {
height:auto; width:auto;
min-width:20px; min-height:20px;
max-width:250px; max-height:250px;
border:1px solid blue;
padding-right:5px;
background-color:#00ffff;
}
video.vv {
height:auto; width:auto;
min-width:20px; min-height:20px;
max-width:500px; max-height:500px;
position:relative;
left:-100;
}

span.image {
width:250px; 
height:250px;
border:1px solid #000;
display: inline-block;
vertical-align:text-bottom;
}
span.name {
display: inline-block;
white-space:nowrap;
font-weight: bold;
color: #117743;
min-width:10px;
max-width:100px;
overflow:hidden;
}
span.title {
display: inline-block;
white-space:nowrap;
font-weight: bold;
color: #CC1105;
min-width:10px;
max-width:100px;
overflow:hidden;
}
span.idno {
display: inline-block;
white-space:nowrap;
min-width:10px;
max-width:500px;
overflow:hidden;
}
</STYLE>

<script src="jquery-3.2.0.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function(event) { 
	console.log( 'DOMContentLoaded' );
});
$( document ).ready(function() {
	console.log( 'document ready' );
});
(function() {
	console.log( 'function' );
})();

$(document).ready(function() {
	//全域變數//global
	time = new Date();
	//
	poi();
});

function poi(){

var \$videos = $('video');
var i = 0; 

for(var video of \$videos) {
i=i+1;
video.id = 'video-'+i;
video.src= 'video-'+i;
//$(video).parent().find('img').css( "background-color", "red" );
tmp=$(video).parent().find('img')[0];
//$(tmp).css( "background-color", "red" );
//$(tmp).after('pp1');
$(tmp).attr( 'id', 'img2-'+i );
//console.log( $(tmp).attr( 'src' ) );
video.src=$(tmp).attr( 'src' );
//var tmp=$(video).parent().find('img').src;

//$(video).next("img").after('pp');

tmp2=$(video).next("img");
//$(tmp2).after('pp2');
//console.log( $(tmp2).attr('src') );
//console.log( tmp2.attr('src') );

$(video).attr( 'poster', $(tmp2).attr('src') );
//$(tmp2).detach();
$(tmp2).remove();

//$(tmp).after( $(tmp2) );
//$(tmp).after( '<br>' );
//alert(tmp);
}
	
}
</script>
</head>

<body bgcolor="#FFFFEE" text="#800000" link="#0000EE" vlink="#0000EE">
$htmlbody
</body>

<html>

EOT;
	
	//
	$x=$FFF;
	return $x;
}

	

?>
