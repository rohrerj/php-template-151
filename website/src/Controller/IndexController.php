<?php

namespace mineichen\Controller;

use mineichen\SimpleTemplateEngine;

class IndexController 
{
  /**
   * @var mineichen\SimpleTemplateEngine Template engines to render output
   */
  private $template;
  
  /**
   * @param mineichen\SimpleTemplateEngine
   */
  public function __construct(SimpleTemplateEngine $template)
  {
     $this->template = $template;
  }

  public function homepage() {
    echo "INDEX";
  }

  public function greet($name) {
  	echo $this->template->render("hello.html.php", ["name" => $name]);
  }
}
