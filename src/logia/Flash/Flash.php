<?php

namespace logia\Flash;

use logia\Flash\FlashInterface;
use logia\GlobalManager\GlobalManager;
use logia\Flash\FlashTypes;

class Flash implements FlashInterface
{

    /** @string */
    protected const FLASH_KEY = 'flash_message';

    public static function add($message,$type = FlashTypes::SUCCESS)
    {
        $session = GlobalManager::get('global_session');
        if (!$session->has(self::FLASH_KEY)) {
            $session->set(self::FLASH_KEY, []);
        }
        $session->setArray(self::FLASH_KEY, ['message' => $message, 'type' => $type]);
    }

    public static function get()
    {
        $session = GlobalManager::get('global_session');
        $session->flush(self::FLASH_KEY);
    }

}