/**
 * Created by wangchao on 6/2/17.
 */
define(["vue"],function(Vue){
    var template = (function () {/*
          <nav v-if="totalPageCount" style="text-align: center;">
               <ul class="pagination">
                     <li v-if="pageNum == 1" class="disabled">
                       <a>
                         <span>&laquo;</span>
                       </a>
                     </li>
                     <li v-if="pageNum != 1" style="cursor:pointer;" v-on:click="changePage(paginationUtil.getPrevPageNum())">
                       <a>
                         <span>&laquo;</span>
                       </a>
                     </li>
                     <template v-for="n in paginationNumberRange">
                         <li v-if="paginationUtil.isNeedDisplayPageNum(n) && ( pageNum == n ) " class="active">
                             <a>{{n}}</a>
                         </li>
                         <li v-if="paginationUtil.isNeedDisplayPageNum(n) && ( pageNum != n ) " style="cursor:pointer;" v-on:click="changePage(n)" >
                             <a>{{n}}</a>
                         </li>

                         <li v-if="(!paginationUtil.isNeedDisplayPageNum(n)) && (  (2 == n ) || ( (totalPageCount-1) == n ) )">
                             <a>..</a>
                         </li>
                     </template>
                     <li v-if="pageNum != totalPageCount" style="cursor:pointer;" v-on:click="changePage(paginationUtil.getNextPageNum())">
                       <a>
                         <span>&raquo;</span>
                       </a>
                     </li>
                     <li v-if="pageNum == totalPageCount" class="disabled">
                       <a>
                         <span>&raquo;</span>
                       </a>
                     </li>
                </ul>
          </nav>
     */}).toString().match(/[^]*\/\*([^]*)\*\/\}$/)[1];


    var SpaPagination = Vue.extend({
        template: template,
        props: {
            clickHandler: Function,
            subject: Object,
            paginationUtil: Object
        },
        computed: {
            totalPageCount: function () {
                return parseInt(this.paginationUtil.totalPageCount);
            },
            pageNum: function () {
                return parseInt(this.paginationUtil.pageNum);
            },
            paginationNumberRange: function () {
                var paginationNumberRange= [];
                paginationNumberRange.push(1);
                if (this.totalPageCount > 1) {
                    paginationNumberRange.push(2);
                }
                if (this.pageNum > 2) {
                    paginationNumberRange.push(this.pageNum-1);
                }
                paginationNumberRange.push(this.pageNum);
                if (this.pageNum < (this.totalPageCount-1)) {
                    paginationNumberRange.push(this.pageNum+1);
                }
                if (this.totalPageCount > 1) {
                    paginationNumberRange.push(this.totalPageCount-1);
                }
                paginationNumberRange.push(this.totalPageCount);
                paginationNumberRange = _.sortBy(paginationNumberRange);
                paginationNumberRange = _.uniq(paginationNumberRange);
                return paginationNumberRange;
            }
        },
        methods: {
            changePage: function (targetPageNum) {
                if (this.clickHandler) {
                    this.clickHandler(targetPageNum)
                }
            }
        }
    });
    Vue.component('spa-pagination', SpaPagination);
    return "spa-pagination";
});