/**
 * Created by wangchao on 6/10/17.
 */
define(["ckeditor","vue"],function(ckeditor,Vue){
    if (typeof ckeditor === 'undefined') {
        console.log('CKEDITOR is missing (http://ckeditor.com/)');
        return;
    }

    var template = (function () {/*
        <textarea v-bind:name="name" v-bind:id="id" v-bind:value="value" v-bind:rows="rows" v-bind:cols="80"></textarea>
     */}).toString().match(/[^]*\/\*([^]*)\*\/\}$/)[1];

    var RichEditor = Vue.extend({
        template: template,
        props: {
            name: String,
            rows: Number,
            cols: Number,
            type: String,
            config: Object,
            value: String,
            subject: Object,
            contentChangeEvent: String,
            imageUploadUrl: String
        },
        data: function() {
            if (!this.rows) {
                this.rows = 20;
            }
            if (!this.cols) {
                this.cols = 80;
            }
            if (!this.config) {
                this.config = {};
            }
            if (!this.value) {
                this.value = "";
            }
            var timestamp=new Date().getTime();
            var rand = Math.round(Math.random()*1000);
            var id = 'rich-editor-'+timestamp.toString() + rand.toString();
            return {
                id: id
            };
        },
        computed: {
            instance: function () {
                return ckeditor.instances[this.id]
            }
        },
        ready: function () {
            if (this.type === 'inline') {
                ckeditor.inline(this.id, this.config);
            } else {
                ckeditor.replace(this.id, this.config);
            }
            if (this.imageUploadUrl) {
                window[this.id+"_imageUploadUrl"] = this.imageUploadUrl;
            }

            this.instance.on('change', function(){
                var html = this.instance.getData();
                if (html != this.value) {
                    var oldValue = this.value;
                    this.value = html;
                    if (this.contentChangeEvent){
                        this.$dispatch(this.contentChangeEvent, this.value, oldValue, this.subject);
                    }
                }

            }.bind(this));
        }
    });
    Vue.component('rich-editor', RichEditor);

    return "rich-editor";
});