<?php

namespace beGateway;

/* the class converts text from/to UTF-8 */

class Utf8 {

  // convert to UTF-8 from locale
  public static function from($str, $locale = 'WINDOWS-1251', $size = 0) {
    $in = self::cut($str, $size);

    return mb_convert_encoding($in, 'UTF-8', $locale);
  }

  // convert from UTF-8 to locale
  public static function to($str, $locale = 'WINDOWS-1251', $size = 0) {
    $in = self::cut($str, $size);
    return mb_convert_encoding($in, $locale, 'UTF-8');
  }

  public static function cut($str, $size) {
    $out = $str;

    if ($size > 0)
      $out = substr($str, $size);

    return $out;
  }
}
?>
