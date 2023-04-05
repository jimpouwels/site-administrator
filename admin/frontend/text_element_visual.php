<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "frontend/frontend_visual.php";

    class TextElementFrontendVisual extends FrontendVisual {

        private $_template_engine;
        private $_text_element;
    
        public function __construct($current_page, $text_element) {
            parent::__construct($current_page);
            $this->_template_engine = TemplateEngine::getInstance();
            $this->_text_element = $text_element;
        }

        public function render(): string {
            $element_holder = $this->_text_element->getElementHolder();
            $this->_template_engine->assign("title", $this->toHtml($this->_text_element->getTitle(), $element_holder));
            $this->_template_engine->assign("text", $this->toHtml($this->_text_element->getText(), $element_holder));
            return $this->_template_engine->fetch(FRONTEND_TEMPLATE_DIR . "/" . $this->_text_element->getTemplate()->getFileName());
        }
    }
    
?>