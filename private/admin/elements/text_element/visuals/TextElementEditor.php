<?php
require_once CMS_ROOT . "/view/views/ElementVisual.php";
require_once CMS_ROOT . "/view/views/TextField.php";
require_once CMS_ROOT . "/view/views/TextArea.php";

class TextElementEditor extends ElementVisual {

    private static string $TEMPLATE = "elements/text_element/text_element_form.tpl";

    private TextElement $textElement;

    public function __construct(TextElement $textElement) {
        parent::__construct();
        $this->textElement = $textElement;
    }

    public function getElement(): Element {
        return $this->textElement;
    }

    public function renderElementForm(Smarty_Internal_Data $data): string {
        $titleField = new TextField('element_' . $this->textElement->getId() . '_title', $this->getTextResource("text_element_editor_title"), $this->textElement->getTitle(), false, true, null);
        $textField = new TextArea('element_' . $this->textElement->getId() . '_text', $this->getTextResource("text_element_editor_text"), $this->textElement->getText(), false, true, null);

        $data->assign("title_field", $titleField->render());
        $data->assign("text_field", $textField->render());
        return $this->getTemplateEngine()->fetch(self::$TEMPLATE, $data);
    }

}

?>