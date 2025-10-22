import "./bootstrap";

// 🔔 Notification sound
const notificationSound = new Audio("/assets/new_notification.mp3");

// ✅ Badge Increment Helper
function incrementBadge() {
    let countEl = document.getElementById("notificationCount");
    if (!countEl) {
        const bell = document.getElementById("notificationBell");
        countEl = document.createElement("span");
        countEl.id = "notificationCount";
        countEl.className =
            "absolute -top-1 -right-1 bg-becs-red text-white text-xs rounded-full w-5 h-5 flex items-center justify-center";
        bell.appendChild(countEl);
    }

    const current = parseInt(countEl.textContent || "0", 10);
    countEl.textContent = current + 1;
    countEl.classList.remove("hidden");

    // subtle bounce animation
    countEl.classList.add("scale-110", "transition-transform", "duration-200");
    setTimeout(() => countEl.classList.remove("scale-110"), 300);
}

// ✅ Real-time Notification Listener
if (window.Laravel.userId) {
    window.Echo.private(
        `App.Models.User.${window.Laravel.userId}`
    ).notification((notification) => {
        // Play sound (handle autoplay restrictions)
        notificationSound.play().catch(() => {
            console.warn(
                "🔇 Notification sound blocked by browser autoplay policy."
            );
            const bell = document.getElementById("notificationBell");
            if (bell) {
                bell.classList.add("animate-pulse");
                setTimeout(() => bell.classList.remove("animate-pulse"), 3000);
            }
        });

        // Prepend notification to list
        const list = document.getElementById("notificationList");
        if (list) {
            const item = document.createElement("div");
            item.className = "p-3 border-b hover:bg-gray-50 transition";
            item.innerHTML = `
                    <div class="flex justify-between items-start">
                        <p class="text-sm text-gray-800">${notification.message}</p>
                        <button onclick="markAsRead('${notification.id}', this)"
                                class="text-xs text-becs-blue hover:underline">Mark read</button>
                    </div>
                    <span class="text-xs text-gray-500">Just now</span>
                `;
            list.prepend(item);
        }

        // Update badge count
        incrementBadge();
    });
}
