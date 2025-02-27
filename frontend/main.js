document.addEventListener('alpine:init', () => {
    Alpine.data('flipperWidget', (pages = []) => ({
        pages: [],
        init() {
            this.pages = pages.sort((a, b) => a.order - b.order);
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