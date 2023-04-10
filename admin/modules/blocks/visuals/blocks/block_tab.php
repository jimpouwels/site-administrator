<?php
    
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "modules/blocks/visuals/blocks/blocks_list.php";
    require_once CMS_ROOT . "modules/blocks/visuals/blocks/block_editor.php";
    
    class BlockTab extends Visual {
    
        private static $TEMPLATE = "blocks/blocks/root.tpl";
    
        private $_current_block;
    
        public function __construct($current_block) {
            parent::__construct();
            $this->_current_block = $current_block;
        }
    
        public function render(): string {
            $this->getTemplateEngine()->assign("blocks_list", $this->renderBlocksList());
            if (!is_null($this->_current_block)) {
                $this->getTemplateEngine()->assign("editor", $this->renderBlockEditor());
            }
        
            return $this->getTemplateEngine()->fetch("modules/" . self::$TEMPLATE);
        }
        
        private function renderBlocksList() {
            $blocks_list = new BlocksList($this->_current_block);
            return $blocks_list->render();
        }
        
        private function renderBlockEditor() {
            $block_editor = new BlockEditor($this->_current_block);
            return $block_editor->render();
        }
    
    }
    
?>