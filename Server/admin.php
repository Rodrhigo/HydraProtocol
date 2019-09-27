<?php
include_once 'Cfg.php';
include_once 'inc/Functions.php';

if(isset($_GET['server'])){
    if($_GET['server']=='stop'){

    }elseif($_GET['server']=='restart'){

    }elseif($_GET['server']=='start'){
        SwitchServerStatus(true);
    }
}
//apc_store("hello","world");
//apc_fetch('hello');
?>
<html>
<head>

</head>
<body>

<center>
    <br><br>
    Make your Custom Admin Panel<br><br>
    Server Status: Off<br><br>
    <a href="?server=stop">Stop Server</a> - <a href="?server=restart" target="_blank">Restart Server</a> - <a href="?server=start" target="_blank">Start Server</a>
</center>
<?php

?>

</body>
</html>
