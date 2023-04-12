<?php

    defined("_ACCESS") or die;
    
    require_once CMS_ROOT . "core/form/form.php";
    
    class PositionForm extends Form {
    
        private BlockPosition $_position;
        private BlockDao $_block_dao;
    
        public function __construct($position) {
            $this->_position = $position;
            $this->_block_dao = BlockDao::getInstance();
        }
    
        public function loadFields(): void {
            $positionName = str_replace(" ", "_", $this->getMandatoryFieldValue("name", "Naam is verplicht"));
            $this->_position->setName($positionName);
            $this->_position->setExplanation($this->getFieldValue("explanation"));
            if ($this->hasErrors() || $this->positionAlreadyExists()) {
                throw new FormException();
            }
        }
        
        private function positionAlreadyExists() {
            $existing_pos = $this->_block_dao->getBlockPositionByName($this->_position->getName());
            if (!is_null($existing_pos) && $existing_pos->getId() != $this->_position->getId()) {
                $this->raiseError("name", "Er bestaat al een positie met deze naam");
                return true;
            }
            return false;
        }
        
    }
    