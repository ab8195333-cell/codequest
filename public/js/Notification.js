document.addEventListener("DOMContentLoaded", function () {
    axios.defaults.headers.common['Accept'] = 'application/json';
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // جلب العداد فور تحميل الصفحة
    fetchUnreadCount();
});

// 🟢 1. جلب عدد الإشعارات غير المقروءة
function fetchUnreadCount() {
    let baseUrl = window.APP_URL || '';
    if (baseUrl && !baseUrl.endsWith('/')) baseUrl += '/';

    axios.get(baseUrl + 'notifications')
        .then(response => {
            if (response.data && response.data.success === false) {
                if (typeof displayModalError === 'function') {
                    displayModalError(response.data);
                }
                return;
            }

            const unreadCount = response.data.unread_count || 0;
            updateBadgeUI(unreadCount);
        })
        .catch(error => {
            handleAxiosError(error, 'فشل جلب عداد الإشعارات');
        });
}

// 🟢 2. جلب قائمة الإشعارات
function loadNotifications() {
    const listContainer = document.getElementById('notifications-list');
    let baseUrl = window.APP_URL || '';
    if (baseUrl && !baseUrl.endsWith('/')) baseUrl += '/';

    axios.get(baseUrl + 'notifications')
        .then(response => {
            if (response.data && response.data.success === false) {
                if (typeof displayModalError === 'function') {
                    displayModalError(response.data);
                }
                return;
            }

            const notifications = response.data.notifications || [];
            const totalCountElement = document.getElementById('total-count');

            if (totalCountElement) {
                totalCountElement.innerText = notifications.length;
            }

            if (!listContainer) return;

            if (notifications.length === 0) {
                listContainer.innerHTML = `
                    <li class="text-center p-4 text-muted small">
                        <i class="bi bi-bell-slash d-block fs-4 mb-1"></i>
                        لا توجد إشعارات جديدة غير مقروءة
                    </li>`;
                return;
            }

            let html = '';
            notifications.forEach(item => {
                const formattedTime = formatDate(item.created_at);
                const itemUrl = item.target_url || item.url || '#';

                html += `
                    <li id="notification-item-${item.notification_id}" style="transition: all 0.3s ease;">
                        <div class="p-2 border-bottom bg-light border-start border-3 border-primary d-flex align-items-center justify-content-between gap-2">
                            <a href="${itemUrl}" class="flex-grow-1 text-decoration-none" onclick="markAsReadAndNavigate(event, ${item.notification_id}, '${itemUrl}')">
                                <p class="mb-1 small fw-bold text-dark lh-sm">${item.message}</p>
                                <small class="text-muted" style="font-size: 0.7rem;">${formattedTime}</small>
                            </a>
                            <a href="${itemUrl}" class="btn btn-sm btn-outline-primary p-1 px-2 border-0" title="عرض التفاصيل" onclick="markAsReadAndNavigate(event, ${item.notification_id}, '${itemUrl}')">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </div>
                    </li>
                `;
            });

            listContainer.innerHTML = html;
        })
        .catch(error => {
            handleAxiosError(error, 'فشل تحميل قائمة الإشعارات');
        });
}

// 🟢 3. تعليم كمقروء والانتقال إلى رابط الحدث
function markAsReadAndNavigate(event, notificationId, targetUrl) {
    if (event) {
        event.preventDefault();
    }

    let baseUrl = window.APP_URL || '';
    if (baseUrl && !baseUrl.endsWith('/')) baseUrl += '/';

    // إرسال طلب القراءة ثم التوجيه نحو الرابط Target URL
    axios.post(baseUrl + `notification/${notificationId}/read`)
        .finally(() => {
            if (targetUrl && targetUrl !== '#') {
                window.location.href = targetUrl;
            }
        });
}

// 🛠️ التعامل مع الأخطاء
function handleAxiosError(error, defaultMessage) {
    let errorData = { title: 'خطأ', message: defaultMessage };
    if (error.response && error.response.data) {
        if (typeof error.response.data === 'string') {
            errorData.message = error.response.data;
        } else {
            errorData = error.response.data;
            if (!errorData.title) errorData.title = 'خطأ';
            if (!errorData.message) errorData.message = defaultMessage;
        }
    }
    if (typeof displayModalError === 'function') {
        displayModalError(errorData);
    }
}

// 🛠️ تحديث الشارة
function updateBadgeUI(count) {
    const badge = document.getElementById('unread-badge');
    if (badge) {
        if (count > 0) {
            badge.innerText = count;
            badge.classList.remove('d-none');
        } else {
            badge.innerText = '0';
            badge.classList.add('d-none');
        }
    }
}

// 🛠️ تنسيق التاريخ
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('ar-EG', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
}