document.addEventListener('alpine:init', () => {
    if (window.pagenow === 'page_flipper') {
        const imagesFrame = wp.media({
            multiple: 'add',
            library: {
                type: [ 'image' ],
                uploadedTo: wp.media.view.settings.post.id
            }
        });
    
        // function resizableListener(event) {
        //     const target = event.target;
        //     let x = (parseFloat(target.getAttribute('data-x')) || 0);
        //     let y = (parseFloat(target.getAttribute('data-y')) || 0);
    
        //     target.style.width = event.rect.width + 'px';
        //     target.style.height = event.rect.height + 'px';
    
        //     x += event.deltaRect.left;
        //     y += event.deltaRect.top;
    
        //     target.style.transform = 'translate(' + x + 'px,' + y + 'px)';
    
        //     target.setAttribute('data-x', x);
        //     target.setAttribute('data-y', y);
        // }
    
        // function draggableListener(event) {
        //     const target = event.target;
        //     const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
        //     const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
    
        //     target.style.transform = `translate(${x}px, ${y}px)`;
    
        //     target.setAttribute('data-x', x);
        //     target.setAttribute('data-y', y);
        // }
    
        Alpine.data('flipperBuilder', (pages = []) => ({
            sortable: null,
            pages: [],
            get selectedPage() {
                return this.pages.find(page => page.selected);
            },
            init() {
                this.$watch('selectedPage', () => this.setupHotspotsInteractions());
    
                if (pages.length) {
                    pages.sort((a, b) => a.order - b.order).forEach((page, index) => {
                        page.selected = index === 0;
    
                        this.pages.push(page);
                    });
    
                    setTimeout(() => this.setupPageListSort(), 300);
                }
    
                this.setupImageFrameListeners();
            },
            setupImageFrameListeners() {
                imagesFrame.on('select', () => {
                    const selection = imagesFrame.state().get('selection');
                    
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
    
                imagesFrame.on('open', () => {
                    const selection = imagesFrame.state().get('selection');
    
                    if (!this.pages.length) return;
    
                    this.pages.forEach(page => {
                        const attachment = wp.media.attachment(page.attachment.id);
    
                        selection.add(attachment ? [attachment] : []);
                    })
                });
    
                document.querySelector('.flipper-builder-wrapper .upload-images').addEventListener('click', () => imagesFrame.open());
                document.querySelector('.flipper-builder-wrapper .remove-pages').addEventListener('click', () => {
                    this.pages = [];
                    this.sortable = null;
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
            setupHotspotsInteractions() {
                const image = document.querySelector('.flipper-builder-wrapper .flipper-page img');
                const hotspotWrapper = document.querySelector('.flipper-builder-wrapper .hotspots-wrapper');
    
                hotspotWrapper.innerHTML = "";
    
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
    
                    hotspotWrapper.style.width = `${renderedWidth}px`;
                    hotspotWrapper.style.height = `${renderedHeight}px`;
    
                    // const divTeste = document.createElement('div');
    
                    // // Criando um hotspot de Teste para interagir com interact
                    // divTeste.classList.add("div-teste");
                    // divTeste.innerHTML = "DIV de Teste =D";
                    
                    // Object.assign(divTeste.style, {
                    //     width: "120px",
                    //     borderRadius: "8px",
                    //     padding: "20px",
                    //     margin: "1rem",
                    //     backgroundColor: "#29e",
                    //     color: "white",
                    //     fontSize: "20px",
                    //     fontFamily: "sans-serif",
                    //     touchAction: "none",
                    //     boxSizing: "border-box"
                    // });
    
                    // hotspotWrapper.append(divTeste);
    
                    // interact('.div-teste')
                    // .resizable({
                    //     edges: { left: true, right: true, bottom: true, top: true },
                    //     listeners: { move: resizableListener },
                    //     inertia: true,
                    //     modifiers: [
                    //         interact.modifiers.restrictEdges({
                    //             outer: 'parent'
                    //         }),
                    //         interact.modifiers.restrictSize({
                    //             min: { width: 100, height: 50 }
                    //         })
                    //     ],
                    // })
                    // .draggable({
                    //     listeners: { move: draggableListener },
                    //     inertia: true,
                    //     modifiers: [
                    //         interact.modifiers.restrictRect({
                    //             restriction: 'parent',
                    //             endOnly: true
                    //         })
                    //     ]
                    // })
                }
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
            }
        }));
    }
});