<?php

namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\ORM\DataService;

class AdminAction {

	private $view;
	private $logger;
	private $data;

	public function __construct(Twig $view, LoggerInterface $logger) {
		$this->view = $view;
		$this->logger = $logger;
		$this->data = new DataService($this->view, $this->logger);
	}

	public function __invoke(Request $request, Response $response, $args) {
		$this->view->render($response, 'admin/main.twig', array());
		return $response;
	}

}
