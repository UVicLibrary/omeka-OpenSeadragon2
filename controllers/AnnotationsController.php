<?php

class OpenSeadragon2_AnnotationsController extends Omeka_Controller_AbstractActionController
{
	public function init()
    {
        $this->_helper->db->setDefaultModelName('Annotations');
    }
    
	public function addAction() {
		
    }
}

?>