<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "view/views/element_visual.php";
    require_once CMS_ROOT . "view/views/form_textfield.php";
    require_once CMS_ROOT . "view/views/image_picker.php";

    class ImageElementEditorVisual extends ElementVisual {

        private static $TEMPLATE = "elements/image_element/image_element_form.tpl";

        private $_template_engine;
        private $_image_element;

        public function __construct($image_element) {
            $this->_template_engine = TemplateEngine::getInstance();
            $this->_image_element = $image_element;
        }

        public function getElement(): Element {
            return $this->_image_element;
        }

        public function renderElementForm() {
            $title_field = new TextField($this->createFieldId("title"), "Titel", htmlentities($this->_image_element->getTitle()), false, false, null);
            $alternative_text_field = new TextField($this->createFieldId("alternative_text"), "Alternatieve tekst", $this->_image_element->getAlternativeText(), false, true, null);
            $image_picker = new ImagePicker("Afbeelding", $this->_image_element->getImageId(), "image_image_ref_" . $this->_image_element->getId(), "Selecteer afbeelding", "update_element_holder", "");
            $width_field = new TextField($this->createFieldId("width"), "Breedte", $this->_image_element->getWidth(), false, false, "size_field");
            $height_field = new TextField($this->createFieldId("height"), "Hoogte", $this->_image_element->getHeight(), false, false, "size_field");

            $this->_template_engine->assign("alignment_field", $this->getAlignmentField());
            $this->_template_engine->assign("title_field", $title_field->render());
            $this->_template_engine->assign("alternative_text_field", $alternative_text_field->render());
            $this->_template_engine->assign("width_field", $width_field->render());
            $this->_template_engine->assign("height_field", $height_field->render());
            $this->_template_engine->assign("image_picker", $image_picker->render());
            $this->_template_engine->assign("image_id", $this->_image_element->getImageId());
            $this->_template_engine->assign("selected_image_title", $this->getSelectedImageTitle());
            return $this->_template_engine->fetch(self::$TEMPLATE);
        }

        private function getAlignmentField() {
            $alignment_options = array();
            array_push($alignment_options, array("name" => "Links", "value" => "left"));
            array_push($alignment_options, array("name" => "Rechts", "value" => "right"));
            array_push($alignment_options, array("name" => "Midden", "value" => "center"));
            $current_alignment = $this->_image_element->getAlign();
            $alignment_field = new Pulldown("element_" . $this->_image_element->getId() . "_align", "Uitlijning", $current_alignment, $alignment_options, false, null);
            return $alignment_field->render();
        }

        private function getSelectedImageTitle() {
            $selected_image_title = "";
            $selected_image = $this->_image_element->getImage();
            if (!is_null($selected_image)) {
                $selected_image_title = $selected_image->getTitle();;
            }
            return $selected_image_title;
        }

        private function createFieldId($property_name) {
            return "element_" . $this->_image_element->getId() . "_" . $property_name;
        }

    }

?>
