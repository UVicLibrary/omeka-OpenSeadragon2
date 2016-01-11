<?php

class OpenSeadragon2_AnnotationsController extends Omeka_Controller_AbstractActionController
{
	public function init()
    {
        //$this->_helper->db->setDefaultModelName('Annotations');
    }
    
	public function addAction() {
		$ann = new Annotation();
		foreach($_POST as $key => $value) {
			$ann->$key = $value;	
		}
		$result = insert_annotation($ann);
		if($result["success"]==TRUE) echo "Annotation inserted successfully";
		else echo "Failed to insert annotation \n QUERY:{$result['sql']}";
    }
    
    public function getAction() {
    	$image_id = $_POST["image_id"];
    	$anns = get_annotations($image_id);
    	$arrayAnns = [];
    	foreach($anns as $ann) {
    		$arrayAnns[] = $ann->toArray();
    	}
    	echo json_encode($arrayAnns);
    }
    
    public function editAction() {
		$ann = new Annotation();
		foreach($_POST as $key => $value) {
			$ann->$key = $value;
		}
		$result = update_annotation($ann);
		if($result["success"]==TRUE) echo "Annotation inserted successfully";
		else echo "Failed to insert annotation \n QUERY:{$result['sql']}";
		//echo $result;
    }
    
    public function openseadragonframeAction() {
    	$params = $this->getRequest()->getParams();
    	
    	$_POST['names'] = $params['names'];
    	print_r($_POST);
    }
}

?>