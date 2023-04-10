<?php
    defined('_ACCESS') or die;

    class SearchBoxVisual extends Panel {

        private static $TEMPLATE = "downloads/search_box.tpl";
        private $_download_request_handler;

        public function __construct($download_request_handler) {
            parent::__construct('Zoeken', 'download_search');
            $this->_download_request_handler = $download_request_handler;
        }

        public function render(): string {
            return parent::render();
        }

        public function renderPanelContent() {
            $this->getTemplateEngine()->assign('search_query_field', $this->renderSearchQueryField());
            $this->getTemplateEngine()->assign('search_button', $this->renderSearchButton());
            return $this->getTemplateEngine()->fetch('modules/' . self::$TEMPLATE);
        }

        private function renderSearchQueryField() {
            $default_search_value = $this->_download_request_handler->getSearchQuery();
            $search_query_field = new TextField('search_query', 'Zoekterm', $default_search_value, false, false, null);
            return $search_query_field->render();
        }

        private function renderSearchButton() {
            $search_button = new Button('', 'Zoeken', 'document.getElementById(\'download_search\').submit(); return false;');
            return $search_button->render();
        }
    }
