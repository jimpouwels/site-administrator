<?php

namespace Obcato\Core\admin\elements\text_element\visuals;

use Obcato\Core\admin\view\views\Visual;

class TextElementStatics extends Visual {

    public function __construct() {
        parent::__construct();
    }

    public function getTemplateFilename(): string {
        return "elements/text_element/text_element_statics.tpl";
    }

    public function load(): void {}

}
