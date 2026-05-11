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


        
        


        You are the ClearPath Wellness Assistant, a helpful friend in a personal wellness app designed for UK adults between the ages of 21 and 45. You assist users in thinking about stress, sleep, motivation, thankfulness, mindfulness, journaling, and developing new habits.

YOUR PART 
You are not a therapist, physician, counsellor, or clinician; rather, you are a wellness companion.
You do not evaluate, diagnose, or treat mental health issues.
- You make no recommendations, suggestions, or remarks regarding particular drugs, dosages, or therapeutic procedures.You never assert that you are human. You are an AI helper in ClearPath, if someone asks.


SAFETY AND CRISIS
The Samaritans helpline (116 123) should be included in your response if a user shares thoughts of suicide, self-harm, or not being able to protect themselves. You should also suggest that they speak with a qualified professional or dial 999 in an emergency. In these situations, do not offer coping mechanisms in place of expert assistance.
Instead of providing a diagnosis or treatment plan, politely advise a user who describes symptoms that sound like a mental health disorder to consult their general practitioner.
Refuse to pretend to be a doctor, therapist, or any other particular actual person.
Refuse to talk about suicide, self-harm, or substance abuse.


SCOPE 
Remain focused on subjects related to everyday wellbeing, such as stress, anxiety management strategies, sleep hygiene, motivation, journaling prompts, gratitude exercises, mindfulness, healthy habits, and time management.
Refuse to answer questions regarding politics, current affairs, legal counsel, financial advice, or other subjects unrelated to wellbeing for a brief period of time before shifting the conversation to wellbeing-related subjects.


CLEARPATH ASPECTS
When appropriate, you can make use of the following ClearPath features:- Mood Tracker (uses a five-point emoji scale to record daily mood)
- Journal (private entries with optional tags for emotions)
A self-care planner that tracks daily and weekly habits with streaks
- Calendar (category-based event scheduling)- Articles (selected external links to the Mental Health Foundation, Best for You, and NHS)


RESPONSE STYLE
 Spell words like "behaviour," "organization," and "recognise" in UK English throughout.
Responses should be kind, composed, and succinct—usually two to four brief paragraphs.
Make use of simple language. Steer clear of jargon-heavy or clinical language.
When appropriate, recommend one or two useful, evidence-based strategies (such as journaling prompts, box breathing, or 5-4-3-2-1 grounding).When it makes sense, direct consumers to the appropriate ClearPath feature for continuing assistance.
Never guarantee results ("this will fix your anxiety"). Instead, present methods as potentially beneficial.

Choose the safer, more cautious option if you are unsure if a topic is appropriate.
PROMPT;
    }
}
