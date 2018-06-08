<?php

class JSONAccessException extends Exception
{
    public function __construct($key, $code = 0, Exception $previous = null)
    {
        parent::__construct("Could not access $key", $code, $previous);
    }
}

function pretty_json($json)
{
    return json_encode($json, JSON_PRETTY_PRINT);
}

function get_action_data()
{
    $json = get_json_data();

    if( has_value($_POST, "X-Gitlab-Token") )
        { return extract_gitlab_data($json); }

    elseif( has_value($_POST, "X-GitHub-Event")
        { return extract_github_data($json); }
    
    return null;
}

function get_json_data()
{
    fb("reading json");

    $raw_json = file_get_contents('php://input');
    $request_data = json_decode( $raw_json ) or die("JSON Decode fail");
    
    return $request_data;
}

?>
