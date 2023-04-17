<?php
    defined('_ACCESS') or die;

    abstract class Entity {
    
        private int $_id;
        
        public function getId(): int {
            return $this->_id;
        }
        
        public function setId(int $id): void {
            $this->_id = $id;
        }
    
    }
    
?>