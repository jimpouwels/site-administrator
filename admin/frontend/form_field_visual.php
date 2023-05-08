<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . 'frontend/form_item_visual.php';
    require_once CMS_ROOT . 'frontend/handlers/form_status.php';

    abstract class FormFieldVisual extends FormItemVisual {
        
        public function __construct(Page $page, ?Article $article, WebForm $webform, WebFormField $webform_field) {
            parent::__construct($page, $article, $webform, $webform_field);
        }

        public function getFormItemTemplateFilename(): string {
            return FRONTEND_TEMPLATE_DIR . '/form_field.tpl';
        }

        public function loadFormItem(Smarty_Internal_Data $data): void {
            $mandatory = false;
            if ($this->getFormItem() instanceof WebFormField) {
                $mandatory = $this->getFormItem()->getMandatory();
            }
            $data->assign('value', FormStatus::getFieldValue($this->getFormItem()->getName()));
            $data->assign('mandatory', $mandatory);
            
            $this->loadFormField($data);
            $data->assign('form_field_html', $this->getTemplateEngine()->fetch($this->getFormFieldTemplateFilename(), $data));
        }

        abstract function loadFormField(Smarty_Internal_Data $data): void;
        abstract function getFormFieldTemplateFilename(): string;
    }
?>