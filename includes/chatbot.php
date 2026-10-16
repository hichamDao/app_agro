<?php
/**
 * FoodMax Group Chatbot
 * Conversational lead qualification bot for fresh produce importers
 */
class FM_Chatbot {
    private $sessionKey = 'fm_chatbot_session';
    private $maxHistory = 20;
    private $dbConvId = null;
    private $sessionId = null;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->sessionId = $this->getOrCreateSessionId();
        $this->initSession();
        $this->loadOrCreateConversation();
    }

    private function getOrCreateSessionId() {
        if (empty($_SESSION['fm_chatbot_session_id'])) {
            $_SESSION['fm_chatbot_session_id'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['fm_chatbot_session_id'];
    }

    private function initSession() {
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [
                'step' => 'welcome',
                'history' => [],
                'lead_data' => [
                    'origin' => '',
                    'domain' => '',
                    'products' => [],
                    'volume' => '',
                    'timeline' => '',
                    'contact' => '',
                ],
                'context' => [],
            ];
        }
    }

    private function loadOrCreateConversation() {
        global $conn;
        if (!$conn) return;

        $stmt = mysqli_prepare($conn, 'SELECT id FROM chat_conversations WHERE session_id = ? LIMIT 1');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $this->sessionId);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                $this->dbConvId = (int) $row['id'];
                $this->loadConversationFromDb();
            } else {
                $this->createConversationInDb();
            }
            mysqli_stmt_close($stmt);
        }
    }

    private function createConversationInDb() {
        global $conn;
        if (!$conn) return;

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        $page = isset($_SERVER['REQUEST_URI']) ? 'https://' . ($_SERVER['HTTP_HOST'] ?? '') . $_SERVER['REQUEST_URI'] : '';

        // Get geo from session (set by API from JS ipwho.is)
        $geo = $_SESSION['fm_chatbot_geo'] ?? [];
        $city = $geo['city'] ?? '';
        $country = $geo['country'] ?? '';

        $stmt = mysqli_prepare($conn, 'INSERT INTO chat_conversations (session_id, ip_address, city, country, user_agent, referrer, page_url) VALUES (?, ?, ?, ?, ?, ?, ?)');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssssss', $this->sessionId, $ip, $city, $country, $ua, $ref, $page);
            if (mysqli_stmt_execute($stmt)) {
                $this->dbConvId = (int) mysqli_insert_id($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }

    private function loadConversationFromDb() {
        global $conn;
        if (!$conn || !$this->dbConvId) return;

        $stmt = mysqli_prepare($conn, 'SELECT lead_data, messages, step FROM chat_conversations WHERE id = ? LIMIT 1');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $this->dbConvId);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                if ($row['lead_data']) {
                    $_SESSION[$this->sessionKey]['lead_data'] = json_decode($row['lead_data'], true) ?? $_SESSION[$this->sessionKey]['lead_data'];
                }
                if ($row['messages']) {
                    $_SESSION[$this->sessionKey]['history'] = json_decode($row['messages'], true) ?? $_SESSION[$this->sessionKey]['history'];
                }
                if ($row['step']) {
                    $_SESSION[$this->sessionKey]['step'] = $row['step'];
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    private function saveConversationToDb() {
        global $conn;
        if (!$conn || !$this->dbConvId) return;

        $session = $_SESSION[$this->sessionKey];
        $leadData = json_encode($session['lead_data']);
        $messages = json_encode($session['history']);
        $step = $session['step'];
        $msgCount = count($session['history']);
        $status = $step === 'qualified' ? 'qualified' : 'active';

        $stmt = mysqli_prepare($conn, 'UPDATE chat_conversations SET lead_data = ?, messages = ?, step = ?, message_count = ?, status = ? WHERE id = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssisi', $leadData, $messages, $step, $msgCount, $status, $this->dbConvId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    public function getSession() {
        return $_SESSION[$this->sessionKey];
    }

    public function reset() {
        $_SESSION[$this->sessionKey] = [
            'step' => 'welcome',
            'history' => [],
            'lead_data' => [
                'origin' => '',
                'domain' => '',
                'products' => [],
                'volume' => '',
                'timeline' => '',
                'contact' => '',
            ],
            'context' => [],
        ];
        unset($_SESSION['fm_chatbot_geo']);
        $this->dbConvId = null;
        $_SESSION['fm_chatbot_session_id'] = bin2hex(random_bytes(16));
        $this->sessionId = $_SESSION['fm_chatbot_session_id'];
        $this->loadOrCreateConversation();
    }

    public function processMessage($userMessage) {
        $session = $this->getSession();
        $step = $session['step'];
        $leadData = $session['lead_data'];

        $this->addToHistory('user', $userMessage);

        $response = $this->generateResponse($userMessage, $step, $leadData);

        $this->addToHistory('bot', $response['message'], $response['options'] ?? null);

        $nextStep = $response['next_step'];
        $_SESSION[$this->sessionKey]['step'] = $nextStep;
        $_SESSION[$this->sessionKey]['lead_data'] = $response['lead_data'];

        $this->saveConversationToDb();

        $isQualified = ($nextStep === 'qualified' && $step !== 'qualified');

        // Only auto-reset if user explicitly starts a new request
        $userWantsNewRequest = (strtolower(trim($userMessage)) === 'start new request');
        if ($userWantsNewRequest) {
            $_SESSION[$this->sessionKey] = [
                'step' => 'welcome',
                'history' => [],
                'lead_data' => [
                    'origin' => '',
                    'domain' => '',
                    'products' => [],
                    'volume' => '',
                    'timeline' => '',
                    'contact' => '',
                ],
                'context' => [],
            ];
            unset($_SESSION['fm_chatbot_geo']);
            $this->dbConvId = null;
            $_SESSION['fm_chatbot_session_id'] = bin2hex(random_bytes(16));
            $this->sessionId = $_SESSION['fm_chatbot_session_id'];
            // Force next_step to welcome for the response
            $nextStep = 'welcome';
            $response['message'] = "hi there welcome to foodmax group 👋 i'm here to help you find the best moroccan fruits and vegetables for your market\n\nto give you the right recommendations could you tell me where you're based";
            $response['options'] = [
                'europe (eu)',
                'middle east / gulf',
                'north america',
                'asia / pacific',
                'africa',
                'other'
            ];
            $response['lead_data'] = $_SESSION[$this->sessionKey]['lead_data'];
        }

        return [
            'message' => $response['message'],
            'options' => $response['options'] ?? [],
            'step' => $nextStep,
            'lead_data' => $response['lead_data'],
            'qualified' => $isQualified,
        ];
    }

    private function addToHistory($role, $message, $options = null) {
        $session = $this->getSession();
        $session['history'][] = [
            'role' => $role,
            'message' => $message,
            'options' => $options,
            'timestamp' => time(),
        ];
        if (count($session['history']) > $this->maxHistory) {
            array_shift($session['history']);
        }
        $_SESSION[$this->sessionKey] = $session;
    }

    private function generateResponse($userMessage, $step, $leadData) {
        $message = strtolower(trim($userMessage));

        switch ($step) {
            case 'welcome':
                return $this->handleWelcome($message, $leadData);

            case 'ask_origin':
                return $this->handleOrigin($message, $leadData);

            case 'ask_domain':
                return $this->handleDomain($message, $leadData);

            case 'ask_products':
                return $this->handleProducts($message, $leadData);

            case 'ask_volume':
                return $this->handleVolume($message, $leadData);

            case 'ask_timeline':
                return $this->handleTimeline($message, $leadData);

            case 'ask_contact':
                return $this->handleContact($message, $leadData);

            case 'qualified':
                return $this->handleQualified($message, $leadData);

            default:
                return $this->handleWelcome($message, $leadData);
        }
    }

    private function handleWelcome($message, $leadData) {
        $greetings = ['hello', 'hi', 'hey', 'bonjour', 'salut', 'good morning', 'good afternoon'];
        $isGreeting = in_array($message, $greetings) || strlen($message) < 3;

        if ($isGreeting || $message === '') {
            return [
                'message' => "hi there welcome to foodmax group 👋 i'm here to help you find the best moroccan fruits and vegetables for your market\n\nto give you the right recommendations could you tell me where you're based",
                'options' => [
                    'europe (eu)',
                    'middle east / gulf',
                    'north america',
                    'asia / pacific',
                    'africa',
                    'other'
                ],
                'next_step' => 'ask_origin',
                'lead_data' => $leadData,
            ];
        }

        return $this->handleOrigin($message, $leadData);
    }

    private function handleOrigin($message, $leadData) {
        $validOrigins = [
            'europe (eu)',
            'middle east / gulf',
            'north america',
            'asia / pacific',
            'africa',
            'other'
        ];
        $match = null;
        foreach ($validOrigins as $v) {
            if (strpos(strtolower($message), strtolower(substr($v, 0, 3))) !== false) {
                $match = $v;
                break;
            }
        }
        if (!$match) {
            foreach ($validOrigins as $v) {
                if (strpos(strtolower($message), strtolower($v)) !== false) { $match = $v; break; }
            }
        }
        if (!$match) {
            return [
                'message' => "please choose one of the options below",
                'options' => $validOrigins,
                'next_step' => 'ask_origin',
                'lead_data' => $leadData,
            ];
        }
        $leadData['origin'] = $match;
        return [
            'message' => "got it you're in **$match** that helps us plan the best shipping routes and seasonal availability for you\n\nwhat type of business are you",
            'options' => [
                'importer / distributor',
                'wholesaler',
                'retailer / supermarket',
                'food service / horeca',
                'processing industry',
                'other'
            ],
            'next_step' => 'ask_domain',
            'lead_data' => $leadData,
        ];
    }

private function handleDomain($message, $leadData) {
        // If no options expected in this context? but we have options - validate
        $validDomains = [
            'importer / distributor',
            'wholesaler',
            'retailer / supermarket',
            'food service / horeca',
            'processing industry',
            'other'
        ];
        $match = null;
        foreach ($validDomains as $v) {
            if (strpos($message, strtolower(substr($v, 0, 4))) !== false || $message === strtolower($v)) {
                $match = $v;
                break;
            }
        }
        if (!$match && count($validDomains)) {
            // Try exact substring
            foreach ($validDomains as $v) {
                if (strpos($message, strtolower($v)) !== false) { $match = $v; break; }
            }
        }
        if (!$match) {
            // Invalid - ask again
            return [
                'message' => "please choose one of the options below",
                'options' => $validDomains,
                'next_step' => 'ask_domain',
                'lead_data' => $leadData,
            ];
        }
        $leadData['domain'] = $match;
        return [
            'message' => "perfect a **$match** looking for moroccan produce we work with lots of partners in your space\n\nwhich products interest you most pick as many as you like",
            'options' => [
                'tomatoes (cherry plum round coloured)',
                'citrus (oranges lemons mandarins)',
                'melons / watermelons',
                'peppers (bell coloured)',
                'courgettes / zucchini',
                'berries (strawberries raspberries blueberries)',
                'figs (fresh & dried)',
                'dried fruits (apricots raisins dates almonds)',
                'greenhouse vegetables',
                'dates & nuts',
                'other / specify'
            ],
            'next_step' => 'ask_products',
            'lead_data' => $leadData,
        ];
    }

    private function handleProducts($message, $leadData) {
        $existing = $leadData['products'] ?? [];
        $newProducts = array_filter(array_map('trim', explode(',', $message)));
        $leadData['products'] = array_unique(array_merge($existing, $newProducts));

        return [
            'message' => "great you want **" . implode(', ', $leadData['products']) . "**\n\nwhat sort of volume are you thinking weekly or monthly",
            'options' => [
                'small 1-5 pallets/week',
                'medium 5-20 pallets/week',
                'large 20-50 pallets/week',
                'very large 50+ pallets/week',
                'container loads (20ft/40ft)',
                'not sure yet just exploring'
            ],
            'next_step' => 'ask_volume',
            'lead_data' => $leadData,
        ];
    }

    private function handleVolume($message, $leadData) {
        $leadData['volume'] = $message;

        return [
            'message' => "understood **$message** that helps us lock in the right supply\n\nwhen would you like to start",
            'options' => [
                'asap / immediate',
                'next 2-4 weeks',
                'next month',
                'next season (3-6 months)',
                'planning for next year',
                'just gathering info for now'
            ],
            'next_step' => 'ask_timeline',
            'lead_data' => $leadData,
        ];
    }

    private function handleTimeline($message, $leadData) {
        $leadData['timeline'] = $message;

        return [
            'message' => "got it **$message** we'll make sure the right produce is ready for your window\n\nto send you a proper proposal with pricing and shipping options whats the best email to reach you",
            'options' => [],
            'next_step' => 'ask_contact',
            'lead_data' => $leadData,
        ];
    }

    private function handleContact($message, $leadData) {
        if (!filter_var($message, FILTER_VALIDATE_EMAIL)) {
            return [
                'message' => "that doesnt look like a valid email could you share a working one so our export team can send you a tailored proposal",
                'options' => [],
                'next_step' => 'ask_contact',
                'lead_data' => $leadData,
            ];
        }

        $leadData['contact'] = $message;
        $leadData['qualified_at'] = date('Y-m-d H:i:s');

        $summary = $this->buildSummary($leadData);

        return [
            'message' => "all set your request is complete our team will contact you at **{$leadData['contact']}** within one business day with a tailored offer\n\n---\nyour request summary\n$summary\n---\n\nanything else youd like us to know about your requirements",
            'options' => [
                'add special requirements',
                'ask about certifications',
                'ask about packaging options',
                'ask about payment terms',
                'start new request'
            ],
            'next_step' => 'qualified',
            'lead_data' => $leadData,
        ];
    }

    private function handleQualified($message, $leadData) {
        $lower = strtolower($message);

        if (strpos($lower, 'certif') !== false) {
            return [
                'message' => "we hold **globalgap iso 22000 brc ifs** and can do **eu organic** on request every shipment comes with phytosanitary certificate and certificate of origin need any specific certification details",
                'options' => ['globalgap details', 'organic options', 'other certifications', 'back to main'],
                'next_step' => 'qualified',
                'lead_data' => $leadData,
            ];
        }

        if (strpos($lower, 'packag') !== false) {
            return [
                'message' => "we do **retail clamshells flow-wrap punnets bulk bins private label custom branding** all adapted to your market standards what packaging works for you",
                'options' => ['retail packs (250g-1kg)', 'foodservice bulk (5-10kg)', 'private label / own brand', 'custom specs', 'back to main'],
                'next_step' => 'qualified',
                'lead_data' => $leadData,
            ];
        }

        if (strpos($lower, 'payment') !== false || strpos($lower, 'term') !== false) {
            return [
                'message' => "standard terms **30% deposit 70% against shipping docs (b/l)** for established partners **net 30-60 days** after delivery LC at sight also accepted what works for your business",
                'options' => ['standard terms ok', 'need net 30/60', 'prefer lc', 'discuss custom terms', 'back to main'],
                'next_step' => 'qualified',
                'lead_data' => $leadData,
            ];
        }

        if (strpos($lower, 'special') !== false || strpos($lower, 'require') !== false) {
            return [
                'message' => "just tell us your specific needs varieties grades certifications packaging labeling logistics whatever matters to you and well include it in your proposal",
                'options' => [],
                'next_step' => 'qualified',
                'lead_data' => $leadData,
            ];
        }

        return [
            'message' => "all set your request is complete our team will contact you at **{$leadData['contact']}** within one business day with a tailored offer\n\nin the meantime feel free to browse our catalogue check out our export services or learn more about foodmax group\n\nthanks for considering us as your moroccan produce partner 🙏",
            'options' => [
                'browse products',
                'export services',
                'about us',
                'start new request'
            ],
            'next_step' => 'qualified',
            'lead_data' => $leadData,
        ];
    }

    private function buildSummary($leadData) {
        $lines = [];
        $lines[] = "• **Origin:** {$leadData['origin']}";
        $lines[] = "• **Business type:** {$leadData['domain']}";
        $lines[] = "• **Products:** " . implode(', ', $leadData['products']);
        $lines[] = "• **Volume:** {$leadData['volume']}";
        $lines[] = "• **Timeline:** {$leadData['timeline']}";
        $lines[] = "• **Contact:** {$leadData['contact']}";
        return implode("\n", $lines);
    }

    public function getHistory() {
        return $this->getSession()['history'];
    }

    public function getLeadData() {
        return $this->getSession()['lead_data'];
    }

    public function isQualified() {
        $leadData = $this->getLeadData();
        return !empty($leadData['contact']) && !empty($leadData['origin']);
    }
}