document.addEventListener('alpine:init', () => {
    Alpine.data('flipperWidget', (pages = []) => ({
        pages: [],
        actualPage: 1,
        summaryActive: false,
        zoomActive: false,
        hasNarration: false,
        narrationActive: false,
        narrationAudio: null,
        narrationCurrentTime: 0,
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

                this.turnedElement.turn({
                    pages: this.pages.length,
                    width: imageWidth,
                    height: imageHeight,
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

                            this.checkNarration();
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
                        zoomIn: () => this.resizePageHotspots(),
                        zoomOut: () => this.resizePageHotspots()
                    }
                });

                this.turnedElement.find('.page.change').each((index, element) => {
					jQuery(element).removeClass('change');
					jQuery(element).html(`${(index === 1 ? '<div class="gradient odd"></div>' : '' )}<img src="${this.pages[index].attachment.url}" draggable="false">`);

                    this.buildPageHotspots(index, jQuery(element));
				});

                window.addEventListener('keyup', (event) => {
                    if (event.code === 'ArrowLeft') this.previousPage();
                    if (event.code === 'ArrowRight') this.nextPage();
                });
            }
        },
        toggleZoom(event, ignoreTarget = false) {
            if (this.narrationActive) return;
            if (!ignoreTarget && !jQuery(event.target).parent().hasClass('page')) return;

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

            const element = jQuery('<div />', {'class': `page`}).html(`<div class="gradient"><i class="fa-solid fa-circle-notch fa-2x fa-spin"></i>`);
            
            this.turnedElement.turn('addPage', element, range);
            
			setTimeout(() => {
				element.html(`<div class="gradient"></div><img src="${page.attachment.url}" alt="${page.attachment.alt}" draggable="false" width="100%" height="100%">`);

                this.buildPageHotspots(range - 1, element);
			}, 1000);	
        },
        startNarration() {
            this.narrationActive = true;
            this.narrationAudio  = jQuery(`.flipper-widget-wrapper .page.p${this.actualPage} .narration-hotspot audio`);

            this.narrationAudio.on('timeupdate', () => this.narrationCurrentTime = this.narrationAudio[0].currentTime);

            this.narrationAudio.on('ended', () => {
                this.turnedElement.turn("disable", false);
                this.narrationAudio.off('ended');
                this.narrationAudio.off('timeupdate');

                if (this.actualPage + 1 > this.pages.length) {
                    this.stopNarration();

                    return;
                }

                this.goToPage(this.actualPage + 1);
            });

            this.turnedElement.turn("disable", true);
            this.narrationAudio.trigger('play');
        },
        stopNarration() {
            this.narrationActive = false;

            this.narrationAudio.trigger('pause');
            this.narrationAudio.off('ended');
            this.narrationAudio.off('timeupdate');
            this.turnedElement.turn("disable", false);
        },
        checkNarration() {
            this.hasNarration = this.pages[this.actualPage - 1].hotspots.findIndex(hotspot => hotspot.type === 'narration') !== -1;

            if (this.hasNarration && !this.narrationActive && this.narrationAudio) {
                this.narrationCurrentTime = 0;
                this.audioElement[0].currentTime = 0;
                this.narrationAudio = null;
            }

            if (this.hasNarration && this.narrationActive) this.startNarration();
            if (!this.hasNarration && this.narrationActive) this.stopNarration();
        },
        timeString(time) {
            let ss = Math.floor(time)
            let hh = Math.floor(ss / 3600)
            let mm = Math.floor((ss - hh * 3600) / 60);

            ss = ss - hh * 3600 - mm * 60;
          
            if (hh > 0) mm = mm < 10 ? "0" + mm : mm;

            ss = ss < 10 ? "0" + ss : ss;

            return hh > 0 ? `${hh}:${mm}:${ss}` : `${mm}:${ss}`;
        },
        buildHotspotIconElement(hotspotElement, hotspot) {
            const iconElement = document.createElement('i');

            iconElement.className                = hotspot.extras.icon_name;
            hotspotElement.style.fontSize        = hotspot.extras.icon_size;
            hotspotElement.style.backgroundColor = hotspot.extras.icon_background;
            hotspotElement.style.color           = hotspot.extras.icon_color;

            if (hotspot.extras.icon_border === 'custom') hotspotElement.style.borderRadius = hotspot.extras.icon_border_custom;
            else hotspotElement.style.borderRadius = hotspot.extras.icon_border;

            hotspotElement.append(iconElement);

            return iconElement;
        },
        buildHotspotPopoverElement(hotspotElement, hotspot, content, typographyStyles = false, usesHTML = false) {
            hotspotElement.setAttribute('data-bs-toggle', 'popover');
            hotspotElement.style.setProperty('--background', hotspot.extras.popover_background);

            if (typographyStyles) {
                hotspotElement.style.setProperty('--font-size', `${hotspot.extras.font_size}px`);
                hotspotElement.style.setProperty('--font-family', hotspot.extras.font_family);
                hotspotElement.style.setProperty('--font-color', hotspot.extras.font_color);
                hotspotElement.style.setProperty('--font-weight', hotspot.extras.font_weight);
                hotspotElement.style.setProperty('--text-decoration', hotspot.extras.text_decoration);
                hotspotElement.style.setProperty('--text-align', hotspot.extras.text_align);
            }

            new bootstrap.Popover(hotspotElement, {
                html: usesHTML,
                animation: false,
                sanitize: false,
                placement: 'auto',
                trigger: 'hover',
                customClass: 'hotspot-popover',
                container: jQuery(hotspotElement),
                content: content
            });
        },
        applyHotspotTypographyStyles(hotspotElement, hotspot) {
            hotspotElement.style.fontSize       = `${hotspot.extras.font_size}px`;
            hotspotElement.style.fontFamily     = hotspot.extras.font_family;
            hotspotElement.style.color          = hotspot.extras.font_color;
            hotspotElement.style.fontWeight     = hotspot.extras.font_weight;
            hotspotElement.style.textDecoration = hotspot.extras.text_decoration;
        },
        resizePageHotspots() {
            this.pages.forEach((page, pageIndex) => {
                const pageNumber = pageIndex + 1;

                if (!this.turnedElement.turn('hasPage', pageNumber)) return true;

                const element = jQuery(`.flipper-widget-wrapper .page.p${pageNumber}`);
                const hotspotWrapper = element.find('.hotspots-wrapper');

                if (hotspotWrapper.length) {
                    hotspotWrapper.remove();

                    setTimeout(() => this.buildPageHotspots(pageIndex, element), 300);
                }
            });
        },
        buildPageHotspots(page, element) {
            const pageHotspots = this.pages[page].hotspots;

            if (!pageHotspots?.length) return;

            const hotspotWrapper = document.createElement('div');

            hotspotWrapper.classList.add(`hotspots-wrapper`);

            pageHotspots.forEach(hotspot => {
                const hotspotElement = document.createElement(hotspot.type === 'link' ? 'a' : 'div');

                if (hotspot.type === 'narration') {
                    const audioElement = document.createElement('audio');
                    audioElement.src   = hotspot.attachment.url;

                    hotspotElement.classList.add(`narration-hotspot`);
                    hotspotElement.append(audioElement);
                    hotspotWrapper.append(hotspotElement);

                    return true;
                }

                hotspotElement.classList.add(`${hotspot.extras.mode}-${hotspot.type}-hotspot`);

                const positionX = (hotspot.position.x / 100) * element.width();
                const positionY = (hotspot.position.y / 100) * element.height();
                const sizeX     = (hotspot.size.width / 100) * element.width();
                const sizeY     = (hotspot.size.height / 100) * element.height();

                hotspotElement.style.width     = `${sizeX}px`;
                hotspotElement.style.height    = `${sizeY}px`;
                hotspotElement.style.transform = `translate(${positionX}px, ${positionY}px)`;

                switch (hotspot.type) {
                    case "link":
                        hotspotElement.href   = hotspot.extras.link_url;
                        hotspotElement.target = hotspot.extras.link_target;

                        switch (hotspot.extras.mode) {
                            case "area":
                            case "icon":
                                hotspotElement.title = hotspot.extras.link_text;
                                
                                if (hotspot.extras.mode === 'icon') this.buildHotspotIconElement(hotspotElement, hotspot);
                            break;

                            default:
                                hotspotElement.innerText = hotspot.extras.link_text;
                                
                                this.applyHotspotTypographyStyles(hotspotElement, hotspot);
                        }
                    break;

                    case "text":
                        switch (hotspot.extras.mode) {
                            case "icon":
                                this.buildHotspotIconElement(hotspotElement, hotspot);
                                this.buildHotspotPopoverElement(hotspotElement, hotspot, hotspot.extras.content, true);
                            break;

                            default:
                                hotspotElement.innerText = hotspot.extras.content;

                                this.applyHotspotTypographyStyles(hotspotElement, hotspot);
                        }
                    break;

                    case "video":
                        const videoElement = document.createElement('video');

                        videoElement.src      = hotspot.attachment.url;
                        videoElement.controls = true;

                        switch (hotspot.extras.mode) {
                            case "icon":
                                this.buildHotspotIconElement(hotspotElement, hotspot);
                                this.buildHotspotPopoverElement(hotspotElement, hotspot, videoElement.outerHTML, false, true);
                            break;

                            default:
                                hotspotElement.append(videoElement);
                        }
                    break;

                    case "image":
                        const imageElement = document.createElement('img');

                        imageElement.src = hotspot.attachment.url;
                        imageElement.alt = hotspot.attachment.alt;
                        imageElement.draggable = false;

                        switch (hotspot.extras.mode) {
                            case "icon":
                                this.buildHotspotIconElement(hotspotElement, hotspot);
                                this.buildHotspotPopoverElement(hotspotElement, hotspot, imageElement.outerHTML, false, true);
                            break;

                            default:
                                hotspotElement.append(imageElement);
                        }
                    break;

                    case "audio":
                        const audioElement = document.createElement('audio');

                        audioElement.src = hotspot.attachment.url;

                        switch (hotspot.extras.mode) {
                            case "icon":
                                const iconElement = this.buildHotspotIconElement(hotspotElement, hotspot);

                                hotspotElement.addEventListener('click', () => {
                                    if (audioElement.paused) {
                                        iconElement.className = hotspot.extras.pause_icon_name;
                                        audioElement.play();

                                        return;
                                    }

                                    iconElement.className = hotspot.extras.icon_name;
                                    audioElement.pause();
                                });
                            break;

                            default:
                                audioElement.controls = true;

                        }

                        hotspotElement.append(audioElement);
                    break;
                }

                hotspotWrapper.append(hotspotElement);
            });

            element.append(hotspotWrapper);
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