<?php

namespace Obcato\Core\admin\modules\components\visuals\components;

use Obcato\Core\admin\database\dao\ElementDao;
use Obcato\Core\admin\database\dao\ElementDaoMysql;
use Obcato\Core\admin\modules\components\ComponentRequestHandler;
use Obcato\Core\admin\view\TemplateData;
use Obcato\Core\admin\view\views\Panel;

class ElementsListPanel extends Panel {

    private ElementDao $elementDao;
    private ComponentRequestHandler $componentsRequestHandler;

    public function __construct($requestHandler) {
        parent::__construct('Elementen', 'component-list-fieldset');
        $this->componentsRequestHandler = $requestHandler;
        $this->elementDao = ElementDaoMysql::getInstance();
    }

    public function getPanelContentTemplate(): string {
        return 'modules/components/components/elements_list.tpl';
    }

    public function loadPanelContent(TemplateData $data): void {
        $data->assign('elements', $this->getElementsData());
    }

    private function getElementsData(): array {
        $elementsData = array();
        foreach ($this->elementDao->getElementTypes() as $elementType) {
            $elementData = array();
            $elementData['id'] = $elementType->getId();
            $elementData['name'] = $this->getTextResource($elementType->getIdentifier() . '_label');
            $elementData['icon_url'] = '/admin/static.php?file=/elements/' . $elementType->getIdentifier() . "/img/" . $elementType->getIdentifier() . ".png";
            $elementData['is_current'] = $this->isCurrentElement($elementType);
            $elementsData[] = $elementData;
        }
        return $elementsData;
    }

    private function isCurrentElement($element): bool {
        $currentElement = $this->componentsRequestHandler->getCurrentElementType();
        return $currentElement && $currentElement->getId() == $element->getId();
    }
}
