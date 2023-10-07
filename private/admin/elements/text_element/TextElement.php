<?php
require_once CMS_ROOT . "/core/model/Element.php";
require_once CMS_ROOT . "/core/model/ElementMetadataProvider.php";
require_once CMS_ROOT . "/elements/text_element/visuals/TextElementEditor.php";
require_once CMS_ROOT . "/elements/text_element/visuals/TextElementStatics.php";
require_once CMS_ROOT . "/elements/text_element/TextElementRequestHandler.php";
require_once CMS_ROOT . "/database/MysqlConnector.php";
require_once CMS_ROOT . "/frontend/TextElementFrontendVisual.php";

class TextElement extends Element {

    private ?string $text = null;

    public function __construct(int $scopeId) {
        parent::__construct($scopeId, new TextElementMetadataProvider($this));
    }

    public function setText(?string $text): void {
        $this->text = $text;
    }

    public function getText(): ?string {
        return $this->text;
    }

    public function getStatics(): Visual {
        return new TextElementStatics();
    }

    public function getBackendVisual(): ElementVisual {
        return new TextElementEditor($this);
    }

    public function getFrontendVisual(Page $page, ?Article $article): FrontendVisual {
        return new TextElementFrontendVisual($page, $article, $this);
    }

    public function getRequestHandler(): HttpRequestHandler {
        return new TextElementRequestHandler($this);
    }

    public function getSummaryText(): string {
        $summaryText = $this->getTitle();
        $summaryText .= ' (\'' . substr($this->getText(), 0, 50) . '...\')';
        return $summaryText;
    }

}

class TextElementMetadataProvider extends ElementMetadataProvider {

    private MysqlConnector $mysqlConnector;

    public function __construct(TextElement $textElement) {
        parent::__construct($textElement);
        $this->mysqlConnector = MysqlConnector::getInstance();
    }

    public function getTableName(): string {
        return "text_elements_metadata";
    }

    public function constructMetaData(array $record, $element): void {
        $element->setTitle($record['title']);
        $element->setText($record['text']);
    }

    public function update(Element $element): void {
        $title = $element->getTitle();
        $text = $element->getText();
        $elementId = $element->getId();
        $query = "UPDATE text_elements_metadata SET title = ?, text = ? WHERE element_id = ?";
        $statement = $this->mysqlConnector->prepareStatement($query);
        $statement->bind_param('ssi', $title, $text, $elementId);
        $this->mysqlConnector->executeStatement($statement);
    }

    public function insert(Element $element): void {
        $title = $element->getTitle();
        $text = $element->getText();
        $elementId = $element->getId();
        $query = "INSERT INTO text_elements_metadata (title, `text`, element_id) VALUES (?, ?, ?)";
        $statement = $this->mysqlConnector->prepareStatement($query);
        $statement->bind_param('ssi', $title, $text, $elementId);
        $this->mysqlConnector->executeStatement($statement);
    }

}

?>