</div>
<?php 
global $switched;
switch_to_blog(1); 
global $theme_options;
?>
<footer class="footer-area">
	<div class="aione-row">
		<section class="aione-columns row aione-columns-<?php echo $theme_options['footer_widgets_columns']; ?> columns columns-<?php echo $theme_options['footer_widgets_columns']; ?>">
			<?php 
			$column_width = 12 / $theme_options['footer_widgets_columns'];
			if( $theme_options['footer_widgets_columns'] == '5' ) {
				$column_width = 2;
			}
			?>
			
		
			<?php if( $theme_options['footer_widgets_columns'] >= 1 ): ?>
			<article class="aione-column col <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?> ">
				<div id="footer_nav_menu_1" class="footer-widget-col widget_nav_menu">
				<h3>Learning</h3>
				<?php wp_nav_menu( array('menu' => 'Footer Column 1' )); ?>
				</div>
			</article>
			<?php endif; ?>
			
			<?php if( $theme_options['footer_widgets_columns'] >= 2 ): ?>
			<article class="aione-column col <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?>">
				<div id="footer_nav_menu_2" class="footer-widget-col widget_nav_menu">
				<h3>Solutions</h3>
				<?php wp_nav_menu( array('menu' => 'Footer Column 2' )); ?>
				</div>
			</article>
			<?php endif; ?>
			
			<?php if( $theme_options['footer_widgets_columns'] >= 3 ): ?>
			<article class="aione-column col <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?>">
				<div id="footer_nav_menu_3" class="footer-widget-col widget_nav_menu">
				<h3>Links</h3>
				<?php wp_nav_menu( array('menu' => 'Footer Column 3' )); ?>
				</div>
			</article>
			<?php endif; ?>
			
			<?php if( $theme_options['footer_widgets_columns'] >= 4 ): ?>
			<article class="aione-column col last <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?>">
				<div id="footer_nav_menu_4" class="footer-widget-col widget_nav_menu">
				<h3>Account</h3>
				<?php wp_nav_menu( array('menu' => 'Footer Column 4' )); ?>
				</div>
			</article>
			<?php endif; ?>

			<?php if( $theme_options['footer_widgets_columns'] >= 5 ): ?>
			<article class="aione-column col last <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?>">
				<div id="footer_nav_menu_5" class="footer-widget-col widget_nav_menu">
				<h3>Navigation</h3>
				<?php wp_nav_menu( array('menu' => 'Footer Column 5' )); ?>
				</div>
			</article>
			<?php endif; ?>

			<?php if( $theme_options['footer_widgets_columns'] >= 6 ): ?>
			<article class="aione-column col last <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?>">
				<div id="footer_nav_menu_6" class="footer-widget-col widget_nav_menu">
				<h3>Navigation</h3>
				<?php wp_nav_menu( array('menu' => 'Footer Column 6' )); ?>
				</div>
			</article>
			<?php endif; ?>
			<div class="aione-clearfix"></div>
		</section>
	</div>
</footer>
<footer id="footer">
	<div class="aione-row">
		<div class="copyright-area-content">
			<div class="copyright">
				<div><?php echo do_shortcode( $theme_options['footer_text'] ); ?></div>
			</div>
		</div>
	</div>
</footer>
<?php restore_current_blog(); ?>

	<link rel="stylesheet" href="<?php echo  plugin_dir_url()."aione-page-templates/templates/assets/css/dark.css";?>" media="all">
    <script src="<?php echo  plugin_dir_url()."aione-page-templates/templates/assets/js/jquery-1.9.0.min.js";?>"></script>
    <script src="<?php echo  plugin_dir_url()."aione-page-templates/templates/assets/js/jquery.address-1.6.min.js";?>"></script>
	<style>
