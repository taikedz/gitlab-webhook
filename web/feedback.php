<?php

$reportfile = "last.txt";

function writefile($filename, $data)
{
    $fh = fopen($filename, 'w');
    fwrite($fh, $data);
    fclose($fh);
}

function writedata($filename, $data)
{
    try
    {
        writefile("out-$filename", $data);
    }
    catch (Exception $e)
    {
        //die($e);
    }
}

function fb($text) {
    echo "L__ $text\n";
}

?>
