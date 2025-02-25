document.addEventListener('alpine:init', () => {
    const imagesFrame = wp.media({
        multiple: 'add',
        library: {
            type: [ 'image' ]
        }
    });

    Alpine.data('flipperBuilder', () => ({
        sortable: null,
        pages: [],
        get selectedPage() {
            return this.pages.find(page => page.selected);
        },
        init() {
            imagesFrame.on('select', () => {
                const selection = imagesFrame.state().get('selection');
                
                this.pages = this.pages.filter(page => 
                    selection.findIndex(attachment => attachment.id == page.attachment.id) !== -1
                );
                
                selection.forEach((attachment, index) => {
                    const page = this.pages.find(page => page.attachment.id === attachment.id);

                    if (page) {
                        if (!this.selectedPage) page.selected = true;

                        return true;
                    } 

                    this.pages.push({
                        selected: this.selectedPage ? false : index === 0,
                        attachment: attachment,
                        order: index
                    });
                });

                setTimeout(() => this.pageListSortable(), 300);
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
        },
        selectPage(page) {
            this.selectedPage.selected = false;
            page.selected = true;
        },
        pageListSortable() {
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
        }
    }));
});