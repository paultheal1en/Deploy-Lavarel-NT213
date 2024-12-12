public function handle(Request $request, Closure $next)
{
    $response = $next($request);

    // CSP cơ bản
    $csp = "default-src 'self';"; 
    $csp .= "script-src 'self';"; 
    $csp .= "style-src 'self';";  
    $csp .= "img-src 'self';";

    $response->headers->set('Content-Security-Policy', $csp);

    return $response;
}
