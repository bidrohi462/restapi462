<?php
if(isset($_GET['request']) && !empty($_GET['request']))
	echo $_GET['request'];
if(isset($_GET['q']) && !empty($_GET['q']))
	echo " ".$_GET['q'];
echo " Minhajul Bashir";