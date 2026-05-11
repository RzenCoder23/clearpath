## About the Project

**Author:** st20231150  
**Institution:** Cardiff metropolitan University 
**Project Type:** Final Year BSc Computing Dissertation 
**Academic Year:** (2025-2026)  
**Status:** Final Year Dissertation



# ClearPath — Mental Health & Wellbeing Web Application

A comprehensive web-based mental health support platform developed as part of an undergraduate dissertation project at Cardiffmet Univresity. ClearPath integrates journaling, mood tracking, habit planning, and an AI-powered wellness assistant within a unified dashboard.


## Main Features

- **Secure Authentication** — bcrypt password hashing, session management

- **Mood Tracking** — Five-point emoji scale with Chart.js visualization

- **Journaling** — Private entries with emotion tagging (stress, joy, anxiety, etc.)

- **Self-Care Habit Planner** — Daily/weekly habit tracking with streaks

- **Wellness Calendar** — Event scheduling with color-coded categories

- **Curated Articles** — External links to NHS, Mind, and Mental Health Foundation resources

- **Conversational Wellness Assistant** — Three-layer safety architecture:

  - Crisis-keyword filter (pre-AI interception)
  - Language model layer (system-prompted, constrained)
  - Rule-based fallback (12 intents, deterministic responses)



## Technology stack

**Frontend:**
- HTML5, CSS3, JavaScript
- Chart.js (mood visualization)
- AJAX Fetch API (asynchronous requests)

**Backend:**
- PHP 8.3+
- MySQL 8.0+
- Apache (via XAMPP)

**Security:**
- PDO prepared statements 
- bcrypt password hashing 
- Session-based authentication
- htmlspecialchars() 
- Input validation 

**Architecture:**
- Three-tier MVC-inspired architecture
- Modular PHP handlers for each feature
- Six-table relational database with foreign key cascades



## Installation & Setup:

- **XAMPP** (Apache + MySQL + PHP 8.0+)
- **Visual Studio Code** (or any code editor)
- **Web browser** (Chrome, Firefox, Edge, Safari)



### Step 1: Install XAMPP

Download and install XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)

### Step 2: Extract Project Files

1. Unzip the `clearpath.zip` file
2. Move the `clearpath` folder to: `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)

xampp/
└── htdocs/
    └── clearpath/
        ├── index.php
        ├── dashboard.php
        ├── includes/
        ├── assets/
        └── ...


### Step 3: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Start **Apache** (should show green)
3. Start **MySQL** (should show green)


### Step 4: Create Database

1. Open your browser and go to: **http://localhost/phpmyadmin**
2. Click **"New"** in the left sidebar
3. Database name: **clearpath**
4. Click **"Create"**
5. Select the 'clearpath' database you just created
6. Click the **"SQL"** tab at the top
7. Open `Appendix_C_Schema.sql` (or find the SQL code in the dissertation appendix)
8. Copy and paste the entire SQL code into the text area
9. Ensure "Enable foreign key checks" is CHECKED**
10. Click **"Go"**



You should see messages confirming the tables were created successfully.

### Step 5: Access the Application

Open your browser and navigate to: http://localhost/clearpath

You should see the ClearPath landing page with "Register" and "Login" options.



### Chatbot API setup

- Anthropic API account ([console.anthropic.com](https://console.anthropic.com))
- API key with credits

### 

1. Navigate to `clearpath/includes/chatbot/`
2. Copy `config.local.php.example` to `config.local.php`:

```bash
cp includes/chatbot/config.local.php.example includes/chatbot/config.local.php
```

3. Open `config.local.php` in your editor
4. Paste your Anthropic API key:

```php
return [
    'anthropic_api_key' => 'your_api_key',
    'anthropic_model' => 'claude-haiku',
    'max_tokens' => 400,
    'timeout' => 12,
];
```

5. Save the file

**SECURITY WARNING:**
- `config.local.php` is excluded from version control via `.gitignore`
- **NEVER commit API keys to GitHub**
- Delete `config.local.php` before creating any public archives
- Rotate your API key after testing at [console.anthropic.com/settings/keys](https://console.anthropic.com/settings/keys)



### Includes

- **With API key:** Crisis filter → LLM layer → Fallback (if API fails)
- **Without API key:** Crisis filter → Fallback (deterministic responses)

The application remains fully functional without the API key.

## Testing

### Create a Test Account

1. Go to `http://localhost/clearpath`
2. Click **"Register"**
3. Fill in the form:
   - Full Name: `Test User`
   - Email: `test@example.com`
   - Password: `Test1234`
4. Click **"Sign Up"**



### Features to test:

 **Mood Tracker** — Log a mood score (dashboard or mood page)  
 **Journal** — Create an entry with emotion tags  
 **Habits** — Mark a habit as complete (checkbox)  
 **Calendar** — Add a wellness event  
 **Articles** — Visit external NHS/Mind links  
 **Chatbot** — Send a message (test: "I feel stressed")  


## Known Limitations: 

This project is an academic prototype and is not production-ready.
Current limitations include:

- No HTTPS
- No email verification
- No password reset system
- No MFA support
- No CAPTCHA protection
- Localhost/XAMPP only
- No deployment configuration


## Academic Context
This project was developed as part of my final-year Software Engineering dissertation at Cardiff Metropolitan University. The project focuses on designing and developing a unified mental health and wellbeing platform that combines, multiple wellbeing tools into one accessible web application.


## Project Structure

clearpath/
├── index.php                     
├── register.php                  
├── login.php                     
├── dashboard.php                 
├── mood.php                      
├── journal.php                   
├── selfcare.php                  
├── calendar.php                  
├── articles.php                  
├── chatbot.php                   
├── settings.php                  
├── logout.php                    
│
├── includes/
│   ├── register_handler.php      
│   ├── login_handler.php         
│   ├── mood_handler.php          
│   ├── journal_handler.php       
│   ├── selfcare_handler.php      
│   ├── calendar_handler.php      
│   ├── chatbot_handler.php       
│   ├── settings_handler.php      
│   │
│   └── chatbot/
│       ├── CrisisFilter.php      
│       ├── AnthropicChatbot.php  
│       ├── ChatbotEngine.php     
│       ├── config.local.php.example
│       ├── data.php              
│       └── README.md
│
├── assets/
│   ├── css/
│   │   └── style.css             
│   └── js/
│       └── script.js             
│
└── .gitignore                    


## License & Ethical Considerations:

### Important Dislaimer
ClearPath is not a clinical or medical system.
It should not replace:

- Professional mental health support
- Therapy
- Medical advice
- The application does not diagnose mental health conditions.
- Users experiencing serious distress are encouraged to contact professional support services.


### Disclaimers

- No clinical validation has been performed
- No user trials or empirical testing conducted
- Not approved by any healthcare authority
- Contains no diagnostic or treatment functionality
- Users are directed to professional services (NHS, Samaritans)

### Data Privacy

- All data stored locally in MySQL database
- No cloud storage or third-party data transmission (except optional Anthropic API)
- No analytics or tracking
- User data deleted on account deletion (CASCADE)


### UK Crisis Support Resources
- **Samaritans** (UK): 116 123 (24/7, free)
- **Shout** (UK): Text SHOUT to 85258
- **Emergency**: 999 (UK) or local emergency services


## Acknowledgments

- NHS England
- Anthropic
- OWASP
- Chart.js
- Cardiff Metropolitan University


## Important Notes

 This project was built for educational purposes only.
 API keys should never be uploaded to GitHub.
 Always keep config.local.php private.

 The system was tested mainly on:
- Chrome
- Firefox
- Microsoft Edge


Last updated: May 2026