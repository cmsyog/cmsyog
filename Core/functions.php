<?php
function insert_charset_header()
{
    header('Content-Type: text/html; charset=UTF-8');
}



function can_start_session(){
    if(!empty($_GET['PHPSESSID'])) {
        return true;
    }
    $session_id = session_id();
    return empty($session_id) ? true : false;
}