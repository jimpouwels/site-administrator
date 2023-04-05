<?php

    defined('_ACCESS') or die;

    require_once CMS_ROOT . "modules/articles/visuals/articles/article_editor.php";
    require_once CMS_ROOT . "modules/articles/visuals/articles/articles_list.php";
    require_once CMS_ROOT . "modules/articles/visuals/articles/articles_search.php";

    class ArticleTab extends Visual {

        private static $TEMPLATE = "articles/articles/root.tpl";

        private $_template_engine;
        private $_current_article;
        private $_article_request_handler;

        public function __construct($article_request_handler) {
            $this->_article_request_handler = $article_request_handler;
            $this->_current_article = $article_request_handler->getCurrentArticle();
            $this->_template_engine = TemplateEngine::getInstance();
        }

        public function render(): string {
            $this->_template_engine->assign("search", $this->renderArticlesSearchPanel());
            if (!is_null($this->_current_article))
                $this->_template_engine->assign("editor", $this->renderArticleEditor());
            else
                $this->_template_engine->assign("list", $this->renderArticlesList());

            return $this->_template_engine->fetch("modules/" . self::$TEMPLATE);
        }

        private function renderArticlesSearchPanel() {
            $articles_search_field = new ArticlesSearch($this->_article_request_handler);
            return $articles_search_field->render();
        }

        private function renderArticlesList() {
            $articles_list = new ArticlesList($this->_article_request_handler);
            return $articles_list->render();
        }

        private function renderArticleEditor() {
            $article_editor = new ArticleEditor($this->_current_article);
            return $article_editor->render();
        }

    }

?>
