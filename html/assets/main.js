class SpeakerNotesElement extends HTMLElement {
	constructor() {
		super();
		this.classList.add('SpeakerNotes');
	}
	update(text) {
		this.innerText = text;
	}
}

class SlideshowElement extends HTMLElement {
	constructor() {
		super();
		this.position = 0;
		this.slides = this.querySelectorAll('.Slide');
		this.speakerNotes = document.createElement('speaker-notes');
		
		this.init();
	}

	init() {
		const hashVal = Number(window.location.hash.replace('#',''));
		const targetIndex = (hashVal >= 0 && hashVal < this.slides.length) 
			? hashVal 
			: 0;		
		this.setEventListeners();
		this.activateSlide(targetIndex);
	}

	openSpeakerNotes() {
		this.window = window.open('', '', `popup,width=650,height=200,screenX=${ screen.width },screenY=${ screen.height }`);
		this.window.document.body.appendChild(this.speakerNotes);
		this.activateSlide(this.position);
	}

	setEventListeners() {
		window.addEventListener('keydown', (e) => {
			switch (e.code) {
				case 'ArrowLeft':
					this.activateSlide(this.position - 1);
					break;
				case 'ArrowRight':
					this.activateSlide(this.position + 1);
					break;
				case 'KeyC':
					this.openSpeakerNotes();
					break;
			}
		});
		window.addEventListener('beforeunload', () => {
			if (this.window) {
				this.window.close();
			}
		})
	}

	activateSlide(i) {
		if (i < this.slides.length && i >= 0) {
			
			// update current slide
			this.currentSlide.classList.remove('is-active');
			if (this.currentSlide.querySelector('video')) {
				this.currentSlide.querySelector('video').pause();
			}

			// update target slide
			const targetSlide = this.slides[i];
			targetSlide.classList.add('is-active');
			if (targetSlide.querySelector('video')) {
				targetSlide.querySelector('video').play();
			}

			// update speaker notes
			const slideNotes = targetSlide.querySelector('.Slide-notes');
			if (this.window) {
				this.window.document.body.innerHTML = `
					<div style="font-size: 18px;">
						<strong>${ i + 1 }/${ this.slides.length }</strong>
						<br>
						<div>
							${ slideNotes ? slideNotes.innerHTML : '' }
						</div>
					</div>
				`;
			}
			
			// update position
			this.position = i;
			window.location.hash = `#${ i }`;
		}
	}

	get currentSlide() {
		return this.slides[this.position];
	}

	get nextSlide() {
		return this.slides[this.position + 1];
	}

	get previousSlide() {
		return this.slides[this.position - 1];
	}

	slideHasVideo(slide) {
		return slide.querySelector('video')
	}
}

customElements.define('slide-show', SlideshowElement);
customElements.define('speaker-notes', SpeakerNotesElement);