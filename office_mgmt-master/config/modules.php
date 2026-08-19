<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feature Flags for Modules
    |--------------------------------------------------------------------------
    | Enable or disable specific modules using environment variables.
    | Example: LOAN_INQUIRY_MODULE=true
    */

    'loan_inquiry' => env('LOAN_INQUIRY_MODULE', false),

];
