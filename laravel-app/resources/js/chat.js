// resources/js/chat.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Setup Pusher
window.Pusher = Pusher;

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

document.addEventListener("DOMContentLoaded", () => {
    const conversationBox = document.getElementById("chatWindow");
    const input = document.getElementById("chatInput");
    const sendBtn = document.getElementById("sendChatBtn");
    const userId = parseInt(document.getElementById("currentUserId").value);
    const adminId = 1; // Replace if needed

    // Send message
    sendBtn.addEventListener("click", () => sendMessage());
    input.addEventListener("keypress", (e) => {
        if(e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        fetch("/chat/send", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: text,
                receiver_id: adminId
            })
        })
        .then(res => res.json())
        .then(msg => {
            appendMessage(msg);
            input.value = "";
        })
        .catch(console.error);
    }

    function appendMessage(msg) {
        const isAdmin = msg.sender_id === adminId;
        const div = document.createElement("div");
        div.classList.add("message", isAdmin ? "admin-msg" : "user-msg");

        div.innerHTML = `
            ${isAdmin ? `<img src="/images/solocare.jpg" class="msg-avatar">` : ''}
            <div class="msg-bubble">${msg.message}</div>
            ${!isAdmin ? `<img src="/images/user-avatar.jpg" class="msg-avatar">` : ''}
        `;

        conversationBox.appendChild(div);
        conversationBox.scrollTop = conversationBox.scrollHeight;
    }

    // Listen for real-time messages
    window.Echo.private(`chat.${userId}`)
        .listen('NewMessage', (e) => {
            appendMessage(e.message);
        });
});
