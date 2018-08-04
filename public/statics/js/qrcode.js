/**
 * Created by wangchao on 6/10/17.
 */
define(["qr","vue"],function(qr,Vue){

    var template = (function () {/*
        <img :src="src">
     */}).toString().match(/[^]*\/\*([^]*)\*\/\}$/)[1];

    var QRCode = Vue.extend({
        template: template,
        props: {
            data: String,
            modulesize: Number,
            margin: Number
        },
        data: function() {
            if (!this.modulesize) {
                this.modulesize = 5;
            }
            if (!this.margin) {
                this.margin = 4;
            }

            return {
                options: {
                    modulesize: this.modulesize,
                    margin: this.margin
                }
            };
        },
        computed: {
            src: function () {
                return this.generateImgSrc();
            }
        },
        methods: {
            getOptions: function () {
                return this.options;
            },
            generateImgSrc: function(){
                if (this.data) {
                    try {
                        return qr.generatePNG(this.data, this.getOptions());
                    }
                    catch (e) {
                        console.log("qrcode: no canvas support!",e);
                    }
                }
                return null;
            }
        }
    });
    Vue.component('qrcode', QRCode);

    return "qrcode";
});