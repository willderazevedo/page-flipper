document.addEventListener('alpine:init', () => {
    const imagesFrame = wp.media({
        multiple: 'add',
        library: {
            type: [ 'image' ],
            uploadedTo: wp.media.view.settings.post.id
        }
    });

    Alpine.data('flipperBuilder', (pages = []) => ({
        sortable: null,
        pages: [],
        get selectedPage() {
            return this.pages.find(page => page.selected);
        },
        init() {
            if (pages.length) {
                pages.sort((a, b) => a.order - b.order).forEach((page, index) => {
                    this.pages.push({
                        selected: index === 0,
                        order: page.order,
                        attachment: page.attachment
                    });
                });

                setTimeout(() => this.initPageListSort(), 300);
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

                        page.attachment = attachment;

                        return true;
                    } 

                    this.pages.push({
                        selected: this.selectedPage ? false : index === 0,
                        order: this.pages.length,
                        attachment: {
                            id: attachment.id,
                            title: attachment.attributes.title,
                            url: attachment.attributes.url,
                            alt: attachment.attributes.alt,
                        },
                    });
                });

                setTimeout(() => this.initPageListSort(), 300);
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
        initPageListSort() {
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
});