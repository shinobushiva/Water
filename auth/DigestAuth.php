<?php namespace Mizuderu\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class DigestAuth
 */
class DigestAuth
{
    const PARAM_KEY = 'PHP_AUTH_DIGEST';

    /**
     * @var DigestAuthUser[]
     */
    private $users;

    /**
     * @var string
     */
    private $realm;

    /**
     * DigestAuth constructor.
     * @param DigestAuthUser[] $users
     * @param string $realm
     */
    public function __construct(array $users = [], $realm = 'Restricted Area')
    {
        foreach ($users as $user) {
            $this->addUser($user);
        }
        $this->realm = $realm;
    }

    /**
     * @param DigestAuthUser $user
     */
    private function addUser(DigestAuthUser $user)
    {
        $this->users[$user->getName()] = $user;
    }


    /**
     * @param ServerRequestInterface $request
     * @return DigestAuthResult
     */
    public function handle(ServerRequestInterface $request)
    {
        $params = $request->getServerParams();


        // 未認証
        if (!array_key_exists(self::PARAM_KEY, $params) || empty($params[self::PARAM_KEY])) {
            return new DigestAuthResult(DigestAuthResult::UNAUTHENTICATED);
        }

        // PHP_AUTH_DIGESTをパース
        $data = $this->parseHttpDigest($params[self::PARAM_KEY]);
        if ($data === false) {
            return new DigestAuthResult(DigestAuthResult::FAILURE, null, '認証に失敗しました');
        }

        // ユーザー存在チェック
        if (!array_key_exists($data['username'], $this->users)) {
            return new DigestAuthResult(DigestAuthResult::FAILURE, null, '認証に失敗しました');
        }


        $user = $this->users[$data['username']];
        $password = $user->getPassword();
        $method = $request->getMethod();

        $validResponse = $this->createValidResponse($data, $method, $password);

        // 正当性チェック
        if ($data['response'] !== $validResponse) {
            return new DigestAuthResult(DigestAuthResult::FAILURE, null, '認証に失敗しました');
        }

        return new DigestAuthResult(DigestAuthResult::SUCCESS, $user);
    }


    /**
     * 未認証時のレスポンスを送信
     * @param ResponseInterface $response
     * @return void
     */
    public function sendUnauthenticatedResponse(ResponseInterface $response)
    {
        $response = $this->makeUnauthenticatedResponse($response);
        $version = $response->getProtocolVersion();
        $status = $response->getStatusCode();
        $phrase = $response->getReasonPhrase();
        header("HTTP/{$version} {$status} {$phrase}");

        foreach ($response->getHeaders() as $name => $values) {
            $name = str_replace('-', ' ', $name);
            $name = ucwords($name);
            $name = str_replace(' ', '-', $name);
            foreach ($values as $value) {
                header("{$name}: {$value}");
            }
        }

        $stream = $response->getBody();
        $stream->rewind();
        while (!$stream->eof()) {
            echo $stream->read(8192);
        }
    }

    /**
     * @param array $data
     * @param string $method
     * @param string $password
     * @return string
     */
    private function createValidResponse(array $data, $method, $password)
    {
        $a1 = md5($data['username'] . ':' . $this->realm . ':' . $password);
        $a2 = md5($method . ':' . $data['uri']);
        return md5($a1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $a2);
    }

    /**
     * 未認証時のレスポンスを作成
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function makeUnauthenticatedResponse(ResponseInterface $response)
    {
        $nonce = uniqid();
        $opaque = md5($this->realm);
        $response->getBody()->write('認証をキャンセルしました');
        return $response
            ->withStatus(401)
            ->withHeader(
                'WWW-Authenticate',
                "Digest realm=\"{$this->realm}\",qop=\"auth\",nonce=\"{$nonce}\",opaque=\"{$opaque}\""
            );
    }

    /**
     * 文字列をパース
     * @param string $text
     * @return array|bool
     */
    private function parseHttpDigest($text)
    {
        $neededParts = ['nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1];
        $data = [];
        $keys = implode('|', array_keys($neededParts));

        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($neededParts[$m[1]]);
        }

        return $neededParts ? false : $data;
    }
}
