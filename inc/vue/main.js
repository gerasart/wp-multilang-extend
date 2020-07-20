import Vue from 'vue'
import App from './App'

window.onload = () => {
    let container = document.querySelector('#app');
    if (container) {
        setTimeout(() => {
            new Vue({
                el: container,
                render: h => h(App)
            });
        }, 0);
    }


    let languages = document.querySelectorAll('#wpm-languages .postbox');
    if (languages.length) {
        languages.forEach(item => {
            let check = item.querySelector('input[title=Enable]').getAttribute('checked');

            if (!check) {
                let status = item.querySelector('.language-status');

                status.innerText = status.innerText + ' Disabled';
            }
        });
    }
};