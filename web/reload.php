<?php

$reportfile = "last.txt";

require("project-mapping.php");

class JSONAccessException extends Exception
{
    public function __construct($key, $code = 0, Exception $previous = null)
    {
        parent::__construct("Could not access $key", $code, $previous);
    }
}

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

function stringarray($array)
{
    $strings = array();

    foreach ( $array as $k => $v )
    {
        $strings[] = "$k: $v";
    }

    return implode("\n", $strings);
}

function path_from_token($jsondata)
{
    global $project_to_tokenpath;

    fb("validating project/token");

    $incoming_project = gak( gak($jsondata, 'project') , 'web_url' );
    $http_headers     = apache_request_headers();
    $token            = $http_headers['X-Gitlab-Token'];


    if( array_key_exists($incoming_project, $project_to_tokenpath) ) {
        $token_path = $project_to_tokenpath[$incoming_project];
        if ( $token_path[0] == $token )
            return $token_path[1];

        fb("Invalid token. We received $token");
    } else {
        fb("Unregistered project URL. We received: $incoming_project");
    }

    return null;
}

function get_action_data()
{
    fb("reading json");

    $raw_json = file_get_contents('php://input');
    $request_data = json_decode( $raw_json ) or die("JSON Decode fail");
    
    return $request_data;
}

function pretty_json($json)
{
    return json_encode($json, JSON_PRETTY_PRINT);
}

function do_update($path)
{
    global $reportfile;
    global $runner;
    $output = "";
    $retcode = 0;

    fb("running update against $path");

    exec("sudo $runner $path 2>&1", $output, $retcode);
    $outdata = implode("",$output)."\n";
    writedata($reportfile, $outdata);

    echo("$retcode - ");
    echo($outdata);
}

function gak($map, $key)
{
    // get array key

    if( property_exists($map, $key) )
    {
        return $map->$key;
    } else {
        throw new JSONAccessException($key);
    }
}

function has_value($map, $key, $expect = null)
{
    $value = gak($map, $key);
    if ( $expect == null ) return $value != null;
    return  $value == $expect;
}

function is_accepted_merge_request($json)
{
    try {
        if(!has_value($json, "object_kind", "merge_request") )
            {return false;}

        if(!has_value($json, "object_attributes") )
            {return false;}

        $oattrs = gak($json, "object_attributes");
        $status = gak($oattrs, "state");
        $target = gak($oattrs, "target_branch");

        if($target == "master" and $status = "merged")
        {
            return true;
        }
        return false;
    }
    catch(JSONAccessException $e)
    {
        return false;
    }
}

function fb($text) {
    echo "L__ $text\n";
}

function main()
{
    global $reportfile;

    // Force return data type
    header("Content-Type: text/plain\n\n");

    $json = get_action_data();

    $path = path_from_token($json);

    if ( $path == null ) {
        fb("No path obtained.");
        return 1;
    }

    if( is_accepted_merge_request($json) )
    {
        do_update($path);
    }
    else
    {
        $pretty = pretty_json($json);
        writedata($reportfile, "Not an accepted merge request:\n\n$pretty");
        echo("Did not recognize a merge request.");
        return 1;
    }
}

main();
?>
