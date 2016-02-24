<?php

namespace KinetiseSkeleton\Response;

use Symfony\Component\HttpFoundation\Response;

class MessageResponse extends Response
{
    public function __construct($title = null, $message = null, $status = 200)
    {
        parent::__construct(
            json_encode(
                array(
                    "message" => array(
                        "title"       => $title,
                        "description" => $message
                    )
                )
            ),
            $status,
            array(
                'Content-Type' => 'application/json, charset=UTF-8'
            )
        );
    }
}