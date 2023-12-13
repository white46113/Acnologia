<?php

namespace logia\Http;

use Symfony\Component\HttpFoundation\Response;

class ResponseHandler
{

    /**
     * Wrapper method for symfony http response object
     *
     * @return Response
     */
    public function handler()
    {
        if (!isset($response)) {
            $response = new Response();
            if ($response) {
                return $response;
            }
        }
        return false;
    }

}