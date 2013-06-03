<?php

	// No direct access
	defined('_ACCESS') or die;
	
	include_once FRONTEND_REQUEST . "core/data/entity.php";

	class ImageLabel extends Entity {
	
		private static $TABLE_NAME = "image_labels";
	
		private $_name;
		
		public function setName($name) {
			$this->_name = $name;
		}
		
		public function getName() {
			return $this->_name;
		}
		
		public function persist() {
		}
		
		public function update() {
		}
		
		public function delete() {
		}
		
		public static function constructFromRecord($record) {
			$label = new ImageLabel();
			$label->setId($record['id']);
			$label->setName($record['name']);
			
			return $label;
		}
	
	}
	
?>