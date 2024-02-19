<?php
require_once CMS_ROOT . "/modules/templates/visuals/template_files/TemplateFilesList.php";
require_once CMS_ROOT . "/modules/templates/visuals/template_files/TemplateFileEditor.php";
require_once CMS_ROOT . "/modules/templates/visuals/template_files/TemplateFileCodeViewer.php";
require_once CMS_ROOT . "/modules/templates/visuals/template_files/TemplateVarMigration.php";

class TemplateFilesTab extends Obcato\ComponentApi\Visual {

    private ?TemplateFile $currentTemplateFile;
    private TemplateFilesRequestHandler $requestHandler;

    public function __construct(TemplateEngine $templateEngine, TemplateFilesRequestHandler $requestHandler) {
        parent::__construct($templateEngine);
        $this->currentTemplateFile = $requestHandler->getCurrentTemplateFile();
        $this->requestHandler = $requestHandler;
    }

    public function getTemplateFilename(): string {
        return "modules/templates/template_files/template_files_tab.tpl";
    }

    public function load(): void {
        $this->assign("current_template_file_id", $this->getCurrentTemplateFileId());
        $templateFilesList = new TemplateFilesList($this->getTemplateEngine());
        $this->assign("template_files_list", $templateFilesList->render());

        $editorHtml = "";
        if ($this->currentTemplateFile) {
            $templateFileEditor = new TemplateFileEditor($this->getTemplateEngine(), $this->currentTemplateFile);
            $editorHtml = $templateFileEditor->render();
        }
        $this->assign("template_file_editor", $editorHtml);

        $varMigrationHtml = "";
        if (count($this->requestHandler->getParsedVarDefs()) > 0) {
            $templateVarMigration = new TemplateVarMigration($this->getTemplateEngine(), $this->requestHandler);
            $varMigrationHtml = $templateVarMigration->render();
        }
        $this->assign("template_var_migration", $varMigrationHtml);

        $codeViewerHtml = "";
        if ($this->currentTemplateFile) {
            $codeViewerHtml = $this->renderTemplateCodeViewer();
        }
        $this->assign("template_code_viewer", $codeViewerHtml);
    }

    private function getCurrentTemplateFileId(): string {
        $id = "";
        if ($this->currentTemplateFile) {
            $id = $this->currentTemplateFile->getId();
        }
        return $id;
    }

    private function renderTemplateCodeViewer(): string {
        return (new TemplateFileCodeViewer($this->getTemplateEngine(), $this->currentTemplateFile))->render();
    }

}