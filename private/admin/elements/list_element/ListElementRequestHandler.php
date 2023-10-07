<?php
require_once CMS_ROOT . "/request_handlers/HttpRequestHandler.php";
require_once CMS_ROOT . "/elements/list_element/ListElementForm.php";
require_once CMS_ROOT . "/database/dao/ElementDaoMysql.php";

class ListElementRequestHandler extends HttpRequestHandler {

    private ListElement $listElement;
    private ListElementForm $listElementForm;
    private ElementDao $elementDao;

    public function __construct($listElement) {
        $this->listElement = $listElement;
        $this->listElementForm = new ListElementForm($this->listElement);
        $this->elementDao = ElementDaoMysql::getInstance();
    }

    public function handleGet(): void {}

    public function handlePost(): void {
        $this->listElementForm->loadFields();
        foreach ($this->listElementForm->getListItemsToDelete() as $list_item_to_delete) {
            $this->listElement->deleteListItem($list_item_to_delete);
        }
        if ($this->isAddListItemAction()) {
            $this->listElement->addListItem();
        }
        $this->elementDao->updateElement($this->listElement);
    }

    private function isAddListItemAction(): bool {
        return isset($_POST['element' . $this->listElement->getId() . '_add_item']) &&
            $_POST['element' . $this->listElement->getId() . '_add_item'] != '';
    }
}

?>