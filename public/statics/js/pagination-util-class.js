/**
 * Created by wangchao on 6/2/17.
 */
define(
    function () {

        function PaginationUtil(pageNum,totalPageCount) {
            this.pageNumName = "pageNum";
            this.pageNum = null;
            this.totalPageCount = null;
            if (pageNum) {
                this.pageNum = pageNum;
            }
            if (totalPageCount) {
                this.totalPageCount = totalPageCount;
            }
        }
        PaginationUtil.prototype.getPageUrlByPageNum = function (pageNum) {
            return this.getUpdatedQueryString(this.pageNumName, pageNum);
        };
        PaginationUtil.prototype.getPrevPageNum = function () {
            var pageNum = this.pageNum;
            if (pageNum > 1) {
                return pageNum - 1;
            }
        };
        PaginationUtil.prototype.getPrevPageUrl = function () {
            return this.getUpdatedQueryString(this.pageNumName, this.getPrevPageNum());
        };
        PaginationUtil.prototype.getNextPageNum = function () {
            var pageNum = this.pageNum;
            if (pageNum < this.totalPageCount) {
                return pageNum + 1;
            }
        };
        PaginationUtil.prototype.getNextPageUrl = function () {
            return this.getUpdatedQueryString(this.pageNumName, this.getNextPageNum());
        };

        PaginationUtil.prototype.getDisplayPageNumList = function () {
            var currentPageNum = this.pageNum;
            var totalPageCount = this.totalPageCount;

            var displayPageNumList = [];
            displayPageNumList.push(1);
            displayPageNumList.push(totalPageCount);
            displayPageNumList.push(currentPageNum - 1);
            displayPageNumList.push(currentPageNum);
            displayPageNumList.push(currentPageNum + 1);
            return displayPageNumList;
        };
        PaginationUtil.prototype.isNeedDisplayPageNum = function (pageNumMayDisplay) {
            var displayPageNumList = this.getDisplayPageNumList();
            return _.contains(displayPageNumList, pageNumMayDisplay);
        };
        PaginationUtil.prototype.getUpdatedQueryString = function (key, value, url) {
            if (!url) url = window.location.href;
            var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
            hash;

            if (re.test(url)) {
                if (typeof value !== 'undefined' && value !== null)
                    return url.replace(re, '$1' + key + "=" + value + '$2$3');
                else {
                    hash = url.split('#');
                    url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
                    if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                        url += '#' + hash[1];
                    return url;
                }
            }
            else {
                if (typeof value !== 'undefined' && value !== null) {
                    var separator = url.indexOf('?') !== -1 ? '&' : '?';
                    hash = url.split('#');
                    url = hash[0] + separator + key + '=' + value;
                    if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                        url += '#' + hash[1];
                    return url;
                }
                else
                    return url;
            }
        };

        return PaginationUtil;
    }
);