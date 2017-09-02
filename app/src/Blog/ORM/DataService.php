<?php

namespace App\Blog\ORM;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Settings\Settings;
use \App\ORM\BaseDataService;

class DataService extends BaseDataService {

        private $view;
        private $logger;

        public function __construct(Twig $view, LoggerInterface $logger) {
		parent::__construct($view, $logger);
                $this->view = $view;
                $this->logger = $logger;
        }
	
        /**
         * @return array full main data array
         */
        public function getMainData() {
                return $this->__getData(Settings::MAIN, 0);
        }

        /**
         * @return array - if isset $id - current elem array. else - all elems array
         * @param integer $id - id elem in datafile
         */
        public function getPostsData($id = 0) {
                return $this->__getData(Settings::POSTS, $id);
        }

        /**
         * @return array - if isset $id - current elem array. else - all elems array
         * @param integer $id - id elem in datafile
         */
        public function getCommentData($id = 0) {
                return $this->__getData(Settings::COMMENTS, $id);
        }

        /**
         * @return array - if isset $id - current elem array. else - all elems array
         * @param integer $id - id elem in datafile
         */
        public function getAuthorData($id = 0) {
                return $this->__getData(Settings::AUTHORS, $id);
        }

        /**
         * @return array - if isset $id - current elem array. else - all elems array
         * @param integer $id - id elem in datafile
         */
        public function getCategoryData($id = 0) {
                return $this->__getData(Settings::CATEGORY, $id);
        }
        
        public function saveCategoryData($data) {
                return $this->__saveDatafile(Settings::CATEGORY, $data);
        }

}
