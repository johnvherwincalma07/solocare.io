<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solo Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>



    <script>AOS.init();</script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">


</head>
<body>
<div class="page-wrapper">
  <!-- NAVBAR -->
    @include('layouts.partials.navbar')
    <!-- PAGE CONTENT -->
    <main>
        @yield('content')
    </main>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/user.js') }}"></script>

<!-- ✅ Floating Chat Button -->
<div id="chat-button"
     style="position: fixed; bottom: 20px; right: 20px; background: #007bff; color: white;
            border-radius: 50px; padding: 12px 20px; cursor: pointer; font-weight: 600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2); z-index: 9999;">
  💬 Chat with Admin
</div>

<!-- ✅ Chat Popup -->
<div id="chat-popup"
     style="display: none; position: fixed; bottom: 80px; right: 20px; width: 320px; height: 420px;
            background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            overflow: hidden; z-index: 10000; flex-direction: column;">

  <div style="background: #007bff; color: white; padding: 10px 15px; display: flex;
              justify-content: space-between; align-items: center;">
      <strong>Admin Chat</strong>
      <span id="close-chat" style="cursor: pointer;">✖</span>
  </div>

  <div id="chat-messages" style="flex: 1; padding: 10px; overflow-y: auto;">
      <div style="background:#e6f0ff; padding:8px 10px; border-radius:8px; margin-bottom:8px;">
          👋 Hi! How can I help you today?
      </div>
  </div>

  <form id="chat-form" style="display: flex; border-top: 1px solid #ddd;">
      <input type="text" id="chat-input" placeholder="Type a message..."
             style="flex: 1; padding: 10px; border: none; outline: none;">
      <button type="submit"
              style="background:#007bff; color:white; border:none; padding: 0 20px;">Send</button>
  </form>
</div>
</div>
<script>
  // --- toggle open / close ---
  const chatButton = document.getElementById('chat-button');
  const chatPopup = document.getElementById('chat-popup');
  const closeChat = document.getElementById('close-chat');
  const chatForm = document.getElementById('chat-form');
  const chatInput = document.getElementById('chat-input');
  const chatMessages = document.getElementById('chat-messages');

  chatButton.addEventListener('click', () => {
      chatPopup.style.display = 'flex';
      chatButton.style.display = 'none';
  });

  closeChat.addEventListener('click', () => {
      chatPopup.style.display = 'none';
      chatButton.style.display = 'block';
  });

  // --- handle message sending ---
  chatForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const msg = chatInput.value.trim();
      if (!msg) return;

      const userMsg = document.createElement('div');
      userMsg.textContent = msg;
      userMsg.style.cssText =
        "background:#f1f1f1;padding:8px 10px;border-radius:8px;margin-bottom:8px;text-align:right;";
      chatMessages.appendChild(userMsg);
      chatInput.value = '';

      setTimeout(() => {
          const reply = document.createElement('div');
          reply.textContent = "Thanks for your message! We'll reply soon.";
          reply.style.cssText =
            "background:#e6f0ff;padding:8px 10px;border-radius:8px;margin-bottom:8px;text-align:left;";
          chatMessages.appendChild(reply);
          chatMessages.scrollTop = chatMessages.scrollHeight;
      }, 800);

      chatMessages.scrollTop = chatMessages.scrollHeight;
  });
</script>


</body>
</html>
