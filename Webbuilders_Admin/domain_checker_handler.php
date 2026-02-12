<?php
// Enable error reporting but don't display HTML errors
error_reporting(E_ALL);
ini_set('display_errors', '0');
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input data
    $input = file_get_contents('php://input');
    
    $data = json_decode($input, true);
    $domain = isset($data['domain']) ? trim(strtolower($data['domain'])) : '';
    
    if (empty($domain)) {
        echo json_encode(['success' => false, 'message' => 'Domain name is required']);
        exit;
    }
    
    // Validate domain format
    if (!preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,}$/', $domain)) {
        echo json_encode(['success' => false, 'message' => 'Invalid domain format']);
        exit;
    }
    
        
    // Method 1: Try multiple WHOIS APIs (free tiers)
    $available = checkDomainAvailability($domain);
    
    echo json_encode([
        'success' => true,
        'available' => $available,
        'domain' => $domain,
        'checked_at' => date('Y-m-d H:i:s')
    ]);
    exit;
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

function checkDomainAvailability($domain) {
    // Try multiple methods to check domain availability
    
    // Method 1: Simple DNS check (fastest)
    if (dnsCheck($domain)) {
        return false; // DNS record exists = domain is taken
    }
    
    // Method 2: WHOIS check using whoisapi.com (free tier)
    $result1 = checkWithWhoisAPI($domain);
    if ($result1 !== null) {
        return $result1;
    }
    
    // Method 3: Alternative WHOIS check
    $result2 = checkWithAlternativeAPI($domain);
    if ($result2 !== null) {
        return $result2;
    }
    
    // If all methods fail, assume domain is available (conservative approach)
    return true;
}

function dnsCheck($domain) {
    // Check if domain has DNS A records
    $records = dns_get_record($domain, DNS_A);
    return !empty($records);
}

function checkWithWhoisAPI($domain) {
    try {
        // Free WHOIS API (500 requests/month free)
        $api_key = 'at_9vFpqYZ5Pl4kXvKQqvJVQqCpX7bFr'; // Your existing key
        
        $url = "https://www.whoisxmlapi.com/whoisserver/WhoisService";
        $params = [
            'apiKey' => $api_key,
            'domainName' => $domain,
            'outputFormat' => 'JSON'
        ];
        
        $full_url = $url . '?' . http_build_query($params);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'WEBbuilders.lk Domain Checker/1.0');
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            return null;
        }
        
        curl_close($ch);
        
        
        if ($response === false || $http_code !== 200) {
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['WhoisRecord']['domainName'])) {
            // Domain exists in WHOIS record
            return false;
        }
        
        if (isset($data['ErrorMessage']['errorCode'])) {
            // Error from API
            return null;
        }
        
        // No domain name in response - likely available
        return true;
        
    } catch (Exception $e) {
        return null;
    }
}

function checkWithAlternativeAPI($domain) {
    try {
        // Alternative free WHOIS check using JSONWHOIS API
        $url = "https://jsonwhois.com/api/v1/whois";
        
        $headers = [
            'Accept: application/json',
            'Authorization: Token token=YOUR_API_KEY_HERE' // You can sign up for free at jsonwhois.com
        ];
        
        $params = ['domain' => $domain];
        $full_url = $url . '?' . http_build_query($params);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200 && $response) {
            $data = json_decode($response, true);
            
            // Check if domain is registered
            if (isset($data['registered']) && $data['registered'] === true) {
                return false;
            }
            
            if (isset($data['available']) && $data['available'] === true) {
                return true;
            }
        }
        
        return null;
        
    } catch (Exception $e) {
        return null;
    }
}

function checkWithLocalWhois($domain) {
    // Method 4: Local WHOIS command (requires whois to be installed on server)
    // This is a fallback method if server has whois command available
    
    if (function_exists('shell_exec')) {
        $command = "whois " . escapeshellarg($domain) . " 2>&1";
        $output = shell_exec($command);
        
        if ($output) {
            // Check common patterns in WHOIS output
            $patterns = [
                '/No match for/i',
                '/NOT FOUND/i',
                '/Domain not found/i',
                '/No Data Found/i',
                '/The queried object does not exist/i'
            ];
            
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $output)) {
                    return true; // Domain is available
                }
            }
            
            // If we see registry information, domain is taken
            if (preg_match('/Registrar:/i', $output) || 
                preg_match('/Creation Date:/i', $output) ||
                preg_match('/Updated Date:/i', $output)) {
                return false;
            }
        }
    }
    
    return null;
}
?>