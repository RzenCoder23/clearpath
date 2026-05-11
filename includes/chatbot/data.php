<?php

return [

    'intents' => [


        'anxiety' => [
            'phrases'  => ['manage anxiety', 'panic attack', 'panic attacks', 'racing thoughts', 'on edge', "can't stop worrying", 'cope with anxiety'],
            'keywords' => ['anxious', 'anxiety', 'worried', 'worry', 'panic', 'nervous', 'jittery'],
            'responses' => [
                "Anxiety is a common experience that affects both mind and body. Several evidence-based techniques can help manage it:\n\n• The 4-7-8 breathing method — inhale for 4 seconds, hold for 7, exhale for 8\n• The 5-4-3-2-1 grounding exercise — name 5 things you see, 4 you can touch, 3 you hear, 2 you smell, 1 you taste\n• Regular physical activity, which reduces baseline anxiety over time\n\nThe Self-Care planner is a good place to set up a daily breathing or movement habit.",

                "Managing anxiety often involves both quick techniques and long-term habits. Slow, deep breathing helps regulate the nervous system in the moment. Over time, consistent sleep, reduced caffeine, and regular movement all lower overall anxiety levels.\n\nWriting anxious thoughts down in the Journal can also help — externalising thoughts on paper makes them feel less overwhelming.",

                "Several strategies have research behind them for managing anxiety: deep breathing, mindfulness exercises, and gradual exposure to what triggers the worry. Limiting screen time before bed and reducing caffeine are also commonly recommended.\n\nThe Articles section in ClearPath has more detailed reading on anxiety techniques.",
            ],
        ],


        'stress' => [
            'phrases'  => ['feeling overwhelmed', 'feel overwhelmed', "can't cope", 'too much going on', 'falling behind', 'spread too thin', 'so much pressure', 'burnt out', 'burned out'],
            'keywords' => ['stressed', 'stress', 'overwhelmed', 'pressure', 'drowning'],
            'responses' => [
                "Stress and overwhelm are common when we have too much to manage. A few useful techniques:\n\n• Write down everything on your mind, then pick just one thing to focus on today\n• Take short breaks — even 5 minutes outside helps reset focus\n• Sort tasks into 'within my control' and 'outside my control' columns\n\nThe Self-Care planner can help build small daily habits that reduce overall stress.",

                "Feeling overwhelmed often means too many demands without enough recovery time. Effective approaches include breaking large tasks into smaller steps, scheduling short breaks, and protecting sleep and meal times.\n\nUsing the Calendar to plan downtime alongside tasks can make recovery time visible and intentional.",

                "Stress management generally involves three things: reducing input where possible, increasing recovery (sleep, movement, time outdoors), and building reliable daily routines. Even small consistent habits make a measurable difference.\n\nThe Mood Tracker can help you spot patterns — for example, which days are most stressful and what tends to help.",
            ],
        ],

        // Sadness/Low mood
        'sadness' => [
            'phrases'  => ['feel low', 'feel sad', 'feel down', 'feeling low', 'feeling sad', 'feeling down', 'feeling blue', 'low mood'],
            'keywords' => ['sad', 'depressed', 'depression', 'unhappy', 'down', 'blue'],
            'responses' => [
                "Low mood is a common experience and can have many causes — sleep, stress, diet, lack of sunlight, or simply going through a difficult period. Things that often help include:\n\n• Maintaining a daily routine, even a simple one\n• Light physical activity, like a short walk\n• Connecting with at least one person each day\n• Limiting time on social media\n\nIf low mood lasts more than two weeks, the NHS recommends speaking to a GP — there are effective treatments available.",

                "Persistent low mood is something many people experience at some point. Evidence-based strategies include keeping a regular sleep schedule, getting daylight exposure each morning, gentle exercise, and writing about thoughts and feelings.\n\nThe Journal in ClearPath supports this with emotion tags so patterns become visible over time.",

                "When mood is low, small consistent habits tend to help more than large changes. Maintaining basic routines around sleep, food, and movement provides a foundation. Tracking mood daily in the Mood Tracker can also reveal patterns and improvement over time.\n\nFor mood that lasts more than a couple of weeks, a GP can provide proper assessment and support.",
            ],
        ],

        //Anger/Frustration
        'anger' => [
            'phrases'  => ['so frustrated', 'so annoyed', 'losing my temper', 'wound up', 'fed up'],
            'keywords' => ['angry', 'anger', 'furious', 'frustrated', 'mad', 'irritated', 'rage'],
            'responses' => [
                "Anger and frustration are normal emotions — what matters is how we manage them. Useful techniques include:\n\n• Slow exhalation breathing (longer out-breath than in-breath) to calm the body\n• Physical activity to release built-up tension\n• Writing down the situation before responding to it\n\nThe Journal is a private space to process frustration without sending anything you might regret.",

                "Anger management generally involves three steps: noticing the physical signs early (tense shoulders, racing heart), pausing before reacting, and finding constructive ways to express the emotion. Walking, writing, and talking with someone trusted are commonly recommended.\n\nMany people find regular exercise reduces their overall reactivity over time.",

                "Frustration tends to peak quickly and pass within minutes if given space. A short break, slow breathing, or writing the frustration down often helps the intensity fade. Suppressing it tends to make it worse; expressing it constructively tends to help.\n\nThe Self-Care planner can include short daily habits like walking or meditation that reduce baseline irritability.",
            ],
        ],


        'loneliness' => [
            'phrases'  => ['feel alone', 'feel lonely', 'feeling alone', 'feeling lonely', 'feel isolated'],
            'keywords' => ['lonely', 'loneliness', 'isolated'],
            'responses' => [
                "Loneliness is widely experienced — research suggests around 1 in 4 adults feel lonely often. It is also one of the more solvable wellbeing challenges, though it takes consistent small actions.\n\nApproaches that tend to help: reconnecting with one person you've lost touch with, joining a regular group activity, volunteering, or scheduling small social interactions into the week.",

                "Loneliness isn't always about being alone — it's about feeling disconnected. Brief, regular social contact (a chat with a neighbour, a quick message to a friend) can have an outsized effect on wellbeing.\n\nThe Calendar in ClearPath can be used to schedule social activities, which makes them more likely to happen.",

                "Strategies for reducing loneliness include joining communities around shared interests, regular volunteering, and being deliberate about scheduling time with others. The NHS website has a useful page on coping with loneliness if you'd like to read more.",
            ],
        ],



        'sleep' => [
            'phrases'  => ['better sleep', "can't sleep", 'cannot sleep', 'trouble sleeping', 'tips for sleep', 'sleep problems', 'sleep better'],
            'keywords' => ['insomnia', 'sleepless', 'tired', 'exhausted', 'sleep'],
            'responses' => [
                "Sleep affects almost every aspect of wellbeing. The most evidence-backed sleep tips are:\n\n• Keep a consistent wake time, even on weekends\n• Get natural daylight within an hour of waking\n• Avoid screens for 30 minutes before bed\n• Keep the bedroom cool and dark\n• Avoid caffeine after early afternoon\n\nFor persistent sleep problems, a GP can recommend CBT-i, which is highly effective and doesn't require medication.",

                "Good sleep generally relies on routine. A consistent bedtime, a wind-down period without screens, and a cool dark bedroom all support deeper rest. Caffeine has a 6-8 hour effect on most adults, so timing matters.\n\nA short journal entry before bed — writing down anything still on your mind — helps many people fall asleep faster.",

                "Sleep difficulty is very common but often improvable with small changes. The fundamentals are: regular wake times, daylight exposure in the morning, physical activity during the day, and a low-stimulation hour before bed.\n\nThe Self-Care planner can be used to set up a 'screen off by 10pm' habit, which is one of the highest-impact sleep changes.",
            ],
        ],


        'meditation' => [
            'phrases'  => ['start meditating', 'how to meditate', 'how do i meditate', 'meditation app', 'mindfulness practice', 'breathing exercise'],
            'keywords' => ['meditate', 'meditation', 'mindfulness', 'mindful'],
            'responses' => [
                "Meditation is the practice of noticing where attention goes and gently bringing it back. A simple way to start:\n\n1. Sit comfortably and set a timer for 3 minutes\n2. Close your eyes and focus on your breath\n3. When your mind wanders, return attention to the breath without judgement\n\nResearch suggests short daily sessions (3-10 minutes) are more effective than occasional long ones. The Self-Care planner is a good place to set up a daily meditation habit.",

                "Mindfulness has substantial research behind it for stress reduction, focus, and emotional regulation. The technique itself is simple — pay attention to the present moment without judgement — but consistency is what builds the benefit.\n\nFree resources include the NHS-recommended 'Be Mindful' programme and apps like Insight Timer or Smiling Mind.",

                "Meditation doesn't require clearing the mind — that's a common misconception. The practice is noticing when attention wanders and returning it to a chosen focus (often the breath). Each return is the exercise itself.\n\nStarting with just 2-3 minutes per day is enough to begin building the habit. Try adding it to the Self-Care planner.",
            ],
        ],

        

        'gratitude' => [
            'phrases'  => ['gratitude practice', 'feel grateful', 'feeling grateful'],
            'keywords' => ['grateful', 'gratitude', 'thankful'],
            'responses' => [
                "Gratitude practice is one of the most well-researched positive psychology interventions. Writing down three things you're grateful for each day has been shown to improve mood over weeks of practice.\n\nThe Journal in ClearPath supports this with emotion tags including 'Grateful' — useful for reviewing patterns over time.",

                "Regular gratitude practice can shift attention toward positive aspects of daily life. Common methods include writing three things at the end of each day, keeping a gratitude journal, or noting one good moment per day.\n\nSpecificity matters more than quantity — 'a good cup of tea this morning' is more effective than vague 'family and health'.",

                "Gratitude is a learnable skill that gets stronger with practice. Even brief regular reflection — three things at the end of each day — has measurable effects on mood and outlook in research studies.\n\nClearPath's Journal is designed to support exactly this kind of reflection.",
            ],
        ],


        'self_care' => [
            'phrases'  => ['self care routine', 'self-care routine', 'build a routine', 'wellness routine', 'build a self care', 'build a self-care', 'daily routine'],
            'keywords' => [],
            'responses' => [
                "An effective self-care routine generally has three layers:\n\n1. Daily basics — sleep, water, movement, time outdoors\n2. Weekly resets — social time, hobbies, longer outdoor activity\n3. Monthly check-ins — reviewing how things are going overall\n\nStart small: pick 2-3 daily habits that take under 10 minutes total, and add to them once they become automatic. The Self-Care planner is built for tracking exactly this.",

                "Sustainable self-care is built on small consistent habits rather than occasional large efforts. Effective starting habits include drinking water on waking, 5-10 minutes of fresh air or movement, and a brief reflection or journal entry at the end of the day.\n\nUse the Self-Care planner to add habits and tick them off daily.",

                "Building a routine works best with three principles: start small, be specific, and track consistency. Vague goals like 'exercise more' tend to fail; specific habits like 'walk for 10 minutes after lunch' tend to stick.\n\nThe Self-Care planner supports this by letting you define and track concrete daily habits.",
            ],
        ],


        'motivation' => [
            'phrases'  => ['no motivation', 'cannot focus', "can't focus", 'no energy', "can't be bothered", 'lost motivation', 'always procrastinating'],
            'keywords' => ['unmotivated', 'lazy', 'procrastinating', 'procrastinate', 'unfocused'],
            'responses' => [
                "Motivation tends to follow action rather than precede it. A useful approach is to make the first step very small — open the document, write one sentence, walk to the door. Once started, momentum often builds.\n\nLow motivation is also frequently a sign of tiredness, hunger, or low blood sugar — checking the basics first often helps.",

                "Procrastination usually indicates a task that feels too large, too vague, or too uncertain. Breaking tasks down until the next step is concrete and small (under 5 minutes) often gets things moving.\n\nUsing the Calendar to schedule focused work blocks can also help — protected time tends to produce better results than 'whenever I feel like it'.",

                "Sustained focus generally requires three things: enough sleep, clear priorities, and minimised distractions. Techniques like the Pomodoro method (25 minutes focused work, 5 minutes break) can help structure work sessions.\n\nFor longer-term motivation, consistency in basic habits (sleep, food, movement) tends to matter more than willpower.",
            ],
        ],


        'happiness' => [
            'phrases'  => ['feeling great', 'feeling good', 'feeling happy', 'good day', 'great day'],
            'keywords' => ['happy', 'great', 'amazing', 'wonderful', 'excited', 'fantastic'],
            'responses' => [
                "Good days are worth noticing. Research suggests that pausing to recognise positive moments — rather than rushing past them — extends their effect on mood.\n\nLogging a good day in the Mood Tracker is useful for spotting which conditions tend to produce them.",

                "Capturing positive moments is just as valuable as tracking difficult ones. The Mood Tracker and Journal both support this — over time they reveal patterns about what tends to support wellbeing.\n\nWriting briefly about what made today good is a useful habit to develop.",
            ],
        ],




        'features' => [
            'phrases'  => ['what can you do', 'what features', 'what does clearpath do', 'how does this work', 'what is clearpath', 'what can this app do', 'features does clearpath'],
            'keywords' => ['features'],
            'responses' => [
                "ClearPath has six main tools:\n\n📊 Mood Tracker — log your daily mood and view weekly patterns\n📝 Journal — private writing space with emotion tags\n✅ Self-Care Planner — build and track daily wellness habits\n📅 Calendar — schedule wellness events and appointments\n📚 Wellbeing Articles — short evidence-based reads\n💬 This Assistant — for general wellness information and feature guidance\n\nWhich would you like to know more about?",

                "ClearPath provides tools for tracking mood, journalling, building habits, and reading wellness content. The Mood Tracker captures daily check-ins; the Journal supports written reflection; the Self-Care planner manages habits; the Calendar handles events; and the Articles section has educational content.\n\nLet me know which area interests you most.",
            ],
        ],


        'mood_tracking' => [
            'phrases'  => ['log mood', 'log my mood', 'track mood', 'track my mood', 'mood tracker'],
            'keywords' => [],
            'responses' => [
                "Daily mood tracking is one of the simplest wellness habits. Over a few weeks it reveals patterns — which days tend to be harder, what activities seem to help, and how mood relates to other factors like sleep.\n\nClearPath's Mood Tracker uses a 1-5 scale and takes just a few seconds per day.",

                "Mood tracking helps in two ways: it creates a regular pause to check in with yourself, and it reveals patterns over time. Both are valuable for self-awareness.\n\nThe Mood Tracker is accessible from the Dashboard or as its own page.",
            ],
        ],


        'journal' => [
            'phrases'  => ['write in journal', 'journal entry', 'start journalling', 'start journaling', 'how to journal', 'how do i journal'],
            'keywords' => ['journal', 'journalling', 'journaling', 'diary'],
            'responses' => [
                "Journalling has substantial research support for reducing anxiety, improving sleep, and processing difficult experiences. Useful starting prompts include:\n\n• 'How am I feeling right now, and why?'\n• 'What's on my mind today?'\n• 'Three things that went well today'\n\nClearPath's Journal is private and supports emotion tags for tracking patterns over time.",

                "Effective journalling doesn't require length or eloquence — three honest sentences count as a journal entry. The benefit comes from regularity rather than depth.\n\nCommon formats include 'how I felt today, what happened, one thing I'm grateful for'. The Journal in ClearPath supports this with optional emotion tags.",
            ],
        ],


        'greeting' => [
            'phrases'  => ['good morning', 'good afternoon', 'good evening', 'how are you'],
            'keywords' => ['hi', 'hello', 'hey', 'hiya'],
            'responses' => [
                "Hello! I can provide general wellness information and help you find features in ClearPath. What would you like to know about?",
                "Hi there. I'm the ClearPath assistant — happy to share wellness information or guide you through the app. What can I help with?",
                "Hello. You can ask me about wellness topics like sleep, stress, anxiety, or meditation, or about any of ClearPath's features. What are you interested in?",
            ],
        ],


        'thanks' => [
            'phrases'  => ['thank you', 'thanks so much', 'thanks a lot'],
            'keywords' => ['thanks', 'thx', 'ty'],
            'responses' => [
                "You're welcome. Let me know if there's anything else you'd like to know about.",
                "Glad it was helpful. I'm here if you have more questions.",
                "You're welcome. Feel free to ask about other wellness topics or app features any time.",
            ],
        ],


        'goodbye' => [
            'phrases'  => ['talk later', 'see you', 'gotta go'],
            'keywords' => ['bye', 'goodbye', 'cya'],
            'responses' => [
                "Take care. The assistant is here whenever you have wellness questions.",
                "Bye for now. Feel free to come back any time.",
                "See you later. ClearPath's other tools are always available too.",
            ],
        ],



        'about_bot' => [
            'phrases'  => ['who are you', 'what are you', 'are you human', 'are you real', 'are you ai', 'are you a robot', 'are you a bot'],
            'keywords' => [],
            'responses' => [
                "I'm a wellness information assistant built into ClearPath. I'm not a human and not a medical professional — I provide general information and can guide you to ClearPath's features. For medical or mental health concerns, please speak to a GP or qualified professional.",
                "I'm a chatbot designed to provide general wellness information and help you navigate ClearPath. I'm not a replacement for professional advice. The disclaimer panel on this page has helpline information if you ever need it.",
            ],
        ],

    ],


    'fallback' => [
        "I'm not sure I have information on that specifically. I can help with topics like:\n\n• Anxiety, stress, low mood\n• Sleep, motivation, focus\n• Meditation, gratitude, self-care\n• Building habits and routines\n• ClearPath's features\n\nCould you rephrase your question?",

        "That's outside what I can help with directly. I'm best at general wellness topics (anxiety, sleep, stress, meditation, self-care) and explaining how ClearPath's tools work. Could you try asking about one of those?",

        "I didn't quite catch that. Try asking about a wellness topic — for example, 'tips for better sleep', 'how do I manage stress', 'how do I start meditating' — or about ClearPath's features.",
    ],


    'short_yes' => [
        "Great. Head to the relevant page in ClearPath, or let me know what else you'd like to know about.",
        "Got it. Is there anything else I can help with?",
    ],

    'short_no' => [
        "No problem. Is there a different topic I can help with?",
        "Understood. Let me know what you'd like to ask about instead.",
    ],
];