<?php
class LinkCountExtension extends DataObjectDecorator {

	static $wrote = false;

	function extraStatics() {
		return array(
			'db' => array(
				'LinkCount' => 'Int',
			),
		);
	}

	function onAfterWrite() {
		if (!self::$wrote) {
			self::$wrote = true;    //prevent infinite recursion
			
			$links = SEOSitemapPage::countLinksOnPage($this->owner);
			$this->owner->LinkCount = $links;
			$this->owner->writeWithoutVersion();
		}
	}
}
?>