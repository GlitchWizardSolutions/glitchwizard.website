const productsForm = document.querySelector('.products-form');
const searchIcon = document.querySelector('.search-toggle');
const searchInput = document.querySelector('.search input');
if (searchIcon && searchInput) {
    searchIcon.onclick = () => {
        searchIcon.style.display = 'none';
        searchIcon.parentElement.querySelector('input').style.display = 'block';
        searchIcon.parentElement.querySelector('input').focus();
    };
    searchInput.onkeyup = event => {
        if (event.keyCode === 13 && searchInput.value.length > 0) {
            window.location.href = encodeURI(searchInput.dataset.url + searchInput.value);
        }
    };
}
if (document.querySelector('.product-img-small')) {
    let imgs = document.querySelectorAll('.product-img-small img');
    imgs.forEach(img => {
        img.onmouseover = () => {
            document.querySelector('.product-img-large img').src = img.src;
            imgs.forEach(i => i.parentElement.classList.remove('selected'));
            img.parentElement.classList.add('selected');
        };
        img.onclick = () => {
            document.body.insertAdjacentHTML('beforeend', `
            <div class="img-modal">
                <div>
                    <a href="#" class="close">&times;</a>
                    <img src="${img.src}" alt="">
                </div>
            </div>
            `);
            document.querySelector('.img-modal div').style.height = (document.querySelector('.img-modal img').height+80) + 'px';
            document.querySelector('.img-modal .close').onclick = event => {
                event.preventDefault();
                document.querySelector('.img-modal').remove();
            };
            document.querySelector('.img-modal').onclick = event => {
                if (event.target.classList.contains('img-modal')) document.querySelector('.img-modal').remove();
            };
        };
    });
}
if (document.querySelector('.product .product-form')) {
    let updatePrice = () => {
        let price = parseFloat(document.querySelector('.product .price').dataset.price);
        document.querySelectorAll('.product .product-form .option').forEach(e => {
            if (e.value) {
                let optionPrice = e.classList.contains('text') || e.classList.contains('datetime') ? e.dataset.price : 0.00;
                optionPrice = e.classList.contains('select') ? e.options[e.selectedIndex].dataset.price : optionPrice;
                optionPrice = (e.classList.contains('radio') || e.classList.contains('checkbox')) && e.checked ? e.dataset.price : optionPrice;
                price = (e.classList.contains('select') ? e.options[e.selectedIndex].dataset.modifier : e.dataset.modifier) == 'add' ? price+parseFloat(optionPrice) : price-parseFloat(optionPrice);
            }
        });
        document.querySelector('.product .price').innerHTML = currency_code + (price > 0.00 ? price.toFixed(2) : 0.00);
    };
    let updateQty = () => {
        if (!document.querySelector('.product .product-form #quantity')) return;
        let qtyEle = document.querySelector('.product .product-form #quantity');
        let qty = parseInt(qtyEle.dataset.quantity);
        document.querySelectorAll('.product .product-form .option').forEach(e => {
            if (e.value) {
                let optionQty = e.classList.contains('text') || e.classList.contains('datetime') ? e.dataset.quantity : 0;
                optionQty = e.classList.contains('select') ? e.options[e.selectedIndex].dataset.quantity : optionQty;
                optionQty = (e.classList.contains('radio') || e.classList.contains('checkbox')) && e.checked ? e.dataset.quantity : optionQty;
                if ((qty > parseInt(optionQty) || qty == -1) && parseInt(optionQty) > 0) {
                    qty = parseInt(optionQty);
                }
            }
        });
        if (qty == -1) {
            qtyEle.removeAttribute('max');
        } else {
            qtyEle.max = qty;
        }
    };
    document.querySelectorAll('.product .product-form .option').forEach(ele => ele.onchange = () => {
        updatePrice();
        updateQty();
        let imgs = document.querySelectorAll('.product-img-small img');
        imgs.forEach(img => {
            if (img.alt.includes(ele.name.toLowerCase() + '-' + ele.value.toLowerCase())) {
                document.querySelector('.product-img-large img').src = img.src;
                imgs.forEach(i => i.parentElement.classList.remove('selected'));
                img.parentElement.classList.add('selected');
            }
        });
    });
    updatePrice();
    updateQty();
}
if (productsForm) {
    document.querySelector('.sortby select').onchange = () => productsForm.submit();
    document.querySelectorAll('.products-filters .filter-title').forEach(ele => {
        ele.onclick = () => ele.closest('.products-filter').classList.toggle('closed');
    });
    document.querySelectorAll('.products-filters .show-more').forEach(ele => {
        ele.onclick = event => {
            event.preventDefault();
            ele.closest('.filter-options').querySelectorAll('label').forEach(label => label.classList.remove('hidden'));
            ele.remove();
        };
    });
    document.querySelectorAll('.products-filters input').forEach(ele => {
        ele.onchange = () => productsForm.submit();
    });
}
if (document.querySelector('.responsive-toggle')) {
    document.querySelector('.responsive-toggle').onclick = event => {
        event.preventDefault();
        let nav_display = document.querySelector('header nav').style.display;
        document.querySelector('header nav').style.display = nav_display == 'block' ? 'none' : 'block';
    };
}
if (document.querySelector('.cart .ajax-update')) {
    document.querySelectorAll('.cart .ajax-update').forEach(ele => ele.onchange = () => {
        let formEle = document.querySelector('.cart form');
        let formData = new FormData(formEle);
        formData.append('update', 'Update');
        
        // Show loading state
        showCartLoading();
        
        fetch(formEle.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            let doc = (new DOMParser()).parseFromString(html, 'text/html');
            
            // Update cart totals
            if (doc.querySelector('.total')) {
                document.querySelector('.total').innerHTML = doc.querySelector('.total').innerHTML;
            }
            
            // Update individual product totals
            document.querySelectorAll('.product-total').forEach((e,i) => {
                if (doc.querySelectorAll('.product-total')[i]) {
                    e.innerHTML = doc.querySelectorAll('.product-total')[i].innerHTML;
                }
            });
            
            // Hide loading state
            hideCartLoading();
            
            // Show success feedback
            showCartFeedback('Cart updated successfully!', 'success');
        })
        .catch(error => {
            console.error('Error updating cart:', error);
            hideCartLoading();
            showCartFeedback('Error updating cart. Please try again.', 'error');
        });
    });
}
const checkoutHandler = () => {
    document.querySelectorAll('.checkout .ajax-update').forEach(ele => {
        ele.onchange = () => {
            let formEle = document.querySelector('.checkout form');
            let formData = new FormData(formEle);
            formData.append('update', 'Update');
            
            // Show loading state
            showCheckoutLoading();
            
            fetch(formEle.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                let doc = (new DOMParser()).parseFromString(html, 'text/html');
                
                // Update summary section
                if (doc.querySelector('.summary')) {
                    document.querySelector('.summary').innerHTML = doc.querySelector('.summary').innerHTML;
                }
                
                // Update total
                if (doc.querySelector('.total')) {
                    document.querySelector('.total').innerHTML = doc.querySelector('.total').innerHTML;
                }
                
                // Update discount code result
                if (doc.querySelector('.discount-code .result')) {
                    document.querySelector('.discount-code .result').innerHTML = doc.querySelector('.discount-code .result').innerHTML;
                }
                
                // Update shipping methods
                if (doc.querySelector('.shipping-methods-container')) {
                    document.querySelector('.shipping-methods-container').innerHTML = doc.querySelector('.shipping-methods-container').innerHTML;
                }
                
                // Hide loading state
                hideCheckoutLoading();
                
                // Re-bind event handlers
                checkoutHandler();
                
                // Show success feedback
                showCheckoutFeedback('Checkout updated successfully!', 'success');
            })
            .catch(error => {
                console.error('Error updating checkout:', error);
                hideCheckoutLoading();
                showCheckoutFeedback('Error updating checkout. Please try again.', 'error');
            });
        };
        
        // Enhanced discount code handling
        if (ele.name == 'discount_code') {
            ele.onkeydown = event => {
                if (event.key == 'Enter') {
                    event.preventDefault();
                    ele.onchange();
                }
            };
            
            // Add visual feedback for discount code
            ele.oninput = () => {
                clearTimeout(ele.discountTimeout);
                ele.discountTimeout = setTimeout(() => {
                    if (ele.value.length >= 3) {
                        validateDiscountCode(ele.value);
                    }
                }, 1000);
            };
        }
    });
};
checkoutHandler();

