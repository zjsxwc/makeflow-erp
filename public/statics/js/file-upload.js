/**
 * Created by wangchao on 7/7/17.
 */
define(["vue"], function (Vue) {
    /* globals FormData, Promise, Vue */

    var FileUploadComponent = Vue.extend({
        template: '<div class="{{ class }}"><label for="{{ name }}"><input type="file" name="{{ name }}" id="{{ id || name }}" accept="{{ accept }}" v-on:click="fileInputClick" v-on:change="fileInputChange" multiple="{{ multiple }}" v-show="!hideComponent"><slot></slot></label><button type="button" v-on:click="fileUpload" v-show="!hideComponent">Upload</button></div>',
        props: {
            class: String,
            name: {
                type: String,
                required: true
            },
            id: String,
            action: {
                type: String,
                required: true
            },
            accept: String,
            multiple: String,
            headers: Object,
            subject: Object,
            method: String,
            imgUploadedCustomEvent: String,
            uploadImgOnFileChange: {
                default: true,
                type: Boolean
            },
            hideComponent: {
                default: true,
                type: Boolean
            },
            limitFileUploadSize: {
                default: 500,
                type: Number
            },
            uploadAsync: {
                default: true,
                type: Boolean
            }
        },
        data: function () {
            return {
                myFiles: [] // a container for the files in our field
            };
        },
        methods: {
            fileInputClick: function () {
                // click actually triggers after the file dialog opens
                var inputEle = this.$el.getElementsByTagName('input')[0];
                try {
                    inputEle.value = ''; //for IE11, latest Chrome/Firefox/Opera...
                } catch (err) {
                    console.log("浏览器太老了")
                }
                this.$dispatch('onFileClick', this.myFiles);
            },
            fileInputChange: function () {
                // get the group of files assigned to this field
                var ident = this.id || this.name;
                this.myFiles = document.getElementById(ident).files;
                this.$dispatch('onFileChange', this.myFiles);
                if (this.uploadImgOnFileChange) {
                    this.fileUpload();
                }

            },
            _onProgress: function (e) {
                // this is an internal call in XHR to update the progress
                e.percent = (e.loaded / e.total) * 100;
                this.$dispatch('onFileProgress', e);
            },
            _handleAsyncUpload: function (file) {
                this.$dispatch('beforeFileUpload', file);
                var form = new FormData();
                var xhr = new XMLHttpRequest();
                try {
                    form.append('Content-Type', file.type || 'application/octet-stream');
                    // our request will have the file in the ['file'] key
                    form.append('file', file);
                } catch (err) {
                    this.$dispatch('onFileError', file, err);
                    return;
                }

                return new Promise(function (resolve, reject) {

                    xhr.upload.addEventListener('progress', this._onProgress, false);

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState < 4) {
                            return;
                        }
                        if (xhr.status < 400) {
                            var res = JSON.parse(xhr.responseText);
                            if (this.imgUploadedCustomEvent) {
                                this.$dispatch(this.imgUploadedCustomEvent, file, res, this.subject);
                            }
                            this.$dispatch('onFileUpload', file, res);
                            resolve(file);
                        } else {
                            var err = JSON.parse(xhr.responseText);
                            err.status = xhr.status;
                            err.statusText = xhr.statusText;
                            this.$dispatch('onFileError', file, err);
                            reject(err);
                        }
                    }.bind(this);

                    xhr.onerror = function () {
                        var err = JSON.parse(xhr.responseText);
                        err.status = xhr.status;
                        err.statusText = xhr.statusText;
                        this.$dispatch('onFileError', file, err);
                        reject(err);
                    }.bind(this);

                    xhr.open(this.method || "POST", this.action, true);
                    if (this.headers) {
                        for (var header in this.headers) {
                            xhr.setRequestHeader(header, this.headers[header]);
                        }
                    }
                    xhr.send(form);
                    this.$dispatch('afterFileUpload', file);
                }.bind(this));
            },

            _handleSyncUpload: function (file) {

                this.$dispatch('beforeFileUpload', file);
                var form = new FormData();
                var xhr = new XMLHttpRequest();
                try {
                    form.append('Content-Type', file.type || 'application/octet-stream');
                    // our request will have the file in the ['file'] key
                    form.append('file', file);
                } catch (err) {
                    this.$dispatch('onFileError', file, err);
                    return;
                }


                xhr.upload.addEventListener('progress', this._onProgress, false);

                xhr.onreadystatechange = function () {
                    if (xhr.readyState < 4) {
                        return;
                    }
                    if (xhr.status < 400) {
                        var res = JSON.parse(xhr.responseText);
                        if (this.imgUploadedCustomEvent) {
                            this.$dispatch(this.imgUploadedCustomEvent, file, res, this.subject);
                        }
                        this.$dispatch('onFileUpload', file, res);
                        if (file.nextFile) {
                            this._handleSyncUpload(file.nextFile);
                        }

                    } else {
                        var err = JSON.parse(xhr.responseText);
                        err.status = xhr.status;
                        err.statusText = xhr.statusText;
                        this.$dispatch('onFileError', file, err);

                    }
                }.bind(this);

                xhr.onerror = function () {
                    var err = JSON.parse(xhr.responseText);
                    err.status = xhr.status;
                    err.statusText = xhr.statusText;
                    this.$dispatch('onFileError', file, err);
                    reject(err);
                }.bind(this);

                xhr.open(this.method || "POST", this.action, true);
                if (this.headers) {
                    for (var header in this.headers) {
                        xhr.setRequestHeader(header, this.headers[header]);
                    }
                }
                xhr.send(form);
                this.$dispatch('afterFileUpload', file);

            },

            fileUpload: function () {
                var maxFileUploadSizeKB = this.limitFileUploadSize;

                var i = 0;
                var file;

                if (this.myFiles.length > 0) {
                    for (i = 0; i < this.myFiles.length; i++) {
                        file = this.myFiles[i];
                        if ((file.size / 1024) > maxFileUploadSizeKB) {
                            var err = "File too large";
                            console.log(err);
                            this.$dispatch('onFileError', file, err);
                            return;
                        }
                    }

                    if (this.uploadAsync) {
                        // a hack to push all the Promises into a new array
                        var arrayOfPromises = Array.prototype.slice.call(this.myFiles, 0).map(function (file) {
                            return this._handleAsyncUpload(file);
                        }.bind(this));
                        // wait for everything to finish
                        Promise.all(arrayOfPromises).then(function (allFiles) {
                            this.$dispatch('onAllFilesUploaded', allFiles);
                        }.bind(this)).catch(function (err) {
                            this.$dispatch('onFileError', this.myFiles, err);
                        }.bind(this));
                    } else {
                        for (i = 0; i < this.myFiles.length; i++) {
                            this.myFiles[i].nextFile = null;
                            if (this.myFiles[i + 1]) {
                                this.myFiles[i].nextFile = this.myFiles[i + 1];
                            }
                        }
                        this._handleSyncUpload(this.myFiles[0]);
                    }

                } else {
                    // someone tried to upload without adding files
                    var err = new Error("No files to upload for this field");
                    this.$dispatch('onFileError', this.myFiles, err);
                }
            }
        }
    });

    Vue.component('file-upload', FileUploadComponent);
    return 'file-upload';
});