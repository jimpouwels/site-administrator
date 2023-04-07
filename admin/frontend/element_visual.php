<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "frontend/frontend_visual.php";

    abstract class ElementFrontendVisual extends FrontendVisual {

        private Element $_element;

        public function __construct($current_page, $element) {
            parent::__construct($current_page);
            $this->_element = $element;
        }

        public abstract function renderElement(): string;

        public function render(): string {
            $this->getTemplateEngine()->assign("toc_reference", $this->toAnchorValue($this->_element->getTitle()));
            $this->getTemplateEngine()->assign("include_in_table_of_contents", $this->_element->includeInTableOfContents());
            $this->getTemplateEngine()->assign("element_html", $this->renderElement());
            return $this->getTemplateEngine()->fetch(FRONTEND_TEMPLATE_DIR . "/element.tpl");
        }

    }