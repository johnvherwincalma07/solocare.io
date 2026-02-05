// server.js
const express = require('express');
const app = express();
const http = require('http').createServer(app);
const io = require('socket.io')(http);
const bodyParser = require('body-parser');
const path = require('path');

app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, 'public')));

let messages = []; // {userId, sender:'user'|'admin', text}

// Socket.io
io.on('connection', (socket) => {
  console.log('A user connected');

  // Join user room
  socket.on('join_chat', (room) => {
    socket.join(room);
    console.log(`Joined room: ${room}`);

    // Send chat history for that room
    const history = messages.filter(m => m.userId === room);
    socket.emit('chat_history', history);
  });

  // Listen for messages from user or admin
  socket.on('send_message', (data) => {
    const { userId, sender, text } = data;
    const msg = { userId, sender, text };
    messages.push(msg);

    // Emit to user and admin
    io.to(userId).emit('new_message', msg); // user sees it
    io.to('admin_room').emit('new_message', msg); // admin sees it
  });
});

const PORT = 3000;
http.listen(PORT, () => console.log(`Server running on http://localhost:${PORT}`));
