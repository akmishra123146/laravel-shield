<?php

namespace CyberSec\Shield\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ThreatIntelligenceService
{
    /**
     * Check if an IP address is malicious according to AbuseIPDB.
     * Results are cached to prevent API rate limiting.
     *
     * @param string $ip
     * @return bool
     */
    public function isIpMalicious(string $ip): bool
    {
        $apiKey = config('cybershield.threat_intel.abuseipdb_api_key');

        if (!$apiKey) {
            return false;
        }

        // Cache the result for 24 hours to save API calls
        return Cache::remember("cybershield.threat_intel.ip.{$ip}", now()->addHours(24), function () use ($ip, $apiKey) {
            try {
                $response = Http::withHeaders([
                    'Key' => $apiKey,
                    'Accept' => 'application/json',
                ])->get('https://api.abuseipdb.com/api/v2/check', [
                    'ipAddress' => $ip,
                    'maxAgeInDays' => 90
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    // Consider it malicious if abuse confidence score is > 75
                    return ($data['data']['abuseConfidenceScore'] ?? 0) > 75;
                }
            } catch (\Exception $e) {
                // Log exception in a real application
            }

            return false;
        });
    }
}
