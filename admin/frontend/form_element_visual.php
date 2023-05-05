<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . 'frontend/element_visual.php';
    require_once CMS_ROOT . 'modules/webforms/webform_field_factory.php';

    class FormElementFrontendVisual extends ElementFrontendVisual {

        private WebFormFieldFactory $_webform_field_factory;

        public function __construct(Page $page, ?Article $article, FormElement $form_element) {
            parent::__construct($page, $article, $form_element);
            $this->_webform_field_factory = WebFormFieldFactory::getInstance();
        }

        public function getElementTemplateFilename(): string {
            return FRONTEND_TEMPLATE_DIR . "/" . $this->getElement()->getTemplate()->getFileName();
        }

        public function loadElement(Smarty_Internal_Data $data): void {
            $data->assign('title', $this->getElement()->getTitle());
            $webform_data = array();
            if ($this->getElement()->getWebForm()) {
                $webform_data = $this->renderWebForm($this->getElement()->getWebForm());
            }
            $data->assign('webform', $webform_data);
        }

        private function renderWebForm(WebForm $webform): array {
            $webform_data = array();
            $webform_data['title'] = $webform->getTitle();
            $webform_data['fields'] = $this->renderFields($webform);
            return $webform_data;
        }

        private function renderFields(WebForm $webform): array {
            $fields = array();
            foreach ($webform->getFormFields() as $form_field) {
                $field = $this->_webform_field_factory->getFrontendVisualFor($form_field, $this->getPage(), $this->getArticle());
                $fields[] = $field->render();
            }
            return $fields;
        }
    }