<?php

namespace Obcato\Core\admin\view\views;

use Obcato\Core\admin\view\TemplateData;

class PasswordField extends FormField {

    public function __construct(string $name, string $label, string $value, bool $mandatory, ?string $class_name) {
        parent::__construct($name, $value, $label, $mandatory, false, $class_name);
    }

    public function getFormFieldTemplateFilename(): string {
        return "system/form_password.tpl";
    }

    public function loadFormField(TemplateData $data) {}

    public function getFieldType(): string {
        return 'password';
    }

}
