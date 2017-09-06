<?php

namespace App\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\Imgs;
use App\Action\Auth;
use App\ORM\DataService;

final class UserAction extends DataService {

        private $view;
        private $logger;
        private $path;
        private $user;

        public function __construct(Twig $view, LoggerInterface $logger) {
                parent::__construct($view, $logger);
                $this->view = $view;
                $this->logger = $logger;
                $this->path = __DIR__ . "/../../../public/css/photo/";
                $this->user = new Auth($this->view, $this->logger);
        }

        public function __invoke(Request $request, Response $response, $args) {
                $this->view->render($response, 'main.twig', array());
                return $response;
        }

        public function LoginCheck(Request $request, Response $response, $args) {
                $params = $request->getParsedBody();

                $userdata = $this->user->login($params['login'], $params['pass']);

                if ($userdata) {
                        return $response->withJson(array("result" => 200, "content" => array("type" => "success", "sid" => $userdata["session_id"])));
                } else {
                        return $response->withJson(array("result" => 400, "content" => "unknown"));
                }
        }

        public function RegClient(Request $request, Response $response, $args) {
                $params = $request->getParsedBody();

                $userdata = $this->user->reg($params['email'], $params['pass'], $params['name']);

                if ($userdata) {
                        if (isset($userdata["error"]) && $userdata["error"] !== "") {
                                return $response->withJson(array("result" => 400, "content" => array("type" => "error", "message" => $userdata["error"])));
                        }
                        return $response->withJson(array("result" => 200, "content" => array("type" => "success", "sid" => $userdata["session_id"])));
                }
                return $response->withJson(array("result" => 400, "content" => "unknown"));
        }
        
        //----------------------------------------------------------------------
        // admin methods
        //----------------------------------------------------------------------
        
        public function AdminView(Request $request, Response $response, $args) {
                $data = $this->getUsersData();
                $this->view->render($response, 'admin/users/main.twig', array(
                    "categories"=>$data
                ));
                return $response;
        }
        
        public function AdminForm(Request $request, Response $response, $args) {
                if (isset($args["curr_id"]) && $args["curr_id"] !== ""){
                        $data = $this->getUsersData($args["curr_id"]);
                }
                else {
                        $data = array();
                }
                $this->view->render($response, 'admin/users/form.twig', array(
                    "data"=>$data
                ));
                return $response;
        }
        
        public function AdminSave(Request $request, Response $response, $args) {
                $params = $request->getParsedBody();
                if (!$params){
                        return $response->withStatus(400, "empty request");
                }
                
                $curr_id = null;
                if (isset($params["curr_id"]) && $params["curr_id"]){
                        $curr_id = $params["curr_id"];
                }
                
                if (!$curr_id){
                        $curr_id = uniqid();
                }
                $elem = array(
                    "id" => $curr_id,
                    "name" => $params["name"],
                    "descr" => $params["descr"],
                );
                
                $list = $this->getCategoryData();
                $list[$curr_id] = $elem;
                
                // save datafile
                $this->saveCategoryData($list);
                
                // return rendered data
                return $this->AdminView($request, $response, $args);
        }
        
        public function AdminDrop(Request $request, Response $response, $args) {
                if (!(isset($args["curr_id"]) && $args["curr_id"] !== "")){
                        return $response->withStatus(400, "empty request");
                }
                
                $curr_id = $args["curr_id"];
                
                $list = $this->getCategoryData();
                if (isset($list[$curr_id])){
                        // remove current data
                        unset($list[$curr_id]);
                }
                
                // save datafile
                $this->saveCategoryData($list);
                
                // return rendered data
                return $this->AdminView($request, $response, $args);
        }

}
