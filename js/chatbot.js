/**
 * FoodMax Group Chatbot Widget
 * Floating chat widget for website
 */
(function() {
    'use strict';

    // Use fm_app if available, otherwise fallback
    const API_URL = (window.fm_app || '') + 'api/chatbot/';
    const STORAGE_KEY = 'fm_chatbot_state';

    let isOpen = false;
    let isMinimized = false;
    let sessionData = null;

    // Create widget HTML
    function createWidget() {
        const widget = document.createElement('div');
        widget.id = 'fm-chatbot';
        widget.innerHTML = `
            <style>
                #fm-chatbot { position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: 'Raleway', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
                #fm-chatbot * { box-sizing: border-box; }
                .fm-chat-toggle { width: 60px; height: 60px; border-radius: 50%; background: #4a7a18; border: none; cursor: pointer; box-shadow: 0 4px 20px rgba(74,122,24,0.3); display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
                .fm-chat-toggle:hover { background: #2f5010; transform: scale(1.05); }
                .fm-chat-toggle i { font-size: 24px; color: white; }
                .fm-chat-toggle .fm-close-icon { display: none; }
                .fm-chat-toggle.open .fm-chat-icon { display: none; }
                .fm-chat-toggle.open .fm-close-icon { display: block; }
                .fm-chat-badge { position: absolute; top: -5px; right: -5px; background: #a13a20; color: white; font-size: 11px; font-weight: 700; min-width: 18px; height: 18px; border-radius: 9px; display: flex; align-items: center; justify-content: center; padding: 0 5px; }
                .fm-chat-window { position: absolute; bottom: 80px; right: 0; width: 380px; height: 550px; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); display: none; flex-direction: column; overflow: hidden; border: 1px solid #e3e8da; }
                .fm-chat-window.open { display: flex; animation: fmSlideUp 0.3s ease; }
                @keyframes fmSlideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
                .fm-chat-header { background: #4a7a18; color: white; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; }
                .fm-chat-header h3 { margin: 0; font-size: 16px; font-weight: 700; }
                .fm-chat-header .fm-status { font-size: 11px; opacity: 0.9; }
                .fm-chat-minimize { background: none; border: none; color: white; cursor: pointer; padding: 4px; display: flex; }
                .fm-chat-messages { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 16px; background: #fafbf9; }
                .fm-msg { max-width: 85%; animation: fmFadeIn 0.3s ease; }
                @keyframes fmFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
                .fm-msg.bot { align-self: flex-start; }
                .fm-msg.user { align-self: flex-end; }
                .fm-msg-bubble { padding: 12px 16px; border-radius: 18px; font-size: 14px; line-height: 1.5; }
                .fm-msg.bot .fm-msg-bubble { background: white; border: 1px solid #e3e8da; border-bottom-left-radius: 4px; color: #33382c; }
                .fm-msg.user .fm-msg-bubble { background: #4a7a18; color: white; border-bottom-right-radius: 4px; }
                .fm-msg-options { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
                .fm-option-btn { background: #edf2e2; border: 1px solid #d2dac4; color: #4a7a18; padding: 8px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
                .fm-option-btn:hover { background: #4a7a18; color: white; border-color: #4a7a18; }
                .fm-chat-input-area { padding: 16px 20px; border-top: 1px solid #e3e8da; background: white; }
                .fm-input-row { display: flex; gap: 8px; }
                .fm-chat-input { flex: 1; border: 1px solid #d2dac4; border-radius: 24px; padding: 12px 18px; font-size: 14px; outline: none; transition: border-color 0.2s; }
                .fm-chat-input:focus { border-color: #4a7a18; }
                .fm-send-btn { width: 44px; height: 44px; border-radius: 50%; background: #4a7a18; border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s; flex-shrink: 0; }
                .fm-send-btn i { font-size: 18px; }
                .fm-send-btn:hover { background: #2f5010; }
                .fm-send-btn:disabled { background: #a8c472; cursor: not-allowed; }
                .fm-chat-footer { padding: 0 20px 16px; text-align: center; }
                .fm-chat-footer a { color: #4a7a18; font-size: 12px; font-weight: 600; text-decoration: none; }
                .fm-chat-footer a:hover { text-decoration: underline; }
                .fm-typing { display: flex; gap: 4px; padding: 8px 0; }
                .fm-typing span { width: 8px; height: 8px; background: #4a7a18; border-radius: 50%; animation: fmBounce 1.4s infinite ease-in-out both; }
                .fm-typing span:nth-child(1) { animation-delay: -0.32s; }
                .fm-typing span:nth-child(2) { animation-delay: -0.16s; }
                @keyframes fmBounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
                @media (max-width: 480px) {
                    .fm-chat-window { width: calc(100vw - 40px); height: calc(100vh - 100px); bottom: 80px; right: -10px; border-radius: 12px 12px 0 0; }
                    #fm-chatbot { bottom: 10px; right: 10px; }
                }
            </style>
            <button class="fm-chat-toggle" aria-label="Open chat" title="Chat with FoodMax">
                <i class="fa fa-comments fm-chat-icon" aria-hidden="true"></i>
                <i class="fa fa-times fm-close-icon" aria-hidden="true"></i>
                <span class="fm-chat-badge" style="display:none;">1</span>
            </button>
            <div class="fm-chat-window" role="dialog" aria-label="FoodMax Group Chat Assistant">
                <div class="fm-chat-header">
                    <div>
                        <h3>FoodMax Assistant</h3>
                        <span class="fm-status">Typically replies in minutes</span>
                    </div>
                    <button class="fm-chat-minimize" aria-label="Minimize" title="Minimize">
                        <i class="fa fa-minus" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="fm-chat-messages" id="fmChatMessages"></div>
                <div class="fm-chat-input-area">
                    <div class="fm-input-row">
                        <input type="text" class="fm-chat-input" placeholder="Type your message..." aria-label="Message" autocomplete="off">
                        <button class="fm-send-btn" aria-label="Send" disabled>
                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="fm-chat-footer">
                    <a href="/contact/" target="_blank">Contact our export team directly →</a>
                </div>
            </div>
        `;
        document.body.appendChild(widget);
        return widget;
    }

    // Initialize widget
    function init() {
        const widget = createWidget();
        const toggleBtn = widget.querySelector('.fm-chat-toggle');
        const windowEl = widget.querySelector('.fm-chat-window');
        const minimizeBtn = widget.querySelector('.fm-chat-minimize');
        const inputEl = widget.querySelector('.fm-chat-input');
        const sendBtn = widget.querySelector('.fm-send-btn');
        const messagesEl = widget.querySelector('#fmChatMessages');
        const badgeEl = widget.querySelector('.fm-chat-badge');

        // Load session from localStorage
        loadSession();

        // Sync with server immediately (async but fast)
        const syncPromise = callAPI('init', {}).then(res => {
            // Hide badge if visitor has completed a qualified conversation
            if (res.has_qualified_conversation) {
                badgeEl.style.display = 'none';
            }
            // Always reset to welcome on page load - clear localStorage if server says welcome
            if (res.step === 'welcome') {
                localStorage.removeItem(STORAGE_KEY);
                sessionData = null;
            }
        }).catch(() => {});

        // Toggle chat
        toggleBtn.addEventListener('click', async () => {
            // Wait for sync to complete before checking sessionData
            await syncPromise;
            
            isOpen = !isOpen;
            windowEl.classList.toggle('open', isOpen);
            toggleBtn.classList.toggle('open', isOpen);
            badgeEl.style.display = 'none';
            if (isOpen) {
                inputEl.focus();
                if (!sessionData) {
                    startConversation();
                } else {
                    renderHistory();
                }
            }
        });

        // Minimize
        minimizeBtn.addEventListener('click', () => {
            isOpen = false;
            windowEl.classList.remove('open');
            toggleBtn.classList.remove('open');
        });

        // Send message
        function sendMessage() {
            const text = inputEl.value.trim();
            if (!text) return;
            // Check if last message has options - prevent free text
            const lastBotMsg = document.querySelector('.fm-msg.bot:last-of-type');
            const hasOptions = lastBotMsg && lastBotMsg.querySelector('.fm-msg-options');
            if (hasOptions) {
                // Show hint
                addMessage('bot', "please choose one of the options above");
                inputEl.value = '';
                sendBtn.disabled = true;
                // Remove hint after 2 seconds
                setTimeout(() => {
                    const lastMsg = messagesEl.querySelector('.fm-msg.bot:last-of-type');
                    if (lastMsg && lastMsg.textContent.toLowerCase().includes('please choose')) {
                        lastMsg.remove();
                    }
                }, 2000);
                return;
            }
            addMessage('user', text);
            inputEl.value = '';
            sendBtn.disabled = true;
            showTyping();
            
            const needsGeo = sessionData?.needsGeo === true;
            const sendData = { message: text };
            if (needsGeo) {
                fetchGeoInfo().then(geo => {
                    sendData.geo = geo;
                    doSend(sendData);
                });
            } else {
                doSend(sendData);
            }
        }

        function doSend(data) {
            callAPI('send', data).then(res => {
                hideTyping();
                if (res.error) {
                    addMessage('bot', 'Sorry, something went wrong. Please try again.');
                } else {
                    addMessage('bot', res.message, res.options);
                    updateSession(res);
                }
            }).catch(() => {
                hideTyping();
                addMessage('bot', 'Connection error. Please check your internet and try again.');
            });
        }

        sendBtn.addEventListener('click', sendMessage);
        inputEl.addEventListener('input', () => {
            // Disable if options are present
            const lastBotMsg = document.querySelector('.fm-msg.bot:last-of-type');
            const hasOptions = lastBotMsg && lastBotMsg.querySelector('.fm-msg-options');
            sendBtn.disabled = hasOptions || !inputEl.value.trim();
        });
        inputEl.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });

        // Option button delegation
        messagesEl.addEventListener('click', (e) => {
            if (e.target.classList.contains('fm-option-btn')) {
                const text = e.target.textContent;
                // Handle navigation actions
                const navMap = {
                    'browse products': '/products/',
                    'export services': '/export-services/',
                    'about us': '/about-us/',
                };
                if (navMap[text.toLowerCase()]) {
                    window.location.href = navMap[text.toLowerCase()];
                    return;
                }
                // Handle start new request via API
                if (text.toLowerCase() === 'start new request') {
                    addMessage('user', text);
                    showTyping();
                    callAPI('send', { message: text }).then(res => {
                        hideTyping();
                        if (res.error) {
                            addMessage('bot', 'Sorry, something went wrong. Please try again.');
                        } else {
                            addMessage('bot', res.message, res.options);
                            updateSession(res);
                        }
                    });
                    return;
                }
                // Regular message - fetch geo if first interaction
                addMessage('user', text);
                showTyping();
                const needsGeo = sessionData?.needsGeo === true;
                const sendData = { message: text };
                if (needsGeo) {
                    fetchGeoInfo().then(geo => {
                        sendData.geo = geo;
                        doSend(sendData);
                    });
                } else {
                    doSend(sendData);
                }
            }
        });

        // Disable input if last bot message has options? Show placeholder hint
        inputEl.addEventListener('focus', () => {
            const lastBotMsg = document.querySelector('.fm-msg.bot:last-of-type');
            const hasOptions = lastBotMsg && lastBotMsg.querySelector('.fm-msg-options');
            if (hasOptions) {
                inputEl.placeholder = 'please select an option above';
            } else {
                inputEl.placeholder = 'type your message...';
            }
        });

        // Show notification badge from the start so visitors see there's a message waiting
        badgeEl.textContent = '1';
        badgeEl.style.display = 'flex';
    }

    function startConversation() {
        // Show welcome message locally FIRST (no API call)
        // API will be called on first real user interaction
        const welcomeMsg = "hi there welcome to foodmax group 👋 i'm here to help you find the best moroccan fruits and vegetables for your market\n\nto give you the right recommendations could you tell me where you're based";
        const welcomeOptions = [
            'europe (eu)',
            'middle east / gulf',
            'north america',
            'asia / pacific',
            'africa',
            'other'
        ];
        addMessage('bot', welcomeMsg, welcomeOptions);
        // Mark that we need to send geo on first real interaction
        sessionData = {
            step: 'welcome',
            lead_data: {
                'origin': '',
                'domain': '',
                'products': [],
                'volume': '',
                'timeline': '',
                'contact': '',
            },
            history: [],
            needsGeo: true
        };
        saveSession();
    }

    function addMessage(role, text, options = null) {
        const messagesEl = document.getElementById('fmChatMessages');
        const msgDiv = document.createElement('div');
        msgDiv.className = `fm-msg ${role}`;
        const bubble = document.createElement('div');
        bubble.className = 'fm-msg-bubble';
        // Convert markdown-like formatting
        bubble.innerHTML = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
        msgDiv.appendChild(bubble);
        if (options && options.length > 0) {
            const optsDiv = document.createElement('div');
            optsDiv.className = 'fm-msg-options';
            options.forEach(opt => {
                const btn = document.createElement('button');
                btn.className = 'fm-option-btn';
                btn.textContent = opt;
                optsDiv.appendChild(btn);
            });
            msgDiv.appendChild(optsDiv);
        }
        messagesEl.appendChild(msgDiv);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function showTyping() {
        const messagesEl = document.getElementById('fmChatMessages');
        const typingDiv = document.createElement('div');
        typingDiv.className = 'fm-msg bot fm-typing-msg';
        typingDiv.innerHTML = '<div class="fm-msg-bubble"><div class="fm-typing"><span></span><span></span><span></span></div></div>';
        messagesEl.appendChild(typingDiv);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function hideTyping() {
        const typingMsg = document.querySelector('.fm-typing-msg');
        if (typingMsg) typingMsg.remove();
    }

    function renderHistory() {
        const messagesEl = document.getElementById('fmChatMessages');
        messagesEl.innerHTML = '';
        sessionData.history.forEach(item => {
            if (item.role === 'user') {
                addMessage('user', item.message);
            } else if (item.role === 'bot') {
                addMessage('bot', item.message, item.options);
            }
        });
    }

    function callAPI(action, data) {
        return fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, ...data }),
            credentials: 'same-origin'
        }).then(r => r.json());
    }

    function updateSession(res) {
        // Clear session if conversation is qualified (completed)
        if (res.qualified) {
            localStorage.removeItem(STORAGE_KEY);
            sessionData = null;
            return;
        }
        sessionData = {
            step: res.step,
            lead_data: res.lead_data,
            history: [...(sessionData?.history || []),
                { role: 'user', message: '' }, // placeholder
                { role: 'bot', message: res.message, options: res.options }
            ],
            needsGeo: false  // Geo sent, no longer needed
        };
        saveSession();
    }

    function saveSession() {
        if (sessionData) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(sessionData));
        }
    }

    function loadSession() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                sessionData = JSON.parse(stored);
            }
        } catch (e) {
            console.warn('Chatbot session load failed:', e);
        }
    }

    // Expose globally for manual control
    window.FM_Chatbot = {
        open: () => { if (!isOpen) document.querySelector('.fm-chat-toggle').click(); },
        close: () => { if (isOpen) document.querySelector('.fm-chat-toggle').click(); },
        reset: () => {
            localStorage.removeItem(STORAGE_KEY);
            sessionData = null;
            callAPI('reset', {});
        }
    };

    // Fetch visitor geo info from ipwho.is
    async function fetchGeoInfo() {
        try {
            const response = await fetch('https://ipwho.is/');
            const data = await response.json();
            if (data.success) {
                return {
                    city: data.city || '',
                    country: data.country || '',
                    ip: data.ip || ''
                };
            }
        } catch (e) {
            console.warn('Geo fetch failed:', e);
        }
        return { city: '', country: '', ip: '' };
    }

    // Auto-init when DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();