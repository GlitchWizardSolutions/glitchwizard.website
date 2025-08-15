// Container we'll use to show an image
const mediaPopup = document.querySelector('.media-popup');
// The list of files to upload
const uploadFiles = [];
// The list of thumbnails to upload
const uploadThumbnails = [];
// Upload form element
const uploadForm = document.querySelector('.media-upload form');
// Upload media element
const uploadMedia = document.querySelector('.media-upload #media');
// Upload drop zone element
const uploadDropZone = document.querySelector('.media-upload #media-upload-drop-zone');
// Handle the next and previous buttons
const mediaNavigationHandler = mediaLink => {
	// Add the onclick event
	mediaPopup.querySelector('.prev').onclick = event => {
		event.preventDefault();
		// Determine the previous element (media)
		let prevMediaElement = document.querySelector('[data-index="' + (parseInt(mediaLink.dataset.index)-1) + '"]');
		if (prevMediaElement) prevMediaElement.click();
	};
	// Add the onclick event
	mediaPopup.querySelector('.next').onclick = event => {
		event.preventDefault();
		// Determine the next element (media)
		let nextMediaElement = document.querySelector('[data-index="' + (parseInt(mediaLink.dataset.index)+1) + '"]');
		if (nextMediaElement) nextMediaElement.click();
	};
};
// Handle the likes and dislikes
const mediaToggleLike = mediaLink => {
	// Retrieve the like and dislike elements
	let likeBtn = mediaPopup.querySelector('.like');
	// Add the onclick event
	likeBtn.onclick = event => {
		event.preventDefault();
		// Use AJAX to update the value in the database
		fetch('like.php?id=' + mediaLink.dataset.id, { cache: 'no-store' }).then(res => res.text()).then(data => {
			if (data.includes('login')) {
				mediaPopup.querySelector('.like-count').innerHTML = data;
			} else if (data.includes('unlike')) {
				let likes = parseInt(mediaPopup.querySelector('.like-count').innerHTML) - 1;
				mediaPopup.querySelector('.like-count').innerHTML = likes + (likes == 1 ? ' like' : ' likes');
				likeBtn.classList.remove('active');
			} else if (data.includes('like')) {
				let likes = parseInt(mediaPopup.querySelector('.like-count').innerHTML) + 1;
				mediaPopup.querySelector('.like-count').innerHTML = likes + (likes == 1 ? ' like' : ' likes');
				likeBtn.classList.add('active');
			}
		});
	};
};
// Handle media save to collection
const mediaSave = mediaLink => {
	// Retrieve the save element
	let saveBtn = mediaPopup.querySelector('.save');
	// If the save button doesn't exist (user isn't loggedin)
	if (!saveBtn) return;
	// Add the onclick event
	saveBtn.onclick = event => {
		event.preventDefault();
		let userCollections = mediaLink.dataset.userCollections.split(',,').filter(n => n);
		document.body.insertAdjacentHTML('beforebegin', `
			<div class="media-save">
				<div class="con">
					<h2>Add to Collection</h2>
					<form action="add-media-collection.php" method="post" class="gallery-form full">
						<label for="collection">Collection</label>
						<select name="collection" id="collection">
							${userCollections.map(value => '<option value="' + value + '">' + value + '</option>')}
						</select>
						<input type="hidden" name="media_id" value="${mediaLink.dataset.id}">
						<div class="btn-wrapper">
							<button type="submit" class="btn">Save</button>
							<a href="#" class="btn alt close-btn">Close</a>
						</div>
						<p class="result"></p>
					</form>
				</div>
			</div>
		`);
		document.querySelector('.media-save .close-btn').onclick = event => {
			event.preventDefault();
			document.querySelector('.media-save').remove();
		};
		document.querySelector('.media-save').onsubmit = event => {
			event.preventDefault();
			fetch(document.querySelector('.media-save form').action, {
				method: 'POST',
				body: new FormData(document.querySelector('.media-save form')),
				cache: 'no-store'
			}).then(response => response.text()).then(data => {
				document.querySelector('.media-save .result').innerHTML = data;
			});
		};
	};
};
// Handle the media view popup
const mediaModal = (mediaLink, mediaPreview) => {
	// Create the pop out image
	mediaPopup.innerHTML = `
		<a href="#" class="prev${document.querySelector('[data-index="' + (parseInt(mediaLink.dataset.index)-1) + '"]') ? '' : ' hidden'}"><svg width="50" height="50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z" /></svg></a>
		<div class="con">
			<h3 class="media-title"></h3>
			<p class="media-author"></p>
			<p class="media-description"></p>
			<div class="media-preview">${mediaPreview}</div>
			<div class="like-con">
				<a href="#" class="like${mediaLink.dataset.liked == 1 ? ' active' : ''}" title="Like Media">
					<svg class="solid-heart" width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z" /></svg>
					<svg class="outline-heart" width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12.1,18.55L12,18.65L11.89,18.55C7.14,14.24 4,11.39 4,8.5C4,6.5 5.5,5 7.5,5C9.04,5 10.54,6 11.07,7.36H12.93C13.46,6 14.96,5 16.5,5C18.5,5 20,6.5 20,8.5C20,11.39 16.86,14.24 12.1,18.55M16.5,3C14.76,3 13.09,3.81 12,5.08C10.91,3.81 9.24,3 7.5,3C4.42,3 2,5.41 2,8.5C2,12.27 5.4,15.36 10.55,20.03L12,21.35L13.45,20.03C18.6,15.36 22,12.27 22,8.5C22,5.41 19.58,3 16.5,3Z" /></svg>
				</a>
				<span class="like-count">${mediaLink.dataset.likes} like${mediaLink.dataset.likes == 1 ? '' : 's'}</span>
				<div class="action-btns">
					${mediaLink.dataset.ownMedia !== undefined ? '<a href="edit-media.php?id=' + mediaLink.dataset.id + '" class="edit" title="Edit Media"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" /></svg></a>' : ''}
					${mediaLink.dataset.userCollections ? '<a href="#" class="save" title="Add to Collection"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" /></svg></a>' : ''}
					${mediaLink.dataset.collection ? '<a href="delete-collection-media.php?collection_id=' + mediaLink.dataset.collection + '&media_id=' + mediaLink.dataset.id + '" title="Remove Media from Collection" onclick="return confirm(\'Remove media from collection?\')"><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M7,6H17V19H7V6M9,8V17H11V8H9M13,8V17H15V8H13Z" /></svg></a>' : ''}
					<a href="#" class="share" title="Share Media"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,12L14,5V9C7,10 4,15 3,20C5.5,16.5 9,14.9 14,14.9V19L21,12Z" /></svg></a>
				</div>
			</div>
		</div>
		<a href="#" class="next${document.querySelector('[data-index="' + (parseInt(mediaLink.dataset.index)+1) + '"]') ? '' : ' hidden'}"><svg width="50" height="50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" /></svg></a>
		<a href="#" class="close"><svg width="30" height="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg></a>
	`;
	mediaPopup.style.display = 'flex';
	// Set the media title, description and author
	mediaPopup.querySelector('.media-title').innerText = mediaLink.dataset.title;
	mediaPopup.querySelector('.media-description').innerText = mediaLink.dataset.description;
	mediaPopup.querySelector('.media-author').innerText = 'Uploaded on ' + mediaLink.dataset.uploadedDate + ' by ' + mediaLink.dataset.user;
	// Execute the media_mext_prev function
	mediaNavigationHandler(mediaLink);
	// Execute the media_like_dislike function
	mediaToggleLike(mediaLink);
	// Execute the mediaSave function
	mediaSave(mediaLink);
	// Share media link
	mediaPopup.querySelector('.share').onclick = event => {
		event.preventDefault();
		// Attempt to share the media link using the Web Share API
		if (navigator.share) {
			navigator.share({
				title: mediaLink.dataset.title,
				text: mediaLink.dataset.description,
				url: websiteUrl + 'view.php?id=' + mediaLink.dataset.id
			}).then(() => {
				// update share button with ticket icon
				mediaPopup.querySelector('.share').innerHTML = `<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>`;
			});
		} else {
			// copy link to clipboard
			navigator.clipboard.writeText(websiteUrl + 'view.php?id=' + mediaLink.dataset.id).then(() => {
				// update share button with ticket icon
				mediaPopup.querySelector('.share').innerHTML = `<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>`;
			});
		}
	};
	// Handle the X button in the top right corner
	mediaPopup.querySelector('.close').onclick = event => {
		event.preventDefault();
		mediaPopup.style.display = 'none';
		mediaPopup.innerHTML = '';		
	};
	// Handle keydown events for the media popup
	document.onkeydown = event => {
		if (mediaPopup.style.display == 'flex') {
			// If the user presses the Escape key, close the popup
			if (event.key == 'Escape') {
				mediaPopup.style.display = 'none';
				mediaPopup.innerHTML = '';		
			}
			// If the user presses the left or right arrow keys, navigate to the previous or next media element
			if (event.key == 'ArrowLeft') {
				let prevMediaElement = document.querySelector('[data-index="' + (parseInt(mediaLink.dataset.index)-1) + '"]');
				if (prevMediaElement) prevMediaElement.click();
			}
			if (event.key == 'ArrowRight') {
				let nextMediaElement = document.querySelector('[data-index="' + (parseInt(mediaLink.dataset.index)+1) + '"]');
				if (nextMediaElement) nextMediaElement.click();
			}
		}
	};
};
// Handle the media file upload
const addMediaFile = (file, collection = '') => {
	// Determine the file type
	let fileType = file.type.toLowerCase();
	let fileTypeSplit = fileType.split('/');
	// Check if the file type is allowed
	if (uploadForm.getAttribute('data-' + fileTypeSplit[0] + '-max-size') == null) {
		alert('File type not allowed: ' + file.name + ' (' + fileType + ')');
		return false;
	}
	// Determine the max file size
	let maxFileSize = Number(uploadForm.getAttribute('data-' + fileTypeSplit[0] + '-max-size'));
	// Check if the file size is larger than the max file size
	if (file.size > maxFileSize) {
		alert('File size is too large: ' + file.name + ' (' + (file.size/1024).toFixed(2) + ' KB)');
		return false;
	}
	// Add the media form element to the media list
	document.querySelector(".media-list").insertAdjacentHTML('beforeend', `
		<div class="media" data-index="${uploadFiles.length}">
			<div class="media-preview">
				${fileType.includes('image') ? `<img src="${URL.createObjectURL(file)}" alt="preview">` : ''}
				${fileType.includes('audio') ? `<audio src="${URL.createObjectURL(file)}" controls></audio>` : ''}
				${fileType.includes('video') ? `<video src="${URL.createObjectURL(file)}" controls></video>` : ''}
			</div>
			<div class="media-preview-info">
				<h3>${file.name}</h3>
				<p>${file.type}</p>
				<p>${(file.size/1024).toFixed(2)} KB</p>
			</div>
			<div class="media-options">
				<a href="#" class="media-edit-btn">
					<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5,3C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19H5V5H12V3H5M17.78,4C17.61,4 17.43,4.07 17.3,4.2L16.08,5.41L18.58,7.91L19.8,6.7C20.06,6.44 20.06,6 19.8,5.75L18.25,4.2C18.12,4.07 17.95,4 17.78,4M15.37,6.12L8,13.5V16H10.5L17.87,8.62L15.37,6.12Z" /></svg>
				</a>
				<a href="#" class="media-remove-btn">
					<svg width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
				</a>
			</div>
			<div class="media-dropdown-options">
				<label for="title_${uploadFiles.length}">Title</label>
				<input type="text" id="title_${uploadFiles.length}" name="title_${uploadFiles.length}" id="title" value="${file.name}" placeholder="Title" required>
		
				<label for="description_${uploadFiles.length}">Description</label>
				<textarea id="description_${uploadFiles.length}" name="description_${uploadFiles.length}" id="description" placeholder="Description"></textarea>
		
				${fileType.includes('audio') || fileType.includes('video') ? `
				<label for="thumbnail_${uploadFiles.length}" class="thumbnail_${uploadFiles.length}">Thumbnail</label>
				<input type="file" id="thumbnail_${uploadFiles.length}" name="thumbnail_${uploadFiles.length}" accept="image/*" class="thumbnail thumbnail_${uploadFiles.length}">
				` : ''}
				${fileType.includes('video') ? `<a href="#" class="form-link select-video-frame">Or select frame from video</a><div class="thumbnail-preview"></div>` : ''}

				<label for="public_${uploadFiles.length}">Who can view this media?</label>
				<select id="public_${uploadFiles.length}" name="public_${uploadFiles.length}" type="text" required>
					<option value="1">Everyone</option> 
					<option value="0">Only Me</option>
				</select>

				<label for="collection">Collection</label>
				<select id="collection" name="collection">
					<option value="">(none)</option>
					${uploadForm.dataset.userCollections.split(',,').filter(n => n).map(item => '<option value="' + item + '"' + (item == collection ? ' selected' : '') + '>' + item + '</option>')}
				</select>
			</div>
		</div>
	`);
	// Add file to the upload files array
	uploadFiles.push(file);
	return true;
};
// Handle the upload form events
const uploadFormEventHandlers = () => {
	if (!uploadFiles.length) {
		return false;
	}
	document.querySelector('.btn-wrapper').style.display = 'flex';
	// Toggle the media dropdown options (Title, Description, etc.)
	document.querySelectorAll('.media-edit-btn').forEach(el => el.onclick = event => {
		event.preventDefault();
		event.currentTarget.closest('.media').querySelector('.media-dropdown-options').classList.toggle('active');
	});
	// Remove the media file from the form list
	document.querySelectorAll('.media-remove-btn').forEach(el => el.onclick = event => {
		event.preventDefault();
		uploadFiles[event.currentTarget.closest('.media').dataset.index] = null;
		event.currentTarget.closest('.media').remove();
		if (!document.querySelector('.media-list .media')) {
			document.querySelector('.btn-wrapper').style.display = 'none';
		}
	});
	// Capture the video frame and generate a thumbnail
	document.querySelectorAll('.select-video-frame').forEach(el => el.onclick = event => {
		event.preventDefault();
		const mediaContainer = event.currentTarget.closest('.media');
		let video = mediaContainer.querySelector('video');
		let generatedThumbnailObjectUrl = null;
		const modalHTML = `
			<div class="media-select-frame">
				<div class="con">
					<h2>Select frame from video</h2>
					<video class="media-select-frame-video" src="${video.src}" width="100%" height="100%" controls></video>
					<input type="range" min="0" max="${video.duration}" value="0" step="0.1">
					<div class="btn-wrapper">
						<a href="#" class="btn select-frame-btn">Select frame</a>
						<a href="#" class="btn alt close-btn">Close</a>
					</div>
				</div>
			</div>
		`;
		document.body.insertAdjacentHTML('beforeend', modalHTML);
		const modal = document.querySelector('.media-select-frame');
		const modalVideo = modal.querySelector('.media-select-frame-video');
		const rangeInput = modal.querySelector('input[type="range"]');
		modalVideo.onloadedmetadata = () => rangeInput.max = modalVideo.duration;
		if (modalVideo.readyState >= 1) {
			rangeInput.max = modalVideo.duration;
		}
		rangeInput.oninput = event => {
			if (!isNaN(modalVideo.duration)) modalVideo.currentTime = event.target.value;
		};
		modal.querySelector('.close-btn').onclick = event => {
			event.preventDefault();
			modal.remove();
			if (generatedThumbnailObjectUrl) {
				URL.revokeObjectURL(generatedThumbnailObjectUrl);
				generatedThumbnailObjectUrl = null;
			}
		};
		modal.querySelector('.select-frame-btn').onclick = event => {
			event.preventDefault();
			let canvas = document.createElement('canvas');
			canvas.width = modalVideo.videoWidth;
			canvas.height = modalVideo.videoHeight;
			try {
				canvas.getContext('2d').drawImage(modalVideo, 0, 0, canvas.width, canvas.height);
				canvas.toBlob(function(blob) {
					if (!blob) {
						alert('Could not generate thumbnail! Please try again!');
						modal.remove();
						return;
					}
					let generatedThumbnailFile = new File([blob], 'thumbnail.jpg', { type: 'image/jpeg' });
					uploadThumbnails.push({
						file: generatedThumbnailFile,
						name: `thumbnail_${mediaContainer.dataset.index}`
					})
					let previewContainer = mediaContainer.querySelector('.thumbnail-preview');
					if (previewContainer) {
						if (generatedThumbnailObjectUrl) {
							URL.revokeObjectURL(generatedThumbnailObjectUrl);
						}
						generatedThumbnailObjectUrl = URL.createObjectURL(blob);
						previewContainer.innerHTML = '';
						previewContainer.insertAdjacentHTML('beforeend', `<img src="${generatedThumbnailObjectUrl}" alt="thumbnail">`);
					}
					modal.remove();
				}, 'image/jpeg', 0.8);
			} catch (e) {
				alert('Could not capture video frame! Please try again!');
				modal.remove();
				if (generatedThumbnailObjectUrl) {
					URL.revokeObjectURL(generatedThumbnailObjectUrl);
					generatedThumbnailObjectUrl = null;
				}
			}
		};
		modalVideo.onerror = () => {
			alert('Could not load video for frame selection! Please try again!');
			modal.remove();
		};
	});
	return true;
};
// Handle masonry responsive layout
const sortMasonry = () => {
	const breakpoint = 800; // Set your desired breakpoint here
	const container = document.querySelector('.layout-masonry');
	const columns = container.querySelectorAll('.masonry-column');
	if (columns.length === 0) return;
	if (window.innerWidth < breakpoint) {
		if (columns.length > 0 && columns[0].style.display !== 'none') {
			columns.forEach(column => {
				while (column.firstChild) {
					container.insertBefore(column.firstChild, column);
				}
				column.style.display = 'none';
			});
		}
	} else {
		const columnMap = {};
		columns.forEach(column => {
			column.style.display = '';
			if (column.dataset.columnIndex !== undefined) {
				columnMap[column.dataset.columnIndex] = column;
			}
		});
		const itemsToDistribute = container.querySelectorAll(':scope > *:not(.masonry-column)[data-original-column]');
		if (itemsToDistribute.length > 0) {
			itemsToDistribute.forEach(item => {
				const originalColumnIndex = item.dataset.originalColumn;
				if (originalColumnIndex !== undefined) {
					const targetColumn = columnMap[originalColumnIndex];
					if (targetColumn) {
						targetColumn.appendChild(item);
					}
				}
			});
		}
	}
};
if (document.querySelector('.layout-masonry')) {
	let resizeTimer;
	window.addEventListener('resize', () => {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(sortMasonry, 250);
	});
	sortMasonry();
}
// If the media popup element exists...
if (mediaPopup) {
	// Iterate the media and create the onclick events
	document.querySelectorAll('.media-list a').forEach(mediaLink => {
		// If the user clicks the media
		mediaLink.onclick = event => {
			event.preventDefault();
			// If the media type is an image
			if (mediaLink.dataset.type == 'image') {
				// Create new image object
				let img = new Image();
				// Image onload event, show the image popup
				img.onload = () => mediaModal(mediaLink, `<img src="${img.src}" width="${img.width}" height="${img.height}" alt="preview">`);
				// If image not found, show empty image
				img.onerror = () => mediaModal(mediaLink, `(Media not found)`);
				// Set the image source
				img.src = mediaLink.dataset.src;
			} else {
				// Determine the media type
	            let element = mediaLink.dataset.type == 'video' ? `<video src="${mediaLink.dataset.src}" width="852" height="480" controls autoplay></video>` : `<audio src="${mediaLink.dataset.src}" controls autoplay></audio>`;
				// Show the media popup
				mediaModal(mediaLink, element);
			}
		};
		// Autoplay video on hover (if enabled)
		if (mediaLink.dataset.autoplay && mediaLink.dataset.type == 'video') {
			mediaLink.onmouseover = () => {
				if (!mediaLink.querySelector('video')) {
					mediaLink.insertAdjacentHTML('beforeend', `<video src="${mediaLink.dataset.src}" width="100%" height="100%" autoplay muted></video>`);
				}
			};
			mediaLink.onmouseleave = () => {
				if (mediaLink.querySelector('video')) {
					mediaLink.querySelector('video').remove();
					if (mediaLink.querySelector('.placeholder')) {
						mediaLink.querySelector('.placeholder').style.color = '';
						mediaLink.querySelector('.placeholder svg').style.fill = '';
					}
				}
			};
		}
	});
	// Hide the image popup container, but only if the user clicks outside the image
	mediaPopup.onclick = event => {
		if (event.target.className == 'media-popup') {
			mediaPopup.style.display = 'none';
	        mediaPopup.innerHTML = '';
		}
	};
}
// Check whether the upload form element exists, which basically means the user is on the upload page
if (uploadForm) {
	// Upload form submit event
	uploadForm.onsubmit = event => {
		event.preventDefault();
		// Create a new FormData object and retrieve data from the upload form
		let uploadFormData = new FormData(uploadForm);
		let numFiles = 0;
		for (let i = 0; i < uploadFiles.length; i++) {
			if (uploadFiles[i] == null) continue;
			uploadFormData.append('file_' + i, uploadFiles[i]);
			numFiles++;
		}
		if (numFiles == 0) {
			document.querySelector('.upload-result').innerHTML = 'Please select a media file!';
			return false;
		}
		// Append the thumbnail files to the FormData object
		for (let i = 0; i < uploadThumbnails.length; i++) {
			if (uploadThumbnails[i] == null) continue;
			if (uploadFormData.has(uploadThumbnails[i].name)) {
				uploadFormData.delete(uploadThumbnails[i].name);
			}
			uploadFormData.append(uploadThumbnails[i].name, uploadThumbnails[i].file);
		}
		uploadFormData.append('total_files', uploadFiles.length);
		// Create a new AJAX request
		let request = new XMLHttpRequest();
		// POST request
		request.open('POST', uploadForm.action);
		// Add the progress event
		request.upload.addEventListener('progress', event => {
			// Update the submit button with the current upload progress in percent format
			let progress = 'Uploading... ' + '(' + ((event.loaded/event.total)*100).toFixed(2) + '%)';
			document.querySelector('.upload-result').innerHTML = progress;
			document.title = progress;
			// Disable the submit button
			uploadForm.querySelector('#submit_btn').disabled = true;
		});
		// Check if the upload is complete or if there are any errors
		request.onreadystatechange = () => {
			if (request.readyState == 4 && request.status == 200) {
				// Upload is complete
				if (request.responseText.includes('Complete')) {
					// Output the successful response
					document.querySelector('.upload-result').innerHTML = request.responseText;
					document.title = request.responseText;
				} else {
					// Output the errors
					uploadForm.querySelector('#submit_btn').disabled = false;
					document.querySelector('.upload-result').innerHTML = request.responseText;
				}
			} else if (request.readyState == 4 && request.status == 413) {
				// File is too large
				document.querySelector('.upload-result').innerHTML = 'One or more files are too large!';
				uploadForm.querySelector('#submit_btn').disabled = false;
			} else if (request.readyState == 4 && request.status == 422) {
				// File type is not allowed
				document.querySelector('.upload-result').innerHTML = 'One or more files are not allowed!';
				uploadForm.querySelector('#submit_btn').disabled = false;
			} else if (request.readyState == 4 && request.status != 200) {
				// An error occurred
				document.querySelector('.upload-result').innerHTML = 'An error occurred while uploading the media!';
			}
		};
		// Send the request
		request.send(uploadFormData);
	};
	// On media change, display the thumbnail form element, but only if the media type is either a video or image
	uploadMedia.onchange = () => {
		for (let i = 0; i < uploadMedia.files.length; i++) {
			addMediaFile(uploadMedia.files[i], uploadForm.dataset.collection);
		}
		uploadFormEventHandlers();
	};
	// On drag and drop media file, do the same as the above code, but in addition, update the media file variable
	uploadDropZone.ondrop = event => {
		event.preventDefault();
		for (let i = 0; i < event.dataTransfer.items.length; i++) {
			if (event.dataTransfer.items && event.dataTransfer.items[i].kind === 'file') {
				addMediaFile(event.dataTransfer.items[i].getAsFile(), uploadForm.dataset.collection);
			}
		}
		uploadFormEventHandlers();
	};
	// Dragover drop zone event
	uploadDropZone.ondragover = event => {
		event.preventDefault();
		// Update the element style; add CSS class
		uploadDropZone.classList.add('dragover');
	};
	// Dragleave drop zone event
	uploadDropZone.ondragleave = event => {
		event.preventDefault();
		// Update the element style; remove CSS class
		uploadDropZone.classList.remove('dragover');
	};
	// Click drop zone event
	uploadDropZone.onclick = event => {
		event.preventDefault();
		// Click the media file upload element, which will show the open file dialog
		document.querySelector('.media-upload #media').click();
	}
}