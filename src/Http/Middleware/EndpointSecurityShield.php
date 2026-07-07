<?php

namespace CyberSec\Shield\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EndpointSecurityShield
{
    /**
     * Handle an incoming request.
     * Acts as a basic Web Application Firewall (WAF).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('cybershield.waf.enabled', true)) {
            return $next($request);
        }

        // 1. Payload Size Check
        $maxSize = config('cybershield.waf.max_payload_size', 2097152); // 2MB
        if (strlen((string) $request->getContent()) > $maxSize) {
            Log::warning("WAF: Payload too large.", ['ip' => $request->ip()]);
            abort(413, 'Payload Too Large');
        }

        // 2. SQL Injection Inspection (Basic Example)
        if (config('cybershield.waf.block_sqli', true)) {
            $input = json_encode($request->all());
            // Basic regex for common SQLi patterns (UNION SELECT, OR 1=1, etc.)
            // A real WAF uses much more sophisticated lexers.
            if (preg_match('/(union\s+select|or\s+1\s*=\s*1|drop\s+table)/i', $input)) {
                Log::alert("WAF: SQL Injection attempt detected.", [
                    'ip' => $request->ip(),
                    'payload' => $input
                ]);
                abort(403, 'Forbidden: Malicious payload detected.');
            }
        }

        // 3. XSS Inspection (Basic Example)
        if (config('cybershield.waf.block_xss', true)) {
            $input = json_encode($request->all());
            if (preg_match('/(<script>|javascript:|onerror=)/i', $input)) {
                Log::alert("WAF: XSS attempt detected.", [
                    'ip' => $request->ip(),
                    'payload' => $input
                ]);
                abort(403, 'Forbidden: Malicious payload detected.');
            }
        }

        return $next($request);
    }
}
