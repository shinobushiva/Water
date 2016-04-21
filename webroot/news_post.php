<?php
require_once("../bootstrap.php");

digestAuth: {
    require_once(AUTH_DIR . '/DigestAuthUser.php');
    require_once(AUTH_DIR . '/DigestAuthResult.php');
    require_once(AUTH_DIR . '/DigestAuth.php');
    require_once(AUTH_DIR . '/DigestAuthFactory.php');
    $authFactory = new \Mizuderu\Auth\DigestAuthFactory();
    $auth = $authFactory->createFromEnv();
    $result = $auth->handle(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

    if ($result->isUnauthenticated()) {
        $auth->sendUnauthenticatedResponse(new \GuzzleHttp\Psr7\Response());
        exit();
    }

    if ($result->isFailure()) {
        $auth->sendUnauthenticatedResponse(new \GuzzleHttp\Psr7\Response());
        exit();
    }

    $user = $result->getUser();
}


if( isset( $_POST['submit'] ) ){

    $title = $_POST['title'];
    $url = $_POST['url'];

    if( IsUrl($url) ) {

        error_log("Post:".$title.",".$url);

        $err = '不正な値が入力された可能性があります．投稿に失敗しました．';

        if( $title != '' && $url != ''){

            $sql = "INSERT INTO news SET title = :title, url = :url";
            $params = ["title"    => $title ,
                "url"  => $url ,
            ];

            DB::conn()->query($sql , $params);
            header('Location: index.php');

        }
    }
    echo $err .PHP_EOL;
}

$template = Template::factory();
echo $template->render('news_post.html', array(
    'php_self' => $_SERVER['PHP_SELF'],
    'user' => $user,
));
