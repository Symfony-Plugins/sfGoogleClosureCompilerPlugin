<?php


require_once dirname(__FILE__).'/../bootstrap/unit.php';

$c = new sfGoogleClosureCompiler();

$t = new lime_test(null, new lime_output_color());

$html =<<<ENDHTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-EN">
    <head>
      <title>Test title</title>
      <script type="text/javascript" src="http://hotsupport.ru/sfProtoculousPlugin/js/prototype.js"></script>
      <script src="/sfProtoculousPlugin/js/builder.js"    type="text/javascript"></script>
      <script src="/sfProtoculousPlugin/js/effects.js"></script>
      <script type="text/javascript" src="/js/mahalko.js" />
      <Script type="text/javascript" src="/js/site.js"/>
      
    </HEAD>
    <body>
      test body
    </body>
</html>
ENDHTML;


$t->is(
    sfGoogleClosureCompilerFilter::findScripts($html),
    array(
      'http://hotsupport.ru/sfProtoculousPlugin/js/prototype.js',
      '/sfProtoculousPlugin/js/builder.js',
      '/sfProtoculousPlugin/js/effects.js',
      '/js/mahalko.js',
      '/js/site.js'
    )
);

$t->is($c->createPostParams(array('http://jeka.ru/s1.js','http://jeka.ru/s2.js')),
  'output_format=text&output_info=compiled_code&code_url=http://jeka.ru/s1.js&code_url=http://jeka.ru/s2.js'
);

$t->is($c->getCompiledSource(array('http://hotsupport.ru/js/test1.js')), 'function hello(a){alert(a)};');
//$t->isnt($c->getCompiledSource('http://hotsupport.ru/js/test1.js'), 'function hello(a){alert(a)};');


$to_src= '/js/test.js';

$to_html =<<<ENDHTML_2
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-EN">
    <head>
      <title>Test title</title>
    <script src="$to_src" type="text/javascript"></script></HEAD>
    <body>
      test body
    </body>
</html>
ENDHTML_2;

$t->is(sfGoogleClosureCompilerFilter::replaceScripts($html, $to_src), $to_html);