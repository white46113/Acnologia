<?php


namespace logia\Flash;

interface FlashInterface
{

    /**
     * Add a flash message stored within the session
     *
     * @param string $message
     * @param string $type
     * @return void
     */
    public static function add( $message,  $type = FlashTypes::SUCCESS);

    /**
     * Get all message within the session
     *
     * @return void
     */
    public static function get();

}