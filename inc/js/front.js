class ExtendLang {
    constructor() {
        this.langs = window.hide_langs;
        this.item_class = 'item-language';
        this.hiddenLangs();
    }

    hiddenLangs() {
        // console.log(this.langs);
        this.langs.forEach(item => {
            const elem = '.' + this.item_class + '-' + item;
            const selector = document.querySelector(elem);
            if (selector) {
                selector.style.display = 'none';
            }
        });
    }
}

window.onload = new ExtendLang();
