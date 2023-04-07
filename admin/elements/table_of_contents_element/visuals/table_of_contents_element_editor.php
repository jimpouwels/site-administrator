<?php
    defined('_ACCESS') or die;
    
    require_once CMS_ROOT . "view/views/element_visual.php";
    require_once CMS_ROOT . "view/views/form_textfield.php";
    require_once CMS_ROOT . "view/views/form_date.php";
    require_once CMS_ROOT . "view/views/term_selector.php";

    class TableOfContentsElementEditor extends ElementVisual {
    
        private static $TEMPLATE = "elements/table_of_contents_element/table_of_contents_element_form.tpl";
        private $_element;
    
        public function __construct($_element) {
            parent::__construct();
            $this->_element = $_element;
        }
    
        public function getElement(): Element {
            return $this->_element;
        }
        
        public function renderElementForm() {
            $title_field = new TextField("element_" . $this->_element->getId() . "_title", "Titel", $this->_element->getTitle(), false, true, null);
            $this->getTemplateEngine()->assign("title_field", $title_field->render());
            return $this->getTemplateEngine()->fetch(self::$TEMPLATE);
        }
        
    }
    
?>