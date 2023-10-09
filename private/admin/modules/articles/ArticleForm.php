<?php
require_once CMS_ROOT . "/core/form/Form.php";
require_once CMS_ROOT . "/core/form/FormException.php";
require_once CMS_ROOT . "/database/dao/ArticleDaoMysql.php";
require_once CMS_ROOT . "/utilities/DateUtility.php";

class ArticleForm extends Form {

    private Article $article;
    private array $selectedTerm;
    private ArticleDao $articleDao;

    public function __construct(Article $article) {
        $this->article = $article;
        $this->articleDao = ArticleDaoMysql::getInstance();
    }

    public function loadFields(): void {
        $this->article->setTitle($this->getMandatoryFieldValue("article_title"));
        $this->article->setTemplateId($this->getNumber('template'));
        $this->article->setKeywords($this->getFieldValue('keywords'));
        $this->article->setDescription($this->getFieldValue("article_description"));
        $this->article->setPublished($this->getCheckboxValue("article_published"));
        $this->article->setImageId($this->getNumber("article_image_ref_" . $this->article->getId()));
        $this->article->setTargetPageId($this->getNumber("article_target_page"));
        $this->article->setParentArticleId($this->getNumber("parent_article_id"));
        $this->article->setCommentWebFormId($this->getNumber("article_comment_webform"));
        $publicationDate = $this->loadPublicationDate();
        $sortDate = $this->loadSortDate();
        $this->deleteLeadImageIfNeeded();
        $this->deleteParentArticleIfNeeded();
        $this->selectedTerm = $this->getSelectValue("select_terms_" . $this->article->getId());
        if ($this->hasErrors()) {
            throw new FormException();
        } else {
            $this->article->setPublicationDate(DateUtility::stringMySqlDate($publicationDate));
            $this->article->setSortDate(DateUtility::stringMySqlDate($sortDate));
        }
    }

    public function getSelectedTerm(): array {
        return $this->selectedTerm;
    }

    public function getTermsToDelete(): array {
        $termsToDelete = array();
        $articleTerms = $this->articleDao->getTermsForArticle($this->article->getId());
        foreach ($articleTerms as $articleTerm) {
            if (!is_null($this->getFieldValue("term_" . $this->article->getId() . "_" . $articleTerm->getId() . "_delete"))) {
                $termsToDelete[] = $articleTerm;
            }
        }
        return $termsToDelete;
    }

    private function deleteLeadImageIfNeeded(): void {
        if ($this->getFieldValue("delete_lead_image_field") == "true") {
            $this->article->setImageId(null);
        }
    }

    private function deleteParentArticleIfNeeded(): void {
        if ($this->getFieldValue("delete_parent_article_field") == "true") {
            $this->article->setParentArticleId(null);
        }
    }

    private function loadPublicationDate(): string {
        return $this->getMandatoryDate("publication_date");
    }

    private function loadSortDate(): string {
        return $this->getMandatoryDate("sort_date");
    }

}