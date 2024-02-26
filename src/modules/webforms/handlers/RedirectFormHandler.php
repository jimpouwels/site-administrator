<?php

namespace Obcato\Core\modules\webforms\handlers;

use Obcato\Core\database\dao\PageDao;
use Obcato\Core\database\dao\PageDaoMysql;
use Obcato\Core\modules\articles\model\Article;
use Obcato\Core\modules\pages\model\Page;
use Obcato\Core\modules\webforms\visuals\RedirectFormHandlerEditor;
use Obcato\Core\view\TemplateEngine;
use const Obcato\CMS_ROOT;

class RedirectFormHandler extends FormHandler {

    public static string $TYPE = 'redirect_form_handler';
    private PageDao $_page_dao;

    public function __construct() {
        parent::__construct();
        $this->_page_dao = PageDaoMysql::getInstance();
    }

    public function getRequiredProperties(): array {
        require_once CMS_ROOT . '/modules/webforms/visuals/RedirectFormHandlerEditor.php';
        return array(
            new HandlerProperty('page_id', 'textfield', new RedirectFormHandlerEditor(TemplateEngine::getInstance())),
        );
    }

    public function getNameResourceIdentifier(): string {
        return 'webforms_redirect_form_handler_name';
    }

    public function getType(): string {
        return self::$TYPE;
    }

    public function handle(array $fields, Page $page, ?Article $article): void {
        $page_id = $this->getProperty('page_id');
        if ($page_id) {
            $page = $this->_page_dao->getPage($page_id);
            if ($page) {
                $this->redirectTo($this->getPageUrl($page));
            }
        }
    }
}