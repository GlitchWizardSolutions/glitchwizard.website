class Reviews {

    constructor(options) {
        let defaults = {
            page_id: 1,
            container: document.querySelector('.reviews'),
            php_file_url: 'reviews.php',
            breakdown_status: 'open',
            type: 'full'
        };
        this.options = Object.assign(defaults, options);
        if (this.type == 'full') {
            this.fetchReviews();
        } else if (this.type == 'stars') {
            this.fetchStars();
        }
    }

    fetchReviews(callback = null) {
        let url = `${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}`;
        url += 'current_pagination_page' in this.options ? `&current_pagination_page=${this.currentPaginationPage}` : '';
        url += 'reviews_per_pagination_page' in this.options ? `&reviews_per_pagination_page=${this.reviewsPerPaginationPage}` : '';
        url += 'sort_by' in this.options ? `&sort_by=${this.sortBy}` : '';
        url += 'breakdown_status' in this.options ? `&breakdown_status=${this.breakdownStatus}` : '';
        url += 'type' in this.options ? `&type=${this.type}` : '';
        fetch(url, { cache: 'no-store' }).then(response => response.text()).then(data => {
            this.container.innerHTML = data;
            this._eventHandlers();
            if (location.hash && this.container.querySelector(location.hash)) {
                location.href = location.hash;
            }
            if (callback) callback();
        });
    }

    fetchStars() {
        fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&type=${this.type}`, { cache: 'no-store' }).then(response => response.text()).then(data => {
            this.container.innerHTML = data;
        });
    }

    get reviewsPerPaginationPage() {
        return this.options.reviews_per_pagination_page;
    }

    set reviewsPerPaginationPage(value) {
        this.options.reviews_per_pagination_page = value;
    }

    get currentPaginationPage() {
        return this.options.current_pagination_page;
    }

    set currentPaginationPage(value) {
        this.options.current_pagination_page = value;
    }

    get pageId() {
        return this.options.page_id;
    }

    set pageId(value) {
        this.options.page_id = value;
    }

    get phpFileUrl() {
        return this.options.php_file_url;
    }

    set phpFileUrl(value) {
        this.options.php_file_url = value;
    }

    get container() {
        return this.options.container;
    }

    set container(value) {
        this.options.container = value;
    }

    get sortBy() {
        return this.options.sort_by;
    }

    set sortBy(value) {
        this.options.sort_by = value;
    }

    get type() {
        return this.options.type;
    }

    set type(value) {
        this.options.type = value;
    }

    get breakdownStatus() {
        return this.options.breakdown_status;
    }

    set breakdownStatus(value) {
        if (this.container && this.container.querySelector('.review-breakdown')) {
            if (value == 'closed') {
                this.container.querySelector('.review-breakdown').classList.remove('open');
                this.container.querySelector('.review-breakdown').classList.add('closed');
                this.container.querySelector('.toggle-breakdown-button i').classList.remove('bi-dash');
                this.container.querySelector('.toggle-breakdown-button i').classList.add('bi-plus');
            } else {
                this.container.querySelector('.review-breakdown').classList.remove('closed');
                this.container.querySelector('.review-breakdown').classList.add('open');    
                this.container.querySelector('.toggle-breakdown-button i').classList.remove('bi-plus');
                this.container.querySelector('.toggle-breakdown-button i').classList.add('bi-dash');        
            }
        }
        this.options.breakdown_status = value;
    }

    _eventHandlers() {
        this.container.querySelectorAll('.write-review .star').forEach(star => {
            star.onmouseover = () => {
                for (let i = 1; i <= parseInt(star.dataset.id); i++) {
                    this.container.querySelector('.write-review .star[data-id="' + i + '"]').classList.add('selected');
                }
            };
            star.onmouseout = () => {
                let numStars = this.container.querySelectorAll('.write-review .star').length;
                for (let i = 1; i <= numStars; i++) {
                    this.container.querySelector('.write-review .star[data-id="' + i + '"]').classList.remove('selected');
                }
                if (this.container.querySelector('.write-review input[name="rating"]').value) {
                    for (let i = 1; i <= this.container.querySelector('.write-review input[name="rating"]').value; i++) {
                        this.container.querySelector('.write-review .star[data-id="' + i + '"]').classList.add('selected');
                    }
                }
            };
            star.onclick = () => {
                this.container.querySelector('.write-review input[name="rating"]').value = parseInt(star.dataset.id);
            };
        });
        if (this.container.querySelector('.write-review-btn')) {
            this.container.querySelector('.write-review-btn').onclick = event => {
                event.preventDefault();
                this.container.querySelector('.write-review').style.display = 'block';
                if (this.container.querySelector('.write-review input[name="name"]')) {
                    this.container.querySelector('.write-review input[name="name"]').focus();
                }
            };
            this.container.querySelector('.write-review form').onsubmit = event => {
                event.preventDefault();
                if (this.container.querySelector('.write-review .rating').value == '') {
                    this.container.querySelector('.write-review .msg').innerHTML = '<span class="error">* Please select a rating!</span>';
                } else if (this.container.querySelector('.write-review .content').value == '') {
                    this.container.querySelector('.write-review .msg').innerHTML = '<span class="error">* Please enter your review!</span>';
                } else {
                    let btnElement = this.container.querySelector('.write-review form button');
                    let btnRect = this.container.querySelector('.write-review form button').getBoundingClientRect();
                    btnElement.style.width = btnRect.width + 'px';
                    btnElement.style.height = btnRect.height + 'px';
                    btnElement.innerHTML = '<span class="loader"></span>';
                    fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}`, {
                        method: 'POST',
                        body: new FormData(this.container.querySelector('.write-review form')),
                        cache: 'no-store'
                    }).then(response => response.text()).then(data => {
                        this.container.querySelector('.write-review form').reset();
                        this.container.querySelector('.write-review').style.display = 'none';
                        btnElement.innerHTML = 'Submit';
                        this.container.querySelectorAll('.write-review .star').forEach(star => star.classList.remove('selected'));
                        this.container.querySelector('.write-review .msg').innerHTML = '';
                        this.container.querySelector('.write-review-msg').style.display = 'block';
                        this.container.querySelector('.write-review-msg').innerHTML = data;
                    });
                }
            };
        }
        this.container.querySelectorAll('.sort-by .options a').forEach(element => {
            element.onclick = event => {
                event.preventDefault();
                this.currentPaginationPage = 1;
                this.sortBy = element.dataset.value;
                this.container.querySelector('.sort-by').innerHTML = '<span class="loader"></span>';
                this.fetchReviews();
            };
        });
        this.container.querySelector('.sort-by > a').onclick = event => {
            event.preventDefault();
            this.container.querySelector('.sort-by .options').style.display = 'flex';
        };
        document.body.addEventListener('click', event => { 
            if (!event.target.closest('.sort-by')) {
                this.container.querySelector('.sort-by .options').style.display = 'none';
            }    
        });
        this.container.querySelectorAll('.review-breakdown a').forEach(element => {
            element.onclick = event => {
                event.preventDefault();
                this.currentPaginationPage = 1;
                this.sortBy = 'star_' + element.dataset.star;
                this.container.querySelector('.sort-by').innerHTML = '<span class="loader"></span>';
                this.fetchReviews();
            };
        });
        this.container.querySelector('.toggle-breakdown-button').onclick = event => {
            event.preventDefault();
            if (this.container.querySelector('.review-breakdown').classList.contains('open')) {
                this.container.querySelector('.review-breakdown').classList.remove('open');
                this.container.querySelector('.review-breakdown').classList.add('closed');
                this.container.querySelector('.toggle-breakdown-button i').classList.remove('bi-dash');
                this.container.querySelector('.toggle-breakdown-button i').classList.add('bi-plus');
                this.breakdownStatus = 'closed';
            } else {
                this.container.querySelector('.review-breakdown').classList.remove('closed');
                this.container.querySelector('.review-breakdown').classList.add('open');    
                this.container.querySelector('.toggle-breakdown-button i').classList.remove('bi-plus');
                this.container.querySelector('.toggle-breakdown-button i').classList.add('bi-dash');  
                this.breakdownStatus = 'open';        
            }
        };
        this.container.querySelectorAll('.like-btn').forEach(element => {
            element.onclick = event => {
                event.preventDefault();
                let numLikes = 0;
                let likeMsgElement = null;
                if (element.parentElement.querySelector('.like-msg')) {
                    numLikes = parseInt(element.parentElement.querySelector('.like-msg span').innerHTML);
                    likeMsgElement = element.parentElement.querySelector('.like-msg');
                } else {
                    likeMsgElement = document.createElement('div');
                    likeMsgElement.classList.add('like-msg');
                    element.insertAdjacentElement('beforebegin', likeMsgElement);
                }
                numLikes++;
                if (document.cookie.indexOf(`review_like_${element.dataset.id}=`) == -1) {
                    likeMsgElement.innerHTML = numLikes + (numLikes == 1 ? ' person' : ' people') + ' liked this review.';
                }
                fetch(`${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}page_id=${this.pageId}&like=${element.dataset.id}`, { cache: 'no-store' });
            };
        });
        if (this.reviewsPerPaginationPage && this.currentPaginationPage) {
            this.container.querySelectorAll('.pagination a').forEach(a => {
                a.onclick = event => {
                    event.preventDefault();
                    let btnRect = a.getBoundingClientRect();
                    a.style.width = btnRect.width + 'px';
                    a.style.height = btnRect.height + 'px';
                    a.innerHTML = '<span class="loader"></span>';
                    this.currentPaginationPage = a.dataset.pagination_page;
                    this.reviewsPerPaginationPage = a.dataset.records_per_page;
                    this.fetchReviews(() => {
                        if (this.container.querySelectorAll('.review')) {
                            this.container.querySelectorAll('.review')[0].scrollIntoView();
                        }
                    });
                };
            });
        }
        this.container.querySelectorAll('.review .images img').forEach(img => {
            img.onclick = event => {
                event.preventDefault();
                img.parentElement.parentElement.querySelector('.image').innerHTML = `<img src="${img.src}" alt="">`;
            };
        });
        this.container.querySelectorAll('.content .toolbar .format-btn').forEach(element => element.onclick = () => {
            let textarea = this.container.querySelector('.content textarea');
            let text = '<strong></strong>';
            const format = element.getAttribute('data-format');
            if (format === 'italic') text = '<i></i>';
            else if (format === 'underline') text = '<u></u>';
            textarea.setRangeText(text, textarea.selectionStart, textarea.selectionEnd, 'select');
        });
    }

}