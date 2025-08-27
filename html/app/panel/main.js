const renamedItemsInput = document.querySelector('input[type=hidden]');
let RENAMED_ITEMS = {};
const form = document.querySelector('form');

form.addEventListener('submit', (e) => {
	e.preventDefault();
	const formData = new FormData(form);
	fetch('/app/panel/update.php', {
		method: 'POST',
		body: formData
	})
	.then(response => {
		console.log(response);
	})
	.catch(error => {
		console.log(error);
	});
});

class StructureItem extends HTMLElement {

	constructor() {
		super();
		this.type = 'structure';
	}

	init(path) {
		this.label = document.createElement('label');
		this.appendChild(this.label);
		this.setAttribute('originalPath', path);
		this.setAttribute('path', path);
	}

	attributeChangedCallback(attribute, oldValue, newValue) {
		switch (attribute) {
			case 'path':
				var name = newValue.split('/').slice(-1);
				this.label.innerText = name;
				break;
			case 'name':
				var folderPath = this.getAttribute('path').split('/').slice(0,-1).join('/');
				var newPath = folderPath + '/' + newValue;
				this.setAttribute('path', newPath);
				break;
			case 'index':
				var name = this.getAttribute('path').split('/').slice(-1)[0];
				if (/[0-9]{2}(_|\.)/.test(name)) {
					var newName = name.replace(/[0-9]{2}(_|\.)/, `${ newValue }$1`);
				} else {
					var newName = newValue + '_' + name;
				}
				this.setAttribute('name', newName);
				break;
		}
	}
	  
	static get observedAttributes() { return ['path','name','index']; }

}

class ContentItem extends StructureItem {

	constructor() {
		super();
		this.type = 'content';
	}

    init(path) {
        this.thumbnail = document.createElement('figure');
        this.appendChild(this.thumbnail);
        super.init(path);
        this.setThumbnail(path);
	}

    setThumbnail(path) {
        const extension = path.split('.').slice(-1)[0].toLowerCase();
        switch (extension) {
            case 'jpeg':
            case 'jpg':
            case 'png':
            case 'gif':
            case 'webp':
                this.thumbnail.innerHTML = `<img src="/content/${ path }">`;
                break;
        }
    }
}

class StructureList extends HTMLElement {

	constructor() {
		super();
		this.items = [];
		this.type = 'structure';
	}

	init(elements, prefix = '') {
		Object.values(elements).forEach((element) => {
			
			const item = (this.type == 'structure')
				? document.createElement('structure-item')
				: document.createElement('content-item');
			const path = (this.type == 'structure')
				? ((prefix) ? prefix + '/' : '') + element.id
				: ((prefix) ? prefix + '/' : '') + element.basename;

			this.items.push(item);
			item.init(path);
			this.appendChild(item);

			if (element.children) {
				const list = document.createElement('structure-list');
				list.init(element.children, path);
				item.appendChild(list);
			} else if (element.contents) {
				const list = document.createElement('content-list');
				list.init(element.contents, path);
				item.appendChild(list);
			}

		})
	}

	getData() {
		return this.items.map((item) => {
			return {
				path: item.getAttribute('path'),
				children: this.lists.map(list => list.getData()),
			};
		});
	}

}

class ContentList extends StructureList {

	constructor() {
		super();
		this.type = 'content';
	}

	init(elements, prefix) {
		super.init(elements, prefix);
		this.items.forEach((item) => {
			item.draggable = true;
			this.setItemEventListeners(item);			
		});
	}

	setItemEventListeners(item) {
		item.addEventListener('dragstart', (e) => {
			const dragTargetPath = e.target.getAttribute('path');
			e.dataTransfer.setData("text/plain", dragTargetPath);
		});
		item.addEventListener('dragenter', (e) => {
			e.preventDefault();
			if (this.validateDropTarget(e)) {
				e.target.classList.add('is-dropTarget');
			}
		});
		item.addEventListener('dragover', (e) => {
			e.preventDefault();
		});
		item.addEventListener('drop', (e) => {
			e.preventDefault();
			const dragTargetPath = e.dataTransfer.getData("text/plain");
			const dragTarget = this.querySelector(`[path="${ dragTargetPath }"]`);
			const dropTarget = e.target;
			if (this.validateDropTarget(e)) {
				if (dropTarget.compareDocumentPosition(dragTarget) == 4) {
					this.insertBefore(dragTarget, dropTarget);
				} else {
					this.insertBefore(dragTarget, dropTarget.nextSibling);
				}
				
				this.updateItemIndices();
			}
			dropTarget.classList.remove('is-dropTarget');
		});
		item.addEventListener('dragleave', (e) => {
			const dropTarget = e.target;
			dropTarget.classList.remove('is-dropTarget');
		});
	}

	validateDropTarget(e) {
		const dragTargetPath = e.dataTransfer.getData("text/plain");
		const dropTargetPath = e.target.getAttribute('path');
		const dragTargetFolderPath = dragTargetPath.split('/').slice(0,-1).join('/');
		const dropTargetFolderPath = dropTargetPath.split('/').slice(0,-1).join('/');
		return dragTargetPath !== dropTargetPath && dragTargetFolderPath == dropTargetFolderPath;
	}

	updateItemIndices() {
		const itemsSorted = this.items.toSorted((a,b) => {
			const position = a.compareDocumentPosition(b);
			switch (position) {
				case 2:
					return 1;
				case 4:
					return -1;
			}
		});
		itemsSorted.forEach((item, i) => {
			item.setAttribute('index', String(i+1).padStart(2, '0'));
		});
		this.items.forEach((item) => {
			RENAMED_ITEMS[item.getAttribute('originalPath')] = item.getAttribute('path');
			renamedItemsInput.value = JSON.stringify(RENAMED_ITEMS);			
		});
		const formData = new FormData(form);
		fetch('/app/panel/update.php', {
			method: 'POST',
			body: formData
		})
			.then(response => {
				RENAMED_ITEMS = {};
				this.items.forEach((item) => {
					item.setAttribute('originalPath', item.getAttribute('path'));
				});
				return response.text();
			})
			.then((text) => {
				console.log(text);
			})
			.catch(error => {
				console.log(error);
			});
	}

}

class AdminPanel extends HTMLElement {

	constructor() {
		super();
		fetch('/data.php')
			.then((response) => response.json())
			.then((data) => {
				this.entries = data.entries;
				this.init();
			});
	}

	init() {
		this.list = document.createElement('structure-list');
		this.list.init(this.entries);
		this.appendChild(this.list);
	}
}

customElements.define('content-item', ContentItem);
customElements.define('content-list', ContentList);
customElements.define('structure-item', StructureItem);
customElements.define('structure-list', StructureList);
customElements.define('admin-panel', AdminPanel);