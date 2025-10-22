<!-- Header -->
<header class="bg-becs-navy shadow-sm p-4 flex justify-between items-center text-white">
    <h1 class="text-xl font-semibold">BECS Consultancy</h1>

    <div class="flex items-center space-x-4 relative">
        {{-- 🔔 Notification Bell --}}
        <button id="notificationBell" class="relative">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405C18.21 14.79 18 13.918 18 13V8a6 6 0 10-12 0v5c0 .918-.21 1.79-.595 2.595L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>

            {{-- Badge --}}
            <span id="notificationCount"
                class="absolute -top-1 -right-1 bg-becs-red text-white text-xs rounded-full w-5 h-5 flex items-center justify-center {{ $notifications->whereNull('read_at')->count() == 0 ? 'hidden' : '' }}">
                {{ $notifications->whereNull('read_at')->count() }}
            </span>
        </button>

        {{-- Hamburger Menu --}}
        <button id="hamburgerBtn" class="block lg:hidden ml-2">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</header>

<!-- 🧭 Notification Modal -->
<div id="notificationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div id="notificationBox" class="bg-white rounded-lg shadow-xl w-full max-w-2xl relative">
        <!-- Header -->
        <div class="flex justify-between items-center bg-becs-navy text-white px-5 py-3 rounded-t-lg cursor-move">
            <h2 class="text-lg font-semibold">Notifications</h2>
            <button id="closeModal" class="text-white hover:text-gray-200 text-2xl leading-none">&times;</button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b text-sm font-medium text-gray-600">
            <button
                class="tab-btn active-tab px-4 py-2 border-b-2 border-becs-blue text-becs-blue flex items-center gap-1"
                data-tab="unread">
                Unread
                <span id="unreadCount" class="ml-1 text-xs bg-becs-red text-white rounded-full px-2">
                    {{ $notifications->whereNull('read_at')->count() }}
                </span>
            </button>
            <button
                class="tab-btn px-4 py-2 border-b-2 border-transparent hover:border-gray-300 flex items-center gap-1"
                data-tab="all">
                All
                <span id="allCount" class="ml-1 text-xs bg-gray-300 text-gray-700 rounded-full px-2">
                    {{ $notifications->count() }}
                </span>
            </button>
            <button class="tab-btn px-4 py-2 border-b-2 border-transparent hover:border-gray-300"
                data-tab="mentions">Mentions</button>
            <button class="tab-btn px-4 py-2 border-b-2 border-transparent hover:border-gray-300"
                data-tab="system">System</button>
        </div>

        <!-- Tab Content -->
        <div class="p-4 max-h-[70vh] overflow-y-auto">
            <!-- Unread -->
            <!-- Unread -->
            <div id="tab-unread" class="tab-content">
                @forelse ($notifications->whereNull('read_at') as $notification)
                <div class="p-3 border-b hover:bg-gray-50 transition cursor-pointer"
                    onclick="openNotificationChat('{{ $notification->data['task_id'] ?? '' }}')">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-800">{{ $notification->data['message'] }}</p>
                        <button onclick="event.stopPropagation(); markAsRead('{{ $notification->id }}', this)"
                            class="text-xs text-becs-blue hover:underline">Mark read</button>
                    </div>
                    <div class="text-xs text-gray-500 flex justify-between mt-1">
                        <span>Sent: {{ $notification->created_at->format('d M Y, H:i') }}</span>
                        <span>Read: Unread</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">No unread notifications.</p>
                @endforelse
            </div>

            <!-- All -->
            <div id="tab-all" style="overflow-x: auto; max-height:400px;" class="tab-content hidden">
                @forelse ($notifications as $notification)
                <div class="p-3 border-b hover:bg-gray-50 transition cursor-pointer"
                    onclick="openNotificationChat('{{ $notification->data['task_id'] ?? '' }}')">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-800">{{ $notification->data['message'] }}</p>
                    </div>
                    <div class="text-xs text-gray-500 flex justify-between mt-1">
                        <span>Sent: {{ $notification->created_at->format('d M Y, H:i') }}</span>
                        <span>Read: {{ $notification->read_at ? $notification->read_at->format('d M Y, H:i') : 'Unread'
                            }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">No notifications yet.</p>
                @endforelse
            </div>

            <!-- Mentions -->
            <div id="tab-mentions" class="tab-content hidden">
                <p class="text-center text-gray-500 py-4">No mentions yet.</p>
            </div>

            <!-- System -->
            <div id="tab-system" class="tab-content hidden">
                <p class="text-center text-gray-500 py-4">No system alerts yet.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t px-5 py-3 flex justify-end bg-gray-50 rounded-b-lg">
            <button onclick="markAllAsRead()"
                class="bg-becs-navy text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                Mark all as read
            </button>
        </div>
    </div>
</div>

<!-- ✅ Scripts -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('notificationModal');
    const box = document.getElementById('notificationBox');
    const closeModal = document.getElementById('closeModal');
    const bell = document.getElementById('notificationBell');
    const unreadTab = document.getElementById('tab-unread');
    const countEl = document.getElementById('notificationCount');
    const unreadBadge = document.getElementById('unreadCount');
    const allTab = document.getElementById('tab-all');
    const allBadge = document.getElementById('allCount');

    // Make modal draggable & resizable
    $(box).draggable({ handle: '.cursor-move' }).resizable({ minWidth: 500, minHeight: 400 });

    // Modal open/close
    bell.addEventListener('click', () => modal.classList.remove('hidden'));
    closeModal.addEventListener('click', () => modal.classList.add('hidden'));
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });

    // Tabs switcher
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('text-becs-blue', 'border-becs-blue', 'active-tab'));
            btn.classList.add('text-becs-blue', 'border-becs-blue', 'active-tab');
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
            document.getElementById(`tab-${btn.dataset.tab}`).classList.remove('hidden');
        });
    });

    // Toastr setup
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "timeOut": "5000",
    };

    // Echo real-time listener
    if (window.Laravel?.userId && window.Echo) {
        window.Echo.private(`App.Models.User.${window.Laravel.userId}`)
            .notification((notification) => {
                // 🔔 Play alert sound
                const audio = new Audio('/assets/notification.wav');
                audio.play().catch(()=>{});

                // 🟦 Show toast
                toastr.info(notification.message, notification.sender_name, {
                    onclick: () => openNotificationChat(notification.task_id)
                });

                // 🧩 Create new notification HTML
                const newItem = document.createElement('div');
                newItem.className = "p-3 border-b hover:bg-gray-50 transition cursor-pointer";
                newItem.onclick = () => openNotificationChat(notification.task_id);
                newItem.innerHTML = `
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-800">${notification.message}</p>
                        <button onclick="event.stopPropagation(); markAsRead('${notification.id}', this)"
                            class="text-xs text-becs-blue hover:underline">Mark read</button>
                    </div>
                    <span class="text-xs text-gray-500 flex justify-end">Just now</span>
                `;

                // 🧭 Prepend to "Unread" and "All" tabs
                unreadTab.prepend(newItem.cloneNode(true));
                allTab.prepend(newItem);

                // 🧮 Update counts safely
                const currentUnread = parseInt(unreadBadge.textContent || '0', 10) + 1;
                const currentAll = parseInt(allBadge.textContent || '0', 10) + 1;

                countEl.textContent = currentUnread;
                unreadBadge.textContent = currentUnread;
                allBadge.textContent = currentAll;
                countEl.classList.remove('hidden');
            });
    }
});

