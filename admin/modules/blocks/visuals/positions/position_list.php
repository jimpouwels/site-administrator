<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "view/views/information_message.php";

    class PositionList extends Panel {

        private static $TEMPLATE = "blocks/positions/list.tpl";

        private $_template_engine;
        private $_block_dao;

        public function __construct() {
            parent::__construct('Posities');
            $this->_template_engine = TemplateEngine::getInstance();
            $this->_block_dao = BlockDao::getInstance();
        }

        public function render(): string {
            return parent::render();
        }

        public function renderPanelContent() {
            $this->_template_engine->assign("all_positions", $this->getAllPositions());
            $no_positions_message = new InformationMessage($this->getTextResource("blocks_no_positions_found"));
            $this->_template_engine->assign("no_positions_message", $no_positions_message->render());

            return $this->_template_engine->fetch("modules/" . self::$TEMPLATE);
        }

        private function getAllPositions() {
            $all_positions_values = array();
            $all_positions = $this->_block_dao->getBlockPositions();

            foreach ($all_positions as $position) {
                $position_value = array();
                $position_value["id"] = $position->getId();
                $position_value["name"] = $position->getName();
                $position_value["explanation"] = $position->getExplanation();
                $delete_field = new SingleCheckbox("position_" . $position->getId() . "_delete", "", false, false, "");
                $position_value["delete_field"] = $delete_field->render();

                $all_positions_values[] = $position_value;
            }
            return $all_positions_values;
        }
    }
