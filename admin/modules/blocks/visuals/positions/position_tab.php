<?php
    
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "modules/blocks/visuals/positions/position_editor.php";
    require_once CMS_ROOT . "modules/blocks/visuals/positions/position_list.php";
    
    class PositionTab extends Visual {
    
        private static $TEMPLATE = "blocks/positions/root.tpl";
        private static $POSITION_QUERYSTRING_KEY = "position";
        private static $NEW_POSITION_QUERYSTRING_KEY = "new_position";
    
        private $_current_position;
    
        public function __construct($current_position) {
            parent::__construct();
            $this->_current_position = $current_position;
        }
    
        public function renderVisual(): string {
            if ($this->isEditPositionMode()) {
                $this->getTemplateEngine()->assign("position_editor", $this->renderPositionEditor());
            }
            $this->getTemplateEngine()->assign("position_list", $this->renderPositionList());
            
            return $this->getTemplateEngine()->fetch("modules/" . self::$TEMPLATE);
        }
        
        public static function isEditPositionMode() {
            return (isset($_GET[self::$POSITION_QUERYSTRING_KEY]) && $_GET[self::$POSITION_QUERYSTRING_KEY] != '');
        }
        
        private function renderPositionEditor() {
            $position_editor = new PositionEditor($this->_current_position);
            return $position_editor->render();
        }
        
        private function renderPositionList() {
            $position_list = new PositionList();
            return $position_list->render();
        }
    
    }
    
?>