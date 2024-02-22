<?php

namespace Obcato\Core\admin\elements\text_element\visuals;

use Obcato\Core\admin\core\model\Element;
use Obcato\Core\admin\elements\text_element\TextElement;
use Obcato\Core\admin\view\TemplateData;
use Obcato\Core\admin\view\views\ElementVisual;
use Obcato\Core\admin\view\views\TextArea;
use Obcato\Core\admin\view\views\TextField;

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

    public function getElementFormTemplateFilename(): string {
        return self::$TEMPLATE;
    }

    public function loadElementForm(TemplateData $data): void {
        $titleField = new TextField('element_' . $this->textElement->getId() . '_title', $this->getTextResource("text_element_editor_title"), $this->textElement->getTitle(), false, true, null);
        $textField = new TextArea('element_' . $this->textElement->getId() . '_text', $this->getTextResource("text_element_editor_text"), $this->textElement->getText(), false, true, null);

        $data->assign("title_field", $titleField->render());
        $data->assign("text_field", $textField->render());
    }

}