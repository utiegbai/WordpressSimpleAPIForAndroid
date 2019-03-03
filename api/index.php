<?php
header("Access-Control-Allow-Origin: *");
require_once('../core/init.php');

$post = new Posts();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['url'])) {
        if ($_GET['url'] == 'posts') {
            $AllPosts = $post->AllPosts('ad_listing');

            echo json_encode($AllPosts);
            http_response_code(200);
        } elseif ($_GET['url'] == 'categories') {
            $getCat = $post->getCat();

            echo json_encode($getCat);
            http_response_code(200);
        } elseif ($_GET['url'] == 'catposts' && $_GET['cat_id'] != "") {
            $getCatPosts = $post->getCatPosts($_GET['cat_id']);

            echo json_encode($getCatPosts);
            http_response_code(200);
        } elseif ($_GET['url'] == 'singlepost' && $_GET['post_id'] != "") {
            $getSinglePost = $post->getSinglePost($_GET['post_id']);

            echo json_encode($getSinglePost);
            http_response_code(200);
        } elseif ($_GET['url'] == 'search' && $_GET['q'] != "") {
            $searchPosts = $post->searchPosts($_GET['q']);

            echo json_encode($searchPosts);
            http_response_code(200);
        } elseif ($_GET['url'] == 'user' && $_GET['action'] == "posts" && $_GET['userid'] != "") {
            $getUserPost = $post->getUserPost($_GET['userid']);

            echo json_encode($getUserPost);
            http_response_code(200);
        } elseif ($_GET['url'] == 'user' && $_GET['action'] == "signup") {
            $registerUser = $post->registerUser($_GET['username'], $_GET['email'], $_GET['password'], $_GET['fullname']);

            echo json_encode($registerUser);
            http_response_code(200);
        } elseif ($_GET['url'] == 'user' && $_GET['action'] == "login") {
            $loginUser = $post->loginUser($_GET['username'], $_GET['password']);

            echo json_encode($loginUser);
            http_response_code(200);
        }
    }
}

?>