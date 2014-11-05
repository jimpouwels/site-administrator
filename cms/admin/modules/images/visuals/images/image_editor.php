<?php        defined('_ACCESS') or die;    require_once CMS_ROOT . "database/dao/image_dao.php";        class ImageEditor extends Visual {            private static $TEMPLATE = "images/images/editor.tpl";            private $_template_engine;        private $_current_image;        private $_image_dao;            public function __construct($current_image) {            $this->_current_image = $current_image;            $this->_image_dao = ImageDao::getInstance();            $this->_template_engine = TemplateEngine::getInstance();        }            public function render() {            $this->assignFormIds();            $this->assignImageMetaDataFields();            $this->assignLabelSelector();            $this->assignImageValues();            return $this->_template_engine->fetch("modules/" . self::$TEMPLATE);        }                private function assignImageValues() {            $this->_template_engine->assign("title", $this->_current_image->getTitle());            $this->_template_engine->assign("id", $this->_current_image->getId());            $this->_template_engine->assign("url", $this->_current_image->getUrl());        }                private function assignLabelSelector() {            $image_labels = $this->_image_dao->getLabelsForImage($this->_current_image->getId());            $all_label_values = $this->labelsToArray($this->_image_dao->getAllLabels(), $image_labels);            $image_label_values = $this->selectedLabelsToArray($image_labels);            $this->_template_engine->assign("all_labels", $all_label_values);            $this->_template_engine->assign("image_labels", $image_label_values);        }                private function assignImageMetaDataFields() {            $title_field = new TextField("image_title", "Titel", $this->_current_image->getTitle(), true, false, null);            $published_field = new SingleCheckbox("image_published", "Gepubliceerd", $this->_current_image->isPublished(), false, null);            $upload_field = new UploadField("image_file", "Afbeelding", false, null);            $this->_template_engine->assign("image_id", $this->_current_image->getId());            $this->_template_engine->assign("title_field", $title_field->render());            $this->_template_engine->assign("published_field", $published_field->render());            $this->_template_engine->assign("upload_field", $upload_field->render());        }                private function assignFormIds() {            $this->_template_engine->assign("action_form_id", ACTION_FORM_ID);        }                private function labelsToArray($labels, $image_labels) {            $label_values = array();            foreach ($labels as $label) {                $label_value = $this->createLabelValue($label);                $label_value["is_selected"] = in_array($label, $image_labels);                $label_values[] = $label_value;            }            return $label_values;        }                private function selectedLabelsToArray($labels) {            $label_values = array();            foreach ($labels as $label) {                $label_value = $this->createLabelValue($label);                $label_value["delete_checkbox"] = $this->renderSelectedLabelCheckbox($label);                $label_values[] = $label_value;            }            return $label_values;        }                private function renderSelectedLabelCheckbox($label) {            $checkbox = new SingleCheckbox("label_" . $label->getId() . "_delete", "", 0, false, "");            return $checkbox->render();        }                private function createLabelValue($label) {            $label_value = array();            $label_value["id"] = $label->getId();            $label_value["name"] = $label->getName();            return $label_value;        }            }    ?>