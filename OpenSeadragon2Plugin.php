<?php
/**
 * OpenSeadragon2 
 * 
 * @copyright Copyright 2015 University of Victoria Library
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */


require_once dirname(__FILE__) . '/helpers/OpenSeadragonFunctions.php';
require_once dirname(__FILE__) . '/functions.php';

/**
 * The OpenSeadragon2 plugin.
 * 
 * @package Omeka\Plugins\OpenSeadragon2
 */
class OpenSeadragon2Plugin extends Omeka_Plugin_AbstractPlugin
{
    const DEFAULT_VIEWER_EMBED = 1;
    
    const DEFAULT_VIEWER_WIDTH = 500;
    
    const DEFAULT_VIEWER_HEIGHT = 600;

    const DEFAULT_CSS_OVERRIDE = 1;
    
    protected $_hooks = array(
        'install', 
        'uninstall', 
        'initialize',  
        'upgrade', 
        'config_form', 
        'config', 
        'admin_items_show', 
        'admin_files_show',
        'admin_files_form',
        'public_items_show', 
        'public_files_show', 
        'admin_head',
        'public_head',
        'define_routes'
    );
    //add_plugin_hook('append_to_item_form', 'hookAdminFilesEdit');
    protected $_options = array(
        'openseadragon2_embed_admin' => self::DEFAULT_VIEWER_EMBED, 
        'openseadragon2_width_admin' => self::DEFAULT_VIEWER_WIDTH, 
        'openseadragon2_height_admin' => self::DEFAULT_VIEWER_HEIGHT, 
        'openseadragon2_embed_public' => self::DEFAULT_VIEWER_EMBED, 
        'openseadragon2_css_override_public' => self::DEFAULT_CSS_OVERRIDE,
        'openseadragon2_width_public' => self::DEFAULT_VIEWER_WIDTH, 
        'openseadragon2_height_public' => self::DEFAULT_VIEWER_HEIGHT, 
    );
    
    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
    	annotations_install();
        $this->_installOptions();
    }
   
    /**
     * Unnstall the plugin.
     */
    public function hookUninstall()
    {
    	annotations_uninstall();
        $this->_uninstallOptions();
    }
    
    
    /**
     * Initialize the plugin.
     */
    public function hookInitialize()
    {
        // Add translation.
        add_translation_source(dirname(__FILE__) . '/languages');
    } 
    
     /**
     * Upgrade the plugin.
     */
    public function hookUpgrade($args)
    {
        // Version 2.0 introduced image viewer embed flags.
        if (version_compare($args['old_version'], '2.0', '<')) {
            set_option('openseadragon2_embed_admin', self::DEFAULT_VIEWER_EMBED);
            set_option('openseadragon2_embed_public', self::DEFAULT_VIEWER_EMBED);
        }
    }
    
    /**
     * Display the config form.
     */
    public function hookConfigForm()
    {
        echo get_view()->partial('plugins/openseadragon-config-form.php');
    }
    
    /**
     * Handle the config form.
     */
    public function hookConfig()
    {
        if (!is_numeric($_POST['openseadragon2_width_admin']) || 
            !is_numeric($_POST['openseadragon2_height_admin']) || 
            !is_numeric($_POST['openseadragon2_width_public']) || 
            !is_numeric($_POST['openseadragon2_height_public'])) {
            throw new Omeka_Validate_Exception('The width and height must be numeric.');
        }
        set_option('openseadragon2_embed_admin', (int) (boolean) $_POST['openseadragon2_embed_admin']);
        set_option('openseadragon2_width_admin', $_POST['openseadragon2_width_admin']);
        set_option('openseadragon2_height_admin', $_POST['openseadragon2_height_admin']);
        set_option('openseadragon2_embed_public', (int) (boolean) $_POST['openseadragon2_embed_public']);
        set_option('openseadragon2_css_override_public', (int) (boolean) $_POST['openseadragon2_css_override_public']);
        set_option('openseadragon2_width_public', $_POST['openseadragon2_width_public']);
        set_option('openseadragon2_height_public', $_POST['openseadragon2_height_public']);
    }
    
    /**
     * Display the image viewer in admin items/show.
     */
    public function hookAdminItemsShow($args)
    {
        // Embed viewer only if configured to do so.
        if (!get_option('openseadragon2_embed_admin')) {
            return;
        }
        echo $args['view']->openseadragon($args['item']->Files);
        echo $args['view']->partial('annotations/annoViewer.php', array('file' => $args['item']->Files));
    }
    
    public function hookAdminFilesShow($args)
    {
        // Embed viewer only if configured to do so.
        if (!get_option('openseadragon2_embed_admin')) {
            return;
        }
        echo $args['view']->partial('annotations/annoViewer.php', array('file' => $args['file']));
    }
    
    public function hookAdminFilesForm($args)
    {
        // Embed viewer only if configured to do so.
        if (!get_option('openseadragon2_embed_admin')) {
            return;
        }
        echo $args['view']->partial('annotations/annoViewer.php', array('file' => $args['file']));
    }
    
    /**
     * Display the image viewer in public items/show.
     */
    public function hookPublicItemsShow($args)
    {
    	
        // Embed viewer only if configured to do so.
        if (!get_option('openseadragon2_embed_public')) {
            return;
        }
        echo $args['view']->openseadragon($args['item']->Files);
        //$arr = $args['item']->Files;
        //echo $args['view']->partial('annotations/add.php', array('file' => $arr[0]));
    }
    public function hookPublicFilesShow($args)
    {
    	
        // Embed viewer only if configured to do so.
        if (!get_option('openseadragon2_embed_public')) {
            return;
        }
        echo $args['view']->openseadragon($args['item']->Files);
        //echo $args['view']->partial('annotations/add.php', array('file' => $args['file']));
    }

    private function _osd_css($width, $height)
    {
        return ".openseadragonframe { width: ".$width."px; height: ".$height."px};";
    }

    public function hookAdminHead($args)
    {
    	queue_js_file('bigscreen.min', 'openseadragon');
        queue_css_string($this->_osd_css(get_option('openseadragon2_width_admin'), get_option('openseadragon2_height_admin')));
    }

    public function hookPublicHead($args)
    {
    	queue_js_file('bigscreen.min', 'openseadragon');
        if (!get_option('openseadragon2_css_override_public')) {
            queue_css_string($this->_osd_css(get_option('openseadragon2_width_public'), get_option('openseadragon2_height_public')));
        }
    }
    
    function hookDefineRoutes($args)
    {
    	$router = $args['router'];
    	$router->addConfig(new Zend_Config_Ini(dirname(__FILE__) . '/routes.ini', 'routes'));
    	/*$router->addRoute(
			'annotations',
			new Zend_Controller_Router_Route(
				'annotations/:action',
				array(
					'module'       => 'annotations',
					'controller'   => 'open-seadragon2',
					'action'       => 'add'
				)
			)
		);*/
    }
}
