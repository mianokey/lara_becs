export function initNotifications() {
    const bell = document.getElementById('notificationBell');
    const badge = document.getElementById('notificationCount');

    // fetch unread count every 10 seconds
    setInterval(() => {
        fetch('/notifications/unread')
            .then(res => res.json())
            .then(data => {
                badge.innerText = data.unreadCount;
            });
    }, 10000);

    bell.addEventListener('click', () => {
        // optionally play a tone
        const audio = new Audio('/assets/notification.wav');
        audio.play();

        // open modal for the latest task
        if (data.latestTaskId) {
            openTaskModal(data.latestTaskId);
        }
    });
}
