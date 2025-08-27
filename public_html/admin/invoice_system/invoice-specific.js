/* Invoice System - Specific JavaScript Only
 * This file contains ONLY invoice-specific JavaScript functions
 * Global admin functionality is handled by /admin/assets/js/ files
 */

// Initialize multiselect components for invoice forms
const initMultiselect = () => {
    document.querySelectorAll('.multiselect').forEach(element => {
        let updateList = () => {
            element.querySelectorAll('.item').forEach(item => {
                element.querySelectorAll('.list span').forEach(newItem => {
                    if (item.dataset.value == newItem.dataset.value) {
                        newItem.style.display = 'none';
                    }
                });
                item.querySelector('.remove').onclick = () => {
                    element.querySelector('.list span[data-value="' + item.dataset.value + '"]').style.display = 'flex';
                    item.querySelector('.remove').parentElement.remove();
                };
            });
            if (element.querySelectorAll('.item').length > 0) {
                element.querySelector('.search').placeholder = '';
            }
        };
        element.onclick = () => element.querySelector('.search').focus();
        element.querySelector('.search').onfocus = () => element.querySelector('.list').style.display = 'flex';
        element.querySelector('.search').onclick = () => element.querySelector('.list').style.display = 'flex';
        element.querySelector('.search').onkeyup = () => {
            element.querySelector('.list').style.display = 'flex';
            element.querySelectorAll('.list span').forEach(item => {
                item.style.display = item.innerText.toLowerCase().includes(element.querySelector('.search').value.toLowerCase()) ? 'flex' : 'none';
            });
            updateList();
        };
        element.querySelectorAll('.list span').forEach(item => item.onclick = () => {
            item.style.display = 'none';
            let html = `
                <span class="item" data-value="${item.dataset.value}">
                    <i class="remove">&times;</i>${item.innerText}
                    <input type="hidden" name="${element.dataset.name}" value="${item.dataset.value}">    
                </span>
            `;
            if (element.querySelector('.item')) {
                let ele = element.querySelectorAll('.item');
                ele = ele[ele.length-1];
                ele.insertAdjacentHTML('afterend', html);                          
            } else {
                element.insertAdjacentHTML('afterbegin', html);
            }
            element.querySelector('.search').value = '';
            updateList();
        });
        updateList();
    });
};

// Initialize invoice item management functionality
const initManageInvoiceItems = () => {
    if (!document.querySelector('.add-item')) return;
    
    document.querySelector('.add-item').onclick = event => {
        event.preventDefault();
        document.querySelector('.manage-invoice-table tbody').insertAdjacentHTML('beforeend', `
        <tr>
            <td><input type="hidden" name="item_id[]" value="0"><input name="item_name[]" type="text" placeholder="Name"></td>
            <td><input name="item_description[]" type="text" placeholder="Description"></td>
            <td><input name="item_price[]" type="number" placeholder="Price" step=".01"></td>
            <td><input name="item_quantity[]" type="number" placeholder="Quantity"></td>
            <td><svg class="delete-item" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg></td>
        </tr>
        `);
        document.querySelectorAll('.delete-item').forEach(element => element.onclick = event => {
            event.preventDefault();
            element.closest('tr').remove();
        });
        if (document.querySelector('.no-invoice-items-msg')) {
            document.querySelector('.no-invoice-items-msg').remove();
        }
    };
    
    document.querySelectorAll('.delete-item').forEach(element => element.onclick = event => {
        event.preventDefault();
        element.closest('tr').remove();
    });
    
    if (document.querySelector('.add-client')) {
        document.querySelector('.add-client').onclick = event => {
            event.preventDefault();
            addClient();
        };
    }
    
    if (document.querySelector('#recurrence')) {
        document.querySelector('#recurrence').onchange = () => {
            document.querySelector('.recurrence-options').style.display = document.querySelector('#recurrence').value == 1 ? 'block' : 'none';
        };
    }
};