.row {
  margin-left: -15px;
  margin-right: -15px;
}
.col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
  position: relative;
  min-height: 1px;
  padding-left: 15px;
  padding-right: 15px;
}
.col-xs-1, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9, .col-xs-10, .col-xs-11, .col-xs-12 {
  float: left;
}
.col-xs-12 {
  width: 100%;
}
.col-xs-11 {
  width: 91.66666666666666%;
}
.col-xs-10 {
  width: 83.33333333333334%;
}
.col-xs-9 {
  width: 75%;
}
.col-xs-8 {
  width: 66.66666666666666%;
}
.col-xs-7 {
  width: 58.333333333333336%;
}
.col-xs-6 {
  width: 50%;
}
.col-xs-5 {
  width: 41.66666666666667%;
}
.col-xs-4 {
  width: 33.33333333333333%;
}
.col-xs-3 {
  width: 25%;
}
.col-xs-2 {
  width: 16.666666666666664%;
}
.col-xs-1 {
  width: 8.333333333333332%;
}
.col-xs-pull-12 {
  right: 100%;
}
.col-xs-pull-11 {
  right: 91.66666666666666%;
}
.col-xs-pull-10 {
  right: 83.33333333333334%;
}
.col-xs-pull-9 {
  right: 75%;
}
.col-xs-pull-8 {
  right: 66.66666666666666%;
}
.col-xs-pull-7 {
  right: 58.333333333333336%;
}
.col-xs-pull-6 {
  right: 50%;
}
.col-xs-pull-5 {
  right: 41.66666666666667%;
}
.col-xs-pull-4 {
  right: 33.33333333333333%;
}
.col-xs-pull-3 {
  right: 25%;
}
.col-xs-pull-2 {
  right: 16.666666666666664%;
}
.col-xs-pull-1 {
  right: 8.333333333333332%;
}
.col-xs-pull-0 {
  right: 0%;
}
.col-xs-push-12 {
  left: 100%;
}
.col-xs-push-11 {
  left: 91.66666666666666%;
}
.col-xs-push-10 {
  left: 83.33333333333334%;
}
.col-xs-push-9 {
  left: 75%;
}
.col-xs-push-8 {
  left: 66.66666666666666%;
}
.col-xs-push-7 {
  left: 58.333333333333336%;
}
.col-xs-push-6 {
  left: 50%;
}
.col-xs-push-5 {
  left: 41.66666666666667%;
}
.col-xs-push-4 {
  left: 33.33333333333333%;
}
.col-xs-push-3 {
  left: 25%;
}
.col-xs-push-2 {
  left: 16.666666666666664%;
}
.col-xs-push-1 {
  left: 8.333333333333332%;
}
.col-xs-push-0 {
  left: 0%;
}
.col-xs-offset-12 {
  margin-left: 100%;
}
.col-xs-offset-11 {
  margin-left: 91.66666666666666%;
}
.col-xs-offset-10 {
  margin-left: 83.33333333333334%;
}
.col-xs-offset-9 {
  margin-left: 75%;
}
.col-xs-offset-8 {
  margin-left: 66.66666666666666%;
}
.col-xs-offset-7 {
  margin-left: 58.333333333333336%;
}
.col-xs-offset-6 {
  margin-left: 50%;
}
.col-xs-offset-5 {
  margin-left: 41.66666666666667%;
}
.col-xs-offset-4 {
  margin-left: 33.33333333333333%;
}
.col-xs-offset-3 {
  margin-left: 25%;
}
.col-xs-offset-2 {
  margin-left: 16.666666666666664%;
}
.col-xs-offset-1 {
  margin-left: 8.333333333333332%;
}
.col-xs-offset-0 {
  margin-left: 0%;
}
@media (min-width: 800px) {
  .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
    float: left;
  }
  .col-sm-12 {
    width: 100%;
  }
  .col-sm-11 {
    width: 91.66666666666666%;
  }
  .col-sm-10 {
    width: 83.33333333333334%;
  }
  .col-sm-9 {
    width: 75%;
  }
  .col-sm-8 {
    width: 66.66666666666666%;
  }
  .col-sm-7 {
    width: 58.333333333333336%;
  }
  .col-sm-6 {
    width: 50%;
  }
  .col-sm-5 {
    width: 41.66666666666667%;
  }
  .col-sm-4 {
    width: 33.33333333333333%;
  }
  .col-sm-3 {
    width: 25%;
  }
  .col-sm-2 {
    width: 16.666666666666664%;
  }
  .col-sm-1 {
    width: 8.333333333333332%;
  }
  .col-sm-pull-12 {
    right: 100%;
  }
  .col-sm-pull-11 {
    right: 91.66666666666666%;
  }
  .col-sm-pull-10 {
    right: 83.33333333333334%;
  }
  .col-sm-pull-9 {
    right: 75%;
  }
  .col-sm-pull-8 {
    right: 66.66666666666666%;
  }
  .col-sm-pull-7 {
    right: 58.333333333333336%;
  }
  .col-sm-pull-6 {
    right: 50%;
  }
  .col-sm-pull-5 {
    right: 41.66666666666667%;
  }
  .col-sm-pull-4 {
    right: 33.33333333333333%;
  }
  .col-sm-pull-3 {
    right: 25%;
  }
  .col-sm-pull-2 {
    right: 16.666666666666664%;
  }
  .col-sm-pull-1 {
    right: 8.333333333333332%;
  }
  .col-sm-pull-0 {
    right: 0%;
  }
  .col-sm-push-12 {
    left: 100%;
  }
  .col-sm-push-11 {
    left: 91.66666666666666%;
  }
  .col-sm-push-10 {
    left: 83.33333333333334%;
  }
  .col-sm-push-9 {
    left: 75%;
  }
  .col-sm-push-8 {
    left: 66.66666666666666%;
  }
  .col-sm-push-7 {
    left: 58.333333333333336%;
  }
  .col-sm-push-6 {
    left: 50%;
  }
  .col-sm-push-5 {
    left: 41.66666666666667%;
  }
  .col-sm-push-4 {
    left: 33.33333333333333%;
  }
  .col-sm-push-3 {
    left: 25%;
  }
  .col-sm-push-2 {
    left: 16.666666666666664%;
  }
  .col-sm-push-1 {
    left: 8.333333333333332%;
  }
  .col-sm-push-0 {
    left: 0%;
  }
  .col-sm-offset-12 {
    margin-left: 100%;
  }
  .col-sm-offset-11 {
    margin-left: 91.66666666666666%;
  }
  .col-sm-offset-10 {
    margin-left: 83.33333333333334%;
  }
  .col-sm-offset-9 {
    margin-left: 75%;
  }
  .col-sm-offset-8 {
    margin-left: 66.66666666666666%;
  }
  .col-sm-offset-7 {
    margin-left: 58.333333333333336%;
  }
  .col-sm-offset-6 {
    margin-left: 50%;
  }
  .col-sm-offset-5 {
    margin-left: 41.66666666666667%;
  }
  .col-sm-offset-4 {
    margin-left: 33.33333333333333%;
  }
  .col-sm-offset-3 {
    margin-left: 25%;
  }
  .col-sm-offset-2 {
    margin-left: 16.666666666666664%;
  }
  .col-sm-offset-1 {
    margin-left: 8.333333333333332%;
  }
  .col-sm-offset-0 {
    margin-left: 0%;
  }
}
@media (min-width: 992px) {
  .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
    float: left;
  }
  .col-md-12 {
    width: 100%;
  }
  .col-md-11 {
    width: 91.66666666666666%;
  }
  .col-md-10 {
    width: 83.33333333333334%;
  }
  .col-md-9 {
    width: 75%;
  }
  .col-md-8 {
    width: 66.66666666666666%;
  }
  .col-md-7 {
    width: 58.333333333333336%;
  }
  .col-md-6 {
    width: 50%;
  }
  .col-md-5 {
    width: 41.66666666666667%;
  }
  .col-md-4 {
    width: 33.33333333333333%;
  }
  .col-md-3 {
    width: 25%;
  }
  .col-md-2 {
    width: 16.666666666666664%;
  }
  .col-md-1 {
    width: 8.333333333333332%;
  }
  .col-md-pull-12 {
    right: 100%;
  }
  .col-md-pull-11 {
    right: 91.66666666666666%;
  }
  .col-md-pull-10 {
    right: 83.33333333333334%;
  }
  .col-md-pull-9 {
    right: 75%;
  }
  .col-md-pull-8 {
    right: 66.66666666666666%;
  }
  .col-md-pull-7 {
    right: 58.333333333333336%;
  }
  .col-md-pull-6 {
    right: 50%;
  }
  .col-md-pull-5 {
    right: 41.66666666666667%;
  }
  .col-md-pull-4 {
    right: 33.33333333333333%;
  }
  .col-md-pull-3 {
    right: 25%;
  }
  .col-md-pull-2 {
    right: 16.666666666666664%;
  }
  .col-md-pull-1 {
    right: 8.333333333333332%;
  }
  .col-md-pull-0 {
    right: 0%;
  }
  .col-md-push-12 {
    left: 100%;
  }
  .col-md-push-11 {
    left: 91.66666666666666%;
  }
  .col-md-push-10 {
    left: 83.33333333333334%;
  }
  .col-md-push-9 {
    left: 75%;
  }
  .col-md-push-8 {
    left: 66.66666666666666%;
  }
  .col-md-push-7 {
    left: 58.333333333333336%;
  }
  .col-md-push-6 {
    left: 50%;
  }
  .col-md-push-5 {
    left: 41.66666666666667%;
  }
  .col-md-push-4 {
    left: 33.33333333333333%;
  }
  .col-md-push-3 {
    left: 25%;
  }
  .col-md-push-2 {
    left: 16.666666666666664%;
  }
  .col-md-push-1 {
    left: 8.333333333333332%;
  }
  .col-md-push-0 {
    left: 0%;
  }
  .col-md-offset-12 {
    margin-left: 100%;
  }
  .col-md-offset-11 {
    margin-left: 91.66666666666666%;
  }
  .col-md-offset-10 {
    margin-left: 83.33333333333334%;
  }
  .col-md-offset-9 {
    margin-left: 75%;
  }
  .col-md-offset-8 {
    margin-left: 66.66666666666666%;
  }
  .col-md-offset-7 {
    margin-left: 58.333333333333336%;
  }
  .col-md-offset-6 {
    margin-left: 50%;
  }
  .col-md-offset-5 {
    margin-left: 41.66666666666667%;
  }
  .col-md-offset-4 {
    margin-left: 33.33333333333333%;
  }
  .col-md-offset-3 {
    margin-left: 25%;
  }
  .col-md-offset-2 {
    margin-left: 16.666666666666664%;
  }
  .col-md-offset-1 {
    margin-left: 8.333333333333332%;
  }
  .col-md-offset-0 {
    margin-left: 0%;
  }
}
@media (min-width: 1200px) {
  .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12 {
    float: left;
  }
  .col-lg-12 {
    width: 100%;
  }
  .col-lg-11 {
    width: 91.66666666666666%;
  }
  .col-lg-10 {
    width: 83.33333333333334%;
  }
  .col-lg-9 {
    width: 75%;
  }
  .col-lg-8 {
    width: 66.66666666666666%;
  }
  .col-lg-7 {
    width: 58.333333333333336%;
  }
  .col-lg-6 {
    width: 50%;
  }
  .col-lg-5 {
    width: 41.66666666666667%;
  }
  .col-lg-4 {
    width: 33.33333333333333%;
  }
  .col-lg-3 {
    width: 25%;
  }
  .col-lg-2 {
    width: 16.666666666666664%;
  }
  .col-lg-1 {
    width: 8.333333333333332%;
  }
  .col-lg-pull-12 {
    right: 100%;
  }
  .col-lg-pull-11 {
    right: 91.66666666666666%;
  }
  .col-lg-pull-10 {
    right: 83.33333333333334%;
  }
  .col-lg-pull-9 {
    right: 75%;
  }
  .col-lg-pull-8 {
    right: 66.66666666666666%;
  }
  .col-lg-pull-7 {
    right: 58.333333333333336%;
  }
  .col-lg-pull-6 {
    right: 50%;
  }
  .col-lg-pull-5 {
    right: 41.66666666666667%;
  }
  .col-lg-pull-4 {
    right: 33.33333333333333%;
  }
  .col-lg-pull-3 {
    right: 25%;
  }
  .col-lg-pull-2 {
    right: 16.666666666666664%;
  }
  .col-lg-pull-1 {
    right: 8.333333333333332%;
  }
  .col-lg-pull-0 {
    right: 0%;
  }
  .col-lg-push-12 {
    left: 100%;
  }
  .col-lg-push-11 {
    left: 91.66666666666666%;
  }
  .col-lg-push-10 {
    left: 83.33333333333334%;
  }
  .col-lg-push-9 {
    left: 75%;
  }
  .col-lg-push-8 {
    left: 66.66666666666666%;
  }
  .col-lg-push-7 {
    left: 58.333333333333336%;
  }
  .col-lg-push-6 {
    left: 50%;
  }
  .col-lg-push-5 {
    left: 41.66666666666667%;
  }
  .col-lg-push-4 {
    left: 33.33333333333333%;
  }
  .col-lg-push-3 {
    left: 25%;
  }
  .col-lg-push-2 {
    left: 16.666666666666664%;
  }
  .col-lg-push-1 {
    left: 8.333333333333332%;
  }
  .col-lg-push-0 {
    left: 0%;
  }
  .col-lg-offset-12 {
    margin-left: 100%;
  }
  .col-lg-offset-11 {
    margin-left: 91.66666666666666%;
  }
  .col-lg-offset-10 {
    margin-left: 83.33333333333334%;
  }
  .col-lg-offset-9 {
    margin-left: 75%;
  }
  .col-lg-offset-8 {
    margin-left: 66.66666666666666%;
  }
  .col-lg-offset-7 {
    margin-left: 58.333333333333336%;
  }
  .col-lg-offset-6 {
    margin-left: 50%;
  }
  .col-lg-offset-5 {
    margin-left: 41.66666666666667%;
  }
  .col-lg-offset-4 {
    margin-left: 33.33333333333333%;
  }
  .col-lg-offset-3 {
    margin-left: 25%;
  }
  .col-lg-offset-2 {
    margin-left: 16.666666666666664%;
  }
  .col-lg-offset-1 {
    margin-left: 8.333333333333332%;
  }
  .col-lg-offset-0 {
    margin-left: 0%;
  }
}
.footer-widget-col {
  margin-bottom: 20px;
}
.footer-widget-col:last-child {
  margin-bottom: 0;
}
.footer-widget-col .row,
.footer-area .footer-widget-col .columns {
  margin-left: 0;
  margin-right: 0;
}
.footer-area .footer-widget-col .col {
  padding-left: 3px;
  padding-right: 3px;
  padding-bottom: 3px;
}
.footer-widget-col .flexslider a {
  border: 0;
  padding: 0;
}
.footer-area {
  border-top: 12px solid #e9eaee;
  background: #363839;
  padding: 43px 10px 40px;
  color: #8c8989;
  position: relative;
  overflow: hidden;
}
.footer-area .logo {
  float: none;
  display: block;
  margin: 0 0 22px;
}
.footer-area h3 {
  margin: 0 0 28px;
  color: #ddd;
  text-transform: uppercase;
  font: 13px/20px 'PTSansBold', arial, helvetica, sans-serif;
}
.footer-area .columns {
  margin: 0;
}
.footer-area .text-block {
  text-shadow: 1px 2px 1px #000;
}
.footer-area .holder-block img {
  width: 100%;
}
.footer-area ul {
  list-style: none;
  margin: 0;
  padding: 0;
  font-size: 12px;
  line-height: 15px;
}
.footer-area ul li a {
  padding: 12px 0;
  border-bottom: 1px solid #282a2b;
  display: block;
}
.footer-area .footer-widget-col:not(.widget_icl_lang_sel_widget) ul:first-child > li:first-child > a,
.footer-area .footer-widget-col:not(.widget_icl_lang_sel_widget) > ul > li:first-child > a {
  background-image: none;
  padding-top: 0px;
}
.footer-area a {
  text-shadow: 1px 2px 1px #000;
  color: #bfbfbf;
}
.footer-area ul li a:hover {
  color: #a0ce4e;
}
.footer-area ul#recentcomments li.recentcomments:first-child {
  padding-top: 0px;
  background: none;
}
.footer-area li.recentcomments {
  padding: 12px 0;
  border-bottom: 1px solid #282a2b;
  display: block;
}
.footer-area li.recentcomments a {
  border: none;
}
.footer-area .widget_recent_entries li {
  border-bottom: 1px solid;
  padding-bottom: 12px;
}
.footer-area .widget_recent_entries a {
  border-bottom: 0;
  padding-bottom: 0;
}
#footer {
  z-index: 1;
  position: relative;
  padding: 18px 10px 12px;
  background: #282a2b;
  border-top: 1px solid #4b4c4d;
}
#footer .copyright-area-content {
  display: table;
  width: 100%;
}
.copyright {
  display: table-cell;
  vertical-align: middle;
  margin: 0;
  padding: 0;
  color: #8c8989;
  font-size: 12px;
  text-shadow: 1px 2px 1px #000;
}
.copyright a {
  color: #bfbfbf;
}
.footer-area {
  border-top: none;
  margin-top:10px;
  background-color: #1570a6;
	background-image: url(http://darlic.com/wp-content/uploads/2015/02/bg_color_section.png);
	background-repeat: repeat;
	background-clip: border-box;
	background-origin: border-box;
	background-size: inherit;
	background-attachment: scroll;
	background-position: center top;
}
.footer-area article {
    padding: 0;
}

.footer-area article .widget_nav_menu{
	padding: 0 20px;
}

.footer-area h3 {
  color: #FFFFFF;
  border-bottom: 5px solid #ffffff;
  margin-bottom: 10px;
  cursor: pointer;
  padding: 10px 0px;
  text-transform: none;
  font-size: 24px;
  font-weight: 100;
  font-family: "Open sans", Arial, Helvetica, sans-serif;
  -webkit-transition: all 0.2s ease-in-out;
  -moz-transition: all 0.2s ease-in-out;
  -o-transition: all 0.2s ease-in-out;
  transition: all 0.2s ease-in-out;
}
.footer-area h3:hover{
    background-color: rgba(0,0,0,0.2);
    text-indent: 10px;
  }
.footer-area h3:after {
  content: "";
  width: 0px;
  height: 5px;
  background-color: #08324A;
  display: block;
  position: absolute;
  top: 40px;
    -webkit-transition: all 0.2s ease-in-out;
  -moz-transition: all 0.2s ease-in-out;
  -o-transition: all 0.2s ease-in-out;
  transition: all 0.2s ease-in-out;
}
.footer-area h3:hover:after {
  width: 88%;
}


.footer-area ul li a{
    color:#ffffff;
  border:none;
  font-weight: 100;
  font-size: 15px;
  padding:12px 0 12px 15px !important;
  font-family: "Open Sans", Arial, Helvetica, sans-serif;
  	-webkit-transition: all 0.2s ease-in-out;
	-moz-transition: all 0.2s ease-in-out;
	-o-transition: all 0.2s ease-in-out;
	transition: all 0.2s ease-in-out;
}
.footer-area ul li:hover a, #wrapper .footer-area .current_page_item > a, 
#wrapper .footer-area .current-menu-item > a{
  background-color: rgba(0,0,0,0.1);
  padding-left:21px !important;
  color:#FFFFFF;
}
.footer-area ul li{
  border-bottom:1px solid rgba(255,255,255,0.5);
  	-webkit-transition: all 0.2s ease-in-out;
	-moz-transition: all 0.2s ease-in-out;
	-o-transition: all 0.2s ease-in-out;
	transition: all 0.2s ease-in-out;
}
.footer-area ul > li:before{
  content: "";
  border-left: 5px solid #ffffff;
  position: absolute;
  border-top: 5px solid transparent;
  border-bottom: 5px solid transparent;
  margin-top: 14px;
  	-webkit-transition: all 0.2s ease-in-out;
	-moz-transition: all 0.2s ease-in-out;
	-o-transition: all 0.2s ease-in-out;
	transition: all 0.2s ease-in-out;
 }
 .footer-area ul > li:hover:before, #wrapper .footer-area .current_page_item:before {
  margin-left: 6px;
 }
