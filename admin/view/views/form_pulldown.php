<?php
    defined('_ACCESS') or die;
    
    require_once CMS_ROOT . "view/views/form_field.php";
    
    class Pulldown extends FormField {
    
        private static $TEMPLATE = "system/form_pulldown.tpl";
        private $_options;
    
        public function __construct($name, $label, $value, $options, $mandatory, $class_name) {
            parent::__construct($name, $value, $label, $mandatory, false, $class_name);
            $this->_options = $options;
        }
    
        public function render(): string {
            $this->getTemplateEngine()->assign("options", $this->_options);
            return parent::render() . $this->getTemplateEngine()->fetch(self::$TEMPLATE);
        }
    
    }

?>