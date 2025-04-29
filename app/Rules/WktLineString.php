<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use InvalidArgumentException; // Assuming fromWkt throws this on format errors
use Illuminate\Support\Facades\Log; // <-- Import the Log facade

class WktLineString implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * Checks if the value is a valid Well-Known Text (WKT) representation of a LineString.
     * Allows null values if the 'nullable' rule is also present on the attribute.
     *
     * @param  string  $attribute The name of the attribute being validated.
     * @param  mixed  $value The value of the attribute being validated.
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The callback to signal validation failure.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If the main validation rules include 'nullable', a null value is acceptable here.
        if ($value === null) {
            return;
        }

        // WKT must be a string.
        if (!is_string($value)) {
             $fail("The :attribute must be a string.");
             return;
        }

        // An empty string is not a valid LineString WKT.
        if (trim($value) === '') {
            $fail("The :attribute cannot be an empty string if provided.");
            return;
        }

        try {
            // Attempt to parse the WKT string using the spatial library.
            // This is the core check. It will throw an exception if the format is invalid
            // or if the geometry type is not a LineString.
            LineString::fromWkt($value);
        } catch (InvalidArgumentException $e) {
            // Catch the specific exception thrown for invalid WKT format or type.
            $fail("The :attribute must be a valid WKT LineString. Error: " . $e->getMessage());
        } catch (\Throwable $e) {
             // Catch any other unexpected errors during parsing as a fallback.
             Log::error("Unexpected error validating WKT LineString for attribute {$attribute}: " . $e->getMessage()); // Now Log:: should work
             $fail("An unexpected error occurred while validating the :attribute as WKT LineString.");
        }
    }
}