<?php

namespace App\Settings;

final class Settings {

    // ---------------------------------------------------------------------
    // datafile name consts
    //----------------------------------------------------------------------
    const ISSUES = "issues";
    const ANSWERS = "answers";
    // ---------------------------------------------------------------------
    // session cookie settings
    // ---------------------------------------------------------------------
    const SESSIONCOOKIE = '_quizuc';
    const SESSION_COOKIE_LIFETIME = 86400;
    //----------------------------------------------------------------------
    // IMGs files path
    //----------------------------------------------------------------------
    const IMGS_PATH = __DIR__ . "/../../../public/userimgs/";
    const IMGS_USERPATH = "/userimgs/";
    //----------------------------------------------------------------------
    // Posts files path
    //----------------------------------------------------------------------
    const POSTS_PATH = __DIR__ . "/../../../public/posts/";
    const POSTS_USERPATH = "/posts/";
    //----------------------------------------------------------------------
    // Comments files path
    //----------------------------------------------------------------------
    const COMMENTS_PATH = __DIR__ . "/../../../public/comments/";
    const COMMENTS_USERPATH = "/comments/";

    public function __construct() {
        
    }

    /**
     * @return string path to data files
     */
    public static function dataPath() {
        return __DIR__ . "/../Data/";
    }

}
