<?php

namespace Obcato\Core\admin\frontend;

use Obcato\Core\admin\database\dao\TemplateDao;
use Obcato\Core\admin\database\dao\TemplateDaoMysql;
use Obcato\Core\admin\frontend\handlers\FormStatus;
use Obcato\Core\admin\modules\articles\model\Article;
use Obcato\Core\admin\modules\pages\model\Page;
use Obcato\Core\admin\modules\templates\model\Presentable;
use Obcato\Core\admin\modules\webforms\model\Webform;
use Obcato\Core\admin\modules\webforms\WebformItemFactory;
use const Obcato\Core\admin\FRONTEND_TEMPLATE_DIR;

class FormFrontendVisual extends FrontendVisual {

    private WebformItemFactory $webformItemFactory;

    private WebForm $webform;
    private TemplateDao $templateDao;

    public function __construct(Page $page, ?Article $article, WebForm $webform) {
        parent::__construct($page, $article);
        $this->webform = $webform;
        $this->webformItemFactory = WebformItemFactory::getInstance();
        $this->templateDao = TemplateDaoMysql::getInstance();
    }

    public function getTemplateFilename(): string {
        return FRONTEND_TEMPLATE_DIR . '/sa_form.tpl';
    }

    public function loadVisual(?array &$data): void {
        $this->assign('webform_id', $this->webform->getId());
        if ($this->webform->getIncludeCaptcha()) {
            $captchaKey = $this->webform->getCaptchaKey();
            $this->assign('captcha_key', $captchaKey);
        }
        $this->assign('title', $this->webform->getTitle());

        $webformChildData = $this->createChildData();
        $webformData = $this->renderWebForm($this->webform);
        $webformChildData->assign('webform', $webformData);
        $this->assign('form_html', $this->getTemplateEngine()->fetch(FRONTEND_TEMPLATE_DIR . "/" . $this->templateDao->getTemplateFile($this->webform->getTemplate()->getTemplateFileId())->getFileName(), $webformChildData));
    }

    public function getPresentable(): ?Presentable {
        return $this->webform;
    }

    private function renderWebForm(WebForm $webform): array {
        $webformData = array();
        $webformData['title'] = $webform->getTitle();
        $webformData['fields'] = $this->renderFields($webform);
        $webformData['is_submitted'] = FormStatus::getSubmittedForm() == $this->webform->getId();
        return $webformData;
    }

    private function renderFields(WebForm $webform): array {
        $fields = array();
        foreach ($webform->getFormFields() as $formField) {
            $field = $this->webformItemFactory->getFrontendVisualFor($webform, $formField, $this->getPage(), $this->getArticle());
            $fields[] = $field->render();
        }
        return $fields;
    }
}