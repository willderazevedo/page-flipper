document.addEventListener('alpine:init', () => {
    if (window.pagenow === 'page_flipper') {
        const pagesMediaFrame = wp.media({
            multiple: 'add',
            library: {
                type: [ 'image' ],
                uploadedTo: wp.media.view.settings.post.id
            }
        });

        const audioHotspotMediaFrame = wp.media({
            multiple: false,
            library: {
                type: [ 'audio' ],
                uploadedTo: wp.media.view.settings.post.id
            }
        });

        const imageHotspotMediaFrame = wp.media({
            multiple: false,
            library: {
                type: [ 'image' ],
                uploadedTo: wp.media.view.settings.post.id
            }
        });

        const videoHotspotMediaFrame = wp.media({
            multiple: false,
            library: {
                type: [ 'video' ],
                uploadedTo: wp.media.view.settings.post.id
            }
        });

        const hotspotObject = {
            type: '',
            position: { x: 5, y: 5 },
            size: { width: 25, height: 25 },
            extras: {},
            attachment: {}
        };
    
        Alpine.data('flipperBuilder', (pages = []) => ({
            sortable: null,
            pages: [],
            hotspotType: "",
            hotspotToEdit: null,
            hotspotWrapperWidth: 0,
            hotspotWrapperHeight: 0,
            get selectedPage() {
                return this.pages.find(page => page.selected);
            },
            init() {
                this.$watch('selectedPage', () => this.setupHotspotsWrapperSizes());
    
                if (pages.length) {
                    pages.sort((a, b) => a.order - b.order).forEach((page, index) => {
                        page.selected = index === 0;
    
                        this.pages.push(page);
                    });
    
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
                            if (!this.selectedPage) page.selected = true;
    
                            page.attachment = {
                                id: attachment.id,
                                title: attachment.attributes.title,
                                url: attachment.attributes.url,
                                alt: attachment.attributes.alt,
                            };
    
                            return true;
                        } 
    
                        this.pages.push({
                            selected: this.selectedPage ? false : index === 0,
                            order: this.pages.length,
                            hotspots: [],
                            attachment: {
                                id: attachment.id,
                                title: attachment.attributes.title,
                                url: attachment.attributes.url,
                                alt: attachment.attributes.alt,
                            },
                        });
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
                    
                    selection.forEach(attachment => {
                        if (this.hotspotToEdit) {
                            this.hotspotToEdit.attachment.id    = attachment.id
                            this.hotspotToEdit.attachment.title = attachment.attributes.title
                            this.hotspotToEdit.attachment.alt   = attachment.attributes.url
                            this.hotspotToEdit                  = null;
    
                            return;
                        }

                        const newHotspot            = this.cloneObject(hotspotObject);
                        newHotspot.type             = this.hotspotType;
                        newHotspot.attachment.id    = attachment.id
                        newHotspot.attachment.title = attachment.attributes.title
                        newHotspot.attachment.alt   = attachment.attributes.url

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

                    const interactInstance = interact(`.${hotspot.type}-hotspot`);

                    if (["video", "image", "text", "link"].findIndex(type => type === hotspot.type) !== -1) {
                        interactInstance.resizable({
                            edges: { left: true, right: true, bottom: true, top: true },
                            listeners: {
                                move: (event) => {
                                    const target = event.target;
                                    const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.deltaRect.left;
                                    const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.deltaRect.top;
                            
                                    target.style.width = event.rect.width + 'px';
                                    target.style.height = event.rect.height + 'px';
                                    target.style.transform = 'translate(' + x + 'px,' + y + 'px)';
                            
                                    target.setAttribute('data-x', x);
                                    target.setAttribute('data-y', y);
                                },
                                end: (event) => {
                                    hotspot.size.width  = (event.rect.width * 100) / this.hotspotWrapperWidth;
                                    hotspot.size.height = (event.rect.height * 100) / this.hotspotWrapperHeight;
                                    hotspot.position.x  = (parseFloat(event.target.getAttribute('data-x')) * 100) / this.hotspotWrapperWidth;
                                    hotspot.position.y  = (parseFloat(event.target.getAttribute('data-y')) * 100) / this.hotspotWrapperHeight;
                                }
                            },
                            inertia: true,
                            modifiers: [
                                interact.modifiers.restrictEdges({ outer: 'parent' }),
                                interact.modifiers.restrictSize({ min: { width: 100, height: 50 } })
                            ],
                        });
                    }
                    
                    if (["audio", "video", "image", "text", "link"].findIndex(type => type === hotspot.type) !== -1) {
                        interactInstance.draggable({
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
                                    hotspot.position.x = (parseFloat(event.target.getAttribute('data-x')) * 100) / this.hotspotWrapperWidth;
                                    hotspot.position.y = (parseFloat(event.target.getAttribute('data-y')) * 100) / this.hotspotWrapperHeight;
                                }
                            },
                            inertia: true,
                            modifiers: [
                                interact.modifiers.restrictRect({ restriction: 'parent', endOnly: true })
                            ]
                        });
                    }
                });
            },
            selectPage(page) {
                if (this.selectedPage) this.selectedPage.selected = false;
    
                page.selected = true;
            },
            removePage(page) {
                const pageIndex = this.pages.findIndex(item => item.attachment.id === page.attachment.id);
    
                this.pages.splice(pageIndex, 1);
                this.pages.forEach(item => {
                    if (item.order > page.order) item.order -= 1;
                });
    
                if (page.selected && this.pages.length) this.selectPage(this.pages[0]);
                if (!this.pages.length) this.sortable = null;
            },
            addPages() {
                pagesMediaFrame.open();
            },
            removePages() {
                this.pages = [];
                this.sortable = null;
            },
            removeHotspot(hotspotIndex) {
                this.selectedPage.hotspots.splice(hotspotIndex, 1);
            },
            addHotspot(type) {
                this.hotspotType = type;
                this.hotspotToEdit = null;

                switch (type) {
                    case "narration":
                        const hotspot = this.selectedPage.hotspots.find(hotspot => hotspot.type === type);

                        if (hotspot) this.editMediaHotspot(hotspot);
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
                        newHotspot.type  = type;

                        this.selectedPage.hotspots.push(newHotspot);

                        setTimeout(() => this.setupHotspotsInteractions(), 300);
                }
            },
            editMediaHotspot(hotspot) {
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
                if (hotspot.type === 'narration') return {};

                const hotspotClone = this.cloneObject(hotspot);
                const positionX = (hotspotClone.position.x / 100) * this.hotspotWrapperWidth;
                const positionY = (hotspotClone.position.y / 100) * this.hotspotWrapperHeight;
            
                let style = `transform: translate(${positionX}px, ${positionY}px);`;
            
                if (hotspotClone.type !== 'audio') {
                    const sizeX = (hotspotClone.size.width / 100) * this.hotspotWrapperWidth;
                    const sizeY = (hotspotClone.size.height / 100) * this.hotspotWrapperHeight;
            
                    style += `width: ${sizeX}px; height: ${sizeY}px;`;
            
                    return {
                        "data-x": positionX,
                        "data-y": positionY,
                        "style": style
                    };
                }
            
                return {
                    "data-x": positionX,
                    "data-y": positionY,
                    "style": style
                };
            },
            cloneObject(object) {
                return JSON.parse(JSON.stringify(object));
            }
        }));
    }
});