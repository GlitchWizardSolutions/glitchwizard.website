if (document.querySelector('.add_answer')) {
    document.querySelector('.add_answer').onclick = function(event) {
        event.preventDefault();
        let num_answers = document.querySelectorAll('.answers .form-input').length + 1;
        document.querySelector('.answers').insertAdjacentHTML('beforeend', `
            <div>
                <input type="text" name="answers[]" placeholder="Option ${num_answers}" class="form-input mar-top-3">
                ${images_enabled ? `
                <label class="file-input mar-top-2">
                    <span class="file-icon"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,19H5V5H19M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M13.96,12.29L11.21,15.83L9.25,13.47L6.5,17H17.5L13.96,12.29Z" /></svg></span>
                    <span class="file-name">Select Image ${num_answers}...</span>
                    <input id="image" name="images[]" type="file" placeholder="Image" class="image">
                </label>` : ''}
            </div>
        `);
        document.querySelectorAll('.image').forEach(img => img.onchange = event => {
            img.parentElement.querySelector('.file-name').innerHTML = event.target.files[0].name;
        });
    };
    document.querySelectorAll('.image').forEach(img => img.onchange = event => {
        img.parentElement.querySelector('.file-name').innerHTML = event.target.files[0].name;
    });
    document.body.addEventListener('click', event => {
        if (!event.target.closest('.multiselect')) {
            document.querySelectorAll('.multiselect .list').forEach(element => element.style.display = 'none');
        } 
    });
}
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