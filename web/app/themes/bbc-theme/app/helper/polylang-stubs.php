<?php

if (!function_exists('pll__')) {
  function pll__($string)
  {
    return $string;
  }
}

if (!function_exists('pll_e')) {
  function pll_e($string)
  {
    echo $string;
  }
}

if (!function_exists('pll_register_string')) {
  function pll_register_string($name, $string, $group = 'Polylang', $multiline = false)
  {
    return true;
  }
}
