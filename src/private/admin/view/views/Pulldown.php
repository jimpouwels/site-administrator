<?php
require_once CMS_ROOT . "/view/views/FormField.php";

class Pulldown extends FormField {

    private array $_options;
    private bool $_include_select_indication;

    public function __construct(TemplateEngine $templateEngine, string $name, string $label, ?string $value, array $options, bool $mandatory, ?string $className, bool $include_select_indication = false) {
        parent::__construct($templateEngine, $name, $value, $label, $mandatory, false, $className);
        $this->_include_select_indication = $include_select_indication;
        $this->_options = $options;
    }

    public function getFormFieldTemplateFilename(): string {
        return "system/form_pulldown.tpl";
    }

    public function loadFormField(Smarty_Internal_Data $data): void {
        $data->assign("options", $this->_options);
        $data->assign("include_select_indication", $this->_include_select_indication);
    }

    public function addOption(string $name, string $value): void {
        $this->_options[] = array('name' => $name, 'value' => $value);
    }

    public function getFieldType(): string {
        return 'pulldown';
    }

}