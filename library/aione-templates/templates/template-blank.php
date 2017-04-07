<?php
header('Content-Type: application/json;charset=utf-8'); 
global $post;
echo do_shortcode($post->post_content);