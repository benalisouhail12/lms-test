
class NotificationManager {
    constructor(options = {}) {
        this.options = {
            wsUrl: options.wsUrl || this.getWebSocketUrl(),
            userId: options.userId,
            authToken: options.authToken,
            reconnectAttempts: options.reconnectAttempts || 5,
            reconnectDelay: options.reconnectDelay || 3000,
            pollInterval: options.pollInterval || 60000,
            onNotification: options.onNotification || this.defaultOnNotification,
            onStatusUpdate: options.onStatusUpdate || this.defaultOnStatusUpdate,
            onBulkUpdate: options.onBulkUpdate || this.defaultOnBulkUpdate,
            onConnect: options.onConnect || (() => {}),
            onDisconnect: options.onDisconnect || (() => {}),
        };

        this.socket = null;
        this.reconnectCount = 0;
        this.connected = false;
        this.polling = false;
        this.pollTimer = null;

        this.notifications = [];
        this.unreadCount = 0;

        // Bind methods
        this.connect = this.connect.bind(this);
        this.disconnect = this.disconnect.bind(this);
        this.handleMessage = this.handleMessage.bind(this);
        this.fetchNotifications = this.fetchNotifications.bind(this);
        this.markAsRead = this.markAsRead.bind(this);
        this.markAllAsRead = this.markAllAsRead.bind(this);
        this.deleteNotification = this.deleteNotification.bind(this);
    }

    getWebSocketUrl() {
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        return `${protocol}//${window.location.host}/api/notifications/ws`;
    }

    connect() {
        if (!this.options.userId || !this.options.authToken) {
            console.error('UserId and authToken are required to connect to notifications');
            return;
        }

        try {
            const url = `${this.options.wsUrl}?token=${this.options.authToken}&user_id=${this.options.userId}`;
            this.socket = new WebSocket(url);

            this.socket.addEventListener('open', () => {
                console.log('WebSocket connection established');
                this.connected = true;
                this.reconnectCount = 0;
                this.options.onConnect();

                // Initial fetch of notifications
                this.fetchNotifications();
            });

            this.socket.addEventListener('message', this.handleMessage);

            this.socket.addEventListener('close', () => {
                this.connected = false;
                this.options.onDisconnect();

                if (this.reconnectCount < this.options.reconnectAttempts) {
                    this.reconnectCount++;
                    console.log(`WebSocket disconnected. Reconnecting (attempt ${this.reconnectCount})...`);
                    setTimeout(this.connect, this.options.reconnectDelay);
                } else {
                    console.log('WebSocket disconnected. Falling back to polling...');
                    this.startPolling();
                }
            });

            this.socket.addEventListener('error', (error) => {
                console.error('WebSocket error:', error);
            });
        } catch (error) {
            console.error('Failed to establish WebSocket connection:', error);
            this.startPolling();
        }
    }

    disconnect() {
        if (this.socket) {
            this.socket.close();
            this.socket = null;
            this.connected = false;
        }

        this.stopPolling();
    }

    handleMessage(event) {
        try {
            const data = JSON.parse(event.data);

            switch (data.type) {
                case 'notification':
                    this.handleNewNotification(data.payload);
                    break;

                case 'status_update':
                    this.handleStatusUpdate(data.payload);
                    break;

                case 'bulk_update':
                    this.handleBulkUpdate(data.payload);
                    break;

                case 'connection_ack':
                    console.log('Connection acknowledged by server');
                    break;

                default:
                    console.log('Unknown message type:', data.type);
            }
        } catch (error) {
            console.error('Error handling WebSocket message:', error);
        }
    }

    handleNewNotification(notification) {
        // Add to local cache
        this.notifications.unshift(notification);

        if (notification.status === 'unread') {
            this.unreadCount++;
        }

        // Call the callback
        this.options.onNotification(notification);
    }

    handleStatusUpdate(update) {
        // Update local cache
        const notification = this.notifications.find(n => n.id === update.id);

        if (notification) {
            const oldStatus = notification.status;
            notification.status = update.status;
            notification.updated_at = update.updated_at;

            // Update unread count
            if (oldStatus === 'unread' && update.status !== 'unread') {
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            } else if (oldStatus !== 'unread' && update.status === 'unread') {
                this.unreadCount++;
            }
        }

        // Call the callback
        this.options.onStatusUpdate(update);
    }

    handleBulkUpdate(update) {
        // Refresh notifications after a bulk update
        this.fetchNotifications();

        // Call the callback
        this.options.onBulkUpdate(update);
    }

    startPolling() {
        if (!this.polling) {
            this.polling = true;
            this.pollTimer = setInterval(this.fetchNotifications, this.options.pollInterval);
            this.fetchNotifications(); // Fetch immediately
        }
    }

    stopPolling() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
        this.polling = false;
    }

    fetchNotifications() {
        fetch('/api/notifications', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.options.authToken}`
            }
        })
        .then(response => response.json())
        .then(data => {
            this.notifications = data.data || [];
            this.unreadCount = this.notifications.filter(n => n.status === 'unread').length;

            // Trigger an update with the fetched notifications
            this.options.onBulkUpdate({
                type: 'refresh',
                notifications: this.notifications,
                unreadCount: this.unreadCount
            });
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
        });
    }

    markAsRead(notificationId) {
        // Optimistic update
        const notification = this.notifications.find(n => n.id === notificationId);
        if (notification && notification.status === 'unread') {
            notification.status = 'read';
            this.unreadCount = Math.max(0, this.unreadCount - 1);

            // Trigger UI update
            this.options.onStatusUpdate({
                id: notificationId,
                status: 'read',
                updated_at: new Date().toISOString()
            });
        }

        // Send to server
        fetch(`/api/notifications/${notificationId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.options.authToken}`
            },
            body: JSON.stringify({ status: 'read' })
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            // Revert optimistic update on error
            this.fetchNotifications();
        });
    }

    markAllAsRead(type = null) {
        // Optimistic update
        const affectedNotifications = type
            ? this.notifications.filter(n => n.type === type && n.status === 'unread')
            : this.notifications.filter(n => n.status === 'unread');

        affectedNotifications.forEach(n => n.status = 'read');
        this.unreadCount = this.notifications.filter(n => n.status === 'unread').length;

        // Trigger UI update
        this.options.onBulkUpdate({
            type: 'mark_all_read',
            notification_type: type,
            unreadCount: this.unreadCount
        });

        // Send to server
        fetch('/api/notifications/mark-all-as-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.options.authToken}`
            },
            body: JSON.stringify({ type: type })
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
            // Revert optimistic update on error
            this.fetchNotifications();
        });
    }

    deleteNotification(notificationId) {
        // Optimistic update
        const index = this.notifications.findIndex(n => n.id === notificationId);

        if (index !== -1) {
            const notification = this.notifications[index];
            this.notifications.splice(index, 1);

            if (notification.status === 'unread') {
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }

            // Trigger UI update
            this.options.onBulkUpdate({
                type: 'delete',
                notification_id: notificationId,
                unreadCount: this.unreadCount
            });
        }

        // Send to server
        fetch(`/api/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.options.authToken}`
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
            // Revert optimistic update on error
            this.fetchNotifications();
        });
    }

    // Default callback implementations
    defaultOnNotification(notification) {
        console.log('New notification received:', notification);
    }

    defaultOnStatusUpdate(update) {
        console.log('Notification status updated:', update);
    }

    defaultOnBulkUpdate(update) {
        console.log('Bulk update received:', update);
    }
}

// Export for use in other scripts
window.NotificationManager = NotificationManager;