#footer{  
    border-top: none;
    text-align:center;
    color:#FFFFFF;
	background-color:#0e4667;
}
.copyright, .copyright a{
    font-size:16px;
    color:#FFFFFF;
    font-weight: 100;
    font-family: "Open Sans", Arial, Helvetica, sans-serif;
	-webkit-transition: all 0.1s ease-in-out;
	-moz-transition: all 0.1s ease-in-out;
	-o-transition: all 0.1s ease-in-out;
	transition: all 0.1s ease-in-out;
}
.copyright a:hover{
    color:#FFFFFF;
}
.footer-area a {
    text-shadow: none;
}
	
	</style>

	<script>
		$(document).ready(function(){
			// Optional code to hide all divs
			$(".feature-block").hide();
			
			var urlHash = $.address.path();
			urlHash = urlHash.substr(1, urlHash.length);
			$("#" + urlHash ).show();
			$('.' + urlHash ).parent('li').addClass("active").siblings('li').removeClass("active");
			
			// Show chosen div, and hide all others
			$("ul.list a").click(function (e) {
				e.preventDefault();
				$(this).parent('li').addClass("active").siblings('li').removeClass("active");
				$("#" + $(this).attr("class")).show().siblings('div').hide();
				$('html, body').animate({scrollTop:160}, 500);
				$.address.value($(this).attr('class'));
			});
			
			$( ".feature-box-content").hide();
			$( ".feature-box .feature-box-title" ).click(function() {
				$( this ).toggleClass( "open" );
				$( this ).parent().toggleClass( "closed" );
				$( this ).next().slideToggle( "slow", function() {
				// Animation complete.
				});
			});
			
			
		});
		$.address.change(function(event) {  
			var tagDiv = event.value;
			tagDiv = tagDiv.substr(1, tagDiv.length);
			//alert(tagDiv);
			$('.' + tagDiv ).parent('li').addClass("active").siblings('li').removeClass("active");
			$("#" + tagDiv ).show().siblings('div').hide();
			$('html, body').animate({scrollTop:160}, 500);
		});		
	</script>
	<script src="http://listjs.com/no-cdn/list.js"></script>
	<script>
			var options = {
			  valueNames: [ 'htmltaglistitem' ]
			};

			var userList = new List('html-tags-list', options);

	</script>
	</body>
</html>