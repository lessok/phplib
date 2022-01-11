<?php
namespace lessok;
/*
* 字符串操作类
*/

class String {

    /**
     *
     * 截取中文字符串
     * @param string $src
     * @param int $len
     * @return string $return string
     */
    public static function cut($string, $length = 80, $etc = null, $charset='UTF-8'){
        $str_length = strlen($string);
        //字符串的字节数
        if ($str_length < $length) {
            return $string;
        }
        if (function_exists('mb_strimwidth')) {
            return mb_strimwidth($string, 0, $length * 2, $etc, $charset);
        }
    }
	//only for utf-8
	public static function len($string,$char='utf-8') {
		if (function_exists('mb_strlen')){
			return mb_strlen($string,$char);
		}
		// 将字符串分解为单元
		preg_match_all("/./us", $string, $match);
		// 返回单元个数
		return count($match[0]);
	}
    /**
     * 根据传入的key解密字符串
     *
     */
    static function  decrypt($string, $key) {
        $result = '';
        $string = base64_decode($string);

        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }
        return $result;
    }
    /**
     * 根据传入的key加密字符串
     * @return string
     */
    static function encrypt($string, $key) {
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }


	//字符串安全处理
	public static function safe($string){
	    return is_array($string) ? array_map(array(__CLASS__,'safe'), $string) : str_replace(array( '&', '"', "'", '<', '>', ';'), '', $string);
	}

}