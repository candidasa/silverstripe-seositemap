<?php
class SitemapPage extends SiteTree {

	/**
	 * @return SiteTree All pages on the site
	 */
	protected function allPages() {
		return DataObject::get("SiteTree");
	}

	protected function countLinksOnPage($page) {
		if ($page->ClassName != $this->ClassName) {
			$pageHTML = Director::test(Director::absoluteURL($page->Link()));
			$numberOfLinks = substr_count($pageHTML->getBody(), '<a');
			return $numberOfLinks;
		}
	}

	function Sitetree() {
		$combinedOutput = "";
		$topLevelPages = DataObject::get("SiteTree","ParentID = 0");    //get all top-level pages
		foreach($topLevelPages as $page) {
			$output = "";
			$combinedOutput .= $this->getChildrenCountLinks($page, $output);
		}

		return $combinedOutput;
	}

	function getChildrenCountLinks($page, &$output) {
		$combinedOutput = '<ul><li>';
		$links = $this->countLinksOnPage($page);
		
		if ($links) {
			$combinedOutput .= ''."<a href='".$page->Link()."'>$page->Title (<strong>$links</strong> links)</a>";
		} else {
			$combinedOutput .= ''."<a href='".$page->Link()."'>$page->Title</a>";
		}

		$children = $page->AllChildren();
		if ($children && $children->Count() > 0) {
			foreach($children as $child) {
				$output = "";
				$combinedOutput .= $this->getChildrenCountLinks($child, $output);
			}
		}

		$combinedOutput .= '</li></ul>';
		return $combinedOutput;
	}
	
}

class SitemapPage_Controller extends ContentController {

}
?>