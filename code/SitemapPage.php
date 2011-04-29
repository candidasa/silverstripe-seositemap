<?php
class SitemapPage extends SiteTree {

	static $allPagesCache = array();

	/**
	 * @return SiteTree All pages on the site
	 */
	protected function allPages() {
		return DataObject::get("SiteTree");
	}

	public static function countLinksOnPage($page) {
		if ($page->ClassName != "SitemapPage") { //don't count the sitemap page, otherwise we get unlimited recursion
			$pageHTML = Director::test(Director::absoluteURL($page->Link()));
			$numberOfLinks = substr_count($pageHTML->getBody(), '<a');
			return $numberOfLinks;
		}
	}

	protected function countAllLinks() {
		self::$allPagesCache = DB::query("SELECT ID, LinkCount FROM SiteTree_Live")->map();

		foreach(self::$allPagesCache as $id => $links) {
			if ($links == null || $links == 0) {
				$page = DataObject::get_by_id("SiteTree",$id);
				if ($page && $page->ClassName != "SitemapPage") {
					$page->LinkCount = self::countLinksOnPage($page);
					$page->write();
				}
				else self::$allPagesCache[$id] = -1;
			}
		}

		return self::$allPagesCache;

		/*$allPages = DataObject::get("SiteTree");
		foreach($allPages as $page) {
			$page->LinkCount = self::countLinksOnPage($page);
			self::$allPagesCache[$page->ID] = $page;
		}*/
	}

	function Sitetree() {
		$this->countAllLinks();

		$combinedOutput = "";

		//tree
		if (!$this->isList()) {

			$topLevelPages = DataObject::get("SiteTree","ParentID = 0");    //get all top-level pages
			foreach($topLevelPages as $page) {
				$output = "";
				$combinedOutput .= $this->getChildrenCountLinks($page, $output);
			}
		} else {    //list
			$allPages = DataObject::get("SiteTree");
			$allPages->sort('LinkCount');
			
			$combinedOutput .= $this->getAllCountLinks($allPages,$combinedOutput);
		}

		return $combinedOutput;
	}

	function getAllCountLinks($allPages, $combinedOutput) {
		foreach($allPages as $page) {
			$combinedOutput .= '<ul><li>';
			$links = $page->LinkCount;

			if ($links && $links > 0) {
				$combinedOutput .= ''."<a href='".$page->Link()."'>$page->Title (<strong>$links</strong> links)</a>";
			} else {
				$combinedOutput .= ''."<a href='".$page->Link()."'>$page->Title</a>";
			}
			$combinedOutput .= '</li></ul>';
		}
		return $combinedOutput;
	}

	function countSubpageLinks($page) {
		$ids = $page->getDescendantIDList();
		$counting = array();
		foreach($ids as $id) {
			$counting[] = self::$allPagesCache[$id];
		}

		if (count(array_filter($counting)) > 0) return $this->array_average_nonzero(array_filter($counting));
	}

	function array_average_nonzero($arr) {
        return array_sum($arr) / count(array_filter($arr));
	}


	function getChildrenCountLinks($page, &$output) {
		$combinedOutput = '<ul><li>';
		$links = self::$allPagesCache[$page->ID];

		if ($links && $links > 0) {
			//$subpagelinkCount = $this->countSubpageLinks($page);
			//$subpagetext = " - subpages: ; avg. subpage links: $subpagelinkCount;";
			$subpagetext = "";

			$combinedOutput .= ''."<a href='".$page->Link()."'>$page->Title (<strong>$links</strong> links$subpagetext)</a>";
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

	function isList() {
		return (!empty($_GET['list']));
	}
	
}

class SitemapPage_Controller extends ContentController {

	static $allows_actions = array('recountAll');

	function recountAll() {
		$allPages = DataObject::get("SiteTree");
		foreach($allPages as $page) {
			$page->LinkCount = null;
			$page->write();
		}
	}

}
?>