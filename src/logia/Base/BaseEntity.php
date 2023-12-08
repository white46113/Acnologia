<?php

namespace logia\Base;

use logia\Base\Exception\BaseInvalidArgumentException;
use logia\Utility\Sanitizer;

class BaseEntity
{

    /**
     * BaseEntity constructor.
     * Assign the key which is now a property of this object to its array value
     * 
     * @param array $dirtyData
     * @return void
     * @throws BaseInvalidArgumentException
     */
    public function __construct( $dirtyData)
    {
        if (empty($dirtyData)) {
            throw new BaseInvalidArgumentException('No data was submitted');
        }
        if (is_array($dirtyData)) {
            foreach ($this->cleanData($dirtyData) as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return the sanitize post data back to the main constructor
     * 
     * @param array $dirtyData
     * @return array
     * @throws BaseInvalidArgumentException
     */
    private function cleanData( $dirtyData) 
    {
        $cleanData = Sanitizer::clean($dirtyData);
        if ($cleanData) {
            return $cleanData;
        }
    }

}