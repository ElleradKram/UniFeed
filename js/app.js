// Create post mode functionality
function toggleCreatePostMode() {
    const mainContent = document.querySelector('.main-content');
    
    // Check if we're on create post page
    const isCreatePostPage = window.location.pathname.includes('create_post.php');
    
    if (isCreatePostPage) {
        mainContent.classList.add('create-post-mode');
    } else {
        mainContent.classList.remove('create-post-mode');
    }
}

// Notification panel functionality
function toggleNotificationPanel() {
    const panel = document.querySelector('.notification-panel');
    if (panel) {
        panel.classList.toggle('show');
        
        // Load notifications if panel is being shown
        if (panel.classList.contains('show')) {
            loadNotifications();
        }
    }
}

// Load notifications from server
function loadNotifications() {
    fetch('api/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            const panel = document.querySelector('.notification-panel');
            if (data.success && panel) {
                updateNotificationPanel(data.notifications);
                updateNotificationBadge(data.unread_count);
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
}

// Update notification panel content
function updateNotificationPanel(notifications) {
    const panel = document.querySelector('.notification-panel');
    if (!panel) return;
    
    let content = `
        <div class="notification-header">
            <h3>Notifications</h3>
        </div>
    `;
    
    if (notifications.length === 0) {
        content += '<div class="no-notifications">No notifications yet</div>';
    } else {
        notifications.forEach(notification => {
            const unreadClass = notification.is_read == 0 ? 'unread' : '';
            const timeAgo = formatTimeAgo(notification.created_at);
            
            content += `
                <div class="notification-item ${unreadClass}" onclick="markNotificationRead(${notification.notification_id})">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-message">${notification.message}</div>
                    <div class="notification-time">${timeAgo}</div>
                </div>
            `;
        });
    }
    
    panel.innerHTML = content;
}

// Update notification badge
function updateNotificationBadge(count) {
    const badge = document.querySelector('.notif-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Mark notification as read
function markNotificationRead(notificationId) {
    fetch('api/mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            notification_id: notificationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications(); // Reload notifications
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Format time ago
function formatTimeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) {
        return 'Just now';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
}

// Voting functionality
document.addEventListener('DOMContentLoaded', function() {
    // Toggle create post mode based on current page
    toggleCreatePostMode();
    
    // Load notifications on page load
    loadNotifications();
    
    // Set up notification button click handler
    const notifBtn = document.querySelector('.notif-btn');
    if (notifBtn) {
        notifBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleNotificationPanel();
        });
    }
    
    // Close notification panel when clicking outside
    document.addEventListener('click', function(event) {
        const panel = document.querySelector('.notification-panel');
        const notifBtn = document.querySelector('.notif-btn');
        
        if (panel && panel.classList.contains('show')) {
            if (!panel.contains(event.target) && !notifBtn.contains(event.target)) {
                panel.classList.remove('show');
            }
        }
    });

    // Handle vote buttons
    document.querySelectorAll('.vote-btn').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const voteType = this.dataset.type;
            
            fetch('/BullBoard/api/vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    post_id: postId,
                    vote_type: voteType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update vote count display
                    const voteCountElement = this.parentElement.querySelector('.vote-count');
                    voteCountElement.textContent = data.total;
                    
                    // Update button states
                    const upvoteBtn = this.parentElement.querySelector('.upvote');
                    const downvoteBtn = this.parentElement.querySelector('.downvote');
                    
                    // Reset button states
                    upvoteBtn.classList.remove('active');
                    downvoteBtn.classList.remove('active');
                    
                    // Set active state based on action
                    if (data.action === 'added' || data.action === 'changed') {
                        this.classList.add('active');
                    }
                } else {
                    console.error('Vote failed:', data.error);
                    alert('Failed to vote. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });

    // Handle save post functionality
    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            
            fetch('/BullBoard/api/save_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    post_id: postId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'saved') {
                        this.innerHTML = '🔖 Saved';
                        this.classList.add('saved');
                    } else {
                        this.innerHTML = '🔖 Save';
                        this.classList.remove('saved');
                    }
                } else {
                    console.error('Save failed:', data.error);
                    alert('Failed to save post. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });

    // Handle search functionality
    const searchInput = document.querySelector('.search-container input');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 3) {
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 500);
            } else if (query.length === 0) {
                // Reset to show all posts
                location.reload();
            }
        });
    }

    // Handle form validation for create post
    const createPostForm = document.querySelector('.create-post-form');
    if (createPostForm) {
        createPostForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const content = document.getElementById('content').value.trim();
            const categoryId = document.getElementById('category_id').value;

            if (!title || !content || !categoryId) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }

            if (title.length < 5) {
                e.preventDefault();
                alert('Title must be at least 5 characters long.');
                return false;
            }

            if (content.length < 10) {
                e.preventDefault();
                alert('Content must be at least 10 characters long.');
                return false;
            }
        });
    }
});

