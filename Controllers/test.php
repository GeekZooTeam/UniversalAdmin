<?php


class Action
{
    function index()
    {
        Uploadr::saveFILES($_FILES['some_name'], './tmp');
    }
}

?>