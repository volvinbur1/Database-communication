<?php
$open = fopen('/var/log/nginx/error.log', 'r');
$content = fread($open, filesize('/var/log/nginx/error.log'));
$repl = str_replace("\n", "<br><br>", "$content");
echo $repl;
fclose($open);
?>
