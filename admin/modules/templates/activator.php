<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "view/views/module_visual.php";
    require_once CMS_ROOT . "modules/templates/template_request_handler.php";
    require_once CMS_ROOT . "modules/templates/visuals/template_list.php";
    require_once CMS_ROOT . "modules/templates/visuals/template_editor.php";
    require_once CMS_ROOT . "modules/templates/visuals/template_var_editor.php";
    require_once CMS_ROOT . "modules/templates/visuals/template_code_viewer.php";

    class TemplateModuleVisual extends ModuleVisual {

        private static string $HEAD_INCLUDES_TEMPLATE = "templates/head_includes.tpl";

        private Module $_template_module;
        private TemplateRequestHandler $_template_request_handler;
        private ?Template $_current_template;
        private ?Scope $_current_scope;

        public function __construct(Module $template_module) {
            parent::__construct($template_module);
            $this->_template_module = $template_module;
            $this->_template_request_handler = new TemplateRequestHandler();
        }

        public function getTemplateFilename(): string {
            return "modules/templates/root.tpl";
        }

        public function load(): void {
            $this->assign("current_template_id", $this->getCurrentTemplateId());
            if (!is_null($this->_current_template)) {
                $this->assign("template_editor", $this->renderTemplateEditor());
                $this->assign("template_var_editor", $this->renderTemplateVarEditor());
                $this->assign('template_code_viewer', $this->renderTemplateCodeViewer());
            }
            $this->assign("scope_selector", $this->getScopeSelector());
            if (!is_null($this->_current_scope)) {
                $this->assign("template_list", $this->renderTemplateList());
            }
        }

        public function getActionButtons(): array {
            $action_buttons = array();
            if (!is_null($this->_current_template)) {
                $action_buttons[] = new ActionButtonSave('update_template');
                $action_buttons[] = new ActionButtonReload('reload_template');
            }
            $action_buttons[] = new ActionButtonAdd('add_template');
            if (!is_null($this->_current_scope)) {
                $action_buttons[] = new ActionButtonDelete('delete_template');
            }
            return $action_buttons;
        }

        public function renderHeadIncludes(): string {
            $this->getTemplateEngine()->assign("path", $this->_template_module->getIdentifier());
            return $this->getTemplateEngine()->fetch("modules/" . self::$HEAD_INCLUDES_TEMPLATE);
        }

        public function getRequestHandlers(): array {
            $request_handlers = array();
            $request_handlers[] = $this->_template_request_handler;
            return $request_handlers;
        }

        public function onRequestHandled(): void {
            $this->_current_template = $this->_template_request_handler->getCurrentTemplate();
            $this->_current_scope = $this->_template_request_handler->getCurrentScope();
        }

        public function getTitle(): string {
            return $this->getTextResource($this->_template_module->getTitleTextResourceIdentifier());
        }

        public function getTabMenu(): ?TabMenu {
            return null;
        }

        private function getScopeSelector(): string {
            $scope_selector = new ScopeSelector();
            return $scope_selector->render();
        }

        private function renderTemplateEditor(): string {
            $template_editor = new TemplateEditor($this->_current_template);
            return $template_editor->render();
        }

        private function renderTemplateCodeViewer(): string {
            $template_code_viewer = new TemplateCodeViewer($this->_current_template);
            return $template_code_viewer->render();
        }

        private function renderTemplateVarEditor(): string {
            $template_var_editor = new TemplateVarEditor($this->_current_template);
            return $template_var_editor->render();
        }

        private function renderTemplateList(): string {
            $template_list = new TemplateList($this->_current_scope);
            return $template_list->render();
        }

        private function getCurrentTemplateId(): ?int {
            $current_template_id = null;
            if (!is_null($this->_current_template)) {
                $current_template_id = $this->_current_template->getId();
            }
            return $current_template_id;
        }

    }

?>
