<?php
/**
 * @copyright Braydon Justice for the University of Victoria, 2015
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package OpenSeadragon2
 */
 
/**
 * Annotation model.
 *
 * @package OpenSeadragon2
 */
class Annotation extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
	 /**
     * Annotation id.
     *
     * @var string
     */
    public $id;
    
     /**
     * Annotation image id.
     *
     * @var string
     */
    public $image_id;
    
     /**
     * position of x
     *
     * @var integer
     */
    public $x;
    
     /**
     * length of x
     *
     * @var integer
     */
    public $xlen;
    
     /**
     * position of y
     *
     * @var integer
     */
    public $y;
    
     /**
     * length of y
     *
     * @var integer
     */
    public $ylen;
    
    /**
     * Annotation title.
     *
     * @var string
     */
    public $title;

    /**
     * Annotation author.
     *
     * @var string
     */
    public $author;
    
    /**
     * Annotation description (in HTML).
     *
     * @var string
     */
    public $description;
    
    /**
     * Whether the annotation is public.
     *
     * @var integer
     */
    public $public = 0;
    
    /**
     * date created as a MySQL-formatted date string
     *
     * @var integer
     */
    public $date;

    /**
     * Publisher.
     *
     * @var integer
     */
    public $publisher;
    
    /**
     * Published place
     *
     * @var string
     */
    public $published_place;

    /**
     * Published date as a MySQL-formatted date string
     *
     * @var string
     */
    public $published_date;
    
    /**
     * people
     *
     * @var string
     */
    public $people;
    
    /**
     * Locations
     *
     * @var string
     */
    public $locations;
    
    /**
     * transcript
     *
     * @var string
     */
    public $transcript;
    
    /**
     * genre
     *
     * @var string
     */
    public $genre;

    /**
     * Date the exhibit was last modified, as a MySQL-formatted date string.
     *
     * @var string
     */
    public $modified;

    /**
     * User ID of the user who created the exhibit.
     *
     * @var integer
     */
    public $owner_id;

    /**
     *Converts vars to an array
     */     
    public function toArray()
    {
    	$ann = Array();
        $ann["id"] = $this->id;
		$ann["image_id"] = $this->image_id;
		$ann["x"] = $this->x;
		$ann["xlen"] = $this->xlen;
		$ann["y"] = $this->y;
		$ann["ylen"] = $this->ylen;
		$ann["title"] = $this->title;
		$ann["author"] = $this->author;
		$ann["description"] = $this->description;
		$ann["public"] = $this->public;
		$ann["date"] = $this->date;
		$ann["publisher"] = $this->publisher;
		$ann["published_place"] = $this->published_place;
		$ann["published_date"] = $this->published_date;
		$ann["people"] = $this->people;
		$ann["locations"] = $this->locations;
		$ann["transcript"] = $this->transcript;
		$ann["genre"] = $this->genre;
		$ann["modified"] = $this->modified;
		$ann["owner_id"] = $this->owner_id;
		
		return $ann;
    }
     
    /**
     * Set up mixins for shared behaviors.
     */
    public function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this);
        $this->_mixins[] = new Mixin_Timestamp($this);
        $this->_mixins[] = new Mixin_Search($this);
    }


    /**
     * Validation callback.
     */
    protected function _validate()
    {
        if (!strlen((string)$this->title)) {
            $this->addError('title', __('An exhibit must be given a title.'));
        }

        if (strlen((string)$this->title) > 255) {
            $this->addError('title', __('The title for an exhibit must be 255 characters or less.'));
        }

        if (strlen((string)$this->theme) > 30) {
            $this->addError('theme', __('The name of your theme must be 30 characters or less.'));
        }
    }

    /**
     * Delete callback.
     *
     * Delete all assigned pages when the exhibit is deleted.
     */
    protected function _delete()
    {
        //get all the pages and delete them
        $pages = $this->getTable('AnnotationPage')->findBy(array('exhibit'=>$this->id));
        foreach($pages as $page) {
            $page->delete();
        }
        $this->deleteTaggings();
    }

    /**
     * After-save callback.
     *
     * Updates search text and page data for the exhibit.
     *
     * @param array $args
     */
    protected function afterSave($args)
    {
        if (!$this->public) {
            $this->setSearchTextPrivate();
        }
        $this->setSearchTextTitle($this->title);
        $this->addSearchText($this->title);
        $this->addSearchText($this->description);
        
        if ($args['post']) {
            //Add the tags after the form has been saved
            $post = $args['post'];
            $this->applyTagString($post['tags']);
            if (isset($post['pages-hidden'])) {
                parse_str($post['pages-hidden'], $pageData);
                $this->_savePages($pageData['page']);
            }

            if (isset($post['pages-delete-hidden'])) {
                $pagesToDelete = explode(',', $post['pages-delete-hidden']);
                foreach ($pagesToDelete as $id) {
                    $page = $this->getTable('AnnotationPage')->find($id);
                    if ($page) {
                        $page->delete();
                    }
                }
            }
        }
    }

    public function getTopPageBySlug($slug)
    {

    }

    public function getFirstTopPage()
    {

    }

    /**
     * Determine whether an exhibit uses a particular item on any of its pages.
     *
     * @param Item $item
     * @return boolean
     */
    public function hasItem(Item $item)
    {
        if (!$item->exists()) {
           throw new InvalidArgumentException("Item does not exist (is not persisted).");
        }
        if (!$this->exists()) {
           throw new RuntimeException("Cannot call hasItem() on a new (non-persisted) exhibit.");
        }
        return $this->getTable()->exhibitHasItem($this->id, $item->id);
    }

    /**
     * Set options and optionally the theme name.
     *
     * @param array $themeOptions
     * @param string|null $themeName
     */
    public function setThemeOptions($themeOptions, $themeName = null)
    {
        if ($themeName === null) {
            $themeName = $this->theme;
        }
        if ($themeName !== null && $themeName != '') {
            $themeOptionsArray = unserialize($this->theme_options);
            $themeOptionsArray[$themeName] = $themeOptions;
        }

        $this->theme_options = serialize($themeOptionsArray);
    }

    /**
     * Get the options for the exhibit's theme.
     *
     * @param string|null $themeName If specified, get options for this theme
     *  instead of the exhibit's theme
     * @return array
     */
    public function getThemeOptions($themeName = null)
    {
        if ($themeName === null) {
            $themeName = $this->theme;
        }

        $themeName = (string)$themeName;
        if ($themeName == '' || empty($this->theme_options)) {
            return array();
        }

        $themeOptionsArray = unserialize($this->theme_options);
        return @$themeOptionsArray[$themeName];
    }

    /**
     * Required by Zend_Acl_Resource_Interface.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'AnnotationBuilder_Annotations';
    }
}
