<?php
    defined('_ACCESS') or die;
    
    require_once CMS_ROOT . "view/views/visual.php";

    class ImageElementStatics extends Visual {
    
        private static $TEMPLATE = "elements/image_element/image_element_statics.tpl";
        
        private $_template_engine;
    
        public function __construct() {
            $this->_template_engine = TemplateEngine::getInstance();
        }
        
        public function render(): string {
            return $this->_template_engine->fetch(self::$TEMPLATE);
        }
    
    }
    
?>