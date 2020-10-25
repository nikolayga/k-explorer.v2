<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('isAddress'))
{
    function isEtheriumAddress($address)
    {
        return preg_match('/^(0x)?[0-9a-f]{40}$/i',$address); 
    }   
}


