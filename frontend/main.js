document.addEventListener('alpine:init', () => {
    Alpine.data('flipperWidget', (pages = []) => ({
        pages: [],
        actualPage: 1,
        summaryActive: false,
        zoomActive: false,
        turnedElement: null,
        init() {
            this.pages = pages.sort((a, b) => a.order - b.order);

            this.setupPageViewer();
        },
        setupPageViewer() {
            if (!this.pages.length) return;

            let image = new Image();

            image.src = this.pages[0].attachment.url;
            image.onload = () => {
                const maxWidth  = window.innerWidth - (window.innerWidth * 0.2); 
                const maxHeight = (window.innerHeight - 60) - (window.innerHeight * 0.2);
                const ratio     = Math.min(maxWidth / image.width, maxHeight / image.height);
                let imageWidth  = (image.width * ratio);
                let imageHeight = image.height * ratio;
				
                if (!this.isMobile()) imageWidth = imageWidth * 2;
                if (imageWidth == imageHeight) imageHeight = imageHeight / 2;

                this.turnedElement = jQuery(".flipper-pages");
                this.turnedElement.parent().css({'width': `${imageWidth}px`, 'height': `${imageHeight}px`});
				
				this.turnedElement.find('.page.change').each((index, element) => {
					jQuery(element).removeClass('change');
					jQuery(element).html(`${(index === 1 ? '<div class="gradient odd"></div>' : '' )}<img src="${this.pages[index].attachment.url}" draggable="false">`);
				});

                this.turnedElement.turn({
                    pages: this.pages.length,
                    width: imageWidth,
                    height: imageHeight ,
                    autoCenter: true,
                    display: this.isMobile() ? 'single' : 'double',
                    when: {
                        turning: (event, page, view) => {
                            const element = jQuery(event.target);
                            const range = element.turn('range', page);
        
                            for (let pageRange = range[0]; pageRange <= range[1]; pageRange++) this.addPage(this.pages[pageRange - 1], pageRange);
                        },
                        turned: (e, page) => {
                            this.actualPage = page;
                            
                            // checkNarration();
                        }
                    }
                });

                this.turnedElement.parent().zoom({
                    flipbook: this.turnedElement,
                    max: 1.5,
                    when: {
                        tap: event => this.toggleZoom(event),
                        swipeLeft: () => this.nextPage(),
                        swipeRight: () => this.previousPage(),
                        // zoomIn: () => resizeHotspots(),
                        // zoomOut: () => resizeHotspots()
                    }
                });

                window.addEventListener('keyup', (event) => {
                    if (event.code === 'ArrowLeft') this.previousPage();
                    if (event.code === 'ArrowRight') this.nextPage();
                });
            }
        },
        toggleZoom(event) {
            if (!this.zoomActive) {
                this.turnedElement.parent().zoom('zoomIn', event);
            } else {
                this.turnedElement.parent().zoom('zoomOut');
            }

            this.zoomActive = !this.zoomActive;
        },
        goToPage(pageNumber) {
            if (isNaN(pageNumber) || pageNumber <= 0) pageNumber = 1;
            if (pageNumber > this.pages.length) pageNumber = this.pages.length;

            this.turnedElement.turn('page', pageNumber);
        },
        nextPage() {
            this.turnedElement.turn('next');
        },
        previousPage() {
            this.turnedElement.turn('previous');
        },
        addPage(page, range) {
            if (this.turnedElement.turn('hasPage', range)) return;

            const element = jQuery('<div />', {'class': `page loading ${((range % 2==0) ? 'odd' : 'even')}`}).html(`<div class="gradient ${((range % 2==0) ? 'odd' : 'even')}"><i class="fa-solid fa-circle-notch fa-2x fa-spin"></i>`);
            
            this.turnedElement.turn('addPage', element, range);
            
			setTimeout(() => {
				element.html(`<div class="gradient ${((range % 2==0) ? 'odd' : 'even')}"></div><img src="${page.attachment.url}" alt="${page.attachment.alt}" draggable="false" width="100%" height="100%">`);
			}, 1000);	
        },
        isMobile() {
            const toMatch = [
                /Android/i,
                /webOS/i,
                /iPhone/i,
                /iPad/i,
                /iPod/i,
                /BlackBerry/i,
                /Windows Phone/i
            ];
            
            return toMatch.some((toMatchItem) => {
                return navigator.userAgent.match(toMatchItem);
            });
        }
    }));
});