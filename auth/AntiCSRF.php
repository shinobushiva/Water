<?php namespace Mizuderu\Auth;

class AntiCSRF {

    /**
     * @var string
     */
    private $salt;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * .envの設定から作成
     * @return AntiCSRF
     */
    public static function fromEnv()
    {
        return new self(getenv('ANTI_CSRF_SALT'));
    }

    /**
     * AntiCSRF constructor.
     * @param string $salt
     * @param string $algorithm
     */
    public function __construct($salt, $algorithm = 'sha256')
    {
        if(!is_string($salt) || empty($salt)) {
            throw new \InvalidArgumentException('saltは空でない文字列で指定してください。');
        }

        $this->salt = $salt;
        $this->algorithm = $algorithm;
    }

    /**
     * CSRF対策トークンを生成
     * @return string
     */
    public function generateToken() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \BadMethodCallException('セッションがアクティブではありません。');
        }

        return hash($this->algorithm, session_id() . $this->salt);
    }

    /**
     * CSRF対策トークンの妥当性を検証
     * @param string $token
     * @return bool
     */
    public function validate($token)
    {
        return $this->generateToken() === $token;
    }
}