// Enhanced helper functions for better UX
function showCartLoading() {
    const cartForm = document.querySelector('.cart form');
    if (cartForm) {
        cartForm.style.opacity = '0.6';
        cartForm.style.pointerEvents = 'none';
        
        // Add spinner if not exists
        if (!document.querySelector('.cart-loading')) {
            const spinner = document.createElement('div');
            spinner.className = 'cart-loading';
            spinner.innerHTML = '<div class="spinner">Updating cart...</div>';
            spinner.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; background: rgba(255,255,255,0.9); padding: 20px; border-radius: 5px; text-align: center;';
            cartForm.style.position = 'relative';
            cartForm.appendChild(spinner);
        }
    }
}

function hideCartLoading() {
    const cartForm = document.querySelector('.cart form');
    if (cartForm) {
        cartForm.style.opacity = '1';
        cartForm.style.pointerEvents = 'auto';
        
        const spinner = document.querySelector('.cart-loading');
        if (spinner) {
            spinner.remove();
        }
    }
}

function showCheckoutLoading() {
    const checkoutForm = document.querySelector('.checkout form');
    if (checkoutForm) {
        checkoutForm.style.opacity = '0.6';
        checkoutForm.style.pointerEvents = 'none';
        
        // Add spinner if not exists
        if (!document.querySelector('.checkout-loading')) {
            const spinner = document.createElement('div');
            spinner.className = 'checkout-loading';
            spinner.innerHTML = '<div class="spinner">Updating checkout...</div>';
            spinner.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; background: rgba(255,255,255,0.9); padding: 20px; border-radius: 5px; text-align: center;';
            checkoutForm.style.position = 'relative';
            checkoutForm.appendChild(spinner);
        }
    }
}

