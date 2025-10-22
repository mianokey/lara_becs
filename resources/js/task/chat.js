export function sendMessage(taskId) {
    const input = document.getElementById('newMessage');
    const message = input.value.trim();
    if (!message) return;

    fetch(`/tasks/${taskId}/messages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ message })
    })
    .then(res => res.json())
    .then(data => {
        const chatBox = document.getElementById('chatBox');
        const msgEl = document.createElement('div');
        msgEl.classList.add('my-2', 'text-right');
        msgEl.innerHTML = `<span class="px-3 py-2 rounded bg-blue-200">${data.message}</span>`;
        chatBox.appendChild(msgEl);
        chatBox.scrollTop = chatBox.scrollHeight;
        input.value = '';
    });
}
