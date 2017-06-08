<?php

namespace rohrerj;

/**
 * Simple Template-Engine which provides arguments into a template,
 * captures the output and returns it to the caller.
 */
class SimpleTemplateEngine 
{
  /**
   * @var string Location of the directory containing the templates
   */
  private $templatePath;
  
  /**
   * @param string $templatePath 
   */
  public function __construct($templatePath) 
  {
    $this->templatePath = $templatePath;
  }
  
  /**
   * Renders a *.html.php file inside the template path
   * @return string
   */
  public function render($template, array $arguments = []) 
  {
  	if($template != "main.html.php") {
  		echo $this->render("main.html.php");
  	}
    extract($arguments);
    ob_start();
    require($this->templatePath.$template);
    return ob_get_clean();
  }
}
