<?php

// Version: 0.0.1

if( ! $core ) exit;

?><!DOCTYPE html>
<!--
___________.__                    .__                                       .__       .___                     __   
\_   _____/|__| ______  _  ______ |  |__   ____   ___________  _____   ____ |  |    __| _/____ _____    ______/  |_ 
 |    __)_ |  |/    \ \/ \/ /  _ \|  |  \ /    \_/ __ \_  __ \/     \_/ __ \|  |   / __ |/ __ \\__  \  /     \   __\
 |        \|  |   |  \     (  <_> )   Y  \   |  \  ___/|  | \/  Y Y  \  ___/|  |__/ /_/ \  ___/ / __ \|  Y Y  \  |  
/_______  /|__|___|  /\/\_/ \____/|___|  /___|  /\___  >__|  |__|_|  /\___  >____/\____ |\___  >____  /__|_|  /__|  
	\/         \/                  \/     \/     \/            \/     \/           \/    \/     \/      \/   
-->
<html lang="en">
<head>
	<title>Einwohnermeldeamt</title>

	<meta tag="generator" content="Einwohnermeldeamt v.<?= $core->version() ?>">

	<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%2210 0 100 100%22><text y=%22.90em%22 font-size=%2290%22>🗝️</text></svg>">

	<style>
		html, body {
			margin: 0;
			padding: 0;
		}
		body {
			background: #eee;
		}
		main {
			min-height: calc(100vh - 40px);
			box-sizing: border-box;
			padding: 60px 20px;
		}
		.box {
			width: 100%;
			max-width: 800px;
			margin: 0 auto;
			border: 1px solid #ccc;
			padding: 40px;
			border-radius: 10px;
			background: #fff;
		}
			.box > *:first-child {
				margin-top: 0;
			}
		footer {
			text-align: right;
			font-size: 0.8em;
			padding: 10px 20px;
		}
	</style>
</head>
<body>
<main>
	<div class="box">