// Search functionality
function performSearch(query) {
    fetch('/BullBoard/api/search.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            query: query
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updatePostsDisplay(data.posts);
        } else {
            console.error('Search failed:', data.error);
        }
    })
    .catch(error => {
        console.error('Search error:', error);
    });
}

// Update posts display with search results
function updatePostsDisplay(posts) {
    const postsContainer = document.querySelector('.posts-section');
    const postsHeader = postsContainer.querySelector('.posts-header');
    
    // Clear existing posts
    const existingPosts = postsContainer.querySelectorAll('.post-card');
    existingPosts.forEach(post => post.remove());
    
    if (posts.length === 0) {
        const noResults = document.createElement('div');
        noResults.className = 'no-results';
        noResults.innerHTML = '<p>No posts found matching your search.</p>';
        postsContainer.appendChild(noResults);
        return;
    }
    
    // Add new posts
    posts.forEach(post => {
        const postElement = createPostElement(post);
        postsContainer.appendChild(postElement);
    });
}

// Create post element from data
function createPostElement(post) {
    const postCard = document.createElement('div');
    postCard.className = 'post-card';
    
    postCard.innerHTML = `
        <div class="post-voting">
            <button class="vote-btn upvote" data-post-id="${post.post_id}" data-type="upvote">▲</button>
            <span class="vote-count">${post.upvotes - post.downvotes}</span>
            <button class="vote-btn downvote" data-post-id="${post.post_id}" data-type="downvote">▼</button>
        </div>
        <div class="post-main">
            <div class="post-header">
                <img src="images/avatar.png" alt="User Avatar" />
                <div class="post-meta">
                    <div class="post-author">
                        <strong>${escapeHtml(post.author_name)}</strong>
                        ${post.org_name ? `<span class="org-name">• ${escapeHtml(post.org_name)}</span>` : ''}
                    </div>
                    <div class="post-info">
                        <span class="category-tag" style="background-color: #1B5E20;">
                            ${escapeHtml(post.category_name)}
                        </span>
                        <span class="post-type">${post.post_type.charAt(0).toUpperCase() + post.post_type.slice(1)}</span>
                        <span class="post-date">${formatDate(post.created_at)}</span>
                    </div>
                </div>
            </div>
            <div class="post-content">
                <h3>${escapeHtml(post.title)}</h3>
                <p>${escapeHtml(post.content).replace(/\n/g, '<br>')}</p>
                ${post.event_date ? `<div class="event-date"><strong>Event Date:</strong> ${formatDateTime(post.event_date)}</div>` : ''}
            </div>
            <div class="post-actions">
                <button class="action-btn comment-btn">💬 ${post.comment_count} Comments</button>
                <button class="action-btn save-btn" data-post-id="${post.post_id}">🔖 Save</button>
                <button class="action-btn share-btn">📤 Share</button>
            </div>
        </div>
    `;
    
    return postCard;
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });
}