function hideCheckoutLoading() {
    const checkoutForm = document.querySelector('.checkout form');
    if (checkoutForm) {
        checkoutForm.style.opacity = '1';
        checkoutForm.style.pointerEvents = 'auto';
        
        const spinner = document.querySelector('.checkout-loading');
        if (spinner) {
            spinner.remove();
        }
    }
}

function showCartFeedback(message, type = 'success') {
    showFeedback(message, type, '.cart');
}

function showCheckoutFeedback(message, type = 'success') {
    showFeedback(message, type, '.checkout');
}

function showFeedback(message, type, container) {
    const targetContainer = document.querySelector(container);
    if (!targetContainer) return;
    
    // Remove existing feedback
    const existingFeedback = targetContainer.querySelector('.ajax-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    // Create new feedback
    const feedback = document.createElement('div');
    feedback.className = `ajax-feedback ${type}`;
    feedback.textContent = message;
    feedback.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 5px;
        z-index: 1001;
        font-weight: 500;
        animation: slideIn 0.3s ease-out;
        ${type === 'success' ? 'background: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'}
    `;
    
    // Add CSS animation if not exists
    if (!document.querySelector('#feedbackAnimation')) {
        const style = document.createElement('style');
        style.id = 'feedbackAnimation';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(feedback);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (feedback.parentNode) {
            feedback.style.animation = 'slideIn 0.3s ease-out reverse';
            setTimeout(() => feedback.remove(), 300);
        }
    }, 3000);
}

function validateDiscountCode(code) {
    if (code.length < 3) return;
    
    const discountInput = document.querySelector('input[name="discount_code"]');
    if (!discountInput) return;
    
    // Visual feedback for validation
    discountInput.style.borderColor = '#ffc107';
    discountInput.style.background = '#fff3cd';
}

// Enhanced product filtering with debouncing
function initProductFiltering() {
    const searchInput = document.querySelector('.products-form .search input');
    if (searchInput) {
        let searchTimeout;
        searchInput.oninput = function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2 || this.value.length === 0) {
                    filterProducts(this.value);
                }
            }, 500);
        };
    }
}

function filterProducts(searchTerm) {
    // This would implement live product filtering
    // For now, just update the URL and reload
    const form = document.querySelector('.products-form');
    if (form && searchTerm !== undefined) {
        const searchInput = form.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.value = searchTerm;
            form.submit();
        }
    }
}

// Initialize enhanced features
document.addEventListener('DOMContentLoaded', function() {
    initProductFiltering();
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add click-to-copy functionality for order numbers
    document.querySelectorAll('.order-number').forEach(orderNum => {
        orderNum.style.cursor = 'pointer';
        orderNum.title = 'Click to copy order number';
        orderNum.onclick = function() {
            navigator.clipboard.writeText(this.textContent).then(() => {
                showFeedback('Order number copied to clipboard!', 'success', 'body');
            });
        };
    });
});