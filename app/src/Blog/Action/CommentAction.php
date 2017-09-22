<?php

namespace App\Blog\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\Imgs;
use App\Blog\ORM\DataService;
use App\Settings\Settings;
use App\Action\Auth;

final class CommentAction extends DataService {

        private $view;
        private $logger;
        private $comments_path;
        private $user;
        private $userdata;

        public function __construct(Twig $view, LoggerInterface $logger) {
                parent::__construct($view, $logger);
                $this->view = $view;
                $this->logger = $logger;
                $this->comments_path = Settings::COMMENTS_PATH;
                $this->user = new Auth($this->view, $this->logger);
                $this->userdata = $this->user->info();
        }

        /**
         * Show main blog page
         * @param Request $request
         * @param Response $response
         * @param type $args
         * @return Response - Twig
         */
        public function __invoke(Request $request, Response $response, $args) {
                //$this->view->render($response, 'main.twig', array());
                //return $response;
        }

        public function Save(Request $request, Response $response, $args) {
                $params = $request->getParsedBody();
                if (!$params) {
                        return $response->withStatus(400, "empty request");
                }
                if (!$this->userdata) {
                        return $response->withStatus(400, "unauthorized");
                }

                $curr_id = null;
                if (isset($params["curr_id"]) && $params["curr_id"]) {
                        $curr_id = $params["curr_id"];
                }

                if (!$curr_id) {
                        $curr_id = uniqid();
                }
                // create main comments dir if it not exists
                $this->create_dir_if_need($this->comments_path);

                $path_to_content = $this->comments_path . $curr_id . ".html";
                // create or update main content file main_content.html
                file_put_contents($path_to_content, $params["txt"]);

                $elem = array(
                    "id" => $curr_id,
                    "user_id" => $this->userdata["id"],
                    "parent_id" => $params["parentId"],
                    "post_id" => $params["postId"],
                    "create_dt" => time(),
                    "modify_dt" => 0,
                    "path" => $path_to_content,
                );

                $list = $this->getCommentData();
                $list[$curr_id] = $elem;

                // save datafile
                $this->saveCommentData($list);

                // return success status
                return $response->withJson(array("result" => 200, "content" => "success"));
        }

        public function Info(Request $request, Response $response, $args) {
                $list = $this->getCommentData();
                if (isset($args["postId"]) && $args["postId"]) {
                        $result = array();
                        foreach ($list as $key => $value) {
                                if ($value["post_id"] === $args["postId"]) {
                                        $result[$key] = $this->_getCommentDataToView($value);
                                }
                        }
                } else {
                        foreach ($list as $key => $value) {
                                $result[$key] = $this->_getCommentDataToView($value);
                        }
                }
                return $response->withJson(array("result" => 200, "content" => $result));
        }

        private function _getCommentDataToView($data = array()) {
                if (!$data) {
                        return $data;
                }
                $data["message"] = file_get_contents($data["path"]);
                $curr_user = $this->user->getUsersData($data["user_id"]);
                $data["user"] = array(
                    "id" => $data["user_id"],
                    "name" => $curr_user ? $curr_user["settings"]["name"] : "",
                    "img" => $curr_user ? $curr_user["settings"]["photo"] : ""
                );
                return $data;
        }

}