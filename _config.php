<?php
Object::add_extension("SiteTree","LinkCountExtension");

if (class_exists('SphinxSearchable')) Object::add_extension('SEOSitemapPage', 'SphinxSearchable');

ini_set('max_execution_time', 14400);  //4 hours max exec time to give the module time to calculate links for all pages
?>