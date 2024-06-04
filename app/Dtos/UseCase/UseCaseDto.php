<?php

declare(strict_types=1);

namespace App\Dtos\UseCase;

use Illuminate\Foundation\Http\FormRequest;

abstract class UseCaseDto
{
    /**
     *  Convert the request into a DTO
     *
     * @author Sakina Maezawa
     *
     * @throws ValidationException
     */
    final public function __construct(FormRequest $request)
    {
        $fromRequest = $request->all(); // get request data

        foreach ($fromRequest as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }
            $this->{$key} = $value; // set the value to the property
        }
    }
}
