<?php

require("project-mapping.php");
require("feedback.php");
require("json-handler.php");
require("array-handling.php");
require("extraction.php");

function main()
{
    global $reportfile;

    // Force return data type
    header("Content-Type: text/plain\n\n");

    $event_data = get_action_data();

    $path = path_from_token($event_data);

    if ( $path == null ) {
        fb("No path obtained.");
        return 1;
    }

    if( $event_data['accepted-merge'] )
    {
        do_update($path);
    }
    else
    {
        $pretty = pretty_json($json);
        writedata($reportfile, "Not an accepted merge:\n\n$pretty");
        echo("Did not recognize a merge/pull request.");
        return 1;
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

function path_from_token($event_data)
{
    global $project_to_tokenpath;

    fb("validating project/token");

    $caller = $event_data['repository'];
    $token  = $event_data['token'];


    if( has_value($caller, $project_to_tokenpath) ) {
        $token_path = $project_to_tokenpath[$caller];

        if ( $token_path[0] == $token )
            {return $token_path[1];}

        fb("Invalid token. We received $token");
    } else {
        fb("Unregistered project URL. We received: $caller");
    }
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

main();
?>
