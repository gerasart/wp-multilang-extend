<template>
    <div>
        <div class="check_lang_from_ip">
            <label for="check_from_ip">Change lang from ip</label>
            <input id="check_from_ip" @click="changeLangFromIP" v-model="check_from_ip" type="checkbox" >
        </div>

        <div class="check_lang_from_site">
            <label for="check_lang_from_site">Change lang from site</label>
            <input id="check_lang_from_site" @click="changeLangFromSite" v-model="check_from_site" type="checkbox" >
        </div>

        <table class="wp-list-table widefat extend-language-list">
            <thead>
            <tr>
                <th scope="col" id="code" class="manage-column column-code">Code</th>
                <th scope="col" id="flag" class="manage-column column-flag">Flag</th>
                <th scope="col" id="name" class="manage-column column-name column-primary">Name</th>
                <th scope="col" id="action" class="manage-column column-action">Add to lang</th>
                <th scope="col" id="view" class="manage-column column-action">View</th>
            </tr>
            </thead>
            <tbody id="the-list">
            <tr v-for="lang in data">
                <td class="code">{{lang.code}}</td>
                <td class="flag"><img :src="plugin_path+'inc/img/flags/'+lang.flag" :alt="lang.name"></td>
                <td class="name">{{lang.name}}</td>
                <td class="action">
                    <button class="add" @click.prevent="addLang(lang)" v-if="!lang.status">Add</button>
                    <p v-else>
                        Added
                        <br>
                        <button class="remove" @click.prevent="removeLang(lang)">remove</button>
                    </p>
                </td>
                <td class="view">
                    <select v-model="lang.view" @change="viewLang(lang)">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <th scope="col" class="manage-column column-code">Code</th>
                <th scope="col" class="manage-column column-flag">Flag</th>
                <th scope="col" class="manage-column column-name column-primary">Name</th>
                <th scope="col" class="manage-column column-action">Add to lang</th>
                <th scope="col" class="manage-column column-action">View</th>
            </tr>
            </tfoot>
        </table>
    </div>
</template>

<script>
    /* global ajaxurl */
    export default {
        name: "ListTab",
        data() {
            return {
                path_json: '/wp-content/plugins/wp-multilang-extend/inc/vue/data/languages.json',
                plugin_path: '/wp-content/plugins/wp-multilang-extend/',
                data: [],
                ajax_url: ajaxurl,
                langs: window.languages,
                check_from_ip: window.check_from_ip,
                check_from_site: window.check_from_site,
            }
        },
        created() {
            fetch(this.path_json)
                .then((response) => {
                    return response.json();
                })
                .then((data) => {
                    console.log('this.langs', this.langs);
                    this.data = data.map(item => {
                        if (Object.keys(this.langs).includes(item.code)) {
                            item.view = this.langs[item.code].view || '1';
                            item.status = true;
                        } else {
                            item.view = '1';
                            item.status = false;
                        }
                        return item;
                    });
                });
        },
        methods: {
            addLang(language) {
                let request = {
                    action: 'send_lang',
                    data: language
                };

                this.ajax(request, (res) => {
                    if (res.data) {
                        this.data = this.data.map(item => {
                            if (item.code === language.code) {
                                item.status = true;
                            }

                            return item;
                        });
                    }
                });
            },
            viewLang(language) {
                let request = {
                    action: 'view_lang',
                    data: language,
                    view: language.view
                };
                this.ajax(request, (res) => {
                    console.log('viewLang', res);
                });
            },
            removeLang(language) {
                this.data = this.data.map(item => {
                    if (item.code === language.code) {
                        item.status = false;
                    }
                    return item;
                });

                let request = {
                    action: 'remove_lang',
                    data: language,
                };

                this.ajax(request, (res) => {
                    console.log('removeLang', res);
                });
            },
            changeLangFromIP() {
                let us_ip = this.check_from_ip ? '0' : '1';
                let request = {
                    action: 'check_from_ip',
                    data: us_ip,
                };

                this.ajax(request, (res) => {
                    console.log('changeLangFromIP', res);
                });

            },
            changeLangFromSite() {
                let us_ip = this.check_from_site ? '0' : '1';
                let request = {
                    action: 'check_from_site',
                    data: us_ip,
                };

                this.ajax(request, (res) => {
                    console.log('changeLangFromSite', res);
                });

            },
            ajax(request, callback) {
                jQuery.ajax({
                    url: this.ajax_url,
                    type: 'POST',
                    data: request,
                    success: callback
                });
            }
        }
    }
</script>

<style lang="scss">
    .extend-language-list {
        tr {
            &:hover {
                background: #0040ff17;
            }
        }
        .remove {
            border: none;
            background: #ff4b43;
            color: white;
            padding: 5px 10px;
        }
        .add {
            border: none;
            background: #1b85ff;
            color: white;
            padding: 5px 10px;
        }
    }
    .check_lang_from_ip,.check_lang_from_site {
        margin-bottom: 30px;
    }
</style>