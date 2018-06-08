<?php

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
    try {
        $value = gak($map, $key);
        
        if ( $expect == null )
            return $value != null;

        return  $value == $expect;

    } catch(JSONAccessException $e) {
        return false;
    }
}

?>
