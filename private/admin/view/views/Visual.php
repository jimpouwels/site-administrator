<?php
require_once CMS_ROOT . "/core/Blackboard.php";

abstract class Visual {

    private TemplateEngine $_template_engine;
    private Smarty_Internal_Data $_template_data;

    public function __construct(?Visual $parent = null) {
        $this->_template_engine = TemplateEngine::getInstance();
        $this->_template_data = $this->_template_engine->createChildData();
    }

    public function render(): string {
        $this->load();
        return $this->_template_engine->fetch($this->getTemplateFilename(), $this->_template_data);
    }

    abstract function load(): void;

    abstract function getTemplateFilename(): string;

    protected function getTemplateEngine(): TemplateEngine {
        return $this->_template_engine;
    }

    protected function assign(string $key, mixed $value): void {
        $this->_template_data->assign($key, $value);
    }

    protected function assignGlobal(string $key, mixed $value): void {
        $this->_template_engine->assign($key, $value);
    }

    protected function createChildData(): Smarty_Internal_Data {
        return $this->_template_engine->createChildData();
    }

    protected function fetch(string $template, Smarty_Internal_Data $data): string {
        return $this->_template_engine->fetch($template, $data);
    }

    protected function getTextResource(string $identifier): string {
        return Session::getTextResource($identifier);
    }

    protected function getBackendBaseUrl(): string {
        return BlackBoard::getBackendBaseUrl();
    }

    protected function getBackendBaseUrlRaw(): string {
        return BlackBoard::getBackendBaseUrlRaw();
    }

    protected function getBackendBaseUrlWithoutTab(): string {
        return BlackBoard::getBackendBaseUrlWithoutTab();
    }

    protected function getCurrentTabId(): int {
        return BlackBoard::$MODULE_TAB_ID;
    }
}