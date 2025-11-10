<?php
$pageTitle = 'Shopping Cart';
include '../includes/header.php';
?>

<div class="container">
    <div class="section-title">
        <h2>Shopping Cart</h2>
        <p>Review your items</p>
    </div>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
        <!-- Cart Items -->
        <div>
            <div class="cart-items" id="cartItems">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading cart...</p>
                </div>
            </div>
        </div>
        
        <!-- Cart Summary -->
        <div>
            <div class="cart-summary" id="cartSummary" style="display: none;">
                <h3>Order Summary</h3>
                <div class="cart-summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div class="cart-summary-row">
                    <span>Shipping:</span>
                    <span>FREE</span>
                </div>
                <div class="cart-summary-row">
                    <span>Total:</span>
                    <span id="total">$0.00</span>
                </div>
                <button class="btn btn-success btn-block" onclick="proceedToCheckout()">
                    <i class="fas fa-check"></i> Proceed to Checkout
                </button>
                <button class="btn btn-outline btn-block mt-2" onclick="clearCart()">
                    <i class="fas fa-trash"></i> Clear Cart
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let cartData = null;

// Load cart
async function loadCart() {
    try {
        const response = await fetch(API_ENDPOINTS.cart);
        const data = await response.json();
        
        const container = document.getElementById('cartItems');
        const summary = document.getElementById('cartSummary');
        
        if (data.success && data.data.length > 0) {
            cartData = data;
            
            container.innerHTML = data.data.map(item => `
                <div class="cart-item" data-book-id="${item.book_id}">
                    <img src="${item.image_url || 'https://via.placeholder.com/100x140?text=No+Image'}" 
                         alt="${item.title}" 
                         class="cart-item-image"
                         onerror="this.src='https://via.placeholder.com/100x140?text=No+Image'">
                    <div class="cart-item-details">
                        <h3 class="cart-item-title">
                            <a href="book-details.php?id=${item.book_id}">${item.title}</a>
                        </h3>
                        <p class="cart-item-author">${item.author}</p>
                        <p class="cart-item-price">${formatPrice(item.price)} each</p>
                        <div class="cart-item-actions">
                            <div class="quantity-control">
                                <button onclick="updateQuantity(${item.book_id}, ${item.quantity - 1})" 
                                        aria-label="Decrease quantity">-</button>
                                <input type="number" value="${item.quantity}" min="1" max="${item.stock_quantity}" 
                                       onchange="updateQuantity(${item.book_id}, this.value)"
                                       aria-label="Quantity">
                                <button onclick="updateQuantity(${item.book_id}, ${item.quantity + 1})" 
                                        aria-label="Increase quantity"
                                        ${item.quantity >= item.stock_quantity ? 'disabled' : ''}>+</button>
                            </div>
                            <button class="btn btn-danger" onclick="removeItem(${item.book_id})">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                    <div style="text-align: right; font-weight: bold; font-size: 1.2rem; color: var(--accent-color);">
                        ${formatPrice(item.subtotal)}
                    </div>
                </div>
            `).join('');
            
            // Update summary
            document.getElementById('subtotal').textContent = formatPrice(data.total);
            document.getElementById('total').textContent = formatPrice(data.total);
            summary.style.display = 'block';
        } else {
            showEmptyState(container, 'Your cart is empty');
            summary.style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading cart:', error);
        document.getElementById('cartItems').innerHTML = '<p>Error loading cart</p>';
    }
}

// Update quantity
async function updateQuantity(bookId, newQuantity) {
    newQuantity = parseInt(newQuantity);
    
    if (newQuantity < 0) return;
    
    try {
        const method = newQuantity === 0 ? 'DELETE' : 'PUT';
        const url = newQuantity === 0 
            ? `${API_ENDPOINTS.cart}?book_id=${bookId}`
            : API_ENDPOINTS.cart;
        
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };
        
        if (method === 'PUT') {
            options.body = JSON.stringify({
                book_id: bookId,
                quantity: newQuantity
            });
        }
        
        const response = await fetch(url, options);
        const data = await response.json();
        
        if (data.success) {
            loadCart();
            updateCartCount();
            showToast(newQuantity === 0 ? 'Item removed' : 'Cart updated', 'success');
        } else {
            showToast(data.error || 'Failed to update cart', 'error');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        showToast('Failed to update cart', 'error');
    }
}

// Remove item
async function removeItem(bookId) {
    if (!confirm('Are you sure you want to remove this item?')) return;
    
    try {
        const response = await fetch(`${API_ENDPOINTS.cart}?book_id=${bookId}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadCart();
            updateCartCount();
            showToast('Item removed from cart', 'success');
        } else {
            showToast(data.error || 'Failed to remove item', 'error');
        }
    } catch (error) {
        console.error('Error removing item:', error);
        showToast('Failed to remove item', 'error');
    }
}

// Clear cart
async function clearCart() {
    if (!confirm('Are you sure you want to clear your cart?')) return;
    
    try {
        const response = await fetch(`${API_ENDPOINTS.cart}?clear=true`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadCart();
            updateCartCount();
            showToast('Cart cleared', 'success');
        } else {
            showToast(data.error || 'Failed to clear cart', 'error');
        }
    } catch (error) {
        console.error('Error clearing cart:', error);
        showToast('Failed to clear cart', 'error');
    }
}

// Proceed to checkout
function proceedToCheckout() {
    if (cartData && cartData.data.length > 0) {
        showToast('Checkout feature coming soon!', 'info');
        // In a real application, this would redirect to a checkout page
    } else {
        showToast('Your cart is empty', 'error');
    }
}

// Load cart on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
});
</script>

<style>
@media (max-width: 768px) {
    .container > div {
        grid-template-columns: 1fr !important;
    }
    
    .cart-summary {
        position: static !important;
    }
}
</style>

<?php
$extraScripts = [];
include '../includes/footer.php';
?>
