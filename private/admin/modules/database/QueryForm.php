<?php
require_once CMS_ROOT . "/core/form/Form.php";

class QueryForm extends Form {

    private ?string $query = null;

    public function loadFields(): void {
        $this->query = $this->getMandatoryFieldValue('query');
        if ($this->hasErrors()) {
            throw new FormException();
        }
    }

    public function getQuery(): ?string {
        return $this->query;
    }
}