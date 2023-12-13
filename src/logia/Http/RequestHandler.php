<?php
namespace logia\Http;

use Symfony\Component\HttpFoundation\Request;

class RequestHandler
{

    /**
     * Wrapper method for symfony http request object
     *
     * @return Request
     */
    public function handler()
    {
        if (!isset($request)) {
            $request = new Request();
            if ($request) {
                $create = $request->createFromGlobals();
                if ($create) {
                    return $create;
                }
            }
        }
        return false;
    }

}