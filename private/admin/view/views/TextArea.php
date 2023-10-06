<?php
require_once CMS_ROOT . "/view/views/FormField.php";

class TextArea extends FormField {

    public function __construct(string $name, string $label, ?string $value, bool $mandatory, bool $linkable, ?string $className) {
        parent::__construct($name, $value, $label, $mandatory, $linkable, $className);
    }

    public function getFormFieldTemplateFilename(): string {
        return "system/form_textarea.tpl";
    }

    public function loadFormField(Smarty_Internal_Data $data) {}

    public function getFieldType(): string {
        return 'textarea';
    }

}