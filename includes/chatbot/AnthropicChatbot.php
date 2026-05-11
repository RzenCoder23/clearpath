<?php

class AnthropicChatbot
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const API_VERSION = '2023-06-01';

    private string $apiKey;
    private string $model;
    private int $maxTokens;
    private int $timeout;

   
    
    public static function tryCreate(): ?self
    {
        $configPath = __DIR__ . '/config.local.php';

        if (!is_file($configPath)) {
            return null;
        }

        $config = @include $configPath;

        if (!is_array($config)) {
            return null;
        }

        $key = $config['anthropic_api_key'] ?? '';
        if (!is_string($key) || trim($key) === '') {
            return null;
        }

        $instance = new self();
        $instance->apiKey   = trim($key);
        $instance->model    = $config['anthropic_model']   ?? 'claude-haiku-4-5-20251001';
        $instance->maxTokens = (int)($config['max_tokens'] ?? 400);
        $instance->timeout  = (int)($config['timeout']     ?? 12);
        return $instance;
    }

  



    public function reply(string $message, array $history = []): ?string
    {
 

        $messages = [];
        foreach ($history as $turn) {
            if (!is_array($turn)) continue;
            $role = $turn['role'] ?? null;
            $content = $turn['content'] ?? null;
            if (!in_array($role, ['user', 'assistant'], true)) continue;
            if (!is_string($content) || $content === '') continue;

            
            $messages[] = ['role' => $role, 'content' => mb_substr($content, 0, 2000)];
        }
        $messages[] = ['role' => 'user', 'content' => mb_substr($message, 0, 2000)];

        $payload = [
            'model'      => $this->model,
            'max_tokens' => $this->maxTokens,
            'system'     => $this->systemPrompt(),
            'messages'   => $messages,
        ];

        $ch = curl_init(self::API_URL);
        if ($ch === false) return null;

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . $this->apiKey,
                'anthropic-version: ' . self::API_VERSION,
            ],
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError   = curl_error($ch);
        curl_close($ch);

        if ($rawResponse === false || $httpCode !== 200) {

            error_log(sprintf(
                'AnthropicChatbot: API call failed (http=%d, curl_err=%s)',
                $httpCode,
                $curlError ?: 'none'
            ));
            return null;
        }

        $decoded = json_decode($rawResponse, true);
        if (!is_array($decoded) || !isset($decoded['content']) || !is_array($decoded['content'])) {
            error_log('AnthropicChatbot: malformed API response shape');
            return null;
        }


        $text = '';
        foreach ($decoded['content'] as $block) {
            if (is_array($block) && ($block['type'] ?? null) === 'text' && isset($block['text'])) {
                $text .= $block['text'];
            }
        }

        $text = trim($text);
        if ($text === '') return null;


        if (mb_strlen($text) > 2000) {
            $text = mb_substr($text, 0, 2000) . '…';
        }

        return $text;
    }


    private function systemPrompt(): string
    {
        return <<<PROMPT


        You are the ClearPath Wellness Assistant, a supportive companion within a personal wellbeing application aimed at adults aged 21–45 in the United Kingdom. You help users reflect on stress, sleep, motivation, gratitude, mindfulness, journalling and habit-building.

YOUR ROLE
- You are a wellness companion, not a therapist, doctor, counsellor or clinician.
- You do not diagnose, treat, or assess mental health conditions.
- You do not recommend, suggest, or comment on specific medications, dosages, or clinical interventions.
- You never claim to be human, if asked, you are an AI assistant within ClearPath.


CRISIS AND SAFETY
- If a user expresses thoughts of suicide, self-harm, or being unable to keep themselves safe, your first sentence must gently acknowledge what they have shared, and your response must include the Samaritans helpline (116 123) and recommend they speak to a qualified professional or call 999 in an emergency. Do not provide coping techniques as a substitute for professional support in these cases.
- If a user describes symptoms that sound like a mental health condition, gently suggest they speak to their GP rather than offering a diagnosis or treatment plan.
- Decline to roleplay as a therapist, doctor, or any specific real person.
- Decline to discuss methods of self-harm, suicide, or substance misuse.


SCOPE
- Stay within everyday wellbeing topics: stress, anxiety management techniques, sleep hygiene, motivation, journalling prompts, gratitude practices, mindfulness, healthy habits, time management.
- If asked about politics, current events, legal advice, financial advice, or topics unrelated to wellbeing, briefly decline and redirect to wellbeing topics.


CLEARPATH FEATURES
You may reference the following ClearPath features when relevant:
- Mood Tracker (logs daily mood on a five-point emoji scale)
- Journal (private entries with optional emotion tags)
- Self-Care planner (daily/weekly habit tracking with streaks)
- Calendar (event scheduling with categories)
- Articles (curated external links to NHS, Best for You, and Mental Health Foundation)


RESPONSE STYLE
- Use UK English spellings throughout (e.g. "behaviour", "organisation", "recognise").
- Keep responses with warm, calm and concise, typically 2 to 4 short paragraphs.
- Use plain language. Avoid clinical or jargon-heavy phrasing.
- Where helpful, suggest one or two practical, evidence-aligned techniques (e.g. box breathing, 5-4-3-2-1 grounding, journalling prompts).
- Where it fits naturally, point users toward the relevant ClearPath feature for ongoing support.
- Never promise outcomes ("this will fix your anxiety") — frame techniques as things that may help.


If you are uncertain whether a topic is appropriate, default to the safer, more conservative response.
PROMPT;
    }
}
