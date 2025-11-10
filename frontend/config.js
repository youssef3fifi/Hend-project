/**
 * API Configuration
 * Configure API base URL based on environment
 */

// Default to localhost for development
// For EC2 deployment: Set environment variable BASE_URL or update this value
const API_CONFIG = {
    // Base URL for API endpoints
    // Development: http://localhost:3000/api
    // Production: http://YOUR_EC2_IP:3000/api or use environment variable
    BASE_URL: window.location.hostname === 'localhost' 
        ? 'http://localhost:3000/api' 
        : `http://${window.location.hostname}:3000/api`,
    
    // Session ID management - stored in localStorage
    SESSION_ID_KEY: 'bookstore_session_id',
    
    /**
     * Get or create session ID
     * @returns {string} Session ID
     */
    getSessionId() {
        let sessionId = localStorage.getItem(this.SESSION_ID_KEY);
        if (!sessionId) {
            // Generate unique session ID
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem(this.SESSION_ID_KEY, sessionId);
        }
        return sessionId;
    },
    
    /**
     * Get headers for API requests
     * @returns {Object} Headers object
     */
    getHeaders() {
        return {
            'Content-Type': 'application/json',
            'X-Session-ID': this.getSessionId()
        };
    }
};

// For EC2 deployment:
// 1. Open port 3000 in security group: 
//    - Go to EC2 console > Security Groups
//    - Add inbound rule: Custom TCP, Port 3000, Source: 0.0.0.0/0
//
// 2. Update BASE_URL above to:
//    BASE_URL: 'http://YOUR_EC2_PUBLIC_IP:3000/api'
//    (Replace YOUR_EC2_PUBLIC_IP with actual EC2 public IP)
//
// 3. Or use environment variable:
//    Set BASE_URL environment variable before starting server
