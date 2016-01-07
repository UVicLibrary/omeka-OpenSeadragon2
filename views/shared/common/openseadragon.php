<?php 
$button_path = src('images/', 'openseadragon');
$frame_path = "http://" . $_SERVER['SERVER_NAME'] . "/openseadragonframe.php";
$query = http_build_query(array('images' => $imageNames));
?>


<iframe class="openseadragonframe" allowfullscreen webkitallowfullscreen mozallowfullscreen style="overflow: hidden; border: none;" scrolling="no" src="<?=$frame_path.'?'.$query?>"></iframe>
    <?php
    
    /*
       
    foreach($images as $image):
        $image_url = html_escape($image->getWebPath('original'));
        $unique_id = "openseadragon_".hash("md4", $image_url);
        $imagedimensions = openseadragon_dimensions($image, 'original');
       ?>
       
    <div class="openseadragon_viewer" id="<?=$unique_id?>">
        <img src="<?=$image_url?>" class="openseadragon-image tmp-img" alt="">
    </div>

    <script type="text/javascript">
        var viewer = OpenSeadragon({
        	id: "<?=$unique_id?>",
            prefixUrl: "<?=$button_path?>",
			preserveViewport:   true,
			visibilityRatio:    0.75,
			minZoomLevel:       0.5,
			defaultZoomLevel:   0.5,
            tileSources:   [{
				"@context": "http://iiif.io/api/image/2/context.json",
				"@id": "http:/kastra.library.uvic.ca:3000/digilib/Scaler/IIIF/<?=explode(".",$image->filename)[0]?>",
				"height": <?=$imagedimensions['height']?>,
				"width": <?=$imagedimensions['width']?>,
				"profile": [ "http://iiif.io/api/image/2/level2.json" ],
				"protocol": "http://iiif.io/api/image",
				"tiles": [{
				"scaleFactors": [ 1, 2, 4, 8, 16 ],
				"width": 256
				}]
			}]
		});
        var ts = "hello";//new OpenSeadragon.LegacyTileSource(<?php echo openseadragon_create_pyramid($image); ?>);
            
        //viewer.openTileSource(ts);
    </script>
    <?php endforeach; 
    */
    ?>
