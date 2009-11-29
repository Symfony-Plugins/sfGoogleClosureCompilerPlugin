<?php


class sfGoogleClosureCompilerFilter extends sfFilter
{

  public static $find_scripts_regexp = '#<script\s+.*?src=[\'"]([^\'"]+?)["\'].*?>#is';
  public static $find_head_regexp = '#<head>(.+)</head>#is';

  public function execute ($filterChain)
  {

    $filterChain->execute();
    // execute this filter only once
    if ($this->isFirstCall())
    {

      $response = $this->getContext()->getResponse();
      $content = $response->getContent();

      //print $content;exit;
      $scripts = self::findScripts($content);

      if ($scripts)
      {

        $urls = array();
        $script_files = array();
        foreach ($scripts as $script_url)
        {
          if (!preg_match('#^http://#', $script_url))
          {
            $script_files[]= sfConfig::get('sf_web_dir').$script_url;
            $script_url = 'http://'.$this->getContext()->getRequest()->getHost().$script_url;

          }
          $urls[]=$script_url;
        }

        $hash = self::getTimeHash($script_files, $scripts);

        
        $hash_file = sfConfig::get('sf_upload_dir').'/js_compiled/'.$hash.'.js';


        if (!file_exists($hash_file))
        {
          $compiler = new sfGoogleClosureCompiler();

          $js_compiled = $compiler->getCompiledSource($urls);

          if (!file_exists(dirname($hash_file)))
          {
            mkdir(dirname($hash_file),0777, true);
          }

          file_put_contents($hash_file, $js_compiled);
        }


        $content = self::replaceScripts($content, str_replace(array(sfConfig::get('sf_web_dir'),'\\'),array('','/'),$hash_file));

        $response->setContent($content);

      }
      //preg_match('', $subject);

    }
    // execute next filter

  }


  public static function replaceScripts($html, $to_replace)
  {
    $head_m=array();
    $scripts_m = array();
    if (preg_match(self::$find_head_regexp, $html, $head_m))
    {

      $head  = $head_m[1];
      if (preg_match_all(self::$find_scripts_regexp, $head, $scripts_m))
      {
        $head = str_replace($scripts_m[0], '', $head);
        $head = str_ireplace("</script>", '', $head);
        $head .= '<script src="'.$to_replace.'" type="text/javascript"></script>';
        $head = preg_replace("#\s+$#sm",'',$head);

        $html = str_replace($head_m[1], $head, $html);
      }
    }

    return $html;
  }

  public static function getTimeHash($files, $urls)
  {
    $times = '';
    foreach ($files as $f)
    {
      $times .= filemtime($f);
    }

    $times .= implode(',',$urls);

    return md5($times);
  }
  
  public static function findScripts($html)
  {
    $m = array();

    if (preg_match(self::$find_head_regexp, $html, $m))
    {

      if (preg_match_all(self::$find_scripts_regexp, $m[1], $m, PREG_PATTERN_ORDER))
      {
        return $m[1];
      }
    }

    return false;
  }





}