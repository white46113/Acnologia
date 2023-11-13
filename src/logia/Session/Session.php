<?php

namespace logia\Session;

use logia\Base\Exception\BaseException;
use logia\Base\Exception\BaseInvalidArgumentException;
use logia\Session\SessionInterface;
use logia\Session\Storage\SessionStorageInterface;
use Throwable;

class Session implements SessionInterface
{

    /** @var SessionStorageInterface */
    protected  $storage;

    /** @var string */
    protected  $sessionName;

    /** @var const */
    const SESSION_PATTERN = "/^[a-zA-Z0-9_\.]{1,64}$/";

    /**
     * Class constructor
     *
     * @param string $sessionName
     * @param SessionStorageInterface $storage
     * @throws BaseInvalidArgumentException
     */
    public function __construct(string $sessionName, SessionStorageInterface $storage = null)
    {
        if ($this->isSessionKeyValid($sessionName) === false) {
            throw new BaseInvalidArgumentException($sessionName . ' is not a valid session name');
        }
        
        $this->sessionName = $sessionName;
        $this->storage = $storage;
    }

    /**
     * @inheritdoc
     *
     * @param string $key
     * @param mixed $value
     * @return void
     * @throws BaseException
     */
    public function set( $key, $value) 
    {
        $this->ensureSessionKeyIsValid($key);
        try{
            $this->storage->SetSession($key, $value);
        } catch(Throwable $throwable) {
            throw new BaseException('An exception was thrown in retrieving the key from the session storage. ' . $throwable);
        }

    }

    /**
     * @inheritdoc
     *
     * @param string $key
     * @param mixed $value
     * @return void
     * @throws BaseException
     */
    public function setArray( $key, $value)
    {
        $this->ensureSessionKeyIsValid($key);
        try{
            $this->storage->setArraySession($key, $value);
        }catch(Throwable $throwable) {
            throw new BaseException('An exception was thrown in retrieving the key from the session storage. ' . $throwable);
        }

    }

    /**
     * @inheritdoc
     *
     * @param string $key
     * @param mixed $default
     * @return void
     * @throws BaseException
     */
    public function get( $key, $default = null) 
    {
        try{
            return $this->storage->getSession($key, $default);
        } catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritdoc
     *
     * @param string $key
     * @return boolean
     * @throws BaseException
     */
    public function delete( $key) 
    {
        $this->ensureSessionKeyIsValid($key);
        try{
            $this->storage->deleteSession($key);
        }catch(Throwable $throwable) {
            throw $throwable;
        }
        return true;
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function invalidate() 
    {
        $this->storage->invalidate();
    }

    /**
     * @inheritdoc
     *
     * @param string $key
     * @param [type] $value
     * @return void
     * @throws BaseException
     */
    public function flush( $key, $value = null)
    {
        $this->ensureSessionKeyIsValid($key);
        try{
            $this->storage->flush($key, $value);
        }catch(Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * @inheritdoc
     *
     * @param string $key
     * @return booleanSessionInterface
     * @throws SessiopnInvalidArgumentException
     */
    public function has( $key)
    {
        $this->ensureSessionKeyIsValid($key);
        return $this->storage->hasSession($key);
    }

    /**
     * Checks whether our session key is valid according the defined regular expression
     *
     * @param string $key
     * @return boolean
     */
    protected function isSessionKeyValid( $key)
    {
        return (preg_match(self::SESSION_PATTERN, $key) === 1);
    }

    /**
     * Checks whether we have session key 
     *
     * @param string $key
     * @return void
     */
    protected function ensureSessionKeyIsvalid( $key)
    {
        if ($this->isSessionKeyValid($key) === false) {
            throw new BaseInvalidArgumentException($key . ' is not a valid session key');
        }
    }
}