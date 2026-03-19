<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class PageLanding extends Composer
{
  protected static $views = [
    'page-landing'
  ];

  public function with()
  {
    return [];
  }
}