// Add client modal functionality
const addClient = () => {
    let countries = ['United States', 'United Kingdom', 'France', 'Germany', 'Italy', 'Spain', 'Australia', 'Canada', 'Japan'];
    modal({
        state: 'open',
        modalTemplate: function() {
            return `
            <div class="dialog">
                <div class="content">
                    <h3 class="heading">Add Client<span class="dialog-close">&times;</span></h3>
                    <div class="body">
                        <form class="form">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" placeholder="Email" required>
                
                            <label for="first_name">First Name</label>
                            <input id="first_name" type="text" name="first_name" placeholder="First Name" required>
                
                            <label for="last_name">Last Name</label>
                            <input id="last_name" type="text" name="last_name" placeholder="Last Name">
                
                            <label for="phone">Phone</label>
                            <input id="phone" type="text" name="phone" placeholder="Phone">
                
                            <label for="address_street">Address</label>
                            <input id="address_street" type="text" name="address_street" placeholder="Street">
                
                            <label for="address_city">City</label>
                            <input id="address_city" type="text" name="address_city" placeholder="City">
                
                            <label for="address_state">State</label>
                            <input id="address_state" type="text" name="address_state" placeholder="State">
                
                            <label for="address_zip">Zip</label>
                            <input id="address_zip" type="text" name="address_zip" placeholder="Zip">
                
                            <label for="address_country">Country</label>
                            <select id="address_country" name="address_country">
                                ${countries.map(country => `<option value="${country}">${country}</option>`).join('')}
                            </select>

                            <span class="error-msg"></span>
                        </form>
                    </div>
                    <div class="footer pad-5">
                        <a href="#" class="btn dialog-close save">Add</a>
                    </div>
                </div>
            </div>
            `;
        },
        onClose: function(event) {
            if (event && event.button && event.button.classList.contains('save')) {
                let form = event.source.querySelector('form');
                fetch('ajax.php?action=add_client', {
                    method: 'POST',
                    body: new FormData(form),
                    cache: 'no-cache'
                }).then(response => response.json()).then(res => {
                    if (res.status == 'error') {
                        form.querySelector('.error-msg').innerText = res.message;
                        form.querySelector('.error-msg').scrollIntoView();
                        return false;
                    }
                    if (res.status == 'success') {
                        location.reload();
                    }
                });
                return false;
            }
        }
    });
};

// Quick create invoice functionality
if (document.querySelector('.quick-create-invoice')) {
    document.querySelector('.quick-create-invoice').onclick = event => {
        event.preventDefault();
        fetch('invoice.php', { cache: 'no-cache' }).then(response => response.text()).then(data => {
            let html = (new DOMParser()).parseFromString(data, 'text/html');
            let form = html.querySelector('.form');
            let table = html.querySelector('.manage-invoice-table');
            table.style.display = 'block';
            table.style.overflowX = 'visible';
            modal({
                state: 'open',
                modalTemplate: function() {
                    return `
                    <div class="dialog create-invoice-modal">
                        <div class="content">
                            <h3 class="heading">Quick Create Invoice<span class="dialog-close">&times;</span></h3>
                            <div class="body">
                                <form class="form">
                                    ${form.innerHTML}
                                    ${table.outerHTML}
                                    <span class="error-msg"></span>
                                </form>
                            </div>
                            <div class="footer pad-5">
                                <a href="#" class="btn dialog-close save">Save</a>
                            </div>
                        </div>
                    </div>
                    `;
                },
                onOpen: function(event) {
                    initMultiselect();
                    initManageInvoiceItems();
                },
                onClose: function(event) {
                    if (event && event.button && event.button.classList.contains('save')) {
                        let form = event.source.querySelector('form');
                        fetch('ajax.php?action=create_invoice', {
                            method: 'POST',
                            body: new FormData(form),
                            cache: 'no-cache'
                        }).then(response => response.json()).then(res => {
                            if (res.status == 'error') {
                                form.querySelector('.error-msg').innerText = res.message;
                                form.querySelector('.error-msg').scrollIntoView();
                            }
                            if (res.status == 'success') {
                                event.source.querySelector('.dialog-close').click();
                                if (confirm('Invoice created. Would you like to view it?')) {
                                    location.href = 'view_invoice.php?id=' + res.invoice_id;
                                }
                            }
                        });
                        return false;
                    }
                }
            });   
        });   
    };  
}

// Initialize components when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMultiselect();
    initManageInvoiceItems();
});
