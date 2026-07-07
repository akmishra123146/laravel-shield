<?php

namespace CyberSec\Shield\Traits;

use Illuminate\Http\Request;

trait HasAdvancedAccess
{
    /**
     * Determine if the user's current context requires Multi-Factor Authentication.
     * This is a core concept of Zero Trust (Attribute-Based Access Control).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function requiresMfaForCurrentContext(Request $request): bool
    {
        // In a real application, you'd check if this device is recognized
        // or if the IP is from a new location (e.g., GeoIP).
        // For demonstration, we'll return false, but provide the structure.

        $currentIp = $request->ip();
        $userAgent = $request->userAgent();

        // Example: if ($this->isNewDevice($userAgent, $currentIp)) return true;

        return false;
    }

    /**
     * Get the clearance level of the user for Data Access Control.
     *
     * @return int
     */
    public function getSecurityClearanceLevel(): int
    {
        // Override this in your User model to integrate with your roles/permissions
        return 1;
    }

    /**
     * Check if the user has a specific security clearance.
     *
     * @param int $level
     * @return bool
     */
    public function hasClearance(int $level): bool
    {
        return $this->getSecurityClearanceLevel() >= $level;
    }
}
