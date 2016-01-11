<?php 
$images = $_GET['images'];

//print_r($images);//<link rel='stylesheet' type='text/css' media='screen' href='openseadragon.css'/>

$Annotations = [];
foreach($images as $image_id => $dimensions) {
	$anns = get_annotations($image_id);
	$arrayAnns = [];
	foreach($anns as $ann) {
		$arrayAnns[] = $ann->toArray();
	}
	$Annotations[] = $arrayAnns;
}

?>
<html>
<head></head>
<body>
<script src="openseadragon.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.js"></script>

<style>
        .navigator .highlight{
            opacity:    0.4;
            filter:     alpha(opacity=40);
            border:     2px solid #900;
            outline:    none;
            background-color: #900;
        }
        .highlight{
            filter:     alpha(opacity=70);
            opacity:    0.7;
            border:     2px solid #0A7EbE;
            outline:    10px auto #0A7EbE;
            background-color: transparent;
        }
        .highlight:hover, .highlight:focus{
            filter:     alpha(opacity=40);
            opacity:    0.4;
            background-color: white;
        }
        </style>

	<div class="demoarea">
    <div id="example-inline-configuration-for-iiif" class="openseadragon" style="width:100%;">
         <div id="example-tip" style='display:none;width:250px;background-color:#df6df5;z-index:3'>
            <p>
                The overlay can provide a class name and ID to bind additional events to.
            </p>
        </div>
        </div>
    </div>
</div>

<script type="text/javascript">
			
                // ----------
                function setupViewer() {

                    this.viewer = OpenSeadragon({
                        id:                 "example-inline-configuration-for-iiif",
						prefixUrl:          "images/",
						preserveViewport:   true,
						visibilityRatio:    0.75,
						minZoomLevel:       0.5,
						defaultZoomLevel:   0.5,
						sequenceMode: true,    
						showReferenceStrip: true,
                        tileSources:   [
                        <?php $firstrun = true;
                        foreach ($images as $key => $values):?>
                        <?=$firstrun ? "" : ","?>
                        {
						  "@context": "http://iiif.io/api/image/2/context.json",
						  "@id": "http://kastra.library.uvic.ca:3000/digilib/Scaler/IIIF/<?=$key?>",
						  "height": <?=$values['height']?>,
						  "width": <?=$values['width']?>,
						  "profile": [ "http://iiif.io/api/image/2/level2.json" ],
						  "protocol": "http://iiif.io/api/image",
						  "tiles": [{
							"scaleFactors": [ 1, 2, 4, 8, 16 ],
							"width": 256
						  }]
						}
						<?php $firstrun = false;
						endforeach; ?>
						]
                    });
                    this.viewer.addHandler('page', function() {
							//Tooltips
							setTimeout(bindtooltip, 2000);
					});
                }
           

            // ----------
            $(document).ready(function() {
                setupViewer();
            });
  
    		jQuery(function() {
                //Tooltips
                setTimeout(bindtooltip, 2000);
            });
            
            function bindtooltip(){
            	var elt = document.createElement("div");
				elt.id = "runtime-overlay";
				elt.className = "highlight";
				viewer.addOverlay({
					element: elt,
					location: new OpenSeadragon.Rect(0.33999, 0.75999, 0.2999, 0.25999)
				});
                var tip = jQuery('#example-tip');
                jQuery("#runtime-overlay").hover(function(e){
                	jQuery("#runtime-overlay").css("border","2px solid red");
                    var mousex = e.pageX + 20, //Get X coodrinates
                        mousey = e.pageY + 20, //Get Y coordinates
                        tipWidth = tip.width(), //Find width of tooltip
                        tipHeight = tip.height(), //Find height of tooltip
                    
                    //Distance of element from the right edge of viewport
                        tipVisX = $(window).width() - (mousex + tipWidth),
                    //Distance of element from the bottom of viewport
                        tipVisY = $(window).height() - (mousey + tipHeight);
                      
                    if ( tipVisX < 20 ) { //If tooltip exceeds the X coordinate of viewport
                        mousex = e.pageX - tipWidth - 20;
                    } if ( tipVisY < 20 ) { //If tooltip exceeds the Y coordinate of viewport
                        mousey = e.pageY - tipHeight - 20;
                    } 
                    tip.css({  top: mousey, left: mousex, position: 'absolute' });
                    tip.show().css({opacity: 0.8}); //Show tooltip
                }, function() {
                    tip.hide(); //Hide tooltip
                    jQuery("#runtime-overlay").css("border","");
                });
            };
</script>
</body>
</html>
