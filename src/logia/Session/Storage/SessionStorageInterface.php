<?php
namespace logia\Session\Storage;

interface SessionStorageInterface
{

  
    public function setSessionName( $sessionName);

   
    public function getSessionName() ;

    
    public function setSessionID( $sessionID);

   
    public function getSessionID();

    
    public function setSession( $key, $value) ;

    
    public function setArraySession( $key, $value) ;

   
    public function getSession( $key, $default = null);

   
    public function deleteSession( $key) ;

    
    public function invalidate() ;

   
    public function flush( $key, $default = null);

    
    public function hasSession( $key);



}