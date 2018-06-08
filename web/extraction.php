<?php

$http_headers = null;

function get_http_headers()
{
    global $http_headers;

    if( $http_headers != null )
        {return $http_headers;}

    $http_headers = apache_request_headers();
}

// ++++++++++++++++++++++++++++++++++++
// Gitlab

function extract_gitlab_data($json)
{
    global $http_headers;
    get_http_headers();

    $data = array();

    $data['token'] = gak($http_headers, 'X-Gitlab-Token');
    $data['repository'] = gak(gak($json, 'project'), 'web_url');
    $data['accepted-merge'] = is_accepted_gitlab_merge_request($json);

    return $data;
}

function is_accepted_gitlab_merge_request($json)
{
    try
    {
        if(!has_value($json, "object_kind", "merge_request") )
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

// ++++++++++++++++++++++++++++++++++++
// Github

function extract_github_data($json)
{
    $data = array();
    
    $data['token'] = gak(gak(gak($json, 'hook'), 'config'), 'secret');
    $data['repository'] = gak(gak($json, 'repository'), 'html_url');
    $data['accepted-merge'] = is_accepted_github_pull_request($json);
}

function is_accepted_github_pull_request($json)
{
    try
    {
        if(!has_value($json, 'pull_request')
            {return false;}

        if(!has_value(gak(gak($json, 'pull_request'), 'base'), 'ref', 'master')
            {return false;}

        if(!has_value($json, 'action', 'closed'))
            {return false;}

        if(!has_value(gak($json, 'pull_request'), 'merged', true)
            {return false;}

    }
    catch(JSONAccessException $e)
    {
        return false;
    }

    return true;
}

?>
