<?php
$ann = new Annotation();
$imgScale = 4;
$ann->image_id = "xxx";
$ann->id = 1;
$imageId = explode(".",$file->filename)[0];
$imgLink = "http://".$_SERVER['SERVER_NAME'].str_replace("admin/index.php", "", $_SERVER['PHP_SELF'])."files/fullsize/".explode(".",$file->filename)[0].".jpg";
//$sql = update_annotation($ann);
//echo $sql;
//echo explode(".",$file->filename)[0];
//echo file_markup($file, array('imageSize'=>'fullsize', 'linkToFile'=>false));
//echo "$imgLink<br>";
//print_r(json_decode($file['metadata'], true)['video']['resolution_y']);
$image_info = getimagesize($imgLink);
$width=550;
$ratio = $image_info[0]/$width;
$height=$image_info[1]/$ratio;


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
</form>
<a id="show_annos" style="margin: 4px; padding: 4px; background: lightgreen; cursor: pointer" onclick="showAnnos();"> <b>show annos</b> </a>
<a id="draw_anno" style="margin: 4px; padding: 4px; background: lightgreen; cursor: pointer" onclick="drawAnno();"> <b>draw anno</b> </a>
<div class="anno-div" id="annotation-container">
    <div id="master" style="width:<?=$width?>px;float:left;">
		<img width="<?=$width?>px" class="anno-layers" src="<?=$imgLink?>" />
		<svg width="<?=$width?>px" height="<?=$height?>" class="anno-layers anno-div" id="svgLayer"></svg>
		<canvas id="canvas" width="<?=$width?>" height="<?=$height?>" style="display:none;border: 1px solid black; cursor: pointer;background-size:cover; background-image: url(<?=$imgLink?>)"></canvas>
		<div style="height:<?=$height+100?>px;" class="anno-layers"></div>
	</div>
	<form id="get" method="post">
	<input type="hidden" value="<?= $imageId ?>" id="image_id" name="image_id">
	</form>
	<script type="text/javascript">
	var offset = $("#canvas").offset();
	var svgLayer = $('#svgLayer');
	var annos = "";
	function getAnnos() {
		$.post("/omeka/admin/annotations/get",$('#get').serialize(), function(response) {
					annos = $.parseJSON(response);
				});
	}
	getAnnos();// must run first so it has time to do query.
	
			function showAnnos(){
				getAnnos();
				$.each($('.anno-layers'), function( index, value ) {
				  value.style.display = "block";
				});
				$('#canvas').css("display", "none")
				svgLayer.empty();
				$.each(annos, function (index, value) {
						var elt = document.createElement("rect");
						elt.setAttribute("id", "Annotation-"+value['id']);
						elt.setAttribute("class", "Annotation");
						elt.setAttribute("width", (value['xlen']*<?= $width ?>)+"px");
						elt.setAttribute("height", (value['ylen']*<?= $height ?>)+"px");
						elt.setAttribute("x", (value['x']*<?= $width ?>));
						elt.setAttribute("y", (value['y']*<?= $height ?>));
						elt.setAttribute("style", "fill:blue;stroke:black;stroke-width:1;fill-opacity:0;stroke-opacity:0.9");
						svgLayer.append(elt);
				});

				svgLayer.html(function(){return this.innerHTML});
				
				$.each(annos, function (index, value) {
						var this_id = value['id'];
						$("#Annotation-"+this_id).click(function(e){
								$.each($(".Annotation"), function (ind, ann) {
										ann.style["fill-opacity"] = 0;
								});
								$("#Annotation-"+this_id).css("fill-opacity","0.2");
								//fill form
								document.getElementById('edit').innerHTML = '' +
    '<input type="hidden" value="<?=$imageId?>" id="image_id" name="image_id">'+
    '<input id="id" name="id" type="hidden" value="'+this_id+'">'+
    'title:'+
    '<br>'+
    '<textarea id="title" name="title" style="width: 220px">'+value["title"]+'</textarea>'+
    '<br>'+
    'author:'+
    '<br>'+
    '<textarea id="author" name="author">'+value["author"]+'</textarea>'+
    '<br>'+
    'description:'+
    '<br>'+
    '<textarea id="description" name="description">'+value["description"]+'</textarea>'+
    '<br>'+
    'date:'+
    '<br>'+
    '<textarea id="date" name="date">'+value["date"]+'</textarea>'+
    '<br>'+
    'publisher:'+
    '<br>'+
    '<textarea id="publisher" name="publisher">'+value["publisher"]+'</textarea>'+
    '<br>'+
    'publisher_place:'+
    '<br>'+
    '<textarea id="publisher_place" name="publisher_place">'+value["publisher_place"]+'</textarea>'+
    '<br>'+
    'publisher_date:'+
    '<br>'+
    '<textarea id="publisher_date" name="publisher_date">'+value["publisher_date"]+'</textarea>'+
    '<br>'+
    'people:'+
    '<br>'+
    '<textarea id="people" name="people">'+value["people"]+'</textarea>'+
    '<br>'+
    'locations:'+
    '<br>'+
    '<textarea id="locations" name="locations">'+value["locations"]+'</textarea>'+
    '<br>'+
    'transcript:'+
    '<br>'+
    '<textarea id="transcript" name="transcript">'+value["transcript"]+'</textarea>'+
    '<br>'+
    'genre:'+
    '<br>'+
    '<textarea id="genre" name="genre">'+value["genre"]+'</textarea>'+
    '<br>'+
    'public:'+
    '<br>'+
    'Yes<input id="public" name="public" type="radio" value="1" '+(value["public"]==1 ? "checked" : "")+'>' +   
    'No<input id="public" name="public" type="radio" value="0" '+(value["public"]==0 ? "checked" : "")+'>'+
    '<br>'+
    '<br>'+
    '<a style="margin: 4px; padding: 4px; background: lightgreen; cursor: pointer" onclick="editAnno();"> <b>Submit Annotation</b> </a>'+
    '<br>'+
    '<br>';
    $('#new').css("display", "none");
    $('#edit').css("display", "block");
						});
				});
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
				
				$('#new').css("display", "block");
				$('#edit').css("display", "none");
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
				var x = (endX<startX ? endX/<?= $width ?> : startX/<?= $width ?>);
				var y = (endY<startY ? endY/<?= $height ?> : startY/<?= $height ?>);
				var xlen = (w<0 ? (-w/<?= $width ?>) : (w/<?= $width ?>));
				var ylen = (h<0 ? (-h/<?= $height ?>) : (h/<?= $height ?>));
				$('#x').val(x);
				$('#y').val(y);
				$('#xlen').val(xlen);
				$('#ylen').val(ylen);
				
			}
			
			function getMousePos(canvas, evt) {
				var rect = canvas.getBoundingClientRect();
				return {
					x: evt.clientX - rect.left,
					y: evt.clientY - rect.top
				};
			}
            
			function clear(form) {
				$.each($('#'+form).find("textarea"), function (index, value) {
							value.value = "";//clear all textareas in the form
					});
			}
			
			function showNewForm() {
				if($('#new').css("display")=="none") {
					clear('new');
					$('#new').css("display", "block");
				}
			}
			
			function addAnno() {
				$.post("/omeka/admin/annotations/add",$('#new').serialize(), function(response) {
					alert(response);
					getAnnos();
					clear('new');
					context.clearRect(0, 0, canvas.width, canvas.height);
				});
			}
			
			function editAnno() {
				$.post("/omeka/admin/annotations/edit",$('#edit').serialize(), function(response) {
					getAnnos();
					setTimeout(function(){ 
						$('#show_annos').click(); 
						
					}, 4000);
					alert(response, showNewForm);
					showNewForm();
					$('#edit').css("display", "none");
				});
			}
             
            window.addScrollListener = setInterval(function() {
				if(parseInt($( "#save" ).css('margin-top'))>$('#file-metadata').height()+100){
					$( "#save" ).css('margin-top', ($('#file-metadata').height()+100).toString()+"px");
				}
			}, 100);
			$(document).ready(function () {
				$('#annotation-container').css("width", $('#content').width().toString()+"px");
				setTimeout(function(){ $('#show_annos').click(); }, 4000);
			});
			 
    </script>
    
    <div style="float:left;width:220px;margin-left:5px; background-color:lightgrey;">
		<form id="new" name="newform" method="post" target="">
		<input type="hidden" value="0" id="x" name="x">
		<input type="hidden" value="0" id="y" name="y">
		<input type="hidden" value="0" id="xlen" name="xlen">
		<input type="hidden" value="0" id="ylen" name="ylen">
		<input type="hidden" value="<?= $imageId ?>" id="image_id" name="image_id">
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
		<form id="edit" name="edit" method="post" style="display: none;">
		</form>
    <div>
</div>
<div id="output">

</div>