<?php
$ann = new Annotation();
$imgScale = 4;
$ann->image_id = "xxx";
$ann->id = 1;
$imgLink = "http://".$_SERVER['SERVER_NAME'].str_replace("admin/index.php", "", $_SERVER['PHP_SELF'])."files/fullsize/".explode(".",$file->filename)[0].".jpg";
//$sql = update_annotation($ann);
//echo $sql;
//echo explode(".",$file->filename)[0];
//echo file_markup($file, array('imageSize'=>'fullsize', 'linkToFile'=>false));
echo "$imgLink<br>";
print_r(json_decode($file['metadata'], true)['video']['resolution_y']);
$height=(json_decode($file['metadata'], true)['video']['resolution_y'])/4;
$width=(json_decode($file['metadata'], true)['video']['resolution_x'])/4;

?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.js"></script>
<style>
	.anno-layers {
		position: absolute;
		top: 0;
		left: 0;
}
	.anno-div {
		position: relative;
		top: 0;
		left: 0;
	}
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
<a style="margin: 4px; padding: 4px; background: lightgreen; cursor: pointer" onclick="bindtooltip();"> <b>show annos</b> </a>
<a style="margin: 4px; padding: 4px; background: lightgreen; cursor: pointer" onclick="drawAnno();"> <b>draw anno</b> </a>
<div class="anno-div" id="annotation-container">
	<div id="example-tip" style='display:none;width:250px;background-color:#df6df5;z-index:3'>
            <p>
                The overlay can provide a class name and ID to bind additional events to.
            </p>
	</div>
    <div id="master" style="width:<?=$width?>px;float:left;">
		<img width="<?=$width?>px" class="anno-layers" src="<?=$imgLink?>" />
		<svg width="<?=$width?>px" height="<?=$height?>" class="anno-layers anno-div" id="svgLayer"></svg>
		<canvas id="canvas" width="<?=$width?>" height="<?=$height?>" style="display:none;border: 1px solid black; cursor: pointer;background-size:cover; background-image: url(<?=$imgLink?>)"></canvas>
		<div style="height:<?=$height+100?>px;" class="anno-layers"></div>
	</div>
	<script type="text/javascript">
	var offset = $("#canvas").offset();
	var svgLayer = $('#svgLayer');
			function bindtooltip(){
				$.each($('.anno-layers'), function( index, value ) {
				  value.style.display = "block";
				});
				$('#canvas').css("display", "none")
				svgLayer.empty();
            	var elt = document.createElement("rect");
				elt.setAttribute("id", "runtime-overlay");
				//elt.className = "highlight";<img width="<?=$width?>px" class="anno-layers" src="<?=$imgLink?>" />
				elt.setAttribute("width", "50px");
				elt.setAttribute("height", "70px");
				elt.setAttribute("x", "400");
				elt.setAttribute("y", "500");
				elt.setAttribute("style", "fill:blue;stroke:black;stroke-width:1;fill-opacity:0;stroke-opacity:0.9");
				
				svgLayer.append(elt);
				
				svgLayer.html(function(){return this.innerHTML});
                var tip = jQuery('#example-tip');
                jQuery("#runtime-overlay").click(function(e){
                	jQuery("#runtime-overlay").css("fill-opacity","0.2");
                    var mousex = (e.pageX - offset.left) + 100, //Get X coodrinates
                        mousey = (e.pageY - offset.top), //Get Y coordinates
                        tipWidth = tip.width(), //Find width of tooltip
                        tipHeight = tip.height(), //Find height of tooltip
                    
                    //Distance of element from the right edge of viewport
                        tipVisX = $(window).width() - (mousex + tipWidth),
                    //Distance of element from the bottom of viewport
                        tipVisY = $(window).height() - (mousey + tipHeight);
                      
                    if ( tipVisX < 20 ) { //If tooltip exceeds the X coordinate of viewport
                        mousex = (e.pageX - offset.left) - tipWidth - 100;
                    } if ( tipVisY < 20 ) { //If tooltip exceeds the Y coordinate of viewport
                        //mousey = (e.pageY - offset.top) - tipHeight - 100;
                    } 
                    tip.css({  top: mousey, left: mousex, position: 'absolute' });
                    tip.show().css({opacity: 0.8}); //Show tooltip
                }/*, function() {
                    tip.hide(); //Hide tooltip
                	jQuery("#runtime-overlay").css("fill-opacity","0");
                }*/);
            };
            var canvas, context, startX, endX, startY, endY;
            var mouseIsDown = 0;
            function drawAnno() {
            	$.each($('.anno-layers'), function( index, value ) {
				  value.style.display = "none";
				});
				$('#canvas').css("display", "block")
				
				canvas = document.getElementById("canvas");
				context = canvas.getContext("2d");
			
				canvas.addEventListener("mousedown", mouseDown, false);
				canvas.addEventListener("mousemove", mouseXY, false);
				canvas.addEventListener("mouseup", mouseUp, false);
				
				
			}
            function mouseUp(eve) {
				if (mouseIsDown !== 0) {
					mouseIsDown = 0;
					var pos = getMousePos(canvas, eve);
					endX = pos.x;
					endY = pos.y;
					drawSquare(); //update on mouse-up
				}
			}
			
			function mouseDown(eve) {
				mouseIsDown = 1;
				var pos = getMousePos(canvas, eve);
				startX = endX = pos.x;
				startY = endY = pos.y;
				drawSquare(); //update
			}
			
			function mouseXY(eve) {
			
				if (mouseIsDown !== 0) {
					var pos = getMousePos(canvas, eve);
					endX = pos.x;
					endY = pos.y;
			
					drawSquare();
				}
			}
			
			function drawSquare() {
				// creating a square
				var w = endX - startX;
				var h = endY - startY;
				var offsetX = (w < 0) ? w : 0;
				var offsetY = (h < 0) ? h : 0;
				var width = Math.abs(w);
				var height = Math.abs(h);
			
				context.clearRect(0, 0, canvas.width, canvas.height);
						   
				context.beginPath();
				context.rect(startX + offsetX, startY + offsetY, width, height);
				context.fillStyle = "transparent";
				context.fill();
				context.lineWidth = 1;
				context.strokeStyle = 'black';
				context.stroke();
				showNewForm();
			}
			
			function getMousePos(canvas, evt) {
				var rect = canvas.getBoundingClientRect();
				return {
					x: evt.clientX - rect.left,
					y: evt.clientY - rect.top
				};
			}
             
			function showNewForm() {
				if($('#new').css("display")=="none") {
					$.each($('#new').find("textarea"), function (index, value) {
							value.value = "";//clear all textareas in the form
					});
					$('#new').css("display", "block");
				}
			}
			
			function addAnno() {
				$.post("/addAnno.php",$('#new').serialize(), function(response) {
					alert(response);
					window.location = document.referrer;
				});
			}
             
            window.addScrollListener = setInterval(function() {
				if(parseInt($( "#save" ).css('margin-top'))>$('#file-metadata').height()){
					$( "#save" ).css('margin-top', $('#file-metadata').height().toString()+"px");
				}
			}, 100);
			$(document).ready(function () {
				$('#annotation-container').css("width", $('#content').width().toString()+"px");
			});
			 
    </script>
    </form>
    <div style="float:left;width:220px;margin-left:5px; background-color:lightgrey;">
    <form id="new" name="newform" method="post" target="">
    title:
    <br>
    <textarea id="title" name="title"></textarea>
    <br>
    author:
    <br>
    <textarea id="author" name="author"></textarea>
    <br>
    description:
    <br>
    <textarea id="description" name="description"></textarea>
    <br>
    date:
    <br>
    <textarea id="date" name="date"></textarea>
    <br>
    publisher:
    <br>
    <textarea id="publisher" name="publisher"></textarea>
    <br>
    publisher_place:
    <br>
    <textarea id="publisher_place" name="publisher_place"></textarea>
    <br>
    publisher_date:
    <br>
    <textarea id="publisher_date" name="publisher_date"></textarea>
    <br>
    people:
    <br>
    <textarea id="people" name="people"></textarea>
    <br>
    locations:
    <br>
    <textarea id="locations" name="locations"></textarea>
    <br>
    transcript:
    <br>
    <textarea id="transcript" name="transcript"></textarea>
    <br>
    genre:
    <br>
    <textarea id="genre" name="genre"></textarea>
    <br>
    public:
    <br>
    Yes<input id="public" name="public" type="radio" value="1">    
    No<input id="public" name="public" type="radio" value="0" checked>
    <br>
    <br>
    <a style="margin: 4px; padding: 4px; background: lightgreen; cursor: pointer" onclick="addAnno();"> <b>Submit Annotation</b> </a>
    <br>
    <br>
    </form>
    
    <script type="text/javascript">
    /*	<form id="edit">
    </form>document.getElementById('edit').innerHTML = '
    <input id="id" name="id" type="hidden" value="">
    title:
    <br>
    <textarea id="title" name="title"></textarea>
    <br>
    author:
    <br>
    <textarea id="author" name="author"></textarea>
    <br>
    description:
    <br>
    <textarea id="description" name="description"></textarea>
    <br>
    date:
    <br>
    <textarea id="date" name="date"></textarea>
    <br>
    publisher:
    <br>
    <textarea id="publisher" name="publisher"></textarea>
    <br>
    publisher_place:
    <br>
    <textarea id="publisher_place" name="publisher_place"></textarea>
    <br>
    publisher_date:
    <br>
    <textarea id="publisher_date" name="publisher_date"></textarea>
    <br>
    people:
    <br>
    <textarea id="people" name="people"></textarea>
    <br>
    locations:
    <br>
    <textarea id="locations" name="locations"></textarea>
    <br>
    transcript:
    <br>
    <textarea id="transcript" name="transcript"></textarea>
    <br>
    genre:
    <br>
    <textarea id="genre" name="genre"></textarea>
    <br>
    public:
    <br>
    Yes<input id="public" name="public" type="radio" value="1">    
    No<input id="public" name="public" type="radio" value="0" checked>';*/
    </script>
    <div>
</div>
<div id="output">

</div>