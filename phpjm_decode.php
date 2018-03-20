<?php
$decode_file='encode.php';
$text=file_get_contents($decode_file);
//格式化
preg($text);
//获取中间的校验函数将其输出到格式化后得内容中便于查看
$text=preg_replace('/var[0-9][0-9]*\(\$var[0-9][0-9]*\(\'(.*)\'/e','fiter(\'\\1\')',$text,1);
//匹配 出两个变量的名称
$text=preg_replace('/var[0-9][0-9]*\(\$var[0-9][0-9]*\(\'(.*)\'/e',"\$a=('\\1')",$text,1);
preg_match_all('/(\$var[0-9]*)/',$a,$out);
$va=$out[0][0];
$vb=$out[0][1];
//取出调用fun16的参数
preg_match("/\\".$va."=fun16(.*);/",$text,$out);
$va_value=explode("\"",$out[0]);
//调用 取得变量1的值
$va_value=fun16($va_value[1],$va_value[3]);
$va_value=fiter($va_value);
//取得变量2的值
preg_match('/return "(.)"/',$text,$out);
$vb_value=$out[1];
//去掉无关字符
$a=str_replace('\"','',$a);
$a=str_replace('.','',$a);
$a=str_replace($va,$va_value,$a);
$a=str_replace($vb,$vb_value,$a);
//输出
$a=fiter($a);
preg_match('/(\?php[\x01-\xff]*)\?php unset/',$a,$out);
echo($out[1]);
function fun16($var16,$var17="")
{
    global $var0, $var1, $var2, $var3, $var4, $var5, $var6, $var7, $var8, $var9, $var10, $var11, $var12, $var13, $var14, $var15;
    if (empty($var17)) {
        return base64_decode($var16);
    } else {
        return fun16(strtr($var16, $var17, strrev($var17)));
    }
}
function write_file($filename,$content){
    $myfile = fopen($filename, "w") or die("Unable to open file!");
    fwrite($myfile, $content);
    fclose($myfile);
}
function replace($p,$fomat){
    static $point=0;
    static $arr=array();
    if(array_key_exists(md5($p),$arr)){
        null;
    }else{
        $arr[md5($p)]=$point++;
    }
    return str_replace("123",$arr[md5($p)],$fomat);
}
function preg(&$str)
{
    $preg=array(
        '/\$([\x7f-\xff][\x7f-\xff]*)/e'=>'replace(\'\\1\',\'\$var123\')',
        '/function ([\x7f-\xff]+)\(/e'=>'replace(\'\\1\',\'function fun123(\')',
        '/=([\x7f-\xff]+)\(/e'=>'replace(\'\\1\',\'=fun123(\')',
        '/return ([\x7f-\xff]+)\(/e'=>'replace(\'\\1\',\'return fun123(\')',
        '/{/'=>"\r\n{\r\n",
        '/}/'=>"\r\n}\r\n",
        '/;/'=>";\r\n",
    );
    foreach ($preg as $key=>$va){
    $str=preg_replace($key,$va,$str);
}
    return $str;
}
function fiter($a){
    $a=base64_decode($a);
    $a=gzuncompress($a);
    $a=preg($a);
    return $a;
}
?>
