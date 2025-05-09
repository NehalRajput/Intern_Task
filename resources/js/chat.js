import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
/*
// Subscribe to private channel
const userId = document.querySelector('meta[name="user-id"]').content;
window.Echo.private(`chat.${userId}`)
    .listen('MessageSent', (e) => {
        // Handle new message
        const message = e.message;
        appendMessage(message);
    });
*/
// Function to send message
function sendMessage(receiverId, message) {
    axios.post('/chat/send', {
        receiver_id: receiverId,
        message: message,
        receiver_type: document.querySelector('meta[name="user-role"]').content === 'admin' ? 'intern' : 'admin'
    })
    .then(response => {
        appendMessage(response.data.message);
    })
    .catch(error => {
        console.error('Error sending message:', error);
    });
}

// Function to append message to chat
function appendMessage(message) {
    const chatContainer = document.querySelector('.chat-messages');
    const messageElement = document.createElement('div');
    messageElement.classList.add('message');
    messageElement.classList.add(message.sender_id === userId ? 'sent' : 'received');
    
    messageElement.innerHTML = `
        <div class="message-content">
            <p>${message.message}</p>
            <small>${new Date(message.created_at).toLocaleTimeString()}</small>
        </div>
    `;
    
    chatContainer.appendChild(messageElement);
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

// Function to load chat history
function loadChatHistory(userId) {
    axios.get(`/chat/messages/${userId}`)
        .then(response => {
            const messages = response.data.messages;
            const chatContainer = document.querySelector('.chat-messages');
            chatContainer.innerHTML = '';
            
            messages.forEach(message => {
                appendMessage(message);
            });
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
}

// Export functions for use in components
export { sendMessage, loadChatHistory }; 