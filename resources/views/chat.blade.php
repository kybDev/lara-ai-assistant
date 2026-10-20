<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Assistant</title>
    <style>
        :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color: #172033; background: #f4f6f9; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; }
        .chat-shell { display: flex; flex-direction: column; width: min(100%, 960px); height: 100vh; margin: 0 auto; background: #fff; box-shadow: 0 0 32px #15233b0d; }
        .chat-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid #e8ebf0; }
        .chat-brand { display: flex; align-items: center; gap: .8rem; }
        .chat-mark { display: grid; width: 2.5rem; height: 2.5rem; place-items: center; border-radius: 50%; background: #e9f1ff; color: #2459a6; font-size: 1.25rem; }
        h1 { margin: 0; font-size: 1rem; }
        .chat-subtitle { margin: .2rem 0 0; color: #6b7280; font-size: .8rem; }
        .chat-header a, .clear-button { border: 0; background: none; color: #315b94; font: inherit; text-decoration: none; cursor: pointer; }
        .chat-notice { margin: 1rem auto 0; padding: .65rem 1rem; border-radius: .65rem; background: #f2f6fc; color: #526174; font-size: .82rem; }
        .chat-messages { display: flex; flex: 1; flex-direction: column; gap: 1rem; overflow-y: auto; padding: 1.5rem; }
        .message { max-width: min(80%, 680px); padding: .85rem 1rem; border-radius: 1rem; line-height: 1.55; white-space: pre-wrap; overflow-wrap: anywhere; }
        .message.assistant { align-self: flex-start; background: #f1f3f6; }
        .message.user { align-self: flex-end; background: #2459a6; color: white; }
        .message.error { align-self: center; max-width: 100%; background: #fff0f0; color: #a32626; }
        .chat-empty { margin: auto; color: #657187; text-align: center; }
        .chat-empty h2 { color: #243047; font-size: 1.4rem; }
        .chat-form { padding: 1rem 1.5rem 1.25rem; border-top: 1px solid #e8ebf0; }
        .composer { display: flex; align-items: flex-end; gap: .65rem; padding: .6rem .65rem .6rem 1rem; border: 1px solid #d8dee8; border-radius: 1rem; box-shadow: 0 2px 8px #15233b0a; }
        textarea { flex: 1; min-height: 2.5rem; max-height: 9rem; resize: vertical; border: 0; outline: 0; font: inherit; line-height: 1.5; }
        .send-button { min-width: 2.5rem; min-height: 2.5rem; border: 0; border-radius: .75rem; background: #2459a6; color: white; font: inherit; cursor: pointer; }
        .send-button:disabled { opacity: .55; cursor: wait; }
        .composer-hint { margin: .6rem 0 0; color: #8992a1; font-size: .72rem; text-align: center; }
        @media (max-width: 600px) { .chat-header { padding: .8rem 1rem; } .chat-messages { padding: 1rem; } .chat-form { padding: .8rem 1rem; } .message { max-width: 92%; } }
    </style>
</head>
<body>
    <main class="chat-shell">
        <header class="chat-header">
            <div class="chat-brand">
                <span class="chat-mark" aria-hidden="true">✦</span>
                <div>
                    <h1>AI Assistant</h1>
                    <p class="chat-subtitle">Local model: {{ $model }}</p>
                </div>
            </div>
            <div>
                <button class="clear-button" id="clear-chat" type="button">New chat</button>
                <a href="{{ url('/') }}" aria-label="Return to application home">Close</a>
            </div>
        </header>

        <div class="chat-notice">Responses are generated locally. This assistant is not connected to application database records.</div>

        <section class="chat-messages" id="chat-messages" aria-live="polite" aria-label="Conversation">
            <div class="chat-empty" id="chat-empty">
                <h2>How can I help?</h2>
                <p>Ask a question to start a conversation with the local assistant.</p>
            </div>
        </section>

        <form class="chat-form" id="chat-form">
            <div class="composer">
                <textarea id="chat-input" name="message" rows="1" maxlength="8000" placeholder="Message the assistant..." aria-label="Message" required></textarea>
                <button class="send-button" id="send-button" type="submit" aria-label="Send message">↑</button>
            </div>
            <p class="composer-hint">Enter to send · Shift + Enter for a new line · Conversation is not saved</p>
        </form>
    </main>

    <script>
        (() => {
            const form = document.getElementById('chat-form');
            const input = document.getElementById('chat-input');
            const sendButton = document.getElementById('send-button');
            const messagesElement = document.getElementById('chat-messages');
            const emptyState = document.getElementById('chat-empty');
            const conversation = [];

            const appendMessage = (role, content, extraClass = '') => {
                emptyState.hidden = true;
                const message = document.createElement('div');
                message.className = `message ${role} ${extraClass}`.trim();
                message.textContent = content;
                messagesElement.appendChild(message);
                messagesElement.scrollTop = messagesElement.scrollHeight;
                return message;
            };

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const content = input.value.trim();
                if (!content || sendButton.disabled) return;

                conversation.push({ role: 'user', content });
                if (conversation.length > 40) conversation.splice(0, conversation.length - 40);
                appendMessage('user', content);
                input.value = '';
                sendButton.disabled = true;
                const pending = appendMessage('assistant', 'Thinking…');

                try {
                    const response = await fetch(@json($messageUrl), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ messages: conversation }),
                    });
                    const data = await response.json();
                    pending.remove();
                    if (!response.ok) throw new Error(data.message || 'The assistant request failed.');
                    conversation.push({ role: 'assistant', content: data.message });
                    appendMessage('assistant', data.message);
                } catch (error) {
                    conversation.pop();
                    appendMessage('assistant', error.message || 'Unable to contact the assistant.', 'error');
                } finally {
                    sendButton.disabled = false;
                    input.focus();
                }
            });

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    form.requestSubmit();
                }
            });

            document.getElementById('clear-chat').addEventListener('click', () => {
                conversation.length = 0;
                messagesElement.querySelectorAll('.message').forEach((message) => message.remove());
                emptyState.hidden = false;
                input.focus();
            });
        })();
    </script>
</body>
</html>
