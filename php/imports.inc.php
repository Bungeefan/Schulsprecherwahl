<?php
global $title;
?>
<meta charset="UTF-8">
<title><?= $title ?></title>
<meta name="description" content="Ein neues und modernes Schulsprecherwahlsystem mit Dark Theme">
<meta name="keywords" content="Schulsprecherwahl,Schule">
<meta name="author" content="Severin Hamader">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<base href="/<?= PROJECT_PATH ?>/">
<!--Styles-->
<link href="lib/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="css/message.css" rel="stylesheet" type="text/css">
<link href="css/base.css" rel="stylesheet" type="text/css">
<style>
    /* roboto-regular - latin */
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: 400;
        src: local('Roboto'), local('Roboto-Regular'),
        url('fonts/roboto-v20-latin-regular.woff2') format('woff2'), /* Chrome 26+, Opera 23+, Firefox 39+ */ url('fonts/roboto-v20-latin-regular.woff') format('woff'); /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
    }

    /* open-sans-regular - latin */
    @font-face {
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: 400;
        src: local('Open Sans Regular'), local('OpenSans-Regular'),
        url('fonts/open-sans-v17-latin-regular.woff2') format('woff2'), /* Chrome 26+, Opera 23+, Firefox 39+ */ url('fonts/open-sans-v17-latin-regular.woff') format('woff'); /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
    }
</style>

<!--Favicon-->
<link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
<link rel="manifest" href="site.webmanifest">
<link rel="mask-icon" href="safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">
