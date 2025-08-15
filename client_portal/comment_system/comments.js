class Comments {

    constructor(options) {
        let defaults = {
            page_id: 1,
            container: document.querySelector('.cs-comments'),
            php_file_url: 'comments.php',
            editor: 'quill',
            search: '',
            toggledComments: []
        };
        this.options = Object.assign(defaults, options);
        this.quillInstances = {};
        this.fetchComments();
    }

    fetchComments(callback) {
        let url = `${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}`;
        url += 'comments_to_show' in this.options ? `&comments_to_show=${this.commentsToShow}` : '';
        url += 'sort_by' in this.options ? `&sort_by=${this.sortBy}` : '';
        url += 'editor' in this.options ? `&editor=${this.editor}` : '';
        url += location.hash && location.hash.includes('comment') ? `&highlight_comment=${location.hash.replace('#','').replace('comment-','')}` : '';
        fetch(url, { cache: 'no-store' }).then(response => response.text()).then(data => {
            this.container.innerHTML = data;
            this.templateHtml = this.container.querySelector('.cs-write-comment[data-comment-id="-1"]').outerHTML;
            this.commentsToShow = parseInt(this.container.querySelector('.cs-comment-header').dataset.maxComments || '10');
            if (typeof this.maxComments === 'undefined') {
                this.maxComments = parseInt(this.container.querySelector('.cs-comment-header').dataset.maxComments || '10');
            }
            this._eventHandlers();
            this.options.toggledComments.forEach(commentId => {
                const toggleElement = this.container.querySelector(`.cs-comment[data-id="${commentId}"] .cs-toggle-comment`);
                if (toggleElement) {
                    toggleElement.dispatchEvent(new Event('click'));
                }
            });
            if (location.hash && this.container.querySelector(location.hash)) {
                location.href = location.hash;
            }
            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    _initEditor(formElement) {
        const writeCommentDiv = formElement.closest('.cs-write-comment');
        const commentId = writeCommentDiv.dataset.commentId;
        const editorType = writeCommentDiv.dataset.editor;
        const charCounter = formElement.querySelector('.cs-char-counter');
        if (writeCommentDiv.dataset.initialized) return;
        if (editorType === 'quill') {
            const editorContainer = formElement.querySelector('.cs-wysiwyg-editor');
            const maxLength = editorContainer.dataset.maxlength || 1000;
            const toolbar = formElement.querySelector('.cs-toolbar');
            const hiddenTextarea = formElement.querySelector('.cs-hidden-textarea');
            const quill = new Quill(editorContainer, {
                modules: { toolbar: toolbar },
                theme: 'snow',
                placeholder: 'Add to the discussion...'
            });
            this.quillInstances[commentId] = quill;
            quill.on('text-change', () => {
                const textLength = quill.getText().trim().length;
                if (charCounter) {
                    charCounter.textContent = `${textLength} / ${maxLength}`;
                    charCounter.classList.toggle('cs-limit-exceeded', textLength > maxLength);
                }
                if (textLength > maxLength) {
                    quill.deleteText(maxLength, textLength);
                }
                hiddenTextarea.value = quill.root.innerHTML;
            });
            if (hiddenTextarea.value) {
                quill.root.innerHTML = hiddenTextarea.value;
            }
        } else {
            const toolbar = formElement.querySelector('.cs-toolbar');
            const textarea = formElement.querySelector('.cs-manual-textarea');
            const maxLength = textarea.getAttribute('maxlength') || 1000;
            toolbar.addEventListener('click', (e) => {
                e.preventDefault();
                const button = e.target.closest('.cs-format-btn');
                if (!button) return;
                const command = button.dataset.value;
                let tags;
                switch(command) {
                    case 'strong': tags = ['<strong>', '</strong>']; break;
                    case 'em': tags = ['<em>', '</em>']; break;
                    case 'u': tags = ['<u>', '</u>']; break;
                    case 's': tags = ['<s>', '</s>']; break;
                    case 'blockquote': tags = ['<blockquote>', '</blockquote>']; break;
                    case 'pre': tags = ['<pre><code>', '</code></pre>']; break;
                    case 'img': 
                        const imgUrl = prompt('Enter the URL:', 'https://');
                        if (imgUrl) tags = [`<img src="${imgUrl}">`, ''];
                        break;
                    case 'a': 
                        const url = prompt('Enter the URL:', 'https://');
                        if (url) tags = [`<a href="${url}" target="_blank" rel="nofollow noopener noreferrer">`, '</a>'];
                        break;
                }
                if (tags) {
                    const start = textarea.selectionStart, end = textarea.selectionEnd;
                    textarea.value = textarea.value.substring(0, start) + tags[0] + textarea.value.substring(start, end) + tags[1] + textarea.value.substring(end);
                    textarea.focus();
                    if (start === end) textarea.selectionStart = textarea.selectionEnd = start + tags[0].length;
                }
            });
            textarea.oninput = () => {
                const textLength = textarea.value.trim().length;
                if (charCounter) {
                    charCounter.textContent = `${textLength} / ${maxLength}`;
                    charCounter.classList.toggle('cs-limit-exceeded', textLength > maxLength);
                }
                if (textLength > maxLength) {
                    textarea.value = textarea.value.substring(0, maxLength);
                }
            };
        }
        writeCommentDiv.dataset.initialized = 'true';
    }
    
    _initSubmitHandler(form) {
        form.onsubmit = event => {
            event.preventDefault();
            const writeCommentDiv = form.closest('.cs-write-comment');
            const commentId = writeCommentDiv.dataset.commentId;
            const editorType = writeCommentDiv.dataset.editor;
            const msgElement = form.querySelector('.cs-msg');
            let textLength = 0;
            let content = '';
            let minLength, maxLength;
            if (editorType === 'quill' && this.quillInstances[commentId]) {
                const quill = this.quillInstances[commentId];
                content = quill.root.innerHTML;
                textLength = quill.getText().trim().length;
                form.querySelector('.cs-hidden-textarea').value = content;
                minLength = parseInt(form.querySelector('.cs-hidden-textarea').minLength);
                maxLength = parseInt(form.querySelector('.cs-hidden-textarea').maxLength);
            } else {
                content = form.querySelector('.cs-manual-textarea').value;
                textLength = content.trim().length;
                minLength = parseInt(form.querySelector('.cs-manual-textarea').minLength);
                maxLength = parseInt(form.querySelector('.cs-manual-textarea').maxLength);
            }
            if (textLength === 0) {
                msgElement.innerHTML = `<span class="cs-error">* Please enter a comment.</span>`;
                return;
            }
            if (textLength < minLength) {
                msgElement.innerHTML = `<span class="cs-error">* Comment must be at least ${minLength} characters long.</span>`;
                return;
            }
            if (textLength > maxLength) {
                msgElement.innerHTML = `<span class="cs-error">* Comment cannot be more than ${maxLength} characters long.</span>`;
                return;
            }
            const postButton = form.querySelector('.cs-post-button');
            postButton.disabled = true;
            form.querySelector('.cs-loader').classList.remove('cs-hidden');
            fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&editor=${this.editor}`, {
                method: 'POST', 
                body: new FormData(form),
                cache: 'no-store'
            }).then(response => response.text()).then(data => {
                if (data.includes('Error')) {
                    msgElement.innerHTML = `<span class="cs-error">* ${data.replace('Error: ', '')}</span>`;
                    postButton.disabled = false;
                } else if (data.includes('Note')) {
                    msgElement.innerHTML = `<span class="cs-note">* ${data.replace('Note: ', '')}</span>`;
                } else if (data.includes('comment')) {
                    this.fetchComments(() => {
                        const newCommentId = data.replace('comment-', '');
                        const comment = this.container.querySelector(`.cs-comment[data-id="${newCommentId}"]`);
                        if (comment) {
                            comment.getBoundingClientRect();
                            comment.classList.add('cs-new-comment-animation');
                        }
                    });
                }
                form.querySelector('.cs-loader').classList.add('cs-hidden');
            });
        };
    }

    _eventHandlers() {
        const mainWriteCommentDiv = this.container.querySelector('.cs-write-comment[data-comment-id="-1"]');
        const placeholder = this.container.querySelector('.cs-comment-placeholder-content');
        if (placeholder && mainWriteCommentDiv) {
            placeholder.onfocus = e => {
                e.preventDefault();
                if (placeholder.dataset.loginRequired) {
                    placeholder.blur();
                    this.container.querySelector('.cs-modal-login').showModal();
                } else {
                    placeholder.style.display = 'none';
                    mainWriteCommentDiv.classList.remove('cs-hidden');
                    if (!mainWriteCommentDiv.dataset.initialized) {
                        this._initEditor(mainWriteCommentDiv.querySelector('form'));
                        this._initSubmitHandler(mainWriteCommentDiv.querySelector('form'));
                    }
                    if (mainWriteCommentDiv.querySelector('#name') && mainWriteCommentDiv.querySelector('#name').value === '') {
                        mainWriteCommentDiv.querySelector('#name').focus();
                    } else if (mainWriteCommentDiv.dataset.editor == 'quill') {
                        this.quillInstances['-1'].focus();
                    } else {
                        mainWriteCommentDiv.querySelector('.cs-manual-textarea').focus();
                    }
                }
            };
            mainWriteCommentDiv.querySelector('.cs-cancel-button').onclick = e => {
                e.preventDefault();
                mainWriteCommentDiv.classList.add('cs-hidden');
                if (placeholder) placeholder.style.display = 'block';
                if (mainWriteCommentDiv.dataset.editor == 'quill' && this.quillInstances['-1']) {
                    this.quillInstances['-1'].setContents([]);
                } else {
                    mainWriteCommentDiv.querySelector('.cs-manual-textarea').value = '';
                }
            };
            if (mainWriteCommentDiv.querySelector('#name')) {
                const storedName = localStorage.getItem('cs_comment_name');
                if (storedName) {
                    mainWriteCommentDiv.querySelector('#name').value = storedName;
                }
                mainWriteCommentDiv.querySelector('#name').oninput = () => {
                    localStorage.setItem('cs_comment_name', mainWriteCommentDiv.querySelector('#name').value);
                };
            }
        }
        this.container.querySelectorAll('.cs-reply-comment-btn, .cs-edit-comment-btn').forEach(element => {
            if (parseInt(this.container.querySelector('.cs-comment-header').dataset.pageStatus) === 0) {
                element.style.display = 'none';
                return;
            }
            element.onclick = e => {
                e.preventDefault();
                if (element.dataset.loginRequired) {
                    this.container.querySelector('.cs-modal-login').showModal();
                    return;
                }
                this.container.querySelectorAll('.cs-write-comment[data-comment-id]:not([data-comment-id="-1"])').forEach(form => form.remove());
                this.container.querySelectorAll('.cs-comment-content').forEach(el => el.style.display = 'block');
                this.container.querySelectorAll('.cs-edit-comment-btn.cs-selected, .cs-reply-comment-btn.cs-selected').forEach(btn => btn.classList.remove('cs-selected'));
                element.classList.add('cs-selected');
                const isEdit = element.classList.contains('cs-edit-comment-btn');
                const commentId = element.getAttribute('data-comment-id');
                const commentDiv = element.closest('.cs-comment');
                const tempWrapper = document.createElement('div');
                tempWrapper.innerHTML = this.templateHtml;
                const newFormDiv = tempWrapper.firstChild;
                const form = newFormDiv.querySelector('form');
                let originalContent = '';
                if (isEdit) {
                    const contentDiv = commentDiv.querySelector('.cs-comment-content');
                    originalContent = contentDiv.innerHTML;
                    contentDiv.style.display = 'none';
                    form.querySelector('.cs-post-button').innerHTML = 'Update';
                }
                newFormDiv.dataset.commentId = isEdit ? commentId : `reply-${commentId}`;
                form.querySelector('input[name="comment_id"]').value = isEdit ? commentId : -1;
                form.querySelector('input[name="parent_id"]').value = isEdit ? (commentDiv.dataset.parentId || -1) : commentId;
                if (form.querySelector('.cs-hidden-textarea')) {
                    form.querySelector('.cs-hidden-textarea').value = originalContent;
                } else if (form.querySelector('.cs-manual-textarea')) {
                    originalContent = originalContent.replace(/<br>/g, '\n');
                    const txt = document.createElement('textarea');
                    txt.innerHTML = originalContent;
                    form.querySelector('.cs-manual-textarea').value = txt.value;
                }
                commentDiv.querySelector('.cs-replies').insertAdjacentElement('beforebegin', newFormDiv);
                newFormDiv.classList.remove('cs-hidden');
                this._initEditor(form);
                this._initSubmitHandler(form);
                if (newFormDiv.querySelector('#name')) {
                    const storedName = localStorage.getItem('cs_comment_name');
                    if (storedName) {
                        newFormDiv.querySelector('#name').value = storedName;
                    }
                    newFormDiv.querySelector('#name').oninput = () => {
                        localStorage.setItem('cs_comment_name', newFormDiv.querySelector('#name').value);
                    };
                }
                if (newFormDiv.querySelector('#name') && newFormDiv.querySelector('#name').value === '') {
                    newFormDiv.querySelector('#name').focus();
                } else if (newFormDiv.dataset.editor === 'quill') {
                    this.quillInstances[newFormDiv.dataset.commentId].focus();
                } else {
                    newFormDiv.querySelector('.cs-manual-textarea').focus();
                }
                newFormDiv.querySelector('.cs-cancel-button').onclick = e => { 
                    e.preventDefault(); 
                    newFormDiv.remove(); 
                    element.classList.remove('cs-selected');
                    if (isEdit) commentDiv.querySelector('.cs-comment-content').style.display = 'block';
                };
            };
        });
        this.container.querySelectorAll('.cs-share-comment-btn').forEach(element => element.onclick = event => {
            event.preventDefault();
            if (!navigator.canShare) {
                navigator.clipboard.writeText(location.href.split('#')[0] + '#comment-' + element.getAttribute('data-comment-id'));
                element.innerHTML = '<svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg> Copied !';
            } else {
                navigator.share({
                    title: 'Comment',
                    text: 'Check out this comment',
                    url: location.href.split('#')[0] + '#comment-' + element.getAttribute('data-comment-id')
                });
            }
        });
        this.container.querySelectorAll('.cs-toggle-comment').forEach(element => {
            element.onclick = event => {             
                event.preventDefault();
                const isExpanded = element.querySelector('.cs-toggle-minus');
                element.parentElement.parentElement.querySelector('.cs-comment-content').style.display = isExpanded ? 'none' : null;
                element.parentElement.parentElement.querySelector('.cs-replies').style.display = isExpanded ? 'none' : null;
                element.innerHTML = isExpanded ? '<svg class="cs-toggle-plus" width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" /></svg>' : '<svg class="cs-toggle-minus" width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,13H5V11H19V13Z" /></svg>';
                if (isExpanded) {
                    const commentId = element.closest('.cs-comment').getAttribute('data-id');
                    if (!this.options.toggledComments.includes(commentId)) {
                        this.options.toggledComments.push(commentId);
                    }
                }
            }
        });
        this.container.querySelectorAll('.cs-toggle-comment-menu').forEach(element => {
            element.onclick = event => {
                event.preventDefault();
                this.container.querySelectorAll('.cs-comment-options').forEach(el => el.remove());
                const id = element.closest('.cs-comment').getAttribute('data-id');
                this.container.insertAdjacentHTML('beforeend', `
                    <div class="cs-options cs-comment-options">
                        ${element.dataset.isAdmin && element.dataset.accountId ? `<a href="#" class="cs-ban-user">${element.dataset.isBanned ? 'Unban' : 'Ban'} User</a>` : ''}
                        ${element.dataset.isAdmin ? `<a href="#" class="cs-feature-comment">${element.dataset.isFeatured ? 'Unfeature' : 'Feature'} Comment</a>` : ''}
                        ${element.dataset.isAdmin ? `<a href="admin/comment.php?id=${id}">Moderate</a>` : ''}
                        ${element.dataset.canDelete && !element.closest('.cs-comment').querySelector('.cs-comment-content').innerHTML.includes('This comment has been deleted.') ? `<a href="#" class="cs-delete-comment">Delete</a>` : ''}
                        <a href="#" class="cs-report-comment cs-alt">Report</a>
                    </div>
                `);
                const commentOptionsElement = this.container.querySelector('.cs-comment-options');
                commentOptionsElement.style.left = `${element.offsetLeft-130}px`;
                commentOptionsElement.style.top = `${element.offsetTop+30}px`;
                commentOptionsElement.style.display = 'flex';
                if (commentOptionsElement.querySelector('.cs-delete-comment')) {
                    commentOptionsElement.querySelector('.cs-delete-comment').onclick = event => {
                        event.preventDefault();
                        if (confirm('Are you sure you want to delete this comment?')) {
                            fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&delete_comment=${id}`, { cache: 'no-store' }).then(response => response.text()).then(data => {
                                if (data.includes('success')) {
                                    this.fetchComments();
                                }
                            });
                        }
                    };
                }
                if (commentOptionsElement.querySelector('.cs-ban-user')) {
                    commentOptionsElement.querySelector('.cs-ban-user').onclick = event => {
                        event.preventDefault();
                        let status = element.dataset.isBanned ? 'unban' : 'ban';
                        if (confirm('Are you sure you want to ' + status + ' this user?')) {
                            let deleteComments = !element.dataset.isBanned && confirm('Delete ALL comments by this user?') ? 1 : 0;
                            fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&acc_id=${element.dataset.accountId}&delete_all_comments=${deleteComments}&method=ban_user`, { cache: 'no-store' }).then(response => response.text()).then(data => {
                                if (data.includes('success')) {
                                    this.fetchComments();
                                }
                            });
                        }
                    };
                }
                if (commentOptionsElement.querySelector('.cs-feature-comment')) {
                    commentOptionsElement.querySelector('.cs-feature-comment').onclick = event => {
                        event.preventDefault();
                        let status = element.dataset.isFeatured ? 'unfeature' : 'feature';
                        if (confirm('Are you sure you want to ' + status + ' this comment?')) {
                            fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&feature_comment=${id}`, { cache: 'no-store' }).then(response => response.text()).then(data => {
                                if (data.includes('success')) {
                                    this.fetchComments();
                                }
                            });
                        }
                    };
                }
                commentOptionsElement.querySelector('.cs-report-comment').onclick = event => {
                    event.preventDefault();
                    this.container.querySelectorAll('.cs-modal-report .cs-modal-close').forEach(element => element.onclick = event => {
                        event.preventDefault()
                        this.container.querySelector('.cs-modal-report').close();
                    });
                    this.container.querySelector('.cs-modal-report form').onsubmit = event => {
                        event.preventDefault();
                        const formData = new FormData(this.container.querySelector('.cs-modal-report form'));
                        formData.append('report_comment', id);
                        let btnElement = this.container.querySelector('.cs-modal-report .cs-submit-btn');
                        btnElement.innerHTML = '<span class="cs-loader"></span>';
                        btnElement.disabled = true;
                        fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}`, {
                            method: 'POST',
                            body: formData
                        }).then(response => response.text()).then(data => {
                            btnElement.innerHTML = 'Report';
                            btnElement.disabled = false;
                            if (data.includes('success')) {
                                this.container.querySelector('.cs-modal-report').close();
                                this.container.querySelector('.cs-modal-report form').reset();
                            } else {
                                this.container.querySelector('.cs-modal-report .cs-msg').innerHTML = `<span class="cs-error">* ${data.replace('Error: ', '')}</span>`;
                            }
                        });
                    };
                    this.container.querySelector('.cs-modal-report').showModal();
                };
            }
        });
        this.container.querySelectorAll('.cs-vote').forEach(element => {
            element.onclick = event => {
                event.preventDefault();
                fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&vote=${element.getAttribute('data-vote')}&comment_id=${element.getAttribute('data-comment-id')}`, { cache: 'no-store' }).then(response => response.text()).then(data => {
                    element.parentElement.querySelector('.cs-num').innerHTML = data;
                });
            };
        });
        this.container.querySelectorAll('.cs-sort-by .cs-options a').forEach(element => {
            element.onclick = event => {
                event.preventDefault();
                this.sortBy = element.dataset.value;
                this.container.querySelector('.cs-sort-by').innerHTML = `<span class='cs-loader'></span>`;
                this.fetchComments();
            };
        });
        this.container.querySelector('.cs-sort-by > a').onclick = event => {
            event.preventDefault();
            this.container.querySelector('.cs-sort-by .cs-options').style.display = 'flex';
        };
        if (this.container.querySelector('.cs-profile-info')) {
            this.container.querySelector('.cs-profile-info .cs-profile-photo').onclick = event => {
                event.preventDefault();
                this.container.querySelector('.cs-profile-info .cs-options').style.display = 'flex';
            };
            this.container.querySelectorAll('.cs-profile-info .cs-options a').forEach(element => {
                element.onclick = event => {
                    if (!element.dataset.action) return;
                    event.preventDefault();
                    if (element.dataset.action == 'logout') {
                        fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&method=logout`, { cache: 'no-store' }).then(response => response.text()).then(data => {
                            if (data == 'success') {
                                this.fetchComments();
                            }
                        });
                    }
                    if (element.dataset.action == 'edit') {
                        this.container.querySelector('.cs-modal-edit-profile').showModal();
                        this.container.querySelectorAll('.cs-modal-edit-profile .cs-modal-close').forEach(element => element.onclick = event => {
                            event.preventDefault()
                            this.container.querySelector('.cs-modal-edit-profile').close();
                        });
                        this.container.querySelector('.cs-modal-edit-profile form').onsubmit = event => {
                            event.preventDefault();
                            const formData = new FormData(this.container.querySelector('.cs-modal-edit-profile form'));
                            let btnElement = this.container.querySelector('.cs-modal-edit-profile .cs-submit-btn');
                            btnElement.innerHTML = '<span class="cs-loader"></span>';
                            btnElement.disabled = true;
                            fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&method=edit_profile`, {
                                method: 'POST',
                                body: formData
                            }).then(response => response.text()).then(data => {
                                btnElement.innerHTML = 'Save';
                                btnElement.disabled = false;
                                if (data.includes('success')) {
                                    this.container.querySelector('.cs-modal-edit-profile').close();
                                    this.fetchComments();
                                } else {
                                    this.container.querySelector('.cs-modal-edit-profile .cs-msg').innerHTML = `<span class="cs-error">* ${data.replace('Error: ', '')}</span>`;
                                }
                            });
                        };
                    }
                };
            });
        }
        window.addEventListener('click', event => {
            if (!event.target.closest('.cs-sort-by') && this.container.querySelector('.cs-sort-by .cs-options')) {
                this.container.querySelector('.cs-sort-by .cs-options').style.display = 'none';
            }
            if (!event.target.closest('.cs-profile-info') && this.container.querySelector('.cs-profile-info .cs-options')) {
                this.container.querySelector('.cs-profile-info .cs-options').style.display = 'none';
            }
            if (!event.target.closest('.cs-toggle-comment-menu') && this.container.querySelector('.cs-comment-options')) {
                this.container.querySelector('.cs-comment-options').remove();
            }
        });
        if (this.container.querySelector('.cs-show-more-comments')) {
            this.container.querySelector('.cs-show-more-comments').onclick = event => {
                event.preventDefault();
                if (this.container.querySelector('.cs-show-more-comments a').disabled) return;
                this.commentsToShow += this.maxComments;
                let oldBtnText = this.container.querySelector('.cs-show-more-comments a').innerHTML;
                this.container.querySelector('.cs-show-more-comments a').innerHTML = '<span class="cs-loader"></span>';
                this.container.querySelector('.cs-show-more-comments a').disabled = true;
                if (this.search.length > 0) {
                    this.searchComments(this.search);  
                    this.container.querySelector('.cs-show-more-comments a').innerHTML = oldBtnText;
                    this.container.querySelector('.cs-show-more-comments a').disabled = false;
                } else {
                    this.fetchComments();
                }
            };
        }
        if (this.container.querySelector('.cs-modal-login') && this.container.querySelector('.cs-login-btn')) {
            this.container.querySelector('.cs-login-btn').onclick = event => {
                event.preventDefault();
                this.container.querySelector('.cs-modal-login').showModal();
            };
            this.container.querySelectorAll('.cs-modal-login .cs-modal-close').forEach(element => element.onclick = event => {
                event.preventDefault()
                this.container.querySelector('.cs-modal-login').close();
            });
            if (this.container.querySelector('.cs-modal-login .cs-modal-register-link')) {
                this.container.querySelector('.cs-modal-login .cs-modal-register-link').onclick = event => {
                    event.preventDefault();
                    this.container.querySelector('.cs-modal-login').close();
                    this.container.querySelector('.cs-modal-register').showModal();
                };
            }
            this.container.querySelector('.cs-comment-login-form').onsubmit = event => {
                event.preventDefault();
                let btnElement = this.container.querySelector('.cs-comment-login-form .cs-submit-btn');
                btnElement.innerHTML = '<span class="cs-loader"></span>';
                btnElement.disabled = true;
                fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&method=login`, {
                    method: 'POST',
                    body: new FormData(this.container.querySelector('.cs-comment-login-form')),
                    cache: 'no-store'
                }).then(response => response.text()).then(data => {
                    btnElement.innerHTML = 'Login';
                    btnElement.disabled = false;
                    if (data.includes('success')) {
                        this.fetchComments();
                    } else {
                        this.container.querySelector('.cs-comment-login-form .cs-msg').innerHTML = `<span class="cs-error">* ${data.replace('Error: ', '')}</span>`;
                    }
                });
            };
        }
        if (this.container.querySelector('.cs-modal-register')) {
            this.container.querySelectorAll('.cs-modal-register .cs-modal-close').forEach(element => element.onclick = event => {
                event.preventDefault()
                this.container.querySelector('.cs-modal-register').close();
            });
            if (this.container.querySelector('.cs-modal-register .cs-modal-login-link')) {
                this.container.querySelector('.cs-modal-register .cs-modal-login-link').onclick = event => {
                    event.preventDefault();
                    this.container.querySelector('.cs-modal-register').close();
                    this.container.querySelector('.cs-modal-login').showModal();
                };
            }
            this.container.querySelector('.cs-comment-register-form').onsubmit = event => {
                event.preventDefault();
                let btnElement = this.container.querySelector('.cs-comment-register-form .cs-submit-btn');
                btnElement.innerHTML = '<span class="cs-loader"></span>';
                btnElement.disabled = true;
                fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&method=register`, {
                    method: 'POST',
                    body: new FormData(this.container.querySelector('.cs-comment-register-form')),
                    cache: 'no-store'
                }).then(response => response.text()).then(data => {
                    btnElement.innerHTML = 'Register';
                    btnElement.disabled = false;
                    if (data.includes('success')) {
                        this.fetchComments();
                    } else {
                        this.container.querySelector('.cs-comment-register-form .cs-msg').innerHTML = `<span class="cs-error">* ${data.replace('Error: ', '')}</span>`;
                    }
                });
            };
        }
        if (this.container.querySelector('.cs-search-btn')) {
            this.container.querySelector('.cs-search-btn').onclick = event => {
                event.preventDefault();
                const searchInput = this.container.querySelector('.cs-comment-search');
                this.container.querySelector('.cs-comment-search').style.display = searchInput.style.display == 'block' ? 'none' : 'block';
                this.container.querySelector('.cs-comment-search input').focus();
            };
            this.container.querySelector('.cs-comment-search input').onkeyup = event => {
                if (this.container.querySelector('.cs-search-btn').classList.contains('cs-search-local')) {
                    this.container.querySelectorAll('.cs-comments-wrapper .cs-comment').forEach(comment => {
                        comment.style.display = comment.querySelector('.cs-comment-content').innerHTML.toLowerCase().includes(event.target.value.toLowerCase()) ? null : 'none';
                    });
                } else {
                    this.search = event.target.value;
                    this.searchComments(this.search);
                }
            };
        }
    }

    searchComments(value) {
        let url = `${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}`;
        url += 'comments_to_show' in this.options ? `&comments_to_show=${this.commentsToShow}` : '';
        url += 'sort_by' in this.options ? `&sort_by=${this.sortBy}` : '';
        url += 'editor' in this.options ? `&editor=${this.editor}` : '';
        fetch(`${url}&method=search&query=${encodeURIComponent(value)}`).then(response => response.text()).then(data => {
            this.container.querySelector('.cs-comments-wrapper').innerHTML = data;
            this._eventHandlers();
        });
    }

    get search() { return this.options.search; }
    set search(value) { this.options.search = value; }

    get editor() { return this.options.editor; }
    set editor(value) { this.options.editor = value; }

    get maxComments() { return this.options.max_comments; }
    set maxComments(value) { this.options.max_comments = value; }

    get commentsToShow() { return this.options.comments_to_show; }
    set commentsToShow(value) { this.options.comments_to_show = value; }

    get pageId() { return this.options.page_id; }
    set pageId(value) { this.options.page_id = value; }

    get phpFileUrl() { return this.options.php_file_url; }
    set phpFileUrl(value) { this.options.php_file_url = value; }

    get container() { return this.options.container; }
    set container(value) { this.options.container = value; }

    get sortBy() { return this.options.sort_by; }
    set sortBy(value) { this.options.sort_by = value; }

}