<?php

namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\Imgs;

final class AdminAction {
        private $view;
        private $logger;

        public function __construct(Twig $view, LoggerInterface $logger) {
                $this->view = $view;
                $this->logger = $logger;
        }

        public function __invoke(Request $request, Response $response, $args) {
                $this->view->render($response, 'admin/main.twig', array(
                ));
                return $response;
        }

}