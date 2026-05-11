<?php

class ChatbotEngine
{

    private const PHRASE_WEIGHT = 3;


    private const KEYWORD_WEIGHT = 1;


    private const MAX_INPUT_LENGTH = 2000;

    /** @var array chatbot content */
    private array $data;

    /**
     * @param string|null $dataFile  optional data file path
     */
    public function __construct(?string $dataFile = null)
    {
        $this->data = require ($dataFile ?? __DIR__ . '/data.php');
    }

   
    public function reply(string $message, array $history = []): string
    {

        $clean = $this->normalise($message);
        if ($clean === '') {
            return "What would you like to know about? I can help with wellness topics or ClearPath's features.";
        }


        $contextual = $this->handleShortReply($clean, $history);
        if ($contextual !== null) {
            return $contextual;
        }


        $scores = $this->scoreIntents($clean);


        $bestIntent = $this->pickBestIntent($scores);

        if ($bestIntent === null) {
            return $this->randomFrom($this->data['fallback']);
        }


        $responses = $this->data['intents'][$bestIntent]['responses'];
        return $this->randomFrom($responses);
    }

 
    private function normalise(string $message): string
    {
        $message = trim($message);
        if (strlen($message) > self::MAX_INPUT_LENGTH) {
            $message = substr($message, 0, self::MAX_INPUT_LENGTH);
        }
        return mb_strtolower($message);
    }

 
    private function handleShortReply(string $message, array $history): ?string
    {
        $words = preg_split('/\s+/', $message);
        if (count($words) > 2) {
            return null;
        }

        $affirmatives = ['yes', 'yeah', 'yep', 'sure', 'ok', 'okay', 'please'];
        $negatives    = ['no', 'nope', 'nah', 'not really'];

        if (in_array($message, $affirmatives, true)) {
            return $this->randomFrom($this->data['short_yes']);
        }
        if (in_array($message, $negatives, true)) {
            return $this->randomFrom($this->data['short_no']);
        }

        return null;
    }

   
    private function scoreIntents(string $message): array
    {
        $scores = [];

        foreach ($this->data['intents'] as $intent => $rules) {
            $score = 0;

            foreach ($rules['phrases'] ?? [] as $phrase) {
                if (str_contains($message, $phrase)) {
                    $score += self::PHRASE_WEIGHT;
                }
            }

            foreach ($rules['keywords'] ?? [] as $word) {
                $pattern = '/\b' . preg_quote($word, '/') . '\b/u';
                if (preg_match($pattern, $message)) {
                    $score += self::KEYWORD_WEIGHT;
                }
            }

            if ($score > 0) {
                $scores[$intent] = $score;
            }
        }

        return $scores;
    }

  
    private function pickBestIntent(array $scores): ?string
    {
        if (empty($scores)) {
            return null;
        }
        arsort($scores);
        return array_key_first($scores);
    }

   
    private function randomFrom(array $items): string
    {
        return $items[array_rand($items)];
    }
}