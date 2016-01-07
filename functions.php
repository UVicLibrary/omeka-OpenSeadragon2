<?php
/**
 * @copyright Braydon Justice for the University of Victoria
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package OpenSeadragon2
 */

 /**
 * Install the plugin, creating the tables in the database.
 */
function annotations_install()
{
    $db = get_db();

    $db->query(<<<SQL
CREATE TABLE IF NOT EXISTS `{$db->prefix}annotations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `image_id` TEXT,
    `x` INT,
    `xlen` INT,
    `y` INT,
    `ylen` INT,
    `title` VARCHAR(255) DEFAULT NULL,
    `author` VARCHAR(255),
    `description` TEXT,
    `public` TINYINT(1) DEFAULT 0,
    `date` TEXT, 
    `publisher` TEXT,
    `published_place` TEXT,
    `published_date` TEXT,
    `people` TEXT,
    `locations` TEXT,
    `transcript` TEXT,
    `genre` TEXT,
    `modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    `owner_id` INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY  (`id`),
    KEY `public` (`public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
SQL
    );
}

/**
 * Uninstall the plugin.
 */
function annotations_uninstall()
{
    // drop the tables
    $db = get_db();
    $sql = "DROP TABLE IF EXISTS `{$db->prefix}annotations`";
    $db->query($sql);
}

function insert_annotation($ann) {
	$db = get_db();
	$annValues = $ann->toArray();
	
	$sql = "INSERT INTO `{$db->prefix}annotations`";
	$cols = " (";
	$values = ") VALUES (";
	$firstRun = true;
	foreach ($annValues as $key => $value) {
		 if(isset($value) && $key!="modified" && $key!="id") {
		 	 if(!$firstRun){
		 	 	 $cols .= ", ";
		 	 	 $values .= ", ";
		 	 }
			 $cols .= "`$key`";
			 $values .= "'$value'";
		 	 $firstRun = false;
		 }
		 
	}
	$cols .= ", `modified`";
	$values .= ", NOW()";
	$sql .= $cols . $values . ")";
	
    $db->query($sql);
    	return $sql;
}

function update_annotation($ann) {
	$db = get_db();
	$annValues = $ann->toArray();
	
	$sql = "UPDATE `{$db->prefix}annotations`";
	$set = " SET ";
	$where = " WHERE `id`={$annValues['id']}";
	$firstRun = true;
	foreach ($annValues as $key => $value) {
		 if(isset($value) && $key!="modified" && $key!="id") {
		 	 if(!$firstRun){
		 	 	 $set .= ", ";
		 	 }
			 $set .= "`$key`='$value'";
		 	 $firstRun = false;
		 }
		 
	}
	$set .= ", `modified`=NOW()";
	$sql .= $set . $where;
	
    $db->query($sql);
    	return $sql;
}

function get_annotations($image_id) {
	$db = get_db();
	
	$sql = "SELECT * `{$db->prefix}annotations` WHERE `image_id`='$image_id'";
	$rows = $db->query($sql);
	$result = Array();
	
	if ($rows->num_rows > 0) {
			while ($row = $rows->fetch_assoc()) {
				$ann = new Annotation();
				foreach ($row as $key => $value) {
					if($key=="id") $ann->id = $value;
					else if($key=="image_id") $ann->image_id = $value;
					else if($key=="x") $ann->x = $value;
					else if($key=="xlen") $ann->xlen = $value;
					else if($key=="y") $ann->y = $value;
					else if($key=="ylen") $ann->ylen = $value;
					else if($key=="title") $ann->title = $value;
					else if($key=="author") $ann->author = $value;
					else if($key=="description") $ann->description = $value;
					else if($key=="public") $ann->public = $value;
					else if($key=="date") $ann->date = $value;
					else if($key=="publisher") $ann->publisher = $value;
					else if($key=="published_place") $ann->published_place = $value;
					else if($key=="published_date") $ann->published_date = $value;
					else if($key=="people") $ann->people = $value;
					else if($key=="locations") $ann->locations = $value;
					else if($key=="transcript") $ann->transcript = $value;
					else if($key=="genre") $ann->genre = $value;
					else if($key=="modified") $ann->modified = $value;
					else if($key=="owner_id") $ann->owner_id = $value;	
				}
				$result[] = $ann;
			}
		}
	
	return $result;
	
}