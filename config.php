<?php
// Your existing database connection
$host = "";
$user = "";
$password = ""; // Replace with your actual password
$db = "";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    // For production, you might want to log this error and show a generic message
    // error_log("Database Connection Failed: " . $conn->connect_error);
    die("Sorry, we're having some technical difficulties (DB). Please try again later."); // Added (DB) for clarity
}

// Define Base URL for easier asset linking and redirects
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host_name = $_SERVER['HTTP_HOST'];
    // $script_dir = dirname($_SERVER['SCRIPT_NAME']); // Be careful with dirname if config.php is in a subdirectory
    // For robust BASE_URL, consider if config.php is in root or a sub-folder.
    // If config.php is in the project root that is web-accessible:
    $script_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); // Remove trailing slash/backslash
    // If SCRIPT_NAME is /index.php, dirname is /. If /subdir/index.php, dirname is /subdir.
    // If your project files (ocr.php, analyze.php) are in the same directory as config.php or subdirectories relative to it,
    // and that directory is the web root or a primary subdirectory.
    define('BASE_URL', $protocol . $host_name . ($script_path === '/' ? '' : $script_path) . '/');
}

// Start session if not already started (good place for global session start)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- MISTRAL API CONFIGURATION AND HELPERS ---

// --- API KEY SECTION ---
// Try to get the API key from an environment variable
$mistralApiKey = getenv('MISTRAL_API_KEY'); // Using a more specific variable name

if (!$mistralApiKey) {
    // Fallback to a hardcoded API key if the environment variable is not set
    // IMPORTANT: For better security, try to use environment variables if your hosting supports it.
    $mistralApiKey = "aCHLMXr7v1Ojf2lniUNkjftHGnzxLoDp"; // YOUR API KEY
    // Using error_log is good for server-side logging, not visible to users directly
    error_log("INFO: Using hardcoded MISTRAL_API_KEY. Last 4 chars: " . substr($mistralApiKey, -4));
} else {
    error_log("INFO: Using MISTRAL_API_KEY from environment variable.");
}

// Define constants for Mistral API
if (!defined('MISTRAL_API_KEY')) {
    define('MISTRAL_API_KEY', $mistralApiKey);
}
if (!defined('MISTRAL_API_URL')) {
    define('MISTRAL_API_URL', 'https://api.mistral.ai/v1/chat/completions');
}
if (!defined('MISTRAL_MODEL_SMALL')) {
    define('MISTRAL_MODEL_SMALL', 'mistral-small-latest'); // Or your preferred model
}

// Function to send JSON response
if (!function_exists('send_json_response')) {
    function send_json_response($data, $statusCode = 200) {
        http_response_code($statusCode);
        // Ensure previous outputs (if any) are cleared if headers are already sent by mistake
        if (!headers_sent()) {
             header('Content-Type: application/json');
        }
        echo json_encode($data);
        exit;
    }
}

// Function to make cURL requests to Mistral API
if (!function_exists('call_mistral_api')) {
    function call_mistral_api($payload) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, MISTRAL_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . MISTRAL_API_KEY, // Uses the defined constant
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60 seconds timeout
        // It's good practice to set CURLOPT_CONNECTTIMEOUT as well
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20); // 20 seconds connection timeout


        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error_num = curl_errno($ch);
        $curl_error_msg = curl_error($ch);
        curl_close($ch);

        if ($curl_error_num) {
            error_log("cURL Error to Mistral API ($curl_error_num): $curl_error_msg. URL: " . MISTRAL_API_URL);
            return ['error' => "API request failed. Please try again later. cURL error: $curl_error_msg", 'httpcode' => 0, 'data' => null];
        }

        $responseData = json_decode($response, true);

        // Log the raw response if decoding fails or if it's an error code, for debugging
        if ($httpcode >= 400 || !$responseData) {
             error_log("Mistral API Error ($httpcode). URL: " . MISTRAL_API_URL . ". Payload: " . json_encode($payload) . ". Response: " . $response);
             return [
                'error' => "Mistral API error. Status: $httpcode.",
                'httpcode' => $httpcode,
                'data' => $responseData ?: ['raw_response' => $response, 'message' => 'Could not decode API response or received an error.']
            ];
        }

        return ['error' => null, 'httpcode' => $httpcode, 'data' => $responseData];
    }
}

?>