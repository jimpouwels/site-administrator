<?php
require_once CMS_ROOT . "/database/dao/ImageDaoMysql.php";

class ImportTab extends Panel {

    private ImageDao $_image_dao;

    public function __construct(TemplateEngine $templateEngine) {
        parent::__construct($templateEngine, 'Importeren');
        $this->_image_dao = ImageDaoMysql::getInstance();
    }

    public function getPanelContentTemplate(): string {
        return "modules/images/import/root.tpl";
    }

    public function loadPanelContent(Smarty_Internal_Data $data): void {
        $data->assign("upload_field", $this->renderUploadField());
        $data->assign("labels_pulldown", $this->renderLabelPullDown());
    }

    private function renderUploadField(): string {
        $upload_field = new UploadField(TemplateEngine::getInstance(), "import_zip_file", "ZIP bestand", false, "");
        return $upload_field->render();
    }

    private function renderLabelPullDown(): string {
        $labels_name_value_pair = $this->getLabelsValuePair();
        $pulldown = new Pulldown(TemplateEngine::getInstance(), "import_label", "Label", null, $labels_name_value_pair, 200, false);
        return $pulldown->render();
    }

    private function getLabelsValuePair(): array {
        $labels_name_value_pair = array();
        $labels_name_value_pair[] = array("name" => "&gt; Selecteer", "value" => null);
        foreach ($this->_image_dao->getAllLabels() as $label) {
            $labels_name_value_pair[] = array("name" => $label->getName(), "value" => $label->getId());
        }
        return $labels_name_value_pair;
    }

}
