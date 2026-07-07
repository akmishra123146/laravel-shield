<?php

namespace CyberSec\Shield\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PasswordNotBreached implements Rule
{
    protected $threshold = 0; // Number of allowed times the password can be breached

    /**
     * Create a new rule instance.
     *
     * @param int $threshold The number of allowed times the password has appeared in breaches (default 0)
     * @return void
     */
    public function __construct(int $threshold = 0)
    {
        $this->threshold = $threshold;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!config('cybershield.passwords.check_breaches', true)) {
            return true;
        }

        // Use k-Anonymity model for HaveIBeenPwned API
        $sha1 = strtoupper(sha1($value));
        $prefix = substr($sha1, 0, 5);
        $suffix = substr($sha1, 5);

        try {
            $response = Http::get("https://api.pwnedpasswords.com/range/{$prefix}");

            if ($response->successful()) {
                // The response contains lines like `SUFFIX:COUNT`
                $lines = explode("\n", $response->body());
                foreach ($lines as $line) {
                    list($hashSuffix, $count) = explode(':', trim($line)) + [null, 0];
                    
                    if (strtoupper($hashSuffix) === $suffix) {
                        return (int) $count <= $this->threshold;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Password breach check failed: " . $e->getMessage());
            // Fail open or closed depending on strictness. We fail open here to not break registration if API is down.
            return true;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The given :attribute has appeared in a data leak. Please choose a different password.';
    }
}
