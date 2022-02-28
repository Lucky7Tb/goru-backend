<?php

namespace App\Exceptions;

use Exception;

class ForbiddenException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->json([
            "status" => 403,
            "message" => $this->getMessage(),
        ], 403);
    }
}
