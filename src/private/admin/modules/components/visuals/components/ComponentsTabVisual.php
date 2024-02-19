<?php
require_once CMS_ROOT . '/modules/components/visuals/components/ModulesListPanel.php';
require_once CMS_ROOT . '/modules/components/visuals/components/ElementsListPanel.php';
require_once CMS_ROOT . '/modules/components/visuals/components/ComponentsDetailsPanel.php';

class ComponentsTabVisual extends Obcato\ComponentApi\Visual {

    private ComponentRequestHandler $componentRequestHandler;

    public function __construct(TemplateEngine $templateEngine, $componentRequestHandler) {
        parent::__construct($templateEngine);
        $this->componentRequestHandler = $componentRequestHandler;
    }

    public function getTemplateFilename(): string {
        return 'modules/components/components/root.tpl';
    }

    public function load(): void {
        $modules_list = new ModulesListPanel($this->getTemplateEngine(), $this->componentRequestHandler);
        $elements_list = new ElementsListPanel($this->getTemplateEngine(), $this->componentRequestHandler);
        if ($this->componentRequestHandler->getCurrentElementType() || $this->componentRequestHandler->getCurrentModule()) {
            $details = new ComponentsDetailsPanel($this->getTemplateEngine(), $this->componentRequestHandler);
            $this->assign('details', $details->render());
        }
        $this->assign('modules_list', $modules_list->render());
        $this->assign('elements_list', $elements_list->render());
    }
}
