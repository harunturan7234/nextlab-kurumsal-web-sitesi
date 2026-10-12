/**
 * NextLab AI Chatbot Client Controller
 * Provides real-time interaction, quick-chips, session persistence,
 * responsive layout, and seamless communication with /api/chat.
 */

(function () {
  'use strict';

  function initChatbot() {
    const launcherBtn = document.getElementById('chatbot-launcher-btn');
    const panel = document.getElementById('chatbot-panel');
    const closeBtn = document.getElementById('chatbot-close-btn');
    const resetBtn = document.getElementById('chatbot-reset-btn');
    const form = document.getElementById('chatbot-form');
    const input = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send-btn');
    const messagesContainer = document.getElementById('chatbot-messages');
    const typingIndicator = document.getElementById('chatbot-typing');
    const suggestionsContainer = document.getElementById('chatbot-suggestions');
    const charCount = document.getElementById('chatbot-char-count');

    if (!launcherBtn || !panel || !form || !input) return;

    // Chat conversation memory in current session
    let conversationHistory = [];
    let isWaitingResponse = false;
    const STORAGE_KEY = 'nextlab_chat_history_v2';
    const INITIAL_WELCOME_TEXT = 'Merhaba! 👋 Ben Nextlab Akıllı Asistanı. İstediğinizi sorabilirsiniz ?';

    // Format local time HH:MM
    function getCurrentTimeString() {
      const now = new Date();
      return now.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
    }

    // Markdown-lite formatter
    function formatMarkdown(text) {
      if (!text) return '';
      let escaped = text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

      // Links: [label](url)
      escaped = escaped.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+|\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="chatbot-link">$1 ↗</a>');

      // Bold: **text**
      escaped = escaped.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');

      // Italic: *text*
      escaped = escaped.replace(/(^|[^*])\*([^*]+)\*(?=[^*]|$)/g, '$1<em>$2</em>');

      // Bullet points: • or -
      const lines = escaped.split('\n');
      let inList = false;
      let html = '';

      lines.forEach((line) => {
        const trimmed = line.trim();
        const bulletMatch = trimmed.match(/^([•\-\*]|\d+\.)\s+(.+)$/);

        if (bulletMatch) {
          if (!inList) {
            html += '<ul class="chatbot-msg-list">';
            inList = true;
          }
          html += `<li>${bulletMatch[2]}</li>`;
        } else {
          if (inList) {
            html += '</ul>';
            inList = false;
          }
          if (trimmed.length > 0) {
            html += `<p>${line}</p>`;
          }
        }
      });

      if (inList) {
        html += '</ul>';
      }

      return html;
    }

    // Append message to UI
    function appendMessage(role, text, time) {
      const msgDiv = document.createElement('div');
      msgDiv.className = `chatbot-msg chatbot-msg-${role === 'user' ? 'user' : 'bot'}`;

      const timeStr = time || getCurrentTimeString();

      if (role === 'user') {
        msgDiv.innerHTML = `
          <div class="chatbot-msg-body">
            <div class="chatbot-msg-text">
              <p>${escapeHtml(text)}</p>
            </div>
            <span class="chatbot-msg-time">${timeStr}</span>
          </div>
        `;
      } else {
        msgDiv.innerHTML = `
          <div class="chatbot-msg-avatar">
            <span>NL</span>
          </div>
          <div class="chatbot-msg-body">
            <div class="chatbot-msg-sender">Nextlab Akıllı Asistanı</div>
            <div class="chatbot-msg-text">
              ${formatMarkdown(text)}
            </div>
            <span class="chatbot-msg-time">${timeStr}</span>
          </div>
        `;
      }

      messagesContainer.appendChild(msgDiv);
      scrollToBottom();
    }

    function escapeHtml(string) {
      const div = document.createElement('div');
      div.innerText = string;
      return div.innerHTML;
    }

    function scrollToBottom() {
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Toggle panel
    function toggleChatbot(forceState) {
      const isVisible = panel.style.display !== 'none';
      const shouldOpen = typeof forceState === 'boolean' ? forceState : !isVisible;

      if (shouldOpen) {
        panel.style.display = 'flex';
        panel.classList.add('is-open');
        launcherBtn.classList.add('is-active');
        launcherBtn.setAttribute('aria-expanded', 'true');
        const iconOpen = launcherBtn.querySelector('.icon-open');
        const iconClose = launcherBtn.querySelector('.icon-close');
        if (iconOpen) iconOpen.style.display = 'none';
        if (iconClose) iconClose.style.display = 'block';

        // Scroll to bottom & focus input
        setTimeout(() => {
          scrollToBottom();
          input.focus();
        }, 150);
      } else {
        panel.classList.remove('is-open');
        setTimeout(() => {
          panel.style.display = 'none';
        }, 200);
        launcherBtn.classList.remove('is-active');
        launcherBtn.setAttribute('aria-expanded', 'false');
        const iconOpen = launcherBtn.querySelector('.icon-open');
        const iconClose = launcherBtn.querySelector('.icon-close');
        if (iconOpen) iconOpen.style.display = 'block';
        if (iconClose) iconClose.style.display = 'none';
        launcherBtn.focus();
      }
    }

    // Save history to session
    function saveHistory() {
      try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(conversationHistory));
      } catch (e) {
        // Storage unavailable or quota
      }
    }

    // Load history from session
    function loadHistory() {
      try {
        const raw = sessionStorage.getItem(STORAGE_KEY);
        if (raw) {
          const parsed = JSON.parse(raw);
          if (Array.isArray(parsed) && parsed.length > 0) {
            conversationHistory = parsed;
            // Clear default messages and render loaded history
            messagesContainer.innerHTML = '';
            // Always ensure the first greeting is there if not in history
            const firstIsBot = parsed[0] && parsed[0].role === 'model';
            if (!firstIsBot) {
              appendMessage('model', INITIAL_WELCOME_TEXT, 'Şimdi');
            }
            parsed.forEach((m) => {
              appendMessage(m.role, m.text, m.time);
            });
          }
        }
      } catch (e) {
        // ignore
      }
    }

    // Send query to server
    async function sendMessage(text) {
      if (!text || !text.trim() || isWaitingResponse) return;
      const cleanText = text.trim();

      // Clear input & reset height
      input.value = '';
      input.style.height = 'auto';
      if (charCount) charCount.textContent = '0/800';

      // 1. Show user message
      const timeNow = getCurrentTimeString();
      appendMessage('user', cleanText, timeNow);
      conversationHistory.push({ role: 'user', text: cleanText, time: timeNow });
      saveHistory();

      // 2. Show typing indicator
      isWaitingResponse = true;
      sendBtn.disabled = true;
      typingIndicator.style.display = 'flex';
      scrollToBottom();

      try {
        const response = await fetch('/api/chat', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            message: cleanText,
            history: conversationHistory.slice(-8)
          })
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        const replyText = data.text || 'Sorunuz alındı ancak şu anda yanıt oluşturulamadı. Lütfen tekrar deneyin.';

        // 3. Show bot reply
        const replyTime = getCurrentTimeString();
        appendMessage('model', replyText, replyTime);
        conversationHistory.push({ role: 'model', text: replyText, time: replyTime });
        saveHistory();

      } catch (err) {
        console.error('Chat error:', err);
        const errorText = 'Bağlantı sırasında bir aksaklık oluştu. Lütfen tekrar deneyin veya doğrudan **info@nextlabcosmetic.com** adresinden bize ulaşabilirsiniz.';
        appendMessage('model', errorText, getCurrentTimeString());
      } finally {
        isWaitingResponse = false;
        sendBtn.disabled = false;
        typingIndicator.style.display = 'none';
        scrollToBottom();
      }
    }

    // Event listeners
    launcherBtn.addEventListener('click', () => toggleChatbot());
    closeBtn.addEventListener('click', () => toggleChatbot(false));

    // Reset button
    resetBtn.addEventListener('click', () => {
      if (confirm('Sohbet geçmişini sıfırlamak istiyor musunuz?')) {
        conversationHistory = [];
        try {
          sessionStorage.removeItem(STORAGE_KEY);
        } catch (e) {}

        // Restore initial single welcome message
        messagesContainer.innerHTML = `
          <div class="chatbot-msg chatbot-msg-bot">
            <div class="chatbot-msg-avatar">
              <span>NL</span>
            </div>
            <div class="chatbot-msg-body">
              <div class="chatbot-msg-sender">Nextlab Akıllı Asistanı</div>
              <div class="chatbot-msg-text">
                <p>${INITIAL_WELCOME_TEXT}</p>
              </div>
              <span class="chatbot-msg-time" data-time="now">Şimdi</span>
            </div>
          </div>
        `;
        scrollToBottom();
      }
    });

    // Form submit
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      sendMessage(input.value);
    });

    // Enter to submit, Shift+Enter for newline
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage(input.value);
      }
    });

    // Auto-resize textarea
    input.addEventListener('input', () => {
      input.style.height = 'auto';
      input.style.height = Math.min(input.scrollHeight, 110) + 'px';
      if (charCount) {
        charCount.textContent = `${input.value.length}/800`;
      }
    });

    // Quick suggestion chips
    if (suggestionsContainer) {
      suggestionsContainer.addEventListener('click', (e) => {
        const chip = e.target.closest('.chatbot-chip');
        if (chip) {
          const query = chip.getAttribute('data-query');
          if (query) {
            sendMessage(query);
          }
        }
      });
    }

    // Escape key to close
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && panel.style.display !== 'none') {
        toggleChatbot(false);
      }
    });

    // Load saved conversation
    loadHistory();
  }

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initChatbot);
  } else {
    initChatbot();
  }
})();
