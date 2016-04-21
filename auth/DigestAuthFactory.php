<?php namespace Mizuderu\Auth;

/**
 * Class DigestAuthFactory
 */
class DigestAuthFactory
{
    /**
     * @return DigestAuth
     */
    public function createFromEnv()
    {
        $users = [
            new DigestAuthUser(getenv('DIGEST_AUTH_USER'), getenv('DIGEST_AUTH_PASSWORD'))
        ];

        return new DigestAuth($users, getenv('DIGEST_AUTH_REALM'));
    }
}
