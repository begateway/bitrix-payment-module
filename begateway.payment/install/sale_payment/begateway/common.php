<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?

function _output_message($msg) {
  global $APPLICATION;

  $APPLICATION->RestartBuffer();
  echo $msg;
  die;
}

# returns URL with added query params
# http://example.com -> http://example.com?param=value
# http://example.com?param1=value -> http://example.com?param1=value&param2=value
# $url - string
# $arParams - array key = value params
function _build_return_url($url, $arParams) {
  $_url = parse_url($url);
  $_query = http_build_query($arParams);

  if (isset($_url['query'])) {
    $_url['query'] = $_url['query'] . '&' . $_query;
  } else {
    $_url['query'] = $_query;
  }

  return _http_build_url($_url);
}

function _http_build_url($parsed_url) {
  $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
  $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
  $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
  $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
  $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
  $pass     = ($user || $pass) ? "$pass@" : '';
  $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
  $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
  $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
  return "$scheme$user$pass$host$port$path$query$fragment";
}
