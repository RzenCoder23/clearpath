<?php


session_start();
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['reply' => 'Method not allowed.']);
    exit();
}


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['reply' => 'Please log in to use the assistant.']);
    exit();
}


$rawBody = file_get_contents('php://input');
$input   = json_decode($rawBody, true);

if (!is_array($input)) {
    echo json_encode(['reply' => 'Sorry, I had trouble reading your message. Please try again.']);
    exit();
}

$message = isset($input['message']) && is_string($input['message']) ? trim($input['message']) : '';
$history = isset($input['history']) && is_array($input['history'])  ? $input['history']       : [];

if ($message === '') {
    echo json_encode(['reply' => 'Please type a message and I\'ll do my best to help.']);
    exit();
}

require_once __DIR__ . '/chatbot/CrisisFilter.php';
require_once __DIR__ . '/chatbot/AnthropicChatbot.php';
require_once __DIR__ . '/chatbot/ChatbotEngine.php';


$crisisResponse = CrisisFilter::check($message);
if ($crisisResponse !== null) {
    echo json_encode(['reply' => $crisisResponse, 'source' => 'crisis_filter']);
    exit();
}


$reply  = null;
$source = null;

try {
    $client = AnthropicChatbot::tryCreate();
    if ($client !== null) {
        $apiReply = $client->reply($message, $history);
        if (is_string($apiReply) && trim($apiReply) !== '') {
            $reply  = $apiReply;
            $source = 'anthropic';
        }
    }
} catch (Throwable $e) {
    error_log('AnthropicChatbot threw: ' . $e->getMessage());
    
}


if ($reply === null) {
    try {
        $engine = new ChatbotEngine();
        $reply  = $engine->reply($message, $history);
        $source = 'rule_based';
    } catch (Throwable $e) {
        error_log('ChatbotEngine error: ' . $e->getMessage());
        $reply  = "Something went wrong on my side. Please try rephrasing your message or come back in a moment.";
        $source = 'error';
    }
}

echo json_encode(['reply' => $reply, 'source' => $source]);
