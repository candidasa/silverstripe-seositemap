<?php
Object::add_extension("SiteTree","LinkCountExtension");

if (class_exists('SphinxSearchable')) Object::add_extension('SEOSitemapPage', 'SphinxSearchable');
?>