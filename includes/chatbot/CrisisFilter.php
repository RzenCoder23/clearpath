<?php

<?php

/**
 * ClearPath Crisis Filter
 * 
 * CONTENT WARNING: This file contains explicit phrases related to 
 * self-harm and suicidal ideation for pattern matching purposes only.
 * 
 * PURPOSE: Content moderation and user safety
 * CONTEXT: Academic proof-of-concept implementation
 * USAGE: Pattern matching to redirect users to professional services
 * 
 * These phrases are standard in content moderation systems (social media 
 * platforms, crisis helplines, mental health services). This implementation 
 * is informed by Vaidyam et al. (2019) and demonstrates defensive safety 
 * mechanisms in mental health technology.
 * @reference Vaidyam, A.N. et al. (2019)
 */



class CrisisFilter
{

    private const CRISIS_PHRASES = [
        'kill myself', 'kill my self', 'killing myself',
        'end my life', 'ending my life',
        'end it all', 'ending it all',
        'end things', 'ending things',
        'take my own life', 'taking my own life',
        'want to die', 'wanna die', 'wish i was dead', 'wish i were dead',
        "don't want to be here", "dont want to be here",
        "don't want to live", "dont want to live",
        'no reason to live', 'no point living', 'no point in living',
        'better off without me', 'better off dead',
        'hurt myself', 'harm myself', 'cut myself', 'cutting myself',
        'overdose on', 'take an overdose',
        'jump off', 'jump from',
    ];

  
    private const CRISIS_KEYWORDS = [
        'suicide', 'suicidal',
    ];

    private const CONTEXT_WORDS = [
        'feel', 'feeling', 'thinking', 'thought', 'thoughts',
        'help', 'need', 'having', 'have', 'i', "i'm", 'im',
    ];

  
    public static function check(string $message): ?string
    {
        $normalised = self::normalise($message);


        foreach (self::CRISIS_PHRASES as $phrase) {
            if (str_contains($normalised, $phrase)) {
                return self::response();
            }
        }


        foreach (self::CRISIS_KEYWORDS as $kw) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/u', $normalised)) {
                foreach (self::CONTEXT_WORDS as $ctx) {
                    if (preg_match('/\b' . preg_quote($ctx, '/') . '\b/u', $normalised)) {
                        return self::response();
                    }
                }
            }
        }

        return null;
    }


    private static function normalise(string $message): string
    {
        $m = mb_strtolower($message, 'UTF-8');
        $m = preg_replace('/[\s\r\n]+/', ' ', $m);
        $m = trim($m);
        return $m;
    }

    private static function response(): string
    {
        return "I'm really sorry you're feeling this way. What you've shared sounds important, and you deserve to talk to someone who can properly support you right now.\n\n"
             . "Please reach out to one of these services — they are free, confidential, and available 24/7:\n\n"
             . "• Samaritans — call 116 123, or email jo@samaritans.org\n"
             . "• Shout — text SHOUT to 85258\n"
             . "• If you are in immediate danger, please call 999 or go to A&E\n\n"
             . "I'm a wellness companion and I'm not able to replace the kind of support a trained person can offer. Please don't go through this alone.";
    }
}
