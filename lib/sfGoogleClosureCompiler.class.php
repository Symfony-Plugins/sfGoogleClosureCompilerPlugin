<?php

/**
 * Closure Compiler util class
 */
class sfGoogleClosureCompiler
{

  private $__compile_service_url = 'http://closure-compiler.appspot.com/compile';
  private $__service_init_params = 'output_format=text&output_info=compiled_code';

  public function createPostParams(array $urls)
  {
    $params = $this->__service_init_params;
    $br = new sfWebBrowser();
    foreach ($urls as $url)
    {
      $params.='&code_url='.$url;
      //$br->get($url);
      //$js_content = $br->getResponseText();

      //$params .= '&'.http_build_query(array('js_code'=>$js_content));
    }

    return $params;
  }

  public function getCompiledSource(array $urls)
  {
    $browser = new sfWebBrowser(array('Content-type'=>'application/x-www-form-urlencoded'));
    
    $browser->post($this->__compile_service_url, self::createPostParams($urls));

    return trim($browser->getResponseText());
    
  }

}


