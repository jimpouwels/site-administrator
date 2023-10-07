<?php
require_once CMS_ROOT . "/core/model/Element.php";
require_once CMS_ROOT . "/core/model/ElementMetadataProvider.php";
require_once CMS_ROOT . "/database/MysqlConnector.php";
require_once CMS_ROOT . "/elements/list_element/ListItem.php";
require_once CMS_ROOT . "/elements/list_element/visuals/ListElementStatics.php";
require_once CMS_ROOT . "/elements/list_element/visuals/ListElementEditor.php";
require_once CMS_ROOT . "/frontend/ListElementFrontendVisual.php";
require_once CMS_ROOT . "/elements/list_element/ListElementRequestHandler.php";

class ListElement extends Element {

    private array $listItems = array();

    public function __construct(int $scopeId) {
        parent::__construct($scopeId, new ListElementMetaDataProvider($this));
    }

    public function getListItems(): array {
        return $this->listItems;
    }

    public function setListItems(array $listItems): void {
        $this->listItems = $listItems;
    }

    public function addListItem(): void {
        $listItem = new ListItem();
        $listItem->setIdent(0);
        $this->listItems[] = $listItem;
    }

    public function deleteListItem(ListItem $listItemToDelete): void {
        $this->listItems = array_filter($this->listItems, function ($listItem) use ($listItemToDelete) {
            return $listItem->getId() !== $listItemToDelete->getId();
        });
        $this->getMetaDataProvider()->deleteListItem();
    }

    public function getStatics(): Visual {
        return new ListElementStatics();
    }

    public function getBackendVisual(): ElementVisual {
        return new ListElementEditor($this);
    }

    public function getFrontendVisual(Page $page, ?Article $article): FrontendVisual {
        return new ListElementFrontendVisual($page, $article, $this);
    }

    public function getRequestHandler(): HttpRequestHandler {
        return new ListElementRequestHandler($this);
    }

    public function getSummaryText(): string {
        return $this->getTitle() || '';
    }
}

class ListElementMetaDataProvider extends ElementMetadataProvider {

    private static string $myAllColumns = "i.id, i.text, i.indent, i.element_id";
    private MysqlConnector $mysqlConnector;

    public function __construct(Element $element) {
        parent::__construct($element);
        $this->mysqlConnector = MysqlConnector::getInstance();
    }

    public function getTableName(): string {
        return "list_elements_metadata";
    }

    public function constructMetaData(array $record, $element): void {
        $element->setTitle($record['title']);
        $element->setListItems($this->getListItems($element));
    }

    public function deleteListItem(ListItem $list_item): void {
        $query = "DELETE FROM list_element_items WHERE id = " . $list_item->getId();
        $this->mysqlConnector->executeQuery($query);
    }

    public function update(Element $element): void {
        $query = "UPDATE list_elements_metadata SET title = '" . $element->getTitle() . "' WHERE element_id = " . $element->getId();
        $this->mysqlConnector->executeQuery($query);

        $this->storeItems($element->getListItems());
    }

    public function insert(Element $element): void {
        $query = "INSERT INTO list_elements_metadata (title, element_id) VALUES 
                        ('" . $element->getTitle() . "', " . $element->getId() . ")";
        $this->mysqlConnector->executeQuery($query);
    }

    private function storeItems(array $items): void {
        foreach ($items as $listItem) {
            $statement = null;
            $id = $listItem->getId();
            $indent = $listItem->getIndent();
            $text = $listItem->getText();
            $elementId = $this->getElement()->getId();
            if (!is_null($listItem->getId())) {
                $query = "UPDATE list_element_items SET `text` = ?, indent = ? WHERE id = ?";
                $statement = $this->mysqlConnector->prepareStatement($query);
                $statement->bind_param('sii', $text, $indent, $id);
            } else {
                $query = "INSERT INTO list_element_items (`text`, indent, element_id) VALUES ('', ?, ?)";
                $statement = $this->mysqlConnector->prepareStatement($query);
                $statement->bind_param('ii', $indent, $elementId);
            }
            $this->mysqlConnector->executeStatement($statement);
        }
    }

    private function getListItems(Element $element): array {
        $query = "SELECT " . self::$myAllColumns . " FROM list_element_items i, elements e WHERE i.element_id = " . $element->getId() .
            " AND e.id = " . $element->getId();
        $result = $this->mysqlConnector->executeQuery($query);
        $listItems = array();
        while ($row = $result->fetch_assoc()) {
            $listItem = new ListItem();
            $listItem->setId($row['id']);
            $listItem->setText($row['text']);
            $listItem->setIdent($row['indent']);
            $listItem->setElementId($row['element_id']);

            $listItems[] = $listItem;
        }

        return $listItems;
    }

}