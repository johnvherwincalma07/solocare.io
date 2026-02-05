<input type="hidden" id="currentUserId" value="{{ auth()->id() }}">

<div id="chat-section">
    <div class="chat-wrapper">
        <div class="chat-conversation" id="conversation-box">
            <div class="conversation-inner" id="conversation-content">
                <p class="empty-text">💬 Start chatting with admin</p>
            </div>
            <div class="chat-input">
                <input type="text" id="chatInput" placeholder="Type your message..." />
                <button id="sendChatBtn">Send</button>
            </div>
        </div>
    </div>
</div>

@vite('resources/js/chat.js')
