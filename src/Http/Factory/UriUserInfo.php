<?php

namespace Dhl\Sdk\Paket\Retoure\Http\Factory;

/**
 * UriUserInfo class file.
 *
 * This class represents only the user info part of the url.
 *
 * @author Anastaszor
 */
class UriUserInfo {

    /**
     * The user name of the authority.
     *
     * @var ?string
     */
    protected $_username;

    /**
     * The user password of the authority.
     *
     * @var ?string
     */
    protected $_password;

    /**
     * Builds a new UriAuthority with its inner data.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username = null, $password = null)
    {
        if(!empty($username))
        {
            $this->_username = \str_replace('+', ' ', \rawurldecode($username));

            if(!empty($password))
                $password = \str_replace('+', ' ', \rawurldecode($password));
            $this->_password = $password;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Stringable::__toString()
     */
    public function __toString()
    {
        if(empty($this->_username))
            return '';

        $str = \rawurlencode($this->_username);
        if(!empty($this->_password))
            $str .= ':'.\rawurlencode($this->_password);

        return $str;
    }

    /**
     * Gets the username of the user info.
     *
     * @return ?string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Gets the password of the user info.
     *
     * @return ?string
     */
    public function getPassword()
    {
        return $this->_password;
    }

}
