<?php

namespace Obcato\Core\admin\request_handlers;

use Obcato\ComponentApi\RequestHandler as IHttpRequestHandler;
use Obcato\Core\admin\authentication\Session;
use Obcato\Core\admin\core\Blackboard;
use Obcato\Core\admin\core\model\Notifications;

abstract class HttpRequestHandler implements IHttpRequestHandler {

    public function handle(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->handleGet();
        }
    }

    protected function sendSuccessMessage(string $message): void {
        Notifications::setSuccessMessage($message);
    }

    protected function sendErrorMessage(string $message): void {
        Notifications::setFailedMessage($message);
    }

    protected function redirectTo(string $url): void {
        header("Location: $url");
        exit();
    }

    protected function getTextResource(string $identifier): string {
        return Session::getInstance()->getTextResource($identifier);
    }

    protected function getBackendBaseUrl(): string {
        return BlackBoard::getInstance()->getBackendBaseUrl();
    }

}