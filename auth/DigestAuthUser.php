<?php namespace Mizuderu\Auth;

/**
 * Class DigestAuthUser
 */
class DigestAuthUser
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $password;

    /**
     * コンストラクタ
     * @param string $name
     * @param string $password
     */
    public function __construct($name, $password)
    {
        $this->name = $name;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
