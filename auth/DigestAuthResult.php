<?php namespace Mizuderu\Auth;

/**
 * Class DigestAuthResult
 */
class DigestAuthResult
{
    const SUCCESS = 'success';

    const FAILURE = 'failure';

    const UNAUTHENTICATED = 'unauthenticated';

    /**
     * @var array
     */
    private static $statuses = [
        self::SUCCESS => '成功',
        self::FAILURE => '失敗',
        self::UNAUTHENTICATED => '未認証',
    ];

    /**
     * @var string
     */
    private $status;

    /**
     * @var DigestAuthUser
     */
    private $user;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * DigestAuthResult constructor.
     * @param string $status
     * @param DigestAuthUser $user
     * @param string $errorMessage
     */
    public function __construct($status, DigestAuthUser $user = null, $errorMessage = '')
    {
        if (!array_key_exists($status, self::$statuses)) {
            throw new \InvalidArgumentException('結果の種別の指定が不正です');
        }

        $this->status = $status;
        $this->user = $user;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return bool
     */
    public function isUnauthenticated()
    {
        return $this->status === self::UNAUTHENTICATED;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status === self::SUCCESS;
    }

    /**
     * @return bool
     */
    public function isFailure()
    {
        return $this->status === self::FAILURE;
    }

    /**
     * @return DigestAuthUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
