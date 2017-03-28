<!DOCTYPE html>
<!-- saved from url=(0030)http://caniuse.com/#comparison -->
<html dir="ltr" lang="en-US" class=""><!--<![endif]--><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title><?php bloginfo('name'); ?> <?php wp_title(' - ', true, 'left'); ?></title>

	<meta name="viewport" content="width=device-width">

    <link href="http://fonts.googleapis.com/css?family=Inconsolata|Open+Sans:300,700" rel="stylesheet">
	<link rel="icon" href="<?php bloginfo('url'); ?>/assets/images/favicon.png" sizes="16x16 32x32 64x64 128x128" type="image/png">
	<link rel="apple-touch-icon" href="<?php bloginfo('url'); ?>/assets/images/favicon.png">

	<meta name="keywords" content="web browser compatibility support html css svg html5 css3 opera chrome firefox safari internet explorer">

	<style>
	<?php echo $theme_options['custom_css']; ?>
	</style>

</head>
<body class="">
	<?php global $theme_options,  $main_menu; ?>
	<div id="wrapper" class="wrapper">
	  <div class='top-menu'>
			<ul id="snav" class="menu">
				<?php
				if ( has_nav_menu( 'top_navigation' ) ) {
					wp_nav_menu(array('theme_location' => 'top_navigation', 'depth' => 5, 'container' => false, 'menu_id' => 'snav', 'items_wrap' => '%3$s'));
				}
				?>
			</ul>
			<?php if(tf_checkIfMenuIsSetByLocation('top_navigation')): ?>
				<div class="mobile-topnav-holder"></div>
			<?php endif; ?>
		</div>
		<header id="header" class="header">
			<?php if($theme_options['header_show_logo']): ?>
			<div id="logo" class="logo" >
				<a href="<?php bloginfo('url'); ?>">
					<?php if($theme_options['logo']['url']): ?>
					<img src="<?php echo $theme_options['logo']['url']; ?>" alt="<?php bloginfo('name'); ?>" class="site_logo" />
					<?php endif; ?>
					<?php if($theme_options['logo_retina']['url']): ?>
					<img src="<?php echo $theme_options["logo_retina"]['url']; ?>" alt="<?php bloginfo('name'); ?>" class="site_logo ratina" />
					<?php endif; ?>
				</a>
			</div>
			<?php endif; ?>
			<?php if($theme_options['header_show_site_title'] || $theme_options['header_show_tagline'] ): ?>
				<div id="logo_text">
					<?php if($theme_options['header_show_site_title']){
						$site_title = get_bloginfo( 'name' );
						if(!empty($site_title)):?>
							<div id="site_title"><a id="site_name" href="<?php echo home_url( '/' ); ?>"><?php bloginfo('name'); ?></a></div>
						<?php endif; }?>
					<?php if($theme_options['header_show_tagline']){
						$site_desc = get_bloginfo( 'description' );
						if(!empty($site_desc)):?>
							<div id="site_description"><?php bloginfo( 'description' ); ?></div>
						<?php endif; }?>
				</div>
			<?php endif; ?>
			<div id="main_search" class="main_search">
				<input type="text" class="main_search_input" name="s" autocomplete="off" autofocus="" value="" placeholder="Search">
			</div>
			<div class="clear"></div>
        </header>
		<nav id="nav" class="nav">					
			<ul id="main_menu" class="navigation menu nav">
				<?php echo $main_menu;?>
			</ul>
		</nav>
		<style>
		.top-menu {
			display: block;
			width: 100%;
			background-color: #FFF;
			margin-top: 10px;
			border-bottom: 1px solid #E8E8E8;
		}
		.top-menu #snav {
			list-style: outside none none;
			margin: 0px;
			padding: 21px;
			color: rgb(255, 255, 255);
		}
		.top-menu ul li {
			float: left;
			padding: 0px 40px 0px 0px;
			margin: 0px;
			position: relative;
			color: #FFF;
			top: -9px;
		}
		.header {
			padding: 10px;
			margin-top: 0px;
			background-color: #FFF;
			box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1) inset;
		}
		#snav li::after {
			content: "/";
			color: rgb(186, 47, 0);
			position: absolute;
			right: 18px;
			top: 1px;
		}
		#snav li:last-child::after {
			content: "";
			display:none;
		}
	
		</style>