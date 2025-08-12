document.addEventListener('alpine:init', () => {
    if (window.pagenow === 'wa_page_flipper') {
        wp.media.featuredImage.frame().on('open', () => {
			const library = wp.media.featuredImage.frame().state().get('library');

			library.props.set({
				order: 'ASC'
			});

			library._requery(true);
		});
        
        const pagesMediaFrame = wp.media({
            multiple: 'add',
            library: {
                type: [ 'image' ],
                order: 'ASC'
            }
        });

        const audioHotspotMediaFrame = wp.media({
            multiple: false,
            library: {
                type: [ 'audio' ],
                order: 'ASC'
            }
        });

        const imageHotspotMediaFrame = wp.media({
            multiple: false,
            library: {
                type: [ 'image' ],
                order: 'ASC'
            }
        });

        const videoHotspotMediaFrame = wp.media({
            multiple: false,
            library: {
                type: [ 'video' ],
                order: 'ASC'
            }
        });

        const pdfMediaFrame = wp.media({
            multiple: false,
            library: {
                type: [ 'application/pdf' ],
                order: 'ASC'
            }
        });

        const hotspotObject = {
            id: null,
            type: '',
            position: { x: 45, y: 45 },
            size: {width: null, height: null},
            attachment: {},
            extras: { 
                mode: 'icon',
                icon_border: '50%',
                icon_color: '#ffffff',
                icon_background: '#2271b1',
                popover_background: '#333333',
                icon_name: 'fa-solid fa-circle-info',
                pause_icon_name: 'fa-solid fa-pause',
                icon_size: 15,
                font_size: 15,
                font_family: '',
                font_color: '#000000',
                text_align: 'left',
                font_weight: 'normal',
                text_decoration: 'none',
                link_type: 'url',
                link_url: '',
                link_target: '_blank',
                link_text: '',
                link_page: '',
                video_controls: 'no',
                video_muted: 'yes',
                video_autoplay: 'yes',
                video_loop: 'yes'
            }
        };
    
        Alpine.data('flipperBuilder', (pages = []) => ({
            sortable: null,
            pages: [],
            hotspotType: "",
            hotspotToEdit: null,
            hotspotWrapperWidth: 0,
            hotspotWrapperHeight: 0,
            selectedPage: null,
            init() {
                this.$watch('selectedPage', () => this.setupHotspotsWrapperSizes());
    
                if (pages.length) {
                    const sortedPages = pages.sort((a, b) => a.order - b.order);
                    
                    this.pages = sortedPages;
                    this.selectedPage = this.pages[0];
    
                    setTimeout(() => this.setupPageListSort(), 300);
                }
    
                this.setupPagesMediaFrameListeners();
                this.setupHotspotMediaFrameListeners(audioHotspotMediaFrame);
                this.setupHotspotMediaFrameListeners(imageHotspotMediaFrame);
                this.setupHotspotMediaFrameListeners(videoHotspotMediaFrame);
            },
            setupPagesMediaFrameListeners() {
                pagesMediaFrame.on('select', () => {
                    const selection = pagesMediaFrame.state().get('selection');
                    
                    this.pages = this.pages.filter(page => 
                        selection.findIndex(attachment => attachment.id == page.attachment.id) !== -1
                    );
                    
                    selection.forEach((attachment, index) => {
                        const page = this.pages.find(page => page.attachment.id === attachment.id);
    
                        if (page) {
                            if (!this.selectedPage) this.selectedPage = page;
    
                            page.attachment = {
                                id: attachment.id,
                                title: attachment.attributes.title,
                                url: attachment.attributes.url,
                                alt: attachment.attributes.alt,
                            };
    
                            return true;
                        }

                        const newPage = {
                            id: this.generateRandomId(),
                            order: this.pages.length,
                            hotspots: [],
                            attachment: {
                                id: attachment.id,
                                title: attachment.attributes.title,
                                url: attachment.attributes.url,
                                alt: attachment.attributes.alt,
                            },
                        };

                        if (!this.selectedPage && index === 0) this.selectedPage = newPage;
    
                        this.pages.push(newPage);
                    });
    
                    setTimeout(() => this.setupPageListSort(), 300);
                });
    
                pagesMediaFrame.on('open', () => {
                    const selection = pagesMediaFrame.state().get('selection');
    
                    if (!this.pages.length) return;
    
                    this.pages.forEach(page => {
                        const attachment = wp.media.attachment(page.attachment.id);
    
                        selection.add(attachment ? [attachment] : []);
                    })
                });
            },
            setupHotspotMediaFrameListeners(media) {
                media.on('select', () => {
                    const selection = media.state().get('selection');
                    
                    selection.forEach((attachment, index) => {
                        if (this.hotspotToEdit) {
                            this.hotspotToEdit.attachment.id    = attachment.id
                            this.hotspotToEdit.attachment.title = attachment.attributes.title
                            this.hotspotToEdit.attachment.url   = attachment.attributes.url
                            this.hotspotToEdit.attachment.alt   = attachment.attributes.alt
                            this.hotspotToEdit.attachment.mime  = attachment.attributes.mime

                            if (this.hotspotType === 'narration') this.hotspotToEdit.attachment.duration = attachment.attributes.fileLength;

                            this.hotspotToEdit = null;
    
                            return;
                        }

                        const newHotspot            = this.cloneObject(hotspotObject);
                        newHotspot.id               = this.generateRandomId();
                        newHotspot.type             = this.hotspotType;
                        newHotspot.extras.icon_name = this.getDefaultHotspotIcon(this.hotspotType);
                        newHotspot.attachment.id    = attachment.id
                        newHotspot.attachment.title = attachment.attributes.title
                        newHotspot.attachment.url   = attachment.attributes.url
                        newHotspot.attachment.alt   = attachment.attributes.alt
                        newHotspot.attachment.mime  = attachment.attributes.mime

                        if (this.hotspotType === 'narration') newHotspot.attachment.duration = attachment.attributes.fileLength;

                        this.selectedPage.hotspots.push(newHotspot);
                    });
    
                    setTimeout(() => this.setupHotspotsInteractions(), 300);
                });
    
                media.on('open', () => {
                    const selection = media.state().get('selection');

                    selection.reset();

                    if (this.hotspotToEdit) {
                        const attachment = wp.media.attachment(this.hotspotToEdit.attachment.id);
        
                        selection.add(attachment ? [attachment] : []);
                    }
                });
            },
            setupPageListSort() {
                const builderPageList = document.querySelector('.flipper-builder-wrapper .page-list');
                
                if (!builderPageList || this.sortable) return;
            
                this.sortable = new Sortable(builderPageList, {
                    handle: '.drag-page',
                    ghostClass: '.ghost-page',
                    animation: 150,
                    onEnd: (event) => {
                        const { oldIndex, newIndex } = event;
            
                        if (oldIndex === newIndex) return; 
    
                        const movedPage = this.pages.find(page => page.order === oldIndex);
    
                        if (!movedPage) return;
    
                        this.pages.forEach(page => {
                            if (page.order === oldIndex) {
                                page.order = newIndex;
                            } else if (oldIndex < newIndex && page.order > oldIndex && page.order <= newIndex) {
                                page.order -= 1;
                            } else if (oldIndex > newIndex && page.order < oldIndex && page.order >= newIndex) {
                                page.order += 1;
                            }
                        });
                    }
                });
            },
            setupHotspotsWrapperSizes() {
                if (!this.selectedPage) return;

                const image = document.querySelector('.flipper-builder-wrapper .flipper-page img');
    
                image.onload = () => {
                    const container = getComputedStyle(image.parentElement);
                    const containerWidth = parseFloat(container.width.replace('px', ''));
                    const containerHeight = parseFloat(container.height.replace('px', ''));
                    const imgRatio = image.naturalWidth / image.naturalHeight;
                    const containerRatio = containerWidth / containerHeight;
                    let renderedWidth, renderedHeight;
    
                    if (imgRatio > containerRatio) {
                        renderedWidth = containerWidth;
                        renderedHeight = containerWidth / imgRatio;
                    } else {
                        renderedHeight = containerHeight;
                        renderedWidth = containerHeight * imgRatio;
                    }
    
                    this.hotspotWrapperWidth  = renderedWidth;
                    this.hotspotWrapperHeight = renderedHeight;

                    setTimeout(() => this.setupHotspotsInteractions(), 300);
                }
            },
            setupHotspotsInteractions() {
                if (!this.selectedPage.hotspots.length) return;

                this.selectedPage.hotspots.forEach(hotspot => {
                    if (hotspot.type === 'narration') return true;

                    const interactInstance = interact(`.hotspot-container.interact`);

                    interactInstance.resizable({
                        edges: { left: true, right: true, bottom: true, top: true },
                        ignoreFrom: '.hotspot-extras',
                        listeners: {
                            move: (event) => {
                                const target = event.target;
                                const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.deltaRect.left;
                                const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.deltaRect.top;
                        
                                target.style.width = event.rect.width + 'px';
                                target.style.height = event.rect.height + 'px';
                                target.style.transform = `translate(${x}px, ${y}px)`;
                        
                                target.setAttribute('data-x', x);
                                target.setAttribute('data-y', y);
                            },
                            end: (event) => {
                                const oldHotspot = this.selectedPage.hotspots.find(item => item.id == event.target.getAttribute('data-id'));

                                oldHotspot.size.width  = (event.rect.width * 100) / this.hotspotWrapperWidth;
                                oldHotspot.size.height = (event.rect.height * 100) / this.hotspotWrapperHeight;
                                oldHotspot.position.x  = (parseFloat(event.target.getAttribute('data-x')) * 100) / this.hotspotWrapperWidth;
                                oldHotspot.position.y  = (parseFloat(event.target.getAttribute('data-y')) * 100) / this.hotspotWrapperHeight;
                            }
                        },
                        inertia: true,
                        modifiers: [
                            interact.modifiers.restrictEdges({ outer: 'parent' }),
                            interact.modifiers.restrictSize({ min: { width: 50, height: 50 } })
                        ],
                    });
                    
                    interactInstance.draggable({
                        ignoreFrom: '.hotspot-extras',
                        listeners: {
                            move: (event) => {
                                const target = event.target;
                                const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                                const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                                target.style.transform = `translate(${x}px, ${y}px)`;
                        
                                target.setAttribute('data-x', x);
                                target.setAttribute('data-y', y);
                            },
                            end: (event) => {
                                const oldHotspot = this.selectedPage.hotspots.find(item => item.id == event.target.getAttribute('data-id'));

                                oldHotspot.position.x = (parseFloat(event.target.getAttribute('data-x')) * 100) / this.hotspotWrapperWidth;
                                oldHotspot.position.y = (parseFloat(event.target.getAttribute('data-y')) * 100) / this.hotspotWrapperHeight;
                            }
                        },
                        inertia: true,
                        modifiers: [
                            interact.modifiers.restrictRect({ restriction: 'parent', endOnly: true })
                        ]
                    });
                });
            },
            isSelected(page) {
                return this.selectedPage.id === page.id;
            },
            selectPage(page) {
                this.selectedPage = page;
            },
            removePage(page) {
                const pageIndex = this.pages.findIndex(item => item.attachment.id === page.attachment.id);
    
                this.pages.splice(pageIndex, 1);
                this.pages.forEach(item => {
                    if (item.order > page.order) item.order -= 1;
                });
    
                if (this.selectedPage.id === page.id && this.pages.length) this.selectPage(this.pages[0]);
                if (!this.pages.length) this.sortable = null;
            },
            addPages() {
                pagesMediaFrame.open();
            },
            removePages() {
                this.pages = [];
                this.selectedPage = null;
                this.sortable = null;
            },
            removeHotspot(hotspot) {
                this.selectedPage.hotspots = this.selectedPage.hotspots.filter(item => item.id !== hotspot.id);
            },
            generateRandomId() {
                return Math.random().toString(36).slice(2, 8) + Math.random().toString(36).slice(2, 3);
            },
            addHotspot(type) {
                this.hotspotType = type;
                this.hotspotToEdit = null;

                switch (type) {
                    case "narration":
                        const hotspot = this.selectedPage.hotspots.find(hotspot => hotspot.type === type);

                        if (hotspot) this.editHotspotMedia(hotspot);
                        else audioHotspotMediaFrame.open();
                    break;

                    case "audio":
                        audioHotspotMediaFrame.open();
                    break;

                    case "image":
                        imageHotspotMediaFrame.open();
                    break;

                    case "video":
                        videoHotspotMediaFrame.open();
                    break;

                    default:
                        const newHotspot = this.cloneObject(hotspotObject);
                        newHotspot.id    = this.generateRandomId();
                        newHotspot.type  = type;
                        newHotspot.extras.icon_name = this.getDefaultHotspotIcon(type);

                        this.selectedPage.hotspots.push(newHotspot);

                        setTimeout(() => this.setupHotspotsInteractions(), 300);
                }
            },
            editHotspotMedia(hotspot) {
                this.hotspotType   = hotspot.type;
                this.hotspotToEdit = hotspot;

                switch (hotspot.type) {
                    case "narration":
                    case "audio":
                        audioHotspotMediaFrame.open();
                    break;

                    case "image":
                        imageHotspotMediaFrame.open();
                    break;

                    case "video":
                        videoHotspotMediaFrame.open();
                    break;
                }
            },
            buildHotspotInitialAttributes(hotspot) {
                if (hotspot.type === 'narration') return {
                    "class": 'narration-hotspot'
                };

                
                if (!hotspot.size.width || !hotspot.size.height) {
                    const oldHotspot = this.selectedPage.hotspots.find(item => item.id === hotspot.id);
                    
                    oldHotspot.size.width = (50 / this.hotspotWrapperWidth) * 100;
                    oldHotspot.size.height = (50 / this.hotspotWrapperHeight) * 100;
                }

                const positionX = (hotspot.position.x / 100) * this.hotspotWrapperWidth;
                const positionY = (hotspot.position.y / 100) * this.hotspotWrapperHeight;
                const sizeX     = (hotspot.size.width / 100) * this.hotspotWrapperWidth;
                const sizeY     = (hotspot.size.height / 100) * this.hotspotWrapperHeight;
            
                return {
                    "data-id": hotspot.id,
                    "data-x": positionX,
                    "data-y": positionY,
                    "style": `transform: translate(${positionX}px, ${positionY}px); width: ${sizeX}px; height: ${sizeY}px;`,
                    "class": 'interact'
                };
            },
            getDefaultHotspotIcon(type) {
                switch (type) {
                    case "audio":
                        return "fa-solid fa-volume-high";

                    case "image":
                        return "fa-solid fa-image";

                    case "video":
                        return "fa-solid fa-video";

                    case "text":
                        return "fa-solid fa-font";

                    default:
                        return "fa-solid fa-link";
                }
            },
            cloneObject(object) {
                return JSON.parse(JSON.stringify(object));
            }
        }));

        Alpine.data('flipperPdf', (attachment = null) => ({
            attachment: null,
            init() {
                this.attachment = attachment;

                this.setupPdfMediaFrameListeners();
            },
            setupPdfMediaFrameListeners() {
                pdfMediaFrame.on('select', () => {
                    const selection = pdfMediaFrame.state().get('selection');
                    
                    selection.forEach(attachment => {
                        this.attachment = {
                           id: attachment.id,
                           title: attachment.attributes.title,
                           url: attachment.attributes.url
                        }
                    });
                });
    
                pdfMediaFrame.on('open', () => {
                    const selection = pdfMediaFrame.state().get('selection');
    
                    if (this.attachment === null) return;
    
                    const attachment = wp.media.attachment(this.attachment.id);

                    selection.add(attachment ? [attachment] : []);
                });
            },
            selectPdfFile(event) {
                event.preventDefault();

                pdfMediaFrame.open();
            },
            removePdfFile(event) {
                event.preventDefault();
                
                this.attachment = null;
            }
        }));

        Alpine.data('flipperShortcode', () => ({
            copyShortcode: (sucessMessage) => {
                const actions = document.querySelector('.flipper-shortcode-actions');
                const shortcodeInput = document.querySelector(".flipper-shortcode");
                const copyMessage    = document.createElement('span');
                copyMessage.innerHTML = sucessMessage;

                shortcodeInput.select();
                shortcodeInput.setSelectionRange(0, 99999);
                document.execCommand("copy");
                document.getSelection().removeAllRanges();
                actions.prepend(copyMessage);

                setTimeout(() => copyMessage.remove(), 2500);
            }
        }));
    }
});