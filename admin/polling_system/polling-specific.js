/* 
 * Polling System Specific JavaScript
 * 
 * This file contains ONLY the unique JavaScript required for polling system functionality
 * that is NOT covered by the centralized admin template system.
 * 
 * Removed: General admin functionality (covered by central template)
 * Kept: Add answer functionality, poll results modal, and poll-specific interactions
 */

// Add Answer Functionality for Poll Creation/Editing
if (document.querySelector('.add_answer')) {
    document.querySelector('.add_answer').onclick = function(event) {
        event.preventDefault();
        let num_answers = document.querySelectorAll('.answers input[type="text"]').length + 1;
        
        // Create new option HTML using Bootstrap structure
        const newOptionHTML = `
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">Option ${num_answers}</span>
                    <input type="text" name="answers[]" class="form-control" 
                        placeholder="Enter option text">
                </div>
                ${document.querySelector('input[name="images[]"]') ? `
                <div class="mt-2">
                    <label class="form-label">Option Image</label>
                    <input type="file" name="images[]" class="form-control" accept="image/*">
                    <div class="form-text">Optional image for this option.</div>
                </div>
                ` : ''}
            </div>
        `;
        
        document.querySelector('.answers .answer').insertAdjacentHTML('beforeend', newOptionHTML);
        
        // Update file input event handlers for new inputs if images are enabled
        document.querySelectorAll('input[type="file"][name="images[]"]').forEach(input => {
            input.onchange = event => {
                console.log('File selected:', event.target.files[0]?.name);
            };
        });
    };
    
    // Initialize file input event handlers for existing inputs
    document.querySelectorAll('input[type="file"][name="images[]"]').forEach(input => {
        input.onchange = event => {
            console.log('File selected:', event.target.files[0]?.name);
        };
    });
}

// Modal Dialog System for Poll Results
const modal = options => {
    let element;
    if (document.querySelector(options.element)) {
        element = document.querySelector(options.element);
    } else if (options.modalTemplate) {
        document.body.insertAdjacentHTML('beforeend', options.modalTemplate());
        element = document.body.lastElementChild;
    }
    options.element = element;
    options.open = obj => {
        element.style.display = 'flex';
        element.getBoundingClientRect();
        element.classList.add('open');
        if (options.onOpen) options.onOpen(obj);
    };
    options.close = obj => {
        if (options.onClose) {
            let returnCloseValue = options.onClose(obj);
            if (returnCloseValue !== false) {
                element.style.display = 'none';
                element.classList.remove('open');
                element.remove();
            }
        } else {
            element.style.display = 'none';
            element.classList.remove('open');
            element.remove();
        }
    };
    if (options.state == 'close') {
        options.close({ source: element, button: null });
    } else if (options.state == 'open') {
        options.open({ source: element }); 
    }
    element.querySelectorAll('.dialog-close').forEach(e => {
        e.onclick = event => {
            event.preventDefault();
            options.close({ source: element, button: e });
        };
    });
    return options;
};

// Poll Results Display Function
const viewPoll = (title, json, totalVotes) => {
    modal({
        state: 'open',
        modalTemplate: function() {
            return `
            <div class="dialog view-poll-modal">
                <div class="content">
                    <h3 class="heading">Poll Results<span class="dialog-close">&times;</span></h3>
                    <div class="body">
                        <h3 class="poll-title">${title}</h3>
                        <div class="wrapper">
                            <div class="poll-question">
                                ${json.map((answer, index) => `
                                    <div class="poll-txt">
                                        <span class="answer">${answer.title}</span>
                                        <span class="votes">${answer.votes} Votes</span>
                                    </div>
                                    <div class="result-bar-container">
                                        <div class="result-bar ${answer.votes === 0 ? 'no-votes' : ''}" style="width:${(answer.votes/totalVotes)*100}%">
                                            ${answer.votes > 0 ? `${((answer.votes/totalVotes)*100).toFixed(0)}%` : '0%'}
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                    <div class="footer pad-5">
                        <a href="#" class="btn dialog-close save">Close</a>
                    </div>
                </div>
            </div>
            `;
        }
    });
};

// Poll Results Modal Trigger Event Handlers
document.querySelectorAll('.trigger-answers-modal').forEach(element => element.onclick = event => {
    event.preventDefault();
    let title = element.closest('tr').querySelector('.title').innerText;
    viewPoll(title, JSON.parse(element.dataset.json), element.dataset.totalVotes);
});

// Enhanced Multiselect Category Functionality with Bootstrap Styling
if (document.querySelector('.multiselect')) {
    const multiselect = document.querySelector('.multiselect');
    const searchInput = multiselect.querySelector('.search');
    const listContainer = multiselect.querySelector('.list');
    
    // Show/hide dropdown on focus/click
    searchInput.addEventListener('focus', () => {
        listContainer.style.display = 'block';
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', (event) => {
        if (!multiselect.contains(event.target)) {
            listContainer.style.display = 'none';
        }
    });
    
    // Filter categories as user types
    searchInput.addEventListener('input', (event) => {
        const searchTerm = event.target.value.toLowerCase();
        const items = listContainer.querySelectorAll('.list-item');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Add category when clicked
    listContainer.addEventListener('click', (event) => {
        if (event.target.classList.contains('list-item')) {
            const categoryId = event.target.getAttribute('data-value');
            const categoryTitle = event.target.textContent.trim();
            
            // Check if already selected
            if (multiselect.querySelector(`input[value="${categoryId}"]`)) {
                return;
            }
            
            // Create new badge
            const badge = document.createElement('span');
            badge.className = 'badge bg-secondary me-1 mb-1';
            badge.setAttribute('data-value', categoryId);
            badge.innerHTML = `
                <button type="button" class="btn-close btn-close-white btn-sm me-1 remove" aria-label="Remove category"></button>
                ${categoryTitle}
                <input type="hidden" name="categories[]" value="${categoryId}">
            `;
            
            // Add before search input
            multiselect.insertBefore(badge, searchInput);
            
            // Clear search
            searchInput.value = '';
            listContainer.style.display = 'none';
            
            // Add remove functionality
            badge.querySelector('.remove').addEventListener('click', () => {
                badge.remove();
            });
        }
    });
    
    // Add remove functionality to existing badges
    multiselect.querySelectorAll('.remove').forEach(removeBtn => {
        removeBtn.addEventListener('click', (event) => {
            event.target.closest('span').remove();
        });
    });
}
