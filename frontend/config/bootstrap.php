<?php

if(isset($_GET['admitad_uid'])){
    setcookie('admitad_uid', $_GET['admitad_uid'], time()+60*60*24*90, '/');
}