// 🟩 Mark single as read
async function markAsRead(id, btn) {
    try {
        await fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
        });
        btn.closest('div.p-3').remove();
        updateUnreadCounts(-1);
    } catch (err) { console.error(err); }
}

// 🟨 Mark all as read
async function markAllAsRead() {
    try {
        await fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
        });
        document.querySelectorAll('#tab-unread .p-3').forEach(e => e.remove());
        updateUnreadCounts('clear');
        toastr.success('All notifications marked as read');
    } catch (err) { console.error(err); }
}

// 🔢 Counter manager
function updateUnreadCounts(change) {
    const countEl = document.getElementById('notificationCount');
    const unreadTab = document.getElementById('unreadCount');
    let current = parseInt(countEl?.textContent || '0', 10);

    if (change === 'clear') {
        countEl.textContent = unreadTab.textContent = '0';
        countEl.classList.add('hidden');
    } else {
        let newCount = Math.max(current + change, 0);
        countEl.textContent = unreadTab.textContent = newCount;
        if (newCount === 0) countEl.classList.add('hidden');
    }
}

// 🗨️ Open task chat
function openNotificationChat(taskId) {
    if (!taskId) return;
    if (typeof openTaskModal === 'function') openTaskModal(taskId);
    else {
        fetch(`/tasks/${taskId}/chat`)
            .then(r => r.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                document.body.appendChild(temp);
            });
    }
    document.getElementById('notificationModal').classList.add('hidden');
}   
</script>